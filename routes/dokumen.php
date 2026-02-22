<?php

use App\Http\Controllers\DokumenController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DokumenController::class, 'index'])->name('home');
Route::get('/getData', [DokumenController::class, 'getData'])->name('getData');
Route::get('/getDetailPengajuan', [DokumenController::class, 'getDetailPengajuan'])->name('getDetailPengajuan');

Route::middleware('currentRole:admin')->group(function () {
    Route::post('/store_baru', [DokumenController::class, 'store_baru'])->name('store_baru');
    Route::post('/destroy', [DokumenController::class, 'destroy'])->name('destroy');
});



Route::get('/download_excel', [DokumenController::class, 'download_excel'])->name('download_excel');
