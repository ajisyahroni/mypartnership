<nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSectionLinks">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSectionLinks">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link scroll-to" href="#section-informasi">Informasi
                        Umum</a></li>
                <li class="nav-item"><a class="nav-link scroll-to" href="#section-pendanaan">Detail
                        Pendanaan</a></li>
                {{-- @if (@$dataHibah->date_selesai != null && @$dataHibah->status_selesai) --}}
                @if (@$dataHibah->status_verify_tahap_satu == '1' && @$dataHibah->status_verify_admin == '1')
                    <li class="nav-item"><a class="nav-link scroll-to" href="#section-pencairan">Pencairan
                            Dana</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>


<div class="scrollDetail" style="height: 50vh;overflow-y:auto;">
    <h5 id="section-informasi" class="mt-4">Informasi Umum</h5>
    <table id="tabelDetailHibah" class="table table-hover table-bordered">
        <tbody>
            <tr>
                <td>Judul Proposal</td>
                <td>:</td>
                <td>{{ @$dataHibah->judul_proposal }}</td>
            </tr>
            <tr>
                <td>Institusi Mitra</td>
                <td>:</td>
                <td>{{ @$dataHibah->institusi_mitra }}</td>
            </tr>
            <tr>
                <td>Jenis Hibah</td>
                <td>:</td>
                <td>{{ @$dataHibah->jenis_hibah }}</td>
            </tr>
            <tr>
                <td>Deadline Proposal</td>
                <td>:</td>
                <td>{{ Tanggal_Indo(@$dataHibah->dl_proposal) }}</td>
            </tr>
            <tr>
                <td>Deadline Laporan</td>
                <td>:</td>
                <td>{{ Tanggal_Indo(@$dataHibah->dl_laporan) }}</td>
            </tr>
            <tr>
                <td>Dokumen MoU/MoA</td>
                <td>:</td>
                <td>{{ @$dataHibah->nama_institusi }} | {{ @$dataHibah->dn_ln }} |
                    {{ @$dataHibah->jenis_institusi }}
                </td>
            </tr>
            <tr>
                <td>Ketua Pelaksana</td>
                <td>:</td>
                <td>{{ @$dataHibah->ketua_pelaksana }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td>{{ @$dataHibah->nama_prodi }}</td>
            </tr>
            <tr>
                <td>Fakultas</td>
                <td>:</td>
                <td>{{ @$dataHibah->nama_fakultas }}</td>
            </tr>
            <tr>
                <td>E-mail Ketua Pelaksana</td>
                <td>:</td>
                <td>{{ @$dataHibah->email }}</td>
            </tr>
            <tr>
                <td>Nomor HP Ketua Pelaksana</td>
                <td>:</td>
                <td>{{ @$dataHibah->no_hp }}</td>
            </tr>
            <tr>
                <td>Pengusul</td>
                <td>:</td>
                <td>{{ @$dataHibah->nama_pengusul }}</td>
            </tr>
            <tr>
                <td>Penanggung Jawab Kegiatan</td>
                <td>:</td>
                <td>{{ ucwords(@$dataHibah->penanggung_jawab_kegiatan) }}</td>
            </tr>
            <tr>
                <td>Nama Penanggung Jawab</td>
                <td>:</td>
                <td>{{ @$dataHibah->nama_penanggung_jawab }}</td>
            </tr>
            <tr>
                <td>NIDN Penanggung Jawab</td>
                <td>:</td>
                <td>{{ @$dataHibah->nidn_penanggung_jawab }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>:</td>
                <td><span class="badge bg-primary">{{ @$dataHibah->status_verify }}</span></td>
            </tr>

            @php
                $anggota = json_decode(@$dataHibah->anggota);
                $peran = json_decode(@$dataHibah->peran);
            @endphp
            <tr>
                <td rowspan="{{ count($anggota) }}">Anggota Tim (apabila ada)</td>
                <td>:</td>
                <td>1. {{ $anggota[0] . ' | ' . @$peran[0] ?? '-' }}</td>
            </tr>
            @foreach ($anggota as $index => $nama)
                @if ($index != 0)
                    <tr>
                        <td>:</td>
                        <td>{{ $index + 1 }}. {{ @$nama }} | {{ @$peran[@$index] }}</td>
                    </tr>
                @endif
            @endforeach

            <tr>
                <td>Tanggal Mulai</td>
                <td>:</td>
                <td>{{ tanggalIndonesia(@$dataHibah->tgl_mulai) }}</td>
            </tr>
            <tr>
                <td>Tanggal Selesai</td>
                <td>:</td>
                <td>{{ tanggalIndonesia(@$dataHibah->tanggal_selesai) }}</td>
            </tr>
            <tr>
                <td>Total biaya yang diperlukan</td>
                <td>:</td>
                <td>Rp. {{ @$dataHibah->biaya }}</td>
            </tr>
            <tr>
                <td>Pendanaan dari BKUI</td>
                <td>:</td>
                <td>Rp. {{ @$dataHibah->pendanaan_bkui }}</td>
            </tr>
            <tr>
                <td>Pendanaan dari Sumber Lain</td>
                <td>:</td>
                <td>Rp. {{ @$dataHibah->pendanaan_lain }}</td>
            </tr>

            <tr>
                <td>Latar Belakang</td>
                <td>:</td>
                <td style="text-align: justify;">{!! @$dataHibah->latar_belakang !!}</td>
            </tr>
            <tr>
                <td>Tujuan</td>
                <td>:</td>
                <td style="text-align: justify;">{!! @$dataHibah->tujuan !!}</td>
            </tr>
            <tr>
                <td>Detail Institusi Mitra</td>
                <td>:</td>
                <td style="text-align: justify;">{!! @$dataHibah->detail_institusi_mitra !!}</td>
            </tr>
            <tr>
                <td>Jenis Kerja Sama</td>
                <td>:</td>
                <td>{{ @$dataHibah->jenis_kerma }}</td>
            </tr>
            <tr>
                <td>Detail Kerja Sama</td>
                <td>:</td>
                <td style="text-align: justify;">{!! @$dataHibah->detail_kerma !!}</td>
            </tr>
            <tr>
                <td>Target Output dan Outcome</td>
                <td>:</td>
                <td style="text-align: justify;">{!! @$dataHibah->target !!}</td>
            </tr>
            <tr>
                <td>Indikator Keberhasilan</td>
                <td>:</td>
                <td style="text-align: justify;">{!! @$dataHibah->indikator_keberhasilan !!}</td>
            </tr>
            <tr>
                <td>Rencana Keberlanjutan Kerja Sama</td>
                <td>:</td>
                <td style="text-align: justify;">{!! @$dataHibah->rencana !!}</td>
            </tr>

            <tr>
                <td>File Tambahan</td>
                <td>:</td>
                <td>
                    @if (@$dataHibah->file_lain)
                        @php $fileUrl = encodedFileUrl(@$dataHibah->file_lain); @endphp
                        <a href="{{ $fileUrl }}" class="btn btn-sm btn-primary" target="_blank"><i
                                class="bx bx-download"></i></a><br><br>
                        <iframe src="{{ $fileUrl }}" frameborder="0" id="iframe_file_lain"
                            style="width: 100%;height: 500px;"></iframe>
                    @else
                        <small class="text-danger">Belum Ada File Upload</small>
                    @endif
                </td>
            </tr>
            <tr>
                <td>File Kontrak</td>
                <td>:</td>
                <td>
                    @if (@$dataHibah->file_kontrak)
                        @php $fileUrl = encodedFileUrl(@$dataHibah->file_kontrak); @endphp
                        <a href="{{ $fileUrl }}" class="btn btn-sm btn-primary" target="_blank"><i
                                class="bx bx-download"></i></a><br><br>
                        <iframe src="{{ $fileUrl }}" frameborder="0" id="iframe_file_kontrak"
                            style="width: 100%;height: 500px;"></iframe>
                    @else
                        <small class="text-danger">Belum Ada File Upload</small>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    <br><br>
    <h5 id="section-pendanaan" class="mt-5">Detail Pendanaan</h5>
    <table id="tabelDetailPendanaan" class="table table-hover">
        <thead>
            <tr>
                <th colspan="6" class="text-center">DETAIL PENDANAAN</th>
            </tr>
            <tr>
                <th>No</th>
                <th>Jenis Pengeluaran</th>
                <th>Jumlah</th>
                <th>Biaya Satuan</th>
                <th>Biaya Total</th>
                <th>Sumber Pendanaan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $jenis = json_decode(@$dataHibah->jenis_pengeluaran);
                $jumlah = json_decode(@$dataHibah->jumlah_pengeluaran);
                $satuan = json_decode(@$dataHibah->biaya_satuan);
                $total = json_decode(@$dataHibah->biaya_total);
                $sumber = json_decode(@$dataHibah->sumber_pendanaan);
            @endphp
            @if (empty($jenis))
                <tr>
                    <td colspan="7">Belum Ada Data</td>
                </tr>
            @else
                @php
                    $totalPendanaan = 0;
                @endphp
                @foreach ($jenis as $index => $jns)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $jns }}</td>
                        <td>{{ @$jumlah[$index] ?? '' }}
                        </td>
                        <td>
                            {{ @$satuan[$index] ?? '' }}
                        </td>
                        <td>
                            {{ @$total[$index] ?? '' }}
                        </td>
                        <td>
                            {{ @$sumber[$index] ?? '' }}
                        </td>
                    </tr>
                    @php
                        $totalPendanaan += @$total[$index];
                    @endphp
                @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align:right">Total</td>
                <td colspan="2">{{ rupiah(@$totalPendanaan) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- @if (@$dataHibah->date_selesai != null && @$dataHibah->status_selesai) --}}
    @if (@$dataHibah->status_verify_tahap_satu == '1' && @$dataHibah->status_verify_admin == '1')
        <h5 id="section-pencairan" class="mt-5">Pencairan Dana</h5>
        <table id="tabelPencairanNominal" class="table table-hover table-bordered">
            <tbody>
                <tr>
                    <td>Dana yang disetujui</td>
                    <td>:</td>
                    <td>Rp. {{ rupiah(@$dataHibah->dana_disetujui_bkui) }}</td>
                </tr>
                <tr>
                    <td>Sisa Dana yang disetujui</td>
                    <td>:</td>
                    <td>Rp.
                        {{ rupiah(@$dataHibah->sisa_dana) }}
                    </td>
                </tr>
                <tr>
                    <td>Nominal Pencairan Tahap Satu</td>
                    <td>:</td>
                    <td>Rp. {{ rupiah(@$dataHibah->pencairan_tahap_satu) }}
                        @if (@$dataHibah->file_bukti_transfer_tahap_satu != null)
                            <a href="{{ asset('storage/'.@$dataHibah->file_bukti_transfer_tahap_satu) }}" target="_blank" data-title-tooltip="Download Bukti Transfer Tahap 1" class="btn btn-primary btn-sm"><i class="bx bx-download"></i></a>
                        @endif
                    </td>
                </tr>
                @if (@$dataHibah->status_verify_tahap_dua == '1' && @$dataHibah->status_verify_laporan == '1')
                    <tr>
                        <td>Nominal Pencairan Tahap Dua</td>
                        <td>:</td>
                        <td>Rp. {{ rupiah(@$dataHibah->pencairan_tahap_dua) }}
                            @if (@$dataHibah->file_bukti_transfer_tahap_dua != null)
                                <a href="{{ asset('storage/'.@$dataHibah->file_bukti_transfer_tahap_dua) }}" target="_blank" data-title-tooltip="Download Bukti Transfer Tahap 2" class="btn btn-primary btn-sm"><i class="bx bx-download"></i></a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <td>:</td>
                        <td>Rp.
                            {{ rupiah(@$dataHibah->pencairan_tahap_dua + @$dataHibah->pencairan_tahap_satu) }}
                        </td>
                    </tr>
                @else
                    <tr>
                        <td>Total</td>
                        <td>:</td>
                        <td>Rp.
                            {{ rupiah(@$dataHibah->pencairan_tahap_satu) }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif
</div>
