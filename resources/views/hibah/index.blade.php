@extends('layouts.app')

@section('contents')
    <style>
        .icon-circle {
            width: 80px;
            height: 80px;
            border: 2px solid #3f51b5;
            /* Warna biru */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container-fluid.body-konten {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }


        @media (min-width:360px) and (max-width:926px) {
            .hero h2 {
                font-size: 2.5rem !important;
            }
        }
    </style>
    <div class="page-body" style="background-color: #fff;">
        <div class="px-5 py-4 position-relative overflow-hidden">
            <div class="d-flex hero flex-column flex-lg-row align-items-center justify-content-between ">
                <!-- Kiri: Gambar Dekoratif -->
                <div class="flex-shrink-0 mb-4 mb-lg-0 px-lg-5 px-0 ms-md-5 ms-0 scroll-animate" style="max-width: 45%;">
                    <img src="{{ asset('images/dashboard_hibah.png') }}" alt="Dashboard Hibah" class="img-fluid w-100"
                        style="object-fit: cover;">
                </div>


                <!-- Kanan: Konten Text -->
                <div class="ps-lg-5 text-lg-start text-center scroll-animate">
                    <h2 style="font-size: 3.5rem; color: #2f3185;font-weight: 700;">
                        HIBAH <br class="d-none d-md-block">
                        KERJA SAMA <br class="d-none d-md-block">
                        INTERNASIONAL
                    </h2>
                    <p class="mt-3 mb-4 text-muted" style="max-width: 480px;">
                        Dapatkan dukungan hibah untuk mengembangkan kemitraan internasional di bidang
                        pendidikan, riset, dan inovasi bersama institusi global.
                    </p>
                    <a href="{{ route('hibah.tambah') }}" class="btn px-4 py-2 fw-bold"
                        style="background-color: #2f3185; color: white; border-radius: 40px;">
                        AJUKAN HIBAH
                    </a>
                </div>
            </div>
        </div>
        <div class="map-decorator">
            <div class="blue-bg"></div>
        </div>
        <section class="my-5">
            <div class="text-center mb-4 scroll-animate">
                <div class="title-bar"></div>
                <h3 class="title-dashboard">Alur Proses Hibah</h3>
            </div>

            <div class="p-4 rounded shadow-sm bg-light-blue position-relative overflow-auto mx-auto scroll-animate"
                style="max-width: 1100px;">
                <div class="d-flex flex-column align-items-center gap-4">

                    <!-- Baris 1 -->
                    <div class="d-flex flex-row flex-wrap justify-content-center align-items-center gap-3 text-center">
                        <div class="step-box">User Melakukan Pengajuan</div>
                        <div class="arrow-right"></div>
                        <div class="step-box">Verifikasi Kepala<br>Program Studi</div>
                        <div class="arrow-right"></div>
                        <div class="step-box">Verifikasi Admin</div>
                        <div class="arrow-right"></div>
                        <div class="step-box">TTD Kontrak<br>& Upload</div>
                        <div class="arrow-right"></div>
                        <div class="step-box">Proses Pencairan</div>
                    </div>

                    <!-- Baris 2 -->
                    <div class="d-flex flex-row flex-wrap justify-content-center align-items-center gap-3 text-center">
                        <div class="step-box">Selesai</div>
                        <div class="arrow-left"></div>
                        <div class="step-box">Pencairan 2</div>
                        <div class="arrow-left"></div>
                        <div class="step-box">Approval Admin</div>
                        <div class="arrow-left"></div>
                        <div class="step-box">Laporan<br>Pencairan 1</div>
                    </div>

                </div>
            </div>
        </section>

        <div class="modal fade" id="modal-pemberitahuan" aria-labelledby="PemberitahuanLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">

                    <!-- Header -->
                    <div class="modal-header border-0 bg-primary bg-gradient text-white py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                <i class="bi bi-shield-check fs-5"></i>
                            </div>
                            <div>
                                <h6 class="modal-title fw-bold mb-0" id="PemberitahuanLabel">Pemberitahuan Verifikator</h6>
                                <small class="opacity-75" style="font-size: 0.75rem;">MyPartnership</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-3" id="konten-pemberitahuan">
                        
                        <!-- Info Card -->
                        <div class="alert alert-primary bg-opacity-10 border border-primary border-opacity-25 rounded-3 mb-3 py-2">
                            <div class="d-flex align-items-start gap-2">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                    <i class="bi bi-person-badge text-light fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1 small">
                                        <strong>Anda masuk sebagai:</strong> <span class="badge bg-primary px-2 py-1">Verifikator</span>
                                    </p>
                                    <p class="mb-2 text-muted" style="font-size: 0.8rem;">Tanggung jawab Anda:</p>

                                    <div class="d-flex align-items-start gap-2 mb-2">
                                        <i class="bi bi-check-circle-fill text-success" style="font-size: 0.9rem;"></i>
                                        <span style="font-size: 0.8rem;">Memvalidasi <strong>pengajuan hibah kerja sama</strong></span>
                                    </div>

                                    <div class="alert alert-warning bg-opacity-10 border-0 mb-0 py-1 px-2">
                                        <small class="d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                            <span>Pastikan data sesuai sebelum memberikan persetujuan</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Card -->
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body text-center p-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                                    <i class="bi bi-file-earmark-check text-light fs-3"></i>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">Verifikasi Pengajuan</h6>
                                <p class="text-muted mb-2" style="font-size: 0.75rem;">Validasi hibah kerja sama</p>
                                <a href="{{ route('hibah.ajuan') }}" 
                                class="btn btn-primary btn-sm w-100 d-flex justify-content-center align-items-center gap-2 rounded-pill">
                                    <i class="bi bi-arrow-right-circle"></i>
                                    <span class="fw-semibold">Mulai Verifikasi</span>
                                </a>
                            </div>
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="modal-footer border-0 bg-light bg-gradient py-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2 rounded-pill px-3" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i>
                            <span>Tutup</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <style>
            .title-bar {
                width: 60px;
                height: 4px;
                background-color: #f1c40f;
                margin: 0 auto 10px auto;
                display: block;
                border-radius: 2px;
            }

            .title-dashboard {
                color: #2d2f92;
                font-weight: bold;
            }


            .bg-light-blue {
                background-color: #f2faff;
            }

            .step-box {
                background-color: #b8dce7;
                padding: 12px 16px;
                border-radius: 6px;
                font-weight: 600;
                text-align: center;
                color: #1f1f1f;
                min-width: 140px;
                font-size: 14px;
            }

            .arrow-right::after {
                content: '';
                display: inline-block;
                width: 0;
                height: 0;
                border-top: 10px solid transparent;
                border-bottom: 10px solid transparent;
                border-left: 15px solid #777;
            }

            .arrow-left::after {
                content: '';
                display: inline-block;
                width: 0;
                height: 0;
                border-top: 10px solid transparent;
                border-bottom: 10px solid transparent;
                border-right: 15px solid #777;
            }
        </style>

    </div>
    
@endsection

@push('scripts')
<script>
    var roleDashboard = @json(session('current_role'));
    var notif_verifikator = @json($notif_verifikator);
    
    $(document).ready(function() {
        if (roleDashboard == 'verifikator' && notif_verifikator > 0) {
            $("#modal-pemberitahuan").modal('show');
        }
    })
       
</script>
@endpush
