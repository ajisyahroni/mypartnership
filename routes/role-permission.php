<?php

use App\Http\Controllers\superadmin\RolesPermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RolesPermissionController::class, 'index'])->name('home');
Route::get('/getData', [RolesPermissionController::class, 'getData'])->name('getData');
Route::post('/store', [RolesPermissionController::class, 'store'])->name('store');
Route::post('/destroy', [RolesPermissionController::class, 'destroy'])->name('destroy');
Route::post('/switch_status', [RolesPermissionController::class, 'switch_status'])->name('switch_status');
Route::post('/assignRole', [RolesPermissionController::class, 'assignRole'])->name('assignRole');
