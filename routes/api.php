<?php

use App\Http\Controllers\Back\AdminController;
use App\Http\Controllers\Api\CrmFormController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('/send_get', [CrmFormController::class, 'sendGet'])->name('send.get');
Route::post('/send_post', [CrmFormController::class, 'sendPost'])->name('send.notify');
Route::post('/create', [CrmFormController::class, 'createUser'])->name('create.user');
Route::get('/teachers', [AdminController::class, 'date_students'])->name('get.students');