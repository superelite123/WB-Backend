<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
class InvoicePaymentLog extends Model
{
    //
    protected $table = 'invoice_payment_log';
    protected $fillable = ['amount','order_id','type','allowed','sign_filename','d_personame','is_first','user_id','cash_serial'];

    public function Deliveryer()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
