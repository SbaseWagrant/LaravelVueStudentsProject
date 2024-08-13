<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentSessionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/students', [StudentController::class, 'index']);
    Route::post('/students', [StudentController::class, 'store']);
    Route::post('/students/{student}/availability', [StudentController::class, 'addAvailability']);
    Route::get('/students/{student}/availabilities', [StudentController::class, 'getAvailabilities']);
    Route::post('/student-sessions', [StudentSessionController::class, 'store']);
    Route::get('/students/{student}', [StudentSessionController::class, 'getSessions']);
    Route::post('/generate-report', [ReportController::class, 'generate']);
    Route::post('/check-availability', [StudentSessionController::class, 'checkAvailability']);
    Route::post('/student-available-days', [StudentSessionController::class, 'getAvailableDays']);
    Route::post('/student-sessions/{id}/rate', [StudentSessionController::class, 'rateSession']);
    Route::get('/passed-sessions', [StudentSessionController::class, 'getPassedSessions']);
    Route::post('/rate-student', [StudentSessionController::class, 'rateStudent']);
    Route::post('/upload-docx', [YourController::class, 'uploadDocx']);
});

require __DIR__.'/auth.php';
