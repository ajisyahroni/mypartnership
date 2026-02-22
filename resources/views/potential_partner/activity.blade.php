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
                                                    class="dropdown-item d-flex align-items-center btn-download-excel">
                                                    <i class="bx bx-spreadsheet me-2"></i> Download Daftar Mitra Potensial
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <span class="mb-3 fs-2">Tabel ini menghimpun data penting tentang calon mitra, yang menyajikan
                                gambaran singkat tentang atribut dan kualifikasi utama mereka. Tabel ini mencakup informasi
                                seperti detail kontak, bidang keahlian, dan pengalaman yang relevan.</span>
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
                                                        <a href="{{ route('potential_partner.tambah') }}"
                                                            class="btn ms-2 btn-sm btn-primary"><i
                                                                class="bx bx-plus me-2"></i> TAMBAH MITRA POTENSIAL</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-hover align-middle custom-table" id="dataTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>Nomor HP</th>
                                                <th>Minat Penelitian</th>
                                                <th>Jabatan</th>
                                                <th>Institusi</th>
                                                <th>Negara</th>
                                                <th>Website</th>
                                                <th>Status Verifikasi</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{-- </div> --}}

                            {{-- </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="modal fade" id="modal-lihat-file" aria-labelledby="DetailLabel" aria-hidden="true">
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
    </div> --}}

    <div class="modal fade" id="modal-view-partner" aria-labelledby="DetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-group"></i> View Partner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="loading" style="display: none;">
                        <div class="d-flex justify-content-center my-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="container px-3 py-3" id="content-view-partner" style="display: none;">
                        <div class="card shadow-sm rounded mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Personal Information</h5>
                                <div class="row gy-3">
                                    <div class="col-md-6">
                                        <label class="text-muted">Name</label>
                                        <div class="text-dark fw-semibold fs-5" id="name"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted">Email</label>
                                        <div class="text-dark fw-semibold fs-5" id="email"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted">Job Title</label>
                                        <div class="text-dark fw-semibold fs-5" id="occupation"></div>
                                    </div>
                                    @if (session('current_role') == 'admin')
                                        <div class="col-md-6">
                                            <label class="text-muted">Phone Number</label>
                                            <div class="text-dark fw-semibold fs-5" id="phonenumber"></div>
                                        </div>
                                    @endif
                                    <div class="col-md-6">
                                        <label class="text-muted">Social Media</label>
                                        <div class="text-dark fw-semibold fs-5" id="socmed"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted">Research Interest</label>
                                        <div class="text-dark fw-semibold fs-5" id="researchint"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm rounded mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Partner Location</h5>
                                <div class="row gy-3">
                                    <div class="col-md-6">
                                        <label class="text-muted">Institution</label>
                                        <div class="text-dark fw-semibold fs-5" id="institution"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted">Country</label>
                                        <div class="text-dark fw-semibold fs-5" id="country_name"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted">Website</label>
                                        <div class="text-dark fw-semibold fs-5" id="website"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted">Address</label>
                                        <div class="text-dark fw-semibold fs-5" id="address"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm rounded">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Upload Image</h5>
                                <div class="row gy-3">
                                    <div class="col-md-6 text-center">
                                        <label class="text-muted">Card Name Side A</label>
                                        <div>
                                            <img src="" id="cardname1show" class="img-fluid img-thumbnail"
                                                alt="" style="display: none; max-height: 250px;">
                                            <span class="fw-bold text-danger" style="font-size: 14px; display: none;"
                                                id="cardname1">Tidak Ada Foto</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-center">
                                        <label class="text-muted">Card Name Side B</label>
                                        <div>
                                            <img src="" id="cardname2show" class="img-fluid img-thumbnail"
                                                alt="" style="display: none; max-height: 250px;">
                                            <span class="fw-bold text-danger" style="font-size: 14px; display: none;"
                                                id="cardname2">Tidak Ada Foto</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
        var getData = "{{ route('potential_partner.getDataActivity') }}";
        let role = @json(session('current_role'));
    </script>
    <script src="{{ asset('js/potential_partner/activity.js') }}"></script>
@endpush
