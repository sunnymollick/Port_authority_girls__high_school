<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Std_Fee_Category extends Model
{

   protected $table = 'std_fee_categories';
   protected $fillable = ['name'];

   public function fee_items()
   {
      return $this->hasMany(Fee_item::class, 'fee_category_id');
   }

   public function std_class()
   {
      return $this->belongsTo(StdClass::class, 'class_id');
   }

   public function invoice()
   {
      return $this->hasMany(Invoice::class, 'roles_id');
   }


   // Event Handler
   public static function boot()
   {
      parent::boot();
      static::deleting(function ($feecategory) { // Delete all book request related to a book
         $feecategory->fee_items()->delete();
         $feecategory->invoice()->delete();
      });
   }
}
