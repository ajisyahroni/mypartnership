@extends('layouts.app')
@push('styles')
@endpush
@section('contents')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home-responsive.css') }}">
    <div class="page-body">
        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="content pt-4">
                <!-- Card Line 1 -->
                <div class="row flex-md-nowrap">
                    <!-- Skor Instansi Card -->
                    <div class="col-md-2 col-12">
                        <!-- Button to toggle menu (visible only on mobile/tablet) -->
                        <button class="btn btn-primary menu-toggle-btn" onclick="openMenu()">Menu</button>

                        <div class="nav-sidebar">
                            <a class="nav-link" href="#skor">Skor Produktivitas</a>
                            <a class="nav-link" href="#mitra">Sebaran Mitra</a>
                            <a class="nav-link" href="#rekap">Jumlah Mitra</a>
                            <a class="nav-link" href="#tren-grafis">Grafik Kerja Sama</a>
                            <a class="nav-link" href="#kerma_lembaga">Kerja Sama Lembaga</a>
                            <a class="nav-link" href="#ranking">Ranking</a>
                            <a class="nav-link" href="#kerja">Bentuk Kerja Sama</a>
                            <a class="nav-link" href="#institusi">Jenis Institusi</a>
                            <a class="nav-link" href="#prodi">Implementasi Program Studi</a>
                            <a class="nav-link" href="#fakultas">Implementasi Fakultas</a>
                        </div>
                        {{-- Tempat Navigasi --}}
                    </div>
                    <!-- Notifikasi Aktivitas Kerjasama -->
                    <div class="col-md-10 col-12">
                        <div class="container-fluid content-area">
                            <div class="row">
                               {{-- PROFIL --}}
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="profile-card profile-card-institution" style="cursor: pointer;">
                                        <div class="profile-content">
                                            <div class="profile-badge">
                                                <i class="fas fa-building"></i>
                                            </div>
                                            <div class="profile-info">
                                                <span class="profile-label">Lembaga</span>
                                                <h4 class="profile-title">
                                                    {{ auth()->user()->status_tempat ?? 'Belum ada lembaga' }}
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="profile-decoration">
                                            <div class="circle circle-1"></div>
                                            <div class="circle circle-2"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 mb-3">
                                    <div class="profile-card profile-card-user" style="cursor: pointer;">
                                        <div class="profile-content">
                                            <div class="profile-badge">
                                                <i class="fas fa-user-circle"></i>
                                            </div>
                                            <div class="profile-info">
                                                <span class="profile-label">Selamat Datang</span>
                                                <h4 class="profile-title">
                                                    {{ ucwords(Auth::user()->name) ?? '-' }}
                                                    @if(Auth::user()->jabatan)
                                                        <span class="profile-position-badge">
                                                            <i class="fas fa-briefcase"></i>
                                                            {{ ucwords(Auth::user()->jabatan) }}
                                                        </span>
                                                    @endif
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="profile-decoration">
                                            <div class="circle circle-1"></div>
                                            <div class="circle circle-2"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">

                                    {{-- Skor --}}

                                    <div id="skor" class="content-section p-3">
                                        <div class="container">
                                            <div class="title-bar"></div>
                                            <h3 class="title-dashboard">Skor Produktivitas</h3>
                                            <div class="row">
                                                <!-- Skor Prodi 1 Tahun Terakhir -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3 text-center">
                                                        <span class="title-dashboard">Skor {{ $jenisLembagaUser ?? 'Program Studi' }} 1 Tahun
                                                            Terakhir</span>
                                                        <div class="point-box platinum"
                                                            onclick="detailSkor('ProdiScore','Detail Skor {{ $jenisLembagaUser ?? 'Program Studi' }} 1 Tahun','1')">
                                                            {{ round(@$ProdiScore1, 2) }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Skor Rata Rata 1 Tahun Terakhir -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3 text-center">
                                                        <span class="title-dashboard">Skor Rata Rata 1 Tahun Terakhir</span>
                                                        <div class="point-box platinum "
                                                            onclick="detailSkor('AverageScore','Detail Skor Rata-rata 1 Tahun','1')">
                                                            {{ round(@$AverageScore1, 2) }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Skor Prodi 5 Tahun Terakhir -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3 text-center">
                                                        <span class="title-dashboard">Skor {{ $jenisLembagaUser ?? 'Program Studi' }} 5 Tahun
                                                            Terakhir</span>
                                                        <div class="point-box platinum"
                                                            onclick="detailSkor('ProdiScore','Detail Skor {{ $jenisLembagaUser ?? 'Program Studi' }} 5 Tahun','5')">
                                                            {{ round(@$ProdiScore5, 2) }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Skor Rata Rata 5 Tahun Terakhir -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3 text-center">
                                                        <span class="title-dashboard">Skor Rata Rata 5 Tahun Terakhir</span>
                                                        <div class="point-box platinum "
                                                            onclick="detailSkor('AverageScore','Detail Skor Rata-rata 5 Tahun','5')">
                                                            {{ round(@$AverageScore5, 2) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
                                    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

                                    <!-- Leaflet Search (CSS & JS) -->
                                    <link rel="stylesheet"
                                        href="https://unpkg.com/leaflet-search/dist/leaflet-search.min.css" />
                                    <script src="https://unpkg.com/leaflet-search/dist/leaflet-search.min.js"></script>

                                    @include('home.sebaran_mitra_produktif', [
                                        'KermaDNProduktif' => $kermaDNProduktif,
                                        'KermaLNProduktif' => $kermaLNProduktif,
                                        'dataNegaraProduktif2' => $dataNegaraProduktif2,
                                    ])

                                    @include('home.sebaran_mitra', [
                                        'KermaDN' => $kermaDN,
                                        'KermaLN' => $kermaLN,
                                        'dataNegara2' => $dataNegara2,
                                    ])



                                    {{-- @include('home.rekap', ['KermaDN' => $kermaDN, 'KermaLN' => $kermaLN]) --}}

                                    @include('home.grafisKerma', ['trenGrafis' => $trenGrafis])

                                    @include('home.kerjasama_lembaga', [
                                        'KerjaSamaLembaga' => $KerjaSamaLembaga,
                                        'q' => $q,
                                        'placeState' => $placeState,
                                    ])
                                    {{-- Ranking --}}

                                    <div id="ranking" class="content-section p-3">
                                        <div class="container">
                                            <div class="title-bar"></div>
                                            <h3 class="title-dashboard">Peringkat (5 Terbaik)</h3>
                                            <div class="row">
                                                <!-- Tabel Program Studi -->
                                                <!-- Tabel Ranking Program Studi -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3">
                                                        <div class="table-wrapper">
                                                            <table class="table table-bordered ranking-table mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 50px;">No</th>
                                                                        <th>Program Studi</th>
                                                                        <th style="width: 100px;">Skor</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse ($dataProdi as $dtProdi)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td>{{ $dtProdi->status_tempat }}</td>
                                                                            <td>{{ number_format($dtProdi->jumlah_skor, 0) }}
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="3" class="text-center">Tidak ada
                                                                                data program studi.</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="btn btn-purple btn-sm mt-2 w-100 btn-selengkapnya"
                                                            data-tipe="peringkat" data-jenis="prodi"
                                                            data-judul="Peringkat Program Studi">Lihat
                                                            Selengkapnya
                                                        </div>
                                                        <div class="d-flex align-items-center mt-2">
                                                            <span class="dot bg-success me-2"
                                                                style="width: 10px; height: 10px; border-radius: 50%; display: inline-block;"></span>
                                                            <span class="fw-semibold text-muted">Program Studi</span>
                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- Tabel Ranking Fakultas -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3">
                                                        <div class="table-wrapper">
                                                            <table class="table table-bordered ranking-table mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 50px;">No</th>
                                                                        <th>Fakultas</th>
                                                                        <th style="width: 100px;">Skor</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse ($dataFakultas as $dtFakultas)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td>{{ $dtFakultas->status_tempat }}</td>
                                                                            <td>{{ number_format($dtFakultas->jumlah_skor, 0) }}
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="3" class="text-center">Tidak
                                                                                ada
                                                                                data fakultas.</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="btn btn-purple btn-sm mt-2 w-100 btn-selengkapnya"
                                                            data-tipe="peringkat" data-jenis="fakultas"
                                                            data-judul="Peringkat Fakultas">Lihat
                                                            Selengkapnya
                                                        </div>
                                                        <div class="d-flex align-items-center mt-2">
                                                            <span class="dot bg-primary me-2"
                                                                style="width: 10px; height: 10px; border-radius: 50%; display: inline-block;"></span>
                                                            <span class="fw-semibold text-muted">Fakultas</span>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bentuk Kerja Sama -->
                                    <div id="kerja" class="content-section p-3">
                                        <div class="container">
                                            <div class="d-block d-sm-flex justify-content-between">
                                                <div class="col-12 col-md-8 mb-2">
                                                    <div class="title-bar"></div>
                                                    <h3 class="title-dashboard">Bentuk Kerja Sama</h3>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <style>
                                                    /* Style bar */
                                                    #chart_kerma_dn svg rect,
                                                    #chart_kerma_ln svg rect,
                                                    #chart_implementasi_dn svg rect,
                                                    #chart_implementasi_ln svg rect {
                                                        rx: 6 !important;
                                                        ry: 6 !important;
                                                        cursor: pointer;
                                                        transition: fill 0.3s ease;
                                                    }
                                                </style>

                                                <!-- Dalam Negeri -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3">
                                                        <div class="chart-wrapper position-relative"
                                                            style="min-height: 300px;">
                                                            <div id="chart_kerma_dn" style="width: 100%; height: 300px;">
                                                            </div>
                                                            <div class="no-data-dn d-none text-center text-muted"
                                                                style="height: 300px; display: flex; align-items: center; justify-content: center; position: absolute; top: 0; left: 0; width: 100%; background-color: rgba(255,255,255,0.9); z-index: 5;">
                                                                Tidak ada data untuk kategori dalam negeri.
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center mt-2">
                                                            <span class="dot me-2"></span>
                                                            <span class="title-dashboard">Dalam Negeri</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Luar Negeri -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3">
                                                        <div class="chart-wrapper position-relative"
                                                            style="min-height: 300px;">
                                                            <div id="chart_kerma_ln" style="width: 100%; height: 300px;">
                                                            </div>
                                                            <div class="no-data-ln d-none text-center text-muted"
                                                                style="height: 300px; display: flex; align-items: center; justify-content: center; position: absolute; top: 0; left: 0; width: 100%; background-color: rgba(255,255,255,0.9); z-index: 5;">
                                                                Tidak ada data untuk kategori luar negeri.
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center mt-2">
                                                            <span class="dot me-2"></span>
                                                            <span class="title-dashboard">Luar Negeri</span>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- Jenis Institusi -->
                                    <div id="institusi" class="content-section p-3">
                                        <div class="container">
                                            <div class="d-block d-sm-flex justify-content-between">
                                                <div class="col-12 col-md-8 mb-2">
                                                    <div class="title-bar"></div>
                                                    <h3 class="title-dashboard">Jenis Institusi</h3>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <!-- Dalam Negeri -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3">
                                                        <div class="chart-wrapper position-relative"
                                                            style="min-height: 300px;">
                                                            <div id="chart_implementasi_dn"
                                                                style="width: 100%; height: 300px;">
                                                            </div>
                                                            <div class="no-data-institusi-dn d-none text-center text-muted"
                                                                style="height: 300px; display: flex; align-items: center; justify-content: center; position: absolute; top: 0; left: 0; width: 100%; background-color: rgba(255,255,255,0.9); z-index: 5;">
                                                                Tidak ada data untuk institusi dalam negeri.
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center mt-2">
                                                            <span class="dot me-2"></span>
                                                            <span class="title-dashboard">Dalam Negeri</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Luar Negeri -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3">
                                                        <div class="chart-wrapper position-relative"
                                                            style="min-height: 300px;">
                                                            <div id="chart_implementasi_ln"
                                                                style="width: 100%; height: 300px;">
                                                            </div>
                                                            <div class="no-data-institusi-ln d-none text-center text-muted"
                                                                style="height: 300px; display: flex; align-items: center; justify-content: center; position: absolute; top: 0; left: 0; width: 100%; background-color: rgba(255,255,255,0.9); z-index: 5;">
                                                                Tidak ada data untuk institusi luar negeri.
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center mt-2">
                                                            <span class="dot me-2"></span>
                                                            <span class="title-dashboard">Luar Negeri</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div id="prodi" class="content-section p-3">
                                        <div class="container">
                                            <div class="title-bar"></div>
                                            <h3 class="title-dashboard">Implementasi dari Program Studi (5 Terbaik)</h3>
                                            <div class="row">
                                                <!-- Tabel Produktivitas Kerja Sama -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3">
                                                        <div class="table-wrapper">
                                                            <table class="table table-bordered ranking-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th rowspan="2"
                                                                            style="vertical-align:middle;text-align:center;">
                                                                            No</th>
                                                                        <th rowspan="2"
                                                                            style="vertical-align:middle;text-align:center;">
                                                                            Lembaga</th>
                                                                        <th colspan="2" style="text-align:center;">
                                                                            Implementasi Kerja Sama</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="text-center">Dalam Negeri
                                                                        </th>
                                                                        <th class="text-center">Luar
                                                                            Negeri</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse ($dataProdiProduktif as $dtProdiProduktif)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td>{{ $dtProdiProduktif->status_tempat }}</td>
                                                                            <td>{{ number_format($dtProdiProduktif->jumlah_produktivitas_kerma_dn, 0) }}
                                                                            </td>
                                                                            <td>{{ number_format($dtProdiProduktif->jumlah_produktivitas_kerma_ln, 0) }}
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="4" class="text-center">Tidak
                                                                                ada
                                                                                data Program Studi.</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="btn btn-purple btn-sm mt-2 w-100 btn-selengkapnya"
                                                            data-tipe="produktif" data-jenis="prodi"
                                                            data-judul="Produktivitas Kerja Sama Program Studi ">Lihat
                                                            Selengkapnya
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <span class="dot me-2"></span>
                                                            <span class="title-dashboard">Produktivitas Kerja
                                                                Sama</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Jumlah Mitra Terbanyak -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3">
                                                        <div class="table-wrapper">
                                                            <table class="table table-bordered ranking-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th rowspan="2"
                                                                            style="vertical-align:middle;text-align:center;">
                                                                            No</th>
                                                                        <th rowspan="2"
                                                                            style="vertical-align:middle;text-align:center;">
                                                                            Lembaga</th>
                                                                        <th colspan="2" style="text-align:center;">
                                                                            Implementasi Kerja Sama</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="text-center">Dalam Negeri
                                                                        </th>
                                                                        <th class="text-center">Luar
                                                                            Negeri</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse ($dataProdiMitra as $dtProdiMitra)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td>{{ $dtProdiMitra->status_tempat }}</td>
                                                                            <td>{{ number_format($dtProdiMitra->jumlah_mitra_kerma_dn, 0) }}
                                                                            </td>
                                                                            <td>{{ number_format($dtProdiMitra->jumlah_mitra_kerma_ln, 0) }}
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="4" class="text-center">Tidak
                                                                                ada
                                                                                data Program Studi.</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="btn btn-purple btn-sm mt-2 w-100 btn-selengkapnya"
                                                            data-tipe="mitra" data-jenis="prodi"
                                                            data-judul="Mitra Kerja Sama Program Studi ">Lihat
                                                            Selengkapnya
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <span class="dot me-2"></span>
                                                            <span class="title-dashboard">Jumlah Mitra
                                                                Terbanyak</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="fakultas" class="content-section p-3">
                                        <div class="container">
                                            <div class="title-bar"></div>
                                            <h3 class="title-dashboard">Implementasi dari Fakultas (5 Terbaik)</h3>
                                            <div class="row">
                                                <!-- Tabel Produktivitas Kerja Sama -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3">
                                                        <div class="table-wrapper">
                                                            <table class="table table-bordered ranking-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th rowspan="2"
                                                                            style="vertical-align:middle;text-align:center;">
                                                                            No</th>
                                                                        <th rowspan="2"
                                                                            style="vertical-align:middle;text-align:center;">
                                                                            Lembaga</th>
                                                                        <th colspan="2" style="text-align:center;">
                                                                            Implementasi Kerja Sama</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="text-center">Dalam Negeri
                                                                        </th>
                                                                        <th class="text-center">Luar
                                                                            Negeri</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse ($dataFakultasProduktif as $dtFakultasProduktif)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td>{{ $dtFakultasProduktif->status_tempat }}
                                                                            </td>
                                                                            <td>{{ number_format($dtFakultasProduktif->jumlah_produktivitas_kerma_dn, 0) }}
                                                                            </td>
                                                                            <td>{{ number_format($dtFakultasProduktif->jumlah_produktivitas_kerma_ln, 0) }}
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="4" class="text-center">Tidak
                                                                                ada
                                                                                data Fakultas.</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="btn btn-purple btn-sm mt-2 w-100 btn-selengkapnya"
                                                            data-tipe="produktif" data-jenis="fakultas"
                                                            data-judul="Produktivitas Kerja Sama Program Studi ">Lihat
                                                            Selengkapnya
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <span class="dot me-2"></span>
                                                            <span class="title-dashboard">Produktivitas Kerja
                                                                Sama</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Jumlah Mitra Terbanyak -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3">
                                                        <div class="table-wrapper">
                                                            <table class="table table-bordered ranking-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th rowspan="2"
                                                                            style="vertical-align:middle;text-align:center;">
                                                                            No</th>
                                                                        <th rowspan="2"
                                                                            style="vertical-align:middle;text-align:center;">
                                                                            Lembaga</th>
                                                                        <th colspan="2" style="text-align:center;">
                                                                            Implementasi Kerja Sama</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="text-center">Dalam Negeri
                                                                        </th>
                                                                        <th class="text-center">Luar
                                                                            Negeri</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse ($dataFakultasMitra as $dtFakultasMitra)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td>{{ $dtFakultasMitra->status_tempat }}</td>
                                                                            <td>{{ number_format($dtFakultasMitra->jumlah_mitra_kerma_dn, 0) }}
                                                                            </td>
                                                                            <td>{{ number_format($dtFakultasMitra->jumlah_mitra_kerma_ln, 0) }}
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="4" class="text-center">Tidak
                                                                                ada
                                                                                data Fakultas.</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="btn btn-purple btn-sm mt-2 w-100 btn-selengkapnya"
                                                            data-tipe="mitra" data-jenis="fakultas"
                                                            data-judul="Mitra Kerja Sama Program Studi ">Lihat
                                                            Selengkapnya
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <span class="dot me-2"></span>
                                                            <span class="title-dashboard">Jumlah Mitra
                                                                Terbanyak</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Tempat Konten --}}
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal for menu -->
        <div class="menu-modal" id="menuModal">
            <div class="menu-content">
                <span class="close-menu" onclick="closeMenu()">×</span>
                <a class="nav-link" href="#skor">Skor Produktivitas</a>
                <a class="nav-link" href="#mitra">Sebaran Mitra</a>
                <a class="nav-link" href="#rekap">Jumlah Mitra</a>
                <a class="nav-link" href="#tren-grafis">Grafik Kerja Sama</a>
                <a class="nav-link" href="#kerma_lembaga">Kerja Sama Lembaga</a>
                <a class="nav-link" href="#ranking">Ranking</a>
                <a class="nav-link" href="#kerja">Bentuk Kerja Sama</a>
                <a class="nav-link" href="#institusi">Jenis Institusi</a>
                <a class="nav-link" href="#prodi">Implementasi Program Studi</a>
                <a class="nav-link" href="#fakultas">Implementasi Fakultas</a>
            </div>
        </div>

        <script>
            function openMenu() {
                document.getElementById("menuModal").style.display = "flex";
            }

            function closeMenu() {
                document.getElementById("menuModal").style.display = "none";
            }

            // Optional: Close when clicking outside menu
            window.addEventListener('click', function(event) {
                const modal = document.getElementById("menuModal");
                if (event.target === modal) {
                    closeMenu();
                }
            });
        </script>


        <div class="modal fade" id="modal-detail" aria-labelledby="DetailLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="DetailLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="konten-detail">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-selengkapnya" aria-labelledby="SelengkapnyaLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="SelengkapnyaLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="konten-selengkapnya">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="modal-pemberitahuan" aria-labelledby="PemberitahuanLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">

                    <!-- Header -->
                    <div class="modal-header border-0 bg-primary bg-gradient text-white py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                <i class="bi bi-shield-check fs-5"></i>
                            </div>
                            <div>
                                <h6 class="modal-title fw-bold mb-0" id="PemberitahuanLabel">Pemberitahuan Verifikator</h6>
                                <small class="opacity-75" style="font-size: 0.75rem;">MyPartnership</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-3" id="konten-pemberitahuan">
                        
                        <!-- Info Card -->
                        <div class="alert alert-primary bg-opacity-10 border border-primary border-opacity-25 rounded-3 mb-3 py-2">
                            <div class="d-flex align-items-start gap-2">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                    <i class="bi bi-person-badge text-light fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1 small">
                                        <strong>Anda masuk sebagai:</strong> <span class="badge bg-primary px-2 py-1">Verifikator</span>
                                    </p>
                                    <p class="mb-2 text-muted" style="font-size: 0.8rem;">Tanggung jawab Anda:</p>

                                    <div class="d-flex align-items-start gap-2 mb-2">
                                        <i class="bi bi-check-circle-fill text-success" style="font-size: 0.9rem;"></i>
                                        <span style="font-size: 0.8rem;">Memvalidasi <strong>pengajuan dokumen kerja sama</strong></span>
                                    </div>

                                    <div class="alert alert-warning bg-opacity-10 border-0 mb-0 py-1 px-2">
                                        <small class="d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                            <span>Pastikan data sesuai sebelum memberikan persetujuan</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Card -->
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body text-center p-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                                    <i class="bi bi-file-earmark-check text-light fs-3"></i>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">Verifikasi Pengajuan</h6>
                                <p class="text-muted mb-2" style="font-size: 0.75rem;">Validasi dokumen kerja sama</p>
                                <a href="{{ route('pengajuan.home') }}" 
                                class="btn btn-primary btn-sm w-100 d-flex justify-content-center align-items-center gap-2 rounded-pill">
                                    <i class="bi bi-arrow-right-circle"></i>
                                    <span class="fw-semibold">Mulai Verifikasi</span>
                                </a>
                            </div>
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="modal-footer border-0 bg-light bg-gradient py-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2 rounded-pill px-3" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i>
                            <span>Tutup</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <script>
        var UrlDetailSkor = "{{ route('home.detailSkor') }}";
        var UrldetailSelengkapnya = "{{ route('home.detailSelengkapnya') }}";
        var allDataKategoriData = @json($dataBentukKerjaSama);
        var allDataJenisInstitusiQuery = @json($dataJenisInstitusi);
        var allDataNegaraQuery = @json($dataNegara);
        var roleDashboard = @json(session('current_role'));
        var notif_verifikator = @json($notif_verifikator);
    </script>

    <script>
        $(document).ready(function() {
            if (roleDashboard == 'verifikator' && notif_verifikator > 0) {
                $("#modal-pemberitahuan").modal('show');
            }
            // Terapkan Filter
            function setupFilter(selectId) {
                const $select = $(selectId);

                // Trigger on load
                $select.trigger("change");

                // Event handler
                $select.on("change", function (e) {
                    e.preventDefault();

                    const selectedOption = $select.find("option:selected");
                    const selectedFilter = selectedOption.val();
                    const placeState = selectedOption.data("place_state");

                    const baseUrl = window.location.origin + window.location.pathname;

                    showLoading("Menerapkan Filter...");

                    setTimeout(() => {
                        let params = [];

                        if (selectedFilter) {
                            params.push(`q=${encodeURIComponent(selectedFilter)}`);
                        }
                        if (placeState) {
                            params.push(`ps=${encodeURIComponent(placeState)}`);
                        }

                        const targetUrl = params.length ? `${baseUrl}?${params.join("&")}` : baseUrl;

                        window.location.href = targetUrl;
                    }, 300);
                });
            }

            // Apply to both filters
            setupFilter("#filterSebaranMitra");
            setupFilter("#filterSebaranMitraProduktif");


            // Reset Filter
            $('#resetFilterBtn').on('click', function(e) {
                e.preventDefault();

                showLoading("Mereset Filter...");

                setTimeout(() => {
                    window.location.href = window.location.origin + window.location.pathname;
                }, 300);
            });
        });
    </script>
    <script src="{{ asset('js/home/index.js') }}"></script>
@endsection
