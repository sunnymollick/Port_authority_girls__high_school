<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsFeesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('accounts_fees', function (Blueprint $table) {
         $table->increments('id');
         $table->string('title')->nullable();
         $table->string('class_id');
         $table->string('month')->nullable();
         $table->tinyInteger('status')->default(1);
         $table->string('year')->nullable();
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
      Schema::dropIfExists('accounts_fees');
   }
}
