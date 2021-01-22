<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class FGInventory extends Inventory
{
    //
    protected $table = "fginventory";
    public $prefix = 'FG';
    public $type = 1;
}
