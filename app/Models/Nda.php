<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nda extends Model
{
    //
    public function rState()
    {
        return $this->belongsTo(State::class,'state_id');
    }
    public function rCustomerType()
    {
        return $this->belongsTo(CustomerType::class,'customer_type_id');
    }
}
