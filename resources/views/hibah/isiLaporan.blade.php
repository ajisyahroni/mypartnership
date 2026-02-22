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
                                <div>
                                    @if (@$dataLaporanHibah->id_hibah)
                                        <button class="btn btn-warning btn-export" data-title-tooltip="Print Ajuan Hibah"
                                            data-id_hibah="{{ @$dataLaporanHibah->id_hibah ?? '' }}">
                                            <i class="bx bx-printer"></i> Cetak Laporan
                                        </button>
                                    @endif
                                    <a href="{{ route('hibah.ajuan') }}" class="btn btn-danger">Kembali</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            @if (@$catatanRevisi)
                                <div class="alert alert-danger d-flex align-items-start p-3 position-relative"
                                    role="alert" style="background-color: #f8cdcd; border-left: 6px solid #f33232;">
                                    <div class="me-3 text-white d-flex align-items-center justify-content-center"
                                        style="background-color: #f33232; width: 30px; height: 30px; border-radius: 4px;">
                                        <i class="bx bx-note"></i>
                                    </div>
                                    <div class="text-dark flex-grow-1">
                                        <strong>Catatan Revisi</strong>
                                        <div>{{ @$catatanRevisi }}</div>
                                    </div>

                                    {{-- @if ($status_revisi_laporan == 1)
                                        <button class="btn btn-success btn-sm ms-3"
                                            data-title-tooltip="Revisi sudah diperbaiki" disabled>
                                            <i class="bx bx-check"></i>
                                        </button>
                                    @else
                                        <button id="btnRevisiSelesai" data-field="status_revisi_laporan"
                                            class="btn btn-outline-danger btn-sm ms-3"
                                            data-title-tooltip="Tandai sudah diperbaiki">
                                            <i class="bx bx-check"></i>
                                        </button>
                                    @endif --}}
                                </div>
                            @endif

                            <form action="{{ route('hibah.isiLaporan.store') }}" method="post" id="formInput"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id_hibah" value="{{ @$id_hibah ?? '' }}">
                                <input type="hidden" name="id_laporan_hibah"
                                    value="{{ @$dataLaporanHibah->id_laporan_hibah ?? '' }}">
                                <div class="accordion custom-accordion" id="customAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse2" aria-expanded="true">
                                                FORM LAPORAN HIBAH
                                            </button>
                                        </h2>
                                        <div id="collapse2" class="accordion-collapse collapse show">
                                            <div class="accordion-body">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <!-- Latar Belakang -->
                                                        <div class="row mb-3">
                                                            <label for="detail_aktivitas"
                                                                class="col-sm-3 text-dark text-sm fw-bold">
                                                                Detail Aktivitas Kerja Sama <span
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
                                                                        Jelaskan hal-hal berikut: <br>
                                                                        1. Jenis aktivitas<br>
                                                                        2. Tanggal mulai dan tanggal selesai <br>
                                                                        3. Detail aktivitas<br>
                                                                        4. Tempat<br>
                                                                        5. Peserta (sebutkan nama dan perannya)<br>

                                                                    </div>
                                                                </div>
                                                                <textarea name="detail_aktivitas" id="detail_aktivitas" class="form-control" cols="30" rows="5"
                                                                    placeholder="Masukkan Detail Aktivitas Kerja Sama maksimum 300 kata...">{{ @$dataLaporanHibah->detail_aktivitas ?? '' }}
                                                                </textarea>
                                                                <small class="text-muted d-block mt-2">
                                                                    * Wajib diisi, maksimal 300
                                                                    kata.
                                                                </small>
                                                                <small id="charCountdetail_aktivitas" class="text-muted">0 /
                                                                    200
                                                                    kata</small>
                                                            </div>
                                                        </div>
                                                        <!--  Target Output dan outcome -->
                                                        <div class="row mb-3">
                                                            <label for="target_laporan"
                                                                class="col-sm-3 text-dark text-sm fw-bold">
                                                                Target Output dan Outcome <span
                                                                    class="text-danger">*</span><br>
                                                            </label>
                                                            <div class="col-sm-9">
                                                                <textarea name="target_laporan" id="target_laporan" class="form-control" cols="30" rows="5"
                                                                    placeholder="Masukkan Target Output dan Outcome maksimum 100 kata...">
                                                                    {{ @$dataLaporanHibah->target_laporan ?? '' }}
                                                                </textarea>
                                                                <small class="text-muted d-block mt-2">
                                                                    * Wajib diisi, maksimal 100
                                                                    kata.
                                                                </small>
                                                                <small id="charCounttarget_laporan" class="text-muted">0 /
                                                                    100
                                                                    kata</small>
                                                            </div>
                                                        </div>
                                                        <!-- Hasil dan Dampak Kerja Sama -->
                                                        <div class="row mb-3">
                                                            <label for="Hasil dan Dampak Kerja Sama"
                                                                class="col-sm-3 text-dark text-sm fw-bold">
                                                                Hasil dan Dampak Kerja Sama <span
                                                                    class="text-danger">*</span><br>
                                                            </label>
                                                            <div class="col-sm-9">
                                                                <textarea name="hasil_laporan" id="hasil_laporan" class="form-control" cols="30" rows="5"
                                                                    placeholder="Masukkan Hasil dan Dampak Kerja Sama maksimum 100 kata...">
                                                                    {{ @$dataLaporanHibah->hasil_laporan ?? '' }}
                                                                </textarea>
                                                                <small class="text-muted d-block mt-2">
                                                                    * Wajib diisi, maksimal 100
                                                                    kata.
                                                                </small>
                                                                <small id="charCounthasil_laporan" class="text-muted">0 /
                                                                    100
                                                                    kata</small>
                                                            </div>
                                                        </div>
                                                        <!-- Rencana tindak lanjut kerja sama -->
                                                        <div class="row mb-3">
                                                            <label for="Rencana tindak lanjut kerja sama"
                                                                class="col-sm-3 text-dark text-sm fw-bold">
                                                                Rencana tindak lanjut kerja sama <span
                                                                    class="text-danger">*</span><br>
                                                            </label>
                                                            <div class="col-sm-9">
                                                                <textarea name="rencana_tindak_lanjut" id="rencana_tindak_lanjut" class="form-control" cols="30"
                                                                    rows="5" placeholder="Masukkan Rencana tindak lanjut kerja sama maksimum 100 kata...">
                                                                    {{ @$dataLaporanHibah->rencana_tindak_lanjut ?? '' }}
                                                                </textarea>
                                                                <small class="text-muted d-block mt-2">
                                                                    * Wajib diisi, maksimal 100
                                                                    kata.
                                                                </small>
                                                                <small id="charCountrencana_tindak_lanjut"
                                                                    class="text-muted">0 /
                                                                    100
                                                                    kata</small>
                                                            </div>
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
                                                                Penggunaan anggaran dirinci dengan jelas untuk setiap
                                                                komponen biaya: <br>
                                                                - Transportasi Internasional (Contoh: Pesawat) <br>
                                                                - Akomodasi (Contoh: Penginapan) <br>
                                                                - Pembuatan Visa<br>
                                                                - Transportasi Lokal<br><br>

                                                                Catatan: Anggaran yang diajukan belum pernah dibiayai oleh
                                                                pihak manapun dan pelaporan penggunaannya harus disertai
                                                                dengan bukti dokumen yang relevan dan valid.
                                                                Bukti-bukti pengeluaran yang asli harus diserahkan kepada
                                                                bendahara BKUI.

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
                                                                    <th>Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $jenis = json_decode(
                                                                        @$dataLaporanHibah->jenis_pengeluaran,
                                                                    );
                                                                    $satuan = json_decode(@$dataLaporanHibah->satuan);
                                                                    $jumlah = json_decode(
                                                                        @$dataLaporanHibah->jumlah_pengeluaran,
                                                                    );
                                                                    $biaya_satuan = json_decode(
                                                                        @$dataLaporanHibah->biaya_satuan,
                                                                    );
                                                                    $total = json_decode(
                                                                        @$dataLaporanHibah->biaya_total,
                                                                    );
                                                                @endphp
                                                                @php
                                                                    $totalPengeluaran = 0;
                                                                @endphp

                                                                @if (empty($jenis))
                                                                    <tr>
                                                                        <td>1</td>
                                                                        <td><input type="text" class="form-control" style="background-color:white!important;"
                                                                                name="jenis_pengeluaran[]" placeholder="Contoh: ATK, Transportasi, dll"></td>
                                                                        <td><input type="number" style="background-color:white!important;"
                                                                                class="form-control jumlah" placeholder="Jumlah"
                                                                                name="jumlah_pengeluaran[]"></td>
                                                                        <td><input type="text" style="background-color:white!important;"
                                                                                class="form-control satuan" placeholder="Contoh: pcs, hari, km, dll"
                                                                                name="satuan[]"></td>
                                                                        <td><input type="number" style="background-color:white!important;"
                                                                                class="form-control biaya_satuan" placeholder="Biaya per satuan"
                                                                                name="biaya_satuan[]"></td>
                                                                        <td><input type="number" style="background-color:white!important;"
                                                                                class="form-control biaya_total" placeholder="Total otomatis"
                                                                                name="biaya_total[]"></td>
                                                                        <td><button type="button" class="btn btn-danger"
                                                                                onclick="hapusBaris(this,'tabelPengeluaran')">Hapus</button>
                                                                        </td>
                                                                    </tr>
                                                                @else
                                                                    @foreach ($jenis as $index => $jns)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td><input type="text" class="form-control" style="background-color:white!important;"
                                                                                    name="jenis_pengeluaran[]"
                                                                                    value="{{ $jns }}"></td>
                                                                            <td><input type="number" style="background-color:white!important;"
                                                                                    class="form-control jumlah"
                                                                                    name="jumlah_pengeluaran[]"
                                                                                    value="{{ $jumlah[$index] ?? '' }}">
                                                                            </td>
                                                                            <td><input type="text" class="form-control satuan" style="background-color:white!important;"
                                                                                    name="satuan[]"
                                                                                    value="{{ $satuan[$index] ?? '' }}">
                                                                            </td>
                                                                            <td><input type="number" class="form-control biaya_satuan" style="background-color:white!important;"
                                                                                    name="biaya_satuan[]"
                                                                                    value="{{ $biaya_satuan[$index] ?? '' }}">
                                                                            </td>
                                                                            <td><input type="number" class="form-control biaya_total" style="background-color:white!important;"
                                                                                    name="biaya_total[]"
                                                                                    value="{{ $total[$index] ?? '' }}">
                                                                            </td>
                                                                            <td><button type="button"
                                                                                    class="btn btn-danger"
                                                                                    onclick="hapusBaris(this,'tabelPengeluaran')">Hapus</button>
                                                                            </td>
                                                                        </tr>
                                                                        @php
                                                                            $totalPengeluaran += $total[$index] ?? '';
                                                                        @endphp
                                                                    @endforeach
                                                                @endif


                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <th colspan="5" class="text-end">Total Semua
                                                                        Pengeluaran:</th>
                                                                    <th><span id="totalPengeluaran">{{ @$totalPengeluaran ?? 0 }}</span></th>
                                                                    <th colspan="2"></th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                        <input type="hidden" name="total" id="inputBiaya">
                                                        <button type="button" class="btn btn-tambah btn-primary"
                                                            onclick="tambahBaris('tabelPengeluaran')">+ Tambah
                                                            Pendanaan</button>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse4" aria-expanded="true">
                                                UPLOAD FILE
                                            </button>
                                        </h2>
                                        <div id="collapse4" class="accordion-collapse collapse show">
                                            <div class="accordion-body">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="alert alert-danger d-flex align-items-center p-3"
                                                                    role="alert"
                                                                    style="background-color: #ffe6e6; border-left: 6px solid #d40000;">
                                                                    <div class="me-3 text-white d-flex align-items-center justify-content-center"
                                                                        style="background-color: #d40000; width: 30px; height: 30px; border-radius: 4px;">
                                                                        <i class="bx bx-info-circle"></i>
                                                                    </div>
                                                                    <div class="text-dark small">
                                                                        File yang diupload terdiri dari: <br>
                                                                        1. Dokumen-dokumen pendukung <br>
                                                                        2. Foto-foto atau dokumentasi kegiatan kerja
                                                                        sama<br>
                                                                        3. Bukti pelaporan kegiatan kerja sama di aplikasi
                                                                        Mypartnership<br>
                                                                        4. Bukti transaksi berupa Receipt, Boarding Pass,
                                                                        Invoice, Tanda Terima, dll. Diserahkan ke BKUI<br>
                                                                        5. Laporan-laporan tambahan (jika ada)
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-sm-3">
                                                                <label class="col-form-label fw-bold text-dark">Upload
                                                                    Dokumen Pendukung <span> <span
                                                                            class="text-danger">*</span></label><br>
                                                                @if (count(@$logFilePendukung) > 0)
                                                                    <button type="button"
                                                                        class="btn btn-outline-primary btn-sm btn-detail-log"
                                                                        data-log="{{ json_encode(@$logFilePendukung) }}">Log
                                                                        Draft</button>
                                                                @endif
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="file" name="file_pendukung"
                                                                    class="form-control custom-file-input" accept=".pdf"
                                                                    placeholder="Upload File">
                                                                @if (@$dataLaporanHibah->file_pendukung != null)
                                                                    <small style="font-size: 12px;"><a target="_blank"
                                                                            href="{{ asset('storage/' . @$dataLaporanHibah->file_pendukung) }}">Lihat
                                                                            Dokumen Sebelumnya</a></small><br>
                                                                @endif
                                                                <small class="text-danger"
                                                                    style="font-size: 12px;">*Ukuran
                                                                    Maksimal
                                                                    Unggah
                                                                    File
                                                                    5Mb.</small><br>
                                                                <small class="text-danger" style="font-size: 12px;">*File
                                                                    ber
                                                                    Berformat
                                                                    pdf.</small><br>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-sm-3">
                                                                <label class="col-form-label fw-bold text-dark">Upload
                                                                    Dokumen Bukti Kegiatan <span> <span
                                                                            class="text-danger">*</span></label><br>
                                                                @if (count(@$logFileDokumentasi) > 0)
                                                                    <button type="button"
                                                                        class="btn btn-outline-primary btn-sm btn-detail-log"
                                                                        data-log="{{ json_encode(@$logFileDokumentasi) }}">Log
                                                                        Draft</button>
                                                                @endif
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="file" name="file_dokumentasi"
                                                                    class="form-control custom-file-input" accept=".pdf"
                                                                    placeholder="Upload File">
                                                                @if (@$dataLaporanHibah->file_dokumentasi != null)
                                                                    <small style="font-size: 12px;"><a target="_blank"
                                                                            href="{{ asset('storage/' . @$dataLaporanHibah->file_dokumentasi) }}">Lihat
                                                                            Dokumen Sebelumnya</a></small><br>
                                                                @endif
                                                                <small class="text-danger"
                                                                    style="font-size: 12px;">*Ukuran
                                                                    Maksimal
                                                                    Unggah
                                                                    File
                                                                    5Mb.</small><br>
                                                                <small class="text-danger" style="font-size: 12px;">*File
                                                                    ber
                                                                    Berformat
                                                                    pdf.</small><br>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-sm-3">
                                                                <label class="col-form-label fw-bold text-dark">Upload
                                                                    Pelaporan Kegiatan Kerja Sama di MyPartnership
                                                                    <span> <span class="text-danger">*</span></label><br>
                                                                @if (count(@$logFileLaporanKegiatan) > 0)
                                                                    <button type="button"
                                                                        class="btn btn-outline-primary btn-sm btn-detail-log"
                                                                        data-log="{{ json_encode(@$logFileLaporanKegiatan) }}">Log
                                                                        Draft</button>
                                                                @endif
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="file" name="file_laporan_kegiatan"
                                                                    class="form-control custom-file-input" accept=".pdf"
                                                                    placeholder="Upload File">
                                                                @if (@$dataLaporanHibah->file_laporan_kegiatan != null)
                                                                    <small style="font-size: 12px;"><a target="_blank"
                                                                            href="{{ asset('storage/' . @$dataLaporanHibah->file_laporan_kegiatan) }}">Lihat
                                                                            Dokumen Sebelumnya</a></small><br>
                                                                @endif
                                                                <small class="text-danger"
                                                                    style="font-size: 12px;">*Ukuran
                                                                    Maksimal
                                                                    Unggah
                                                                    File
                                                                    5Mb.</small><br>
                                                                <small class="text-danger" style="font-size: 12px;">*File
                                                                    ber
                                                                    Berformat
                                                                    pdf.</small><br>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-sm-3">
                                                                <label class="col-form-label fw-bold text-dark">Upload
                                                                    Bukti Laporan Keuangan
                                                                    <span> <span class="text-danger">*</span></label><br>
                                                                @if (count(@$logFileLaporanKegiatan) > 0)
                                                                    <button type="button"
                                                                        class="btn btn-outline-primary btn-sm btn-detail-log"
                                                                        data-log="{{ json_encode(@$logFileLaporanKegiatan) }}">Log
                                                                        Draft</button>
                                                                @endif
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="file" name="file_transaksi"
                                                                    class="form-control custom-file-input" accept=".pdf"
                                                                    placeholder="Upload File">
                                                                @if (@$dataLaporanHibah->file_transaksi != null)
                                                                    <small style="font-size: 12px;"><a target="_blank"
                                                                            href="{{ asset('storage/' . @$dataLaporanHibah->file_transaksi) }}">Lihat
                                                                            Dokumen Sebelumnya</a></small><br>
                                                                @endif
                                                                <small class="text-danger"
                                                                    style="font-size: 12px;">*Ukuran
                                                                    Maksimal
                                                                    Unggah
                                                                    File
                                                                    5Mb.</small><br>
                                                                <small class="text-danger" style="font-size: 12px;">*File
                                                                    ber
                                                                    Berformat
                                                                    pdf.</small><br>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            {{-- <label class="col-sm-3 col-form-label fw-bold text-dark">Upload
                                                                File Tambahan <span></label> --}}
                                                            <div class="col-sm-3">
                                                                <label class="col-form-label fw-bold text-dark">Upload
                                                                    File Tambahan
                                                                    <span></label><br>
                                                                @if (count(@$logFileTambahan) > 0)
                                                                    <button type="button"
                                                                        class="btn btn-outline-primary btn-sm btn-detail-log"
                                                                        data-log="{{ json_encode(@$logFileTambahan) }}">Log
                                                                        Draft</button>
                                                                @endif
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="file" name="file_tambahan"
                                                                    class="form-control custom-file-input" accept=".pdf"
                                                                    placeholder="Upload File">
                                                                @if (@$dataLaporanHibah->file_tambahan != null)
                                                                    <small style="font-size: 12px;"><a target="_blank"
                                                                            href="{{ asset('storage/' . @$dataLaporanHibah->file_tambahan) }}">Lihat
                                                                            Dokumen Sebelumnya</a></small><br>
                                                                @endif
                                                                <small class="text-danger"
                                                                    style="font-size: 12px;">*Ukuran
                                                                    Maksimal
                                                                    Unggah
                                                                    File
                                                                    5Mb.</small><br>
                                                                <small class="text-danger" style="font-size: 12px;">*File
                                                                    ber
                                                                    Berformat
                                                                    pdf.</small><br>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="bg-primary p-3 d-flex justify-content-end">
                            @if (@$dataLaporanHibah->id_laporan_hibah != null)
                                <button type="submit" class="btn btn-warning">
                                    <i class="fa fa-save me-2"></i> Simpan Perbaikan
                                </button>
                            @else
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-paper-plane me-2"></i> Submit Laporan
                                </button>
                            @endif
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#btnRevisiSelesai').on('click', function() {
            let btn = $(this);
            btn.prop('disabled', true);
            let field = btn.data('field');
            $.ajax({
                url: "{{ route('hibah.markRevisiDone') }}", // route ke controller untuk update status
                method: 'POST',
                data: {
                    id_hibah: '{{ $id_hibah ?? 0 }}', // sesuaikan jika ada variable id
                    field: field,
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    if (response.success) {
                        btn.removeClass('btn-outline-danger').addClass('btn-success');
                        btn.attr('title', 'Revisi sudah diperbaiki');
                        toastr.success('Status revisi berhasil diperbarui.');
                    } else {
                        toastr.error('Gagal memperbarui status revisi.');
                        btn.prop('disabled', false);
                    }
                },
                error: function() {
                    toastr.error('Terjadi kesalahan saat mengupdate.');
                    btn.prop('disabled', false);
                }
            });
        });
    </script>
    <script>
        const $id_laporan_hibah = @json(@$dataLaporanHibah->id_laporan_hibah);

        $(document).ready(function () {
            hitungTotalKeseluruhan(); 
        });


        function hitungTotalKeseluruhan() {
            let total = 0;

            $('#tabelPengeluaran tbody tr').each(function() {
                let jumlah = parseFloat($(this).find('.jumlah').val()) || 0;
                let biayaSatuan = parseFloat($(this).find('.biaya_satuan').val()) || 0;
                let totalPerBaris = jumlah * biayaSatuan;
                
                if (totalPerBaris > 0) {
                    total += totalPerBaris;
                }
            });
            // Tampilkan total ke UI
            $('#totalPengeluaran').text(total.toLocaleString());

            // Isi input hidden
            $('#inputBiaya').val(total);
        }

        function tambahBaris(Jenistable) {
            const table = document.getElementById(Jenistable).getElementsByTagName('tbody')[0];
            const rowCount = table.rows.length;
            const newRow = table.insertRow();

            if (Jenistable == 'tabelAnggota') {
                newRow.innerHTML = `
                <td>${rowCount + 1}</td>
                <td><input type="text" name="anggota[]"></td>
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
                        <button type="button" class="btn btn-danger" onclick="hapusBaris(this, 'tabelPengeluaran')">Hapus</button>
                    </td>
                `;
            }
        }

        $(document).on('input change', '.jumlah, .biaya_satuan', function() {
            let $row = $(this).closest('tr');
            let jumlah = parseFloat($row.find('.jumlah').val()) || 0;
            let biayaSatuan = parseFloat($row.find('.biaya_satuan').val()) || 0;
            let total = jumlah * biayaSatuan;
            $row.find('.biaya_total').val(total);
            hitungTotalKeseluruhan();
        });

        function hapusBaris(button, Jenistable) {
            const row = button.parentNode.parentNode;
            row.parentNode.removeChild(row);
            hitungTotalKeseluruhan();
            updateNomor(Jenistable);
        }

        function hitungTotalPerBaris($row) {
            let jumlah = parseFloat($row.find('.jumlah').val()) || 0;
            let biayaSatuan = parseFloat($row.find('.biaya_satuan').val()) || 0;
            let total = jumlah * biayaSatuan;
            $row.find('.biaya_total').val(total.toFixed(0));
        }

        function updateNomor(Jenistable) {
            const rows = document.querySelectorAll("#" + Jenistable + " tbody tr");
            rows.forEach((row, index) => {
                row.cells[0].innerText = index + 1;
            });
        }

        $(document).ready(function() {
            // Inisialisasi Summernote
            $('#detail_aktivitas, #target_laporan, #rencana_tindak_lanjut,  #hasil_laporan')
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

            summernoteCount('detail_aktivitas', '300')
            summernoteCount('target_laporan', '100')
            summernoteCount('rencana_tindak_lanjut', '100')
            summernoteCount('hasil_laporan', '100')
            $('#detail_aktivitas').on('summernote.change', function() {
                summernoteCount('detail_aktivitas', '300')
            })
            $('#target_laporan').on('summernote.change', function() {
                summernoteCount('target_laporan', '100')
            })
            $('#rencana_tindak_lanjut').on('summernote.change', function() {
                summernoteCount('rencana_tindak_lanjut', '100')
            })
            $('#hasil_laporan').on('summernote.change', function() {
                summernoteCount('hasil_laporan', '100')
            })

            // $(document).ready(function() {
            //     $(".btn-detail-log").on('click', function() {
            //         const dataLog = $(this).data('log');

            //         $("#modal-detail-log").modal("show");
            //         $("#content-detail-log").html(`
        //             <div id="loading">
        //                 <div class="d-flex justify-content-center my-3">
        //                     <div class="spinner-border text-primary" role="status">
        //                         <span class="visually-hidden">Loading...</span>
        //                     </div>
        //                 </div>
        //             </div>
        //         `);

            //         let logDraft = '';

            //         dataLog.forEach(dt => {

            //             logDraft += `
        //                 <div class="alert d-flex align-items-start p-3 mb-3"
        //                     role="alert"
        //                     style="background-color: #e6faff; border-left: 6px solid #00bcd4;">

        //                     <div class="me-3 text-white d-flex align-items-center justify-content-center"
        //                         style="background-color: #00bcd4; width: 30px; height: 30px; border-radius: 4px;">
        //                         <i class="bx bx-info-circle"></i>
        //                     </div>

        //                     <div class="text-dark small">
        //                         <strong>Diunggah oleh:</strong> ${dt.pengupload}<br>
        //                         <strong>Tanggal:</strong> ${dateFormatter(dt.created_at)}
        //                     </div>
        //                 </div>
        //             `;
            //         });

            //         $("#content-detail-log").html(logDraft);
            //     });
            // });

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

        $(".btn-export").on("click", function() {
            let id = $(this).data("id_hibah");
            window.open("/hibah-kerjasama/export_laporan/" + id, "_blank");
        });
    </script>
    <script src="{{ asset('js/hibah/laporan.js') }}"></script>
@endpush
