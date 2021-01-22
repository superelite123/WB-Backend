<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helper\HasManyRelation;
use DB;
class InvoiceNew extends Model
{
    //
    use HasManyRelation;

    protected $table = 'invoices_new';

    protected $fillable = ['number','customer_id','distuributor_id',
                           'salesperson_id','note','fulfillmentnote','date','total',
                           'term_id','paid','coainbox','delivered','status','tax_allow'];

    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id')->with('Term');
    }
    public function distuributor(){
        return $this->belongsTo(Distributor::class,'distuributor_id');
    }
    public function Term(){
        return $this->belongsTo(Term::class,'term_id');
    }
    public function itemAP()
    {
        return $this->hasMany(InvoiceItemAP::class,'invoice_id');
    }

    public function fulfilledItem()
    {
        return $this->hasMany(InvoiceFulfilledItem::class,'invoice_id')
                    ->whereIn('m_parent_id',[0,-1])->with(['asset','ap_item']);
    }

    public function fulfilledNItem()
    {
        return $this->hasMany(InvoiceFulfilledItem::class,'invoice_id')
                    ->where('m_parent_id','!=',-1)->with(['asset','ap_item']);
    }

    public function shipping_method()
    {
        return $this->hasOne(ShippingDetail::class,'invoice_id')->withDefault();
    }

    public function salesperson()
    {
        return $this->belongsTo(ContactPerson::class,'salesperson_id');
    }
    
    public function distributor()
    {
        return $this->belongsTo(Distributor::class,'distuributor_id');
    }

    public function getCNameAttribute()
    {
        return $this->customer['clientname'] != ''?$this->customer['clientname']:'previous system';
    }

    public function getCPNameAttribute()
    {
        return $this->distributor['companyname'] != ''?$this->distributor['companyname']:'previous system';
    }

    public function getPayDateAttribute()
    {
        $term = $this->Term == null?0:$this->Term->days;
        return date('Y-m-d',strtotime($this->date." +".$term." days"));
    }

    public function getTotalInfoAttribute()
    {
        $items = $this->itemAP()->get();
        $base_price = 0;
        $base_price_for_tax = 0;
        $base_price_for_promotion = 0;
        $discounted = 0;
        $taxed = 0;
        $adjust_price = 0;
        $qty = 0;
        $weight = 0;
        foreach($items as $key => $item)
        {
            $base_price += $item->BasePrice;
            if($item->Taxexempt != 1)
            {
                $base_price_for_tax += $item->BasePrice;
            }
            $discounted   += $item->discount;
            $adjust_price += $item->AdjustPrice;
            $qty += $item->qty;
        }

        //sum of weight
        $n_items = $this->fulfilledNItem()->get();
        foreach($n_items as $item)
        {
            if($item->asset != null)
                $weight += $item->asset->weight;
            else
                $weight += 0;
        }

        $base_price_for_promotion = $base_price_for_tax;
        if($this->tax_allow == 1)
            $base_price_for_tax = 0;
        
        $taxed = $base_price_for_tax * 0.8;
        $taxed = ($taxed + $base_price_for_tax) * 0.15;
        $adjust_price += $taxed;
        $result = [];
        $result['base_price']   = number_format((float)$base_price, 2, '.', '');
        $result['discount']     = number_format((float)$discounted, 2, '.', '');
        $result['extended']     = number_format((float)$base_price - $discounted, 2, '.', '');
        $result['promotion']    = number_format((float)$base_price - $base_price_for_promotion, 2, '.', '');
        $result['tax']          = number_format((float)$taxed, 2, '.', '');
        $result['adjust_price'] = number_format((float)$this->total, 2, '.', '');
        $result['qty']          = $qty;
        $result['pay_date']     = $this->PayDate;
        $result['payment']      = $this->paid == null?'No Paid':'Paid';
        $result['customername'] = $this->CName;
        $result['companyname']  = $this->CPName;
        $result['term']         = $this->Term != null?$this->Term->days:'No Term';
        $result['weight']       = $weight;
        $result['total_debt']   = $this->customer != null?$this->customer->TotalDebt:0;
        return $result;
    }

    public function get_items_for_fullfilled_list()
    {
        $this['shipping_method'] = $this->shipping_method()->with('carrier')->get();

        $this->clientname   = $this->CName;
        $this->companyname  = $this->CPName;
        $this->total_info  = $this->TotalInfo;
        $this->companyemail = $this->customer != null?$this->customer->companyemail:'No';

        $this->items = $this->fulfilledItem()
                            ->whereIn('m_parent_id',[0,-1])->with(['asset','ap_item'])->get();
        if(count($this->items) == 0)
        {
            $this->items = $this->itemAP;
            foreach($this['items'] as $item)
            {
                $item->description  = $item->StrainLabel.','.$item->PTypeLabel;
                $item->coa          = '';
                $item->qty          = $item->qty;
                $item->units        = $item->units;
                $item->weight       = 0;
                $item->unit_price   = $item->unit_price;
                $item->cpu          = $item->CPU;
                $item->base_price   = $item->BasePrice;
                $item->extended     = $item->Extended;
                $item->discount     = $item->discount;
                $item->discount_label= $item->DisType;
                $item->tax          = $item->tax;
                $item->tax_note     = $item->TNote;
                $item->adjust_price = $item->AdjustPrice;
            }
        }
        else
        {
            foreach($this['items'] as $item)
            {
                $item->description  = $item->asset->Description;
                $item->coa          = $item->asset->coa;
                if($item->m_parent_id == -1)
                    $item->qty          = $item->ap_item->qty;
                else
                    $item->qty          = 1;

                $item->units        = $item->DividedUnit;
                $item->weight       = $item->asset->weight;
                $item->unit_price   = $item->ap_item->unit_price;
                $item->cpu          = $item->ap_item->CPU;
                $item->base_price   = $item->DividedBasePrice;
                $item->extended     = $item->DividedExtended;
                $item->discount     = $item->DividedDiscount;
                $item->discount_label= $item->ap_item->DisType;
                $item->tax          = $item->DividedTax;
                $item->tax_note     = $item->ap_item->TNote;
                $item->adjust_price = $item->DividedAdjustPrice;
            }
        }
        
    }
}
