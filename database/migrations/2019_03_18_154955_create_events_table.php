<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('events', function (Blueprint $table) {
         $table->increments('id');
         $table->string('name');
         $table->text('details')->nullable();
         $table->string('location')->nullable();
         $table->dateTime('start_date')->nullable();
         $table->dateTime('end_date')->nullable();
         $table->tinyInteger('status')->default(1);
         $table->string('year');
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
      Schema::dropIfExists('events');
   }
}
