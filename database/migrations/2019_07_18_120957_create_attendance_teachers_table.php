<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceTeachersTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('attendance_teachers', function (Blueprint $table) {
         $table->increments('id');
         $table->date('attendance_date');
         $table->string('teacher_id');
         $table->string('teacher_name')->nullable();
         $table->string('mobile')->nullable();
         $table->string('post')->nullable();
         $table->string('designation')->nullable();
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
      Schema::dropIfExists('attendance_teachers');
   }
}
