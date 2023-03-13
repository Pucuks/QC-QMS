<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GooglePieController;
use App\Http\Controllers\FullCalenderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/chart/document', [App\Http\Controllers\HomeController::class, 'echart']);

Route::prefix('documents')->group(function () {
    Route::get('', [App\Http\Controllers\DocumentController::class, 'documents'])->name('documents');
    Route::post('', [App\Http\Controllers\DocumentController::class, 'documents_save'])->name('documents_save');
    Route::delete('', [App\Http\Controllers\DocumentController::class, 'documents_delete'])->name('documents_delete')->middleware('adminRole');
    Route::get('categories', [App\Http\Controllers\DocumentController::class, 'categories'])->name('documents.categories');
    Route::post('categories', [App\Http\Controllers\DocumentController::class, 'categories_save'])->name('documents.categories.save')->middleware('adminRole');
    Route::delete('categories', [App\Http\Controllers\DocumentController::class, 'categories_delete'])->name('documents.categories.delete')->middleware('adminRole');
    Route::get('fullcalender', [FullCalenderController::class, 'index']);
    Route::post('fullcalenderAjax', [FullCalenderController::class, 'ajax']);
    Route::get('historydate', [FullCalenderController::class, 'historydatee'])->name('historydate');
    Route::get('logcalendar', [FullCalenderController::class, 'logcalendar'])->name('logcalendar');
});
Route::get('fullcalender', [FullCalenderController::class, 'index']);
Route::post('fullcalenderAjax', [FullCalenderController::class, 'ajax']);
Route::get('historydate', [FullCalenderController::class, 'historydatee'])->name('historydate');
Route::get('logcalendar', [FullCalenderController::class, 'logcalendar'])->name('logcalendar');

Route::prefix('users')->group(function () {
    Route::get('', [App\Http\Controllers\UserController::class, 'users'])->name('users')->middleware('adminRole');
    Route::delete('', [App\Http\Controllers\UserController::class, 'user_delete'])->name('users.delete')->middleware('adminRole');
    Route::post('', [App\Http\Controllers\UserController::class, 'user_save'])->name('users.save')->middleware('adminRole');
});

Route::prefix('account')->group(function () {
    Route::get('', [App\Http\Controllers\UserController::class, 'myaccount'])->name('myaccount');
    Route::post('profile', [App\Http\Controllers\UserController::class, 'myaccount_update'])->name('myaccount.update');
    Route::post('password', [App\Http\Controllers\UserController::class, 'myaccount_update_password'])->name('myaccount.updatePassword');
});
