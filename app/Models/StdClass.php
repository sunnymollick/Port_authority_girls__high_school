<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StdClass extends Model
{
   protected $fillable = ['name', 'in_digit'];

   public function sections()
   {
      return $this->hasMany(Section::class, 'class_id')->orderBy('name');
   }

   public function subjects()
   {
      return $this->hasMany(Subject::class, 'class_id');
   }

   public function syllabus()
   {
      return $this->hasMany(Syllabus::class, 'class_id');
   }

   public function books()
   {
      return $this->hasMany(Book::class, 'class_id');
   }

   public function routine()
   {
      return $this->hasMany(ClassRoutine::class, 'class_id');
   }

   public function enrolls()
   {
      return $this->hasMany(Enroll::class, 'class_id');
   }


   public function fee_roles()
   {
      return $this->hasMany(Std_Fee_Category::class, 'class_id');
   }

   public function admission()
   {
      return $this->hasMany(AdmissionApplication::class, 'admitted_class');
   }

   // Event Handler
   public static function boot()
   {
      parent::boot();

      static::deleting(function ($stdclass) {
         $stdclass->books()->delete();
         $stdclass->subjects()->delete();
         $stdclass->sections()->delete();
         $stdclass->syllabus()->delete();
      });
   }

}
