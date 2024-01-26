<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('staffs', function (Blueprint $table) {
         $table->increments('id');
         $table->string('name');
         $table->string('staff_code')->nullable();
         $table->string('designation')->nullable();
         $table->string('qualification')->nullable();
         $table->string('gender')->nullable();
         $table->string('religion')->nullable();
         $table->date('doj')->nullable();
         $table->string('email')->unique()->nullable();
         $table->string('phone')->nullable();
         $table->string('address')->nullable();
         $table->string('file_path')->nullable()->default('assets/images/staff_image/default.png');
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
      Schema::dropIfExists('staffs');
   }
}
