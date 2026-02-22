<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('index/{id_mou}/{sender?}', [ChatController::class, 'index'])->name('home');
Route::get('load/{id_mou}/{receiverId?}', [ChatController::class, 'loadMessages'])->name('loadMessages');
Route::post('send', [ChatController::class, 'sendMessage'])->name('sendMessage');
Route::post('sendMail', [ChatController::class, 'sendMail'])->name('sendMail');
