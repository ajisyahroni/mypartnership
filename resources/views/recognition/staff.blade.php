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
                                <div class="dropdown">
                                    <button type="button" class="btn btn-success dropdown-toggle d-flex align-items-center"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bx bx-printer me-2"></i> Download Excel
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0)" target="_blank"
                                                class="dropdown-item d-flex align-items-center btn-download-rekognisi">
                                                <i class="bx bx-spreadsheet me-2"></i> Download Data Rekognisi
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
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
                                                </div>

                                                  <!-- Tombol Filter -->
                                                <button class="btn btn-primary ms-2 btn-sm d-flex justify-content-end" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#filterRecognition"
                                                    aria-expanded="false" aria-controls="filterRecognition">
                                                    <i class="bx bx-filter-alt me-2"></i> Filter
                                                </button>
                                            </div>
                                            <!-- Tombol Filter -->
                                        </div>
                                             @include('filter.recognition', ['filterRecognition' => $filterRecognition])
                                
                                    </div>
                                </div>
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-hover align-middle custom-table" id="dataTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Opsi</th>
                                                <th>Status</th>
                                                <th>File Acceptance</th>
                                                <th>File CV</th>
                                                <th>File SK</th>
                                                <th>File Laporan</th>
                                                <th>Pengusul</th>
                                                <th>Prodi Pengusul</th>
                                                <th>Nama Professor</th>
                                                <th>Asal Universitas</th>
                                                <th>Bidang Kepakaran</th>
                                                <th>Tanggal Ajuan</th>
                                                <th>Tanggal Selesai</th>
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
@endsection

@push('scripts')    
    <script>
        var getData = "{{ route('recognition.getData') }}"
        var getDetailRecognition = "{{ route('recognition.getDetailRecognition') }}"
        var recognitionUpload = "{{ route('recognition.uploadFile') }}";

        var showRevisiUrl = "{{ route('recognition.showRevisi') }}"
        
   </script>
    <script src="{{ asset('js/recognition/index.js') }}"></script>
    <script>
        function showRevisi(id_rec, field) {
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
                    id_rec: id_rec,
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


        function uploadFile(id_rec, flag) {
            let input = $(`#fileInput-${id_rec}-${flag}`)[0];
            if (input.files.length === 0) return;

            // Cari tombol <span> terkait
            let uploadBtn = $(`#fileInput-${id_rec}-${flag}`).next("span");
            let originalText = uploadBtn.html();

            // Tampilkan spinner loading
            uploadBtn.html('<i class="fa fa-spinner fa-spin"></i> Uploading...').css({
                pointerEvents: "none", // disable klik
                opacity: 0.6
            });

            let formData = new FormData();
            formData.append("file", input.files[0]);
            formData.append("id_rec", id_rec);
            formData.append("flag", flag);

            $.ajax({
                url: recognitionUpload,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                },
                success: function(res) {
                    if (res.status) {
                        toastr.success(res.message)

                        setTimeout(() => {
                            window.table.ajax.reload(null, false);
                        }, 1000);
                    }else{
                        toastr.error(res.message)
                    }
                     // Kembalikan tombol ke teks asli
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

                    // Kembalikan tombol ke teks asli
                    uploadBtn.html(originalText).css({
                        pointerEvents: "auto",
                        opacity: 1
                    });
                },
            });
        }
    </script>
@endpush
