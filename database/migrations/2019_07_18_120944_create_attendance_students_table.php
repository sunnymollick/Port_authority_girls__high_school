<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceStudentsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('attendance_students', function (Blueprint $table) {
         $table->increments('id');
         $table->date('attendance_date');
         $table->string('student_code');
         $table->string('class_id');
         $table->string('section_id');
         $table->string('year');
         $table->string('in_time')->nullable();
         $table->string('out_time')->nullable();
         $table->string('late')->nullable();
         $table->string('status')->nullable()->comment('A absent P present Lv leave L Late');
         $table->string('remarks')->nullable();
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
      Schema::dropIfExists('attendance_students');
   }
}
