<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryIgnored extends Model
{
    //
    protected $table = 'inventory_ignored';
    protected $primaryKey = "id";
    protected $fillable = ['fgasset_id','strainname','asset_type_id','upc_fk','batch_fk','coa',
                           'coafile_fk','um','weight','qtyonhand','parent_id'];
    public function CombineLog()
    {
        return $this->hasMany(InventoryVSIgnored::class,'parent_id');
    }
}
