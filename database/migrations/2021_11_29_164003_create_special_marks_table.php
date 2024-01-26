<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_marks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exam_id');
            $table->integer('class_id');
            $table->integer('section_id');
            $table->integer('student_code');
            $table->integer('subject_id');
            $table->integer('total_marks')->default(0);
            $table->integer('written_marks')->default(0);
            $table->integer('assignment_marks')->default(0);
            $table->integer('other_marks')->default(0);
            $table->integer('teacher_id')->nullable();
            $table->integer('uploader_id')->nullable(0);
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
        Schema::dropIfExists('special_marks');
    }
}
