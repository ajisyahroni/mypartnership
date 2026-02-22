@extends('layouts.app')

@section('contents')
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
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <button type="button" class="btn btn-success dropdown-toggle d-flex align-items-center"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bx bx-printer me-2"></i> Download Excel
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0)" target="_blank"
                                                class="dropdown-item d-flex align-items-center btn-download-proposal">
                                                <i class="bx bx-spreadsheet me-2"></i> Download Ajuan Proposal
                                            </a>
                                            <a href="javascript:void(0)" target="_blank"
                                                class="dropdown-item d-flex align-items-center btn-download-laporan">
                                                <i class="bx bx-spreadsheet me-2"></i> Download Laporan Hibah
                                            </a>
                                        </li>
                                    </ul>

                                    <a href="{{ route('hibah.tambah') }}"
                                        class="btn ms-2 btn-primary d-flex align-items-center">
                                        <i class="bx bx-plus me-2"></i> Tambah Ajuan Hibah
                                    </a>
                                </div>

                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="mt-4">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="filter_wrapper d-flex justify-content-end">
                                            <div class="d-flex align-items-center justify-content-end mb-2 me-2">
                                                <!-- Search dan Export di kanan -->
                                                <div class="d-flex align-items-center" id="btnDatatable">
                                                    <!-- Tombol Export & Show/Hide Kolom -->
                                                    <div class="btn-group">
                                                        <button id="btnExcel" class="btn btn-sm btn-primary">EXCEL</button>
                                                        <button id="btnCSV" class="btn btn-sm btn-success">CSV</button>
                                                        <!-- Dropdown Show/Hide Kolom -->
                                                        <div class="btn-group">
                                                            <button class="btn btn-sm btn-info dropdown-toggle"
                                                                type="button" id="toggleColumns" data-bs-toggle="dropdown">
                                                                ☰
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-columns"
                                                                style="overflow-x: none;" id="columnToggleList">
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-primary btn-sm d-flex justify-content-end ms-2"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#filterContent" aria-expanded="false"
                                                        aria-controls="filterContent">
                                                        <i class="bx bx-filter-alt me-2"></i> Filter
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @include('filter.hibah', ['filterHibah' => $filterHibah])
                                    </div>
                                </div>
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-hover align-middle custom-table" id="dataTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Action</th>
                                                <th>Judul Proposal</th>
                                                <th>Institusi Mitra</th>
                                                <th>Jenis Hibah</th>
                                                <th>Ketua Pelaksana</th>
                                                <th>Program Studi</th>
                                                <th>Fakultas</th>
                                                <th>Tanggal Kegiatan</th>
                                                <th>File Kontrak</th>
                                                <th>Status</th>
                                                <th>Pengusul</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-verifikasi-pencairan" aria-labelledby="DetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="title-pencairan"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('hibah.VerifikasiTahap') }}" method="post" enctype="multipart/form-data"
                    id="formVerifikasiTahap">
                    @csrf
                    <div class="modal-body" id="content-detail-pencairan">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-detail-notif" aria-labelledby="DetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-detail"></i> Data yang Harus Di Tindak Lanjuti</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="content-detail-notif-hibah">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pemberitahuanModal" tabindex="-1" aria-labelledby="pemberitahuanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pemberitahuanModalLabel">Pemberitahuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="kontenPemberitahuan">
                    <div class="alert alert-danger d-flex align-items-center p-3" role="alert"
                        style="background-color: #facdcd; border-left: 6px solid #d40000;">
                        <div class="me-3 text-white d-flex align-items-center justify-content-center"
                            style="background-color: #d40000; width: 30px; height: 30px; border-radius: 4px;">
                            <i class="bx bx-info-circle"></i>
                        </div>
                        <div class="text-dark small">
                            Yth. <b>{{ ucwords(auth()->user()->name) }}</b>,<br><br>

                            Mohon dengan hormat untuk segera informasikan penanggung jawab kegiatan agar memverifikasi data
                            yang
                            telah diajukan, agar proses dapat
                            dilanjutkan ke tahap selanjutnya. Kami memberikan waktu tambahan terakhir hingga hari ini.
                            <br><br>
                            Terima kasih atas perhatian dan kerja samanya.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal-detail" aria-labelledby="DetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-detail"></i> Detail Ajuan Hibah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="content-detail-hibah">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-revisi" aria-labelledby="DetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-note"></i> Revisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="content-revisi">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="surveiModal" tabindex="-1" aria-labelledby="surveiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="surveiModalLabel">Pemberitahuan Pengisian Survei</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="surveiNotifikasi">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for Filling Survey (hidden by default) -->
    <div class="modal fade" id="surveyFillModal" tabindex="-1" aria-labelledby="surveyFillModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="surveyFillModalLabel">Isi Survei</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('submitSurvey') }}" method="post" id="formInput">
                    @csrf
                    <div class="modal-body" id="surveyFormContent">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Kirim Jawaban</button>
                    </div>
                </form>
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
        var getData = "{{ route('hibah.getData') }}";
        var hibahUpload = "{{ route('hibah.uploadFileKontrak') }}";
        var showRevisiUrl = "{{ route('hibah.showRevisi') }}";
        var urlGetSurvei = @json(route('getSurveiHibah'));

        var modalPemberitahuan = $("#pemberitahuanModal");
        var kontenPemberitahuan = $("#kontenPemberitahuan");

        var currentRole = @json(session('current_role'));

        var isVerifPJ = @json($isVerifPJ)

        $(document).ready(function() {
            if (isVerifPJ > 0) {
                modalPemberitahuan.modal('show')
            }
        })
    </script>
    <script src="{{ asset('js/hibah/ajuan.js') }}"></script>
    <script src="{{ asset('js/hibah/survei.js') }}"></script>
    <script>
        function showRevisi(id_hibah_revisi, field) {
            $("#modal-revisi").modal('show');
            $("#content-revisi").html(`<div id="loading">
                        <div class="d-flex justify-content-center my-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>`);

            $.ajax({
                url: showRevisiUrl,
                method: "GET",
                data: {
                    id_hibah: id_hibah_revisi,
                    field: field
                },
                dataType: 'json',
                success: function(res) {
                    $("#content-revisi").html(res);
                },
                error: function(xhr) {
                    let errorMessages = "";
                    if (xhr.responseJSON?.errors) {
                        Object.values(xhr.responseJSON.errors).forEach((messages) => {
                            errorMessages += messages.join("<br>") + "<br>";
                        });
                    }

                    Swal.fire({
                        icon: "error",
                        title: "Gagal Memuat",
                        html: errorMessages || "Terjadi kesalahan saat memuat data.",
                    });
                }
            });
        }

        function uploadFile(id_hibah, flag) {
            let input = $(`#fileInput-${id_hibah}-${flag}`)[0];
            if (!input || input.files.length === 0) return;

            let formData = new FormData();
            formData.append("file", input.files[0]);
            formData.append("id_hibah", id_hibah);
            formData.append("flag", flag);

            let loader = $(`#loader-${id_hibah}-${flag}`);
            loader.removeClass("d-none");

            $.ajax({
                url: "{{ route('hibah.uploadFileKontrak') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function(res) {
                    loader.addClass("d-none");
                    if (res.message) toastr.success(res.message);
                    window.table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    loader.addClass("d-none");
                    let errorMessages = "";
                    if (xhr.responseJSON?.errors) {
                        Object.values(xhr.responseJSON.errors).forEach((messages) => {
                            errorMessages += messages.join("<br>") + "<br>";
                        });
                    }

                    Swal.fire({
                        icon: "error",
                        title: "Gagal Mengunggah",
                        html: errorMessages || "Terjadi kesalahan saat mengunggah file.",
                    });
                }
            });
        }
    </script>

@endpush
