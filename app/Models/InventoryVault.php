<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class InventoryVault extends Inventory
{
    //
    protected $table = 'inventory_vault';
    public $prefix = 'IV';
    public $type = 2;
}
