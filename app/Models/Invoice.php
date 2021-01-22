<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helper\HasManyRelation;
use DB;
class Invoice extends Model
{
    //
    use HasManyRelation;

    protected $table = 'invoices';

    protected $fillable = ['number','customer_id','distuributor_id','salesperson_id','note','date','total','term_id'];

    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id');
    }
    public function distuributor(){
        return $this->belongsTo(Distributor::class,'distuributor_id');
    }

    public function items(){
        return $this->hasMany(InvoiceItem::class,'invoice_id');
    }

    public function shipping_method()
    {
        return $this->hasOne(ShippingDetail::class,'invoice_id')->withDefault();
    }

    public function salesperson()
    {
        return $this->belongsTo(ContactPerson::class,'salesperson_id');
    }
    
    public function get_invoice_list($date_range = null,$status=0)
    {
        if($date_range == null)
        {
            $date_range['start_date'] = date('Y-m-d', strtotime('today - 31 days'));
            $date_range['end_date']   = date('Y-m-d');
        }
        else
        {
            $date_range = $this->change_date_format($date_range);
        }
       
        return $this->select('invoices.id','invoices.number','d1.companyname','c1.clientname','invoices.total','invoices.date','c1.companyemail',
                             DB::raw("CONCAT(c2.firstname,' ',c2.lastname) AS salesperson"),'t1.term')
                    ->join('customers as c1','customer_id','=','c1.client_id')
                    ->join('contactperson as c2','salesperson_id','=','c2.contact_id')
                    ->join('distributor as d1','invoices.distuributor_id','=','d1.distributor_id')
                    ->leftjoin('terms as t1','invoices.term_id','=','t1.term_id')
                    ->whereRaw('DATE(date) >= ?', [$date_range['start_date']])
                    ->whereRaw('DATE(date) <= ?', [$date_range['end_date']])
                    ->where('invoices.status',$status)
                    ->orderby('created_at','desc')
                    ->get();
    }

    public function get_table_data($date_range,$status)
    {
        $invoice_list = $this->get_invoice_list($date_range,$status);
        $invoiceitem = new InvoiceItem;
        foreach($invoice_list as $invoice)
        {
            $invoice->total = number_format((float)$invoice->total, 2, '.', '');
            $invoice['items'] = $invoiceitem->get_invoice_items($invoice->id);
            $invoice['shipping_method'] = $invoice->shipping_method()->with('carrier')->get();
        }

        return $invoice_list;
    }


    private function change_date_format($date_range)
    {
        if($date_range == null)
        {
            return;
        }
        
        $date_range = str_replace(' ', '', $date_range);
        
        $tmp = [];
        $tmp[0] = explode('-',$date_range);
        $tmp[1] = explode('/',$tmp[0][0]);
        $tmp[2] = explode('/',$tmp[0][1]);
        $result['start_date'] = $tmp[1][2].'-'.$tmp[1][0].'-'.$tmp[1][1];
        $result['end_date']   = $tmp[2][2].'-'.$tmp[2][0].'-'.$tmp[2][1];
        
        return $result;
    }
}
