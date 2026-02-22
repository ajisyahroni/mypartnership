<?php

use App\Http\Controllers\HibahController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HibahController::class, 'index'])->name('home');
Route::get('/ajuan', [HibahController::class, 'ajuan'])->name('ajuan');
Route::get('/tambah', [HibahController::class, 'tambah'])->name('tambah');
Route::get('/edit/{id_hibah}', [HibahController::class, 'edit'])->name('edit');
Route::get('/getData', [HibahController::class, 'getData'])->name('getData');
Route::get('/detailHibah', [HibahController::class, 'detailHibah'])->name('detailHibah');
Route::get('/detailLaporanHibah', [HibahController::class, 'detailLaporanHibah'])->name('detailLaporanHibah');
Route::post('/store', [HibahController::class, 'store'])->name('store');
Route::post('/store_draft', [HibahController::class, 'store_draft'])->name('store_draft');
Route::post('/destroy', [HibahController::class, 'destroy'])->name('destroy');
Route::post('/verifikasi', [HibahController::class, 'verifikasi'])->name('verifikasi');
Route::get('/showRevisi', [HibahController::class, 'showRevisi'])->name('showRevisi');
Route::post('/markRevisiDone', [HibahController::class, 'markRevisiDone'])->name('markRevisiDone');

Route::get('/isiLaporan/{id_hibah}', [HibahController::class, 'isiLaporan'])->name('isiLaporan');
Route::post('/isiLaporan/store', [HibahController::class, 'laporan_store'])->name('isiLaporan.store');
Route::post('/upload-file-kontrak', [HibahController::class, 'uploadFileKontrak'])->name('uploadFileKontrak');


Route::get('/setting', [HibahController::class, 'setting'])->name('setting');
Route::post('/setting/store', [HibahController::class, 'storeSetting'])->name('storeSetting');

Route::get('/export_proposal/{id}', [HibahController::class, 'export_proposal'])->name('export_proposal');
Route::get('/export_laporan/{id}', [HibahController::class, 'export_laporan'])->name('export_laporan');

Route::get('/download_excel', [HibahController::class, 'download_excel'])->name('download_excel');
Route::get('/download_laporan_excel', [HibahController::class, 'download_laporan_excel'])->name('download_laporan_excel');


Route::get('/showVerifikasiTahap', [HibahController::class, 'showVerifikasiTahap'])->name('showVerifikasiTahap');
Route::post('/VerifikasiTahap', [HibahController::class, 'VerifikasiTahap'])->name('VerifikasiTahap');

Route::get('dokumenPendukung', [HibahController::class, 'dokumenPendukung'])->name('dokumenPendukung');
Route::post('storeDokumenPendukung', [HibahController::class, 'storeDokumenPendukung'])->name('storeDokumenPendukung');
Route::post('destroyDokumenPendukung', [HibahController::class, 'destroyDokumenPendukung'])->name('destroyDokumenPendukung');
Route::post('setDokumen', [HibahController::class, 'setDokumen'])->name('setDokumen');

Route::get('dokumen-pendukung/load-all-iframe', [HibahController::class, 'loadAllDokumenIframe'])->name('load.all.iframe');
