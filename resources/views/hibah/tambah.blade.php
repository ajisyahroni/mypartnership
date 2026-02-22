@extends('layouts.app')

@section('contents')
    <style>
        input.flatpickr-input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input.flatpickr-input:focus {
            border-color: #00b4d8;
            box-shadow: 0 0 0 3px rgba(0, 180, 216, 0.2);
            outline: none;
        }

        .text-red-important {
            color: red !important;
        }

        .text-blue-important {
            color: blue !important;
        }

        .text-black {
            color: black;
        }
    </style>
    <div class="page-body">
        <div class="container-fluid">
            <div class="row pt-4">
                <div class="col-sm-12">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <span class="me-2">
                                        <i class="fa-solid fa-folder-open text-warning"></i>
                                    </span>{{ @$page_title }}
                                </h5>
                                <a href="{{ route('hibah.ajuan') }}" class="btn btn-danger">Kembali</a>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            {{-- <form action="{{ route('hibah.store') }}" method="post" id="formInput" --}}
                            <form action="" method="post" id="formInput" enctype="multipart/form-data">
                                @csrf
                                <div class="accordion custom-accordion" id="customAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse1" aria-expanded="true">
                                                FORM AJUAN HIBAH KERJA SAMA
                                            </button>
                                        </h2>
                                        <div id="collapse1" class="accordion-collapse collapse show">
                                            <div class="accordion-body">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row mb-3">
                                                            <label class="col-sm-3 col-form-label fw-bold text-dark">Judul
                                                                Proposal<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="judul_proposal"
                                                                    class="form-control"
                                                                    placeholder="Masukkan Judul Proposal">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label
                                                                class="col-sm-3 col-form-label fw-bold text-dark">Institusi
                                                                Mitra<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="institusi_mitra"
                                                                    class="form-control"
                                                                    placeholder="Masukkan Institusi Mitra">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3 align-items-start">
                                                            <label class="col-sm-3 text-dark text-sm fw-bold">
                                                                Jenis Hibah <span
                                                                    style="font-size: 12px;color:red;">*</span>
                                                            </label>
                                                            <div class="col-sm-9">
                                                                <select id="jenis_hibah" name="jenis_hibah"
                                                                    class="form-control select2">
                                                                    <option value="">Pilih Jenis Hibah</option>
                                                                    @foreach ($jenis_hibah as $jns_hibah)
                                                                        <option value="{{ $jns_hibah->id }}"
                                                                            data-maksimum="{{ rupiah($jns_hibah->maksimum) }}"
                                                                            data-dl_proposal="{{ Tanggal_Indo($jns_hibah->dl_proposal) }}"
                                                                            data-dl_laporan="{{ Tanggal_Indo($jns_hibah->dl_laporan) }}"
                                                                            data-jenis_hibah="{{ $jns_hibah->jenis_hibah }}">
                                                                            {{ $jns_hibah->jenis_hibah }}
                                                                    @endforeach
                                                                </select>
                                                                <div class="alert alert-primary mt-2 p-2 d-none"
                                                                    id="fill_jenis_hibah_wrapper">
                                                                    <span class="fill_jenis_hibah"
                                                                        style="font-size: 12px;">Pilih
                                                                        Jenis Hibah</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3 align-items-start" id="pilih_mou_wrapper">
                                                            <label class="col-sm-3 text-dark text-sm fw-bold">
                                                                Referensi Dokumen Kerja Sama
                                                            </label>
                                                            <div class="col-sm-9">
                                                                <select id="id_mou" name="id_mou" class="form-select">
                                                                    <option value="">Pilih Dokumen Kerja Sama (Masukkan Nama Institusi)</option>
                                                                    @foreach ($jenis_mou as $mou)
                                                                        @php
                                                                            // $mou_tanggal = Tanggal_Indo($mou->mulai) . ($mou->periode_kerma == 'bydoc' ? ' s/d '. Tanggal_Indo($mou->selesai) : '');
                                                                            $mou_tanggal =  ($mou->periode_kerma == 'bydoc' ? Tanggal_Indo($mou->mulai). ' s/d '. Tanggal_Indo($mou->selesai) : ($mou->awal != null && $mou->awal != '0000-00-00' ? Tanggal_Indo($mou->awal) .' s/d tidak ada batasan.' : Tanggal_Indo($mou->mulai) .' s/d tidak ada batasan.')) ;
                                                
                                                                        @endphp
                                                                            <option value="{{ $mou->id_mou }}"
                                                                                data-nama_institusi="{{ $mou->nama_institusi }}"
                                                                                data-jenis_institusi="{{ $mou->jenis_institusi }}"
                                                                                data-jenis_kerjasama="{{ $mou->jenis_kerjasama }}"
                                                                                data-status_tempat="{{ $mou->status_tempat }}"
                                                                                data-periode_kerma="{{ $mou->periode_kerma }}"
                                                                                data-status_mou="{{ $mou->status_mou }}"
                                                                                data-tanggal="{{$mou_tanggal}}"
                                                                                data-kontribusi="{{ $mou->kontribusi }}">
                                                                                {{ $mou->nama_institusi }}|
                                                                                {{ $mou->status_tempat }} |
                                                                                {{ $mou->jenis_kerjasama }} |
                                                                                {{ $mou->jenis_institusi }} |
                                                                                {{ $mou->kontribusi }} |
                                                                                {{ $mou_tanggal }}
                                                                            </option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="alert alert-primary mt-2 p-2 d-none"
                                                                    id="fill_id_mou_wrapper">
                                                                    <span class="fill_id_mou" style="font-size: 12px;">Pilih
                                                                        Dokumen
                                                                        MoU</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label class="col-sm-3 col-form-label fw-bold text-dark">
                                                                Ketua Pelaksana<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="ketua_pelaksana"
                                                                    class="form-control"
                                                                    placeholder="Masukkan Ketua Pelaksana">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label class="col-sm-3 col-form-label fw-bold text-dark">
                                                                NIDN Ketua Pelaksana<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="nidn_ketua_pelaksana"
                                                                    class="form-control"
                                                                    placeholder="Masukkan NIDN Ketua Pelaksana">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label class="col-sm-3 col-form-label fw-bold text-dark">Email
                                                                Ketua Pelaksana<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="email" class="form-control"
                                                                    placeholder="Masukkan Email Ketua Pelaksana">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label class="col-sm-3 col-form-label fw-bold text-dark">Nomor
                                                                HP Ketua Pelaksana<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="number" name="no_hp" class="form-control"
                                                                    placeholder="Masukkan Nomor HP Ketua Pelaksana">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label
                                                                class="col-sm-3 col-form-label fw-bold text-dark">Penanggung
                                                                Jawab
                                                                Kegiatan<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <select name="penanggung_jawab_kegiatan"
                                                                    id="penanggung_jawab_kegiatan"
                                                                    class="form-control select2">
                                                                    <option value="">Pilih Penanggung Jawab Kegiatan
                                                                    </option>
                                                                    @if ($jabatan == 'Kaprodi')
                                                                        <option value="dekan" {{$jabatan == 'Kaprodi' ? "selected":''}}>Dekan</option>
                                                                    @else
                                                                        <option value="kaprodi" {{$jabatan != 'Kaprodi' ? "selected":''}}>Kaprodi</option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label class="col-sm-3 col-form-label fw-bold text-dark">
                                                                Nama Penanggung Jawab<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="nama_penanggung_jawab"
                                                                    class="form-control"
                                                                    placeholder="Masukkan Nama Penanggung Jawab">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label class="col-sm-3 col-form-label fw-bold text-dark">
                                                                NIDN Penanggung Jawab<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="nidn_penanggung_jawab"
                                                                    id="nidn_penanggung_jawab" class="form-control"
                                                                    placeholder="Masukkan NIDN Penanggung Jawab">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label class="col-sm-3 col-form-label fw-bold text-dark">
                                                                Anggota Tim <span
                                                                    style="font-size: 12px; color: red;">*</span>
                                                            </label>
                                                            <div class="col-sm-9">
                                                                <table id="tabelAnggota"
                                                                    class="table table-hover align-middle custom-table">
                                                                    <thead class="table-dark fixed-header">
                                                                        <tr>
                                                                            <th>No</th>
                                                                            <th>Nama</th>
                                                                            <th>Peran</th>
                                                                            <th>Aksi</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>1</td>
                                                                            <td><input type="text" class="form-control"
                                                                                    name="anggota[]">
                                                                            </td>
                                                                            <td><input type="text" class="form-control"
                                                                                    name="peran[]">
                                                                            </td>
                                                                            <td><button type="button"
                                                                                    class="btn btn-danger"
                                                                                    onclick="hapusBaris(this,'tabelAnggota')">Hapus</button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>

                                                                <button type="button" class="btn btn-tambah btn-primary"
                                                                    onclick="tambahBaris('tabelAnggota')">+ Tambah
                                                                    Peserta</button>
                                                            </div>
                                                        </div>

                                                        <!-- Tanggal Mulai Kerja Sama -->
                                                        <div class="row mb-3 align-items-center" id="tgl_mulai_wrapper">
                                                            <label class="col-sm-3 text-dark text-sm fw-bold">Tanggal
                                                                Mulai Pelaksana <span class="text-danger">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="date" id="tanggal_mulai" name="tgl_mulai"
                                                                    placeholder="Pilih Tanggal Mulai">
                                                            </div>
                                                        </div>

                                                        <!-- Tanggal Selesai Pelaksana -->
                                                        <div class="row mb-3 align-items-center" id="tgl_selesai_wrapper">
                                                            <label class="col-sm-3 text-dark text-sm fw-bold">Tanggal
                                                                Selesai Pelaksana</label>
                                                            <div class="col-sm-9">
                                                                <input type="date" id="tanggal_selesai"
                                                                    name="tgl_selesai"
                                                                    placeholder="Pilih Tanggal Selesai">
                                                            </div>
                                                        </div>


                                                        <div class="row mb-3">
                                                            <label
                                                                class="col-sm-3 col-form-label fw-bold text-dark">Fakultas<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <select id="fakultas" name="fakultas"
                                                                    class="form-select select2">
                                                                    <option value="">Pilih Fakultas</option>
                                                                    @foreach ($fakultas as $fak)
                                                                        <option value="{{ $fak->id_lmbg }}"
                                                                            data-nama_lmbg="{{ $fak->nama_lmbg }}">
                                                                            {{ $fak->nama_lmbg }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label
                                                                class="col-sm-3 col-form-label fw-bold text-dark">Program
                                                                Studi<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <select id="prodi" name="prodi"
                                                                    class="form-select select2">
                                                                    <option value="">Pilih Program Studi</option>
                                                                    @foreach ($program_studi as $prodi)
                                                                        <option value="{{ $prodi->id_lmbg }}"
                                                                            data-nama_lmbg="{{ $prodi->nama_lmbg }}"
                                                                            data-place_state="{{ $prodi->place_state }}">
                                                                            {{ $prodi->nama_lmbg }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse2" aria-expanded="true">
                                                DETAIL KEGIATAN
                                            </button>
                                        </h2>
                                        <div id="collapse2" class="accordion-collapse collapse show">
                                            <div class="accordion-body">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <!-- Latar Belakang -->
                                                        <div class="row mb-3">
                                                            <label for="latar_belakang"
                                                                class="col-sm-3 text-dark text-sm fw-bold">
                                                                Latar Belakang <span class="text-danger">*</span><br>
                                                            </label>
                                                            <div class="col-sm-9">
                                                                <textarea name="latar_belakang" id="latar_belakang" class="form-control" cols="30" rows="5"
                                                                    placeholder="Masukkan Latar Belakang maksimum {{ @$settingHibah->latar_belakang_proposal }} kata...">
                                                                </textarea>
                                                                <small class="text-muted d-block mt-2">
                                                                    * Wajib diisi, 
                                                                    minimal {{ @$settingHibah->min_latar_belakang_proposal }} kata dan
                                                                    maksimal {{ @$settingHibah->latar_belakang_proposal }}
                                                                    kata.
                                                                </small>
                                                                <small id="charCountlatar_belakang" class="text-muted">0 /
                                                                    {{ @$settingHibah->latar_belakang_proposal }}
                                                                    kata</small>
                                                            </div>
                                                        </div>
                                                        <!-- Tujuan -->
                                                        <div class="row mb-3">
                                                            <label for="tujuan"
                                                                class="col-sm-3 text-dark text-sm fw-bold">
                                                                Tujuan <span class="text-danger">*</span><br>
                                                            </label>
                                                            <div class="col-sm-9">
                                                                <textarea name="tujuan" id="tujuan" class="form-control" cols="30" rows="5"
                                                                    placeholder="Masukkan Tujuan maksimum {{ @$settingHibah->tujuan_proposal }} kata...">
                                                                </textarea>
                                                                <small class="text-muted d-block mt-2">
                                                                    * Wajib diisi, 
                                                                    minimal {{ @$settingHibah->min_tujuan_proposal }} kata dan
                                                                    maksimal {{ @$settingHibah->tujuan_proposal }}
                                                                    kata.
                                                                </small>
                                                                <small id="charCounttujuan" class="text-muted">0 /
                                                                    {{ @$settingHibah->tujuan_proposal }}
                                                                    kata</small>
                                                            </div>
                                                        </div>
                                                        <!-- Detail Institusi Mitra -->
                                                        <div class="row mb-3">
                                                            <label for="Detail Institusi Mitra"
                                                                class="col-sm-3 text-dark text-sm fw-bold">
                                                                Detail Institusi Mitra <span
                                                                    class="text-danger">*</span><br>
                                                            </label>
                                                            <div class="col-sm-9">
                                                                <div class="alert alert-danger d-flex align-items-center p-3"
                                                                    role="alert"
                                                                    style="background-color: #ffe6e6; border-left: 6px solid #d40000;">
                                                                    <div class="me-3 text-white d-flex align-items-center justify-content-center"
                                                                        style="background-color: #d40000; width: 30px; height: 30px; border-radius: 4px;">
                                                                        <i class="bx bx-info-circle"></i>
                                                                    </div>
                                                                    <div class="text-dark small">
                                                                        Sebutkan institusi internasional yang diusulkan
                                                                        sebagai mitra kerja sama, sertakan alasan mengapa
                                                                        institusi tersebut dipilih.
                                                                    </div>
                                                                </div>
                                                                <textarea name="detail_institusi_mitra" id="detail_institusi_mitra" class="form-control" cols="30"
                                                                    rows="5"
                                                                    placeholder="Masukkan Detail Instruksi Mitra maksimum {{ @$settingHibah->detail_institusi_mitra }} kata...">
                                                                </textarea>
                                                                <small class="text-muted d-block mt-2">
                                                                    * Wajib diisi, 
                                                                    minimal {{ @$settingHibah->min_detail_institusi_mitra }} kata dan
                                                                    maksimal {{ @$settingHibah->detail_institusi_mitra }}
                                                                    kata.
                                                                </small>
                                                                <small id="charCountdetail_institusi_mitra"
                                                                    class="text-muted">0 /
                                                                    {{ @$settingHibah->detail_institusi_mitra }}
                                                                    kata</small>
                                                            </div>
                                                        </div>

                                                        <div class="row mb-3">
                                                            <label class="col-sm-3 col-form-label fw-bold text-dark">Jenis
                                                                Kerja Sama<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <select id="jenis_kerma" name="jenis_kerma"
                                                                    class="form-select select2">
                                                                    <option value="">Pilih Jenis Kerja Sama</option>
                                                                    @foreach ($jenis_kerma as $jenis)
                                                                        <option value="{{ $jenis->nama_dokumen }}"
                                                                            data-alias="{{ $jenis->alias }}"
                                                                            data-lingkup_unit="{{ $jenis->lingkup_unit }}"
                                                                            {{ @$dataPengajuan->jenis_kerjasama == $jenis->nama_dokumen ? 'selected' : '' }}>
                                                                            {{ $jenis->nama_dokumen }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <!-- Detail Kerja Sama -->
                                                        <div class="row mb-3">
                                                            <label for="Detail Kerja Sama"
                                                                class="col-sm-3 text-dark text-sm fw-bold">
                                                                Detail Kerja Sama <span class="text-danger">*</span><br>
                                                            </label>
                                                            <div class="col-sm-9">
                                                                <div class="alert alert-danger d-flex align-items-center p-3"
                                                                    role="alert"
                                                                    style="background-color: #ffe6e6; border-left: 6px solid #d40000;">
                                                                    <div class="me-3 text-white d-flex align-items-center justify-content-center"
                                                                        style="background-color: #d40000; width: 30px; height: 30px; border-radius: 4px;">
                                                                        <i class="bx bx-info-circle"></i>
                                                                    </div>
                                                                    <div class="text-dark small">
                                                                        Jelaskan ke dalam detail kerja sama seperti berikut:
                                                                        <br><br>
                                                                        1. Rencana aktivitas <br>
                                                                        2. Durasi dan jadwal kegiatan <br>
                                                                        3. Tempat<br>
                                                                        4. Peserta (sebutkan nama dan perannya)<br>
                                                                    </div>
                                                                </div>
                                                                <textarea name="detail_kerma" id="detail_kerma" class="form-control" cols="30" rows="5"
                                                                    placeholder="Masukkan Detail Kerja Sama maksimum {{ @$settingHibah->detail_kerma }} kata...">
                                                                </textarea>
                                                                <small class="text-muted d-block mt-2">
                                                                    * Wajib diisi, 
                                                                    minimal {{ @$settingHibah->min_detail_kerma }} kata dan
                                                                    maksimal {{ @$settingHibah->detail_kerma }}
                                                                    kata.
                                                                </small>
                                                                <small id="charCountdetail_kerma" class="text-muted">0 /
                                                                    {{ @$settingHibah->detail_kerma }}
                                                                    kata</small>
                                                            </div>
                                                        </div>

                                                        <!-- Target -->
                                                        <div class="row mb-3">
                                                            <label for="Target"
                                                                class="col-sm-3 text-dark text-sm fw-bold">
                                                                Target Output dan Outcome <span
                                                                    class="text-danger">*</span><br>
                                                            </label>
                                                            <div class="col-sm-9">
                                                                <textarea name="target" id="target" class="form-control" cols="30" rows="5"
                                                                    placeholder="Masukkan Target Output dan Outcome maksimum {{ @$settingHibah->target_proposal }} kata...">
                                                                </textarea>
                                                                <small class="text-muted d-block mt-2">
                                                                    * Wajib diisi, 
                                                                    minimal {{ @$settingHibah->min_target_proposal }} kata dan
                                                                    maksimal {{ @$settingHibah->target_proposal }}
                                                                    kata.
                                                                </small>
                                                                <small id="charCounttarget" class="text-muted">0 /
                                                                    {{ @$settingHibah->target_proposal }}
                                                                    kata</small>
                                                            </div>
                                                        </div>

                                                        <!-- Indikator Keberhasilan -->
                                                        <div class="row mb-3">
                                                            <label for="Indikator Keberhasilan"
                                                                class="col-sm-3 text-dark text-sm fw-bold">
                                                                Indikator Keberhasilan<span
                                                                    class="text-danger">*</span><br>
                                                            </label>
                                                            <div class="col-sm-9">
                                                                <textarea name="indikator_keberhasilan" id="indikator_keberhasilan" class="form-control" cols="30"
                                                                    rows="5"
                                                                    placeholder="Masukkan Indikator Keberhasilan maksimum {{ @$settingHibah->indikator_keberhasilan }} kata...">
                                                                </textarea>
                                                                <small class="text-muted d-block mt-2">
                                                                    * Wajib diisi, 
                                                                    minimal {{ @$settingHibah->min_indikator_keberhasilan }} kata dan
                                                                    maksimal {{ @$settingHibah->indikator_keberhasilan }}
                                                                    kata.
                                                                </small>
                                                                <small id="charCountindikator_keberhasilan"
                                                                    class="text-muted">0 /
                                                                    {{ @$settingHibah->indikator_keberhasilan }}
                                                                    kata</small>
                                                            </div>
                                                        </div>

                                                        <!-- Rencana Keberlanjutan -->
                                                        <div class="row mb-3">
                                                            <label for="Rencana Keberlanjutan"
                                                                class="col-sm-3 text-dark text-sm fw-bold">
                                                                Rencana Keberlanjutan<span class="text-danger">*</span><br>
                                                            </label>
                                                            <div class="col-sm-9">
                                                                <textarea name="rencana" id="rencana" class="form-control" cols="30" rows="5"
                                                                    placeholder="Masukkan Rencana Keberlanjutan maksimum {{ @$settingHibah->rencana_proposal }} kata...">
                                                            </textarea>
                                                                <small class="text-muted d-block mt-2">
                                                                    * Wajib diisi, 
                                                                    minimal {{ @$settingHibah->min_rencana_proposal }} kata dan
                                                                    maksimal {{ @$settingHibah->rencana_proposal }}
                                                                    kata.
                                                                </small>
                                                                <small id="charCountrencana" class="text-muted">0 /
                                                                    {{ @$settingHibah->rencana_proposal }}
                                                                    kata</small>
                                                            </div>
                                                        </div>

                                                        <div class="row mb-3">
                                                            <label class="col-sm-3 col-form-label fw-bold text-dark">Upload
                                                                File Tambahan <span></label>
                                                            <div class="col-sm-9">
                                                                <input type="file" name="file_lain" accept=".pdf"
                                                                    class="form-control custom-file-input"
                                                                    placeholder="Upload File">
                                                                <small class="text-danger"
                                                                    style="font-size: 12px;">*Ukuran
                                                                    Maksimal
                                                                    Unggah
                                                                    File
                                                                    5Mb.</small><br>
                                                                <small class="text-danger" style="font-size: 12px;">*File
                                                                    ber
                                                                    Berformat
                                                                    PDF.</small><br>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <style>
                                            table {
                                                width: 100%;
                                                border-collapse: collapse;
                                                margin-bottom: 1rem;
                                            }

                                            th,
                                            td {
                                                border: 1px solid #000;
                                                padding: 8px;
                                                text-align: left;
                                            }

                                            th {
                                                background-color: #f3f3f3;
                                            }

                                            input[type="text"],
                                            input[type="number"] {
                                                width: 100%;
                                                padding: 6px 8px;
                                                box-sizing: border-box;
                                                border: 1px solid #ccc;
                                                border-radius: 4px;
                                                font-size: 14px;
                                            }

                                            .btn-tambah-pendanaan {
                                                padding: 6px 12px;
                                                background-color: #007bff;
                                                color: #fff;
                                                border: none;
                                                border-radius: 4px;
                                                cursor: pointer;
                                            }

                                            .btn-tambah-pendanaan:hover {
                                                background-color: #0056b3;
                                            }
                                        </style>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse3" aria-expanded="true">
                                                DETAIL PENDANAAN
                                            </button>
                                        </h2>
                                        <div id="collapse3" class="accordion-collapse collapse show">
                                            <div class="accordion-body">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="alert alert-danger d-flex align-items-center p-3"
                                                            role="alert"
                                                            style="background-color: #ffe6e6; border-left: 6px solid #d40000;">
                                                            <div class="me-3 text-white d-flex align-items-center justify-content-center"
                                                                style="background-color: #d40000; width: 30px; height: 30px; border-radius: 4px;">
                                                                <i class="bx bx-info-circle"></i>
                                                            </div>
                                                            <div class="text-dark small">
                                                                Anggaran agar diusulkan sesuai dengan keperluan kegiatan
                                                                dengan tidak melebihi anggaran BKUI sebesar Rp
                                                                {{ number_format(@$settingHibah->pendanaan_bkui, 0, ',', '.') }},-
                                                                per kegiatan. Anggaran dirinci dengan jelas untuk setiap
                                                                komponen biaya: <br><br>
                                                                1. Transportasi Internasional (Contoh: Tiket Pesawat)
                                                                <br>
                                                                2. Akomodasi (Contoh: Penginapan) <br>
                                                                3. Pembuatan Visa<br>
                                                                4. Transportasi Lokal<br>
                                                                <br>
                                                                Catatan: Anggaran yang diajukan belum pernah dibiayai oleh
                                                                pihak manapun dan
                                                                pelaporan penggunaannya harus disertai dengan bukti dokumen
                                                                yang relevan dan valid (Contoh: Receipt, Boarding Pass,
                                                                Invoice, Tanda Terima, dll.).
                                                            </div>
                                                        </div>
                                                        <table id="tabelPengeluaran"
                                                            class="table table-hover align-middle custom-table">
                                                            <thead class="table-dark fixed-header">
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Jenis pengeluaran</th>
                                                                    <th>Jumlah</th>
                                                                    <th>Satuan</th>
                                                                    <th>Biaya satuan</th>
                                                                    <th>Biaya total</th>
                                                                    <th>Sumber pendanaan</th>
                                                                    <th>Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>1</td>
                                                                    <td><input type="text" class="form-control"
                                                                            name="jenis_pengeluaran[]"
                                                                            style="background-color: #ffffff;"
                                                                            placeholder="Contoh: ATK, Transportasi, dll">
                                                                    </td>
                                                                    <td><input type="number" class="form-control jumlah"
                                                                            name="jumlah_pengeluaran[]"
                                                                            style="background-color: #ffffff;"
                                                                            placeholder="Jumlah"></td>
                                                                    <td><input type="text" class="form-control"
                                                                            name="satuan[]"
                                                                            style="background-color: #ffffff;"
                                                                            placeholder="Contoh: pcs, hari, km, dll">
                                                                    </td>
                                                                    <td><input type="number"
                                                                            class="form-control biaya_satuan"
                                                                            name="biaya_satuan[]"
                                                                            style="background-color: #ffffff;"
                                                                            placeholder="Biaya per satuan"></td>
                                                                    <td><input type="number"
                                                                            class="form-control biaya_total"
                                                                            name="biaya_total[]" readonly
                                                                            style="background-color: #ffffff;"
                                                                            placeholder="Total otomatis"></td>
                                                                    <td>
                                                                        <div class="sumber-wrapper">
                                                                            <select class="form-select sumber-pendanaan"
                                                                                style="background-color: #ffffff;"
                                                                                name="sumber_pendanaan[]">
                                                                                <option value="BKUI">BKUI</option>
                                                                                <option value="Lain">Sumber Lain
                                                                                </option>
                                                                            </select>
                                                                            <input type="text"
                                                                                class="form-control mt-2 sumber-lain"
                                                                                placeholder="Tulis sumber lain"
                                                                                name="sumber_pendanaan_lain[]"
                                                                                style="display: none;background-color: #ffffff;">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-danger"
                                                                            onclick="hapusBaris(this, 'tabelPengeluaran')">Hapus</button>
                                                                    </td>
                                                                </tr>

                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <th colspan="5" class="text-end">Total
                                                                        Pendanaan Sumber Lain:</th>
                                                                    <th><span id="totalPendanaanSumberLain">0</span>
                                                                    </th>
                                                                    <th colspan="2"></th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="5" class="text-end">Total
                                                                        Pendanaan BKUI:</th>
                                                                    <th><span id="totalPendanaanBKUI">0</span></th>
                                                                    <th colspan="2"></th>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="5" class="text-end">Total Semua
                                                                        Pengeluaran:</th>
                                                                    <th><span id="totalPengeluaran">0</span></th>
                                                                    <th colspan="2"></th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>

                                                        <input type="hidden" name="hibah_key" value="{{session('hibah_key')}}">
                                                        <input type="hidden" name="biaya" id="inputBiaya">
                                                        <input type="hidden" name="pendanaan_bkui"
                                                            id="inputPendanaanBkui">
                                                        <input type="hidden" name="pendanaan_lain"
                                                            id="inputPendanaanLain">

                                                        <button type="button" class="btn btn-tambah btn-primary"
                                                            onclick="tambahBaris('tabelPengeluaran')">+ Tambah
                                                            Pendanaan</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-primary p-3 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-warning me-2" id="btnDraft"
                                        data-action="{{ route('hibah.store_draft') }}">
                                        <i class="fa fa-save me-2"></i> Simpan Draft
                                    </button>
                                    <button type="submit" class="btn btn-light" id="btnSubmit"
                                        data-action="{{ route('hibah.store') }}">
                                        <i class="fa fa-paper-plane me-2"></i> Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-lihat-file" aria-labelledby="DetailLabel" aria-hidden="true">
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
@endsection

@push('scripts')
    <script>
        const $prodiUser = @json($prodi_user);
        const $fakultasUser = @json($fak_user);

        if ($fakultasUser) {
            $("#fakultas").val($fakultasUser).trigger('change');
        }

        if ($prodiUser) {
            $("#prodi").val($prodiUser).trigger('change');
        }
        // $(document).ready(function(){
        //      $('#pilih_mou_wrapper #id_mou').select2({
        //         theme: "bootstrap-5",
        //         minimumInputLength: 3,
        //         language: {
        //             inputTooShort: function (args) {
        //                 return "Masukkan minimal 3 kata";
        //             },
        //         },
        //     });
        // })
        function hitungTotalKeseluruhan() {
            let total = 0;
            let totalBkui = 0;
            let totalLain = 0;

            $('#tabelPengeluaran tbody tr').each(function() {
                let jumlah = parseFloat($(this).find('.jumlah').val()) || 0;
                let biayaSatuan = parseFloat($(this).find('.biaya_satuan').val()) || 0;
                let totalPerBaris = jumlah * biayaSatuan;
                let sumber = $(this).find('.sumber-pendanaan').val();

                if (totalPerBaris > 0) {
                    total += totalPerBaris;
                    if (sumber === 'BKUI') {
                        totalBkui += totalPerBaris;
                    } else if (sumber === 'Lain') {
                        totalLain += totalPerBaris;
                    }
                }
            });

            // Tampilkan total ke UI
            $('#totalPengeluaran').text(total.toLocaleString());
            $('#totalPendanaanSumberLain').text(totalLain.toLocaleString());
            $('#totalPendanaanBKUI').text(totalBkui.toLocaleString());

            // Isi input hidden
            $('#inputBiaya').val(total);
            $('#inputPendanaanBkui').val(totalBkui);
            $('#inputPendanaanLain').val(totalLain);
        }


        function tambahBaris(Jenistable) {
            const table = document.getElementById(Jenistable).getElementsByTagName('tbody')[0];
            const rowCount = table.rows.length;
            const newRow = table.insertRow();

            if (Jenistable == 'tabelAnggota') {
                newRow.innerHTML = `
                <td>${rowCount + 1}</td>
                <td><input type="text" name="anggota[]" style="background-color: #ffffff;"></td>
                <td><input type="text" name="peran[]" style="background-color: #ffffff;"></td>
                <td><button type="button" class="btn btn-danger" onclick="hapusBaris(this,'tabelAnggota')">Hapus</button></td>
            `;
            } else {
                newRow.innerHTML = `
                    <td>${rowCount + 1}</td>
                    <td><input type="text" class="form-control" style="background-color: #ffffff;" name="jenis_pengeluaran[]" placeholder="Contoh: ATK, Transportasi, dll"></td>
                    <td><input type="number" class="form-control jumlah" name="jumlah_pengeluaran[]" placeholder="Jumlah" style="background-color: #ffffff;"></td>
                    <td><input type="text" class="form-control" name="satuan[]" placeholder="Contoh: pcs, hari, km, dll" style="background-color: #ffffff;"></td>
                    <td><input type="number" class="form-control biaya_satuan" name="biaya_satuan[]" placeholder="Biaya per satuan" style="background-color: #ffffff;"></td>
                    <td><input type="number" class="form-control biaya_total" name="biaya_total[]" readonly placeholder="Total otomatis" style="background-color: #ffffff;"></td>
                    <td>
                        <div class="sumber-wrapper">
                            <select class="form-select sumber-pendanaan" name="sumber_pendanaan[]" style="background-color: #ffffff;">
                                <option value="BKUI">BKUI</option>
                                <option value="Lain">Sumber Lain</option>
                            </select>
                            <input type="text" class="form-control mt-2 sumber-lain" placeholder="Tulis sumber lain" name="sumber_pendanaan_lain[]" style="display: none;background-color: #ffffff;">
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger" onclick="hapusBaris(this, 'tabelPengeluaran')">Hapus</button>
                    </td>
                `;

            }
        }

        function hitungTotalPerBaris($row) {
            let jumlah = parseFloat($row.find('.jumlah').val()) || 0;
            let biayaSatuan = parseFloat($row.find('.biaya_satuan').val()) || 0;
            let total = jumlah * biayaSatuan;
            $row.find('.biaya_total').val(total.toFixed(0));
        }


        $(document).on('input change', '.jumlah, .biaya_satuan, .sumber-pendanaan', function() {
            let $row = $(this).closest('tr');
            let jumlah = parseFloat($row.find('.jumlah').val()) || 0;
            let biayaSatuan = parseFloat($row.find('.biaya_satuan').val()) || 0;
            let total = jumlah * biayaSatuan;
            $row.find('.biaya_total').val(total);

            let pendanaan = $row.find('.sumber-pendanaan').val() || 'BKUI';
            if (pendanaan == 'BKUI') {
                $row.find('.sumber-lain').hide();
            } else {
                $row.find('.sumber-lain').show();
            }

            hitungTotalKeseluruhan();
        });

        function hapusBaris(button, Jenistable) {
            const row = button.parentNode.parentNode;
            row.parentNode.removeChild(row);
            if (Jenistable == 'tabelPengeluaran') {
                hitungTotalKeseluruhan();
            }
            updateNomor(Jenistable);
        }

        function updateNomor(Jenistable) {
            const rows = document.querySelectorAll("#" + Jenistable + " tbody tr");
            rows.forEach((row, index) => {
                row.cells[0].innerText = index + 1;
            });
        }


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

            let allProdiOptions = $('#prodi option').clone();

            function filterProdiByFakultas(fakultasId, selectedProdiId = null) {
                $('#prodi').empty();

                if (!fakultasId) {
                    $('#prodi').append('<option value="">Pilih Fakultas Terlebih Dahulu</option>');
                    $('#prodi').trigger('change.select2');
                    return;
                }

                $('#prodi').append('<option value="">Pilih Program Studi</option>');

                allProdiOptions.each(function() {
                    let placeState = $(this).data('place_state');
                    if (placeState == fakultasId) {
                        $('#prodi').append($(this).clone());
                    }
                });

                // Set prodi yang sudah dipilih (jika ada)
                if (selectedProdiId) {
                    $('#prodi').val(selectedProdiId);
                }

                $('#prodi').trigger('change.select2');
            }

            // Event onchange fakultas
            $('#fakultas').on('change', function() {
                let fakultasId = $(this).val();
                filterProdiByFakultas(fakultasId);
                // Reset prodi selection ketika fakultas berubah
                $('#prodi').val('');
            });

            // Saat page load, cek apakah fakultas dan prodi sudah ada value
            let initialFakultasId = $('#fakultas').val();
            let initialProdiId = $('#prodi').val();

            if (initialFakultasId) {
                filterProdiByFakultas(initialFakultasId, initialProdiId);
            } else {
                // Kalau fakultas kosong, kosongkan prodi juga
                $('#prodi').empty().append('<option value="">Pilih Fakultas Terlebih Dahulu</option>').trigger(
                    'change.select2');
            }

            // Inisialisasi Summernote
            $('#latar_belakang, #tujuan, #detail_institusi_mitra,  #detail_kerma, #target, #indikator_keberhasilan, #rencana')
                .summernote({
                    height: 200,
                    placeholder: 'Tulis di sini...',
                    callbacks: {
                        onImageUpload: function(files) {
                            // uploadImage(files[0]);
                            let editor = $(this); // tangkap elemen summernote pemicu event
                            uploadImage(files[0], editor);
                        }
                    }
                });

            // Fungsi upload gambar ke Summernote
            function uploadImage(file, editor) {
                let data = new FormData();
                data.append("image", file);

                $.ajax({
                    url: '/upload-gambar-summernote', // Ganti ini dengan route Laravel kamu
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // pastikan token tersedia
                    },
                    data: data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(url) {
                        editor.summernote('insertImage', url);
                    },
                    error: function(xhr, status, error) {
                        alert('Upload gagal: ' + error);
                    }
                });
            }

            var countLatarBelakang = "{{ @$settingHibah->latar_belakang_proposal }}"
            var countTujuan = "{{ @$settingHibah->tujuan_proposal }}"
            var countDetailInstitusiMitra = "{{ @$settingHibah->detail_institusi_mitra }}"
            var countDetailKerma = "{{ @$settingHibah->detail_kerma }}"
            var countTarget = "{{ @$settingHibah->target_proposal }}"
            var countIndikatorKeberhasilan = "{{ @$settingHibah->indikator_keberhasilan }}"
            var countRencana = "{{ @$settingHibah->rencana_proposal }}"

            window.hibahSettings = {
                minLatarBelakang: @json(@$settingHibah->min_latar_belakang_proposal ?? 0),
                minTujuan: @json(@$settingHibah->min_tujuan_proposal ?? 0),
                minDetailInstitusiMitra: @json(@$settingHibah->min_detail_institusi_mitra ?? 0),
                minDetailKerma: @json(@$settingHibah->min_detail_kerma ?? 0),
                minTarget: @json(@$settingHibah->min_target_proposal ?? 0),
                minIndikatorKeberhasilan: @json(@$settingHibah->min_indikator_keberhasilan ?? 0),
                minRencana: @json(@$settingHibah->min_rencana_proposal ?? 0),
            };

            $('#latar_belakang').on('summernote.change', function() {
                summernoteCount('latar_belakang', countLatarBelakang)
            })
            $('#tujuan').on('summernote.change', function() {
                summernoteCount('tujuan', countTujuan)
            })
            $('#detail_institusi_mitra').on('summernote.change', function() {
                summernoteCount('detail_institusi_mitra', countDetailInstitusiMitra)
            })
            $('#detail_kerma').on('summernote.change', function() {
                summernoteCount('detail_kerma', countDetailKerma)
            })
            $('#target').on('summernote.change', function() {
                summernoteCount('target', countTarget)
            })
            $('#indikator_keberhasilan').on('summernote.change', function() {
                summernoteCount('indikator_keberhasilan', countIndikatorKeberhasilan)
            })
            $('#rencana').on('summernote.change', function() {
                summernoteCount('rencana', countRencana)
            })

            function summernoteCount(konten, maksimum) {
                const $editor = $('#' + konten);
                const $counter = $('#charCount' + konten);
                const content = $editor.summernote('code');
                const textContent = $('<div>').html(content).text().trim();

                const wordsArray = textContent.split(/\s+/).filter(word => word.length > 0);
                const wordCount = wordsArray.length;

                // Update tampilan jumlah kata
                $counter.text(wordCount + ' / ' + maksimum + ' kata');

                // Jika melebihi batas
                if (wordCount > maksimum) {
                    $counter
                        .removeClass('text-black text-blue-important')
                        .addClass('text-red-important');

                    // Potong dan update konten summernote
                    const limitedText = wordsArray.slice(0, maksimum).join(" ");
                    $editor.summernote('code', limitedText);

                    // Perbarui jumlah kata dan ganti warna ke biru
                    const updatedTextCount = maksimum + ' / maksimum ' + maksimum + ' kata';
                    $counter
                        .text(updatedTextCount)
                        .removeClass('text-red-important text-black')
                        .addClass('text-blue-important');

                    // Notifikasi Swal
                    Swal.fire({
                        icon: "error",
                        title: "Melebihi Maksimal",
                        html: "Jumlah kata melebihi batas maksimal.",
                    });

                } else {
                    $counter
                        .removeClass('text-red-important text-blue-important')
                        .addClass('text-black');
                }
            }


        });
    </script>
    <script src="{{ asset('js/hibah/tambah.js') }}"></script>
@endpush
