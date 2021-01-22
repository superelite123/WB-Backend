<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceOption extends Model
{
    /**
     * Type
     * 1:invoice discount
     */
    protected $fillable = ['note','value','type'];
}
