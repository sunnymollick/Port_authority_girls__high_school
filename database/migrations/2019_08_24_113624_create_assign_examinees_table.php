<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignExamineesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('assign_examinees', function (Blueprint $table) {
         $table->increments('id');
         $table->integer('exam_id');
         $table->integer('class_id');
         $table->integer('section_id');
         $table->integer('subject_id');
         $table->integer('teacher_id');
         $table->string('year');
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
      Schema::dropIfExists('assign_examinees');
   }
}
