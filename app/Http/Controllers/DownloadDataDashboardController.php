<?php

namespace App\Http\Controllers;

use App\Models\PengajuanKerjaSama;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DownloadDataDashboardController extends Controller
{
    public function downloadExcelSebaranMitraProduktif(Request $request)
    {
        $filter = $request->query('q');
        $placeState = $request->query('ps');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $headers = [
            'No',
            // 'ID MoU',
            'Nama Institusi Mitra',
            'Judul Kegiatan',
            'Bentuk Kegiatan',
            'Kategori',

            'Lingkup',
            'Alamat Mitra',
            'Wilayah / Negara Mitra',
            'Jenis Kerja Sama',
            'Jenis Kerja Sama Lain',
            'Jenis Institusi Mitra',
            'Jenis Institusi Lain',
            'Rangking Universitas',
            'Fakultas / Unit Pengusul',

            'Nama Internal PIC Dokumen Kerja Sama',
            'Jabatan Internal PIC Dokumen Kerja Sama',
            'Email Internal PIC Dokumen Kerja Sama',
            'Telepon Internal PIC Dokumen Kerja Sama',

            'Nama Eksternal PIC Dokumen Kerja Sama',
            'Jabatan Eksternal PIC Dokumen Kerja Sama',
            'Email Eksternal PIC Dokumen Kerja Sama',
            'Telepon Eksternal PIC Dokumen Kerja Sama',

            'Nama Internal PIC Implementasi',
            'Jabatan Internal PIC Implementasi',
            'Email Internal PIC Implementasi',
            'Telepon Internal PIC Implementasi',

            'Nama Eksternal PIC Implementasi',
            'Jabatan Eksternal PIC Implementasi',
            'Email Eksternal PIC Implementasi',
            'Telepon Eksternal PIC Implementasi',

            'Penandatangan Internal',
            'Jabatan TTD Internal',
            'Penandatangan Eksternal',
            'Jabatan TTD Eksternal',
            'Kontribusi',
            'Kontribusi Lain',
            'Periode Kerma',
            'Tanggal Mulai',
            'Tanggal Selesai',
            // 'Status Verifikasi',
            'File Ajuan',
            'Tanggal Upload Draft',
            'Tanggal Verifikasi Kaprodi',
            'Tanggal Verifikasi Admin',
            'Tanggal Verifikasi User',
            'Tanggal Request TTD',
            'Status MoU',

            'Lembaga',
            'Nama Pengusul',
            'Tipe Input Kerma',
            'Status Kerma',
            'Tahun Pengajuan',

            'Link File Ajuan',
            'Link File MoU',
            'Link File Bukti Pelaksanaan Implementasi',
            'Link File IKUENAM',
        ];

        // Set header ke baris 1
        foreach ($headers as $i => $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($columnLetter . '1', $header);
        }

        // Data
        $baseQueryKermaProduktif = $this->QueryKermaProduktif(null, $placeState, $filter);
        $dokumens = $baseQueryKermaProduktif->get();

        $row = 2;

        foreach ($dokumens as $index => $d) {

            // Sesuaikan kolom yang ingin diexport
            $data = [
                $index + 1,
                // $d->id_mou,
                $d->nama_institusi,
                $d->judul != 'Lain-lain' ? $d->judul : $d->judul_lain,
                $d->bentuk_kegiatan != null ? $d->bentuk_kegiatan : $d->bentuk_kegiatan_lain,
                $d->category,

                $d->dn_ln,
                $d->alamat_mitra,
                $d->dn_ln == 'Dalam Negeri' ? $d->wilayah_mitra : $d->negara_mitra,
                $d->jenis_kerjasama,
                $d->jenis_kerjasama_lain,
                $d->jenis_institusi,
                $d->jenis_institusi_lain,
                $d->rangking_univ,
                $d->status_tempat,

                $d->nama_internal_pic,
                $d->lvl_internal_pic,
                $d->email_internal_pic,
                $d->telp_internal_pic,

                $d->nama_eksternal_pic,
                $d->lvl_eksternal_pic,
                $d->email_eksternal_pic,
                $d->telp_eksternal_pic,

                $d->nama_pic_internal,
                $d->jabatan_pic_internal,
                $d->email_pic_internal,
                $d->telp_pic_internal,

                $d->nama_pic_kegiatan,
                $d->jabatan_pic_kegiatan,
                $d->pic_kegiatan,
                $d->telp_pic_kegiatan,

                $d->ttd_internal,
                $d->lvl_internal,
                $d->ttd_eksternal,
                $d->lvl_eksternal,
                $d->kontribusi,
                $d->kontribusi_lain,
                $d->periode_kerma,
                $d->mulai,
                $d->selesai,
                // strip_tags($d->status_verifikasi),
                $d->file_ajuan,
                $d->tgl_draft_upload,
                $d->tgl_verifikasi_kaprodi,
                $d->tgl_verifikasi_kabid,
                $d->tgl_verifikasi_user,
                $d->tgl_req_ttd,
                $d->status_mou,

                $d->lembaga->nama_lmbg ?? $d->status_tempat,
                $d->getPengusul->name ?? $d->postby,
                $d->input_kerma,
                $d->stats_kerma,
                $d->created_at ? TanggalIndonesia($d->created_at) : '-',

                $d->file_ajuan ? asset(getDocumentUrl($d->file_ajuan, 'file_ajuan')) : '-',
                $d->file_mou ? asset(getDocumentUrl($d->file_mou, 'file_mou')) : '-',
                $d->file_imp ? asset(getDocumentUrl($d->file_imp, 'file_imp')) : '-',
                $d->file_ikuenam ? asset(getDocumentUrl($d->file_ikuenam, 'file_ikuenam')) : '-',
            ];

            foreach ($data as $i => $value) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);

                $sheet->setCellValueExplicit(
                    $columnLetter . $row,
                    $value,
                    DataType::TYPE_STRING
                );
            }

            $row++;
        }

        // IMPORTANT: BERSIHKAN OUTPUT BUFFER agar XLSX tidak korup
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Simpan file sementara
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Data Sebaran Mitra Dokumen Kerja Sama Produktif.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), 'xlsx_');
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    public function downloadExcelSebaranMitraAktif(Request $request)
    {
        $filter = $request->query('q');
        $placeState = $request->query('ps');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $headers = [
            'No',
            // 'ID MoU',
            'Nama Institusi Mitra',
            'Lingkup',
            'Alamat Mitra',
            'Wilayah / Negara Mitra',
            'Jenis Kerja Sama',
            'Jenis Kerja Sama Lain',
            'Jenis Institusi Mitra',
            'Jenis Institusi Lain',
            'Rangking Universitas',
            'Fakultas / Unit Pengusul',

            'Nama Internal PIC Dokumen Kerja Sama',
            'Jabatan Internal PIC Dokumen Kerja Sama',
            'Email Internal PIC Dokumen Kerja Sama',
            'Telepon Internal PIC Dokumen Kerja Sama',

            'Nama Eksternal PIC Dokumen Kerja Sama',
            'Jabatan Eksternal PIC Dokumen Kerja Sama',
            'Email Eksternal PIC Dokumen Kerja Sama',
            'Telepon Eksternal PIC Dokumen Kerja Sama',

            'Penandatangan Internal',
            'Jabatan TTD Internal',
            'Penandatangan Eksternal',
            'Jabatan TTD Eksternal',
            'Kontribusi',
            'Kontribusi Lain',
            'Periode Kerma',
            'Tanggal Mulai',
            'Tanggal Selesai',
            // 'Status Verifikasi',
            'File Ajuan',
            'Tanggal Upload Draft',
            'Tanggal Verifikasi Kaprodi',
            'Tanggal Verifikasi Admin',
            'Tanggal Verifikasi User',
            'Tanggal Request TTD',
            'Status MoU',

            'Lembaga',
            'Nama Pengusul',
            'Tipe Input Kerma',
            'Status Kerma',
            'Tahun Pengajuan',

            'Link File Ajuan',
            'Link File MoU',
        ];

        // Set header ke baris 1
        foreach ($headers as $i => $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($columnLetter . '1', $header);
        }

        // Data
        $baseQueryKermaAktif = $this->QueryKerma(null, $placeState, $filter);
        $dokumens = $baseQueryKermaAktif->get();
        $row = 2;

        foreach ($dokumens as $index => $d) {

            // Sesuaikan kolom yang ingin diexport
            $data = [
                $index + 1,
                // $d->id_mou,
                $d->nama_institusi,
                $d->dn_ln,
                $d->alamat_mitra,
                $d->dn_ln == 'Dalam Negeri' ? $d->wilayah_mitra : $d->negara_mitra,
                $d->jenis_kerjasama,
                $d->jenis_kerjasama_lain,
                $d->jenis_institusi,
                $d->jenis_institusi_lain,
                $d->rangking_univ,
                $d->status_tempat,

                $d->nama_internal_pic,
                $d->lvl_internal_pic,
                $d->email_internal_pic,
                $d->telp_internal_pic,

                $d->nama_eksternal_pic,
                $d->lvl_eksternal_pic,
                $d->email_eksternal_pic,
                $d->telp_eksternal_pic,

                $d->ttd_internal,
                $d->lvl_internal,
                $d->ttd_eksternal,
                $d->lvl_eksternal,
                $d->kontribusi,
                $d->kontribusi_lain,
                $d->periode_kerma,
                $d->mulai,
                $d->selesai,
                // strip_tags($d->status_verifikasi),
                $d->file_ajuan,
                $d->tgl_draft_upload,
                $d->tgl_verifikasi_kaprodi,
                $d->tgl_verifikasi_kabid,
                $d->tgl_verifikasi_user,
                $d->tgl_req_ttd,
                $d->status_mou,

                $d->lembaga->nama_lmbg ?? $d->status_tempat,
                $d->getPengusul->name ?? $d->add_by,
                $d->input_kerma,
                $d->stats_kerma,
                $d->created_at ? TanggalIndonesia($d->created_at) : '-',

                $d->file_ajuan ? asset(getDocumentUrl($d->file_ajuan, 'file_ajuan')) : '-',
                $d->file_mou ? asset(getDocumentUrl($d->file_mou, 'file_mou')) : '-',
            ];

            foreach ($data as $i => $value) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);

                $sheet->setCellValueExplicit(
                    $columnLetter . $row,
                    $value,
                    DataType::TYPE_STRING
                );
            }

            $row++;
        }

        // IMPORTANT: BERSIHKAN OUTPUT BUFFER agar XLSX tidak korup
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Simpan file sementara
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Data Sebaran Mitra Dokumen Kerja Sama.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), 'xlsx_');
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }


    public function download_excel(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $headers = [
            'No',
            'ID MoU',
            'Nama Institusi Mitra',
            'Lingkup',
            'Alamat Mitra',
            'Wilayah / Negara Mitra',
            'Jenis Kerja Sama',
            'Jenis Kerja Sama Lain',
            'Jenis Institusi Mitra',
            'Jenis Institusi Lain',
            'Rangking Universitas',
            'Fakultas / Unit Pengusul',
            'Nama Internal PIC',
            'Jabatan Internal PIC',
            'Email Internal PIC',
            'Telepon Internal PIC',
            'Nama Eksternal PIC',
            'Jabatan Eksternal PIC',
            'Email Eksternal PIC',
            'Telepon Eksternal PIC',
            'Penandatangan Internal',
            'Jabatan TTD Internal',
            'Penandatangan Eksternal',
            'Jabatan TTD Eksternal',
            'Kontribusi',
            'Kontribusi Lain',
            'Periode Kerma',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Status Pengajuan',
            'Status Verifikasi',
            'File Ajuan',
            'Tanggal Upload Draft',
            'Tanggal Verifikasi Kaprodi',
            'Tanggal Verifikasi Admin',
            'Tanggal Verifikasi User',
            'Tanggal Request TTD',
            'Status Kerma',
            'Status MoU',
            'Lembaga',
            'Nama Pengusul',
            'Tipe Input Kerma',
            'Status Kerma',
            'Tahun Pengajuan',
            'Link File Ajuan',
            'Link File MoU',
        ];


        // Set header ke baris 1
        foreach ($headers as $i => $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($columnLetter . '1', $header);
        }

        // Data

        $query = PengajuanKerjaSama::select('*')->with(['getLembaga', 'getPengusul', 'getVerifikator', 'getKabid', 'getPenandatangan']);
        $query->whereNot('tgl_selesai', '0000-00-00 00:00:00');
        $query->FilterDokumen($request);

        // $pengajuans = $query->limit(1)->get();
        $dokumens = $query->get();
        // return $dokumens;
        $row = 2;

        foreach ($dokumens as $index => $d) {
            $data = [
                $index + 1,
                $d->id_mou,
                $d->nama_institusi,
                $d->judul != 'Lain-lain' ? $d->judul : $d->judul_lain,
                $d->bentuk_kegiatan != null ? $d->bentuk_kegiatan : $d->bentuk_kegiatan_lain,
                $d->category,

                $d->dn_ln,
                $d->alamat_mitra,
                $d->dn_ln == 'Dalam Negeri' ? $d->wilayah_mitra : $d->negara_mitra,
                $d->jenis_kerjasama,
                $d->jenis_kerjasama_lain,
                $d->jenis_institusi,
                $d->jenis_institusi_lain,
                $d->rangking_univ,
                $d->status_tempat,

                $d->nama_internal_pic,
                $d->lvl_internal_pic,
                $d->email_internal_pic,
                $d->telp_internal_pic,

                $d->nama_eksternal_pic,
                $d->lvl_eksternal_pic,
                $d->email_eksternal_pic,
                $d->telp_eksternal_pic,

                $d->nama_pic_internal,
                $d->jabatan_pic_internal,
                $d->email_pic_internal,
                $d->telp_pic_internal,

                $d->nama_pic_kegiatan,
                $d->jabatan_pic_kegiatan,
                $d->pic_kegiatan,
                $d->telp_pic_kegiatan,

                $d->ttd_internal,
                $d->lvl_internal,
                $d->ttd_eksternal,
                $d->lvl_eksternal,
                $d->kontribusi,
                $d->kontribusi_lain,
                $d->periode_kerma,
                $d->mulai,
                $d->selesai,
                strip_tags($d->status_pengajuan),
                strip_tags($d->status_verifikasi),
                $d->file_ajuan,
                $d->tgl_draft_upload,
                $d->tgl_verifikasi_kaprodi,
                $d->tgl_verifikasi_kabid,
                $d->tgl_verifikasi_user,
                $d->tgl_req_ttd,
                $d->status_kerma,
                $d->status_mou,
                optional($d->getLembaga)->nama_lmbg ?? $d->lembaga,
                optional($d->getPengusul)->name,
                $d->input_kerma,
                $d->stats_kerma,
                $d->created_at ? $d->created_at->format('Y') : '-',
                $d->file_ajuan ? asset(getDocumentUrl($d->file_ajuan, 'file_ajuan')) : '-',
                $d->file_mou ? asset(getDocumentUrl($d->file_mou, 'file_mou')) : '-',
                $d->file_imp ? asset(getDocumentUrl($d->file_imp, 'file_imp')) : '-',
                $d->file_ikuenam ? asset(getDocumentUrl($d->file_ikuenam, 'file_ikuenam')) : '-',
            ];


            foreach ($data as $i => $value) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);

                // Jika kolom terakhir adalah file link
                if ($i === array_key_last($data)) {
                    $sheet->setCellValue($columnLetter . $row, 'Unduh File');
                    $sheet->getCell($columnLetter . $row)->getHyperlink()->setUrl($value);
                } else {
                    $sheet->setCellValueExplicit($columnLetter . $row, $value, DataType::TYPE_STRING);
                }
            }


            $row++;
        }

        // Simpan file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Data Dokumen Kerja Sama.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    private function QueryKermaProduktif($interval = null, $placeState, $filter)
    {
        $query = DB::table('kerma_db as b')
            ->leftJoin('kerma_evaluasi as a', 'a.id_mou', '=', 'b.id_mou')
            ->whereRaw('b.deleted_at IS NULL
                    AND a.deleted_at IS NULL
                    AND b.place_state NOT IN ("","admin")
                    AND b.status_tempat != "admin"
                    AND b.status_tempat != "" 
                    AND b.status_tempat IS NOT NULL
                    AND b.id_lembaga IS NOT NULL');

        $query->whereNotNull('a.id_ev');
        // Tambahkan filter jika ada
        if (!empty($filter) && strtolower($filter) !== 'universitas') {
            if (!empty($placeState)) {
                $query->where('place_state', $placeState);
            } else {
                $query->where('status_tempat', $filter);
            }
        }

        $query->whereRaw("(b.periode_kerma = 'notknown' AND b.status_mou = 'Aktif' OR (b.periode_kerma = 'bydoc' AND CURRENT_DATE BETWEEN b.mulai AND b.selesai))");
        if ($interval) {
            $query->where('b.created_at', '>', DB::raw("DATE_SUB(NOW(), INTERVAL {$interval} YEAR)"));
        }

        return $query;
    }

    private function QueryKerma($interval = null, $placeState, $filter)
    {
        $query = DB::table('kerma_db')
            ->whereRaw('deleted_at IS NULL
                    AND place_state NOT IN ("","admin")
                    AND status_tempat != "admin"
                    AND status_tempat != "" 
                    AND status_tempat IS NOT NULL
                    AND id_lembaga IS NOT NULL');

        // Tambahkan filter jika ada
        if (!empty($filter) && strtolower($filter) !== 'universitas') {
            if (!empty($placeState)) {
                $query->where('place_state', $placeState);
            } else {
                $query->where('status_tempat', $filter);
            }
        }

        $query->whereRaw("(periode_kerma = 'notknown' AND status_mou = 'Aktif' OR (periode_kerma = 'bydoc' AND CURRENT_DATE BETWEEN mulai AND selesai))");
        if ($interval) {
            $query->where('created_at', '>', DB::raw("DATE_SUB(NOW(), INTERVAL {$interval} YEAR)"));
        }

        return $query;
    }
}
