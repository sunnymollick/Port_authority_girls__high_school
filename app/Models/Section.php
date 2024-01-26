<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Section extends Model
{

   protected $fillable = ['name', 'class_id'];

   public function stdclass()
   {
      return $this->belongsTo(StdClass::class, 'class_id');
   }

   public function syllabus()
   {
      return $this->hasMany(Syllabus::class, 'section_id');
   }

   public function routine()
   {
      return $this->hasMany(ClassRoutine::class, 'section_id');
   }

   public function admission()
   {
      return $this->hasMany(AdmissionApplication::class, 'admitted_section');
   }

}
