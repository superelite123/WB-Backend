<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Helper\HasManyRelation;
class Inventory extends Model
{
    use HasManyRelation;
    protected $primaryKey = "fgasset_id";
    protected $fillable = ['strainname','upc_fk','batch_fk','coa',
                           'coafil_fk','um','weight','qtyonhand','parent_id'];
    public function upc()
    {
        return $this->belongsTo(UPController::class,'upc_fk');
    }

    public function Strain()
    {
        return $this->belongsTo(Strainame::class,'strainname');
    }
    public function Harvest()
    {
        return $this->belongsTo(Harvest::class,'parent_id');
    }
    public function CombineLog()
    {
        return $this->hasMany(InventoryVSIgnored::class,'parent_id');
    }
    public function AssetType()
    {
        return $this->belongsTo(Producttype::class,'asset_type_id');
    }

    public function UnitOfWeight()
    {
        return $this->belongsTo(Unit::class,'um');
    }

    public function rRestockLog()
    {
        return $this->hasOne(InventoryRestockLog::class,'fgasset_id');
    }
    public function getHarvestBatchIDAttribute()
    {
        return $this->Harvest != null?$this->Harvest->harvest_batch_id:'';
    }
    public function getStrainLabelAttribute()
    {
        return $this->Strain != null?$this->Strain->strain:'';
    }
    public function getPTypeAttribute()
    {
        return $this->AssetType != null?$this->AssetType->producttype:'';
    }
    public function Room()
    {
        return $this->belongsTo(LocationArea::class,'cultivator_company_id');
    }
    public function upcR()
    {
        return $this->belongsTo(UPController::class,'upc_fk');
    }
    public function getUPCLabelAttribute()
    {
        $upc = $this->upcR != null?$this->upcR->upc:'No UPC';
    }
    public function unitVolume()
    {
        return $this->belongsTo(Unit::class,'um');
    }

    public function getUnitsAttribute()
    {
        $units = $this->AssetType != null?$this->AssetType->units:0;
        return $units == null?0:$units;
    }

    public function getCoaNameAttribute()
    {
        $coa = $this->coa;
        if($coa == null || $coa == 'No COA')
        {
            $batch_fk = Harvest::find($this->parent_id);
            $batch_fk = $batch_fk != null?$batch_fk->harvest_batch_id:'No';

            $coa = $batch_fk.'_COA.pdf';
        }

        return $coa;
    }

    public function getDescriptionAttribute()
    {
        $desc  = $this->metrc_tag;
        $desc .= '-';
        $desc .= $this->Strain != null ? $this->Strain->strain:'No Strain';
        $desc .= ' ';
        $desc .= $this->AssetType != null ? $this->AssetType->producttype:'No AssetType';
        $desc .= ' ';

        return $desc;
    }

    public function get_items($strain = 0,$p_type = 0)
    {
        $inventory = [];
        $condition = [['qtyonhand','>','0'],['status','1']];
        if($strain != 0)
        {
            $condition[] = ['strainname',$strain];
        }
        if($p_type != 0)
        {
            $condition[] = ['asset_type_id',$p_type];
        }

        $res = $this->where($condition)->orderby('harvested_date')->get();

        foreach($res as $val)
        {

            $val->i_type = $this->type;
            $val->description = $val->Description;
            $val->status = '0';
            $val->scanned_metrc = '';
            $val->coa  = $val->CoaName;
            $inventory[] = $val;
        }
        return $inventory;
    }
}
