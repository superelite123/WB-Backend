<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryVSIgnored extends Model
{
    //
    protected $table = 'inventory_vs_ignored';
    protected $fillable = ['parent_id','child_id','type'];
}
