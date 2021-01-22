<?php

namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class HarvestTracker extends Model
{
    //

    protected $table = 'harvest_tracker';
    public $timestamps  = false;
    public function get_list()
    {
        return $this->select('h1.harvest_batch_id','s1.strain','p1.producttype','harvest_tracker.datelastmodified','harvest_tracker.allocatedweight',
                             DB::raw("CONCAT(c1.location_id,'-',c1.name) AS location"))
                    ->leftjoin('harvests as h1','harvestid','=','h1.id')
                    ->leftjoin('strainname as s1','strain_id','=','s1.itemname_id')
                    ->leftjoin('productcategory as p1','type','=','p1.producttype_id')
                    ->leftjoin('location_area as c1','current_location','=','c1.location_id')
                    ->get();
    }
}
