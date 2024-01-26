<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccountSetup extends Model
{
   protected $fillable = ['account_number', 'bank_id'];

   public function bank()
   {
      return $this->belongsTo(BankList::class, 'bank_id');
   }
}
