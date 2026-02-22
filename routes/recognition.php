<?php

use App\Http\Controllers\RecognitionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RecognitionController::class, 'index'])->name('home');
Route::get('/detail-data-ajuan/download_excel_detail', [RecognitionController::class, 'download_excel'])->name('download_excel_detail');
Route::get('/download_excel', [RecognitionController::class, 'download_excel'])->name('download_excel');

// Daftar Ajuan
Route::get('/InboundStaffRecognition', [RecognitionController::class, 'InboundStaffRecognition'])->name('InboundStaffRecognition');
Route::get('/getData', [RecognitionController::class, 'getData'])->name('getData');
Route::post('/verifikasi', [RecognitionController::class, 'verifikasi'])->name('verifikasi');

Route::get('/edit-data-ajuan/{id_rec}', [RecognitionController::class, 'edit'])->name('edit');
Route::post('/store-data-ajuan', [RecognitionController::class, 'store'])->name('store');
Route::get('/hapus-data-ajuan', [RecognitionController::class, 'hapus'])->name('hapus');
Route::get('/tambah-data-ajuan', [RecognitionController::class, 'tambah'])->name('tambah');

Route::get('/data-ajuan', [RecognitionController::class, 'dataAjuan'])->name('dataAjuan');
Route::get('/get-data-ajuan', [RecognitionController::class, 'getDataAjuan'])->name('getDataAjuan');
Route::get('/detail-data-ajuan/{id_fak}', [RecognitionController::class, 'detailDataAjuan'])->name('detailDataAjuan');

Route::get('/showRevisi', [RecognitionController::class, 'showRevisi'])->name('showRevisi');

Route::get('/getDetailRecognition', [RecognitionController::class, 'getDetailRecognition'])->name('getDetailRecognition');

Route::get('/ajuan-saya', [RecognitionController::class, 'dataAjuanSaya'])->name('dataAjuanSaya');

Route::get('/lapor-kegiatan-lampau', [RecognitionController::class, 'laporKegiatan'])->name('laporKegiatan');
Route::get('/get-data-lapor-kegiatan-lampau', [RecognitionController::class, 'getDatalaporKegiatan'])->name('getDatalaporKegiatan');
Route::get('/tambah-lapor-kegiatan-lampau', [RecognitionController::class, 'tambahLaporKegiatan'])->name('tambahLaporKegiatan');
Route::get('/edit-lapor-kegiatan-lampau', [RecognitionController::class, 'editLaporKegiatan'])->name('editLaporKegiatan');
Route::post('/store-lapor-kegiatan-lampau', [RecognitionController::class, 'storeLaporKegiatan'])->name('storeLaporKegiatan');
Route::post('/hapus-lapor-kegiatan-lampau', [RecognitionController::class, 'hapusLaporKegiatan'])->name('hapusLaporKegiatan');

Route::get('/dokumenPendukungRecognition', [RecognitionController::class, 'dokumenPendukungRecognition'])->name('dokumenPendukungRecognition');
Route::post('storeDokumenPendukung', [RecognitionController::class, 'storeDokumenPendukung'])->name('storeDokumenPendukung');
Route::post('destroyDokumenPendukung', [RecognitionController::class, 'destroyDokumenPendukung'])->name('destroyDokumenPendukung');
Route::post('setDokumen', [RecognitionController::class, 'setDokumen'])->name('setDokumen');
Route::get('dokumen-pendukung/load-all-iframe', [RecognitionController::class, 'loadAllDokumenIframe'])->name('load.all.iframe');


Route::post('/upload-file-recognition', [RecognitionController::class, 'uploadFileRecognition'])->name('uploadFile');
