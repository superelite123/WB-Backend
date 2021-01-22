<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producttype extends Model
{
    //
    protected $table = "productcategory";

    protected $primaryKey = 'producttype_id';

    public function strains()
    {
        return $this->belongsToMany(Strainame::class,'invupccont','producttype_id','strain_id')
                    ->withPivot('upc');
    }

    public function ptWeight()
    {
        return $this->belongsTo(PtWeight::class,'pcategory');
    }
    
}
