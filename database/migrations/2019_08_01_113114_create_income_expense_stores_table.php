<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncomeExpenseStoresTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('income_expense_stores', function (Blueprint $table) {
         $table->increments('id');
         $table->date('store_date')->nullable();
         $table->string('store_type');
         $table->string('store_voucher')->nullable();
         $table->string('category_id')->nullable();
         $table->string('item_id')->nullable();
         $table->string('name')->nullable();
         $table->string('address')->nullable();
         $table->string('amount')->nullable();
         $table->string('comment')->nullable();
         $table->string('payment_method')->nullable();
         $table->integer('bank_name_id')->nullable();
         $table->string('bank_account_id')->nullable();
         $table->string('check_number')->nullable();
         $table->date('check_date')->nullable();
         $table->string('store_year')->nullable();
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
      Schema::dropIfExists('income_expense_stores');
   }
}
