<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SessionReminder;
use Carbon\Carbon;

class StudentSessionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'type' => 'required|in:one-time,recurring',
        ]);

        $validated['start_time'] = $this->formatTime($validated['start_time']);
        $validated['end_time'] = $this->formatTime($validated['end_time']);
        
        $student = Student::find($request->student_id);

        $overlap = StudentSession::where('student_id', $validated['student_id'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhere(function ($query) use ($validated) {
                          $query->where('start_time', '<=', $validated['start_time'])
                                ->where('end_time', '>=', $validated['end_time']);
                      });
            })
            ->exists();
        
        $student = Student::find($request->student_id);
        $dayOfWeek = Carbon::parse($request->date)->format('l');
        
        $availability = $student->availabilities()->where('weekday', $dayOfWeek)->exists();
        
        if (!$availability) {
            return response()->json(['message' => 'Student is not available on this day'], 422);
        }

        if ($overlap) {
            return response()->json(['message' => 'Session overlaps with an existing session.'], 400);
        }

        $session = StudentSession::create([
            'student_id' => $validated['student_id'],
            'user_id' => auth()->id(),
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'type' => $validated['type'],
        ]);

        Notification::send([$student, auth()->user()], new SessionReminder($session));

        return response()->json($session, 201);
    }

    private function formatTime($time)
    {
        return date('H:i:s', strtotime($time));
    }

    public function getSessions(Student $student)
    {
        $sessions = $student->studentSessions;

        return response()->json($sessions);
    }

    public function updateRating(Request $request, StudentSession $studentSession)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,10',
        ]);

        $studentSession->update(['rating' => $validated['rating']]);

        return response()->json($studentSession);
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'weekday' => 'required|string',
        ]);

        $student = Student::find($request->student_id);
        $isAvailable = $student->availabilities()->where('weekday', $request->weekday)->exists();

        return response()->json(['available' => $isAvailable]);
    }

    public function getAvailableDays(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::find($request->student_id);
        $availableDays = $student->availabilities->pluck('weekday');

        return response()->json(['available_days' => $availableDays]);
    }

    public function rateStudent(Request $request)
    {
        $ratings = $request->input('ratings');

        foreach ($ratings as $ratingData) {
            StudentSession::updateOrCreate(
                ['id' => $ratingData['session_id']],
                ['rating' => $ratingData['rating']]
            );
        }

        return response()->json(['message' => 'Ratings submitted successfully!']);
    }

    public function getPassedSessions()
    {
        $now = Carbon::now();
        $sessions = StudentSession::with('student')
            ->where('created_at', '<', $now)
            ->whereNull('rating')
            ->orderBy('end_time', 'desc')
            ->get();

        return response()->json($sessions);
    }
}
