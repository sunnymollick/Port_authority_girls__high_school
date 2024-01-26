<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
   protected $fillable = ['name', 'class_id', 'subject_marks', 'pass_marks'];

   public function stdclass()
   {
      return $this->belongsTo(StdClass::class, 'class_id');
   }

   public function teacher()
   {
      return $this->belongsTo(Teacher::class, 'teacher_id');
   }


   public function syllabus()
   {
      return $this->hasMany(Syllabus::class, 'subject_id');
   }

   public function routine()
   {
      return $this->hasMany(ClassRoutine::class, 'subject_id');
   }

   public function enrolls()
   {
      return $this->hasMany(Enroll::class, 'subject_id');
   }


   // Event Handler
   public static function boot()
   {
      parent::boot();

      static::deleting(function ($subject) {
         $subject->syllabus()->delete();
      });
   }
}
