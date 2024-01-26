<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use SMartins\PassportMultiauth\HasMultiAuthApiTokens;

class Student extends Authenticatable
{
   use Notifiable, HasMultiAuthApiTokens;

   protected $fillable = ['name', 'student_code'];
   protected $hidden = [
     'password', 'remember_token',
   ];


   public function attendance()
   {
      return $this->hasMany(Attendance::class);
   }

   public function enrolls()
   {
      return $this->hasMany(Enroll::class, 'student_id');
   }

   // Event Handler
   public static function boot()
   {
      parent::boot();
      static::deleting(function ($student) {
         $student->enrolls()->delete();
      });
   }
}
