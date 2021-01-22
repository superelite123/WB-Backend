<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    //
    protected $table = "batch";
    protected $primaryKey = "batch_id";
    public  $timestamps = false;
    public function assets()
    {
        return $this->hasMany(Asset::class,'batch_id','batchid');
    }

    public function strain()
    {
        return $this->hasOne(Strainame::class, 'itemname_id', 'strainid');
    }
}
