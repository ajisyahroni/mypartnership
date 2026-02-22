<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\front\BerandaController;
use App\Http\Controllers\front\ProfileController;
use App\Http\Controllers\front\HelpdeskController;
use App\Http\Controllers\front\BeritaController;
use App\Http\Controllers\front\GalleryController;
use App\Http\Controllers\front\JadwalController;
use App\Http\Controllers\front\KlasemenController;
use App\Http\Controllers\front\AtletController;
use App\Http\Controllers\front\VenuesController;
use App\Http\Controllers\front\CaborController;
use App\Http\Controllers\front\MerchandiseController;
use App\Http\Controllers\front\VolunteerController;
use App\Http\Controllers\front\TouristController;
use App\Http\Controllers\front\TiketController;
use App\Http\Controllers\front\TenderController;
use App\Http\Controllers\front\VideoController;

// Beranda
Route::get('/', [BerandaController::class, 'index']);
