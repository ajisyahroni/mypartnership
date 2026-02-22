<?php

namespace App\Http\Controllers;

use App\Models\AjuanHibah;
use App\Models\laporImplementasi;
use App\Models\PengajuanKerjaSama;
use App\Models\RefJenisDokumen;
use App\Models\RefJenisHibah;
use App\Models\RefJenisInstitusiMitra;
use App\Models\RefKategoriImplementasi;
use App\Models\RefLembagaUMS;
use App\Models\RefNegara;
use App\Models\RefTingkatKerjaSama;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferensiController extends Controller
{
    public function index()
    {
        return view('home/index');
    }

    public function getReferensiDokumen()
    {
        $institusi = PengajuanKerjaSama::distinct('nama_institusi')->pluck('nama_institusi')->toArray();
        $jenisDokumen = RefJenisDokumen::pluck('nama_dokumen')->toArray();
        $tingkatKerjasama = RefTingkatKerjaSama::pluck('nama')->toArray();
        $negara = RefNegara::pluck('name')->toArray();
        $lembaga = RefLembagaUMS::pluck('nama_lmbg')->toArray();
        $jenisInstitusiMitra = RefJenisInstitusiMitra::pluck('klasifikasi')->toArray();
        $tahun = PengajuanKerjaSama::selectRaw('YEAR(timestamp) as tahun')->distinct()->orderBy('tahun')->pluck('tahun')->toArray();

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
            'status_verifikasi'       => $this->getStatusVerifikasiOptions(),
        ];

        return response()->json($filter);
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
        $options = [];

        if ($role === 'verifikator') {
            $options['Menunggu Verifikasi Kaprodi'] = 'Menunggu Verifikasi Kaprodi';
        } else {
            $options['Proses Verifikasi Kaprodi'] = 'Proses Verifikasi Kaprodi';
        }

        $options['Draft Dokumen belum di Upload'] = 'Draft Dokumen belum di Upload';

        $options += $role === 'admin'
            ? ['Menunggu Verifikasi Admin' => 'Menunggu Verifikasi Admin']
            : ['Proses Verifikasi Admin' => 'Proses Verifikasi Admin'];

        $options += $role === 'user'
            ? ['Menunggu Verifikasi Pengusul' => 'Menunggu Verifikasi Pengusul']
            : ['Proses Verifikasi Pengusul' => 'Proses Verifikasi Pengusul'];

        $options['PIC Penandatanganan Belum Dipilih'] = 'PIC Penandatanganan Belum Dipilih';
        $options['Proses Penandatanganan'] = 'Proses Penandatanganan';

        $options += $role === 'admin'
            ? ['Menunggu Verifikasi Dokumen Admin' => 'Menunggu Verifikasi Dokumen Admin']
            : ['Proses Verifikasi Dokumen Admin' => 'Proses Verifikasi Dokumen Admin'];

        $options['Dokumen Resmi Telah di Upload'] = 'Dokumen Resmi Telah di Upload';

        return $this->buildOptions(array_values($options), 'Pilih Status Verifikasi');
    }


    public function getReferensiImplementasi()
    {
        $institusi = PengajuanKerjaSama::distinct('nama_institusi')->pluck('nama_institusi')->toArray();
        $category = RefKategoriImplementasi::all();
        $pelaksana = laporImplementasi::distinct('pelaksana_prodi_unit')->pluck('pelaksana_prodi_unit')->toArray();
        $pelapor = laporImplementasi::select('postby')
            ->with(['getPost'])
            ->distinct()
            ->get();
        $judul = laporImplementasi::selectRaw('DISTINCT CASE WHEN judul = "Lain - lain" THEN judul_lain ELSE judul END as judul')
            ->pluck('judul')
            ->toArray();

        $status = [
            'Belum Diverifikasi',
            'Terverifikasi'
        ];

        $jenis_institusi_mitra = RefJenisInstitusiMitra::all();
        $tahun = PengajuanKerjaSama::selectRaw('YEAR(timestamp) as tahun')
            ->distinct()
            ->orderBy('tahun', 'ASC')
            ->pluck('tahun')
            ->toArray();

        $option_tahun = '<option value="">Pilih Tahun</option>';
        foreach ($tahun as $thn) {
            $option_tahun .= '<option value="' . $thn . '">' . $thn . '</option>';
        }

        $option_jenis_institusi_mitra = '<option value="">Pilih Jenis Institusi Mitra</option>';
        foreach ($jenis_institusi_mitra as $jim) {
            $option_jenis_institusi_mitra .= '<option value="' . $jim->klasifikasi . '">' . $jim->klasifikasi . '</option>';
        }

        $option_institusi = '<option value="">Pilih Institusi</option>';
        foreach ($institusi as $ins) {
            $option_institusi .= '<option value="' . $ins . '">' . $ins . '</option>';
        }

        $option_category = '<option value="">Pilih Kategori</option>';
        foreach ($category as $tk) {
            $option_category .= '<option value="' . $tk->kategori . '">' . $tk->kategori . '</option>';
        }

        $option_pelaksana = '<option value="">Pilih Pelaksana</option>';
        foreach ($pelaksana as $nr) {
            $option_pelaksana .= '<option value="' . $nr . '">' . $nr . '</option>';
        }

        $option_pelapor = '<option value="">Pilih Pelapor</option>';
        foreach ($pelapor as $pr) {
            $namaPelapor = optional($pr->getPost)->name ?? 'Unknown';
            $option_pelapor .= '<option value="' . $pr->postby . '">' . $namaPelapor . '</option>';
        }

        $option_judul = '<option value="">Pilih Jenis Dokumen</option>';
        foreach ($judul as $jdl) {
            $option_judul .= '<option value="' . $jdl . '">' . $jdl . '</option>';
        }

        $option_status = '<option value="">Pilih Status</option>';
        foreach ($status as $sts) {
            $option_status .= '<option value="' . $sts . '">' . $sts . '</option>';
        }

        $filter = [
            'institusi' => $option_institusi,
            'category' => $option_category,
            'pelaksana' => $option_pelaksana,
            'judul' => $option_judul,
            'postby' => $option_pelapor,
            'jenis_institusi_mitra' => $option_jenis_institusi_mitra,
            'tahun' => $option_tahun,
            'status' => $option_status,
        ];
        return response()->json($filter);
    }

    public function getReferensiFilterHibah()
    {
        $judul_proposal = AjuanHibah::distinct()->pluck('judul_proposal')->filter()->values();
        $institusi = AjuanHibah::distinct()->pluck('institusi_mitra')->filter()->values();
        $jenis_hibah = RefJenisHibah::get(['id', 'jenis_hibah']);
        $program_studi = RefLembagaUMS::where('jenis_lmbg', 'Program Studi')->get(['id_lmbg', 'nama_lmbg']);
        $fakultas = RefLembagaUMS::where('jenis_lmbg', 'Fakultas')->get(['id_lmbg', 'nama_lmbg']);

        $makeOptions = function ($data, $default, $isAssoc = false) {
            $options = '<option value="">' . $default . '</option>';
            foreach ($data as $item) {
                $value = $isAssoc ? $item->id ?? $item->id_lmbg : $item;
                $label = $isAssoc ? $item->jenis_hibah ?? $item->nama_lmbg : $item;
                $options .= '<option value="' . e($value) . '">' . e($label) . '</option>';
            }
            return $options;
        };

        $option_status = collect([
            'Menunggu Verifikasi Kaprodi',
            'Menunggu Verifikasi Dekan',
            'Under Review Admin',
            session('current_role') === 'admin' ? 'Upload TTD Kontrak' : 'Proses TTD Kontrak Admin',
            'Proses Pencairan Tahap 1',
            'Menunggu Verifikasi Laporan',
            'Proses Pencairan Tahap 2',
            'Selesai',
        ])->reduce(fn($html, $val) => $html . '<option value="' . e($val) . '">' . e($val) . '</option>', '<option value="">Pilih Status</option>');

        return response()->json([
            'judul_proposal' => $makeOptions($judul_proposal, 'Pilih Judul Proposal'),
            'institusi' => $makeOptions($institusi, 'Pilih Institusi'),
            'jenis_hibah' => $makeOptions($jenis_hibah, 'Pilih Jenis Hibah', true),
            'fakultas' => $makeOptions($fakultas, 'Pilih Fakultas', true),
            'program_studi' => $makeOptions($program_studi, 'Pilih Program Studi', true),
            'status' => $option_status,
        ]);
    }
}
