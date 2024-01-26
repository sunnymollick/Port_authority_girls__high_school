<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('students', function (Blueprint $table) {
         $table->increments('id');
         $table->string('std_code')->unique();
         $table->string('name');
         $table->date('dob')->nullable();
         $table->string('gender')->nullable();
         $table->string('religion')->nullable();
         $table->string('blood_group')->nullable();
         $table->string('address')->nullable();
         $table->string('phone')->nullable();
         $table->string('email')->nullable();
         $table->string('password')->nullable();
         $table->integer('parent_id')->nullable();
         $table->string('remember_token')->nullable();
         $table->tinyInteger('status')->default(1);
         $table->string('file_path')->nullable()->default('assets/images/default/student.png');
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
      Schema::dropIfExists('students');
   }
}
