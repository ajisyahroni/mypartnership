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
                                    <input type="hidden" name="id_mou" id="id_mou" value="{{ @$id_mou }}">
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
                                        <!-- TimeLine -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseTimeline" aria-expanded="true">
                                                    <i class="fa-solid fa-timeline me-2"></i> Timeline Kerja Sama
                                                </button>
                                            </h2>
                                            <div id="collapseTimeline" class="accordion-collapse collapse show">
                                                <div class="accordion-body">
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            <div class="row mb-3 align-items-center">
                                                                <div class="col-12 d-flex justify-content-center">
                                                                    @include(
                                                                        'pengajuan.timeline',
                                                                        compact('dataPengajuan'))
                                                                </div>
                                                                <div class="col-12 d-flex justify-content-center mt-3">
                                                                    @if (@$stats_kerma == 'Lapor Kerma')
                                                                        <a href="#draftDokumen" class="btn btn-primary"> <i
                                                                                class="fa-solid fa-upload me-2">
                                                                            </i> Upload Draft Dokumen</a>
                                                                    @elseif (
                                                                        @$stats_kerma == 'Ajuan Baru' &&
                                                                            $dataPengajuan->tgl_verifikasi_user != '0000-00-00 00:00:00' &&
                                                                            $dataPengajuan->tgl_req_ttd == '0000-00-00 00:00:00')
                                                                        <a href="#draftDokumen" class="btn btn-primary"> <i
                                                                                class="fa-solid fa-upload me-2">
                                                                            </i> Upload Dokumen Resmi Kerja Sama</a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

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
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">Jenis
                                                                    Dokumen Kerja Sama <span class="text-danger">*</span>
                                                                </label>
                                                                <div class="col-sm-9">
                                                                    <select id="jenis_kerjasama" name="jenis_kerjasama"
                                                                        class="form-select text-dark text-sm select2 w-100">
                                                                        <option value="">Pilih Jenis Dokumen</option>
                                                                        @foreach ($jenis_dokumen as $dokumen)
                                                                            <option value="{{ $dokumen->nama_dokumen }}"
                                                                                data-alias="{{ $dokumen->alias }}"
                                                                                data-lingkup_unit="{{ $dokumen->lingkup_unit }}"
                                                                                {{ @$dataPengajuan->jenis_kerjasama == $dokumen->nama_dokumen ? 'selected' : '' }}>
                                                                                {{ $dokumen->nama_dokumen }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="row mb-3 d-none" id="pilih_mou_wrapper">
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">
                                                                    Pilih Jenis MoU <span class="text-danger">*</span>
                                                                </label>
                                                                <div class="col-sm-9">
                                                                    <select id="pilih_mou" name="pilih_mou"
                                                                        class="form-select">
                                                                        <option value="">Pilih Jenis MoU (Masukkan
                                                                            Nama Institusi)</option>
                                                                        @foreach ($jenis_mou as $mou)
                                                                            <option value="{{ $mou->id_mou }}"
                                                                                data-nama_institusi="{{ $mou->nama_institusi }}"
                                                                                data-jenis_institusi="{{ $mou->jenis_institusi }}"
                                                                                data-tanggal="{{ $mou->mulai }} - {{ $mou->selesai }}"
                                                                                data-kontribusi="{{ $mou->kontribusi }}">
                                                                                {{ $mou->nama_institusi }} |
                                                                                {{ $mou->jenis_institusi }}
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
                                                                                    class="form-check d-none tingkat-{{ preg_replace('/[^a-zA-Z0-9]/', '', $tingkat->nama) }}  {{ $tingkat->check }}">
                                                                                    <input class="form-check-input"
                                                                                        type="radio" name="prodi_unit"
                                                                                        {{ old('prodi_unit', $dataPengajuan->prodi_unit ?? '') == $tingkat->nama ? 'checked' : '' }}
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
                                                                                {{ @$dataPengajuan->id_lembaga == $fak->id_lmbg ? 'selected' : '' }}>
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
                                                                                {{ @$dataPengajuan->id_lembaga == $studi->id_lmbg ? 'selected' : '' }}>
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
                                                                            <option value="{{ $u->id_lmbg }}"
                                                                                {{ @$dataPengajuan->id_lembaga == $u->id_lmbg ? 'selected' : '' }}>
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
                                                                    Institusi Mitra</label>
                                                                <div class="col-sm-9">
                                                                    <select id="select_institusi_mitra"
                                                                        name="jenis_institusi"
                                                                        class="form-select text-dark text-sm select2">
                                                                        <option data-alias="" value="">Pilih Jenis
                                                                            Institusi Mitra </option>
                                                                        @foreach ($jenis_institusi_mitra as $mitra)
                                                                            <option data-alias="{{ $mitra->alias }}"
                                                                                value="{{ $mitra->klasifikasi }}"
                                                                                {{ @$dataPengajuan->jenis_institusi == $mitra->klasifikasi ? 'selected' : '' }}>
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
                                                                                <optgroup label="{{ $group->group }}">
                                                                                    @foreach ($group->getRangking as $rangking)
                                                                                        <option
                                                                                            value="{{ $rangking->nama }}"
                                                                                            {{ @$dataPengajuan->rangking_univ == $rangking->nama ? 'selected' : '' }}>
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
                                                                            value="{{ @$dataPengajuan->nama_fk_mitra ?? @$dataPengajuan->nama_fk_mitra }}"
                                                                            placeholder="Masukkan Nama Fakultas Mitra">
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3 align-items-center">
                                                                    <label
                                                                        class="col-sm-3 text-dark text-sm fw-bold">Prodi/Department/Jurusan</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="text" name="nama_dept_mitra"
                                                                            class="form-control"
                                                                            value="{{ @$dataPengajuan->nama_dept_mitra ?? @$dataPengajuan->nama_dept_mitra }}"
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
                                                                        value="{{ @$dataPengajuan->jenis_institusi_lain ?? @$dataPengajuan->jenis_institusi_lain }}"
                                                                        placeholder="Masukkan Nama Institusi Mitra">
                                                                </div>
                                                            </div>

                                                            <!-- Nama Institusi Mitra -->
                                                            <div class="row mb-3 align-items-center">
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">Nama
                                                                    Institusi Mitra</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="nama_institusi"
                                                                        class="form-control"
                                                                        value="{{ @$dataPengajuan->nama_institusi ?? @$dataPengajuan->nama_institusi }}"
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
                                                                                value="Dalam Negeri"
                                                                                {{ old('dn_ln', $dataPengajuan->dn_ln ?? '') == 'Dalam Negeri' ? 'checked' : '' }}>
                                                                            <label class="form-check-label me-2"
                                                                                for="dalam_negeri">Dalam Negeri</label>

                                                                            <input class="form-check-input" type="radio"
                                                                                name="dn_ln" id="luar_negeri"
                                                                                value="Luar Negeri"
                                                                                {{ old('dn_ln', $dataPengajuan->dn_ln ?? '') == 'Luar Negeri' ? 'checked' : '' }}>
                                                                            <label class="form-check-label me-2"
                                                                                for="luar_negeri">Luar Negeri</label>
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
                                                                            {{ old('wilayah_mitra', $dataPengajuan->wilayah_mitra ?? '') == 'Nasional' ? 'checked' : '' }}
                                                                            value="Nasional">
                                                                        <label class="form-check-label me-2"
                                                                            for="nasional">Nasional</label>
                                                                        <input class="form-check-input" type="radio"
                                                                            name="wilayah_mitra" id="lokal"
                                                                            {{ old('wilayah_mitra', $dataPengajuan->wilayah_mitra ?? '') == 'Lokal' ? 'checked' : '' }}
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
                                                                            <option value="{{ $negara->name }}"
                                                                                {{ @$dataPengajuan->negara_mitra == $negara->name ? 'selected' : '' }}>
                                                                                {{ $negara->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <!-- Alamat Institusi Mitra -->
                                                            <div class="row mb-3 align-items-center">
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">Alamat
                                                                    Institusi
                                                                    Mitra <span class="text-danger">*</span></label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="alamat_mitra"
                                                                        class="form-control"
                                                                        value="{{ @$dataPengajuan->alamat_mitra ?? @$dataPengajuan->alamat_mitra }}"
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
                                                            {{-- <div class="pihak_internal_wrapper">
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
                                                            </div> --}}
                                                        </div>

                                                        <!-- Pihak Mitra -->
                                                        <div class="card mb-3 pihak-mitra">
                                                            {{-- <div class="pihak_mitra_wrapper">
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
                                                            </div> --}}
                                                        </div>

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
                                                                                {{ in_array($bentuk->nama, explode(',', $dataPengajuan->kontribusi)) ? 'checked' : '' }}
                                                                                id="checkbox-{{ $bentuk->id }}" />
                                                                            <label class="custom-check-label text-dark"
                                                                                for="checkbox-{{ $bentuk->id }}">{{ $bentuk->nama }}</label>
                                                                        </div>
                                                                    @endforeach
                                                                    {{-- <div class="custom-check checkbox-lg mb-2">
                                                                        <input class="custom-check-input" type="checkbox"
                                                                            name="kontribusi[]" value="Lain-lain"
                                                                            {{ in_array('Lain-lain', explode(',', $dataPengajuan->kontribusi)) ? 'checked' : '' }}
                                                                            id="checkbox-lain" />
                                                                        <label class="custom-check-label text-dark"
                                                                            for="checkbox-lain">Lain-lain</label>
                                                                    </div> --}}
                                                                    <div class="kontribusi_lain_wrapper d-none">
                                                                        <input type="text" class="form-control"
                                                                            id="kontribusi_lain"
                                                                            value="{{ @$dataPengajuan->kontribusi_lain ?? @$dataPengajuan->kontribusi_lain }}"
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
                                                                        name="mulai" class="form-control"
                                                                        placeholder="Pilih Tanggal Mulai"
                                                                        value="{{ @$dataPengajuan->mulai }}">
                                                                </div>
                                                            </div>

                                                            <!-- Tanggal Selesai Kerja Sama -->
                                                            <div class="row mb-3 align-items-center d-none select_periode_kerma_wrapper"
                                                                id="tgl_selesai_wrapper">
                                                                <label class="col-sm-3 text-dark text-sm fw-bold">Tanggal
                                                                    Selesai Kerja Sama <span
                                                                        class="text-danger">*</span></label>
                                                                <div class="col-sm-9">
                                                                    <input type="date" id="tanggal_selesai"
                                                                        name="selesai" class="form-control"
                                                                        placeholder="Pilih Tanggal Selesai"
                                                                        value="{{ @$dataPengajuan->selesai }}">
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
                                                                        <option value="Aktif">Aktif</option>
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
                                                                    @if (@$dataPengajuan->file_sk_mitra != null)
                                                                        <small style="font-size: 12px;"><a target="_blank"
                                                                                href="{{ asset('storage/' . @$dataPengajuan->file_sk_mitra) }}">Lihat
                                                                                Dokumen Sebelumnya</a></small><br>
                                                                    @endif
                                                                    <div>
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
                                        </div>

                                        @if (@$stats_kerma == 'Ajuan Baru')
                                            {{-- Upload Upload Dokumen Draft MOU Kerja Sama --}}
                                            <div class="accordion-item" id="draftDokumen">
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
                                                                        @if (@$dataPengajuan->file_ajuan != null)
                                                                            <small style="font-size: 12px;"><a
                                                                                    target="_blank" {{-- href="{{ asset('storage/' . @$dataPengajuan->file_ajuan) }}">Lihat --}}
                                                                                    href="{{ getDocumentUrl(@$dataPengajuan->file_ajuan, 'file_ajuan') }}">Lihat
                                                                                    Dokumen Sebelumnya</a></small><br>
                                                                        @endif
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
                                                                            Docx / Doc.</small><br>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        {{-- Upload Upload Dokumen Kerja Sama --}}
                                        @if (
                                            @$stats_kerma == 'Lapor Kerma' ||
                                                (@$stats_kerma == 'Ajuan Baru' &&
                                                    @$dataPengajuan->tgl_verifikasi_kabid != '0000-00-00 00:00:00' &&
                                                    @$dataPengajuan->tgl_verifikasi_user != '0000-00-00 00:00:00'))
                                            <div class="accordion-item" id="draftDokumen">
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
                                                                        Dokumen Kerja Sama <span
                                                                            style="font-size: 12px;color:red;">(Sudah
                                                                            Bertanda
                                                                            Tangan)</span></label>
                                                                    <div class="col-sm-9">
                                                                        <input type="file" name="file_mou"
                                                                            class="form-control custom-file-input"
                                                                            accept=".pdf">
                                                                        @if (@$dataPengajuan->file_mou != null)
                                                                            <small style="font-size: 12px;"><a
                                                                                    target="_blank" {{-- href="{{ asset('storage/' . @$dataPengajuan->file_mou) }}">Lihat --}}
                                                                                    href="{{ getDocumentUrl(@$dataPengajuan->file_mou, 'file_mou') }}">Lihat
                                                                                    Dokumen Sebelumnya</a></small><br>
                                                                        @endif
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
        $(document).ready(function() {
            const tanggalMulai = flatpickr("#tanggal_mulai", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                locale: "id",
                allowInput: true,
                onChange: function(selectedDates, dateStr, instance) {
                    // Atur minDate tanggal selesai berdasarkan tanggal mulai
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
    <script src="{{ asset('js/pengajuan/edit.js') }}"></script>
    <script>
        $(document).ready(function() {
            let jenisKerjaSama = '{{ @$dataPengajuan->jenis_kerjasama }}';
            let dokumenMoU = '{{ @$dataPengajuan->dokumen_mou }}';

            let prodiUnit = '{{ @$dataPengajuan->prodi_unit }}';
            let IdLembaga = '{{ @$dataPengajuan->id_lembaga }}';
            let jenisInstitusi = '{{ @$dataPengajuan->jenis_institusi }}';
            let jenisPerusahaan = '{{ @$dataPengajuan->jenis_perusahaan }}';
            let aliasJenisInstitusi = '{{ @$dataPengajuan->getJenisInstitusi->alias }}';
            let rangkingUniv = '{{ @$dataPengajuan->rangking_univ }}';

            let namaFKMitra = '{{ @$dataPengajuan->nama_fk_mitra }}';
            let namaDeptMitra = '{{ @$dataPengajuan->nama_dept_mitra }}';
            let jenisInstitusiLain = '{{ @$dataPengajuan->jenis_institusi_lain }}';
            let kontribusiLain = `{{ in_array('Lain-lain', explode(',', $dataPengajuan->kontribusi)) }}`;

            let periode_kerma = '{{ @$dataPengajuan->periode_kerma }}';
            let mulai = '{{ @$dataPengajuan->mulai }}';
            let selesai = '{{ @$dataPengajuan->selesai }}';
            let status_mou = '{{ @$dataPengajuan->status_mou }}';

            let ttdInternal = `{{ $dataPengajuan->ttd_internal ?? '' }}`.split(';');
            let lvlInternal = `{{ $dataPengajuan->lvl_internal ?? '' }}`.split(';');
            let ttdEksternal = `{{ $dataPengajuan->ttd_eksternal ?? '' }}`.split(';');
            let lvlEksternal = `{{ $dataPengajuan->lvl_eksternal ?? '' }}`.split(';');

            function addInternalCard(nama = "", jabatan = "") {
                let internalTemplate = `
            <div class="pihak_internal_wrapper">
                <div class="card-header bg-light d-flex align-items-center justify-content-between">
                    <div>
                        <i class="bx bx-user-circle me-2 text-primary fs-4"></i>
                        <strong>Pihak Internal</strong>
                    </div>
                    <button class="btn btn-danger btn-sm hapus_internal" type="button">X Hapus</button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" name="ttd_internal[]" class="form-control" placeholder="Tulis disini" value="${nama}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jabatan Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" name="lvl_internal[]" class="form-control" placeholder="Tulis disini" value="${jabatan}">
                    </div>
                </div>
            </div>`;
                $(".pihak-internal").append(internalTemplate);
            }

            function addEksternalCard(nama = "", jabatan = "") {
                let mitraTemplate = `
            <div class="pihak_mitra_wrapper">
                <div class="card-header bg-light d-flex align-items-center justify-content-between">
                    <div>
                        <i class="bx bx-user-circle me-2 text-primary fs-4"></i>
                        <strong>Pihak Mitra</strong>
                    </div>
                    <button class="btn btn-danger btn-sm hapus_eksternal" type="button">X Hapus</button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" name="ttd_eksternal[]" class="form-control" placeholder="Tulis disini" value="${nama}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jabatan Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" name="lvl_eksternal[]" class="form-control" placeholder="Tulis disini" value="${jabatan}">
                    </div>
                </div>
            </div>`;
                $(".pihak-mitra").append(mitraTemplate);
            }

            // Tambahkan card internal sesuai jumlah data
            if (ttdInternal.length > 0 && ttdInternal[0] !== "") {
                ttdInternal.forEach((nama, index) => {
                    addInternalCard(nama, lvlInternal[index] || "");
                });
            }

            // Tambahkan card eksternal sesuai jumlah data
            if (ttdEksternal.length > 0 && ttdEksternal[0] !== "") {
                ttdEksternal.forEach((nama, index) => {
                    addEksternalCard(nama, lvlEksternal[index] || "");
                });
            }


            if (jenisKerjaSama) {
                $("#jenis_kerjasama").val(jenisKerjaSama).trigger("change");
            }

            if (periode_kerma) {
                $('input[name="periode_kerma"][value="' + periode_kerma + '"]').prop("checked", true).trigger(
                    'change')
            }

            if (mulai) {
                $("#tanggal_mulai").val(mulai).trigger("change");
            }

            if (selesai) {
                $("#tanggal_selesai").val(selesai).trigger("change");
            }

            if (status_mou) {
                $("#status_mou").val(status_mou).trigger("change");
            }

            if (dokumenMoU) {
                $("#pilih_mou").val(dokumenMoU).trigger("change");
            }

            if (prodiUnit) {
                $('input[name="prodi_unit"][value="' + prodiUnit + '"]').prop("checked", true).trigger('change')
                if (prodiUnit == 'Fakultas') {
                    $("#select_fakultas").val(IdLembaga).trigger('change')
                } else if (prodiUnit == 'Program Studi') {
                    $("#select_prodi").val(IdLembaga).trigger('change')
                } else if (prodiUnit == 'Unit') {
                    $("#select_unit").val(IdLembaga).trigger('change')
                }
            }
            if (jenisInstitusi) {
                $("#select_institusi_mitra").val(jenisInstitusi).trigger("change");
            }

            if (aliasJenisInstitusi == 'universitas') {
                $("#select_rangking_universitas").val(rangkingUniv).trigger('change')
                $("input[name='nama_fk_mitra']").val(namaFKMitra)
                $("input[name='nama_dept_mitra']").val(namaDeptMitra)
            } else if (aliasJenisInstitusi == 'perusahaan') {
                $("#select_perusahaan").val(jenisPerusahaan).trigger('change')
            } else if (aliasJenisInstitusi == 'Lain-lain') {
                $("input[name='jenis_institusi_lain']").val(jenisInstitusiLain)
            }

            if (kontribusiLain) {
                $(".kontribusi_lain_wrapper").removeClass("d-none").fadeIn();
            }
        });
    </script>
@endpush
