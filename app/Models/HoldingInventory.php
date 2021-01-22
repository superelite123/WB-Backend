<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class HoldingInventory extends Model
{
    protected $table = "holdinginventory";
    protected $fillable = ['stockimage','strainname','asset_type_id','upc_fk','batch_fk','coa','um','weight','qtyonhand','bestbefore'];

    public static function get_items()
    {
        // ->join('strainname','fginventory.strainname','=','strainname.itemname_id')
        // ->join('productcategory','productcategory.producttype_id','=','asset_type_id')
        return HoldingInventory::select('holdinginventory.*',DB::raw("CONCAT(invupccont.upc,'-',CONCAT(invupccont.strain,',',invupccont.type)) AS description"))
                    ->join('invupccont','iteminv_id','=','upc_fk')
                    ->get();
    }

    public function Strain()
    {
        return $this->belongsTo(Strainame::class,'strainname');
    }

    public function AssetType()
    {
        return $this->belongsTo(Producttype::class,'asset_type_id');
    }

    public function UnitOfWeight()
    {
        return $this->belongsTo(Unit::class,'um');
    }

    public function Room()
    {
        return $this->belongsTo(LocationArea::class,'cultivator_company_id');
    }

    public function batch_location()
    {
        return $this->hasMany(BatchRoom::class,'barcode','batch_fk');
    }

    public function get_harvest_id()
    {
        $curning = Curning::find($this->parent_id);
        return $curning->get_harvestsId1();
    }
}
