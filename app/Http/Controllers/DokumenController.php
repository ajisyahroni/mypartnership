<?php

namespace App\Http\Controllers;

use App\Models\PengajuanKerjaSama;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\RefJenisDokumen;
use App\Models\RefJenisDokumenMoU;
use App\Models\RefJenisInstitusiMitra;
use App\Models\RefLembagaUMS;
use App\Models\RefNegara;
use App\Models\RefBentukKerjaSama;
use App\Models\RefGroupRangking;
use App\Models\RefTingkatKerjaSama;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DokumenController extends Controller
{
    public function index()
    {
        $filterDokumen = $this->getReferensiDokumen();
        $data = [
            'li_active' => 'kerjasama',
            'li_sub_active' => 'dokumen_kerjasama_ums',
            'title' => 'Dokumen Kerja Sama UMS',
            'page_title' => 'Dokumen Kerja Sama UMS',
            'filterDokumen' => $filterDokumen
        ];

        if (session('current_role') == 'user' || session('current_role') == 'verifikator') {
            $data['li_active'] = 'dokumen_kerjasama';
        }

        return view('dokumen/index', $data);
    }

    public function getData(Request $request)
    {
        $query = PengajuanKerjaSama::select('*')
            ->with(['getLembaga', 'getPengusul', 'getVerifikator', 'getKabid', 'getPenandatangan']);

        $query->where('created_at', '<', date('2025-12-12'));

        $query->where(function ($q) {
            $q->whereIn('stats_kerma', ['Lapor Kerma', 'Kerma Lama']);
            $q->orwhere(function ($q1) {
                $q1->where('stats_kerma', 'Ajuan Baru');
                $q1->whereNot('tgl_selesai', '0000-00-00 00:00:00');
            });
        });

        $query->orwhere(function ($q) {
            $q->where('created_at', '>', date('2025-12-12'));
            $q->whereNot('tgl_selesai', '0000-00-00 00:00:00');
        });

        $query->FilterDokumen($request);


        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('lembaga', fn($row) => $row->status_tempat ?? '')
            // ->addColumn('lembaga', function ($row) {
            //     return $row->getLembaga->nama_lmbg ?? '';
            //     return $row->lembaga->nama_lmbg ?? '';
            // })
            ->addColumn('mulai', fn($row) => $row->periode_kerma == 'bydoc' ? Tanggal_Indo($row->mulai) : ($row->mulai == null || $row->mulai == '0000-00-00' ? Tanggal_Indo($row->awal) : Tanggal_Indo($row->mulai)))
            ->addColumn('status_verifikasi', function ($row) {
                return $row->getStatusPengajuan();
            })
            ->addColumn('status_pengajuan', function ($row) {
                return $row->statusPengajuan();
            })
            ->addColumn('wilayah_mitra', function ($row) {
                if ($row->dn_ln == 'Luar Negeri') {
                    return $row->negara_mitra;
                } else {
                    return $row->wilayah_mitra;
                }
            })
            ->addColumn('jenis_institusi_mitra', function ($row) {
                if ($row->jenis_institusi == 'Lain-lain') {
                    return $row->nama_institusi;
                } else {
                    return $row->jenis_institusi;
                }
            })
            ->addColumn('implementasi', function ($row) {
                return $row->implementasi_button;
            })
            ->addColumn('action', function ($row) {
                return $row->dokumen_button;
            })
            ->rawColumns(['implementasi', 'action', 'status_pengajuan', 'status_verifikasi'])
            ->make(true);
    }

    public function getDetailPengajuan(Request $request)
    {
        $dataPengajuan = PengajuanKerjaSama::select('*')
            ->with(['getLembaga', 'getPengusul', 'getVerifikator', 'getVerifyUser', 'getKabid', 'getPenandatangan'])
            ->where('id_mou', $request->id_mou)
            ->first();

        $fileUrl = getDocumentUrl($dataPengajuan->file_mou, 'file_mou');
        $fileUrlDraft = getDocumentUrl($dataPengajuan->file_ajuan, 'file_ajuan');
        $fileUrlMitra = getDocumentUrl($dataPengajuan->file_sk_mitra, 'file_mitra');


        $dokumenPerpanjangan = PengajuanKerjaSama::where('id_mou', $dataPengajuan->id_mou_perpanjang)->first() ?? null;
        $data = [
            'dataPengajuan' => $dataPengajuan,
            'fileUrl' => @$fileUrl,
            'fileUrlDraft' => @$fileUrlDraft,
            'fileUrlMitra' => @$fileUrlMitra,
            'dokumenPerpanjangan' => $dokumenPerpanjangan
        ];

        $view = view('pengajuan/detail_data', $data);
        return response()->json(['html' => $view->render(), 'filePdf' =>  $fileUrl], 200);
        // return response()->json(['html' => $view->render(), 'filePdf' => asset('storage/' . $dataPengajuan->file_mou)], 200);
    }

    private function getReferensiDokumen()
    {
        $institusi = PengajuanKerjaSama::distinct('nama_institusi')->pluck('nama_institusi')->toArray();
        $jenisDokumen = RefJenisDokumen::pluck('nama_dokumen')->toArray();
        $tingkatKerjasama = RefTingkatKerjaSama::pluck('nama')->toArray();
        $negara = RefNegara::pluck('name')->toArray();
        $lembaga = RefLembagaUMS::pluck('nama_lmbg')->toArray();
        $jenisInstitusiMitra = RefJenisInstitusiMitra::pluck('klasifikasi')->toArray();
        $tahun = PengajuanKerjaSama::selectRaw('YEAR(timestamp) as tahun')->distinct()->orderBy('tahun')->pluck('tahun')->toArray();

        $statusDokumen = [
            'Belum Dimulai',
            'Berjalan',
            'Expired'
        ];

        $filter = [
            'institusi'               => $this->buildOptions($institusi, 'Pilih Institusi'),
            'jenis_dokumen'           => $this->buildOptions($jenisDokumen, 'Pilih Jenis Dokumen'),
            'tingkat_kerjasama'       => $this->buildOptions($tingkatKerjasama, 'Pilih Tingkat Kerja Sama'),
            'negara'                  => $this->buildOptions($negara, 'Pilih Negara'),
            'lembaga'                 => $this->buildOptions($lembaga, 'Pilih Lembaga'),
            'jenis_institusi_mitra'   => $this->buildOptions($jenisInstitusiMitra, 'Pilih Jenis Institusi Mitra'),
            'tahun'                   => $this->buildOptions($tahun, 'Pilih Tahun'),
            'status'                  => $this->buildOptions(['Dalam Proses', 'Berjalan', 'Dalam Perpanjangan', 'Expired'], 'Pilih Status'),
            'wilayah_mitra'           => $this->buildOptions(['Lokal', 'Nasional'], 'Pilih Wilayah'),
            'dn_ln'                   => $this->buildOptions(['Dalam Negeri', 'Luar Negeri'], 'Pilih Lingkup'),
            'stats_kerma'             => $this->buildOptions(['Ajuan Baru', 'Lapor Kerma'], 'Pilih Status Kerja Sama'),
            'status_dokumen'          => $this->buildOptions($statusDokumen, 'Pilih Status Dokumen'),
            'status_verifikasi'       => $this->getStatusVerifikasiOptions(),
        ];

        // return response()->json($filter);
        return $filter;
    }

    private function buildOptions(array $data, string $defaultLabel): string
    {
        $options = '<option value="">' . $defaultLabel . '</option>';
        foreach ($data as $value) {
            $options .= '<option value="' . e($value) . '">' . e($value) . '</option>';
        }
        return $options;
    }

    private function getStatusVerifikasiOptions(): string
    {
        $role = session('current_role');
        // $options = ['' => 'Pilih Status Verifikasi'];
        $options = [];

        if ($role === 'verifikator') {
            $options['Menunggu Verifikasi Kaprodi'] = 'Menunggu Verifikasi Kaprodi';
            $options['Menunggu Revisi Pengusul'] = 'Menunggu Revisi Pengusul';
        } else {
            $options['Proses Verifikasi Kaprodi'] = 'Proses Verifikasi Kaprodi';
            $options['Proses Revisi Pengusul'] = 'Proses Revisi Pengusul';
        }

        $options['Draft Dokumen belum di Upload'] = 'Draft Dokumen belum di Upload';

        $options += $role === 'admin'
            ? ['Menunggu Verifikasi Admin' => 'Menunggu Verifikasi Admin']
            : ['Proses Verifikasi Admin' => 'Proses Verifikasi Admin'];

        $options += $role === 'admin'
            ? ['Menunggu Revisi Admin' => 'Menunggu Revisi Admin']
            : ['Proses Revisi Admin' => 'Proses Revisi Admin'];

        $options += $role === 'user'
            ? ['Menunggu Verifikasi Pengusul' => 'Menunggu Verifikasi Pengusul']
            : ['Proses Verifikasi Pengusul' => 'Proses Verifikasi Pengusul'];

        $options['PIC Penandatanganan Belum Dipilih'] = 'PIC Penandatanganan Belum Dipilih';
        $options['Proses Penandatanganan'] = 'Proses Penandatanganan';

        $options += $role === 'admin'
            ? ['Menunggu Verifikasi Dokumen Admin' => 'Menunggu Verifikasi Dokumen Admin']
            : ['Proses Verifikasi Dokumen Admin' => 'Proses Verifikasi Dokumen Admin'];

        $options['Dokumen Resmi Telah di Upload'] = 'Dokumen Resmi Telah di Upload';
        $options['Pengajuan Ditolak Admin'] = 'Pengajuan Ditolak Admin';

        return $this->buildOptions(array_values($options), 'Pilih Status Verifikasi');
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
                $d->dn_ln,
                $d->alamat_mitra,
                $d->dn_ln == 'Dalam Negeri' ? $d->wilayah_mitra : $d->negara_mitra,
                $d->jenis_kerjasama,
                $d->jenis_kerjasama_lain,
                $d->jenis_institusi,
                $d->jenis_institusi_lain,
                $d->rangking_univ,
                $d->lembaga,
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
}
