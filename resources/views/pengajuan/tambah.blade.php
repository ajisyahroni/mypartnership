@extends('layouts.app')

@section('contents')
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <span class="me-2">
                                    <i class="fa-solid fa-folder-plus text-warning"></i>
                                </span>
                                {{ @$page_title }}
                            </h5>
                            <a href="{{ route('pengajuan.home') }}" class="btn btn-danger btn-sm shadow-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Tempat untuk konten tambahan -->
                        <div class="content">
                            <div class="container mt-4">
                                <form action="{{ route('pengajuan.store_baru') }}" enctype="multipart/form-data"
                                    method="post" id="formInput">
                                    @csrf
                                    <input type="hidden" name="ajuan_kerma_key" id="ajuan_kerma_key" value="{{ session('ajuan_kerma_key') }}">
                                    <input type="hidden" name="stats_kerma" id="stats_kerma" value="{{ @$stats_kerma }}">
                                    <input type="hidden" name="place_state" id="place_state"
                                        value="{{ Auth::user()->place_state }}">
                                    <div class="card mb-3 alert alert-danger">
                                        <div class="card-body">
                                            @if (@$stats_kerma == 'Lapor Kerma')
                                                <h5 class="fw-bold text-center w-100">"Halaman ini berisi
                                                    form
                                                    untuk menyimpan dokumen yang sudah tertanda tangani."
                                                </h5>
                                            @elseif (@$stats_kerma == 'Ajuan Baru')
                                                <h5 class="fw-bold text-center w-100">"Halaman ini berisi
                                                    form
                                                    untuk menyusun
                                                    dokumen kerja sama baru, baik yang belum ada maupun yang
                                                    masih dalam tahap draft."</h5>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="accordion custom-accordion" id="customAccordion">
                                        <!-- Detail Dokumen Kerja Sama -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseOne" aria-expanded="true">
                                                    <i class="fa-solid fa-file-contract me-2"></i> Detail Dokumen Kerja Sama
                                                </button>
                                            </h2>
                                            <div id="collapseOne" class="accordion-collapse collapse show">
                                                <div class="accordion-body">
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            <div class="row mb-3 align-items-center ">
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">
                                                                    Jenis Dokumen Kerja Sama <span
                                                                        class="text-danger">*</span>
                                                                </label>
                                                                <div class="col-sm-9">
                                                                    <select id="jenis_kerjasama" name="jenis_kerjasama"
                                                                        class="form-select text-dark text-sm select2 w-100">
                                                                        <option value="">Pilih Jenis Dokumen</option>
                                                                        @foreach ($jenis_dokumen as $dokumen)
                                                                            <option value="{{ $dokumen->nama_dokumen }}"
                                                                                data-alias="{{ $dokumen->alias }}"
                                                                                data-nama_dokumen="{{ $dokumen->nama_dokumen }}"
                                                                                data-lingkup_unit="{{ $dokumen->lingkup_unit }}"
                                                                                {{ @$dataPengajuan->jenis_kerjasama == $dokumen->nama_dokumen ? 'selected' : '' }}>
                                                                                {{ $dokumen->nama_dokumen }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="row mb-3 align-items-start d-none"
                                                                id="pilih_mou_wrapper">
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">
                                                                    Pilih Jenis MoU
                                                                </label>
                                                                <div class="col-sm-9">
                                                                    <select id="pilih_mou" name="pilih_mou"
                                                                        class="form-select select2">
                                                                        <option value="">Pilih Jenis MoU (Masukkan
                                                                            Nama Institusi)</option>
                                                                        @foreach ($jenis_mou as $mou)
                                                                        @php
                                                                            $mou_tanggal = Tanggal_Indo($mou->mulai) . ($mou->periode_kerma == 'bydoc' ? ' s/d '. Tanggal_Indo($mou->selesai) : '');
                                                                        @endphp
                                                                            <option value="{{ $mou->id_mou }}"
                                                                                data-nama_institusi="{{ $mou->nama_institusi }}"
                                                                                data-jenis_kerjasama="{{ $mou->jenis_kerjasama }}"
                                                                                data-jenis_institusi="{{ $mou->jenis_institusi }}"
                                                                                 data-status_tempat="{{ $mou->status_tempat }}"
                                                                                data-periode_kerma="{{ $mou->periode_kerma }}"
                                                                                data-status_mou="{{ $mou->status_mou }}"
                                                                                data-tanggal="{{$mou_tanggal}}"
                                                                                data-kontribusi="{{ $mou->kontribusi }}">
                                                                                {{ $mou->nama_institusi }} |
                                                                                {{ $mou->status_tempat }} |
                                                                                {{ $mou->jenis_institusi }} |
                                                                                {{ $mou->jenis_kerjasama }} |
                                                                                {{ $mou->kontribusi }} |
                                                                                {{ $mou_tanggal }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <div class="alert alert-primary mt-2 p-2 d-none"
                                                                        id="fill_pilih_mou_wrapper">
                                                                        <span class="fill_pilih_mou"
                                                                            style="font-size: 12px;">Pilih Dokumen
                                                                            MoU</span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Nomor Dokumen -->
                                                            <div class="row mb-3 align-items-center">
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">Nomor
                                                                    Dokumen</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="no_dokumen"
                                                                        class="form-control"
                                                                        value="{{ @$dataPengajuan->no_dokumen ?? @$dataPengajuan->no_dokumen }}"
                                                                        placeholder="Masukkan Nomor Dokumen">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Pelaksana Kerja Sama -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseTwo" aria-expanded="true">
                                                    <i class="bx bx-user-check me-2"></i> Pelaksana Kerja Sama
                                                </button>
                                            </h2>
                                            <div id="collapseTwo" class="accordion-collapse collapse show">
                                                <div class="accordion-body">
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            <div class="row mb-3 align-items-center">
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">
                                                                    Tingkat Kerja Sama <span class="text-danger">*</span>
                                                                </label>
                                                                <div class="col-sm-9">
                                                                    <div id="tingkat_kerjasama_wrapper">
                                                                        <div id="tingkat_kerjasama">
                                                                            @foreach ($tingkat_kerjasama as $tingkat)
                                                                                <div
                                                                                    class="form-check d-none tingkat-{{ preg_replace('/[^a-zA-Z0-9]/', '', $tingkat->nama) }} {{ $tingkat->check }}">
                                                                                    <input class="form-check-input"
                                                                                        type="radio" name="prodi_unit"
                                                                                        id="{{ $tingkat->label }}"
                                                                                        value="{{ $tingkat->nama }}">
                                                                                    <label class="form-check-label"
                                                                                        for="{{ $tingkat->label }}">{{ $tingkat->nama }}</label>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Dropdown Fakultas, Prodi, Unit -->
                                                            <div class="row mb-3 align-items-center d-none dropdown-wrapper dropdown-prodi_unit"
                                                                id="select_fakultas_wrapper">
                                                                <label for="select_fakultas"
                                                                    class="col-sm-3 text-dark text-sm fw-bold">Pilih
                                                                    Fakultas</label>
                                                                <div class="col-sm-9">
                                                                    <select id="select_fakultas" name="lvl_fak"
                                                                        class="form-select text-dark text-sm select2">
                                                                        <option value="">Pilih Fakultas</option>
                                                                        @foreach ($fakultas as $fak)
                                                                            <option value="{{ $fak->id_lmbg }}"
                                                                                data-nama="{{ $fak->nama_lmbg }}"
                                                                                {{ $fak->id_lmbg == $fak_user ? 'selected':'' }}
                                                                                 >
                                                                                {{ $fak->nama_lmbg }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="row mb-3 align-items-center d-none dropdown-wrapper dropdown-prodi_unit"
                                                                id="select_prodi_wrapper">
                                                                <label for="select_prodi"
                                                                    class="col-sm-3 text-dark text-sm fw-bold">Pilih
                                                                    Program Studi</label>
                                                                <div class="col-sm-9">
                                                                    <select id="select_prodi" name="lvl_prodi"
                                                                        class="form-select text-dark text-sm select2">
                                                                        <option value="">Pilih Program Studi</option>
                                                                        @foreach ($program_studi as $studi)
                                                                            <option value="{{ $studi->id_lmbg }}"
                                                                                data-nama="{{ $studi->nama_lmbg }}"
                                                                                >
                                                                                {{ $studi->nama_lmbg }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="row mb-3 align-items-center d-none dropdown-wrapper dropdown-prodi_unit"
                                                                id="select_unit_wrapper">
                                                                <label for="select_unit"
                                                                    class="col-sm-3 text-dark text-sm fw-bold">Pilih
                                                                    Unit</label>
                                                                <div class="col-sm-9">
                                                                    <select id="select_unit" name="lvl_unit"
                                                                        class="form-select text-dark text-sm select2">
                                                                        <option value="">Pilih Unit</option>
                                                                        @foreach ($unit as $u)
                                                                            <option value="{{ $u->id_lmbg }}">
                                                                                {{ $u->nama_lmbg }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Detail Mitra Kerja Sama -->    
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseThree" aria-expanded="true">
                                                    <i class="fa-solid fa-users me-2"></i> Detail Mitra Kerja Sama
                                                </button>
                                            </h2>
                                            <div id="collapseThree" class="accordion-collapse collapse show">
                                                <div class="accordion-body">
                                                    <!-- Jenis Institusi Mitra -->
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            <div class="row mb-3 align-items-center dropdown-wrapper"
                                                                id="select_institusi_mitra_wrapper">
                                                                <label for="select_institusi_mitra"
                                                                    class="col-sm-3 text-dark text-sm fw-bold">Jenis
                                                                    Institusi Mitra<span
                                                                        class="text-danger">*</span></label>
                                                                <div class="col-sm-9">
                                                                    <select id="select_institusi_mitra"
                                                                        name="jenis_institusi"
                                                                        class="form-select text-dark text-sm select2">
                                                                        <option data-alias="" value="">Pilih Jenis
                                                                            Institusi Mitra </option>
                                                                        @foreach ($jenis_institusi_mitra as $mitra)
                                                                            <option data-alias="{{ $mitra->alias }}"
                                                                                value="{{ $mitra->klasifikasi }}">
                                                                                {{ $mitra->klasifikasi }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Wrapper Perusahaan --}}
                                                            <div class="row mb-3 align-items-center dropdown-wrapper d-none"
                                                                id="select_perusahaan_wrapper">
                                                                <label for="select_perusahaan"
                                                                    class="col-sm-3 text-dark text-sm fw-bold">Jenis
                                                                    Perusahaan</label>
                                                                <div class="col-sm-9">
                                                                    <select id="select_perusahaan" name="jenis_perusahaan"
                                                                        class="form-select text-dark text-sm select2">
                                                                        <option data-alias="" value="">Pilih Jenis
                                                                            Perusahaan </option>
                                                                        @foreach ($perusahaans as $perusahaan)
                                                                            <option value="{{ $perusahaan->nama }}">
                                                                                {{ $perusahaan->nama }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <!-- Nama Institusi Mitra -->
                                                            <div class="mitra_universitas_wrapper d-none"
                                                                id="mitra_universitas_wrapper">
                                                                <div class="row mb-3 align-items-center">
                                                                    <label
                                                                        class="col-sm-3 text-dark text-sm fw-bold">Rangking
                                                                        Universitas</label>
                                                                    <div class="col-sm-9">
                                                                        <select id="select_rangking_universitas"
                                                                            name="rangking_univ"
                                                                            class="form-select text-dark text-sm select2">
                                                                            <option value="">Pilih Rangking
                                                                                Universitas </option>
                                                                            @foreach ($rangking_universitas as $group)
                                                                                <optgroup label="{{ $group->group }}"
                                                                                    class="p-2">
                                                                                    @foreach ($group->getRangking as $rangking)
                                                                                        <option
                                                                                            value="{{ $rangking->nama }}"
                                                                                            class="p-2">
                                                                                            {{ $rangking->nama }}</option>
                                                                                    @endforeach
                                                                                </optgroup>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="row mb-3 align-items-center">
                                                                    <label
                                                                        class="col-sm-3 text-dark text-sm fw-bold">Fakultas</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="text" name="nama_fk_mitra"
                                                                            class="form-control"
                                                                            placeholder="Masukkan Nama Fakultas Mitra">
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3 align-items-center">
                                                                    <label
                                                                        class="col-sm-3 text-dark text-sm fw-bold">Prodi/Department/Jurusan</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="text" name="nama_dept_mitra"
                                                                            class="form-control"
                                                                            placeholder="Masukkan Nama Prodi/Department/Jurusan Mitra">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Jenis Institusi Lain -->
                                                            <div class="row mb-3 align-items-center d-none"
                                                                id="jenis_institusi_wrapper">
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">Jenis
                                                                    Institusi Lain</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="jenis_institusi_lain"
                                                                        class="form-control"
                                                                        placeholder="Masukkan Nama Institusi Mitra">
                                                                </div>
                                                            </div>

                                                            <!-- Nama Institusi Mitra -->
                                                            <div class="row mb-3 align-items-center">
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">Nama
                                                                    Institusi Mitra<span
                                                                        class="text-danger">*</span></label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="nama_institusi"
                                                                        class="form-control"
                                                                        placeholder="Masukkan Nama Institusi Mitra">
                                                                </div>
                                                            </div>

                                                            <!-- Kerja Sama dengan Mitra -->
                                                            <div class="row mb-3 align-items-center">
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">
                                                                    Kerja Sama dengan Mitra <span
                                                                        class="text-danger">*</span>
                                                                </label>
                                                                <div class="col-sm-9">
                                                                    <div id="kerja_sama_mitra_wrapper">
                                                                        <div id="kerja_sama_mitra">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="dn_ln" id="dalam_negeri"
                                                                                value="Dalam Negeri">
                                                                            <label class="form-check-label me-2"
                                                                                for="dalam_negeri">Dalam Negeri</label>
                                                                            <input class="form-check-input" type="radio"
                                                                                name="dn_ln" id="luar_negeri"
                                                                                value="Luar Negeri">
                                                                            <label class="form-check-label me-2"
                                                                                for="luar_negeri">Luar
                                                                                Negeri</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row mt-4 mb-4 align-items-center d-none"
                                                                id="wilayah_mitra_wrapper">
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">
                                                                    Wilayah Mitra <span class="text-danger">*</span>
                                                                </label>
                                                                <div class="col-sm-9">
                                                                    <div id="wilayah_mitra">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="wilayah_mitra" id="nasional"
                                                                            value="Nasional">
                                                                        <label class="form-check-label me-2"
                                                                            for="nasional">Nasional</label>
                                                                        <input class="form-check-input" type="radio"
                                                                            name="wilayah_mitra" id="lokal"
                                                                            value="Lokal">
                                                                        <label class="form-check-label me-2"
                                                                            for="lokal">Lokal</label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row mb-3 align-items-center dropdown-wrapper d-none"
                                                                id="negara_mitra_wrapper">
                                                                <label for="negara_mitra"
                                                                    class="col-sm-3 text-dark text-sm fw-bold">Pilih Negara
                                                                    Mitra
                                                                    <span class="text-danger">*</span></label>
                                                                <div class="col-sm-9">
                                                                    <select id="negara_mitra" name="negara_mitra"
                                                                        class="form-select text-dark text-sm select2">
                                                                        <option value="">Pilih Negara Mitra</option>
                                                                        @foreach ($negara_mitra as $negara)
                                                                            <option value="{{ $negara->name }}">
                                                                                {{ $negara->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <!-- Alamat Institusi Mitra -->
                                                            <div class="row mb-3 align-items-center">
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">Alamat
                                                                    Institusi
                                                                    Mitra<span class="text-danger">*</span></label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="alamat_mitra"
                                                                        class="form-control"
                                                                        placeholder="Masukkan Alamat Institusi Mitra">
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Penandatangan Kerja Sama -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse4" aria-expanded="true">
                                                    <i class="bx bx-pencil me-2"></i> Penandatangan Kerja Sama
                                                </button>
                                            </h2>
                                            <div id="collapse4" class="accordion-collapse collapse show">
                                                <div class="accordion-body">
                                                    <div class="container mt-4">
                                                        <!-- Pihak Internal -->
                                                        <div class="card mb-3 pihak-internal">
                                                            <div class="pihak_internal_wrapper">
                                                                <div
                                                                    class="card-header bg-light d-flex align-items-center justify-content-between">
                                                                    <div>
                                                                        <i
                                                                            class="bx bx-user-circle me-2 text-primary fs-4"></i>
                                                                        <strong>Pihak Internal</strong>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Nama Penandatangan <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="text" name="ttd_internal[]"
                                                                            class="form-control"
                                                                            placeholder="Tulis disini">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Jabatan Penandatangan
                                                                            <span class="text-danger">*</span></label>
                                                                        <input type="text" name="lvl_internal[]"
                                                                            class="form-control"
                                                                            placeholder="Tulis disini">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Pihak Mitra -->
                                                        <div class="card mb-3 pihak-mitra">
                                                            <div class="pihak_mitra_wrapper">
                                                                <div
                                                                    class="card-header bg-light d-flex align-items-center justify-content-between">
                                                                    <div>
                                                                        <i
                                                                            class="bx bx-user-circle me-2 text-primary fs-4"></i>
                                                                        <strong>Pihak Mitra</strong>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Nama Penandatangan <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="text" name="ttd_eksternal[]"
                                                                            class="form-control"
                                                                            placeholder="Tulis disini">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Jabatan Penandatangan
                                                                            <span class="text-danger">*</span></label>
                                                                        <input type="text" name="lvl_eksternal[]"
                                                                            class="form-control"
                                                                            placeholder="Tulis disini">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Tombol Tambah Penandatangan -->
                                                        <!-- Tombol Tambah Penandatangan -->
                                                        <div class="d-flex justify-content-end">
                                                            <div class="dropdown">
                                                                <button class="btn btn-primary dropdown-toggle"
                                                                    type="button" id="dropdownMenuButton"
                                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <i class="bx bx-user-plus"></i> TAMBAH PENANDATANGAN
                                                                </button>
                                                                <ul class="dropdown-menu"
                                                                    aria-labelledby="dropdownMenuButton">
                                                                    <li><a class="dropdown-item" href="#"><i
                                                                                class="fas fa-user-tie"></i> Tambah
                                                                            Penanggung Jawab Internal</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i
                                                                                class="fas fa-handshake"></i> Tambah
                                                                            Penanggung Jawab Mitra</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- @if (@$stats_kerma == 'Ajuan Baru') --}}
                                        <!-- Penandatangan Kerja Sama -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapsePIC" aria-expanded="true">
                                                    <i class='bx bxs-user-pin me-2'></i> Person In Charge
                                                </button>
                                            </h2>
                                            <div id="collapsePIC" class="accordion-collapse collapse show">
                                                <div class="accordion-body">
                                                    <div class="container mt-4">
                                                        <div class="mb-3 alert alert-danger">
                                                            <div class="card-body">
                                                                    <h5 class="fw-bold text-center w-100">"Isi data dengan benar untuk komunikasi kerja sama."</h5>
                                                            </div>
                                                        </div>
                                                        <!-- Pihak Internal -->
                                                        <div class="card mb-3 pihak-internal-pic">
                                                            <div class="pihak_internal_pic_wrapper">
                                                                <div
                                                                    class="card-header bg-light d-flex align-items-center justify-content-between">
                                                                    <div>
                                                                        <i
                                                                            class="bx bx-user-circle me-2 text-primary fs-4"></i>
                                                                        <strong>Pihak Internal</strong>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Nama
                                                                            <span class="text-danger">*</span></label>
                                                                        <input type="text" name="nama_internal_pic"
                                                                            class="form-control"
                                                                            placeholder="Tulis Nama PIC Internal disini">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Jabatan
                                                                            <span class="text-danger">*</span></label>
                                                                        <input type="text" name="lvl_internal_pic"
                                                                            class="form-control"
                                                                            placeholder="Tulis Jabatan PIC Internal disini">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Email
                                                                            <span class="text-danger">*</span></label>
                                                                        <input type="text" name="email_internal_pic"
                                                                            class="form-control"
                                                                            placeholder="Tulis Email PIC Internal disini">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Telepon
                                                                            <span class="text-danger">*</span></label>
                                                                        <input type="number" name="telp_internal_pic"
                                                                            class="form-control"
                                                                            placeholder="Tulis Telepon PIC Internal  disini">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Pihak Mitra -->
                                                        <div class="card mb-3 pihak-mitra-pic">
                                                            <div class="pihak_mitra_pic_wrapper">
                                                                <div
                                                                    class="card-header bg-light d-flex align-items-center justify-content-between">
                                                                    <div>
                                                                        <i
                                                                            class="bx bx-user-circle me-2 text-primary fs-4"></i>
                                                                        <strong>Pihak Mitra</strong>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Nama
                                                                            <span class="text-danger">*</span></label>
                                                                        <input type="text" name="nama_eksternal_pic"
                                                                            class="form-control"
                                                                            placeholder="Tulis Nama PIC Eksternal disini">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Jabatan
                                                                            <span class="text-danger">*</span></label>
                                                                        <input type="text" name="lvl_eksternal_pic"
                                                                            class="form-control"
                                                                            placeholder="Tulis Jabatan PIC Eksternal disini">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Email
                                                                            <span class="text-danger">*</span></label>
                                                                        <input type="text" name="email_eksternal_pic"
                                                                            class="form-control"
                                                                            placeholder="Tulis Email PIC Eksternal disini">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Telepon
                                                                            <span class="text-danger">*</span></label>
                                                                        <input type="number" name="telp_eksternal_pic"
                                                                            class="form-control"
                                                                            placeholder="Tulis Telepon PIC Eksternal disini">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- <!-- Tombol Tambah Penandatangan -->
                                                            <div class="d-flex justify-content-end">
                                                                <div class="dropdown">
                                                                    <button class="btn btn-primary dropdown-toggle"
                                                                        type="button" id="dropdownMenuButton"
                                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <i class="bx bx-user-plus"></i> TAMBAH
                                                                        PENANDATANGAN
                                                                    </button>
                                                                    <ul class="dropdown-menu"
                                                                        aria-labelledby="dropdownMenuButton">
                                                                        <li><a class="dropdown-item" href="#"><i
                                                                                    class="fas fa-user-tie"></i> Tambah
                                                                                Penanggung Jawab Internal</a></li>
                                                                        <li><a class="dropdown-item" href="#"><i
                                                                                    class="fas fa-handshake"></i> Tambah
                                                                                Penanggung Jawab Mitra</a></li>
                                                                    </ul>
                                                                </div>
                                                            </div> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- @endif --}}

                                        <!-- Bentuk Kerja Sama -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse5" aria-expanded="true">
                                                    <i class="fa-solid fa-handshake me-2"></i> Bentuk Kerja Sama
                                                </button>
                                            </h2>
                                            <div id="collapse5" class="accordion-collapse collapse show">
                                                <div class="accordion-body">
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            <!-- CheckBox -->
                                                            <div class="row mb-3">
                                                                <div class="col-sm-3 ">
                                                                    <label class="text-dark text-sm fw-bold">
                                                                        Bentuk Kerja Sama <span
                                                                            class="text-danger">*</span>
                                                                    </label>
                                                                    <br>
                                                                    <span style="font-size: 12px;">*Arahkan cursor pada
                                                                        nama bentuk kerja sama untuk melihat
                                                                        deskripsi.</span>
                                                                </div>
                                                                <div class="col-sm-9">
                                                                    @foreach ($bentuk_kerjasama as $bentuk)
                                                                        <div class="custom-check checkbox-lg mb-2">
                                                                            <input class="custom-check-input"
                                                                                type="checkbox" name="kontribusi[]"
                                                                                value="{{ $bentuk->nama }}"
                                                                                id="checkbox-{{ $bentuk->id }}" />
                                                                            <label class="custom-check-label text-dark"
                                                                                for="checkbox-{{ $bentuk->id }}">{{ $bentuk->nama }}</label>
                                                                        </div>
                                                                    @endforeach
                                                                    {{-- <div class="custom-check checkbox-lg mb-2">
                                                                        <input class="custom-check-input" type="checkbox"
                                                                            name="kontribusi[]" value="Lain-lain"
                                                                            id="checkbox-lain" />
                                                                        <label class="custom-check-label text-dark"
                                                                            for="checkbox-lain">Lain-lain</label>
                                                                    </div> --}}
                                                                    <div class="kontribusi_lain_wrapper d-none">
                                                                        <input type="text" class="form-control"
                                                                            id="kontribusi_lain"
                                                                            placeholder="Contoh: Penelitian Bersama - Prototipe, Pengembangan Sistem / Produk, Penyaluran Lulusan, Pengembangan Pusat Penelitian dan Pengembangan Keilmuan"
                                                                            name="kontribusi_lain">
                                                                        <label for="kontribusi_lain"
                                                                            style="font-size: 12px;">*Beri tanda koma ","
                                                                            jika lebih dari satu Kerja Sama</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Periode Kerja Sama --}}
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse6" aria-expanded="true">
                                                    <i class="bx bx-calendar me-2"></i> Periode Kerja Sama
                                                </button>
                                            </h2>
                                            <div id="collapse6" class="accordion-collapse collapse show">
                                                <div class="accordion-body">

                                                    <div class="card">
                                                        <div class="card-body">

                                                            @if ($stats_kerma == 'Ajuan Baru')
                                                                <div class="row mb-3 align-items-center">
                                                                    <label class="col-sm-3 text-dark text-sm fw-bold">
                                                                        Status Dokumen <span class="text-danger">*</span>
                                                                    </label>
                                                                    <div class="col-sm-9">
                                                                        <div id="status_dokumen">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="status_kerma" id="ajuan_baru"
                                                                                value="Ajuan Baru">
                                                                            <label class="form-check-label me-2"
                                                                                for="ajuan_baru">Ajuan Baru</label>

                                                                            <input class="form-check-input" type="radio"
                                                                                name="status_kerma" id="dalam_perpanjangan"
                                                                                value="Dalam Perpanjangan">
                                                                            <label class="form-check-label me-2"
                                                                                for="dalam_perpanjangan">Dalam Perpanjangan</label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row mb-3 align-items-start d-none"
                                                                    id="mou_perpanjangan_wrapper">
                                                                    <label class="col-sm-3 text-dark text-sm fw-bold">
                                                                        Pilih Dokumen <span class="text-danger">*</span>
                                                                    </label>
                                                                    <div class="col-sm-9">
                                                                        <select id="mou_perpanjangan" name="mou_perpanjangan"
                                                                            class="form-select select2">
                                                                            <option value="">Pilih Dokumen yang akan diperpanjang</option>
                                                                            @foreach ($dokumen_perpanjang as $dkn)
                                                                            @php
                                                                                $dkn_tanggal = Tanggal_Indo($dkn->mulai) . ($dkn->periode_kerma == 'bydoc' ? ' s/d '. Tanggal_Indo($dkn->selesai) : '');
                                                                            @endphp
                                                                                <option value="{{ $dkn->id_mou }}"
                                                                                    data-nama_institusi="{{ $dkn->nama_institusi }}"
                                                                                    data-jenis_kerjasama="{{ $dkn->jenis_kerjasama }}"
                                                                                    data-jenis_institusi="{{ $dkn->jenis_institusi }}"
                                                                                    data-status_tempat="{{ $dkn->status_tempat }}"
                                                                                    data-periode_kerma="{{ $dkn->periode_kerma }}"
                                                                                    data-status_mou="{{ $dkn->status_mou }}"
                                                                                    data-tanggal="{{$dkn_tanggal}}"
                                                                                    data-kontribusi="{{ $dkn->kontribusi }}">
                                                                                    {{ $dkn->nama_institusi }} |
                                                                                    {{ $dkn->status_tempat }} |
                                                                                    {{ $dkn->jenis_institusi }} |
                                                                                    {{ $dkn->jenis_kerjasama }} |
                                                                                    {{ $dkn->kontribusi }} |
                                                                                    {{ $dkn_tanggal }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        <div class="alert alert-primary mt-2 p-2 d-none"
                                                                            id="fill_mou_perpanjangan_wrapper">
                                                                            <span class="fill_mou_perpanjangan"
                                                                                style="font-size: 12px;">Pilih dokumen
                                                                                MoU yang akan diperpanjang</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            {{-- Periode Kerja Sama --}}
                                                            <div class="row mb-3 align-items-center">
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">
                                                                    Periode Kerja Sama <span class="text-danger">*</span>
                                                                </label>
                                                                <div class="col-sm-9">
                                                                    <div id="periode_kerjasama_wrapper">
                                                                        <div id="periode_kerjasama">
                                                                            <div class="form-check">
                                                                                <input class="form-check-input"
                                                                                    type="radio" name="periode_kerma"
                                                                                    id="bydoc" value="bydoc">
                                                                                <label class="form-check-label"
                                                                                    for="bydoc">Berdasarkan
                                                                                    Tanggal</label>
                                                                            </div>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input"
                                                                                    type="radio" name="periode_kerma"
                                                                                    id="notknown" value="notknown">
                                                                                <label class="form-check-label"
                                                                                    for="notknown">Tidak
                                                                                    Dibatasi</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Tanggal Mulai Kerja Sama -->
                                                            <div class="row mb-3 align-items-center d-none select_periode_kerma_wrapper"
                                                                id="tgl_mulai_wrapper">
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">Tanggal
                                                                    Mulai Kerja Sama <span
                                                                        class="text-danger">*</span></label>
                                                                <div class="col-sm-9">
                                                                    <input type="date" id="tanggal_mulai"
                                                                        name="mulai" class=""
                                                                        placeholder="Pilih Tanggal Mulai">
                                                                </div>
                                                            </div>

                                                            <!-- Tanggal Selesai Kerja Sama -->
                                                            <div class="row mb-3 align-items-center d-none select_periode_kerma_wrapper"
                                                                id="tgl_selesai_wrapper">
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">Tanggal
                                                                    Selesai Kerja Sama<span
                                                                        class="text-danger">*</span></label>
                                                                <div class="col-sm-9">
                                                                    <input type="date" id="tanggal_selesai"
                                                                        name="selesai" class=""
                                                                        placeholder="Pilih Tanggal Selesai">
                                                                </div>
                                                            </div>

                                                            {{-- Status MoU --}}
                                                            <div class="row mb-3 align-items-center d-none select_periode_kerma_wrapper"
                                                                id="status_mou_wrapper">
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">
                                                                    Status Kerja Sama <span class="text-danger">*</span>
                                                                </label>
                                                                <div class="col-sm-9">
                                                                    <select id="status_mou" name="status_mou"
                                                                        class="form-select text-dark text-sm select2 w-100">
                                                                        <option value="Aktif" selected>Aktif</option>
                                                                        <option value="Tidak Aktif">Tidak Aktif</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Upload SK Pendirian Institusi Mitra --}}
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse7" aria-expanded="true">
                                                    <i class="fa-solid fa-upload me-2"></i> Upload SK Pendirian Institusi
                                                    Mitra
                                                </button>
                                            </h2>
                                            <div id="collapse7" class="accordion-collapse collapse show">
                                                <div class="accordion-body">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="row mb-3">
                                                                <label
                                                                    class="col-sm-3 col-form-label fw-bold text-dark">Upload
                                                                    SK Pendirian</label>
                                                                <div class="col-sm-9">
                                                                    <input type="file" name="file_sk_mitra"
                                                                        class="form-control custom-file-input"
                                                                        accept=".pdf">
                                                                    <small class="text-muted"
                                                                        style="font-size: 12px;">Optional (tidak wajib
                                                                        diisi).</small><br>
                                                                    <small class="text-danger"
                                                                        style="font-size: 12px;">*Ukuran Maksimal Unggah
                                                                        File
                                                                        5Mb.</small><br>
                                                                    <small class="text-danger"
                                                                        style="font-size: 12px;">*File Berformat
                                                                        PDF.</small><br>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if (@$stats_kerma == 'Ajuan Baru')
                                            {{-- Upload Upload Draft Dokumen Kerja Sama --}}
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse9"
                                                        aria-expanded="true">
                                                        <i class="bx bx-upload me-2"></i> Upload Draft Dokumen Kerja Sama
                                                    </button>
                                                </h2>
                                                <div id="collapse9" class="accordion-collapse collapse show">
                                                    <div class="accordion-body">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="row mb-3">
                                                                    <label
                                                                        class="col-sm-3 col-form-label fw-bold text-dark">Upload
                                                                        Draft
                                                                        Dokumen Kerja Sama <span
                                                                            style="font-size: 12px;color:red;">(Draft)</span></label>
                                                                    <div class="col-sm-9">
                                                                        <input type="file" name="file_ajuan"
                                                                            class="form-control custom-file-input"
                                                                            accept=".doc,.docx">
                                                                        <small class="text-muted"
                                                                            style="font-size: 12px;">Optional (tidak wajib
                                                                            diisi).</small><br>
                                                                        <small class="text-danger"
                                                                            style="font-size: 12px;">*Ukuran Maksimal
                                                                            Unggah
                                                                            File
                                                                            5Mb.</small><br>
                                                                        <small class="text-danger"
                                                                            style="font-size: 12px;">*File ber Berformat
                                                                            Docx/doc.</small><br>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if (@$stats_kerma == 'Lapor Kerma')
                                            {{-- Upload Upload Dokumen Kerja Sama --}}
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse8"
                                                        aria-expanded="true">
                                                        <i class="bx bx-upload me-2"></i> Upload Dokumen Kerja Sama
                                                    </button>
                                                </h2>
                                                <div id="collapse8" class="accordion-collapse collapse show">
                                                    <div class="accordion-body">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="row mb-3">
                                                                    <label
                                                                        class="col-sm-3 col-form-label fw-bold text-dark">Upload
                                                                        Dokumen Kerja Sama <span class="text-danger">*</span> <span
                                                                            style="font-size: 12px;color:red;">(Sudah di
                                                                            Tanda
                                                                            Tangani)</span></label>
                                                                    <div class="col-sm-9">
                                                                        <input type="file" name="file_mou"
                                                                            class="form-control custom-file-input"
                                                                            accept=".pdf">
                                                                        <small class="text-muted"
                                                                            style="font-size: 12px;">Optional (tidak wajib
                                                                            diisi).</small><br>
                                                                        <small class="text-danger"
                                                                            style="font-size: 12px;">*Ukuran Maksimal
                                                                            Unggah
                                                                            File
                                                                            5Mb.</small><br>
                                                                        <small class="text-danger"
                                                                            style="font-size: 12px;">*File Berformat
                                                                            PDF.</small><br>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="bg-primary p-3 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-light">
                                            <i class="fa fa-save me-2"></i> Simpan
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@push('scripts')
    <script>
        const $prodiUser = @json($prodi_user);
        const $fakultasUser = @json($fak_user);
        let originalMoUOptions = null;

        $(document).ready(function() {
            originalMoUOptions = $("#pilih_mou").html();
            
            const tanggalMulai = flatpickr("#tanggal_mulai", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                locale: "id",
                allowInput: true,
                onChange: function(selectedDates, dateStr, instance) {
                    tanggalSelesai.set('minDate', selectedDates[0]);
                }
            });

            const tanggalSelesai = flatpickr("#tanggal_selesai", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                locale: "id",
                allowInput: true
            });

        });
    </script>
    <script src="{{ asset('js/pengajuan/tambah.js') }}"></script>
@endpush
