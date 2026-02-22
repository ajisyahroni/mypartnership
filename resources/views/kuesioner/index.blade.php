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
                                <a href="{{ route('kuesioner.hasilKuesioner') }}" class="btn btn-primary shadow-sm">
                                    <i class="fas fa-chart-bar"></i> Hasil Kuesioner
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
                                                <th>Mitra Kerja Sama</th>
                                                <th>Kontak Mitra</th>
                                                <th>Bentuk Kegiatan</th>
                                                <th>Judul Kuesioner</th>
                                                <th>Template Kuesioner</th>
                                                <th>Waktu Kuesioner dibuat</th>
                                                <th>Status Kuesioner</th>
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
                            <h5 class="modal-title" id="DetailLabel">Detail Implementasi/Kegiatan Kerja Sama</h5>
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
            <div class="modal fade" id="modal-edit" aria-labelledby="editLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editLabel">Edit Kuesioner</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('kuesioner.store') }}" method="post" id="formEditKuesioner">
                            @csrf
                            <div class="modal-body">
                                <div class="konten-edit"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modal-kirim" aria-labelledby="kirimLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="kirimLabel">Kirim Kuesioner</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('kuesioner.kirimEmail') }}" method="post" id="formKirimKuesioner">
                            @csrf
                            <div class="modal-body">
                                <div class="konten-kirim"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Kirim Email</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modal-link" aria-labelledby="linkLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="linkLabel">link Kuesioner</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="konten-link"></div>
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
        var getData = "{{ route('kuesioner.getData') }}"
        var getDetail = "{{ route('kuesioner.getDetail') }}"
        var getEditKuesioner = "{{ route('kuesioner.getEditKuesioner') }}"
        var getLinkKuesioner = "{{ route('kuesioner.getLinkKuesioner') }}"
        var kirimEmail = "{{ route('kuesioner.kirimEmail') }}"
        var getKirimEmail = "{{ route('kuesioner.getKirimEmail') }}"
    </script>
    <script src="{{ asset('js/kuesioner/index.js') }}"></script>
@endpush
