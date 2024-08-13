<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAvailability;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return Student::with('availabilities')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
        ]);

        $student = Student::create($validated);

        return response()->json($student, 201);
    }

    public function addAvailability(Request $request, Student $student)
    {
        $validated = $request->validate([
            'weekdays' => 'required|array',
            'weekdays.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        ]);

        $availabilities = [];
        foreach ($validated['weekdays'] as $weekday) {
            $availability = new StudentAvailability(['weekday' => $weekday]);
            $student->availabilities()->save($availability);
            $availabilities[] = $availability;
        }
        return response()->json($availabilities, 201);
    }

    public function getAvailabilities(Student $student)
    {
        return $student->availabilities;
    }
}

