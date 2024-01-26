<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassRoutinesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('class_routines', function (Blueprint $table) {
         $table->increments('id');
         $table->integer('class_id');
         $table->integer('section_id');
         $table->integer('subject_id');
         $table->integer('teacher_id');
         $table->string('class_room_id')->nullable()->default(00);
         $table->string('time_start')->nullable()->default(00);
         $table->string('time_start_min')->default(00)->default(00);
         $table->string('time_end')->nullable()->default(00);
         $table->string('time_end_min')->nullable()->default(00);
         $table->string('day');
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
      Schema::dropIfExists('class_routines');
   }
}
