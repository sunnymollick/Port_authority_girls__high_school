<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountsFeeItems extends Model
{

   protected $fillable = ['fee_master_id'];

   public function accountsFee()
   {
      return $this->belongsTo(AccountsFee::class, 'fee_master_id');
   }
}
