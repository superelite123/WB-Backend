<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
class NDALog extends Model
{
    //
    protected $table = 'nda_log';
    protected $fillable = ['nda_id','user_id','type'];

    public function rUser()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function rNda()
    {
        return $this->belongsTo(Nda::class,'nda_id');
    }
}
