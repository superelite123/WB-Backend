<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarvestWaistItem extends Model
{
    //

    protected $table = "harvest_item_waist";
    public $fillable = ['parent_id','waist_type','weight','metrc'];
    public  $timestamps = false;
}
