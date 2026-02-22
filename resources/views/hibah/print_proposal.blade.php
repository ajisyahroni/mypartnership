<!DOCTYPE html>
<html lang="id">

@php
    use Carbon\Carbon;

    // Pastikan $hibah->created_at tidak null
    $tanggal = $hibah->created_at ? Carbon::parse($hibah->created_at) : null;

    function konversiHtmlUntukPDF($html)
    {
        $baseUrl = request()->root();
        $html = str_replace('src="'. $baseUrl .'/storage/', 'src="' . public_path('storage') . '/', $html);

        // Tambahkan style default agar gambar rapi di PDF
        $html = str_replace('<img', '<img style="max-width:100%; height:auto;"', $html);

        return $html;
    }

@endphp

<head>
    <meta charset="UTF-8">
    <title>Proposal Pengajuan Hibah</title>
    <style>
        .table-custom {
            border-collapse: collapse;
            width: 100%;
        }

        .table-custom th,
        .table-custom td {
            border: 1px solid #000;
            padding: 6px !important;
            text-align: left;
        }

        .table-custom th {
            background-color: #f2f2f2;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            margin: 2cm;
            line-height: 1.15;
        }

        .cover {
            text-align: center;
        }

        .cover img {
            width: 100px;
            margin-bottom: 20px;
        }

        .title {
            font-weight: bold;
            font-size: 16pt;
            margin: 20px 0;
            text-transform: uppercase;
        }

        .section-title {
            font-weight: bold;
            margin-top: 30px;
            text-transform: uppercase;
            font-size: 13pt;
        }

        .content {
            margin-top: 10px;
            text-align: justify;
        }

        .table-pengesahan {
            margin-top: 30px;
            width: 100%;
        }

        .table-pengesahan td {
            padding: 5px;
            vertical-align: top;
        }

        .signature {
            width: 100%;
        }

        .signature td {
            width: 50%;
            vertical-align: top;
        }

        .footer-note {
            font-size: 10pt;
            font-style: italic;
            margin-top: 10px;
        }

        .page-break {
            page-break-after: always;
        }

        .center {
            text-align: center;
        }

        .logo_ums {
            width: 250px !important;
        }
    </style>
</head>

