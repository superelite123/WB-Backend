<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    //
    protected $table='distributor';
    protected $primaryKey = 'distributor_id';


    public function state_name()
    {
        return $this->belongsTo(State::class,'state');
    }
}