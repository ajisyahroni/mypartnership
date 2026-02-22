<?php

use App\Http\Controllers\superadmin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserManagementController::class, 'index'])->name('home');
Route::get('/getData', [UserManagementController::class, 'getData'])->name('getData');
Route::get('/show', [UserManagementController::class, 'show'])->name('show');

Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/store', [UserManagementController::class, 'store'])->name('store');
    Route::post('/destroy', [UserManagementController::class, 'destroy'])->name('destroy');
    Route::post('/switch_status', [UserManagementController::class, 'switch_status'])->name('switch_status');
});
Route::post('/assignRole', [UserManagementController::class, 'assignRole'])->name('assignRole');
