<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookRequestsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('book_requests', function (Blueprint $table) {
         $table->increments('id');
         $table->integer('book_id');
         $table->integer('student_code');
         $table->date('issue_start_date');
         $table->date('issue_end_date');
         $table->date('returned_date')->nullable();
         $table->string('year')->nullable();
         $table->tinyInteger('status')->default(0);
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('book_requests');
   }
}
