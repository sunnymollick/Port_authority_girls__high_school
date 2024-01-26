<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
   protected $fillable = ['name', 'class_id'];

   public function stdclass()
   {
      return $this->belongsTo(StdClass::class, 'class_id');
   }

   public function bookRequest()
   {
      return $this->hasMany(BookRequest::class);
   }

   public function issued_book()
   {
      return $this->bookRequest()->where('status', '=', 0);
   }

   // Event Handler
   public static function boot()
   {
      parent::boot();
      static::deleting(function ($book) { // Delete all book request related to a book
         $book->bookRequest()->delete();
      });
   }
}
