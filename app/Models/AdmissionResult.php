<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionResult extends Model
{
   protected $fillable = ['file_path'];

   public function stdclass()
   {
      return $this->belongsTo(StdClass::class, 'class_id');
   }

   public function section()
   {
      return $this->belongsTo(Section::class, 'section_id');
   }
}
