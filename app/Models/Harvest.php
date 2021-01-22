<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helper\HasManyRelation;
use DB;

class Harvest extends Model
{
    //
    use HasManyRelation;
    
    protected $fillable = ['harvest_batch_id','unit_weight','total_weight','cultivator_company_id','strain_id',
                           'cultivator_license_id'];
    public function items()
    {
        return $this->hasMany(HarvestItem::class,'harvest_id');
    }

    public function waist_item()
    {
        return $this->hasMany(HarvestWaistItem::class,'parent_id');
    }

    public function fresh_item()
    {
        return $this->hasMany(HarvestFreshItem::class,'parent_id');
    }

    public function Strain()
    {
        return $this->belongsTo(Strainame::class,'strain_id');
    }

    public function License()
    {
        return $this->belongsTo(Cultivator::class,'cultivator_license_id');
    }

    public function Room()
    {
        return $this->belongsTo(LocationArea::class,'cultivator_company_id');
    }

    public function UnitOfWeight()
    {
        return $this->belongsTo(Unit::class,'unit_weight');
    }

    public function batch_location()
    {
        return $this->hasMany(BatchRoom::class,'barcode','harvest_batch_id');
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

    public function get_harvest_list($date_range = null,$archived)
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
       
        $que = $this->select('harvests.id','harvests.harvest_batch_id','harvests.total_weight','harvests.created_at',
                             DB::raw("CONCAT(units.name,'-',units.abbriviation) AS unit"),
                             'c1.name','strainname.strain','c2.license')
                    ->leftjoin('units','unit_weight','=','unit_id')
                    ->leftjoin('location_area as c1','cultivator_company_id','=','c1.location_id')
                    ->leftjoin('strainname','strain_id','=','itemname_id')
                    ->leftjoin('cultivator as c2','cultivator_license_id','=','c2.cultivator_id');
        
        if($archived != -1)
            $que = $que->where('archived',$archived);
        
        $que = $que->whereRaw('DATE(created_at) >= ?', [$date_range['start_date']])
            ->whereRaw('DATE(created_at) <= ?', [$date_range['end_date']])
            ->orderby('created_at','desc')
            ->get();
        return $que;
    }

    public function get_harvest_table_data($date_range)
    {
        $harvest_list = $this->get_harvest_list($date_range,'0');
        
        foreach($harvest_list as $harvest)
        {
            $harvest['items'] = $harvest->items()->get();
            $harvest['waist_item'] = $harvest->waist_item()->get();
            $harvest['fresh_item'] = $harvest->fresh_item()->get();
        }

        return $harvest_list;
    }

    public function get_harvest_archived_table_data($date_range,$all = 0)
    {
        //get archived record
        $harvest_list = $this->get_harvest_list($date_range,'1');
        
        if($all == 1)
        {
            $harvest_list = $this->get_harvest_list($date_range,'-1');
        }

        foreach($harvest_list as $harvest)
        {
            $harvest['items'] = $harvest->items()->get();
            $harvest['waist_item'] = $harvest->waist_item()->get();
            $harvest['fresh_item'] = $harvest->fresh_item()->get();
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

    public function get_harvest_chart_data($s_date = null,$e_date = null)
    {
        $record_collection = $this->select('harvest_batch_id as a','total_weight as b')
                       ->whereRaw('DATE(created_at) >= ?', [$s_date])
                       ->whereRaw('DATE(created_at) <= ?', [$e_date])
                       ->orderby('created_at','asc')
                       ->get();
        $result = [];
        foreach($record_collection as $val)
        {
            $result['label'][]['label'] = $val->a;
            $result['value'][]['value'] = $val->b / 5;
        }
        return json_encode($result);
    }

    public function get_strain_chart_data($s_date = null, $e_date = null)
    {
        $record_collection = $this->select(DB::raw("CONCAT(s1.strain,'-',s1.strainalias) AS a"),DB::raw("count(*) AS b"))
                                  ->leftjoin('harvest_item as h2','harvests.id','=','h2.harvest_id')
                                  ->leftjoin('strainname as s1','harvests.strain_id','=','s1.itemname_id')
                                  ->groupby('harvests.strain_id')
                                  ->whereRaw('DATE(harvests.created_at) >= ?', [$s_date])
                                  ->whereRaw('DATE(harvests.created_at) <= ?', [$e_date])
                                  ->orderby('created_at','asc')
                                  ->orderby('b','desc')
                                  ->get();
        $result = [];
        foreach($record_collection as $val)
        {
            $result['label'][]['label'] = $val->a;
            $result['value'][]['value'] = $val->b;
        }
        return json_encode($result);
    }
}
