<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankAccountSetupsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('bank_account_setups', function (Blueprint $table) {
         $table->increments('id');
         $table->integer('bank_id');
         $table->string('account_name');
         $table->string('account_number')->unique();
         $table->string('branch_name')->nullable();
         $table->string('contact_person')->nullable();
         $table->string('phone')->nullable();
         $table->string('designition')->nullable();
         $table->string('email')->nullable();
         $table->tinyInteger('status')->default(1);
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('bank_account_setups');
   }
}
