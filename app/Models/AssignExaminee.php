<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignExaminee extends Model
{
   protected $fillable = ['exam_id', 'teacher_id'];
}
