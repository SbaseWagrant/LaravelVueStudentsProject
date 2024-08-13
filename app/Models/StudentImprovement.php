<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentImprovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_name',
        'area_of_weakness',
        'session_start_date',
        'session_end_date',
        'improvement_target',
    ];
}

