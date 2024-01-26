<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class StdParent extends Authenticatable
{
   use Notifiable;
   protected $table = 'parents';
   protected $hidden = [
     'password', 'remember_token',
   ];
   protected $fillable = ['name', 'email', 'gender', 'phone', 'address', 'profession', 'password'];
}
