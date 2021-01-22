<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // public function roles(){
    //     return $this->belongsToMany(Models\Role::class);
    // }

    // public function hasAnyRoles($roles){
    //     return null != $this->roles()->whereIn('name',$roles)->first();
    // }

    // public function hasAnyRole($role){
    //     return null != $this->roles()->where('name',$role)->first();
    // }
}
