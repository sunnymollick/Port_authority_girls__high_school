<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use SMartins\PassportMultiauth\HasMultiAuthApiTokens;

class Teacher extends Authenticatable
{
   use Notifiable, HasMultiAuthApiTokens;
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

   public function subjects()
   {
      return $this->hasMany(Subject::class, 'teacher_id');
   }
}
