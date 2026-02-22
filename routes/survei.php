<?php

use App\Http\Controllers\SurveiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SurveiController::class, 'index'])->name('home');
Route::get('/getDataInternal', [SurveiController::class, 'getDataInternal'])->name('getDataInternal');
Route::get('/getDataEksternal', [SurveiController::class, 'getDataEksternal'])->name('getDataEksternal');
Route::get('/getMasukan', [SurveiController::class, 'getMasukan'])->name('getMasukan');
