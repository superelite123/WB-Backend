<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{   
    protected $fillable = ['batch_id','qty','weight','type_id','asset_id','barcode_id','group_id'];

    public function batch()
    {
        return $this->belongsTo(Batch::class,'batchid','batch_id');
    }

    public function asset_group()
    {
        return $this->belongsTo(AssetPotal::class,'id','group_id');
    }
}
