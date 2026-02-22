<?php

use App\Http\Controllers\ImplementasiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ImplementasiController::class, 'index'])->name('home');
Route::get('/getData', [ImplementasiController::class, 'getData'])->name('getData');
Route::get('/tambah', [ImplementasiController::class, 'tambah'])->name('tambah');
Route::get('/tambah/{id}', [ImplementasiController::class, 'tambah'])->name('edit');
Route::get('/getDetailImplementasi', [ImplementasiController::class, 'getDetailImplementasi'])->name('getDetailImplementasi');


Route::get('/getDataGroup', [ImplementasiController::class, 'getDataGroup'])->name('getDataGroup');
// web.php
Route::get('/lapor-implementasi/detail/{institusi}', [ImplementasiController::class, 'getDetailLembaga'])->name('getDetailLembaga');

Route::post('/upload-file-implementasi', [ImplementasiController::class, 'uploadFileImplementasi'])->name('uploadFile');

Route::get('/download_implementasi_excel', [ImplementasiController::class, 'download_implementasi_excel'])->name('download_pengajuan_excel');

Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/store', [ImplementasiController::class, 'store'])->name('store');
    Route::post('/send-email', [ImplementasiController::class, 'sendEmail'])->name('sendEmail');
});

Route::middleware(['throttle:10,1'])->group(function () {
    Route::post('/verifikasi', [ImplementasiController::class, 'verifikasi'])->name('verifikasi');
    Route::post('/destroy', [ImplementasiController::class, 'destroy'])->name('destroy');
});

// Route::post('/verifikasi', [DokumenController::class, 'verifikasi'])->name('verifikasi');
// Route::post('/pilihTTD', [DokumenController::class, 'pilihTTD'])->name('pilihTTD');
// Route::get('/getDetailPengajuan', [DokumenController::class, 'getDetailPengajuan'])->name('getDetailPengajuan');
