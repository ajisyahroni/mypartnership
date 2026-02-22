        <style>
            .ev_content p img {
                width: 100% !important;
            }

            .ev_content p {
                font-size: 12px !important;
            }

            .ev_content span {
                font-size: 12px !important;
            }
        </style>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="detail-verifikasi-modal-tab" data-bs-toggle="tab" data-bs-target="#detail-verifikasi-modal" type="button"
            role="tab" aria-controls="detail-verifikasi-modal" aria-selected="true">
            Detail Implementasi
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="detail-dokumen-verifikasi-tab" data-bs-toggle="tab" data-bs-target="#detail-dokumen-verifikasi" type="button"
            role="tab" aria-controls="detail-dokumen-verifikasi" aria-selected="false">
            Lihat Dokumen Implementasi
        </button>
    </li>
</ul>

<div class="tab-content mt-3" id="myTabContent">
    <!-- Tab Modal Read -->
    <div class="tab-pane fade show active" id="detail-verifikasi-modal" role="tabpanel" aria-labelledby="detail-verifikasi-modal-tab">
        <div id="modal_read" tyle="max-height: 450px; overflow-y:auto; padding-right: 5px;">
            <table class="table table-bordered table-hover" id="detailKerma" style="font-size: 12px;">
                <tbody>
                    <tr>
                        <td style="white-space:nowrap;"><b>Pelaksana :</b></td>
                        <td>
                            <b>{{ @$dataImplementasi->pelaksana_prodi_unit }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Judul Kegiatan :</b></td>
                        <td>
                            {{ @$dataImplementasi->judul != 'Lain-lain' ? @$dataImplementasi->judul : @$dataImplementasi->judul_lain }}
                        </td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Bentuk Kerja Sama :</b></td>
                        <td>
                            {{ @$dataImplementasi->bentuk_kegiatan != 'Lain-lain' ? @$dataImplementasi->bentuk_kegiatan : @$dataImplementasi->bentuk_kegiatan_lain }}
                        </td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Dokumen MoU :</b></td>
                        <td><b>{{ @$dataImplementasi->bentuk_kegiatan }}</b></td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Kategori :</b></td>
                        <td>{{ @$dataImplementasi->category }}</td>
                    </tr>
                    <tr>
                    {{-- <tr>
                        <td style="white-space:nowrap;"><b>Dokumen Bukti Implementasi Kerja Sama :</b></td>
                        <td>
                            @if ($dataImplementasi->file_imp != null)
                                @php
                                    $url = getDocumentUrl($dataImplementasi->file_imp, 'file_imp');
                                    $ext = pathinfo($url, PATHINFO_EXTENSION);
                                @endphp
                                <a href="{{ $url }}" class="btn btn-primary btn-icon mb-2" target="_blank"
                                    data-title-tooltip="Unduh File Implementasi">
                                    <i class="fa-solid fa-download"></i>
                                </a>

                                @if (strtolower($ext) === 'pdf')
                                    <iframe src="{{ $url }}" width="100%" height="500px"
                                        style="border: none;" frameborder="0"></iframe>
                                @elseif (strtolower($ext) === 'docx')
                                    <iframe
                                        src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($url) }}"
                                        width="100%" height="500px" style="border: none;" frameborder="0"></iframe>
                                @else
                                    <p class="text-muted">Format file tidak didukung untuk preview.</p>
                                @endif
                            @else
                                <p class="text-danger">Belum Ada Dokumen</p>
                            @endif
                        </td>
                    </tr> --}}

                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Deskripsi Pelaksanaan :</b></td>
                        <td class="ev_content">{!! @$dataImplementasi->deskripsi_singkat ?? 'Tidak Ada' !!}</td>
                    </tr>
                    {{-- <tr>
                        <td style="white-space:nowrap;"><b>Bukti Pelaksanaan :</b></td>
                        <td class="ev_content">{!! @$dataImplementasi->ev_content ?? 'Tidak Ada' !!}</td>
                    </tr> --}}
                    {{-- <tr>
                        <td style="white-space:nowrap;">
                            <b>Dokumen IKU 6 :</b>
                        </td>
                        <td>
                            @if (@$dataImplementasi->file_ikuenam != '')
                                <a href="{{ getDocumentUrl(@$dataImplementasi->file_ikuenam, 'file_ikuenam') }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-download me-2"></i> Unduh Dokumen IKU 6
                                </a>
                            @else
                                Belum Upload
                            @endif
                        </td>
                    </tr> --}}
                    <tr>
                        <td style="white-space: nowrap; vertical-align: top;"><b>PIC Kegiatan Internal:</b></td>
                        <td>
                            <div><b>Nama:</b> {{ @$dataImplementasi->nama_pic_internal ?? '-' }}</div>
                            <div><b>Jabatan:</b> {{ @$dataImplementasi->jabatan_pic_internal ?? '-' }}</div>
                            <div><b>No. Telp:</b> {{ @$dataImplementasi->telp_pic_internal ?? '-' }}</div>
                            <div><b>Email:</b> {{ @$dataImplementasi->email_pic_internal ?? '-' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="white-space: nowrap; vertical-align: top;"><b>PIC Kegiatan Mitra:</b></td>
                        <td>
                            <div><b>Nama:</b> {{ @$dataImplementasi->nama_pic_kegiatan ?? '-' }}</div>
                            <div><b>Jabatan:</b> {{ @$dataImplementasi->jabatan_pic_kegiatan ?? '-' }}</div>
                            <div><b>No. Telp:</b> {{ @$dataImplementasi->telp_pic_kegiatan ?? '-' }}</div>
                            <div><b>Email:</b> {{ @$dataImplementasi->pic_kegiatan ?? '-' }}</div>
                        </td>
                    </tr>

                    @if (@$dataImplementasi->link_pub_internal != null)
                        <tr>
                            <td style="white-space:nowrap;"><b>Link Publikasi Internal :</b></td>
                            <td>
                                <a href="{{ @$dataImplementasi->link_pub_internal }}" class="btn btn-secondary btn-sm"
                                    target="_blank">
                                    <i class="bx bx-link-external"></i> Menuju Link
                                </a>
                            </td>
                        </tr>
                    @endif

                    @if (@$dataImplementasi->link_pub_eksternal != null)
                        <tr>
                            <td style="white-space:nowrap;"><b>Link Publikasi Eksternal :</b></td>
                            <td>
                                <a href="{{ @$dataImplementasi->link_pub_eksternal }}" class="btn btn-info btn-sm"
                                    target="_blank">
                                    <i class="bx bx-link-external"></i> Menuju Link
                                </a>
                            </td>
                        </tr>
                    @endif

                    @if (session('current_role') == 'admin' || (session('current_role') == 'user' && @$dataImplementasi->is_active == '1'))
                        <tr>
                            <td style="white-space:nowrap;"><b>Bobot UMS :</b></td>
                            <td>{{ @$dataImplementasi->dataPengajuan->dataBobot ? @$dataImplementasi->dataPengajuan->dataBobot->bobot_ums : 'Kosong' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="white-space:nowrap;"><b>Bobot Dikti :</b></td>
                            <td>{{ @$dataImplementasi->dataPengajuan->dataBobot ? @$dataImplementasi->dataPengajuan->dataBobot->bobot_dikti : 'Kosong' }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="tab-pane fade" id="detail-dokumen-verifikasi" role="tabpanel" aria-labelledby="detail-dokumen-verifikasi-tab">
        <div id="modal-read-dokumen" style="max-height: 450px; overflow-y:auto; padding-right: 5px;">
            <table class="table table-bordered table-hover" style="font-size: 12px;">
                <tbody>
                    <tr>
                        <td style="white-space:nowrap;"><b>Dokumen Bukti Pelaksanaan:</b></td>
                        <td>
                            @if ($dataImplementasi->file_imp != null)
                                <a href="{{ $fileUrlImplementasi }}" class="btn btn-primary btn-icon mb-2" target="_blank"
                                    data-title-tooltip="Unduh File Bukti Pelaksanaan"
                                    data-original-data-title-tooltip="Unduh File Bukti Pelaksanaan"><i
                                        class="fa-solid fa-download"></i></a>
                            @else
                                Belum Ada Dokumen
                            @endif
                            <div class="iframe-imp"></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Dokumen Kerja Sama :</b></td>
                        <td>
                            @if ($fileUrlMoU)
                                <a href="{{ $fileUrlMoU }}" class="btn btn-primary btn-icon mb-2" target="_blank"
                                    data-title-tooltip="Unduh File Kerja Sama"
                                    data-original-data-title-tooltip="Unduh File  Kerja Sama"><i
                                        class="fa-solid fa-download"></i></a>
                            @else
                                Belum Ada Dokumen
                            @endif
                            <div class="iframe-mou"></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Dokumen IKU 6:</b></td>
                        <td>
                            @if ($dataImplementasi->file_ikuenam != null)
                                <a href="{{ $fileUrlIkuenam }}" class="btn btn-primary btn-icon mb-2" target="_blank"
                                    data-title-tooltip="Unduh File IKU 6"
                                    data-original-data-title-tooltip="Unduh File IKU 6"><i
                                        class="fa-solid fa-download"></i></a>
                            @else
                                Belum Ada Dokumen
                            @endif
                            <div class="iframe-ikuenam"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    var fileUrlMoU = @json($fileUrlMoU);
    var fileUrlIkuenam = @json($fileUrlIkuenam);
    var fileUrlImplementasi = @json($fileUrlImplementasi);

    $(document).ready(function(){
        function generatePreview(url) {
            if (!url || typeof url !== "string" || url.trim() === "") {
                return `<div class="alert alert-secondary text-center mt-3">Tidak ada file untuk ditampilkan.</div>`;
            }
    
            const ext = url.split(".").pop().toLowerCase();
    
            if (ext === "pdf") {
                return `<iframe src="https://docs.google.com/gview?url=${encodeURIComponent(url)}&embedded=true"
                            style="width:100%;height:500px;border:none;"></iframe>`;
                // return `<iframe src="${url}" width="100%" height="500px" style="border:none;"></iframe>`;
            }
    
            if (["png", "jpg", "jpeg", "webp"].includes(ext)) {
                return `<div class="text-center"><img src="${url}" class="img-fluid" style="max-height:500px;"></div>`;
            }
    
            if (["doc", "docx"].includes(ext)) {
                return `<iframe src="https://docs.google.com/gview?url=${encodeURIComponent(url)}&embedded=true"
                            style="width:100%;height:500px;border:none;"></iframe>`;
            }
    
            return `<div class="alert alert-warning text-center mt-3">Format file <b>.${ext}</b> tidak didukung.</div>`;
        }
    
        $(".iframe-mou").html( generatePreview(fileUrlMoU) );
        $(".iframe-imp").html( generatePreview(fileUrlImplementasi) );
        $(".iframe-ikuenam").html( generatePreview(fileUrlIkuenam) );
    })
    
</script>
