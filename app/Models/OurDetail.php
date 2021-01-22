<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OurDetail extends Model
{
    //
    protected $table = "ourdetails";
    protected $fillable = [
        'companyname','address1','city','state','zip','phone','logo'
    ];
}
