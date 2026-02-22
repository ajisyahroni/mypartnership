<?php

use App\Http\Controllers\superadmin\ImportUserController;
use App\Http\Controllers\superadmin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('', [ImportUserController::class, 'index'])->name('index');
Route::post('/preview', [ImportUserController::class, 'uploadPreview'])->name('preview');
Route::post('/row/update', [ImportUserController::class, 'updateRow'])->name('updateRow');
Route::post('/row/delete', [ImportUserController::class, 'deleteRow'])->name('deleteRow');
Route::post('/save', [ImportUserController::class, 'saveAll'])->name('saveAll');
Route::post('/clear', [ImportUserController::class, 'clearPreview'])->name('clearPreview');

Route::get('/download-failed', [ImportUserController::class, 'downloadFailedRows'])->name('downloadFailedRows');
Route::get('/download-template', [ImportUserController::class, 'downloadTemplate'])->name('downloadTemplate');
