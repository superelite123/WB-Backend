<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarvestItem extends Model
{
    //
    protected $table = "harvest_item";
    protected $fillable = ['plant_tag','weight'];
    public  $timestamps = false;
}
