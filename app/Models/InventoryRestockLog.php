<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryRestockLog extends Model
{
    //
    protected $table = 'inventory_restock_log';

    public function rOrder()
    {
        return $this->belongsTo(InvoiceNew::class,'order_id');
    }
}
