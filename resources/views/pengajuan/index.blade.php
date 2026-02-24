@extends('layouts.app')

@section('contents')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row pt-4">
                <div class="col-sm-12">
                    <div class="card shadow-lg border-0 rounded-4">
                        <style>
                            span.badge {
                                font-size: 10px !important;
                            }
                        </style>
                        <div class="card-header">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                                <h5 class="card-title mb-2 me-3">
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
                                                    class="dropdown-item d-flex align-items-center btn-download-pengajuan">
                                                    <i class="bx bx-spreadsheet me-2"></i> Download Pengajuan
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    <a href="{{ route('pengajuan.tambahBaru') }}" class="btn btn-primary shadow-sm">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        Ajukan Dokumen Baru
                                    </a>

                                    <a href="{{ route('pengajuan.laporPengajuan') }}" class="btn btn-success shadow-sm">
                                        <i class="fas fa-folder-plus me-2"></i> Simpan Dokumen
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="mt-4">
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
                                            <button class="btn btn-primary btn-sm d-flex justify-content-end" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#filterPengajuan"
                                                aria-expanded="false" aria-controls="filterPengajuan">
                                                <i class="bx bx-filter-alt me-2"></i> Filter
                                            </button>
                                        </div>

                                        <!-- Filter Collapse -->
                                    </div>
                                    @include('filter.pengajuan', ['filterPengajuan' => $filterPengajuan])
                                </div>

                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-hover align-middle custom-table" id="dataTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Opsi</th>
                                                <th>Status Verifikasi</th>
                                                <th>Nama Mitra</th>
                                                <th>Lingkup</th>
                                                <th>Wilayah / Negara Mitra</th>
                                                <th>Jenis Kerja Sama</th>
                                                <th>Jenis Institusi Mitra</th>
                                                <th>Tingkat Kerja Sama</th>
                                                <th>Prodi / Unit / Fakultas</th>
                                                <th>Tahun Mulai Kerja Sama</th>
                                                <th>Penandatangan</th>
                                                <th>Status Kerja Sama</th>
                                                <th>Tanggal ajuan</th>
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
            <div class="modal fade" id="modal-detail" aria-labelledby="DetailLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="DetailLabel">Detail Kerja Sama</h5>
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

            <div class="modal fade" id="modal-verifikasi" aria-labelledby="VerifikasiLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="VerifikasiLabel">Verifikasi Kerja Sama</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="konten-verifikasi"></div>
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
        var getData = "{{ route('pengajuan.getData') }}"
        var pengajuanSendEmail = "{{ route('pengajuan.sendEmail') }}"
        </script>
    <script src="{{ asset('js/pengajuan/index.js') }}"></script>
    <script src="{{ asset('js/pengajuan/survei.js') }}"></script>
     @if(!empty($sendEmailData))
    <script>
            $(document).ready(function () {
                $.ajax({
                    url: pengajuanSendEmail,
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
@endpush
