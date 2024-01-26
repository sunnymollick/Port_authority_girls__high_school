<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Syllabus extends Model
{
   protected $table = 'syllabus';
   protected $fillable = ['title', 'description', 'class_id', 'subject_id', 'year'];

   public function stdclass()
   {
      return $this->belongsTo(StdClass::class, 'class_id');
   }

   public function subject()
   {
      return $this->belongsTo(Subject::class, 'subject_id');
   }

   public function section()
   {
      return $this->belongsTo(Section::class, 'section_id');
   }
}
