<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class BatchRoom extends Model
{
    //
    protected $table = "batch_room";
    protected $fillable = ['barcode','room_id','status','record_id','type','parent_id','handler','created_at','updated_at'];
    public $timestamps = true;

    public function location()
    {
        return $this->belongsTo(LocationArea::class,'room_id');
    }
    
    public function handle_user()
    {
        return $this->belongsTo(User::class,'handler');
    }
}
