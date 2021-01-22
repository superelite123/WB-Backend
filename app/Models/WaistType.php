<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaistType extends Model
{
    //
    protected $table = "waist_type";
    public $fillable = ['label'];
    public  $timestamps = false;
}
