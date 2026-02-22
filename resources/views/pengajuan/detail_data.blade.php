<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="detail-verifikasi-modal-tab" data-bs-toggle="tab" data-bs-target="#detail-verifikasi-modal" type="button"
            role="tab" aria-controls="detail-verifikasi-modal" aria-selected="true">
            Detail Kerja Sama
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="detail-dokumen-verifikasi-tab" data-bs-toggle="tab" data-bs-target="#detail-dokumen-verifikasi" type="button"
            role="tab" aria-controls="detail-dokumen-verifikasi" aria-selected="false">
            Lihat Dokumen Kerja Sama
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline" type="button"
            role="tab" aria-controls="timeline" aria-selected="false">
            Proses Pengajuan Dokumen
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content mt-3" id="myTabContent">
    <!-- Tab Modal Read -->
    <div class="tab-pane fade show active" id="detail-verifikasi-modal" role="tabpanel" aria-labelledby="detail-verifikasi-modal-tab">
        <div id="modal_read" tyle="max-height: 450px; overflow-y:auto; padding-right: 5px;">
            <table class="table table-bordered table-hover" id="detailKerma" style="font-size: 12px;">
                <tbody>
                    <tr>
                        <td style="white-space:nowrap;"><b>Status Ajuan :</b></td>
                        <td>
                            <b>{{ @$dataPengajuan->stats_kerma }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Kerja Sama Tingkat :</b></td>
                        <td>
                            <b>{{ @$dataPengajuan->prodi_unit }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Jenis Kerja Sama :</b></td>
                        <td>
                            {{ @$dataPengajuan->jenis_kerjasama }} </td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Nomor Dokumen :</b></td>
                        <td>
                            {{ @$dataPengajuan->no_dokumen }} </td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Kerja Sama :</b></td>
                        <td>
                            {{ @$dataPengajuan->dn_ln }} </td>
                    </tr>
                    @if (@$dataPengajuan->dn_ln == 'Dalam Negeri')
                        <tr>
                            <td style="white-space:nowrap;"><b>Wilayah Mitra :</b></td>
                            <td>
                                <b>{{ @$dataPengajuan->wilayah_mitra }}</b>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td style="white-space:nowrap;"><b>Negara Mitra :</b></td>
                            <td>
                                <b>{{ @$dataPengajuan->negara_mitra }}</b>
                            </td>
                        </tr>
                    @endif
                    @if (@$dataPengajuan->jenis_institusi == 'Lain-lain')
                        <tr>
                            <td style="white-space:nowrap;"><b>Jenis Institusi Mitra :</b></td>
                            <td>
                                {{ @$dataPengajuan->jenis_institusi_lain }} </td>
                        </tr>
                    @else
                        <tr>
                            <td style="white-space:nowrap;"><b>Jenis Institusi Mitra :</b></td>
                            <td>
                                {{ @$dataPengajuan->jenis_institusi }} </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="white-space:nowrap;"><b>Nama Institusi Mitra :</b></td>
                        <td><b>{{ @$dataPengajuan->nama_institusi }}</b></td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Alamat Institusi Mitra :</b></td>
                        <td>{{ @$dataPengajuan->alamat_mitra }}</td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Penandatangan Internal :</b></td>
                        <td>{{ @$dataPengajuan->ttd_internal }}</td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Jabatan Internal :</b></td>
                        <td>{{ @$dataPengajuan->lvl_internal }}</td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Penandatangan Eksternal :</b></td>
                        <td>{{ @$dataPengajuan->ttd_eksternal }}</td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Jabatan Eksternal :</b></td>
                        <td>{{ @$dataPengajuan->lvl_eksternal }}</td>
                    </tr>
                    <tr>
                        <td style="white-space: nowrap; vertical-align: top;"><b>PIC Kegiatan Internal:</b></td>
                        <td>
                            <div><b>Nama:</b> {{ @$dataPengajuan->nama_internal_pic ?? '-' }}</div>
                            <div><b>Jabatan:</b> {{ @$dataPengajuan->lvl_internal_pic ?? '-' }}</div>
                            <div><b>No. Telp:</b> {{ @$dataPengajuan->telp_internal_pic ?? '-' }}</div>
                            <div><b>Email:</b> {{ @$dataPengajuan->email_internal_pic ?? '-' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="white-space: nowrap; vertical-align: top;"><b>PIC Kegiatan Mitra:</b></td>
                        <td>
                            <div><b>Nama:</b> {{ @$dataPengajuan->nama_eksternal_pic ?? '-' }}</div>
                            <div><b>Jabatan:</b> {{ @$dataPengajuan->lvl_eksternal_pic ?? '-' }}</div>
                            <div><b>No. Telp:</b> {{ @$dataPengajuan->telp_eksternal_pic ?? '-' }}</div>
                            <div><b>Email:</b> {{ @$dataPengajuan->email_eksternal_pic ?? '-' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Kontribusi Kerja Sama :</b></td>
                        @if (@$dataPengajuan->kontribusi != 'Lain-lain')
                            <td>{{ @$dataPengajuan->kontribusi }}</td>
                        @else
                            <td>{{ @$dataPengajuan->kontribusi_lain }}</td>
                        @endif
                    </tr>
                    
                    @if ($dataPengajuan->periode_kerma == 'notknown')
                        <tr>
                            <td style="white-space:nowrap;"><b>Kerja Sama Mulai :</b></td>
                            <td>{{ 
                             $dataPengajuan->periode_kerma == 'bydoc' ? Tanggal_Indo($dataPengajuan->mulai) : ($dataPengajuan->mulai == null || $dataPengajuan->mulai == '0000-00-00' ? Tanggal_Indo($dataPengajuan->awal) : Tanggal_Indo($dataPengajuan->mulai))
                              }}</td>
                        </tr>
                        <tr>
                            <td style="white-space:nowrap;"><b>Kerja Sama Berakhir :</b></td>
                            <td>
                                Tidak Ada Batasan
                            </td>
                        </tr>
                        <tr>
                            <td style="white-space:nowrap;"><b>Status Kerja Sama :</b></td>
                            <td>{{ @$dataPengajuan->status_mou }}</td>
                        </tr>
                    @else
                    <tr>
                        <td style="white-space:nowrap;"><b>Kerja Sama Mulai :</b></td>
                        <td>{{ Tanggal_Indo(@$dataPengajuan->mulai) }}</td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Kerja Sama Berakhir :</b></td>
                        <td>{{ Tanggal_Indo(@$dataPengajuan->selesai) }}</td>
                    </tr>
                    @endif

                    @if ($dokumenPerpanjangan)
                        <tr>
                            <td style="white-space:nowrap;"><b>Dokumen Perpanjangan:</b></td>
                            <td>
                                {{ $dokumenPerpanjangan->nama_institusi }}, Tanggal Selesai : {{ $dokumenPerpanjangan->selesai }}
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td style="white-space:nowrap;"><b>Status Kerja Sama:</b></td>
                        <td>
                            {!! $dataPengajuan->statusPengajuanKerjaSama() !!}
                            @if ($dataPengajuan->status_kerma)
                                <span class="badge badge-sm bg-success" style="font-size: 10px!important;">{{ $dataPengajuan->status_kerma }}</span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td style="white-space:nowrap;"><b>File Kerja Sama :</b></td>
                        <td>
                            @if ($dataPengajuan->file_mou != null)
                                <a href="{{ $fileUrl }}" class="btn btn-primary btn-icon" target="_blank"
                                    data-title-tooltip="Unduh File Kerja Sama"
                                    data-original-data-title-tooltip="Unduh File Kerja Sama"><i
                                        class="fa-solid fa-download"></i></a>
                            @else
                                Belum Ada Dokumen
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="tab-pane fade" id="detail-dokumen-verifikasi" role="tabpanel" aria-labelledby="detail-dokumen-verifikasi-tab">
        <div id="modal-read-dokumen" style="max-height: 450px; overflow-y:auto; padding-right: 5px;">
            <table class="table table-bordered table-hover" style="font-size: 12px;">
                <tbody>
                    <tr>
                        <td style="white-space:nowrap;"><b>Dokumen File SK Pendirian :</b></td>
                        <td>
                            @if ($dataPengajuan->file_sk_mitra != null && $dataPengajuan->file_sk_mitra != '')
                                <a href="{{ $fileUrlMitra }}" class="btn btn-primary btn-icon mb-2" target="_blank"
                                    data-title-tooltip="Unduh File Kerja Sama"
                                    data-original-data-title-tooltip="Unduh File  Kerja Sama"><i
                                        class="fa-solid fa-download"></i></a>
                            @else
                                Belum Ada Dokumen
                            @endif
                            <div class="iframe-mitra"></div>
                        </td>
                    </tr>
                    @if ($dataPengajuan->stats_kerma == 'Ajuan Baru')
                        <tr>
                            <td style="white-space:nowrap;"><b>Dokumen Draft Kerja Sama :</b></td>
                            <td>
                                @if ($dataPengajuan->file_ajuan != null)
                                    <a href="{{ $fileUrlDraft }}" class="btn btn-primary btn-icon mb-2" target="_blank"
                                        data-title-tooltip="Unduh File Draft Kerja Sama"
                                        data-original-data-title-tooltip="Unduh File Draft Kerja Sama"><i
                                            class="fa-solid fa-download"></i></a>
                                @else
                                    Belum Ada Dokumen
                                @endif
                                <div class="iframe-draft"></div>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="white-space:nowrap;"><b>Dokumen Kerja Sama :</b></td>
                        <td>
                            @if ($dataPengajuan->file_mou != null)
                                <a href="{{ $fileUrl }}" class="btn btn-primary btn-icon mb-2" target="_blank"
                                    data-title-tooltip="Unduh File Kerja Sama"
                                    data-original-data-title-tooltip="Unduh File  Kerja Sama"><i
                                        class="fa-solid fa-download"></i></a>
                            @else
                                Belum Ada Dokumen
                            @endif
                            <div class="iframe-mou"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Tab Timeline -->
    <div class="tab-pane fade" id="timeline" role="tabpanel" aria-labelledby="timeline-tab">
        @include('pengajuan.timeline', compact('dataPengajuan'))
    </div>

</div>

<script>
    var fileUrlMoU = @json($fileUrl);
    var fileUrlDraft = @json($fileUrlDraft);
    var fileUrlMitra = @json($fileUrlMitra);

    $(document).ready(function(){
        
        function generatePreview(url) {
            if (!url || typeof url !== "string" || url.trim() === "") {
                return `<div class="alert alert-secondary text-center mt-3">Tidak ada file untuk ditampilkan.</div>`;
            }

            // if (url.startsWith("/")) {
            //     url = BASE_URL + url;
            // }
            
            const ext = url.split(".").pop().toLowerCase();
    
            if (ext === "pdf") {
                if (url.startsWith("https://")) {
                    return `<iframe
                        src="https://docs.google.com/gview?url=${encodeURIComponent(url)}&embedded=true"
                        style="width:100%;height:500px;border:none;">
                    </iframe>`;
                }

                return `<iframe
                    src="${url}"
                    width="100%"
                    height="500px"
                    style="border:none;">
                </iframe>`;
            }

    
            if (["png", "jpg", "jpeg", "webp"].includes(ext)) {
                return `<div class="text-center"><img src="${url}" class="img-fluid" style="max-height:500px;"></div>`;
            }
    
            if (url.startsWith("/")) {
                url = BASE_URL + url;
            }

            if (["doc", "docx"].includes(ext)) {
                return `<iframe src="https://docs.google.com/gview?url=${encodeURIComponent(url)}&embedded=true"
                            style="width:100%;height:500px;border:none;"></iframe>`;
            }
    
            return `<div class="alert alert-warning text-center mt-3">Format file <b>.${ext}</b> tidak didukung.</div>`;
        }
    
        $(".iframe-mou").html( generatePreview(fileUrlMoU) );
        $(".iframe-draft").html( generatePreview(fileUrlDraft) );
        $(".iframe-mitra").html( generatePreview(fileUrlMitra) );
    })
    
</script>