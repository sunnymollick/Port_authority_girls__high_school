<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeachersTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('teachers', function (Blueprint $table) {
         $table->increments('id');
         $table->string('teacher_code')->unique();
         $table->string('name');
         $table->string('qualification')->nullable();
         $table->string('subject')->nullable();
         $table->string('marital_status')->nullable();
         $table->date('dob')->nullable();
         $table->date('doj')->nullable();
         $table->string('gender')->nullable();
         $table->string('religion')->nullable();
         $table->string('blood_group')->nullable();
         $table->string('address')->nullable();
         $table->string('phone')->nullable();
         $table->string('email')->nullable();
         $table->string('remember_token')->nullable();
         $table->string('password')->nullable();
         $table->string('designation')->nullable();
         $table->integer('order')->default(50)->nullable();
         $table->string('file_path')->nullable()->default('assets/images/teacher_image/default.png');
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
      Schema::dropIfExists('teachers');
   }
}
