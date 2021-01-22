<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clocking extends Model
{
    //
    protected $table = 'clocking';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(ContactPerson::class,'user_id');
    }

    public function get_clocking_chart_data($date)
    {

    }
}
