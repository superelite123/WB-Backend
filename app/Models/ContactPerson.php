<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactPerson extends Model
{
    //
    protected $table = 'contactperson';
    protected $primaryKey = 'contact_id';

    public static function getSalesPerson()
    {
        return ContactPerson::where('contacttype','3')->get();
    }

    public function get_contacttype_name()
    {
        return $this->belongsTo(Contacttype::class);
    }
}
