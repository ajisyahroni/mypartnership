@extends('layouts.app')
@push('styles')
@endpush
@section('contents')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="content pt-4">
                <!-- Card Line 1 -->
                <div class="row">
                    <!-- Skor Instansi Card -->
                    <div class="col-xl-6">
                        <div class="card shadow-sm border-0 rounded-3">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-light">
                                    <i class="fas fa-trophy me-2"></i> Skor Prodi 1 Tahun Terakhir
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <button class="btn btn-primary bg-primary"
                                    onclick="detailSkor('ProdiScore','Detail Skor Prodi','1')"
                                    style="font-size: 20px;">{{ @$ProdiScore1 }}</button>
                            </div>
                        </div>
                    </div>

                    <!-- Skor Total Card -->
                    <div class="col-xl-6">
                        <div class="card shadow-sm border-0 rounded-3">
                            <div
                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-light">
                                    <i class="fas fa-star me-2"></i> Skor Rata Rata 1 Tahun Terakhir
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <button class="btn btn-primary bg-primary"
                                    onclick="detailSkor('AverageScore','Detail Skor Rata-rata','1')"
                                    style="font-size: 20px;">{{ round(@$AverageScore1, 2) }}</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card shadow-sm border-0 rounded-3">
                            <div
                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-light">
                                    <i class="fas fa-trophy me-2"></i> Skor Prodi 1 Tahun Terakhir
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <button class="btn btn-primary bg-primary"
                                    onclick="detailSkor('ProdiScore','Detail Skor Prodi','5')"
                                    style="font-size: 20px;">{{ @$ProdiScore5 }}</button>
                            </div>
                        </div>
                    </div>

                    <!-- Skor Total Card -->
                    <div class="col-xl-6">
                        <div class="card shadow-sm border-0 rounded-3">
                            <div
                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-light">
                                    <i class="fas fa-star me-2"></i> Skor Rata Rata 1 Tahun Terakhir
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <button class="btn btn-primary bg-primary"
                                    onclick="detailSkor('AverageScore','Detail Skor Rata-rata','5')"
                                    style="font-size: 20px;">{{ round(@$AverageScore5, 2) }}</button>
                            </div>
                        </div>
                    </div>
                    <!-- Chart -->
                    <div class="col-xl-6">
                        <!-- Implementasi Kerjasama Lembaga -->
                        <div class="card shadow-sm border-0 rounded-3">
                            <div
                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-light">
                                    <i class="fas fa-handshake me-2"></i> Sebaran Kerja Sama
                                </h6>
                            </div>

                            <div class="card-body">
                                <div class="row text-center">
                                    <!-- Dalam Negeri -->
                                    <div class="col-md-4 mb-4">
                                        <div class="bg-light rounded shadow-sm p-4 h-100">
                                            <div class="mb-3 text-primary">
                                                <i class="fas fa-building fa-2x"></i>
                                            </div>
                                            <h4 class="fw-bold mb-1">2</h4>
                                            <div class="text-muted mb-2">Aktif Dalam Negeri</div>
                                            <a href="https://mypartnership.ums.ac.id/kerjasama-dalam-negeri"
                                                class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                                        </div>
                                    </div>

                                    <!-- Luar Negeri -->
                                    <div class="col-md-4 mb-4">
                                        <div class="bg-light rounded shadow-sm p-4 h-100">
                                            <div class="mb-3 text-info">
                                                <i class="fas fa-globe-asia fa-2x"></i>
                                            </div>
                                            <h4 class="fw-bold mb-1">4</h4>
                                            <div class="text-muted mb-2">Aktif Luar Negeri</div>
                                            <a href="https://mypartnership.ums.ac.id/kerjasama-luar-negeri"
                                                class="btn btn-sm btn-outline-info">Lihat Detail</a>
                                        </div>
                                    </div>

                                    <!-- Dalam & Luar Negeri -->
                                    <div class="col-md-4 mb-4">
                                        <div class="bg-light rounded shadow-sm p-4 h-100">
                                            <div class="mb-3 text-indigo">
                                                <i class="fas fa-handshake fa-2x"></i>
                                            </div>
                                            <h4 class="fw-bold mb-1">12</h4>
                                            <div class="text-muted mb-2">Aktif Produktif Dalam & Luar Negeri</div>
                                            <!-- Tombol non-link karena tidak ada URL -->
                                            <button class="btn btn-sm btn-outline-secondary">Info Umum</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Implementasi Kerjasama Lembaga -->
                        <div class="card shadow-sm border-0 rounded-3">
                            <div
                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-light">
                                    <i class="fas fa-handshake me-2"></i> Lembaga Aktif
                                </h6>
                            </div>

                            <div class="card-body py-3">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive" style="overflow-x: auto;">
                                            <table class="table table-hover align-middle custom-table" id="dataTable">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Lembaga</th>
                                                        <th>Jumlah Aktivitas</th>
                                                        <th>Nilai Akumulasi Aktivitas</th>
                                                        <th>Opsi</th>
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
                    <!-- Chart -->
                    <!-- Notifikasi Aktivitas Kerjasama -->
                    <div class="col-xl-6">

                        <!-- Aktivitas Sistem Kerjasama -->
                        <div class="card shadow-sm border-0 rounded-3">
                            <div
                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-light">
                                    <i class="fas fa-handshake me-2"></i> Aktivitas
                                </h6>
                                <div class="list-icons">
                                    <a href="https://mypartnership.ums.ac.id/lihat-semua-aktivitas"
                                        class="btn bg-warning legitRipple">
                                        <i class="icon-transmission mr-2"></i>Lihat Semua Aktivitas
                                    </a>
                                </div>
                            </div>

                            <div class="card-body px-3 py-4"
                                style="max-height: 450px; overflow-y: auto; background-color: #ffffff;">
                                <!-- Notifikasi Aktivitas -->
                                <div
                                    class="d-flex align-items-start bg-primary text-white shadow rounded-3 p-3 mb-3 position-relative">
                                    <div class="badge rounded-circle bg-white text-primary me-3 d-flex align-items-center justify-content-center fw-bold"
                                        style="width: 32px; height: 32px;">
                                        1
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">
                                            Kerja Sama Rumah Sakit Jiwa Prof. Dr. Soerojo Magelang
                                        </div>
                                        <div class="small">
                                            telah ditambahkan dan dapat diakuisisi di halaman akuisisi kerjasama
                                        </div>
                                        <div class="small mt-1">2025-03-26 15:59:01</div>
                                    </div>
                                    <button type="button"
                                        class="btn-close btn-close-white position-absolute top-0 end-0 mt-2 me-2"
                                        aria-label="Close"></button>
                                </div>

                                <!-- Notifikasi Kedua -->
                                <div
                                    class="d-flex align-items-start bg-primary text-white shadow rounded-3 p-3 mb-3 position-relative">
                                    <div class="badge rounded-circle bg-white text-primary me-3 d-flex align-items-center justify-content-center fw-bold"
                                        style="width: 32px; height: 32px;">
                                        2
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">
                                            Kerja Sama Universitas Hang Tuah (UHT)
                                        </div>
                                        <div class="small">
                                            telah ditambahkan dan dapat diakuisisi di halaman akuisisi kerjasama
                                        </div>
                                        <div class="small mt-1">2025-03-25 09:38:54</div>
                                    </div>
                                    <button type="button"
                                        class="btn-close btn-close-white position-absolute top-0 end-0 mt-2 me-2"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                        <!-- /Aktivitas Sistem Kerjasama -->
                    </div>
                    <!-- Notifikasi Aktivitas Kerjasama -->

                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-detail" aria-labelledby="DetailLabel" aria-hidden="true">
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

        <!-- Container-fluid Ends-->
    </div>

    <script>
        function detailSkor($type, judul, tahun) {
            $("#modal-detail #DetailLabel").html(judul);
            $("#modal-detail").modal("show");

            $("#konten-detail").html(`
                    <div class="d-flex justify-content-center my-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);

            $.ajax({
                url: "{{ route('home.detailSkor') }}",
                type: 'get',
                data: {
                    type: $type,
                    tahun: tahun,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $("#konten-detail").html(response.view);
                },
                error: function(xhr, status, error) {}
            });
        }
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
@endsection
