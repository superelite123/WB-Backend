<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarvestFreshItem extends Model
{
    //
    protected $table = "harvest_fresh_item";
    protected $fillable = ['parent_id','metrc','weight'];
    public  $timestamps = false;

    public function location()
    {
        return $this->hasMany(BatchRoom::class,'barcode','metrc');
    }
}
