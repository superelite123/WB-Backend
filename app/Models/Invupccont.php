<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invupccont extends Model
{
    //
    protected $table = "invupccont";
    protected $primaryKey = "iteminv_id";

    public function HoldingInventory()
    {
        return $this->hasOne(HoldingInventory::class,'upc_fk');
    }
}
