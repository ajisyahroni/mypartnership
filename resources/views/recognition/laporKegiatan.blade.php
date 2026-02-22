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
                                <a href="{{ route('recognition.tambah') }}" class="btn btn-success shadow-sm">
                                    <i class="fas fa-folder-plus me-2"></i> Buat Ajuan
                                </a>
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
                                            </div>
                                            <!-- Tombol Filter -->
                                        </div>

                                    </div>
                                </div>
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-hover align-middle custom-table" id="dataTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Opsi</th>
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
@endsection

@push('scripts')
    <script>
        var getData = "{{ route('recognition.getDatalaporKegiatan') }}"
        var IdFakultas = "user";
        // var recognitionUpload = "{{ route('recognition.uploadFile') }}";
    </script>
    <script src="{{ asset('js/recognition/laporanKegiatan.js') }}"></script>
    <script>
        function uploadFile(id_rec, flag) {
            let input = $(`#fileInput-${id_rec}-${flag}`)[0];
            if (input.files.length === 0) return;

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
                        toastr.success(res.message);
                    } else {
                        toastr.error(res.message);
                    }
                    
                    setTimeout(() => {
                        window.table.ajax.reload(null, false);
                    }, 800);
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
@endpush
