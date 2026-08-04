<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseSearchController;
use App\Http\Controllers\DepartmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DepartmentManageController;
use App\Http\Controllers\Admin\CourseManageController;
use App\Http\Controllers\Admin\DepartmentPrefixController;



Route::get('/', function () {
    return redirect()->route('courses.search');
});

Route::post('departments/{department}/prefixes', [DepartmentPrefixController::class, 'store'])->name('admin.departments.prefixes.store');
Route::delete('prefixes/{prefix}', [DepartmentPrefixController::class, 'destroy'])->name('admin.prefixes.destroy');

Route::get('/departments/select', [DepartmentController::class, 'show'])->name('departments.select');
Route::post('/departments/select', [DepartmentController::class, 'store'])->name('departments.store');

Route::middleware('department.selected')->group(function () {
    Route::get('/courses/search', CourseSearchController::class)->name('courses.search');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('departments', DepartmentManageController::class);

    // Not resource because it needs custom URL structure
    Route::get('departments/{department}/courses', [CourseManageController::class, 'index'])->name('departments.courses.index');
    Route::get('departments/{department}/courses/create', [CourseManageController::class, 'create'])->name('departments.courses.create');
    Route::post('departments/{department}/courses', [CourseManageController::class, 'store'])->name('departments.courses.store');

    Route::get('courses/{course}/edit', [CourseManageController::class, 'edit'])->name('courses.edit');
    Route::put('courses/{course}', [CourseManageController::class, 'update'])->name('courses.update');
    Route::delete('courses/{course}', [CourseManageController::class, 'destroy'])->name('courses.destroy');
});
