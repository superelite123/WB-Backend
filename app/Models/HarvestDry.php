<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helper\HasManyRelation;
use DB;

class HarvestDry extends Model
{
    //
    use HasManyRelation;
    protected $table = "harvests_dry";
    protected $fillable = ['parent_id','unit_weight','total_weight','remain_weight','cultivator_company_id','strain_id',
                           'cultivator_license_id'];
    public function items()
    {
        return $this->hasMany(HarvestItemDry::class,'parent_id');
    }

    public function Strain()
    {
        return $this->belongsTo(Strainame::class,'strain_id');
    }

    public function License()
    {
        return $this->belongsTo(Cultivator::class,'cultivator_license_id');
    }

    public function UnitOfWeight()
    {
        return $this->belongsTo(Unit::class,'unit_weight');
    }
    public function Room()
    {
        return $this->belongsTo(LocationArea::class,'cultivator_company_id');
    }

    public function getHarvest()
    {
        return Harvest::find(HarvestDynamics::find($this->parent_id)->parent_id);
    }

    public function get_total_weight()
    {
        $items = $this->items()->get();
        $data = [];
        $data['total_weight'] = 0;
        $data['item_count'] = 0;
        foreach($items as $item)
        {
            $data['total_weight'] += $item->weight;
        }
        $data['item_count'] = count($items);
        return $data;
    }
    
    public function get_harvest_list($date_range = null)
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
        
        return $this->select('harvests_dry.id','harvests.harvest_batch_id','harvests_dry.total_weight','harvests_dry.remain_weight','harvests_dry.created_at',
                             DB::raw("CONCAT(units.name,'-',units.abbriviation) AS unit"),
                             'c1.name','strainname.strain','c2.license')
                    ->leftjoin('harvestdynamics','harvests_dry.parent_id','=','harvestdynamics.id')
                    ->leftjoin('harvests','harvestdynamics.parent_id','=','harvests.id')
                    ->leftjoin('units','harvests_dry.unit_weight','=','unit_id')
                    ->leftjoin('location_area as c1','harvests_dry.cultivator_company_id','=','c1.location_id')
                    ->leftjoin('strainname','harvests_dry.strain_id','=','itemname_id')
                    ->leftjoin('cultivator as c2','harvests_dry.cultivator_license_id','=','c2.cultivator_id')
                    ->where('harvests_dry.archived','=','0')
                    ->whereRaw('DATE(harvests_dry.created_at) >= ?', [$date_range['start_date']])
                    ->whereRaw('DATE(harvests_dry.created_at) <= ?', [$date_range['end_date']])
                    ->orderby('harvests_dry.created_at','desc')
                    ->get();
    }
    public function get_harvest_dry_table_data($date_range)
    {
        $harvest_list = $this->get_harvest_list($date_range);
        
        foreach($harvest_list as $harvest)
        {
            $harvest['items'] = $harvest->items()->get();
        }

        return $harvest_list;
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
