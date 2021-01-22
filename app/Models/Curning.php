<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helper\CommonFunction;
use App\Helper\HasManyRelation;
use DB;
class Curning extends Model
{
    use CommonFunction;
    use HasManyRelation;
    protected $table = "harvest_curning";
    protected $fillable = ['parent_id','unit_weight','total_weight','cultivator_company_id','strain_id',
    'cultivator_license_id'];

    public function Strain()
    {
        return $this->belongsTo(Strainame::class,'strain_id');
    }

    public function asset()
    {
        return $this->hasMany(HarvestCurningAsset::class,'parent_id');
    }

    public function waste()
    {
        return $this->hasMany(HarvestWaistItem::class,'parent_id')->where('waist_type',5);
    }

    public function get_list($date_range = null)
    {
        if($date_range == null)
        {
            $date_range['start_date'] = date('Y-m-d', strtotime('today - 31 days'));
            $date_range['end_date']   = date('Y-m-d');
        }
        else
        {
            $date_range = $this->convertDateRangeFormat($date_range);
        }

        return $this->select('harvest_curning.id','harvests.harvest_batch_id','harvest_curning.total_weight','harvest_curning.remain_weight','harvest_curning.created_at',
                             DB::raw("CONCAT(units.name,'-',units.abbriviation) AS unit"),
                             'c1.name','strainname.strain','c2.license')
                    ->leftjoin('harvests_dry','harvest_curning.parent_id','=','harvests_dry.id')
                    ->leftjoin('harvestdynamics','harvests_dry.parent_id','=','harvestdynamics.id')
                    ->leftjoin('harvests','harvestdynamics.parent_id','=','harvests.id')
                    ->leftjoin('units','harvest_curning.unit_weight','=','unit_id')
                    ->leftjoin('location_area as c1','harvest_curning.cultivator_company_id','=','c1.location_id')
                    ->leftjoin('strainname','harvest_curning.strain_id','=','itemname_id')
                    ->leftjoin('cultivator as c2','harvest_curning.cultivator_license_id','=','c2.cultivator_id')
                    ->where('harvest_curning.archived','=',0)
                    ->whereRaw('DATE(harvest_curning.created_at) >= ?', [$date_range['start_date']])
                    ->whereRaw('DATE(harvest_curning.created_at) <= ?', [$date_range['end_date']])
                    ->orderby('harvest_curning.created_at','desc')
                    ->get();
    }

public function get_process_history_data($date_range = null)
    {
        if($date_range == null)
        {
            $date_range['start_date'] = date('Y-m-d', strtotime('today - 31 days'));
            $date_range['end_date']   = date('Y-m-d');
        }
        else
        {
            $date_range = $this->convertDateRangeFormat($date_range);
        }

        $res = $this->select('harvest_curning.id','harvest_curning.parent_id','harvests.harvest_batch_id','harvest_curning.total_weight','harvest_curning.updated_at',
                             DB::raw("CONCAT(units.name,'-',units.abbriviation) AS unit"),
                             'c1.name','strainname.strain','c2.license')
                    ->leftjoin('harvests_dry','harvest_curning.parent_id','=','harvests_dry.id')
                    ->leftjoin('harvestdynamics','harvests_dry.parent_id','=','harvestdynamics.id')
                    ->leftjoin('harvests','harvestdynamics.parent_id','=','harvests.id')
                    ->leftjoin('units','harvest_curning.unit_weight','=','unit_id')
                    ->leftjoin('location_area as c1','harvest_curning.cultivator_company_id','=','c1.location_id')
                    ->leftjoin('strainname','harvest_curning.strain_id','=','itemname_id')
                    ->leftjoin('cultivator as c2','harvest_curning.cultivator_license_id','=','c2.cultivator_id')
                    ->where('harvest_curning.archived','=',1)
                    ->whereRaw('DATE(harvest_curning.created_at) >= ?', [$date_range['start_date']])
                    ->whereRaw('DATE(harvest_curning.created_at) <= ?', [$date_range['end_date']])
                    ->orderby('harvest_curning.created_at','desc')
                    ->get();
        foreach($res as $item)
        {
            $item['items'] = $item->asset()->get();
            $harvest_id = $item->get_harvestsId1();
            $item['waste'] = HarvestWaistItem::where([['parent_id','=',$harvest_id],['waist_type','=','5']])->get();
            //$item['waste'] = $item->waste()->get();
        }

        return $res;
    }

    public function fgItems()
    {
        return $this->hasMany(FGInventory::class,'parent_id');
    }

    public function get_harvestsId()
    {
        $id = $this->parent_id;
        if($id == null) return -1;
        if(HarvestDry::find($id) != null)
            $id = HarvestDry::find($id)->parent_id;
        else
            return -1;
        if(HarvestDynamics::find($id) != null)
            $id = HarvestDynamics::find($id)->parent_id;
        else
            return -1;
        if(Harvest::find($id) != null)
            return Harvest::find($id)->harvest_batch_id;
        else
            return -1;
        
    }
    
    public function get_harvestsId1()
    {
        $id = $this->parent_id;
        if($id == null) return -1;

        if(HarvestDry::find($id) != null)
            $id = HarvestDry::find($id)->parent_id;
        else
            return -1;
        if(HarvestDynamics::find($id) != null)
            $id = HarvestDynamics::find($id)->parent_id;
        else
            return -1;
        if(Harvest::find($id) != null)
            return Harvest::find($id)->id;
        else
            return -1;
    }

    public static function get_invoice_items($cIds)
    {
        //DB::raw('DATE_FORMAT(i.created_at,"%Y-%m-%d") as idate'),
        $res = DB::table('harvest_curning as c')
                 ->select('i.id as iid','p.producttype','i.number',DB::raw('DATE_FORMAT(i.created_at,"%Y-%m-%d") as idate'),'cs.clientname','f.fgasset_id','it.qty')
                 ->join('fginventory as f','c.id','=','f.parent_id')
                 ->join('invoice_items as it','f.fgasset_id','=','it.item_id')
                 ->join('invoices as i','it.invoice_id','=','i.id')
                 ->join('customers as cs','i.customer_id','=','cs.client_id')
                 ->join('productcategory as p','f.asset_type_id','=','p.producttype_id')
                 ->whereIn('c.id', $cIds)
                 ->orderby('i.id','asc')
                 ->get();

        $groupingRes = [];

        foreach($res as $val) {
            $groupingRes[$val->iid]['items'][] = $val;
            $groupingRes[$val->iid]['info']['clientName'] = $val->clientname;
            $groupingRes[$val->iid]['info']['number'] = $val->number;
            $groupingRes[$val->iid]['info']['idate'] = $val->idate;
        }

        return $groupingRes;
    }
}