<body>

    {{-- COVER --}}
    <div class="cover">
        <div class="title">PROPOSAL PENGAJUAN HIBAH KERJA SAMA INTERNASIONAL</div>
        <p><strong>{{ strtoupper(@$hibah->judul_proposal ?: 'Data Belum Diisi') }}</strong></p>
        <img src="{{ public_path('images/ums.png') }}" class="logo_ums" alt="Logo UMS">
        <br><br><br><br>
        <p>{{ strtoupper(@$hibah->nama_prodi ?: 'Data Belum Diisi') }}</p>
        <p>{{ strtoupper(@$hibah->nama_fakultas ?: 'Data Belum Diisi') }}</p>
        <p>UNIVERSITAS MUHAMMADIYAH SURAKARTA</p>
        <p>TAHUN {{ @$hibah->created_at ? $hibah->created_at->format('Y') : 'Data Belum Diisi' }}</p>
    </div>

    <div class="page-break"></div>

    {{-- HALAMAN PENGESAHAN --}}
    <div class="section-title" style="text-align: center;">Halaman Pengesahan</div>
    <table class="table-pengesahan table-custom">
        <tr>
            <td>Judul Proposal</td>
            <td>:</td>
            <td>{{ ucwords(@$hibah->judul_proposal ?: 'Data Belum Diisi') }}</td>
        </tr>
        <tr>
            <td>Ketua Pelaksana</td>
            <td>:</td>
            <td>{{ ucwords(@$hibah->ketua_pelaksana ?: 'Data Belum Diisi') }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>:</td>
            <td>{{ ucwords(@$hibah->nama_prodi ?: 'Data Belum Diisi') }}</td>
        </tr>
        <tr>
            <td>Fakultas</td>
            <td>:</td>
            <td>{{ ucwords(@$hibah->nama_fakultas ?: 'Data Belum Diisi') }}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>:</td>
            <td>{{ @$hibah->email ?: 'Data Belum Diisi' }}</td>
        </tr>
        <tr>
            <td>No. HP</td>
            <td>:</td>
            <td>{{ @$hibah->no_hp ?: 'Data Belum Diisi' }}</td>
        </tr>
        <tr>
            <td valign="top">Anggota Tim <br>(Apabila ada)</td>
            <td>:</td>
            <td>
                @if (empty($anggota))
                    Belum Ada Anggota
                @else
                    <ul>
                        @foreach ($anggota as $agt)
                            <li>{{ ucwords($agt ?: 'Data Belum Diisi') }}</li>
                        @endforeach
                    </ul>
                @endif
            </td>
        </tr>
        <tr>
            <td>Lama Aktivitas</td>
            <td>:</td>
            <td>{{ @$tanggal_pelaksanaan ?: 'Data Belum Diisi' }}</td>
        </tr>
        <tr>
            <td>Total biaya yang diperlukan</td>
            <td>:</td>
            <td>Rp. {{ $hibah->biaya ? number_format($hibah->biaya, 0, ',', '.') : 'Data Belum Diisi' }}</td>
        </tr>
    </table>

    <br><br>
    <table class="signature">
        <tr>
            <td colspan="2" style="text-align: right;">Sukoharjo,
                {{ $tanggal ? $tanggal->translatedFormat('d F Y') : '...........' }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Mengetahui,</strong></td>
        </tr>
        <tr>
            @if ($hibah->penanggung_jawab_kegiatan == 'dekan')
                <td><strong>Dekan</strong></td>
            @else
                <td><strong>Ketua Program Studi</strong></td>
            @endif
            <td><strong>Ketua Pelaksana</strong></td>
        </tr>
        <tr>
            <td>
                <strong>{{ @$hibah->nama_penanggung_jawab ? ucwords($hibah->nama_penanggung_jawab) : '<< Nama Penanggung Jawab Kosong >>' }}</strong>
            </td>
            <td><strong>{{ @$hibah->ketua_pelaksana ? ucwords($hibah->ketua_pelaksana) : 'Data Belum Diisi' }}</strong>
            </td>
        </tr>
        <tr>
            <td><strong>NIDN. {{ @$hibah->nidn_penanggung_jawab ?: '<< NIDN Penanggung Jawab Kosong >>' }}</strong>
            </td>
            <td><strong>NIDN. {{ @$hibah->nidn_ketua_pelaksana ?: '<< NIDN Ketua Pelaksana Kosong >>' }}</strong></td>
        </tr>
        <tr>
            <td><span class="footer-note">Catatan: Jika Pengusul adalah Kaprodi, maka pihak yang “Mengetahui” adalah
                    Dekan.</span></td>
            <td></td>
        </tr>
    </table>

    <div class="page-break"></div>

    {{-- ISI PROPOSAL --}}
    <p class="center"><strong>{{ strtoupper(@$hibah->judul_proposal ?: 'Data Belum Diisi') }}</strong></p>

    <div class="section-title">A. Pendahuluan</div>
    <div class="content">
        <strong>a. Latar Belakang</strong><br>
        {!! konversiHtmlUntukPDF(@$hibah->latar_belakang ?: '<em>Data Belum Diisi</em>') !!}<br><br>
        <strong>b. Tujuan</strong><br>
        {!! konversiHtmlUntukPDF(@$hibah->tujuan ?: '<em>Data Belum Diisi</em>') !!}
    </div>

    <div class="section-title">C. Rencana Kerja Sama</div>
    <div class="content">
        <strong>a. Jenis dan Detail Kerja Sama</strong><br>
        {!! konversiHtmlUntukPDF(@$hibah->detail_kerma ?: '<em>Data Belum Diisi</em>') !!}<br><br>
        <strong>b. Institusi Mitra</strong><br>
        {!! konversiHtmlUntukPDF(@$hibah->detail_institusi_mitra ?: '<em>Data Belum Diisi</em>') !!}<br><br>
        <strong>c. Rencana Kegiatan</strong><br>
        {!! konversiHtmlUntukPDF(@$hibah->rencana ?: '<em>Data Belum Diisi</em>') !!}<br><br>
        <strong>d. Jadwal Kegiatan</strong><br>
        {{ @$tanggal_pelaksanaan ?: 'Data Belum Diisi' }}<br><br>
        <strong>e. Luaran</strong><br>
        {!! konversiHtmlUntukPDF(@$hibah->target ?: '<em>Data Belum Diisi</em>') !!}
    </div>

    <div class="section-title">E. Anggaran</div>
    <div class="content">
        <strong>a. Rincian Biaya</strong><br>
        <table class="table table-custom table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Pengeluaran</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Biaya Satuan</th>
                    <th>Biaya Total</th>
                    <th>Sumber Pendanaan</th>
                </tr>
            </thead>
            <tbody>
                @if (empty($jenis_pengeluaran))
                    <tr>
                        <td colspan="7">Belum Ada Data</td>
                    </tr>
                @else
                    @php
                        $totalSemua = 0;
                    @endphp
                    @foreach ($jenis_pengeluaran as $index => $jns)
                        @php
                            $total = $biaya_total[$index] ?? 0;
                            $totalSemua += $total;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $jns ?: 'Data Belum Diisi' }}</td>
                            <td>{{ $jumlah_pengeluaran[$index] ?? 'Data Belum Diisi' }}</td>
                            <td>{{ $satuan[$index] ?? 'Data Belum Diisi' }}</td>
                            <td>
                                Rp.
                                {{ isset($biaya_satuan[$index]) ? number_format($biaya_satuan[$index], 0, ',', '.') : 'Data Belum Diisi' }}
                            </td>
                            <td>
                                Rp.
                                {{ isset($biaya_total[$index]) ? number_format($biaya_total[$index], 0, ',', '.') : 'Data Belum Diisi' }}
                            </td>
                            <td>{{ $sumber_pendanaan[$index] ?? 'Data Belum Diisi' }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            @if (!empty($jenis_pengeluaran))
                <tfoot>
                    <tr>
                        <th colspan="5" style="text-align: right;">Total Keseluruhan</th>
                        <th colspan="2">Rp. {{ number_format($totalSemua, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            @endif

        </table>

        <br><br>
        <strong>b. Sumber Pendanaan</strong><br>
        {{ $sumber_pendanaan_umum ?? '[Data Belum Diisi]' }}
    </div>

    <div class="section-title">F. Monitoring dan Evaluasi</div>
    <div class="content">
        <strong>a. Indikator Kinerja</strong><br>
        {!! konversiHtmlUntukPDF(@$hibah->indikator_keberhasilan ?: '<em>Data Belum Diisi</em>') !!}
    </div>

</body>

</html>
