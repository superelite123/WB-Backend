<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Strainame extends Model
{
    //
    protected $table = "strainname";
    protected $primaryKey = "itemname_id";

    public function producttypes()
    {
        return $this->belongsToMany(Producttype::class,'invupccont','strain_id','producttype_id')
                    ->withPivot('upc');
    }
}
