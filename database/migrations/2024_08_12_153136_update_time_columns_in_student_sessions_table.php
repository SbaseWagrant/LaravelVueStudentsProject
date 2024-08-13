<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTimeColumnsInStudentSessionsTable extends Migration
{
    public function up()
    {
        Schema::table('student_sessions', function (Blueprint $table) {
            $table->time('start_time')->change();
            $table->time('end_time')->change();
        });
    }

    public function down()
    {
        Schema::table('student_sessions', function (Blueprint $table) {
            $table->string('start_time')->change();
            $table->string('end_time')->change();
        });
    }
}

