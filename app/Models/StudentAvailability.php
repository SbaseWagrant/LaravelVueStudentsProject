<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAvailability extends Model
{
    protected $fillable = [
        'student_id',
        'weekday',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}

