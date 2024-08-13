<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'middle_name', 'last_name', 'date_of_birth'];

    public function availabilities()
    {
        return $this->hasMany(StudentAvailability::class);
    }
    
    public function studentSessions()
    {
        return $this->hasMany(StudentSession::class);
    }
}

