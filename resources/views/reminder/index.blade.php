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
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <span class="me-2">
                                        <i class="fa-solid fa-folder-open text-warning"></i>
                                    </span>{{ @$page_title }}
                                </h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mt-4">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="kerma-tab" data-bs-toggle="tab"
                                            data-bs-target="#kerma" type="button" role="tab" aria-controls="kerma"
                                            aria-selected="true">
                                            Melengkapi Dokumen Kerja Sama
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="produktif-tab" data-bs-toggle="tab"
                                            data-bs-target="#produktif" type="button" role="tab"
                                            aria-controls="produktif" aria-selected="false">
                                            Mengisi Produktivitas Kerja Sama
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="expired-tab" data-bs-toggle="tab"
                                            data-bs-target="#expired" type="button" role="tab" aria-controls="expired"
                                            aria-selected="false">
                                            Expired Kerja Sama
                                        </button>
                                    </li>
                                </ul>

                                <style>
                                    table#dataTableProduktif,
                                    table#dataTableKerma,
                                    table#dataTableExpired {
                                        width: 100% !important;
                                    }
                                </style>

                                <!-- Tab Content -->
                                <div class="tab-content mt-3" id="myTabContent">
                                    <!-- Tab Modal Read -->
                                    <div class="tab-pane fade show active" id="kerma" role="tabpanel"
                                        aria-labelledby="kerma-tab">
                                        <div class="d-flex justify-content-end align-items-center mb-3">
                                            <button class="btn btn-orange btn-broadcast" data-tipe="lengkapi">
                                                <i class="fas fa-paper-plane me-2"></i> Kirim Email
                                            </button>
                                        </div>

                                        <div class="table-responsive" style="overflow-x: auto;">
                                            <table class="table table-hover align-middle custom-table" id="dataTableKerma">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Unit/Fakultas/Program Studi</th>
                                                        <th>Tanggal Kegiatan</th>
                                                        <th>Nama Institusi Mitra</th>
                                                        <th>Jenis Kerja Sama</th>
                                                        <th>Jenis Institusi Mitra</th>
                                                        <th>Opsi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($dataLengkap as $lkp)
                                                        <tr>
                                                            <td><span
                                                                    class="text-dark fw-semibold mb-0">{{ $loop->iteration }}</span>
                                                            </td>
                                                            <td><span
                                                                    class="text-dark fw-semibold mb-0">{{ $lkp->prodi_unit }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-primary">Tanggal Mulai :
                                                                    {{ Tanggal_indo($lkp->mulai) }}</span><br>
                                                                <span class="badge bg-secondary">Tanggal Selesai :
                                                                    {{ Tanggal_indo($lkp->selesai) }}</span>
                                                            </td>
                                                            <td><span
                                                                    class="text-dark fw-semibold mb-0">{{ $lkp->nama_institusi }}</span>
                                                            </td>
                                                            <td><span
                                                                    class="text-dark fw-semibold mb-0">{{ $lkp->jenis_kerjasama }}</span>
                                                            </td>
                                                            <td><span
                                                                    class="text-dark fw-semibold mb-0">{{ $lkp->jenis_institusi }}</span>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex justifiy-content-center">
                                                                    <button class="btn btn-danger btn-reminder"
                                                                        data-title-tooltip="Kirim Reminder"
                                                                        data-id_mou="{{ $lkp->id_mou }}"
                                                                        data-tipe="lengkapi"><i
                                                                            class="fas fa-paper-plane"></i></button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- Tab Timeline -->
                                    <div class="tab-pane fade" id="produktif" role="tabpanel"
                                        aria-labelledby="produktif-tab">
                                        <div class="d-flex justify-content-end align-items-center mb-3">
                                            <button class="btn btn-orange btn-broadcast" data-tipe="produktif">
                                                <i class="fas fa-paper-plane me-2"></i> Kirim Email
                                            </button>
                                        </div>
                                        <div class="table-responsive" style="overflow-x: auto;">
                                            <table class="table table-hover align-middle custom-table"
                                                id="dataTableProduktif">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Unit/Fakultas/Program Studi</th>
                                                        <th>Tanggal Kegiatan</th>
                                                        <th>Nama Institusi Mitra</th>
                                                        <th>Jenis Kerja Sama</th>
                                                        <th>Jenis Institusi Mitra</th>
                                                        <th>Opsi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($dataProduktif as $lkp)
                                                        <tr>
                                                            <td> <span
                                                                    class="text-dark fw-semibold mb-0">{{ $loop->iteration }}</span>
                                                            </td>
                                                            <td> <span
                                                                    class="text-dark fw-semibold mb-0">{{ $lkp->prodi_unit }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-primary">Tanggal Mulai :
                                                                    {{ Tanggal_indo($lkp->mulai) }}</span><br>
                                                                <span class="badge bg-secondary">Tanggal Selesai :
                                                                    {{ Tanggal_indo($lkp->selesai) }}</span>
                                                            </td>
                                                            <td> <span
                                                                    class="text-dark fw-semibold mb-0">{{ $lkp->nama_institusi }}</span>
                                                            </td>
                                                            <td> <span
                                                                    class="text-dark fw-semibold mb-0">{{ $lkp->jenis_kerjasama }}</span>
                                                            </td>
                                                            <td> <span
                                                                    class="text-dark fw-semibold mb-0">{{ $lkp->jenis_institusi }}</span>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex justifiy-content-center">
                                                                    <button class="btn btn-danger btn-reminder"
                                                                        data-title-tooltip="Kirim Reminder"
                                                                        data-id_mou="{{ $lkp->id_mou }}"
                                                                        data-tipe="produktif"><i
                                                                            class="fas fa-paper-plane"></i></button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- Tab Timeline -->
                                    <div class="tab-pane fade" id="expired" role="tabpanel"
                                        aria-labelledby="expired-tab">
                                        <div class="d-flex justify-content-end align-items-center mb-3">
                                            <button class="btn btn-orange btn-broadcast" data-tipe="expired">
                                                <i class="fas fa-paper-plane me-2"></i> Kirim Email
                                            </button>
                                        </div>
                                        <div class="table-responsive" style="overflow-x: auto;">
                                            <table class="table table-hover align-middle custom-table"
                                                id="dataTableExpired">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Unit/Fakultas/Program Studi</th>
                                                        <th>Tanggal Kegiatan</th>
                                                        <th>Nama Institusi Mitra</th>
                                                        <th>Jenis Kerja Sama</th>
                                                        <th>Jenis Institusi Mitra</th>
                                                        <th>Opsi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($dataExpired as $lkp)
                                                        <tr>
                                                            <td><span
                                                                    class="text-dark fw-semibold mb-0">{{ $loop->iteration }}</span>
                                                            </td>
                                                            <td><span
                                                                    class="text-dark fw-semibold mb-0">{{ $lkp->prodi_unit }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-primary">Tanggal Mulai :
                                                                    {{ Tanggal_indo($lkp->mulai) }}</span><br>
                                                                <span class="badge bg-secondary">Tanggal Selesai :
                                                                    {{ Tanggal_indo($lkp->selesai) }}</span>
                                                            </td>
                                                            <td><span
                                                                    class="text-dark fw-semibold mb-0">{{ $lkp->nama_institusi }}</span>
                                                            </td>
                                                            <td><span
                                                                    class="text-dark fw-semibold mb-0">{{ $lkp->jenis_kerjasama }}</span>
                                                            </td>
                                                            <td><span
                                                                    class="text-dark fw-semibold mb-0">{{ $lkp->jenis_institusi }}</span>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex justifiy-content-center">
                                                                    <button class="btn btn-danger btn-reminder"
                                                                        data-title-tooltip="Kirim Reminder"
                                                                        data-tipe="expired"
                                                                        data-id_mou="{{ $lkp->id_mou }}"><i
                                                                            class="fas fa-paper-plane"></i></button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="col-12">
                                    <div class="d-flex align-items-center justify-content-end mb-2">
                                        <div class="d-flex align-items-center" id="btnDatatable">
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
                                        </div>

                                        <!-- Filter Collapse -->
                                    </div>
                                </div> --}}

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
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
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

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var getData = "{{ route('reminder.getData') }}"
    </script>
    <script src="{{ asset('js/reminder/index.js') }}"></script>
@endpush
