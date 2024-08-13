<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentImprovementsTable extends Migration
{
    public function up()
    {
        Schema::create('student_improvements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('subject_name');
            $table->text('area_of_weakness');
            $table->date('session_start_date');
            $table->date('session_end_date');
            $table->integer('improvement_target');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_improvements');
    }
}

