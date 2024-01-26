<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('books', function (Blueprint $table) {
         $table->increments('id');
         $table->string('name');
         $table->string('description')->nullable();
         $table->string('author')->nullable();
         $table->integer('class_id')->nullable();
         $table->string('price')->nullable();
         $table->integer('total_copies')->nullable();
         $table->integer('issued_copies')->nullable();
         $table->string('file_path')->nullable();
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
      Schema::dropIfExists('books');
   }
}
