<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class InvoiceGood extends Inventory
{
    //
    protected $table = "invoice_good";
    protected $primaryKey = "id";
    public $timestamps = false;
}
