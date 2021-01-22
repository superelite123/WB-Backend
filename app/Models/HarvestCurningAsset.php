<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarvestCurningAsset extends Model
{
    //
    protected $table = "harvest_curning_asset";
    protected $fillable = ['parent_id','metrc','weight','type'];
    public $timestamps = false;
    public function location()
    {
        return $this->hasMany(BatchRoom::class,'barcode','metrc');
    }
}
