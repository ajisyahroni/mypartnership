<?php

use App\Http\Controllers\BackupController;
use Illuminate\Support\Facades\Route;

Route::get('', [BackupController::class, 'index'])->name('home');
Route::get('getData', [BackupController::class, 'getData'])->name('getData');
Route::post('destroy', [BackupController::class, 'destroy'])->name('destroy');

Route::get('backupDatabase', [BackupController::class, 'backupDatabase'])->name('backupDatabase');
Route::get('backupFiles', [BackupController::class, 'backupFiles'])->name('backupFiles');
Route::get('downloadBackup', [BackupController::class, 'downloadBackup'])->name('downloadBackup');
