<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contacttype extends Model
{
    //
    protected $table = 'contacttype';
    protected $primaryKey = "ct_id";

    public function get_contactpersion()
    {
        return $this->hasMany(ContactPerson::class,'contacttype');
    }
}
