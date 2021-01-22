<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetPotal extends Model
{
    //
    protected $table = "asset_potal";
    protected $fillable = ['group_id','batch_id','assetscreated','asset_type','coafile'];

    public function assets()
    {
        return $this->hasMany(Asset::class,'group_id','group_id')->select('assets.*','productcategory.producttype as type_label')->join('productcategory','producttype_id','=','type_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class,'batchid','batch_id');
    }
}
