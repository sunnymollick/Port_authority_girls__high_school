<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialSessionSubject extends Model
{
    protected $table = 'special_session_subjects';
    //
    public function stdclass()
   {
      return $this->belongsTo(StdClass::class, 'class_id');
   }
}
