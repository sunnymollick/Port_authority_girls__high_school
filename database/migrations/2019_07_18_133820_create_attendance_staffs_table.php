<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceStaffsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('attendance_staffs', function (Blueprint $table) {
         $table->increments('id');
         $table->date('attendance_date');
         $table->string('staff_id');
         $table->string('staff_name')->nullable();
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
      Schema::dropIfExists('attendance_staffs');
   }
}
