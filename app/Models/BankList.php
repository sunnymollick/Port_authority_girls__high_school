<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankList extends Model
{

   protected $fillable = ['bank_name'];

   public function bankAccounts()
   {
      return $this->hasMany(BankAccountSetup::class, 'bank_id');
   }

   // Event Handler
   public static function boot()
   {
      parent::boot();

      static::deleting(function ($bankList) {
         $bankList->bankAccounts()->delete();
      });
   }
}
