
<?php

use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\StudentCourseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Welcome to the API!']);
});
Route::prefix('students')->controller(StudentController::class)->group(function () {
    Route::get('/', 'index')->name('students.index');
    Route::post('/', 'store')->name('students.store'); //students store with courses scores, and calculate letter grade
    Route::get('/{student}', 'show')->name('students.show');
    Route::put('/{student}', 'update')->name('students.update');
    Route::delete('/{student}', 'destroy')->name('students.destroy');
});
Route::prefix('courses')->controller(CourseController::class)->group(function () {
    Route::get('/', 'index')->name('courses.index');
    Route::post('/', 'store')->name('courses.store'); //courses store with course details
    Route::get('/{course}', 'show')->name('courses.show');
    Route::put('/{course}', 'update')->name('courses.update');
    Route::delete('/{course}', 'destroy')->name('courses.destroy');
});

Route::prefix('enrollments')->controller(StudentCourseController::class)->group(function () {
    // Get all courses for a student
    Route::get('/{student}', 'index')->name('enrollments.index');
    // Enroll a student in a course
    Route::post('/{s_id}/{c_id}', 'store')->name('enrollments.store');

    // Update enrollment (score, grade, semester, year etc.)
    Route::put('/{s_id}/{c_id}', 'update')->name('enrollments.update');

    // Get all students in a course
    Route::get('/course/{c_id}', 'studentsInCourse')->name('enrollments.studentsInCourse');

    // Get all enrollments
    Route::get('/', 'allEnrollments')->name('enrollments.all');
    //get student report with courses, scores, grades, and gpa organized by semester and year
    Route::get('/report/{s_id}', 'report')->name('enrollments.report');
    // Delete an enrollment
    Route::delete('/{s_id}/{c_id}', 'destroy')->name('enrollments.destroy');
});
