<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvImportHistoryItem extends Model
{
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['p_id','inv_id','i_type'];
    public function rFgInventory()
    {
        return $this->belongsTo(FGInventory::class, 'inv_id');
    }
    public function rInventoryVault()
    {
        return $this->belongsTo(InventoryVault::class, 'inv_id');
    }
}
