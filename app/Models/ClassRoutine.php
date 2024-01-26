<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoutine extends Model
{
   protected $fillable = ['name', 'class_id', 'section_id', 'subject_id', 'day'];

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
