<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarvestItemDry extends Model
{
    protected $table = "harvest_item_dry";
    protected $fillable = ['parent_id','weight'];
    public  $timestamps = false;
}
