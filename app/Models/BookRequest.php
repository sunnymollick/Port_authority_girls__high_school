<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookRequest extends Model
{
   protected $fillable = ['book_id', 'student_id', 'issue_start_date', 'issue_end_date'];

   public $timestamps = false;

   public function book()
   {
      return $this->belongsTo(Book::class, 'book_id');
   }
}
