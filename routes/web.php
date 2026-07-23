<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseSearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('courses.search');
});


Route::get('/courses/search', CourseSearchController::class)->name('courses.search');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
