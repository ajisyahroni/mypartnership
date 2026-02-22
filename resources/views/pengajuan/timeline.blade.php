@if (@$dataPengajuan->stats_kerma == 'Lapor Kerma')
    <style>
        /* Mobile (≤ 768px) */
        @media screen and (max-width: 768px) {
            .timeline::before {
                width: 190% !important;
            }
        }

        /* Tablet (769px - 1024px) */
        @media screen and (min-width: 769px) and (max-width: 1024px) {
            .timeline::before {
                width: 80% !important;
            }
        }

        .timeline::before {
            width: 55%;
            left: 130;
        }
    </style>
    <div class="timeline">
        <!-- Step 1 -->
        <div class="timeline-step up active">
            <span class="badge bg-success">Kerja Sama
                Diajukan</span>
            <p class="fw-bold">Oleh : {{ @$dataPengajuan->getPengusul->name }}</p>
            <p class="text-muted">{{ TanggalIndonesia(@$dataPengajuan->timestamp) }}</p>
            <div class="circle">
                <i class="fas fa-check"></i>
            </div>
        </div>

        @if ($dataPengajuan->tgl_verifikasi_kaprodi == '0000-00-00 00:00:00' || empty($dataPengajuan->tgl_verifikasi_kaprodi))
            <!-- Step 2 -->
            <div class="timeline-step down ">
                <div class="circle"><i class="fas fa-user-check"></i></div>
                <p class="fw-bold">Verifikasi Kaprodi</p>
                <p class="text-muted">Menunggu diverifikasi</p>
                <span class="badge bg-danger">Menunggu Verifikasi</span>
            </div>
        @else
            <!-- Step 2 -->
            <div class="timeline-step down active">
                <div class="circle">
                    <i class="fas fa-check"></i>
                </div>
                <p class="fw-bold">Oleh : {{ @$dataPengajuan->getVerifikator->name }}</p>
                <p class="text-muted">{{ TanggalIndonesia(@$dataPengajuan->tgl_verifikasi_kaprodi) }}</p>
                <span class="badge bg-success">Pengajuan Telah di Verifikasi Kaprodi</span>
            </div>
        @endif

        @if ($dataPengajuan->tgl_verifikasi_publish == '0000-00-00 00:00:00' || empty($dataPengajuan->tgl_verifikasi_publish))
            <!-- Step 7 -->
            <div class="timeline-step up">
                <span class="badge bg-danger">Menunggu Verifikasi</span>
                <p class="fw-bold">Verifikasi Dokumen</p>
                <p class="text-muted">Menunggu diverifikasi</p>
                <div class="circle"><i class="fas fa-file"></i>
                </div>
            </div>
        @else
            <!-- Step 7 -->
            <div class="timeline-step up active">
                <span class="badge bg-success">Telah
                    di Verifikasi</span>
                <p class="fw-bold">Oleh {{ @$dataPengajuan->getVerifyDokumen->name }}</p>
                <p class="text-muted">{{ TanggalIndonesia(@$dataPengajuan->tgl_verifikasi_publish) }}</p>
                <div class="circle"><i class="fas fa-check"></i>
                </div>
            </div>
        @endif

        @if ($dataPengajuan->tgl_selesai == '0000-00-00 00:00:00' || empty($dataPengajuan->tgl_selesai))
            <!-- Step 8 -->
            <div class="timeline-step down">
                <div class="circle"><i class="fas fa-pen"></i>
                </div>
                <p class="fw-bold">Finalisasi</p>
                <p class="text-muted">Dokumen resmi belum
                    publish</p>
                <span class="badge bg-danger">Belum
                    selesai</span>
            </div>
        @else
            <!-- Step 8 -->
            <div class="timeline-step down active">
                <div class="circle"><i class="fas fa-check"></i>
                </div>
                <p class="fw-bold">Finalisasi</p>
                <p class="text-muted">{{ TanggalIndonesia(@$dataPengajuan->tgl_selesai) }}</p>
                <span class="badge bg-success">Dokumen resmi telah
                    publish</span>
            </div>
        @endif
    </div>
