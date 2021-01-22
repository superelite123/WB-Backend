<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItemAP extends Model
{
    //
    protected $table  ='invoice_item_ap';

    protected $primay_key = 'id';

    protected $fillable = ['invoice_id','strain','p_type','unit_price',
                           'qty','discount','discount_type','discount_id',
                           'tax','tax_note','units'];

    public  $timestamps = false;

    public function strain()
    {
        return $this->belongsTo(Strainame::class,'strain');
    }

    public function Order(){
        return $this->belongsTo(InvoiceNew::class,'invoice_id');
    }

    public function producttype()
    {
        return $this->belongsTo(Producttype::class,'p_type');
    }

    public function DiscountRelation()
    {
        return $this->belongsTo(Promo::class,'discount_id');
    }

    public function getTaxexemptAttribute()
    {
        // $strain = Strainame::find($this->strain) != null?Strainame::find($this->strain)->strain:-1;
        // $p_type = Producttype::find($this->p_type) != null?Producttype::find($this->p_type)->producttype:-1;
        // if($strain == -1 || $p_type == -1)
        // {
        //     return 0;
        // }

        $upc = UPController::where(
            [
                ['strain',$this->strain],
                ['type',$this->p_type]
            ])->first();
        
        return $upc != null?$upc->taxexempt:0;
    }

    public function getStrainLabelAttribute()
    {
        return $this->Strain != null?$this->Strain->strain:'previous system';
    }
    public function getPTypeLabelAttribute()
    {
        return  $this->producttype != null ? $this->producttype->producttype:'previous system';
    }
    public function getBasePriceAttribute()
    {
        return number_format((float)$this->unit_price * $this->qty, 2, '.', '');
    }

    public function getExtendedAttribute()
    {
        return number_format((float)$this->BasePrice - $this->discount, 2, '.', '');
    }

    public function getCPUAttribute()
    {
        return number_format((float)$this->BasePrice / $this->units, 2, '.', '');
    }

    public function getDividedBasePriceAttribute()
    {
        return number_format((float)$this->unit_price, 2, '.', '');
    }
    public function getDividedUnitAttribute()
    {
        return $this->units / $this->qty;
    }
    public function getDividedDiscountAttribute()
    {
        return number_format((float)$this->discount / $this->qty, 2, '.', '');
    }
    public function getDividedTaxAttribute()
    {
        return number_format((float)$this->tax / $this->qty, 2, '.', '');
    }
    public function getDividedExtendedAttribute()
    {
        return number_format((float)$this->unit_price - $this->DividedDiscount, 2, '.', '');
    }
    public function getDividedAdjustPriceAttribute()
    {
        return number_format((float)$this->DividedExtended + $this->DividedTax, 2, '.', '');
    }
    public function getDisTypeAttribute()
    {
        return $this->DiscountRelation != null?$this->DiscountRelation->name:'';
    }

    public function getAdjustPriceAttribute()
    {
        return number_format((float)$this->Extended + $this->tax , 2, '.', '');
    }
    public function getTNoteAttribute()
    {
        return $this->tax_note == null?'':$this->tax_note;
    }
}
