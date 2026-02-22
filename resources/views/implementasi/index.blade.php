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
                                <div
                                    class="d-flex flex-wrap justify-content-start justify-content-md-end align-items-center gap-2">
                                    <div class="dropdown">
                                        <button type="button"
                                            class="btn btn-success dropdown-toggle d-flex align-items-center"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bx bx-printer me-2"></i> Download Excel
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="javascript:void(0)" target="_blank"
                                                    class="dropdown-item d-flex align-items-center btn-download-implementasi">
                                                    <i class="bx bx-spreadsheet me-2"></i> Download Lapor Implementasi
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <a href="{{ route('implementasi.tambah') }}" class="btn btn-primary shadow-sm">
                                            <i class="fas fa-plus-circle me-2"></i>
                                            Tambah Implementasi
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mt-4 p-3">
                                <div class="col-12">
                                    <div class="d-flex align-items-center justify-content-end mb-2">
                                        <!-- Search dan Export di kanan -->
                                        <div class="d-flex align-items-center" id="btnDatatable">
                                            <!-- Tombol Export & Show/Hide Kolom -->
                                            <div class="btn-group me-2">
                                                <button id="btnExcel" class="btn btn-sm btn-primary">EXCEL</button>
                                                <button id="btnCSV" class="btn btn-sm btn-success">CSV</button>

                                                <!-- Dropdown Show/Hide Kolom -->
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-info dropdown-toggle" type="button"
                                                        id="toggleColumns" data-bs-toggle="dropdown"> ☰
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-columns"
                                                        style="overflow-x: none;" id="columnToggleList">
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="dropdown me-2">
                                                <a class="btn btn-sm btn-secondary dropdown-toggle" href="#"
                                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    View
                                                </a>

                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0)"
                                                            onclick="showTable('instansi')">
                                                            <i class="fas fa-building me-2"></i> Berdasarkan Instansi
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0)"
                                                            onclick="showTable('implementasi')">
                                                            <i class="fas fa-cogs me-2"></i> Berdasarkan Implementasi
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>

                                            <button class="btn btn-primary btn-sm d-flex justify-content-end" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#filterImplementasi"
                                                aria-expanded="false" aria-controls="filterImplementasi">
                                                <i class="bx bx-filter-alt me-2"></i> Filter
                                            </button>
                                        </div>

                                        <!-- Filter Collapse -->
                                    </div>
                                    @include('filter.implementasi')
                                </div>

                                <style>
                                    .custom-table th {
                                        white-space: nowrap;
                                        padding: 10px;
                                    }

                                    .table-responsive {
                                        max-width: 100%;
                                        /* overflow-x: auto; */
                                        overflow: visible !important;
                                        /* default-nya scroll atau auto */
                                        position: relative;
                                        /* agar z-index bisa bekerja */
                                    }


                                    table#dataTable-group,
                                    table#dataTable {
                                        width: 100% !important;
                                    }

                                    #table-instansi .dt-scroll-headInner {
                                        width: 100% !important;
                                    }

                                    table.table.table-hover.align-middle.custom-table.dataTable {
                                        width: 100% !important;

                                    }

                                    /* [data-title-tooltip]::before,
                                                                                                    [data-title-tooltip]::after {
                                                                                                        z-index: 9999;
                                                                                                    } */
                                </style>

                                <div id="table-instansi" class="table-instansi d-none">
                                    <div class="table-responsive" style="overflow-x: auto;">
                                        <table class="table table-hover align-middle custom-table" id="dataTable-group">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Opsi</th>
                                                    <th>Nama Mitra Kerja Sama</th>
                                                    <th>Wilayah</th>
                                                    <th>Jenis Institusi Mitra</th>
                                                    <th>Jumlah Implementasi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div id="table-implementasi" class="table-implementasi d-none">
                                    {{-- <div class="table-responsive" style="overflow-x: auto;"> --}}
                                    <div class="table-responsive" style="overflow-x: auto;">
                                        {{-- <table class="table table-hover align-middle custom-table" id="dataTable"> --}}
                                        <table class="table table-hover align-middle custom-table" id="dataTable">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Opsi</th>
                                                    <th>Kategori</th>
                                                    <th>Mitra Kerja Sama</th>
                                                    <th>Tingkat Kerja Sama</th>
                                                    <th>Pelaksana</th>
                                                    <th>Judul Kegiatan</th>
                                                    <th>Bentuk Kegiatan/ Manfaat</th>
                                                    <th>Bukti Pelaksanaan</th>
                                                    <th>Link Dokumen Kerja Sama</th>
                                                    <th>Link Lapor Kerma</th>
                                                    <th>Tahun Berakhir</th>
                                                    <th>Status Verifikasi</th>
                                                    <th>Pelapor</th>
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
            <div class="modal fade" id="modal-detail" aria-labelledby="DetailLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="DetailLabel">Detail Lapor Implementasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="konten-detail"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="surveiModal" tabindex="-1" aria-labelledby="surveiModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="surveiModalLabel">Pemberitahuan Pengisian Survei</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
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
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
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
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var getData = "{{ route('implementasi.getData') }}"
        var getDataGroup = "{{ route('implementasi.getDataGroup') }}"
        var implementasiUpload = "{{ route('implementasi.uploadFile') }}";
        var implementasiSendEmail = "{{ route('implementasi.sendEmail') }}"

        showTable('implementasi')

        var role = @json(session('current_role'))

        function showTable(jenis) {
            if (jenis == "instansi") {
                $("#table-instansi").removeClass("d-none").hide().fadeIn();
                $("#table-implementasi").fadeOut(function() {
                    $(this).addClass("d-none");
                });
            } else {
                $("#table-implementasi").removeClass("d-none").hide().fadeIn();
                $("#table-instansi").fadeOut(function() {
                    $(this).addClass("d-none");
                });
            }
        }

        function uploadFile(id_ev, flag) {
            let input = $(`#fileInput-${id_ev}-${flag}`)[0];
            if (input.files.length === 0) return;

            let uploadBtn = $(`#fileInput-${id_ev}-${flag}`).next("span");
            let originalText = uploadBtn.html();

            uploadBtn.html('<i class="fa fa-spinner fa-spin"></i> Uploading...').css({
                pointerEvents: "none", // disable klik
                opacity: 0.6
            });

            let formData = new FormData();
            formData.append("file", input.files[0]);
            formData.append("id_ev", id_ev);
            formData.append("flag", flag);

            $.ajax({
                url: implementasiUpload,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                },
                success: function(res) {
                    if (res.status) {
                        toastr.success(res.message);
                    } else {
                        toastr.error(res.message);
                    }
                    
                    setTimeout(() => {
                        window.table.ajax.reload(null, false);
                    }, 800);


                    uploadBtn.html(originalText).css({
                        pointerEvents: "auto",
                        opacity: 1
                    });
                },
                error: function(xhr) {
                    let errorMessages = "";
                    if (xhr.responseJSON?.errors) {
                        Object.values(xhr.responseJSON.errors).forEach(
                            (messages) => {
                                errorMessages += messages.join("<br>") + "<br>";
                            }
                        );
                    }

                    Swal.fire({
                        icon: "error",
                        title: "Gagal Menyimpan",
                        html: errorMessages ||
                            xhr.responseJSON?.error ||
                            "Terjadi kesalahan tak terduga.",
                    });
                },
            });
        }
    </script>
     @if(!empty($sendEmailData))
    <script>
            $(document).ready(function () {
                $.ajax({
                    url: implementasiSendEmail,
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function () {
                        console.log("Email berhasil dikirim.");
                    },
                    error: function () {
                        console.log("Email gagal dikirim.");
                    }
                });
            });
        </script>
    @endif
    <script src="{{ asset('js/implementasi/index.js') }}"></script>

    <script src="{{ asset('js/implementasi/survei.js') }}"></script>
@endpush
