<!DOCTYPE html>
<html lang="id">

@php
    use Carbon\Carbon;

    // Pastikan $hibah->created_at tidak null
    $tanggal = $hibah->created_at ? Carbon::parse($hibah->created_at) : null;

    function konversiHtmlUntukPDF($html)
    {
        $html = str_replace('src="http://127.0.0.1:8000/storage/', 'src="' . public_path('storage/') . '/', $html);

        // Tambahkan style default agar gambar rapi di PDF
        $html = str_replace('<img', '<img style="max-width:100%; height:auto;"', $html);

        return $html;
    }

@endphp

<head>
    <meta charset="UTF-8">
    <title>Laporan Pengajuan Hibah</title>
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
    </style>

    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            margin: 2cm;
            line-height: 1.15;
        }

        .cover {
            text-align: center;
            /* margin-top: 100px; */
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

        ol {
            padding-left: 20px;
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
        <div class="title">LAPORAN KEGIATAN HIBAH KERJA SAMA INTERNASIONAL</div>
        <p><strong>{{ strtoupper(@$hibah->judul_proposal ?? 'Data belum diisi') }}</strong></p>
        <img src="{{ public_path('images/ums.png') }}" class="logo_ums" alt="Logo UMS">
        <br><br><br><br>
        <p>{{ strtoupper(@$hibah->nama_prodi ?? 'Data belum diisi') }}</p>
        <p>{{ strtoupper(@$hibah->nama_fakultas ?? 'Data belum diisi') }}</p>
        <p>UNIVERSITAS MUHAMMADIYAH SURAKARTA</p>
        <p>TAHUN {{ @$hibah->created_at?->format('Y') ?? 'Data belum diisi' }}</p>
    </div>

    <div class="page-break"></div>

    {{-- HALAMAN PENGESAHAN --}}
    <div class="section-title" style="text-align: center;">Halaman Pengesahan</div>
    <table class="table-pengesahan table-custom">
        <tr>
            <td>Judul Proposal</td>
            <td>:</td>
            <td>{{ ucwords(@$hibah->judul_proposal ?? 'Data belum diisi') }}</td>
        </tr>
        <tr>
            <td>Ketua Pelaksana</td>
            <td>:</td>
            <td>{{ ucwords(@$hibah->ketua_pelaksana ?? 'Data belum diisi') }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>:</td>
            <td>{{ ucwords(@$hibah->nama_prodi ?? 'Data belum diisi') }}</td>
        </tr>
        <tr>
            <td>Fakultas</td>
            <td>:</td>
            <td>{{ ucwords(@$hibah->nama_fakultas ?? 'Data belum diisi') }}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>:</td>
            <td>{{ @$hibah->email ?? 'Data belum diisi' }}</td>
        </tr>
        <tr>
            <td>No. HP</td>
            <td>:</td>
            <td>{{ @$hibah->no_hp ?? 'Data belum diisi' }}</td>
        </tr>
        <tr>
            <td valign="top">Anggota Tim <br>(Apabila ada)</td>
            <td>:</td>
            <td>
                @if (!empty($anggota))
                    <ul>
                        @foreach ($anggota as $index => $agt)
                            <li>{{ ucwords($agt) }}</li>
                        @endforeach
                    </ul>
                @else
                    Belum Ada Anggota
                @endif
            </td>
        </tr>
        <tr>
            <td>Lama Aktivitas</td>
            <td>:</td>
            <td>{{ @$tanggal_pelaksanaan ?? 'Data belum diisi' }}</td>
        </tr>
        <tr>
            <td>Total biaya yang diperlukan</td>
            <td>:</td>
            <td>Rp. {{ number_format(@$hibah->biaya ?? 0, 0, ',', '.') }}</td>
        </tr>
    </table>

    <br><br>
    <table class="signature">
        <tr>
            <td colspan="2" style="text-align: right;">Sukoharjo,
                {{ $tanggal ? $tanggal->translatedFormat('d F Y') : '...........' }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: left;"><strong>Mengetahui,</strong></td>
        </tr>
        <tr>
            <td style="text-align: left;">
                <strong>{{ @$hibah->penanggung_jawab == 'kaprodi' ? 'Ketua Program Studi' : 'Dekan' }}</strong></td>
            <td style="text-align: left;"><strong>Ketua Pelaksana</strong></td>
        </tr>
        <tr>
            <td style="text-align: left;">
                <strong>{{ ucwords(@$hibah->nama_penanggung_jawab ?? '<< Nama Penanggung Jawab Kosong >>') }}</strong>
            </td>
            <td style="text-align: left;">
                <strong>{{ ucwords(@$hibah->ketua_pelaksana ?? '<< Ketua Pelaksana Kosong >>') }}</strong></td>
        </tr>
        <tr>
            <td style="text-align: left;"><strong>NIDN.
                    {{ @$hibah->nidn_penanggung_jawab ?? '<< NIDN Penanggung Jawab Kosong >>' }}</strong></td>
            <td style="text-align: left;"><strong>NIDN.
                    {{ @$hibah->nidn_ketua_pelaksana ?? '<< NIDN Ketua Pelaksana Kosong >>' }}</strong></td>
        </tr>
        <tr>
            <td><span class="footer-note">Catatan: Jika Pengusul adalah Kaprodi, maka pihak yang “Mengetahui” adalah
                    Dekan.</span></td>
            <td></td>
        </tr>
    </table>

    <div class="page-break"></div>

    {{-- ISI PROPOSAL --}}
    <p style="text-align: center;"><strong>{{ strtoupper(@$hibah->judul_proposal ?? 'Data belum diisi') }}</strong></p>

    <div class="section-title">A. Pendahuluan</div>
    <div class="content">
        <strong>a. Latar Belakang</strong><br>
        <span>{!! konversiHtmlUntukPDF(@$hibah->latar_belakang ?? 'Data belum diisi') !!}</span><br><br>

        <strong>b. Tujuan</strong><br>
        <span>{!! konversiHtmlUntukPDF(@$hibah->tujuan ?? 'Data belum diisi') !!}</span>
    </div>

    <div class="section-title">B. Detail Aktivitas</div>
    <div class="content">
        <span>{!! konversiHtmlUntukPDF(@$laporHibah->detail_aktivitas ?? 'Data belum diisi') !!}</span>
    </div>

    <div class="section-title">C. Target Aktivitas</div>
    <div class="content">
        <span>{!! konversiHtmlUntukPDF(@$laporHibah->target_laporan ?? 'Data belum diisi') !!}</span>
    </div>

    <div class="section-title">D. Hasil dan Dampak</div>
    <div class="content">
        <strong>a. Hasil Kerja Sama dan Dampak (Jangka Pendek dan Jangka Panjang)</strong><br>
        <span>{!! konversiHtmlUntukPDF(@$laporHibah->hasil_laporan ?? 'Data belum diisi') !!}</span>
    </div>

    <div class="section-title">E. Rencana Tindak Lanjut</div>
    <div class="content">
        <span>{!! konversiHtmlUntukPDF(@$laporHibah->rencana_tindak_lanjut ?? 'Data belum diisi') !!}</span>
    </div>

    <div class="section-title">F. Anggaran</div>
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
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @if (empty($jenis_pengeluaran))
                    <tr>
                        <td colspan="6" class="center">Data belum diisi</td>
                    </tr>
                @else
                    @foreach ($jenis_pengeluaran as $index => $jns)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $jns ?? 'Data belum diisi' }}</td>
                            <td>{{ $jumlah_pengeluaran[$index] ?? 'Data belum diisi' }}</td>
                            <td>{{ $satuan[$index] ?? 'Data belum diisi' }}</td>
                            <td>Rp. {{ number_format($biaya_satuan[$index] ?? 0, 0, ',', '.') }}</td>
                            <td>Rp. {{ number_format($biaya_total[$index] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @php $total += $biaya_total[$index] ?? 0; @endphp
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-end">Total Semua Pengeluaran:</th>
                    <th>Rp. {{ number_format($total, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

</body>

</html>
