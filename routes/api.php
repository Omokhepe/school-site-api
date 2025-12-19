<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Teacher\SubjectController;
use App\Http\Controllers\Teacher\TeacherNoteController;
use App\Http\Controllers\Admin\ClassAssignmentController;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/classes', [SchoolClassController::class, 'index']);

    Route::get('/teachers', [UserController::class, 'teachers']);
    Route::get('/students', [UserController::class, 'students']);
    Route::get('/students/{class_id}', [UserController::class, 'studentsByClass']);

    //subject
    Route::get('/subjects',[SubjectController::class, 'index']);
    Route::get('/subjects/level/{level_group}',[SubjectController::class, 'getByLevelGroup']);
    Route::post('/subjects',[SubjectController::class, 'store']);
    Route::put('/subjects/{id}',[SubjectController::class, 'updateSubject']);

    //Timetable
    Route::get('/timetable/teacher', [TimetableController::class,'teacherTimetable']);
//notes
    Route::get('/notes', [TeacherNoteController::class, 'index']);
    Route::post('/notes', [TeacherNoteController::class, 'store']);
    Route::get('/notes/{id}', [TeacherNoteController::class, 'show']);
    Route::put('/notes/{id}', [TeacherNoteController::class, 'updateNotes']);
    Route::delete('/notes/{id}', [TeacherNoteController::class, 'destroy']);

    Route::middleware('role:admin')->group(function () {
        //Add or update user
        Route::post('/users', [UserController::class, 'store']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::get('/users', [UserController::class, 'index']);
        Route::put('/users/{id}/role', [UserController::class, 'updateUser']);
        //to update student class either single or bulk update
        Route::put('/students/{id}/class', [ClassAssignmentController::class, 'assignSingle']);
        Route::put('/students/class/bulk-class-change', [ClassAssignmentController::class, 'assignBulk']);

        //Add Announcement
        // Route::get('/announcements', [AnnouncementController::class, 'index']); // admin/teacher list
        // Route::post('/announcements', [AnnouncementController::class, 'store']);
        // Route::put('/announcements/{id}', [AnnouncementController::class, 'update']);
        // Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy']);
    });

    Route::middleware('role:admin,teacher')->group(function () {
        // Notes
        // Route::get('/notes', [TeacherNoteController::class, 'index']);
        // Route::post('/notes', [TeacherNoteController::class, 'store']);
        // Route::get('/notes/{id}', [TeacherNoteController::class, 'show']);
        // Route::put('/notes/{id}', [TeacherNoteController::class, 'updateNotes']);
        // Route::delete('/notes/{id}', [TeacherNoteController::class, 'destroy']);

        //Timetable
        Route::get('/timetable', [TimetableController::class,'index']);
        Route::post('/timetable', [TimetableController::class,'store']);
        Route::put('/timetable/{id}', [TimetableController::class,'update']);
        Route::delete('/timetable/{id}', [TimetableController::class,'destroy']);

         //Add Announcement
        // Route::get('/announcements', [AnnouncementController::class, 'index']); // admin/teacher list
        Route::post('/announcements', [AnnouncementController::class, 'store']);
        Route::put('/announcements/{id}', [AnnouncementController::class, 'update']);
        Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy']);
    });
});

Route::get('/announcements/active', [AnnouncementController::class, 'activeAnnouncement']); 


// Route::get('/debug/tables', function () {
//     return DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;");
//     // return DB::select("SELECT * FROM users WHERE id = 4;");
    
// });

Route::get('/debug', function () {
    try {
        DB::connection()->getPdo();
        return 'Database OK';
    } catch (\Exception $e) {
        return $e->getMessage();
    }
});

Route::get('/dbcheck', function () {
    return [
        'driver' => env('DB_CONNECTION'),
        'host' => env('DB_HOST'),
        'database' => env('DB_DATABASE'),
        'username' => env('DB_USERNAME')
    ];
});

Route::get('/health', fn() => 'OK');

Route::get('/dbcheckAgain', function () {
    return config('database.default');
});