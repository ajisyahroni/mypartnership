<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Role_userController;
use App\Http\Controllers\UserController;

Route::resource('users', UserController::class);
Route::resource('roles', RoleController::class);
Route::resource('role_users', Role_userController::class);