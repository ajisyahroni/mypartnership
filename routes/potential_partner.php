<?php

use App\Http\Controllers\PotentialPartnerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PotentialPartnerController::class, 'index'])->name('home');
Route::get('/tambah', [PotentialPartnerController::class, 'tambah'])->name('tambah');
Route::get('/edit/{id}', [PotentialPartnerController::class, 'edit'])->name('edit');
Route::get('/getData', [PotentialPartnerController::class, 'getData'])->name('getData');

Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/store', [PotentialPartnerController::class, 'store'])->name('store');
    Route::post('/destroy', [PotentialPartnerController::class, 'destroy'])->name('destroy');
});

Route::middleware(['throttle:10,1'])->group(function () {
    Route::post('/verifikasi', [PotentialPartnerController::class, 'verifikasi'])->name('verifikasi');
});

Route::get('/download_excel', [PotentialPartnerController::class, 'download_excel'])->name('download_excel');


Route::get('/activity', [PotentialPartnerController::class, 'activity'])->name('activity');
Route::get('/getDataActivity', [PotentialPartnerController::class, 'getDataActivity'])->name('getDataActivity');
Route::get('/reward', [PotentialPartnerController::class, 'reward'])->name('reward');
Route::get('/profile', [PotentialPartnerController::class, 'profile'])->name('profile');

Route::get('/setting', [PotentialPartnerController::class, 'setting'])->name('setting');

Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/setting/store', [PotentialPartnerController::class, 'storeSetting'])->name('storeSetting');
});
