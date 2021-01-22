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

    public function getDividedBasePriceAttribute()
    {
        return number_format((float)$this->ap_item->unit_price * $this->asset->qtyonhand, 2, '.', '');
    }

    public function getDividedUnitAttribute()
    {
        return $this->ap_item->units / $this->ap_item->qty * $this->asset->qtyonhand;
    }
    public function getDividedDiscountAttribute()
    {
        return number_format((float)$this->ap_item->discount / $this->ap_item->qty * $this->asset->qtyonhand, 2, '.', '');
    }
    public function getDividedTaxAttribute()
    {
        return number_format((float)$this->ap_item->ap_itemtax / $this->ap_item->qty * $this->asset->qtyonhand, 2, '.', '');
    }
    public function getDividedExtendedAttribute()
    {
        return number_format((float)$this->DividedBasePrice - $this->DividedDiscount, 2, '.', '');
    }
    public function getDividedAdjustPriceAttribute()
    {
        return number_format((float)$this->DividedExtended + $this->DividedTax, 2, '.', '');
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
