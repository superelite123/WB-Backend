<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $table='customers';
    protected $primaryKey = 'client_id';

    protected $fillable = ['name','address','email'];
    
    public function state_name()
    {
        return $this->belongsTo(State::class,'state');
    }
    public function Term()
    {
        return $this->belongsTo(Term::class,'terms','term_id');
    }
    public function rLicenseType()
    {
        return $this->belongsTo(License::class,'licensetype');
    }
    public function basePrice()
    {
        return $this->hasMany(PriceMatrix::class,'customer_id');
    }
    
    public function Invoices()
    {
        return $this->hasMany(InvoiceNew::class,'customer_id');
    }
    public function rCreditNote()
    {
        return $this->hasMany(InvoiceCreditNote::class,'customer_id');
    }
    public function getCreditNoteTotalAttribute()
    {
        return $this->rCreditNote()->where('archive',0)->get()->sum('total_price');
    }
    public function getTotalDebtAttribute()
    {
        $invoices = $this->Invoices()->where([['paid',null],['status',3]])->get();
        $total = $invoices->sum('total');
        // foreach($invoices as $invoice)
        //     $total += $invoice->TotalInfo['adjust_price'];
        return number_format((float)$total, 2, '.', '');
    }

    public function getFinacialInfoAttribute()
    {
        $invoices = $this->Invoices()->where([['paid',null],['status',3]])->get();
        $total = 0;
        $result = [];
        $result['sumSubTotal'] = 0;
        $result['sumTax'] = 0;
        $result['sumTotal'] = 0;
        $result['sumPTotal'] = 0;
        $result['sumPTax'] = 0;
        $result['sumRTotal'] = 0;
        $result['sumRTax'] = 0;
        $temps = [];
        foreach($invoices as $invoice)
        {
            $temp = [];
            $temp['id']         = $invoice->id;
            $temp['number']     = $invoice->number;
            $temp['date']       = $invoice->date;
            $temp['subTotal']   = $invoice->TotalInfo['extended'];
            $temp['tax']        = $invoice->TotalInfo['tax'];
            $temp['total']      = $invoice->TotalInfo['adjust_price'];
            $temp['pTotal']     = $invoice->FinancialTotalInfo['pSubTotal'];
            $temp['pTax']       = $invoice->FinancialTotalInfo['pTax'];
            $temp['rTotal']     = $invoice->FinancialTotalInfo['rSubTotal'];
            $temp['rTax']       = $invoice->FinancialTotalInfo['rTax'];
            $temp['url']        = url('order_fulfilled/view/'.$invoice->id.'/0');
            $temp['download']   = url('order_fulfilled/_download_invoice_pdf/'.$invoice->id);
            $temp['collect_payment']   = url('order_fulfilled/payment/'.$invoice->id);
            $result['sumSubTotal']    += $temp['subTotal'];
            $result['sumTax']         += $temp['tax'];
            $result['sumTotal']       += $temp['total'];
            $result['sumPTotal']      += $temp['pTotal'];
            $result['sumPTax']        += $temp['pTax'];
            $result['sumRTotal']      += $temp['rTotal'];
            $result['sumRTax']        += $temp['rTax'];

            $temps[]        = $temp;
        }
        
        $result['sumSubTotal']  = number_format((float)$result['sumSubTotal'], 2, '.', '');
        $result['sumTax']       = number_format((float)$result['sumTax'], 2, '.', '');
        $result['sumTotal']     = number_format((float)$result['sumTotal'], 2, '.', '');
        $result['sumPTotal']    = number_format((float)$result['sumPTotal'], 2, '.', '');
        $result['sumPTax']      = number_format((float)$result['sumPTax'], 2, '.', '');
        $result['sumRTotal']    = number_format((float)$result['sumRTotal'], 2, '.', '');
        $result['sumRTax']      = number_format((float)$result['sumRTax'], 2, '.', '');
        $result['myInvoices'] = $temps;
        return $result;
    }
}
