<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="modal-tab" data-bs-toggle="tab" data-bs-target="#modal" type="button"
            role="tab" aria-controls="modal" aria-selected="true">
            Detail Kerja Sama
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
    <div class="tab-pane fade  show active" id="modal" role="tabpanel" aria-labelledby="modal-tab">
        <div id="modal_read">
            <table class="table table-bordered table-hover" id="detailKerma" style="font-size: 12px;">
                <tbody>
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
                        <td style="white-space:nowrap;"><b>Kontribusi Kerja Sama :</b></td>
                        @if (@$dataPengajuan->kontribusi != 'Lain-lain')
                            <td>{{ @$dataPengajuan->kontribusi }}</td>
                        @else
                            <td>{{ @$dataPengajuan->kontribusi_lain }}</td>
                        @endif
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Kerja Sama Mulai :</b></td>
                        <td>{{ @$dataPengajuan->mulai }}</td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>Kerja Sama Berakhir :</b></td>
                        <td>{{ @$dataPengajuan->selesai }}</td>
                    </tr>

                    <tr>
                        <td style="white-space:nowrap;"><b>Status Kerja Sama:</b></td>
                        <td>
                            {!! $dataPengajuan->statusPengajuanKerjaSama() !!}
                        </td>
                    </tr>
                    <tr>
                        <td style="white-space:nowrap;"><b>File Kerja Sama :</b></td>
                        <td>
                            @if ($dataPengajuan->file_mou != null)
                                {{-- <a href="{{ asset('storage/' . $dataPengajuan->file_mou) }}" --}}
                                <a href="{{ getDocumentUrl(@$dataPengajuan->file_mou, 'file_mou') }}"
                                    class="btn btn-primary btn-icon" data-title-tooltip="Unduh File Kerja Sama"
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
    <!-- Tab Timeline -->
    <div class="tab-pane fade" id="timeline" role="tabpanel" aria-labelledby="timeline-tab">
        @include('pengajuan.timeline', compact('dataPengajuan'))
    </div>

</div>
