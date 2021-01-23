<?php

namespace App\Wb;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class WbUser extends Authenticatable
{
    use Notifiable,HasRoles;
    //
}
