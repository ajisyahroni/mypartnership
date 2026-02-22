<?php

use App\Http\Controllers\referensi\DokumenPendukungController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DokumenPendukungController::class, 'index'])->name('home');
Route::get('/getData', [DokumenPendukungController::class, 'getData'])->name('getData');
Route::post('store', [DokumenPendukungController::class, 'store'])->name('store');
Route::post('destroy', [DokumenPendukungController::class, 'destroy'])->name('destroy');
Route::post('setDokumen', [DokumenPendukungController::class, 'setDokumen'])->name('setDokumen');

Route::get('dokumen-pendukung/load-all-iframe', [DokumenPendukungController::class, 'loadAllDokumenIframe'])->name('load.all.iframe');
