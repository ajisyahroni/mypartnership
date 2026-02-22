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
                                <a href="#" class="btn btn-success shadow-sm btn-download-dokumen">
                                    <i class="bx bx-printer me-2"></i> Download Excel
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
                                            <div>
                                                <button class="btn btn-primary btn-sm d-flex justify-content-end"
                                                    type="button" data-bs-toggle="collapse" data-bs-target="#filterDokumen"
                                                    aria-expanded="false" aria-controls="filterDokumen">
                                                    <i class="bx bx-filter-alt me-2"></i> Filter
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Filter Collapse -->
                                        @include('filter.dokumen', ['filterDokumen' => $filterDokumen])

                                    </div>
                                </div>
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-hover align-middle custom-table" id="dataTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Opsi</th>
                                                <th>Nama Mitra</th>
                                                <th>Lingkup</th>
                                                <th>Wilayah / Negara Mitra</th>
                                                <th>Status</th>
                                                <th>Jenis Kerja Sama</th>
                                                <th>Jenis Institusi Mitra</th>
                                                <th>Tingkat Kerja Sama</th>
                                                <th>Prodi / Unit / Fakultas</th>
                                                <th>Tahun Mulai Kerja Sama</th>
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
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var getData = "{{ route('dokumen.getData') }}"
    </script>
    <script src="{{ asset('js/dokumen/index.js') }}"></script>
@endpush
