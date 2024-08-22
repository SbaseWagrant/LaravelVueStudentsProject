<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportTemplateController;
use App\Http\Controllers\ReportGenerationController;
use App\Http\Controllers\StudentSessionController;
use App\Http\Controllers\SendSessionEmailCOntroller;
use App\Http\Controllers\DocxController;
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
    Route::post('/check-availability', [StudentSessionController::class, 'checkAvailability']);
    Route::post('/student-available-days', [StudentSessionController::class, 'getAvailableDays']);
    Route::post('/student-sessions/{id}/rate', [StudentSessionController::class, 'rateSession']);
    Route::get('/passed-sessions-view', [StudentSessionController::class, 'getPassedSessionsVIew']);
    Route::get('/passed-sessions', [StudentSessionController::class, 'getPassedSessions']);
    Route::post('/rate-student', [StudentSessionController::class, 'rateStudent']);

    Route::post('/upload-docx', [DocxController::class, 'uploadDocx']);
    Route::get('/upload-document', function () {
        return Inertia::render('UploadForm');
    });

    Route::any('/reports/create', [ReportTemplateController::class, 'create'])->name('create');
    Route::get('/reports/templates', [ReportTemplateController::class, 'create'])->name('templates');
    Route::post('/reports/templates', [ReportTemplateController::class, 'store'])->name('templates.store');
    Route::get('/reports/templates/listView', [ReportTemplateController::class, 'listVIew'])->name('templates.list');
    Route::get('/reports/templates/list', [ReportTemplateController::class, 'list']);
    Route::post('/report-templates/{id}/generate', [ReportTemplateController::class, 'generate']);
});

require __DIR__.'/auth.php';
