<?php

use App\Http\Controllers\ReminderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ReminderController::class, 'index'])->name('home');
Route::get('/list', [ReminderController::class, 'list'])->name('list');
Route::get('/getDataList', [ReminderController::class, 'getDataList'])->name('getDataList');
Route::get('/getData', [ReminderController::class, 'getData'])->name('getData');
Route::post('/store_baru', [ReminderController::class, 'store_baru'])->name('store_baru');
Route::post('/destroy', [ReminderController::class, 'destroy'])->name('destroy');

Route::post('/send-reminder', [ReminderController::class, 'SendReminder'])->name('SendReminder');

Route::middleware(['throttle:3,1'])->group(function () {
    Route::post('/send-broadcast', [ReminderController::class, 'SendBroadcast'])->name('SendBroadcast');
});
