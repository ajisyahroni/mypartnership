<?php

use App\Http\Controllers\DownloadDataDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;


Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard')->middleware('update_last_seen');
    Route::get('/detailSkor', [HomeController::class, 'detailSkor'])->name('detailSkor');
    Route::get('/detailKerma', [HomeController::class, 'detailKerma'])->name('detailKerma');
    Route::get('/detailSelengkapnya', [HomeController::class, 'detailSelengkapnya'])->name('detailSelengkapnya');
    Route::get('/get-data-sebaran-mitra', [HomeController::class, 'getDataSebaranMitra'])->name('getDataSebaranMitra');

    Route::get('/detailInstansi', [HomeController::class, 'detailInstansi'])->name('detailInstansi');


    Route::get('/download-excel-sebaran-mitra-produktif', [DownloadDataDashboardController::class, 'downloadExcelSebaranMitraProduktif'])->name('downloadExcelSebaranMitraProduktif');
    Route::get('/download-excel-sebaran-mitra-aktif', [DownloadDataDashboardController::class, 'downloadExcelSebaranMitraAktif'])->name('downloadExcelSebaranMitraAktif');
});
