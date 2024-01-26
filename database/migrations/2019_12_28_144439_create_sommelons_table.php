<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSommelonsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('sommelons', function (Blueprint $table) {
         $table->bigIncrements('id');
         $table->integer('sl')->nullable();
         $table->string('reg_no')->nullable();
         $table->date('reg_date')->nullable();
         $table->string('ssc_batch')->nullable();
         $table->string('std_name');
         $table->string('std_father_name')->nullable();
         $table->string('std_mother_name')->nullable();
         $table->date('std_dob')->nullable();
         $table->string('blood_group')->nullable();
         $table->string('prs_address')->nullable();
         $table->string('prm_address')->nullable();
         $table->string('mobile')->nullable();
         $table->string('email')->nullable();
         $table->string('education')->nullable();
         $table->string('session')->nullable();
         $table->string('university_name')->nullable();
         $table->string('profession')->nullable();
         $table->string('bikash_trans_id')->nullable();
         $table->date('bikash_trans_date')->nullable();
         $table->string('file_path')->nullable();
         $table->string('running_year')->nullable();
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
      Schema::dropIfExists('sommelons');
   }
}
