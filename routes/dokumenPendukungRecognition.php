<?php

use App\Http\Controllers\referensi\DokumenPendukungRecognitionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DokumenPendukungRecognitionController::class, 'index'])->name('home');
Route::get('/getData', [DokumenPendukungRecognitionController::class, 'getData'])->name('getData');

Route::middleware('currentRole:admin')->group(function () {
    Route::post('store', [DokumenPendukungRecognitionController::class, 'store'])->name('store');
    Route::post('destroy', [DokumenPendukungRecognitionController::class, 'destroy'])->name('destroy');
});