@else
    <style>
        /* Mobile (≤ 768px) */
        @media screen and (max-width: 768px) {
            .timeline::before {
                width: 600% !important;
                left: 130;
            }
        }

        /* Tablet (769px - 1024px) */
        @media screen and (min-width: 769px) and (max-width: 1024px) {
            .timeline::before {
                width: 210% !important;
            }
        }

        /* Tablet (769px - 1024px) */
        @media screen and (min-width: 1440px) {
            .timeline::before {
                width: 100% !important;
            }
        }

        .timeline::before {
            width: 120%;
            left: 130;
        }
    </style>
    <div class="timeline">
        <!-- Step 1 -->
        <div class="timeline-step up active">
            <span class="badge bg-success">Kerja Sama
                Diajukan</span>
            <p class="fw-bold">Oleh : {{ @$dataPengajuan->getPengusul->name }}</p>
            <p class="text-muted">{{ TanggalIndonesia(@$dataPengajuan->timestamp) }}</p>
            <div class="circle">
                <i class="fas fa-check"></i>
            </div>
        </div>

        @if ($dataPengajuan->tgl_verifikasi_kaprodi == '0000-00-00 00:00:00' || empty($dataPengajuan->tgl_verifikasi_kaprodi))
            <!-- Step 2 -->
            <div class="timeline-step down ">
                <div class="circle"><i class="fas fa-user-check"></i></div>
                <p class="fw-bold">Verifikasi Kaprodi</p>
                <p class="text-muted">Menunggu diverifikasi</p>
                <span class="badge bg-danger">Menunggu Verifikasi</span>
            </div>
        @else
            <!-- Step 2 -->
            <div class="timeline-step down active">
                <div class="circle">
                    <i class="fas fa-check"></i>
                </div>
                <p class="fw-bold">Oleh : {{ @$dataPengajuan->getVerifikator->name }}</p>
                <p class="text-muted">{{ TanggalIndonesia(@$dataPengajuan->tgl_verifikasi_kaprodi) }}</p>
                <span class="badge bg-success">Pengajuan Telah di Verifikasi Kaprodi</span>
            </div>
        @endif

        @if ($dataPengajuan->tgl_draft_upload == '0000-00-00 00:00:00' || empty($dataPengajuan->tgl_draft_upload))
            <!-- Step 3 -->
            <div class="timeline-step up">
                <span class="badge bg-danger">Draft belum
                    diunggah</span>
                <p class="fw-bold">Upload Draft</p>
                <p class="text-muted">Menunggu unggahan</p>
                <div class="circle"><i class="fas fa-upload"></i></div>
            </div>
        @else
            <!-- Step 3 -->
            <div class="timeline-step up active">
                <span class="badge bg-success">Draft telah
                    diunggah</span>
                <p class="fw-bold">Upload Draft</p>
                <p class="text-muted">{{ TanggalIndonesia(@$dataPengajuan->tgl_draft_upload) }}</p>
                <div class="circle"><i class="fas fa-check"></i></div>
            </div>
        @endif

        @if ($dataPengajuan->tgl_verifikasi_kabid == '0000-00-00 00:00:00' || empty($dataPengajuan->tgl_verifikasi_kabid))
            <!-- Step 4 -->
            <div class="timeline-step down">
                <div class="circle"><i class="fas fa-user-check"></i></div>
                <p class="fw-bold">Verifikasi Admin</p>
                <p class="text-muted">Menunggu diverifikasi</p>
                <span class="badge bg-danger">Menunggu Verifikasi</span>
            </div>
        @else
            <!-- Step 4 -->
            <div class="timeline-step down active">
                <div class="circle"><i class="fas fa-check"></i></div>
                <p class="fw-bold">Oleh {{ @$dataPengajuan->getKabid->name }}</p>
                <p class="text-muted">{{ TanggalIndonesia(@$dataPengajuan->tgl_verifikasi_kabid) }}</p>
                <span class="badge bg-success">Telah
                    di Verifikasi BKUI</span>
            </div>
        @endif

        @if ($dataPengajuan->tgl_verifikasi_user == '0000-00-00 00:00:00' || empty($dataPengajuan->tgl_verifikasi_user))
            <!-- Step 5 -->
            <div class="timeline-step up">
                <span class="badge bg-danger">Menunggu Verifikasi</span>
                <p class="fw-bold">Verifikasi Pengusul</p>
                <p class="text-muted">Menunggu diverifikasi</p>
                <div class="circle"><i class="fas fa-user-check"></i></div>
            </div>
        @else
            <!-- Step 5 -->
            <div class="timeline-step up active">
                <span class="badge bg-success">Telah
                    di Verifikasi Pengusul</span>
                <p class="fw-bold">Oleh {{ @$dataPengajuan->getPengusul->name }}</p>
                <p class="text-muted">{{ TanggalIndonesia(@$dataPengajuan->tgl_verifikasi_user) }}</p>
                <div class="circle"><i class="fas fa-check"></i></div>
            </div>
        @endif


        @if ($dataPengajuan->tgl_req_ttd == '0000-00-00 00:00:00' || empty($dataPengajuan->tgl_req_ttd))
            <div class="timeline-step down">
                <div class="circle"><i class="fas fa-upload"></i>
                </div>
                <p class="fw-bold">Upload Dokumen</p>
                <p class="text-muted">Dokumen Resmi Belum diupload</p>
                <span class="badge bg-danger">Belum
                    diUpload</span>
            </div>
        @else
            <div class="timeline-step down active">
                <div class="circle"><i class="fas fa-check"></i>
                </div>
                <p class="fw-bold">Dokumen Resmi Sudah diupload</p>
                <p class="text-muted">{{ TanggalIndonesia(@$dataPengajuan->tgl_req_ttd) }}</p>
                <span class="badge bg-success">Sudah
                    diUpload</span>
            </div>
        @endif


        @if ($dataPengajuan->tgl_verifikasi_publish == '0000-00-00 00:00:00' || empty($dataPengajuan->tgl_verifikasi_publish))
            <!-- Step 7 -->
            <div class="timeline-step up">
                <span class="badge bg-danger">Menunggu Verifikasi</span>
                <p class="fw-bold">Verifikasi Dokumen</p>
                <p class="text-muted">Menunggu diverifikasi</p>
                <div class="circle"><i class="fas fa-file"></i>
                </div>
            </div>
        @else
            <!-- Step 7 -->
            <div class="timeline-step up active">
                <span class="badge bg-success">Telah
                    di Verifikasi</span>
                <p class="fw-bold">Oleh {{ @$dataPengajuan->getVerifyDokumen->name }}</p>
                <p class="text-muted">{{ TanggalIndonesia(@$dataPengajuan->tgl_verifikasi_publish) }}</p>
                <div class="circle"><i class="fas fa-check"></i>
                </div>
            </div>
        @endif

        @if ($dataPengajuan->tgl_selesai == '0000-00-00 00:00:00' || empty($dataPengajuan->tgl_selesai))
            <!-- Step 8 -->
            <div class="timeline-step down">
                <div class="circle"><i class="fas fa-pen"></i>
                </div>
                <p class="fw-bold">Finalisasi</p>
                <p class="text-muted">Dokumen resmi belum
                    publish</p>
                <span class="badge bg-danger">Belum
                    selesai</span>
            </div>
        @else
            <!-- Step 8 -->
            <div class="timeline-step down active">
                <div class="circle"><i class="fas fa-check"></i>
                </div>
                <p class="fw-bold">Finalisasi</p>
                <p class="text-muted">{{ TanggalIndonesia(@$dataPengajuan->tgl_selesai) }}</p>
                <span class="badge bg-success">Dokumen resmi telah
                    publish</span>
            </div>
        @endif
    </div>
@endif
