<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStdParentsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('parents', function (Blueprint $table) {
         $table->increments('id');
         $table->string('father_name')->nullable();
         $table->string('mother_name')->nullable();
         $table->string('parent_code')->unique()->nullable();
         $table->string('email')->nullable();
         $table->string('password')->nullable();
         $table->string('gender')->nullable();
         $table->string('blood_group')->nullable();
         $table->string('phone')->nullable();
         $table->string('address')->nullable();
         $table->string('profession')->nullable();
         $table->string('remember_token')->nullable();
         $table->string('file_path')->nullable()->default('assets/images/parent_image/default.png');
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
      Schema::dropIfExists('parents');
   }
}
