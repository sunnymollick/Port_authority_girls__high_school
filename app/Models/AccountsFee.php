<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountsFee extends Model
{

   protected $fillable = ['class_id'];

   public function stdclass()
   {
      return $this->belongsTo(StdClass::class, 'class_id');
   }

   public function feeItems()
   {
      return $this->hasMany(AccountsFeeItems::class, 'fee_master_id');
   }

   // Event Handler
   public static function boot()
   {
      parent::boot();
      static::deleting(function ($accountsFee) { // Delete all book request related to a book
         $accountsFee->feeItems()->delete();
      });
   }
}
