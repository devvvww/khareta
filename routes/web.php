<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseSearchController;
use App\Http\Controllers\DepartmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('courses.search');
});


Route::get('/departments/select', [DepartmentController::class, 'show'])->name('departments.select');
Route::post('/departments/select', [DepartmentController::class, 'store'])->name('departments.store');

Route::middleware('department.selected')->group(function () {
    Route::get('/courses/search', CourseSearchController::class)->name('courses.search');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
});
