<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class InvoiceExportLog extends Model
{
    protected $table = 'invoice_export_logs';

    public function rUser()
    {
      return $this->belongsTo(User::class,'user_id');
    }

    public function rOrder()
    {
      return $this->belongsTo(InvoiceNew::class,'invoice_id');
    }

    public function getOrderLabelAttribute()
    {
      $order = $this->rOrder;
      return $order != null?$order->number2:'';
    }
}
