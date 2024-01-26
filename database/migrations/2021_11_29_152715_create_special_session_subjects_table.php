<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialSessionSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_session_subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('class_id');
            $table->integer('subject_code')->default(0);
            $table->integer('subject_order')->default(0);
            $table->integer('teacher_id')->nullable()->default(0);
            $table->integer('subject_marks')->default(0);
            $table->integer('pass_marks')->default(0);
            $table->integer('written_marks')->default(0);
            $table->integer('assignment_marks')->default(0);
            $table->integer('other_marks')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->string('year');
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
        Schema::dropIfExists('special_session_subjects');
    }
}
