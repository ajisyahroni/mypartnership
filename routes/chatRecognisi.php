<?php

use App\Http\Controllers\ChatRekognisiController;
use Illuminate\Support\Facades\Route;

Route::get('index/{id_rec}/{sender?}', [ChatRekognisiController::class, 'index'])->name('home');
Route::get('load/{id_rec}/{receiverId?}', [ChatRekognisiController::class, 'loadMessages'])->name('loadMessages');
Route::post('send', [ChatRekognisiController::class, 'sendMessage'])->name('sendMessage');
Route::post('sendMail', [ChatRekognisiController::class, 'sendMail'])->name('sendMail');
