<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsFeeItemsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('accounts_fee_items', function (Blueprint $table) {
         $table->increments('id');
         $table->integer('fee_master_id');
         $table->integer('class_id');
         $table->integer('section_id');
         $table->integer('accounts_head_id');
         $table->string('amount')->nullable();
         $table->string('month')->nullable();
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
      Schema::dropIfExists('accounts_fee_items');
   }
}
