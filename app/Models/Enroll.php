<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enroll extends Model
{
   protected $fillable = ['student_id', 'enroll_code', 'class_id', 'section_id', 'roll'];
   public $timestamps = false;

   public function student()
   {
      return $this->belongsTo(Student::class, 'student_id');
   }

   public function stdclass()
   {
      return $this->belongsTo(StdClass::class, 'class_id');
   }

   public function section()
   {
      return $this->belongsTo(Section::class, 'section_id');
   }

   public function subject()
   {
      return $this->belongsTo(Subject::class, 'subject_id');
   }
}
