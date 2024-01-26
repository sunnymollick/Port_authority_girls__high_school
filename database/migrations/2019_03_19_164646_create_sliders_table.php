<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlidersTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('sliders', function (Blueprint $table) {
         $table->increments('id');
         $table->string('title')->nullable();
         $table->string('sub_title')->nullable();
         $table->string('description')->nullable();
         $table->integer('order')->default(1);
         $table->string('file_path');
         $table->tinyInteger('status')->default(1);
         $table->integer('uploaded_by');
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
      Schema::dropIfExists('sliders');
   }
}
