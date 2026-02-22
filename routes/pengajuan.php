<?php

use App\Http\Controllers\pengajuan\PengajuanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PengajuanController::class, 'index'])->name('home');
Route::get('/tambah-pengajuan-baru', [PengajuanController::class, 'tambahBaru'])->name('tambahBaru');
Route::get('/tambah-pengajuan-baru/{id}', [PengajuanController::class, 'tambahBaru'])->name('editBaru');
Route::get('/setting', [PengajuanController::class, 'setting'])->name('setting');
Route::post('/setting/store', [PengajuanController::class, 'storeSetting'])->name('storeSetting');
// RATE LIMIT
Route::get('/getData', [PengajuanController::class, 'getData'])->name('getData');
Route::get('/getDetailPengajuan', [PengajuanController::class, 'getDetailPengajuan'])->name('getDetailPengajuan');
Route::get('/getDetailVerifikasi', [PengajuanController::class, 'getDetailVerifikasi'])->name('getDetailVerifikasi');

Route::get('/lapor-pengajuan', [PengajuanController::class, 'laporPengajuan'])->name('laporPengajuan');
Route::get('/lapor-pengajuan/{id}', [PengajuanController::class, 'laporPengajuan'])->name('editLaporPengajuan');
Route::get('/download_pengajuan_excel', [PengajuanController::class, 'download_pengajuan_excel'])->name('download_pengajuan_excel');

Route::middleware(['throttle:10,1'])->group(function () {
    Route::post('/send-email', [PengajuanController::class, 'sendEmail'])->name('sendEmail');
    Route::post('/verifikasi', [PengajuanController::class, 'verifikasi'])->name('verifikasi');
});

Route::middleware(['throttle:5,1'])->group(function () {

    Route::post('/store_baru', [PengajuanController::class, 'store_baru'])
        ->name('store_baru');

    Route::post('/destroy', [PengajuanController::class, 'destroy'])->name('destroy');
    Route::post('/pilihTTD', [PengajuanController::class, 'pilihTTD'])->name('pilihTTD');
});
