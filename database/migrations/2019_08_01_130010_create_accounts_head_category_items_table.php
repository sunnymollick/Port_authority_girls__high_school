<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsHeadCategoryItemsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('accounts_head_category_items', function (Blueprint $table) {
         $table->increments('id');
         $table->integer('category_id');
         $table->string('category_item_name')->unique();
         $table->string('category_item_identity')->unique();
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
      Schema::dropIfExists('accounts_head_category_items');
   }
}
