<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSyllabasTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('syllabus', function (Blueprint $table) {
         $table->increments('id');
         $table->string('title');
         $table->integer('class_id');
         $table->integer('section_id');
         $table->integer('subject_id');
         $table->integer('uploaded_by');
         $table->string('file_path');
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
      Schema::dropIfExists('syllabus');
   }
}
