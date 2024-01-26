<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('exams', function (Blueprint $table) {
         $table->increments('id');
         $table->string('name');
         $table->integer('class_id');
         $table->string('description')->nullable();
         $table->date('start_date');
         $table->date('end_date');
         $table->date('result_modification_last_date')->nullable();
         $table->string('main_marks_percentage')->default(100);
         $table->string('ct_marks_percentage')->default(0);
         $table->string('file_path')->nullable();
         $table->string('year')->nullable();
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
      Schema::dropIfExists('exams');
   }
}
