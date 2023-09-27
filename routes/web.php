<?php

use App\Http\Controllers\Back\AdminController;
use App\Http\Controllers\Front\SearchController;
use App\Http\Controllers\Back\StudentController;
use App\Http\Controllers\Back\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SearchController::class, 'index'])->name('search.index');

// Route::resource('/dashboard', AdminController::class)->middleware(['auth']);
Route::resource('/teachers', TeacherController::class)->middleware(['auth']);
Route::get('/teachers/{id}/download', [TeacherController::class, 'download'])->name('teacher.download')->middleware(['auth']);
Route::get('donwload-file', [TeacherController::class, 'downloadExcelExample'])->name('donwload.excel.example')->middleware(['auth']);
Route::delete('/teachers', [TeacherController::class, 'deleteSelected'])->name('teachers.deleteSelected')->middleware(['auth']);

Route::resource('/students', StudentController::class)->middleware(['auth']);
Route::get('/student/{id}/download', [StudentController::class, 'download'])->name('student.download')->middleware(['auth']);

Route::get('/student/{seria_number}', [SearchController::class, 'show'])->name('search.show');

require __DIR__ . '/auth.php';
