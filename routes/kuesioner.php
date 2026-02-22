<?php

use App\Http\Controllers\KuesionerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KuesionerController::class, 'index'])->name('home');
Route::get('/getData', [KuesionerController::class, 'getData'])->name('getData');
Route::post('/store', [KuesionerController::class, 'store'])->name('store');
Route::post('/destroy', [KuesionerController::class, 'destroy'])->name('destroy');

Route::get('/hasilKuesioner/{id_kuesioner?}', [KuesionerController::class, 'hasilKuesioner'])->name('hasilKuesioner');
Route::get('/getDetail', [KuesionerController::class, 'getDetail'])->name('getDetail');
Route::get('/getEditKuesioner', [KuesionerController::class, 'getEditKuesioner'])->name('getEditKuesioner');
Route::get('/getLinkKuesioner', [KuesionerController::class, 'getLinkKuesioner'])->name('getLinkKuesioner');
Route::get('/getKirimEmail', [KuesionerController::class, 'getKirimEmail'])->name('getKirimEmail');
Route::post('/kirimEmail', [KuesionerController::class, 'kirimEmail'])->name('kirimEmail');
