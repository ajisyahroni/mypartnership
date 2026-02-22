@extends('layouts.app')
@push('styles')
@endpush
@section('contents')
    <style>
        /* @media (max-width: 991.98px) { */
        @media (max-width: 1023px) {
            .nav-sidebar {
                display: none !important;
            }

            .menu-toggle-btn {
                display: inline-block !important;
            }
        }

        .menu-toggle-btn {
            display: none;
            margin-bottom: 1rem;
        }

        /* Modal styles */
        .menu-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .menu-modal .menu-content {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            width: 90%;
            max-width: 300px;
        }

        .menu-modal .menu-content a {
            display: block;
            margin: 0.5rem 0;
            color: #2f3185;
            text-decoration: none;
            font-weight: 500;
        }

        .menu-modal .close-menu {
            float: right;
            font-size: 1.2rem;
            cursor: pointer;
        }
    </style>

    <style>
        html {
            scroll-behavior: smooth;
        }

        /* RESPONSIVE: Ubah layout sidebar dan konten untuk layar kecil */
        @media (max-width: 768px) {
            .title-dashboard {
                font-size: 18px;
            }

            .nav-sidebar .nav-link {
                font-size: 14px;
            }

            .nav-sidebar .nav-link {
                margin: 5px 0;
                padding: 8px 12px;
                font-size: 14px;
                border-radius: 10px !important;
            }

            .content-area {
                padding: 0 10px;
            }

            .content-section {
                border: none;
                padding: 10px;
            }

            #regions_div {
                width: 100% !important;
                height: auto !important;
                min-height: 300px;
            }
        }


        .body-konten {
            min-height: 100vh !important;
        }

        .nav-sidebar {
            width: 200px;
            background-color: #2d2f92;
            border-radius: 10px;
            padding-left: 20px;
            padding-top: 20px;
            padding-bottom: 20px;
            position: fixed;
        }

        .nav-sidebar.sticky {
            background-color: #2d2f92;
            border-radius: 10px;
            width: 200px;
            position: sticky;
            padding-left: 20px;
            padding-top: 20px;
            padding-bottom: 20px;
            top: 100%;
        }

        .nav-sidebar .nav-link {
            color: white;
            font-weight: bold;
            margin: 10px 0;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
            transition: background 0.3s;
            padding: 10px;
            padding-left: 20px;
        }

        .nav-sidebar .nav-link.active,
        .nav-sidebar .nav-link:hover {
            background-color: white;
            color: #2d2f92;
        }

        .content-area {
            /* margin-left: 280px; */
            /* padding: 20px; */
            transition: opacity 0.5s ease-in-out;
        }


        .content-section {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            background-color: white;
            width: 100%;
            border: 6px solid #2d2f92;
            border-radius: 10px;
            opacity: 1;
            margin-bottom: 50px;
        }

        .ranking-card {
            background-color: #f9f9f9;
        }

        .ranking-table thead {
            background-color: orange;
            color: #000;
        }

        .ranking-table th {
            font-weight: bold;
            color: #000;
        }

        .ranking-table td {
            color: #000;
        }

        .ranking-table th,
        .ranking-table td {
            border: 2px solid #000 !important;
            /* Garis hitam */
        }

        .ranking-table {
            border-collapse: collapse;
            font-size: 14px;
            /* Untuk menggabungkan garis */
        }

        .legend {
            margin-top: 10px;
        }

        .legend span {
            display: flex;
            align-items: center;
            margin-right: 20px;
            font-weight: bold;
            color: #444;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            background-color: gold;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .title-bar {
            height: 6px;
            width: 60px;
            background-color: #FFC107;
            margin-bottom: 10px;
        }

        .title-dashboard {
            color: #2d2f92;
            font-weight: bold;
        }

        .table-wrapper {
            max-height: 280px;
            overflow-y: auto;
            padding-right: 8px;
        }

        /* Untuk browser berbasis WebKit (Chrome, Edge, Safari) */
        .table-wrapper::-webkit-scrollbar {
            width: 6px;
            /* Tipis */
        }

        .table-wrapper::-webkit-scrollbar-track {
            background: transparent;
        }

        .table-wrapper::-webkit-scrollbar-thumb {
            background-color: #2d2f92;
            /* Biru tua */
            border-radius: 4px;
        }

        /* Hilangkan arrow atas dan bawah di beberapa browser (hanya WebKit-based) */
        .table-wrapper::-webkit-scrollbar-button {
            display: none;
        }

        /* Firefox support */
        @-moz-document url-prefix() {
            .table-wrapper {
                scrollbar-width: thin;
                scrollbar-color: #2d2f92 transparent;
            }
        }

        .dot {
            width: 10px;
            height: 10px;
            background-color: #ffb600;
            /* warna kuning bulat */
            border-radius: 50%;
            display: inline-block;
        }
    </style>
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
                            <a class="nav-link" href="#skor">Skor</a>
                            <a class="nav-link" href="#mitra">Sebaran Mitra</a>
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
                                <div class="col-12">
                                    <style>
                                        .point-box {
                                            width: 100px;
                                            height: 100px;
                                            margin: 20px auto 0;
                                            border-radius: 50%;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            font-size: 28px;
                                            font-weight: bold;
                                            color: white;
                                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                                            cursor: pointer;

                                        }

                                        .platinum {
                                            background: linear-gradient(135deg, #d4d4d4, #f5f5f5, #e0e0e0);
                                            color: #333;
                                        }


                                        .gold {
                                            background: linear-gradient(145deg, #ffdd57, #e6b800);
                                            color: #333;
                                        }

                                        .silver {
                                            background: linear-gradient(145deg, #d9d9d9, #bfbfbf);
                                            color: #333;
                                        }
                                    </style>

                                    <div id="skor" class="content-section p-3">
                                        <div class="container">
                                            <div class="title-bar"></div>
                                            <h3 class="title-dashboard">Skor</h3>
                                            <div class="row">
                                                <!-- Skor Prodi 1 Tahun Terakhir -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3 text-center">
                                                        <span class="title-dashboard">Skor Prodi 1 Tahun Terakhir</span>
                                                        {{-- <div class="point-box {{ @$ProdiScore1 > 50 ? 'platinum' : (@$ProdiScore1 < 50 && @$ProdiScore1 > 25 ? 'gold' : 'silver') }} " --}}
                                                        <div class="point-box platinum"
                                                            onclick="detailSkor('ProdiScore','Detail Skor Prodi','1')">
                                                            {{ round(@$ProdiScore1, 2) }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Skor Rata Rata 1 Tahun Terakhir -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3 text-center">
                                                        <span class="title-dashboard">Skor Rata Rata 1 Tahun Terakhir</span>
                                                        {{-- <div class="point-box {{ @$AverageScore5 > 50 ? 'platinum' : (@$AverageScore1 < 50 && @$AverageScore1 > 25 ? 'gold' : 'silver') }} " --}}
                                                        <div class="point-box platinum "
                                                            onclick="detailSkor('AverageScore','Detail Skor Rata-rata','1')">
                                                            {{ round(@$AverageScore1, 2) }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Skor Prodi 5 Tahun Terakhir -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3 text-center">
                                                        <span class="title-dashboard">Skor Prodi 5 Tahun Terakhir</span>
                                                        {{-- <div class="point-box {{ @$ProdiScore5 > 50 ? 'platinum' : (@$ProdiScore5 < 50 && @$ProdiScore5 > 25 ? 'gold' : 'silver') }} " --}}
                                                        <div class="point-box platinum"
                                                            onclick="detailSkor('ProdiScore','Detail Skor Prodi','5')">
                                                            {{ round(@$ProdiScore5, 2) }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Skor Rata Rata 5 Tahun Terakhir -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="ranking-card p-3 text-center">
                                                        <span class="title-dashboard">Skor Rata Rata 5 Tahun Terakhir</span>
                                                        {{-- <div class="point-box {{ @$AverageScore5 > 50 ? 'platinum' : (@$AverageScore5 < 50 && @$AverageScore5 > 25 ? 'gold' : 'silver') }} " --}}
                                                        <div class="point-box platinum "
                                                            onclick="detailSkor('AverageScore','Detail Skor Rata-rata','5')">
                                                            {{ round(@$AverageScore5, 2) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="mitra" class="content-section p-3">
                                        <div class="container">
                                            <div class="d-flex justify-content-between">
                                                <div class="col-12 col-md-8 mb-2">
                                                    <div class="title-bar"></div>
                                                    <h3 class="title-dashboard">Sebaran Mitra</h3>
                                                </div>
                                                <div class="col-12 col-md-4 mb-2">
                                                    <label for=""><b>Filter Data:</b></label>
                                                    <select name="filterSebaranMitra" id="filterSebaranMitra"
                                                        class="form-control select2">
                                                        <option value="Universitas">Universitas</option>
                                                        <option value="Fakultas">Fakultas</option>
                                                        <option value="Program Studi">Program Studi</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div id="regions_div" style="width: 900px; height: 400px;"></div>
                                        </div>
                                    </div>
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
                                                                            <td colspan="3" class="text-center">Tidak ada
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
                                            <div class="d-flex justify-content-between">
                                                <div class="col-12 col-md-8 mb-2">
                                                    <div class="title-bar"></div>
                                                    <h3 class="title-dashboard">Bentuk Kerja Sama</h3>
                                                </div>
                                                <div class="col-12 col-md-4 mb-2">
                                                    <label for=""><b>Filter Data:</b></label>
                                                    <select name="filterBentukKerma" id="filterBentukKerma"
                                                        class="form-control select2">
                                                        <option value="Universitas">Universitas</option>
                                                        <option value="Fakultas">Fakultas</option>
                                                        <option value="Program Studi">Program Studi</option>
                                                    </select>
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
                                            <div class="d-flex justify-content-between">
                                                <div class="col-12 col-md-8 mb-2">
                                                    <div class="title-bar"></div>
                                                    <h3 class="title-dashboard">Jenis Institusi</h3>
                                                </div>
                                                <div class="col-12 col-md-4 mb-2">
                                                    <label for=""><b>Filter Data:</b></label>
                                                    <select name="filterJenisInstitusi" id="filterJenisInstitusi"
                                                        class="form-control select2">
                                                        <option value="Universitas">Universitas</option>
                                                        <option value="Fakultas">Fakultas</option>
                                                        <option value="Program Studi">Program Studi</option>
                                                    </select>
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
                <a class="nav-link" href="#skor">Skor</a>
                <a class="nav-link" href="#mitra">Sebaran Mitra</a>
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
            <div class="modal-dialog modal-lg">
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

        <!-- Container-fluid Ends-->
    </div>

    <script>
        var UrlDetailSkor = "{{ route('home.detailSkor') }}";
        var UrldetailSelengkapnya = "{{ route('home.detailSelengkapnya') }}";
        var allDataKategoriData = @json($dataBentukKerjaSama);
        var allDataJenisInstitusiQuery = @json($dataJenisInstitusi);
        var allDataNegaraQuery = @json($dataNegara);
    </script>

    <script src="{{ asset('js/home/index.js') }}"></script>
@endsection
