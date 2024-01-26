<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmissionResultsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('admission_results', function (Blueprint $table) {
         $table->increments('id');
         $table->string('title');
         $table->integer('class_id')->nullable();
         $table->integer('section_id')->nullable();
         $table->string('file_path');
         $table->tinyInteger('status')->nullable()->default(1);
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
      Schema::dropIfExists('admission_results');
   }
}
