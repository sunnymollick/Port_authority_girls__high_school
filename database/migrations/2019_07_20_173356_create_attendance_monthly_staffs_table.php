<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceMonthlyStaffsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('attendance_monthly_staffs', function (Blueprint $table) {
         $table->increments('id');
         $table->string('staff_id');
         $table->string('staff_name');
         $table->string('department');
         $table->string('designation');
         $table->string('month');
         $table->string('year');
         $table->string('one')->nullable();
         $table->string('two')->nullable();
         $table->string('three')->nullable();
         $table->string('four')->nullable();
         $table->string('five')->nullable();
         $table->string('six')->nullable();
         $table->string('seven')->nullable();
         $table->string('eight')->nullable();
         $table->string('nine')->nullable();
         $table->string('ten')->nullable();
         $table->string('eleven')->nullable();
         $table->string('twelve')->nullable();
         $table->string('thirteen')->nullable();
         $table->string('fourteen')->nullable();
         $table->string('fifteen')->nullable();
         $table->string('sixteen')->nullable();
         $table->string('seventeen')->nullable();
         $table->string('eightteen')->nullable();
         $table->string('nineteen')->nullable();
         $table->string('twenty')->nullable();
         $table->string('twentyone')->nullable();
         $table->string('twentytwo')->nullable();
         $table->string('twentythree')->nullable();
         $table->string('twentyfour')->nullable();
         $table->string('twentyfive')->nullable();
         $table->string('twentysix')->nullable();
         $table->string('twentyseven')->nullable();
         $table->string('twentyeight')->nullable();
         $table->string('twentynine')->nullable();
         $table->string('thirty')->nullable();
         $table->string('thirtyone')->nullable();
         $table->string('total_present')->nullable();
         $table->string('total_absent')->nullable();
         $table->string('total_leave')->nullable();
         $table->string('total_late')->nullable();
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
      Schema::dropIfExists('attendance_monthly_staffs');
   }
}
