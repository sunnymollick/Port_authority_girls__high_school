<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
   protected $fillable = ['name', 'start_date', 'end_date'];

   public function marks()
   {
      return $this->hasMany(Mark::class, 'exam_id');
   }


   // Event Handler
   public static function boot()
   {
      parent::boot();

      static::deleting(function ($exam) {
         $exam->marks()->delete();
      });
   }
}
