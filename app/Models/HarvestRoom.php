<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class HarvestRoom extends Model
{
    //
    protected $table = "harvest_rooms";
    public $fillable = ['name','user','tables','matrix_col','archived'];
    
    public function room_name()
    {
        return $this->belongsTo(LocationArea::class,'room_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user');
    }

}
