<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsPaymentsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('accounts_payments', function (Blueprint $table) {
         $table->increments('id');
         $table->string('student_id');
         $table->integer('class_id');
         $table->integer('section_id');
         $table->string('student_roll')->nullable();
         $table->string('barcode')->nullable();
         $table->string('amount')->nullable();
         $table->string('fee_month')->nullable();
         $table->date('payment_date')->nullable();
         $table->string('year')->nullable();
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
      Schema::dropIfExists('accounts_payments');
   }
}
