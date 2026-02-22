<?php

use App\Http\Controllers\MailController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MailController::class, 'index'])->name('home');
Route::get('/getData', [MailController::class, 'getData'])->name('getData');
Route::get('/getDetailMail', [MailController::class, 'getDetailMail'])->name('getDetailMail');
Route::post('/store_baru', [MailController::class, 'store_baru'])->name('store_baru');
Route::post('/destroy', [MailController::class, 'destroy'])->name('destroy');

Route::get('/setting', [MailController::class, 'setting'])->name('setting');
Route::get('/getDataSetting', [MailController::class, 'getDataSetting'])->name('getDataSetting');
Route::post('/store_setting', [MailController::class, 'store_setting'])->name('store_setting');
Route::post('/switch_status', [MailController::class, 'switch_status'])->name('switch_status');

Route::get('/isi_pesan', [MailController::class, 'isi_pesan'])->name('isi_pesan');
Route::post('/store_isi_pesan', [MailController::class, 'store_isi_pesan'])->name('store_isi_pesan');

Route::post('/updateStatusPesan', [MailController::class, 'updateStatusPesan'])->name('updateStatusPesan');
