<?php

use App\Http\Controllers\referensi\RefBentukKerjaSamaController;
use App\Http\Controllers\referensi\RefFakultasController;
use App\Http\Controllers\referensi\RefJabatanController;
use App\Http\Controllers\referensi\RefJenisDokumenController;
use App\Http\Controllers\referensi\RefJenisHibahController;
use App\Http\Controllers\referensi\RefJenisInstitusiMitraController;
use App\Http\Controllers\referensi\RefLembagaUMSController;
use App\Http\Controllers\referensi\RefNegaraController;
use App\Http\Controllers\referensi\RefPelaksanaKerjaSamaController;
use App\Http\Controllers\referensi\RefPertanyaanSurveiController;
use App\Http\Controllers\referensi\RefRangkingUniversitasController;
use Illuminate\Support\Facades\Route;

Route::get('/jenis_dokumen', [RefJenisDokumenController::class, 'index'])->name('jenis_dokumen.home');
Route::get('/jenis_dokumen/getData', [RefJenisDokumenController::class, 'getData'])->name('jenis_dokumen.getData');
Route::post('jenis_dokumen/store', [RefJenisDokumenController::class, 'store'])->name('jenis_dokumen.store');
Route::post('jenis_dokumen/destroy', [RefJenisDokumenController::class, 'destroy'])->name('jenis_dokumen.destroy');
Route::get('/jenis_dokumen/getEdit', [RefJenisDokumenController::class, 'getEdit'])->name('jenis_dokumen.getEdit');

Route::get('/pelaksana_kerjasama', [RefPelaksanaKerjaSamaController::class, 'index'])->name('pelaksana_kerjasama.home');
Route::get('/pelaksana_kerjasama/getData', [RefPelaksanaKerjaSamaController::class, 'getData'])->name('pelaksana_kerjasama.getData');
Route::post('pelaksana_kerjasama/store', [RefPelaksanaKerjaSamaController::class, 'store'])->name('pelaksana_kerjasama.store');
Route::post('pelaksana_kerjasama/destroy', [RefPelaksanaKerjaSamaController::class, 'destroy'])->name('pelaksana_kerjasama.destroy');

Route::get('/bentuk_kerjasama', [RefBentukKerjaSamaController::class, 'index'])->name('bentuk_kerjasama.home');
Route::get('/bentuk_kerjasama/getData', [RefBentukKerjaSamaController::class, 'getData'])->name('bentuk_kerjasama.getData');
Route::post('bentuk_kerjasama/store', [RefBentukKerjaSamaController::class, 'store'])->name('bentuk_kerjasama.store');
Route::post('bentuk_kerjasama/destroy', [RefBentukKerjaSamaController::class, 'destroy'])->name('bentuk_kerjasama.destroy');

Route::get('/lembaga_ums', [RefLembagaUMSController::class, 'index'])->name('lembaga_ums.home');
Route::get('/lembaga_ums/getData', [RefLembagaUMSController::class, 'getData'])->name('lembaga_ums.getData');
Route::post('/lembaga_ums/store', [RefLembagaUMSController::class, 'store'])->name('lembaga_ums.store');
Route::post('/lembaga_ums/destroy', [RefLembagaUMSController::class, 'destroy'])->name('lembaga_ums.destroy');

Route::get('/jenis_institusi_mitra', [RefJenisInstitusiMitraController::class, 'index'])->name('jenis_institusi_mitra.home');
Route::get('/jenis_institusi_mitra/getData', [RefJenisInstitusiMitraController::class, 'getData'])->name('jenis_institusi_mitra.getData');
Route::post('/jenis_institusi_mitra/store', [RefJenisInstitusiMitraController::class, 'store'])->name('jenis_institusi_mitra.store');
Route::post('/jenis_institusi_mitra/destroy', [RefJenisInstitusiMitraController::class, 'destroy'])->name('jenis_institusi_mitra.destroy');

Route::get('/fakultas', [RefFakultasController::class, 'index'])->name('fakultas.home');
Route::get('/fakultas/getData', [RefFakultasController::class, 'getData'])->name('fakultas.getData');
Route::post('/fakultas/store', [RefFakultasController::class, 'store'])->name('fakultas.store');
Route::post('/fakultas/destroy', [RefFakultasController::class, 'destroy'])->name('fakultas.destroy');

Route::get('/negara', [RefNegaraController::class, 'index'])->name('negara.home');
Route::get('/negara/getData', [RefNegaraController::class, 'getData'])->name('negara.getData');
Route::post('/negara/store', [RefNegaraController::class, 'store'])->name('negara.store');
Route::post('/negara/destroy', [RefNegaraController::class, 'destroy'])->name('negara.destroy');

Route::get('/jabatan', [RefJabatanController::class, 'index'])->name('jabatan.home');
Route::get('/jabatan/getData', [RefJabatanController::class, 'getData'])->name('jabatan.getData');
Route::post('/jabatan/store', [RefJabatanController::class, 'store'])->name('jabatan.store');
Route::post('/jabatan/destroy', [RefJabatanController::class, 'destroy'])->name('jabatan.destroy');

Route::get('/rangking_universitas', [RefRangkingUniversitasController::class, 'index'])->name('rangking_universitas.home');
Route::get('/rangking_universitas/getData', [RefRangkingUniversitasController::class, 'getData'])->name('rangking_universitas.getData');
Route::post('/rangking_universitas/store', [RefRangkingUniversitasController::class, 'store'])->name('rangking_universitas.store');
Route::post('/rangking_universitas/destroy', [RefRangkingUniversitasController::class, 'destroy'])->name('rangking_universitas.destroy');

Route::get('/pertanyaan_survei', [RefPertanyaanSurveiController::class, 'index'])->name('pertanyaan_survei.home');
Route::get('/pertanyaan_survei/getData', [RefPertanyaanSurveiController::class, 'getData'])->name('pertanyaan_survei.getData');
Route::post('/pertanyaan_survei/store', [RefPertanyaanSurveiController::class, 'store'])->name('pertanyaan_survei.store');
Route::post('/pertanyaan_survei/destroy', [RefPertanyaanSurveiController::class, 'destroy'])->name('pertanyaan_survei.destroy');

Route::get('/jenis_hibah', [RefJenisHibahController::class, 'index'])->name('jenis_hibah.home');
Route::get('/jenis_hibah/getData', [RefJenisHibahController::class, 'getData'])->name('jenis_hibah.getData');
Route::post('/jenis_hibah/store', [RefJenisHibahController::class, 'store'])->name('jenis_hibah.store');
Route::post('/jenis_hibah/destroy', [RefJenisHibahController::class, 'destroy'])->name('jenis_hibah.destroy');
Route::post('/jenis_hibah/switch-status', [RefJenisHibahController::class, 'switch_status'])->name('jenis_hibah.switch-status');
