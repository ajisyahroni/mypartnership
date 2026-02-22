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
                            <a href="{{ route('implementasi.home') }}" class="btn btn-danger btn-sm shadow-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Tempat untuk konten tambahan -->
                        <div class="content">
                            <div class="container mt-4">
                                <form action="{{ route('implementasi.store') }}" enctype="multipart/form-data"
                                    method="post" id="formInput">
                                    @csrf
                                    <input type="hidden" name="implementasi_key" value="{{ session('implementasi_key') }}">
                                    <div class="row mb-4 align-items-center">
                                        <div class="col-12 col-sm-3">
                                            <label class="form-label fw-bold text-dark">Pelaksana <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-12 col-sm-9 d-flex flex-wrap">
                                            @foreach ($tingkat_kerjasama as $tingkat)
                                                <div
                                                    class="form-check me-3 mb-2 tingkat-{{ preg_replace('/[^a-zA-Z0-9]/', '', $tingkat->nama) }} {{ $tingkat->check }}">
                                                    <input class="form-check-input" type="radio"
                                                        name="pelaksana_prodi_unit" id="{{ $tingkat->label }}"
                                                        value="{{ $tingkat->nama }}">
                                                    <label class="form-check-label text-dark"
                                                        for="{{ $tingkat->label }}">{{ $tingkat->nama }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Dropdown Fakultas, Prodi, Unit -->
                                    <div class="row mb-4 align-items-center d-none dropdown-wrapper dropdown-prodi_unit"
                                        id="select_fakultas_wrapper">
                                        <label for="select_fakultas" class="col-sm-3 text-dark text-sm fw-bold">Pelaksana
                                            Kegiatan<span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <select id="select_fakultas" name="lvl_fak"
                                                class="form-select text-dark text-sm select2">
                                                <option value="">Pilih Fakultas</option>
                                                @foreach ($fakultas as $fak)
                                                    <option value="{{ $fak->id_lmbg }}">
                                                        {{ $fak->nama_lmbg }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-4 align-items-center d-none dropdown-wrapper dropdown-prodi_unit"
                                        id="select_prodi_wrapper">
                                        <label for="select_prodi" class="col-sm-3 text-dark text-sm fw-bold">Pelaksana
                                            Kegiatan<span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <select id="select_prodi" name="lvl_prodi"
                                                class="form-select text-dark text-sm select2">
                                                <option value="">Pilih Program Studi</option>
                                                @foreach ($program_studi as $studi)
                                                    <option value="{{ $studi->id_lmbg }}">
                                                        {{ $studi->nama_lmbg }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-4 align-items-center d-none dropdown-wrapper dropdown-prodi_unit"
                                        id="select_unit_wrapper">
                                        <label for="select_unit" class="col-sm-3 text-dark text-sm fw-bold">Pelaksana
                                            Kegiatan<span class="text-danger">*</span></label>
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

                                    <!-- Judul Kerja Sama -->
                                    <div class="row mb-4">
                                        <label class="col-sm-3 text-dark text-sm fw-bold">Judul Kegiatan<span
                                                class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="judul" class="form-control"
                                                placeholder="Masukkan Judul Kegiatan">
                                            <div class="alert alert-primary mt-2">
                                                <span class="text-muted" style="font-size:12px;"><b>Contoh judul
                                                        kegiatan:</b><br>
                                                    1. Seminar Nasional Makanan Bergizi <br>
                                                    2. Outbound Student Mobility ke Dong A University South Korea<br>
                                                    3. Research Matching Fund dengan University of Nottingham
                                                    Malaysia</span>
                                            </div>
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
                                            <input type="date" id="tanggal_selesai" name="tgl_selesai"
                                                placeholder="Pilih Tanggal Selesai">
                                        </div>
                                    </div>

                                    <!-- Bentuk Kerja Sama -->
                                    <div class="row mb-4">
                                        <label class="col-sm-3 text-dark text-sm fw-bold">Bentuk Kerja Sama<span
                                                class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            @foreach ($bentuk_kerjasama as $bentuk)
                                                <div class="custom-check checkbox-lg mb-2">
                                                    <input class="custom-check-input" type="checkbox"
                                                        name="bentuk_kegiatan[]" value="{{ $bentuk->nama }}"
                                                        id="checkbox-{{ $bentuk->id }}" />
                                                    <label class="custom-check-label text-dark"
                                                        for="checkbox-{{ $bentuk->id }}">{{ $bentuk->nama }}</label>
                                                </div>
                                            @endforeach
                                            <div class="bentuk_kegiatan_wrapper d-none">
                                                <input type="text" class="form-control" id="bentuk_kegiatan"
                                                    placeholder="Contoh: Penelitian Bersama - Prototipe, Pengembangan Sistem / Produk, Penyaluran Lulusan, Pengembangan Pusat Penelitian dan Pengembangan Keilmuan"
                                                    name="bentuk_kegiatan_lain">
                                                <label for="bentuk_kegiatan" style="font-size: 12px;">*Beri tanda koma ","
                                                    jika lebih dari satu Kerja Sama</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4 align-items-start" id="pilih_mou_wrapper">
                                        <label class="col-sm-3 text-dark text-sm fw-bold">
                                            Pilih Dokumen Kerja Sama<span
                                                class="text-danger">*</span>
                                        </label>
                                        <div class="col-sm-9">
                                            <select id="id_mou" name="id_mou" class="form-select">
                                                <option value="">Pilih Dokumen</option>
                                                @foreach ($jenis_mou as $mou)
                                                @php
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
                                            <div class="alert alert-primary mt-2 p-2 d-none" id="fill_id_mou_wrapper">
                                                <span class="fill_id_mou" style="font-size: 12px;">Pilih Dokumen
                                                    MoU</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-sm-3 text-dark text-sm fw-bold">
                                            Kategori <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-sm-9">
                                            @foreach ($kategori as $ktg)
                                                <div class="form-check mb-2 ">
                                                    <input class="form-check-input" type="radio" name="category"
                                                        id="{{ $ktg->id }}" value="{{ $ktg->kategori }}">
                                                    <label class="form-check-label text-dark"
                                                        for="{{ $ktg->id }}">{{ $ktg->kategori }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="card mb-4 alert alert-primary">
                                        <div class="card-body p-1">
                                            <span class="font-weight-semibold" style="font-size:12px;">
                                                <div><b>Bukti Pelaksanaan/ Implementasi Kerja Sama dapat berbentuk:</b>
                                                    <br>
                                                </div>
                                                <ol id="two-columns">
                                                    <li>Proposal Kerja Sama</li>
                                                    <li>Kurikulum</li>
                                                    <li>Contoh Ijazah, Sertifikat atau Transkip Nilai</li>
                                                    <li>Publikasi Ilmiah</li>
                                                    <li>Surat Tugas</li>
                                                    <li>Kontrak Hibah Penelitian/ Pengabdian</li>
                                                    <li>Prosiding Seminar</li>
                                                    <li>Laporan Penelitian/ Pengabdian</li>
                                                    <li>Foto Kegiatan</li>
                                                    <li>dll.</li>
                                                </ol>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Deskripsi Singkat Kegiatan -->
                                    <div class="row mb-4">
                                        <label for="deskripsi_singkat" class="col-sm-3 text-dark text-sm fw-bold">
                                            Deskripsi Singkat Kegiatan <span class="text-danger">*</span><br>
                                            <span class="text-muted" style="font-size: 12px;">*Deskripsi Singkat
                                                Kegiatan.</span>
                                        </label>
                                        <div class="col-sm-9">
                                            <textarea name="deskripsi_singkat" id="deskripsi_singkat" class="form-control" cols="30" rows="5"
                                             placeholder="Masukkan deskripsi singkat kegiatan minimal 100 karakter dan maksimal 500 karakter..."></textarea>
                                            <small class="text-muted d-block mt-2">
                                                * Wajib diisi, minimal 100 karakter dan maksimal 500 karakter.
                                            </small>
                                            <small id="charCountdeskripsi_singkat" class="text-muted">0 / 500
                                                karakter</small>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-sm-3 col-form-label fw-bold text-dark">Upload Bukti
                                            Pelaksanaan / Implementasi Kerja Sama</label>
                                        <div class="col-sm-9">
                                            <input type="file" name="file_imp" class="form-control custom-file-input"
                                                accept=".pdf">
                                            <small class="text-muted" style="font-size: 12px;">Optional (tidak
                                                wajib
                                                diisi).</small><br>
                                            <small class="text-danger" style="font-size: 12px;">*Ukuran Maksimal
                                                Unggah
                                                File
                                                5Mb.</small><br>
                                            <small class="text-danger" style="font-size: 12px;">*File
                                                Berformat
                                                PDF.</small><br>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-sm-3 col-form-label fw-bold text-dark">Upload Bukti
                                            Pelaksanaan Kerja Sama untuk Verifikasi IKU 6 <br>
                                            <a href="{{ asset('template/template_ikuenam.docx') }}" target="_blank" class="btn btn-sm btn-primary">Download
                                                Format
                                                Laporan</a></label>
                                        <div class="col-sm-9">
                                            <input type="file" name="file_ikuenam"
                                                class="form-control custom-file-input" accept=".pdf">
                                            <small class="text-muted" style="font-size: 12px;">Optional (tidak
                                                wajib
                                                diisi).</small><br>
                                            <small class="text-danger" style="font-size: 12px;">*Ukuran Maksimal
                                                Unggah
                                                File
                                                5Mb.</small><br>
                                            <small class="text-danger" style="font-size: 12px;">*File
                                                Berformat
                                                PDF.</small><br>
                                        </div>
                                    </div>

                                    <!-- Nama PIC Kegiatan Pihak Mitra -->
                                    <div class="row mb-4">
                                        <label class="col-sm-3 text-dark text-sm fw-bold">Nama PIC Kegiatan Pihak
                                            Mitra<span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="nama_pic_kegiatan" class="form-control"
                                                placeholder="Masukkan Nama PIC Kegiatan Pihak Mitra">
                                        </div>
                                    </div>
                                    <!-- Jabatan PIC Kegiatan Pihak Mitra -->
                                    <div class="row mb-4">
                                        <label class="col-sm-3 text-dark text-sm fw-bold">Jabatan PIC Kegiatan Pihak
                                            Mitra<span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="jabatan_pic_kegiatan" class="form-control"
                                                placeholder="Masukkan Jabatan PIC Kegiatan Pihak Mitra">
                                        </div>
                                    </div>
                                    <!-- Telepon PIC Kegiatan Pihak Mitra -->
                                    <div class="row mb-4">
                                        <label class="col-sm-3 text-dark text-sm fw-bold">Telepon PIC Kegiatan Pihak
                                            Mitra<span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="number" name="telp_pic_kegiatan" class="form-control"
                                                placeholder="Masukkan Telepon PIC Kegiatan Pihak Mitra">
                                        </div>
                                    </div>
                                    <!-- Email PIC Kegiatan Pihak Mitra -->
                                    <div class="row mb-4">
                                        <label class="col-sm-3 text-dark text-sm fw-bold">Email PIC Kegiatan Pihak
                                            Mitra<span class="text-danger">*</span><br>
                                            <span class="text-muted" style="font-size: 12px;">*Email ini digunakan untuk
                                                keperluan pengiriman link
                                                kuesioner kegiatan.</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="pic_kegiatan" class="form-control"
                                                placeholder="Masukkan Email PIC Kegiatan Pihak Mitra">
                                        </div>
                                    </div>

                                    <!-- Nama PIC Kegiatan Pihak Mitra -->
                                    <div class="row mb-4">
                                        <label class="col-sm-3 text-dark text-sm fw-bold">Nama PIC Kegiatan Pihak
                                            Internal<span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="nama_pic_internal" class="form-control"
                                                placeholder="Masukkan Nama PIC Kegiatan Pihak Mitra">
                                        </div>
                                    </div>
                                    <!-- Jabatan PIC Kegiatan Pihak Mitra -->
                                    <div class="row mb-4">
                                        <label class="col-sm-3 text-dark text-sm fw-bold">Jabatan PIC Kegiatan Pihak
                                            Internal<span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="jabatan_pic_internal" class="form-control"
                                                placeholder="Masukkan Jabatan PIC Kegiatan Pihak Mitra">
                                        </div>
                                    </div>
                                    <!-- Telepon PIC Kegiatan Pihak Mitra -->
                                    <div class="row mb-4">
                                        <label class="col-sm-3 text-dark text-sm fw-bold">Telepon PIC Kegiatan Pihak
                                            Internal<span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="number" name="telp_pic_internal" class="form-control"
                                                placeholder="Masukkan Telepon PIC Kegiatan Pihak Mitra">
                                        </div>
                                    </div>
                                    <!-- Email PIC Kegiatan Pihak Mitra -->
                                    <div class="row mb-4">
                                        <label class="col-sm-3 text-dark text-sm fw-bold">Email PIC Kegiatan Pihak
                                            Internal<span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="email_pic_internal" class="form-control"
                                                placeholder="Masukkan Email PIC Kegiatan Pihak Mitra">
                                        </div>
                                    </div>

                                    <!-- Link Publikasi Internal -->
                                    <div class="row mb-4">
                                        <label class="col-sm-3 text-dark text-sm fw-bold">Link Publikasi Internal</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="link_pub_internal" class="form-control"
                                                placeholder="Masukkan Link Publikasi Internal">
                                        </div>
                                    </div>

                                    <!-- Link Publikasi Eksternal -->
                                    <div class="row mb-4">
                                        <label class="col-sm-3 text-dark text-sm fw-bold">Link Publikasi Eksternal</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="link_pub_eksternal" class="form-control"
                                                placeholder="Masukkan Link Publikasi eksternal">
                                        </div>
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
    <script>
        $(document).ready(function() {
            // Inisialisasi Summernote
            $('#deskripsi_singkat').summernote({
                height: 200,
                placeholder: 'Tulis deskripsi singkat di sini...',
                callbacks: {
                    onImageUpload: function(files) {
                        uploadImage(files[0]);
                    },
                }
            });

            // Fungsi upload gambar ke Summernote
            function uploadImage(file) {
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
                        $('#deskripsi_singkat').summernote('insertImage', url);
                    },
                    error: function(xhr, status, error) {
                        alert('Upload gagal: ' + error);
                    }
                });
            }

            $('#deskripsi_singkat').on('summernote.change', function() {
                summernoteCount('deskripsi_singkat', 500)
            })

            function summernoteCount(konten, maksimum) {
                const $editor = $('#' + konten);
                const $counter = $('#charCount' + konten);
                const content = $editor.summernote('code');


                // Menghapus HTML tag dan menghitung jumlah karakter
                const textContent = $('<div>').html(content).text().trim();
                const charCount = textContent.length;

                // Update tampilan jumlah karakter
                $counter.text(charCount + ' / ' + maksimum + ' karakter');

                // Jika melebihi batas
                if (charCount > maksimum) {
                    $counter
                        .removeClass('text-black text-blue-important')
                        .addClass('text-red-important');

                    // Potong karakter sesuai batas maksimal
                    const limitedText = textContent.slice(0, maksimum);

                    // Update konten editor
                    $editor.summernote('code', limitedText);

                    // Perbarui jumlah karakter dan ganti warna ke biru
                    const updatedCharCount = maksimum + ' / maksimum ' + maksimum + ' karakter';
                    $counter
                        .text(updatedCharCount)
                        .removeClass('text-red-important text-black')
                        .addClass('text-blue-important');

                    // Notifikasi Swal
                    Swal.fire({
                        icon: "error",
                        title: "Melebihi Maksimal",
                        html: "Jumlah karakter melebihi batas maksimal.",
                    });
                } else {
                    $counter
                        .removeClass('text-red-important text-blue-important')
                        .addClass('text-black');
                }
            }

        });
    </script>



    <script src="{{ asset('js/implementasi/tambah.js') }}"></script>
@endpush
