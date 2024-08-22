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
            $table->string('type');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('date_range')->nullable();
            $table->integer('target')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_improvements');
    }
}

