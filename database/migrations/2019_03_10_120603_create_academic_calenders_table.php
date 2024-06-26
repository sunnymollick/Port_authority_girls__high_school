<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAcademicCalendersTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('academic_calenders', function (Blueprint $table) {
         $table->increments('id');
         $table->string('name');
         $table->string('year');
         $table->tinyInteger('status')->default(1);
         $table->string('file_path')->nullable();
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
      Schema::dropIfExists('academic_calenders');
   }
}
