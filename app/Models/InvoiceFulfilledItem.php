<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceFulfilledItem extends Model
{
    //
    private $coa_path = 'assets/upload/files/coa/';
    protected $table  ='invoice_fulfilled_item';

    protected $primay_key = 'id';

    protected $fillable = ['invoice_id','item_id','asset_id','m_parent_id',
                           'scanned_metrc',];

    public  $timestamps = false;

    public function asset()
    {
        return $this->belongsTo(InvoiceGood::class,'asset_id')->with('unitVolume');
    }

    public function ap_item()
    {
        return $this->belongsTo(InvoiceItemAP::class,'item_id');
    }

    public function childItems()
    {
        return $this->hasMany(InvoiceFulfilledItem::class,'m_parent_id')->with('asset');
    }

    public function getDividedBasePriceAttribute()
    {
        $a = $this->ap_item != null ? $this->ap_item->DividedBasePrice:0;
        $b = $this->asset != null ? $this->asset->qtyonhand:0;
        return number_format((float)$a * $b, 2, '.', '');
    }

    public function getDividedUnitAttribute()
    {
        $a = $this->ap_item != null ? $this->ap_item->DividedUnit:0;
        $b = $this->asset != null ? $this->asset->qtyonhand:0;
        return $a * $b;
    }
    public function getUnitLabelAttribute()
    {
        $unit = $this->asset->UnitOfWeight;
        return $unit != null?$unit->name:'';
    }
    public function getDividedDiscountAttribute()
    {
        
        
        $a = $this->ap_item != null ? $this->ap_item->DividedDiscount:0;
        $b = $this->asset != null ? $this->asset->qtyonhand:0;
        return number_format((float)$a * $b, 2, '.', '');
    }
    public function getDividedEDiscountAttribute()
    {
        
        $a = $this->ap_item != null ? $this->ap_item->DividedEDiscount:0;
        $b = $this->asset != null ? $this->asset->qtyonhand:0;
        return number_format((float)$a * $b, 2, '.', '');
    }
    public function getDividedTaxAttribute()
    {
        
        $a = $this->ap_item != null ? $this->ap_item->DividedTax:0;
        $b = $this->asset != null ? $this->asset->qtyonhand:0;
        return number_format((float)$a * $b, 2, '.', '');
    }
    public function getDividedExtendedAttribute()
    {
        $a = $this->ap_item != null ? $this->ap_item->DividedExtended:0;
        $b = $this->asset != null ? $this->asset->qtyonhand:0;
        return number_format((float)$a * $b, 2, '.', '');
    }
    public function getDividedAdjustPriceAttribute()
    {
        $a = $this->ap_item != null ? $this->ap_item->DividedAdjustPrice:0;
        $b = $this->asset != null ? $this->asset->qtyonhand:0;
        return number_format((float)$a * $b, 2, '.', '');
    }
    public function getCoaListAttribute()
    {
        $coas = [];
        $checking_items = [];
        if($this->m_parent_id == -1)
        {
            $checking_items = InvoiceFulfilledItem::where('m_parent_id',$this->id)->get();
        }
        else
        {
            $checking_items[] = $this;
        }

        foreach($checking_items as $item)
        {
            $temp['coa'] = $item->asset->CoaName;
            $temp['is_exist'] = true;
            if( file_exists( public_path( $this->coa_path.$item->asset->CoaName ) ) )
            {
                $flag = true;
                foreach($coas as $key => $coa)
                {
                    if($coa['coa'] == $item->asset->CoaName) $flag = false;

                }
                if($flag)
                {
                    $coas[] = $temp;
                }
            }
            else
            {
                $temp['is_exist'] = false;
                $coas[] = $temp;
            }
        }

        return $coas;
    }
}
