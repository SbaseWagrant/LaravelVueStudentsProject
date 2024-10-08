<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentImprovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'start_date',
        'end_date',
        'target',
    ];
}

