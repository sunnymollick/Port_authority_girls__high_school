<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsExceptionalStudentsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('accounts_exceptional_students', function (Blueprint $table) {
         $table->increments('id');
         $table->string('student_id');
         $table->integer('class_id');
         $table->integer('section_id');
         $table->integer('accounts_head_id');
         $table->string('amount')->nullable();
         $table->string('month')->nullable();
         $table->string('year')->nullable();
         $table->tinyInteger('status')->nullable();
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
      Schema::dropIfExists('accounts_exceptional_students');
   }
}
