<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsHeadCategoriesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('accounts_head_categories', function (Blueprint $table) {
         $table->increments('id');
         $table->string('category_identity')->unique();
         $table->string('category_name')->unique();
         $table->string('category_type')->nullable();
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
      Schema::dropIfExists('accounts_head_categories');
   }
}
