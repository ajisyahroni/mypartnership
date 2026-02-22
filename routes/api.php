<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KerjaSamaController;

Route::get('/kerjasama', [KerjaSamaController::class, 'index']);
Route::get('/kerjasama/{id}', [KerjaSamaController::class, 'show']);
