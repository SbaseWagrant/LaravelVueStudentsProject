<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('student_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->boolean('is_recurring')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_sessions');
    }
}
