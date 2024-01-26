<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnrollsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('enrolls', function (Blueprint $table) {
         $table->increments('id');
         $table->integer('student_id');
         $table->string('student_code')->nullable();
         $table->integer('class_id');
         $table->integer('section_id');
         $table->integer('roll')->nullable();
         $table->integer('subject_id')->default(0);
         $table->date('date_added');
         $table->string('year');
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('enrolls');
   }
}
