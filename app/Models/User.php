<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;
use SMartins\PassportMultiauth\HasMultiAuthApiTokens;

class User extends Authenticatable
{
   use HasMultiAuthApiTokens, Notifiable, HasRoles;

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


}
