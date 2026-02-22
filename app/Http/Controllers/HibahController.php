<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailHibah;
use App\Models\AjuanHibah;
use App\Models\DokumenPendukungHibah;
use App\Models\Kuisioner;
use App\Models\LaporanHibah;
use App\Models\LogActivity;
use App\Models\PengajuanKerjaSama;
use App\Models\ProspectPartner;
use App\Models\RefJenisDokumen;
use App\Models\RefJenisHibah;
use App\Models\RefLembagaUMS;
use App\Models\Roles;
use App\Models\SettingHibah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use function PHPUnit\Framework\fileExists;

class HibahController extends Controller
{
    public function index()
    {
        $dataNegara = ProspectPartner::select(
            'ref_countries.name as country_name',
            DB::raw('COUNT(tbl_prospect_partner.id) as total')
        )
            ->join('ref_countries', 'ref_countries.id', '=', 'tbl_prospect_partner.country')
            ->groupBy('ref_countries.latitude', 'ref_countries.longitude', 'ref_countries.name')
            ->get();

        $data = [
            'li_active' => 'dashboard',
            'title' => 'Dashboard | Hibah',
            'page_title' => 'Hibah',
            'dataNegara' => $dataNegara,
        ];

        $data['notif_verifikator'] = 0;
        if (session('current_role') == 'verifikator') {
            $data['notif_verifikator'] = $this->notifikasiHibah();
        }

        return view('hibah/index', $data);
    }

    public function tambah()
    {
        $data = [
            'li_active' => 'ajuan',
            'title' => 'Tambah Ajuan Hibah',
            'page_title' => 'Tambah Ajuan Hibah',
            'jenis_hibah' => RefJenisHibah::where('is_active', '1')->where('dl_proposal', '>=', Carbon::today())->get(),
            'jenis_kerma' => RefJenisDokumen::all(),
            'fakultas' => RefLembagaUMS::where('jenis_lmbg', 'Fakultas')->get(),
            'program_studi' => RefLembagaUMS::where('jenis_lmbg', 'Program Studi')->get(),
            'country' => DB::table('ref_countries')->get(),
            'settingHibah' => SettingHibah::getData(),
            'jabatan' => Auth::user()->jabatan,
            'prodi_user' => RefLembagaUMS::where('nama_lmbg', auth()->user()->status_tempat)->first()->id_lmbg ?? '',
            'fak_user' => auth()->user()->place_state,
        ];

        session(['hibah_key' => Str::random(40)]);

        $data['jenis_mou'] = PengajuanKerjaSama::whereDate('tgl_selesai', '!=', '0000-00-00 00:00:00')->get();


        return view('hibah/tambah', $data);
    }

    public function edit($id_hibah)
    {
        $query = AjuanHibah::withFullJoin();
        $dataHibah =  $query->where('tbl_ajuan_hibah.id_hibah', $id_hibah)->firstOrFail();

        // return $dataHibah;
        if (
            $dataHibah->is_submit == '1'
        ) {
            $arrIdHibah = RefJenisHibah::where('is_active', '1')->where('dl_proposal', '>=', Carbon::today())
                ->orwhere('id', $dataHibah->id_jenis_hibah)
                ->pluck('jenis_hibah')->toArray();
            $refJenisHibah = RefJenisHibah::where('is_active', '1')->where('dl_proposal', '>=', Carbon::today())
                ->orwhere('id', $dataHibah->id_jenis_hibah)
                ->get();
        } else {
            $refJenisHibah = RefJenisHibah::where('is_active', '1')->where('dl_proposal', '>=', Carbon::today())->get();
            $arrIdHibah = RefJenisHibah::where('is_active', '1')->where('dl_proposal', '>=', Carbon::today())->pluck('jenis_hibah')->toArray();
        }

        $hasVerify = [
            'kaprodi' => $dataHibah->status_verify_kaprodi,
            'dekan' => $dataHibah->status_verify_dekan,
            'admin' => $dataHibah->status_verify_admin,
            'tahap_satu' => $dataHibah->status_verify_tahap_satu,
            'laporan' => $dataHibah->status_verify_laporan,
            'tahap_dua' => $dataHibah->status_verify_tahap_dua,
            'selesai' => $dataHibah->status_verify_selesai,
        ];

        $filtered = array_filter($hasVerify, function ($v) {
            return $v !== null;
        });

        if ($dataHibah->is_submit == '1') {
            if (!in_array(0, $filtered)) {
                abort(403);
            }
        }

        // if (in_array('0', [$hasVerify['kaprodi'], $hasVerify['dekan'], $hasVerify['admin']]) && $dataHibah->is_submit == '1') {
        //     abort(403);
        // }

        // return $refJenisHibah;

        $data = [
            'li_active' => 'ajuan',
            'title' => 'Edit Ajuan Hibah',
            'page_title' => 'Edit Ajuan Hibah',
            'arrIdHibah' => $arrIdHibah,
            'jenis_hibah' => $refJenisHibah,
            'jenis_kerma' => RefJenisDokumen::all(),
            'fakultas' => RefLembagaUMS::where('jenis_lmbg', 'Fakultas')->get(),
            'program_studi' => RefLembagaUMS::where('jenis_lmbg', 'Program Studi')->get(),
            'country' => DB::table('ref_countries')->get(),
            'dataHibah' => $dataHibah,
            'settingHibah' => SettingHibah::getData(),
            'logFileLain' => getLogFile($id_hibah, 'file_lain'),
            'jabatan' => Auth::user()->jabatan,
            'prodi_user' => RefLembagaUMS::where('nama_lmbg', auth()->user()->status_tempat)->first()->id_lmbg ?? '',
            'fak_user' => auth()->user()->place_state,
        ];

        session(['hibah_key' => Str::random(40)]);

        // return json_decode($dataHibah->anggota, true) ?? [];
        $data['jenis_mou'] = PengajuanKerjaSama::whereDate('tgl_selesai', '!=', '0000-00-00 00:00:00')->get();

        return view('hibah/edit', $data);
    }

    public function ajuan()
    {

        $isVerifPJ = AjuanHibah::query()
            ->where('tbl_ajuan_hibah.add_by', auth()->user()->username)
            ->leftJoin('ref_jenis_hibah as jh', 'jh.id', '=', 'tbl_ajuan_hibah.jenis_hibah')
            ->whereDate('jh.dl_proposal', '=', Carbon::now()->subDay()->toDateString())
            ->where(function ($query) {
                $query->where('tbl_ajuan_hibah.penanggung_jawab_kegiatan', 'kaprodi')->where(function ($kaprodi) {
                    $kaprodi->whereNull('tbl_ajuan_hibah.status_verify_kaprodi')->orWhere('tbl_ajuan_hibah.status_verify_kaprodi', '0');
                });
                $query->where('tbl_ajuan_hibah.penanggung_jawab_kegiatan', 'dekan')->where(function ($dekan) {
                    $dekan->whereNull('tbl_ajuan_hibah.status_verify_dekan')->orWhere('tbl_ajuan_hibah.status_verify_dekan', '0');
                });
            })
            ->count();


        $filterHibah = $this->getReferensiFilterHibah();

        $data = [
            'li_active' => 'ajuan',
            'title' => 'Daftar Ajuan Hibah',
            'page_title' => 'Daftar Ajuan Hibah',
            'isVerifPJ' => $isVerifPJ,
            'filterHibah' => $filterHibah
        ];

        return view('hibah/ajuan', $data);
    }

    public function isiLaporan($id_hibah)
    {
        $dataLaporanHibah = LaporanHibah::where('id_hibah', $id_hibah)->first();
        $ajuanHibah = AjuanHibah::where('id_hibah', $id_hibah)->first();
        $catatanRevisi = $ajuanHibah->catatan_laporan;
        $status_revisi_laporan = $ajuanHibah->status_revisi_laporan;

        $data = [
            'li_active' => 'ajuan',
            'title' => 'Laporan Ajuan Hibah',
            'page_title' => 'Laporan Ajuan Hibah',
            'id_hibah' => $id_hibah,
            'status_revisi_laporan' => $status_revisi_laporan,
            'dataLaporanHibah' => $dataLaporanHibah,
            'catatanRevisi' => $catatanRevisi
        ];

        $data['logFilePendukung'] = LogActivity::select('tbl_log_activity.*', 'users.name as pengupload')->leftJoin('users', 'users.username', '=', 'tbl_log_activity.add_by')->where('id_table', @$dataLaporanHibah->id_laporan_hibah)->where('jenis', 'file_pendukung')->orderBy('created_at', 'ASC')->get();
        $data['logFileDokumentasi'] = LogActivity::select('tbl_log_activity.*', 'users.name as pengupload')->leftJoin('users', 'users.username', '=', 'tbl_log_activity.add_by')->where('id_table', @$dataLaporanHibah->id_laporan_hibah)->where('jenis', 'file_dokumentasi')->orderBy('created_at', 'ASC')->get();
        $data['logFileLaporanKegiatan'] = LogActivity::select('tbl_log_activity.*', 'users.name as pengupload')->leftJoin('users', 'users.username', '=', 'tbl_log_activity.add_by')->where('id_table', @$dataLaporanHibah->id_laporan_hibah)->where('jenis', 'file_laporan_kegiatan')->orderBy('created_at', 'ASC')->get();
        $data['logFileTransaksi'] = LogActivity::select('tbl_log_activity.*', 'users.name as pengupload')->leftJoin('users', 'users.username', '=', 'tbl_log_activity.add_by')->where('id_table', @$dataLaporanHibah->id_laporan_hibah)->where('jenis', 'file_transaksi')->orderBy('created_at', 'ASC')->get();
        $data['logFileTambahan'] = LogActivity::select('tbl_log_activity.*', 'users.name as pengupload')->leftJoin('users', 'users.username', '=', 'tbl_log_activity.add_by')->where('id_table', @$dataLaporanHibah->id_laporan_hibah)->where('jenis', 'file_tambahan')->orderBy('created_at', 'ASC')->get();

        return view('hibah/isiLaporan', $data);
    }

    public function getData(Request $request)
    {

        $orderColumnIndex = $request->input('order.0.column');
        $searchIndex = $request->input('search.value');
        $query = AjuanHibah::select(
            'tbl_ajuan_hibah.*',
            'tbl_laporan_hibah.id_laporan_hibah',
            'kerma_db.nama_institusi',
            'kerma_db.dn_ln',
            'kerma_db.jenis_institusi',

            'lmbg_place_state.nama_lmbg as nama_place_state',
            'lmbg_fakultas.nama_lmbg as nama_fakultas',
            'lmbg_prodi.nama_lmbg as nama_prodi',

            'pengusul.name as nama_pengusul',
            'admin.name as nama_admin',
            'kaprodi.name as nama_verifikator_kaprodi',
            'dekan.name as nama_verifikator_dekan',
            'kaprodi_ref.name as nama_kaprodi_ref',
            'dekan_ref.name as nama_dekan_ref',

            'jenis_hibah.jenis_hibah as nama_jenis_hibah',
            'jenis_hibah.maksimum',
            'jenis_hibah.dl_proposal',
            'jenis_hibah.dl_laporan',
        )
            ->leftJoin('kerma_db', 'kerma_db.id_mou', '=', 'tbl_ajuan_hibah.id_mou')
            ->leftJoin('tbl_laporan_hibah', function ($joinLaporan) {
                $joinLaporan->on('tbl_laporan_hibah.id_hibah', '=', 'tbl_ajuan_hibah.id_hibah')
                    ->whereNull('tbl_laporan_hibah.deleted_at');
            })

            ->leftJoin('ref_lembaga_ums as lmbg_place_state', 'lmbg_place_state.id_lmbg', '=', 'tbl_ajuan_hibah.place_state')
            ->leftJoin('ref_lembaga_ums as lmbg_fakultas', 'lmbg_fakultas.id_lmbg', '=', 'tbl_ajuan_hibah.fakultas')
            ->leftJoin('ref_lembaga_ums as lmbg_prodi', 'lmbg_prodi.id_lmbg', '=', 'tbl_ajuan_hibah.prodi')
            ->leftJoin('users as pengusul', 'pengusul.username', '=', 'tbl_ajuan_hibah.add_by')
            ->leftJoin('users as admin', 'admin.username', '=', 'tbl_ajuan_hibah.verify_admin_by')
            ->leftJoin('users as kaprodi', 'kaprodi.username', '=', 'tbl_ajuan_hibah.verify_kaprodi_by')
            ->leftJoin('users as dekan', 'dekan.username', '=', 'tbl_ajuan_hibah.verify_dekan_by')
            ->leftJoin('users as kaprodi_ref', function ($join) {
                $join->on('kaprodi_ref.jabatan', DB::raw("'Kaprodi'"))
                    ->on('kaprodi_ref.status_tempat', '=', 'tbl_ajuan_hibah.prodi');
            })->leftJoin('users as dekan_ref', function ($join) {
                $join->on('dekan_ref.jabatan', DB::raw("'Dekan'"))
                    ->on('dekan_ref.status_tempat', '=', 'tbl_ajuan_hibah.fakultas');
            })
            ->leftJoin('ref_jenis_hibah as jenis_hibah', 'jenis_hibah.id', '=', 'tbl_ajuan_hibah.jenis_hibah');

        $query->FilterDokumen($request);

        if (session('current_role') == 'user') {
            $query->where("tbl_ajuan_hibah.add_by", Auth::user()->username);
        } elseif (session('current_role') == 'verifikator' && Auth::user()->jabatan == 'Kaprodi') {
            $query->where("tbl_ajuan_hibah.add_by", Auth::user()->username);
            $query->orwhere(function ($q) {
                $q->where("tbl_ajuan_hibah.status_tempat", Auth::user()->status_tempat);
                $q->where("tbl_ajuan_hibah.is_submit", '1');
            });
        } elseif (session('current_role') == 'verifikator' && Auth::user()->jabatan == 'Dekan') {
            $query->where("tbl_ajuan_hibah.add_by", Auth::user()->username);
            $id_lmbg_dekan = RefLembagaUMS::where('nama_lmbg', Auth::user()->status_tempat)
                ->value('id_lmbg');

            if (!$id_lmbg_dekan) {
                $id_lmbg_dekan = RefLembagaUMS::where('nama_lmbg_old', Auth::user()->status_tempat)
                    ->value('id_lmbg');
            }

            $query->orWhere(function ($q) use ($id_lmbg_dekan) {
                if ($id_lmbg_dekan) {
                    $q->where('tbl_ajuan_hibah.place_state', $id_lmbg_dekan)
                        ->where('tbl_ajuan_hibah.is_submit', '1');
                }
            });
        } elseif (session('current_role') == 'admin') {
            $query->where("tbl_ajuan_hibah.is_submit", '1');
        }


        if ($orderColumnIndex == 0) {
            $query->orderByRole();
        }
        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($search = $request->input('search.value')) {
                    $query->where(function ($q) use ($search) {
                        $q->where('pengusul.name', 'like', "%{$search}%")
                            ->orWhere('admin.name', 'like', "%{$search}%")
                            ->orWhere('kaprodi.name', 'like', "%{$search}%")
                            ->orWhere('dekan.name', 'like', "%{$search}%")
                            ->orWhere('jenis_hibah.jenis_hibah', 'like', "%{$search}%")
                            ->orWhere('kerma_db.nama_institusi', 'like', "%{$search}%")
                            ->orWhere('lmbg_fakultas.nama_lmbg', 'like', "%{$search}%")

                            ->orWhere('tbl_ajuan_hibah.judul_proposal', 'like', "%{$search}%")
                            ->orWhere('tbl_ajuan_hibah.institusi_mitra', 'like', "%{$search}%")
                            ->orWhere('tbl_ajuan_hibah.ketua_pelaksana', 'like', "%{$search}%")

                            ->orWhere('lmbg_prodi.nama_lmbg', 'like', "%{$search}%");
                    });
                }
            })
            ->addIndexColumn()
            ->addColumn('nama_place_state', function ($row) {
                return $row->nama_place_state;
            })
            ->addColumn('nama_fakultas', function ($row) {
                return $row->nama_fakultas;
            })
            ->addColumn('nama_prodi', function ($row) {
                return $row->nama_prodi;
            })
            ->addColumn('dekan', function ($row) {
                return $row->nama_verifikator_dekan;
            })
            ->addColumn('kaprodi', function ($row) {
                return $row->nama_verifikator_kaprodi;
            })
            ->addColumn('admin', function ($row) {
                return $row->nama_admin;
            })
            ->addColumn('pengusul', function ($row) {
                return $row->nama_pengusul;
            })
            ->addColumn('status', function ($row) {
                return $row->status_label;
            })
            ->addColumn('tanggal_pelaksanaan', function ($row) {
                return $row->tanggal_pelaksanaan_label;
            })
            ->addColumn('jenis_hibah', function ($row) {
                return $row->jenis_hibah_label;
            })
            ->addColumn('file_kontrak', function ($row) {
                return $row->file_kontrak_label;
            })
            ->addColumn('action', function ($row) {
                return $row->action_buttons_label;
            })
            ->rawColumns(['action', 'tanggal_pelaksanaan', 'pengusul', 'status', 'file_kontrak', 'jenis_hibah'])
            ->make(true);
    }

    public function store_draft(Request $request)
    {
        if ($request->hibah_key !== session('hibah_key') || session('hibah_key') == null) {
            return response()->json(['error' => 'Form tidak valid, silakan reload tab terbaru.'], 403);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'file_lain' => 'nullable|mimes:pdf|max:5120'
            ],
            [
                'file_lain.mimes' => 'File Tambahan Berformat PDF.',
                'file_lain.max' => 'File Tambahan maksimal 5MB.',
            ],
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Bersihkan input pendanaan dari user agar menjadi numerik
        $inputBKUI = (int) str_replace(['.', ','], '', $request->pendanaan_bkui);

        if ($inputBKUI && $request->jenis_hibah) {
            $maksimalBKUI = RefJenisHibah::where('id', $request->jenis_hibah)->first()->maksimum;
            if ($inputBKUI > $maksimalBKUI) {
                return response()->json([
                    'errors' => ['total_bkui' => ['Total Pendanaan BKUI tidak boleh melebihi ' . rupiah($maksimalBKUI) . '.']]
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            // Generate ID jika tidak ada
            $id = $request->id_hibah  ?? 'HBH' . str_replace('-', '', Str::uuid());

            $dataInsert = [
                'is_submit' => '0',
                'judul_proposal' => $request->judul_proposal ?? '',
                'institusi_mitra' => $request->institusi_mitra ?? '',
                'id_mou' => $request->id_mou ?? '',
                'ketua_pelaksana' => $request->ketua_pelaksana ?? '',
                'nidn_ketua_pelaksana' => $request->nidn_ketua_pelaksana ?? '',
                'penanggung_jawab_kegiatan' => $request->penanggung_jawab_kegiatan ?? '',
                // 'nama_kaprodi' => $request->nama_kaprodi ?? '',
                // 'nidn_kaprodi' => $request->nidn_kaprodi ?? '',
                'nama_penanggung_jawab' => $request->nama_penanggung_jawab ?? '',
                'nidn_penanggung_jawab' => $request->nidn_penanggung_jawab ?? '',
                'email' => $request->email ?? '',
                'no_hp' => $request->no_hp ?? '',

                'tgl_mulai' => $request->tgl_mulai ?: null,
                'tgl_selesai' => $request->tgl_selesai ?: null,


                'jenis_hibah' => $request->jenis_hibah ?? '',

                'biaya' => str_replace('.', '', $request->biaya) ?? '',
                'pendanaan_bkui' => str_replace('.', '', $request->pendanaan_bkui) ?? '',
                'pendanaan_lain' => str_replace('.', '', $request->pendanaan_lain) ?? '',


                'fakultas' => $request->fakultas ?? '',
                'prodi' => $request->prodi ?? '',
                'latar_belakang' => sanitize_input($request->latar_belakang ?? ''),
                'tujuan' => sanitize_input($request->tujuan ?? ''),
                'detail_institusi_mitra' => sanitize_input($request->detail_institusi_mitra ?? ''),
                'jenis_kerma' => $request->jenis_kerma ?? '',
                'detail_kerma' => sanitize_input($request->detail_kerma ?? ''),
                'target' => sanitize_input($request->target ?? ''),
                'indikator_keberhasilan' => sanitize_input($request->indikator_keberhasilan ?? ''),
                'rencana' => sanitize_input($request->rencana ?? ''),

                'anggota' => is_array($request->anggota) ? json_encode($request->anggota) : '',
                'peran' => is_array($request->peran) ? json_encode($request->peran) : '',

                'jenis_pengeluaran' => is_array($request->jenis_pengeluaran) ? json_encode($request->jenis_pengeluaran) : '',
                'jumlah_pengeluaran' => is_array($request->jumlah_pengeluaran) ? json_encode($request->jumlah_pengeluaran) : '',
                'satuan' => is_array($request->satuan) ? json_encode($request->satuan) : '',
                'biaya_satuan' => is_array($request->biaya_satuan) ? json_encode($request->biaya_satuan) : '',
                'biaya_total' => is_array($request->biaya_total) ? json_encode($request->biaya_total) : '',
                'sumber_pendanaan' => is_array($request->sumber_pendanaan) ? json_encode($request->sumber_pendanaan) : '',
                'sumber_pendanaan_lain' => is_array($request->sumber_pendanaan_lain) ? json_encode($request->sumber_pendanaan_lain) : '',

            ];

            $allFiles = [];

            if ($request->hasFile('file_lain')) {
                $file_lain = $request->file('file_lain');
                // $path = "uploads/hibah/{$id}";
                $path = "uploads/hibah/file_lain";
                $file_lain = $this->upload_file($file_lain, $path);
                $dataInsert['file_lain'] = $file_lain;

                $dtFiles = [
                    'jenis' => 'file_lain',
                    'path' => $file_lain
                ];

                $allFiles[] = $dtFiles;
            }

            // Simpan atau update data
            if ($request->id_hibah) {
                $dataInsert['status_revisi_kaprodi'] = '1';
                $dataInsert['status_revisi_dekan'] = '1';
                $dataInsert['status_revisi_admin'] = '1';
                $dataInsert['status_revisi_laporan'] = '1';

                $insert = AjuanHibah::where('id_hibah', $request->id_hibah)->update($dataInsert);
                $dataLogketerangan = 'Update';
            } else {
                if ($request->penanggung_jawab_kegiatan == 'kaprodi') {
                    $dataInsert['status'] = 'Menunggu Verifikasi Kaprodi';
                } else if ($request->penanggung_jawab_kegiatan == 'dekan' && Auth::user()->jabatan != 'Dekan') {
                    $dataInsert['status'] = 'Menunggu Verifikasi Dekan';
                } else if ($request->penanggung_jawab_kegiatan == 'dekan' && Auth::user()->jabatan == 'Dekan') {
                    $dataInsert['status_verify_dekan'] = '1';
                    $dataInsert['date_verify_dekan'] = date('Y-m-d H:i:s');
                    $dataInsert['verify_dekan_by'] = Auth::user()->username;
                    $dataInsert['status'] = 'Menunggu Verifikasi Admin';
                } else if (session('current_role') == 'admin') {
                    $dataInsert['status'] = 'Proses TTD Kontrak'; // Set ID jika baru
                }

                $dataInsert['add_by'] = Auth::user()->username;
                $dataInsert['place_state'] = Auth::user()->place_state;
                $dataInsert['status_tempat'] = Auth::user()->status_tempat;
                $dataInsert['jabatan_pengusul'] = Auth::user()->jabatan;

                $dataLogketerangan = 'Baru';
                $dataInsert['id_hibah'] = $id; // Set ID jika baru
                $insert = AjuanHibah::create($dataInsert);
            }
            // Simpan Log

            foreach ($allFiles as $key => $value) {
                $dataLog = [
                    'table' => 'tbl_ajuan_hibah',
                    'id_table' => $id,
                    'jenis' => $value['jenis'],
                    'path' => $value['path'],
                    'keterangan' => $dataLogketerangan,
                    'add_by' => Auth::user()->username,
                    'role' => session('current_role')
                ];
                LogActivity::create($dataLog);
            }


            // if ($emailReceiver) {
            //     $dataEmail = [
            //         'tipe' => $dataLogketerangan,
            //         'receiver' => $emailReceiver
            //     ];

            //     SendEmailHibah::dispatchSync($dataEmail, session('environment'));
            // }

            DB::commit();
            session()->forget('hibah_key');

            if ($insert) {
                return response()->json(['status' => true, 'message' => 'Data berhasil disimpan', 'route' => route('hibah.ajuan')], 200);
            } else {
                return response()->json(['status' => false, 'message' => 'Data gagal disimpan'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }


    public function store(Request $request)
    {
        if ($request->hibah_key !== session('hibah_key') || session('hibah_key') == null) {
            return response()->json(['error' => 'Form tidak valid, silakan reload tab terbaru.'], 403);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'judul_proposal' => 'required',
                'jenis_hibah' => 'required',
                'institusi_mitra' => 'required',
                'ketua_pelaksana' => 'required',
                'penanggung_jawab_kegiatan' => 'required',
                'email' => 'required|email',
                'no_hp' => 'required',
                'biaya' => 'required',
                'pendanaan_bkui' => 'required',
                'fakultas' => 'required',
                'prodi' => 'required',
                'jenis_kerma' => 'required',
                'latar_belakang' => 'required',
                'tujuan' => 'required',
                'detail_institusi_mitra' => 'required',
                'detail_kerma' => 'required',
                'target' => 'required',
                'indikator_keberhasilan' => 'required',
                'rencana' => 'required',
                'file_lain' => 'nullable|mimes:pdf|max:5120'
            ],
            [
                'judul_proposal.required' => 'Judul Proposal harus diisi.',
                'jenis_hibah.required' => 'Jenis Hibah harus diisi.',
                'email.required' => 'Email harus diisi.',
                'email.email' => 'Email tidak sesuai format.',
                'ketua_pelaksana.required' => 'Ketua Pelaksana harus diisi.',
                'penanggung_jawab_kegiatan.required' => 'Penanggung Jawab harus diisi.',
                'institusi_mitra.required' => 'Institusi Mitra harus diisi.',
                'no_hp.required' => 'Nomor HP harus diisi.',
                'biaya.required' => 'Total Biaya harus diisi.',
                'pendanaan_bkui.required' => 'Pendanaan dari BKUI harus diisi.',
                'fakultas.required' => 'Fakultas harus diisi.',
                'prodi.required' => 'Program Studi harus diisi.',
                'jenis_kerma.required' => 'Jenis Kerja Sama harus diisi.',
                'latar_belakang.required' => 'Latar Belakang Sama harus diisi.',
                'tujuan.required' => 'Tujuan harus diisi.',
                'detail_institusi_mitra.required' => 'Detail Institusi Mitra harus diisi.',
                'detail_kerma.required' => 'Detail Kerja Sama harus diisi.',
                'target.required' => 'Target Output dan Outcome harus diisi.',
                'indikator_keberhasilan.required' => 'Indikator Keberhasilan harus diisi.',
                'rencana.required' => ' Rencana Keberlanjutan harus diisi.',
                'file_lain.mimes' => 'File Tambahan Berformat PDF.',
                'file_lain.max' => 'File Tambahan maksimal 5MB.',
            ],
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Ambil maksimal pendanaan dari setting
        $maksimalBKUI = RefJenisHibah::where('id', $request->jenis_hibah)->first()->maksimum;

        // Bersihkan input pendanaan dari user agar menjadi numerik
        $inputBKUI = (int) str_replace(['.', ','], '', $request->pendanaan_bkui);

        // Cek apakah melebihi batas
        if ($inputBKUI > $maksimalBKUI) {
            return response()->json([
                'errors' => ['total_bkui' => ['Total Pendanaan BKUI tidak boleh melebihi ' . rupiah($maksimalBKUI) . '.']]
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Generate ID jika tidak ada
            $id = $request->id_hibah  ?? 'HBH' . str_replace('-', '', Str::uuid());

            $dataInsert = [
                'is_submit' => '1',
                'judul_proposal' => $request->judul_proposal ?? '',
                'institusi_mitra' => $request->institusi_mitra ?? '',
                'id_mou' => $request->id_mou ?? '',
                'ketua_pelaksana' => $request->ketua_pelaksana ?? '',
                'nidn_ketua_pelaksana' => $request->nidn_ketua_pelaksana ?? '',
                'penanggung_jawab_kegiatan' => $request->penanggung_jawab_kegiatan ?? '',
                // 'nama_kaprodi' => $request->nama_kaprodi ?? '',
                // 'nidn_kaprodi' => $request->nidn_kaprodi ?? '',
                'nama_penanggung_jawab' => $request->nama_penanggung_jawab ?? '',
                'nidn_penanggung_jawab' => $request->nidn_penanggung_jawab ?? '',
                'email' => $request->email ?? '',
                'no_hp' => $request->no_hp ?? '',
                'tgl_mulai' => $request->tgl_mulai ?? '',
                'tgl_selesai' => $request->tgl_selesai ?? '',

                'jenis_hibah' => $request->jenis_hibah ?? '',

                'biaya' => str_replace('.', '', $request->biaya) ?? '',
                'pendanaan_bkui' => str_replace('.', '', $request->pendanaan_bkui) ?? '',
                'pendanaan_lain' => str_replace('.', '', $request->pendanaan_lain) ?? '',


                'fakultas' => $request->fakultas ?? '',
                'prodi' => $request->prodi ?? '',
                'latar_belakang' => sanitize_input($request->latar_belakang ?? ''),
                'tujuan' => sanitize_input($request->tujuan ?? ''),
                'detail_institusi_mitra' => sanitize_input($request->detail_institusi_mitra ?? ''),
                'jenis_kerma' => $request->jenis_kerma ?? '',
                'detail_kerma' => sanitize_input($request->detail_kerma ?? ''),
                'target' => sanitize_input($request->target ?? ''),
                'indikator_keberhasilan' => sanitize_input($request->indikator_keberhasilan ?? ''),
                'rencana' => sanitize_input($request->rencana ?? ''),

                'anggota' => is_array($request->anggota) ? json_encode($request->anggota) : '',
                'peran' => is_array($request->peran) ? json_encode($request->peran) : '',

                'jenis_pengeluaran' => is_array($request->jenis_pengeluaran) ? json_encode($request->jenis_pengeluaran) : '',
                'jumlah_pengeluaran' => is_array($request->jumlah_pengeluaran) ? json_encode($request->jumlah_pengeluaran) : '',
                'satuan' => is_array($request->satuan) ? json_encode($request->satuan) : '',
                'biaya_satuan' => is_array($request->biaya_satuan) ? json_encode($request->biaya_satuan) : '',
                'biaya_total' => is_array($request->biaya_total) ? json_encode($request->biaya_total) : '',
                'sumber_pendanaan' => is_array($request->sumber_pendanaan) ? json_encode($request->sumber_pendanaan) : '',
                'sumber_pendanaan_lain' => is_array($request->sumber_pendanaan_lain) ? json_encode($request->sumber_pendanaan_lain) : '',

            ];

            if (Auth::user()->jabatan == 'Dekan') {
                $dataInsert['status_verify_kaprodi'] = '1';
                $dataInsert['date_verify_kaprodi'] = date('Y-m-d H:i:s');
                $dataInsert['verify_kaprodi_by'] = Auth::user()->username;
                $dataInsert['status_verify_dekan'] = '1';
                $dataInsert['date_verify_dekan'] = date('Y-m-d H:i:s');
                $dataInsert['verify_dekan_by'] = Auth::user()->username;
            } elseif (Auth::user()->jabatan == 'Kaprodi' || (session('current_role') == 'verifikator' && Auth::user()->jabatan == 'Kaprodi')) {
                $dataInsert['status_verify_kaprodi'] = '1';
                $dataInsert['date_verify_kaprodi'] = date('Y-m-d H:i:s');
                $dataInsert['verify_kaprodi_by'] = Auth::user()->username;
            } elseif (session('current_role') == 'admin') {
                $dataInsert['status_verify_kaprodi'] = '1';
                $dataInsert['date_verify_kaprodi'] = date('Y-m-d H:i:s');
                $dataInsert['verify_kaprodi_by'] = Auth::user()->username;
                $dataInsert['status_verify_dekan'] = '1';
                $dataInsert['date_verify_dekan'] = date('Y-m-d H:i:s');
                $dataInsert['verify_dekan_by'] = Auth::user()->username;
                $dataInsert['status_verify_admin'] = '1';
                $dataInsert['date_verify_admin'] = date('Y-m-d H:i:s');
                $dataInsert['verify_admin_by'] = Auth::user()->username;
            }

            $allFiles = [];

            if ($request->hasFile('file_lain')) {
                $file_lain = $request->file('file_lain');
                // $path = "uploads/hibah/{$id}";
                $path = "uploads/hibah/file_lain";
                $file_lain = $this->upload_file($file_lain, $path);
                $dataInsert['file_lain'] = $file_lain;

                $dtFiles = [
                    'jenis' => 'file_lain',
                    'path' => $file_lain
                ];

                $allFiles[] = $dtFiles;
            }

            // Simpan atau update data
            if ($request->id_hibah) {
                $dataInsert['status_revisi_kaprodi'] = '1';
                $dataInsert['status_revisi_dekan'] = '1';
                $dataInsert['status_revisi_admin'] = '1';
                // dd($dataInsert);
                $insert = AjuanHibah::where('id_hibah', $request->id_hibah)->update($dataInsert);
                $dataLogketerangan = 'Update';
            } else {
                if ($request->penanggung_jawab_kegiatan == 'kaprodi') {
                    $dataInsert['status'] = 'Menunggu Verifikasi Kaprodi';
                } else if ($request->penanggung_jawab_kegiatan == 'dekan' && Auth::user()->jabatan != 'Dekan') {
                    $dataInsert['status'] = 'Menunggu Verifikasi Dekan';
                } else if ($request->penanggung_jawab_kegiatan == 'dekan' && Auth::user()->jabatan == 'Dekan') {
                    $dataInsert['status_verify_dekan'] = '1';
                    $dataInsert['date_verify_dekan'] = date('Y-m-d H:i:s');
                    $dataInsert['verify_dekan_by'] = Auth::user()->username;
                    $dataInsert['status'] = 'Menunggu Verifikasi Admin';
                } else if (session('current_role') == 'admin') {
                    $dataInsert['status'] = 'Proses TTD Kontrak'; // Set ID jika baru
                }

                $dataInsert['add_by'] = Auth::user()->username;
                $dataInsert['place_state'] = Auth::user()->place_state;
                $dataInsert['status_tempat'] = Auth::user()->status_tempat;
                $dataInsert['jabatan_pengusul'] = Auth::user()->jabatan;

                $dataLogketerangan = 'Baru';
                $dataInsert['id_hibah'] = $id; // Set ID jika baru
                $insert = AjuanHibah::create($dataInsert);
            }
            // Simpan Log

            if ($insert) {
                foreach ($allFiles as $key => $value) {
                    $dataLog = [
                        'table' => 'tbl_ajuan_hibah',
                        'id_table' => $id,
                        'jenis' => $value['jenis'],
                        'path' => $value['path'],
                        'keterangan' => $dataLogketerangan,
                        'add_by' => Auth::user()->username,
                        'role' => session('current_role')
                    ];
                    LogActivity::create($dataLog);
                }
                DB::commit();
                session()->forget('hibah_key');
                return response()->json(['status' => true, 'message' => 'Data berhasil disimpan', 'route' => route('hibah.ajuan')], 200);
            }
            return response()->json(['status' => false, 'message' => 'Terdapat Masalah Ketika Penyimpanan'], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function laporan_store(Request $request)
    {

        $id = $request->id_laporan_hibah  ?? 'LPRHBH' . str_replace('-', '', Str::uuid());

        $validator = Validator::make(
            $request->all(),
            [
                'detail_aktivitas' => 'required',
                'target_laporan' => 'required',
                'hasil_laporan' => 'required',
                'rencana_tindak_lanjut' => 'required',

                'file_pendukung' => ($request->id_laporan_hibah ? 'nullable' : 'required') . '|file|mimes:pdf|max:5120',
                'file_dokumentasi' => ($request->id_laporan_hibah ? 'nullable' : 'required') . '|file|mimes:pdf|max:5120',
                'file_laporan_kegiatan' => ($request->id_laporan_hibah ? 'nullable' : 'required') . '|file|mimes:pdf|max:5120',
                'file_transaksi' => ($request->id_laporan_hibah ? 'nullable' : 'required') . '|file|mimes:pdf|max:5120',
                'file_tambahan' => 'nullable|file|mimes:pdf|max:5120',
            ],
            [
                'detail_aktivitas.required' => 'Detail Aktivitas harus diisi.',
                'target_laporan.required' => 'Target Output dan Outcome harus diisi.',
                'hasil_laporan.required' => 'Hasil dan Dampak Mitra harus diisi.',
                'rencana_tindak_lanjut.required' => 'Rencana Tindak Lanjut harus diisi.',

                'file_pendukung.required' => 'File pendukung wajib diunggah.',
                'file_pendukung.max' => 'Ukuran file pendukung maksimal 5 MB.',
                'file_pendukung.mimes' => 'Format File Pendukung Harus PDF.',

                'file_dokumentasi.required' => 'File dokumen bukti kegiatan wajib diunggah.',
                'file_dokumentasi.max' => 'Ukuran file dokumen bukti kegiatan maksimal 5 MB.',
                'file_dokumentasi.mimes' => 'Format file dokumen bukti kegiatan harus PDF.',

                'file_laporan_kegiatan.required' => 'File laporan kegiatan wajib diunggah.',
                'file_laporan_kegiatan.max' => 'Ukuran file laporan kegiatan maksimal 5 MB.',
                'file_laporan_kegiatan.mimes' => 'Format file laporan kegiatan harus PDF.',

                'file_transaksi.required' => 'File laporan keuangan wajib diunggah.',
                'file_transaksi.max' => 'Ukuran file transaksi maksimal 5 MB.',
                'file_transaksi.mimes' => 'Format file transaksi harus PDF.',

                'file_tambahan.max' => 'Ukuran file tambahan maksimal 5 MB.',
                'file_tambahan.mimes' => 'Format file tambahan harus PDF.',
            ]
        );


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->id_laporan_hibah) {
            $dataExistLaporan = LaporanHibah::where('id_laporan_hibah', $request->id_laporan_hibah)->firstorfail();
        }
        $dataExistHibah = AjuanHibah::where('id_hibah', $request->id_hibah)->firstorfail();

        DB::beginTransaction();
        try {
            // Generate ID jika tidak ada

            $dataInsert = [
                'id_hibah' => $request->id_hibah ?? '',

                'detail_aktivitas' => $request->detail_aktivitas ?? '',
                'target_laporan' => $request->target_laporan ?? '',
                'hasil_laporan' => $request->hasil_laporan ?? '',
                'rencana_tindak_lanjut' => $request->rencana_tindak_lanjut ?? '',

                'jenis_pengeluaran' => is_array($request->jenis_pengeluaran) ? json_encode($request->jenis_pengeluaran) : '',
                'jumlah_pengeluaran' => is_array($request->jumlah_pengeluaran) ? json_encode($request->jumlah_pengeluaran) : '',
                'satuan' => is_array($request->satuan) ? json_encode($request->satuan) : '',
                'biaya_satuan' => is_array($request->biaya_satuan) ? json_encode($request->biaya_satuan) : '',
                'biaya_total' => is_array($request->biaya_total) ? json_encode($request->biaya_total) : '',
            ];

            $allFiles = [];

            if ($request->hasFile('file_pendukung')) {
                $file_pendukung = $request->file('file_pendukung');
                $path = "uploads/hibah/laporan/file_pendukung";
                $file_pendukung = $this->upload_file($file_pendukung, $path);
                $dataInsert['file_pendukung'] = $file_pendukung;

                $dtFiles = [
                    'jenis' => 'file_pendukung',
                    'path' => $file_pendukung
                ];

                $allFiles[] = $dtFiles;
            }

            if ($request->hasFile('file_dokumentasi')) {
                $file_dokumentasi = $request->file('file_dokumentasi');
                $path = "uploads/hibah/laporan/file_dokumentasi";
                $file_dokumentasi = $this->upload_file($file_dokumentasi, $path);
                $dataInsert['file_dokumentasi'] = $file_dokumentasi;

                $dtFiles = [
                    'jenis' => 'file_dokumentasi',
                    'path' => $file_dokumentasi
                ];

                $allFiles[] = $dtFiles;
            }

            if ($request->hasFile('file_laporan_kegiatan')) {
                $file_laporan_kegiatan = $request->file('file_laporan_kegiatan');
                $path = "uploads/hibah/laporan/file_laporan_kegiatan";
                $file_laporan_kegiatan = $this->upload_file($file_laporan_kegiatan, $path);
                $dataInsert['file_laporan_kegiatan'] = $file_laporan_kegiatan;

                $dtFiles = [
                    'jenis' => 'file_laporan_kegiatan',
                    'path' => $file_laporan_kegiatan
                ];

                $allFiles[] = $dtFiles;
            }

            if ($request->hasFile('file_transaksi')) {
                $file_transaksi = $request->file('file_transaksi');
                $path = "uploads/hibah/laporan/file_transaksi";
                $file_transaksi = $this->upload_file($file_transaksi, $path);
                $dataInsert['file_transaksi'] = $file_transaksi;

                $dtFiles = [
                    'jenis' => 'file_transaksi',
                    'path' => $file_transaksi
                ];

                $allFiles[] = $dtFiles;
            }

            if ($request->hasFile('file_tambahan')) {
                $file_tambahan = $request->file('file_tambahan');
                $path = "uploads/hibah/laporan/file_tambahan";
                $file_tambahan = $this->upload_file($file_tambahan, $path);
                $dataInsert['file_tambahan'] = $file_tambahan;
                $dtFiles = [
                    'jenis' => 'file_tambahan',
                    'path' => $file_tambahan
                ];

                $allFiles[] = $dtFiles;
            }

            // Simpan atau update data
            if ($request->id_laporan_hibah) {
                $dataLogketerangan = 'Update';
                $insert = LaporanHibah::where('id_laporan_hibah', $request->id_laporan_hibah)->update($dataInsert);

                AjuanHibah::where('id_hibah', $request->id_hibah)->update(
                    ['status_revisi_laporan' => '1']
                );
            } else {
                $dataLogketerangan = 'Baru';
                $dataInsert['id_laporan_hibah'] = $id; // Set ID jika baru
                $dataInsert['add_by'] = Auth::user()->username; // Set ID jika baru
                $insert = LaporanHibah::create($dataInsert);
            }

            if ($insert) {
                foreach ($allFiles as $key => $value) {
                    $dataLog = [
                        'table' => 'tbl_laporan_hibah',
                        'id_table' => $id,
                        'jenis' => $value['jenis'],
                        'path' => $value['path'],
                        'keterangan' => $dataLogketerangan,
                        'add_by' => Auth::user()->username
                    ];
                    LogActivity::create($dataLog);
                }

                DB::commit();
                return response()->json(['status' => true, 'message' => 'Data berhasil disimpan', 'route' => route('hibah.ajuan')], 200);
            }
            return response()->json(['status' => false, 'message' => 'Terdapat Masalah Ketika Penyimpanan'], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function detailHibah(Request $request)
    {
        $id_hibah = $request->id_hibah;
        $query = AjuanHibah::withFullJoin();
        $dataHibah =  $query
            ->where('tbl_ajuan_hibah.id_hibah', $id_hibah)->firstOrFail();

        $dataLaporanHibah = LaporanHibah::where('id_hibah', $id_hibah)->first();
        // return $dataHibah;
        return response()->json(view('hibah/detail', ['dataHibah' => $dataHibah, 'dataLaporanHibah' => $dataLaporanHibah])->render());
    }

    public function detailLaporanHibah(Request $request)
    {
        $id_laporan_hibah = $request->id_laporan_hibah;
        $dataLaporanHibah = LaporanHibah::where('id_laporan_hibah', $id_laporan_hibah)->first();

        return response()->json(view('hibah/detail_laporan', ['dataLaporanHibah' => $dataLaporanHibah])->render());
    }

    public function showVerifikasiTahap(Request $request)
    {
        $id_hibah = $request->id_hibah;
        $tahap = $request->tahap;

        $query = AjuanHibah::withFullJoin();
        $dataHibah =  $query
            ->where('tbl_ajuan_hibah.id_hibah', $id_hibah)->firstOrFail();
        // return $dataHibah;
        $data = [
            'dataHibah' => $dataHibah,
            'tahap' => $tahap,
        ];

        return response()->json(view('hibah/showVerifikasiTahap', $data)->render());
    }

    public function VerifikasiTahap(Request $request)
    {
        $tahapAllowed = ['1', '2'];

        if (!in_array($request->tahap, $tahapAllowed)) {
            return response()->json(['error' => 'Form tidak valid, silakan reload tab terbaru.'], 403);
        }

        $tahap = $request->tahap == '1' ? 'satu' : 'dua';
        $field_pencairan = 'pencairan_tahap_' . $tahap;
        $field_metode = 'metode_tahap_' . $tahap;
        $field_file_bukti_transfer = 'file_bukti_transfer_tahap_' . $tahap;

        $hibah = AjuanHibah::where('id_hibah', $request->id_hibah)->firstOrFail();
        if ($request->id_laporan_hibah) {
            $dataExistLaporan = LaporanHibah::where('id_laporan_hibah', $request->id_laporan_hibah)->firstOrFail();
        }

        $validator = Validator::make(
            $request->all(),
            [
                $field_pencairan => 'required',
                'metode_pembayaran' => 'required',
                $field_file_bukti_transfer => 'required|mimes:pdf|max:5120',
            ],
            [
                $field_pencairan . '.required' => 'Nominal Pencairan Tahap ' . ucwords($tahap) . ' Harus Diisi.',
                'metode_pembayaran' . '.required' => 'Metode Pembayaran Harus Diisi',
                $field_file_bukti_transfer . '.required' => 'File Bukti Transfer harus diisi',
                $field_file_bukti_transfer . '.mimes' => 'File Bukti Transfer harus berformat PDF',
                $field_file_bukti_transfer . '.max' => 'File Bukti Transfer Maksimal 5 MB',
            ],
        );

        $validator->after(function ($validator) use ($request, $hibah, $field_pencairan) {
            $danaPencairan = (float) str_replace('.', '', $request->input($field_pencairan)); // pastikan input rupiah diformat
            $sisaDana = (float) $hibah->sisa_dana;

            if ($danaPencairan > $sisaDana) {
                $validator->errors()->add($field_pencairan, 'Sisa dana tidak mencukupi untuk pencairan.');
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $danaPencairan = str_replace('.', '', $request->input($field_pencairan));
            $dataInsert = [
                $field_pencairan => $danaPencairan,
                $field_metode => $request->metode_pembayaran,
                'date_verify_tahap_' . $tahap => date('Y-m-d H:i:s'),
                'verify_tahap_' . $tahap . '_by' => Auth::user()->username,
                'status_verify_tahap_' . $tahap => '1',
            ];

            if ($request->hasFile($field_file_bukti_transfer)) {
                $file = $request->file($field_file_bukti_transfer);
                $path = "uploads/hibah/bukti_transfer";
                $file = $this->upload_file($file, $path);
                $dataInsert[$field_file_bukti_transfer] = $file;
            }

            $dataInsert['sisa_dana'] = $hibah->sisa_dana - $danaPencairan;
            if ($hibah->sisa_dana < $danaPencairan) {
                return response()->json(['errors' => 'Sisa dana tidak mencukupi untuk pencairan.'], 422);
            }

            $hibah->update($dataInsert);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data Berhasil di Simpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function dokumenPendukung()
    {
        $role = session('current_role');
        $data = [
            'li_active' => 'dokumenPendukungHibah',
            'title' => 'Dokumen Pendukung',
            'page_title' => 'Dokumen Pendukung',
        ];

        if ($role == 'admin') {
            $data['dokumenPendukung'] = DokumenPendukungHibah::all();
        } else {
            $data['dokumenPendukung'] = DokumenPendukungHibah::where('is_active', '1')->get();
        }

        return view('hibah/dokumenPendukung', $data);
    }

    public function loadAllDokumenIframe()
    {
        try {
            $role = session('current_role');

            if ($role == 'admin') {
                $dokumenList = DokumenPendukungHibah::all();
            } else {
                $dokumenList = DokumenPendukungHibah::where('is_active', '1')->get();
            }

            $results = [];

            foreach ($dokumenList as $dokumen) {
                $filePath = $dokumen->link_dokumen ?? asset('storage/' . rawurlencode($dokumen->file_dokumen));
                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                $iframeSrc = '';
                $success = true;
                $message = '';

                if ($dokumen->link_dokumen != null) {
                    $iframeSrc = $dokumen->link_dokumen;
                } else {
                    if (in_array($ext, ['pdf', 'png', 'jpg', 'jpeg', 'webp'])) {
                        $iframeSrc = $filePath;
                    } elseif (in_array($ext, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'])) {
                        $iframeSrc = 'https://docs.google.com/gview?url=' . urlencode($filePath) . '&embedded=true';
                    } else {
                        $success = false;
                        $message = 'Format file tidak didukung untuk preview';
                    }
                }

                if ($success) {
                    if (str_starts_with($iframeSrc, 'https://www.youtube.com')) {
                        $html = '<iframe width="100%" height="1200" src="' . $iframeSrc . '"
                            title="YouTube" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>';
                    } else {
                        $html = '<iframe 
                            src="' . $iframeSrc . '" 
                            frameborder="0" 
                            width="100%" 
                            height="1200"
                            style="border: none; border-radius: 8px; opacity: 0;"
                            onload="this.style.opacity=\'1\'"
                            class="dokumen-iframe">
                        </iframe>';
                    }
                } else {
                    $html = '<div class="alert alert-warning">
                            <i class="bx bx-info-circle me-2"></i>
                            ' . $message . '
                        </div>';
                }

                $results[] = [
                    'uuid' => $dokumen->uuid,
                    'success' => $success,
                    'html' => $html,
                    'message' => $message
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $results,
                'total' => count($results)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeDokumenPendukung(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nama_dokumen' => 'required',
                'file_dokumen' => 'nullable|mimes:pdf,docx,doc|max:10240',
                'link_dokumen' => 'nullable|url',
            ],
            [
                'file_dokumen.max' => 'File Dokumen maksimal 10MB.',
                'file_dokumen.mimes' => 'File Dokumen Berformat PDF, DOCX, dan DOC.',
                'link_dokumen.url' => 'Format link tidak valid.',
            ]
        );


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('file_dokumen') && $request->link_dokumen) {
            return response()->json(['status' => false, 'message' => "Pilih Salah Satu Masukkan Link atau Upload Dokumen"], 200);
        }

        DB::beginTransaction();
        try {
            // Generate ID jika tidak ada
            $uuid = $request->uuid ?? Str::uuid();
            $cekDokumen = DokumenPendukungHibah::where('uuid', $uuid)->first();

            $dataInsert = [
                'nama_dokumen' => $request->nama_dokumen,
                'file_dokumen' =>  $cekDokumen->file_dokumen ?? null,
                'link_dokumen' => null
            ];

            // Simpan file SK Mitra
            if ($request->hasFile('file_dokumen')) {
                $file_dokumen = $request->file('file_dokumen');
                $path = "uploads/dokumen_pendukung_hibah";
                $filePath = $this->upload_file($file_dokumen, $path);
                $dataInsert['file_dokumen'] = $filePath;
            } else if ($request->link_dokumen) {
                // if ($cekDokumen && $cekDokumen->file_dokumen) {
                //     Storage::disk('public')->delete($cekDokumen->file_dokumen);
                // }
                $dataInsert['link_dokumen'] = $request->link_dokumen;
            }

            // Simpan atau update data
            if ($cekDokumen) {
                $cekDokumen->update($dataInsert);
            } else {
                $dataInsert['uuid'] = $uuid;
                DokumenPendukungHibah::create($dataInsert);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data berhasil disimpan'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroyDokumenPendukung(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = DokumenPendukungHibah::where('uuid', $request->uuid)->firstOrFail();
            $user->delete();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Dokumen Berhasil di Hapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => true, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function setDokumen(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataUpdate = [
                'is_active' => $request->is_active
            ];
            $user = DokumenPendukungHibah::where('id', $request->id)->firstOrFail();
            $user->update($dataUpdate);
            if ($request->is_active) {
                $status = 'Aktifkan';
            } else {
                $status = 'dinonaktifkan';
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Dokumen Berhasil di ' . $status]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function showRevisi(Request $request)
    {
        $id_hibah = $request->id_hibah;
        $field = $request->field;
        $query = AjuanHibah::withFullJoin();
        $dataHibah =  $query
            ->where('tbl_ajuan_hibah.id_hibah', $id_hibah)->firstOrFail();

        if ($field == 'catatan_laporan') {
            $urlEdit = route('hibah.isiLaporan', ['id_hibah' => $dataHibah->id_hibah]);
        } else {
            $urlEdit = route('hibah.edit', ['id_hibah' => $dataHibah->id_hibah]);
        }

        return response()->json(view('hibah/showRevisi', ['dataHibah' => $dataHibah, 'urlEdit' => $urlEdit, 'catatan' => $dataHibah->{$field}])->render());
    }

    public function markRevisiDone(Request $request)
    {
        $id = $request->id_hibah;
        $field = $request->field;
        $hibah = AjuanHibah::find($id);
        if (!$hibah) {
            return response()->json(['success' => false], 404);
        }

        $hibah->{$field} = '1'; // atau flag lain sesuai kolom db
        $hibah->save();

        return response()->json(['success' => true]);
    }


    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $hibah = AjuanHibah::where('id_hibah', $request->id_hibah)->firstOrFail();
            $hibah->delete();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data Berhasil di Hapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function verifikasi(Request $request)
    {
        DB::beginTransaction();
        try {

            $tipeAllowed = [
                'selesai',
                'laporan',
                'kaprodi',
                'dekan',
                'admin',
            ];
            $statusAllowed = [
                '1',
                '0',
            ];

            if (!in_array($request->tipe, $tipeAllowed) || !in_array($request->status, $statusAllowed)) {
                return response()->json(['error' => 'Form tidak valid, silakan reload tab terbaru.'], 403);
            }

            $tipe = $request->tipe;
            $dataInsert = [];
            if ($request->status == '0') {
                $dataInsert['catatan_' . $tipe] = $request->revisi;
                $dataInsert['status_revisi_' . $tipe] = $request->status;

                if ($tipe == 'kaprodi') {
                    $dataInsert['status'] = 'Menunggu Revisi Pengusul';
                } else if ($tipe == 'dekan') {
                    $dataInsert['status'] = 'Menunggu Revisi Kaprodi';
                } else if ($tipe == 'admin') {
                    $dataInsert['status'] = 'Menunggu Revisi Pengusul oleh Admin';
                } else if ($tipe == 'laporan') {
                    $dataInsert['status'] = 'Menunggu Revisi Laporan oleh Pengusul';
                }
            }

            if ($tipe != 'selesai') {
                $dataInsert['status_verify_' . $tipe] = $request->status;
                $dataInsert['status_revisi_' . $tipe] = $request->status;
                $dataInsert['date_verify_' . $tipe] = date('Y-m-d H:i:s');

                if ($tipe == 'kaprodi') {
                    $dataInsert['status'] = 'Menunggu Verifikasi Admin';
                } else if ($tipe == 'dekan') {
                    $dataInsert['status'] = 'Menunggu Verifikasi Admin';
                } else if ($tipe == 'admin' && $request->status == '1') {
                    $dataInsert['dana_disetujui_bkui'] = $request->dana_disetujui_bkui ?? '';
                    $dataInsert['sisa_dana'] = $request->dana_disetujui_bkui ?? '';
                    $dataInsert['status'] = 'Proses TTD Kontrak';
                } else if ($tipe == 'laporan') {
                    $existLaporan = LaporanHibah::where('id_hibah', $request->id_hibah)->exists();
                    if (!$existLaporan) {
                        return response()->json(['status' => false, 'message' => 'Pengusul Belum Mengisi Laporan.'], 422);
                    }
                    $dataInsert['status'] = 'Proses Pencairan Tahap Dua';
                }
            } else {
                $dataInsert['status_' . $tipe] = $request->status;
                $dataInsert['date_' . $tipe] = date('Y-m-d H:i:s');
                $dataInsert['status'] = 'Ajuan Selesai';
            }

            $dataInsert['verify_' . $tipe . '_by'] = Auth::user()->username ?? '';

            // return response()->json($dataInsert, 500);
            $hibah = AjuanHibah::where('id_hibah', $request->id_hibah)->firstOrFail();
            $hibah->update($dataInsert);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data Berhasil di Simpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function uploadFileKontrak(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'file' => 'required|file|mimes:pdf|max:5120',
                'id_hibah' => 'required|string',
                'flag' => 'required|in:file_kontrak'
            ],
            [
                'file.required' => 'Tidak ada file yang diunggah.',
                'file.file' => 'File yang diunggah tidak valid.',
                'file.max' => 'Ukuran file maksimal adalah 5MB.',
                'file.mimes' => 'File Dokumen Berformat PDF.',
                'id_hibah.required' => 'ID rekognisi tidak boleh kosong.',
                'id_hibah.string' => 'ID rekognisi harus berupa teks.',
                'flag.required' => 'Tipe file (flag) wajib diisi.',
                'flag.in' => 'Tipe file yang dipilih tidak valid.'
            ]
        );


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {

            $dataInsert = [];
            $dataExist = AjuanHibah::where('id_hibah', $request->id_hibah)->firstOrFail();

            if ($request->hasFile('file')) {
                $flag = $request->file('file');
                $path = "uploads/hibah/{$request->id_hibah}/{$request->flag}";
                $fileUpload = $this->upload_file($flag, $path);
                $dataInsert[$request->flag] = $fileUpload;
            } else {
                if (!empty($dataExist) && $dataExist->{$request->flag} != null && $dataExist->{$request->flag} != '') {
                } else {
                    $dataInsert['{$request->flag}'] = '';
                }
            }

            AjuanHibah::where('id_hibah', $request->id_hibah)->update($dataInsert);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data berhasil disimpan'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function export_proposal($id)
    {
        $hibah = AjuanHibah::withFullJoin()->where('tbl_ajuan_hibah.id_hibah', $id)->firstOrFail();

        $data = [
            'hibah' => $hibah,
            'anggota' => json_decode($hibah->anggota),
            'jenis_pengeluaran' => json_decode($hibah->jenis_pengeluaran),
            'jumlah_pengeluaran' => json_decode($hibah->jumlah_pengeluaran),
            'biaya_satuan' => json_decode($hibah->biaya_satuan),
            'satuan' => json_decode($hibah->satuan),
            'biaya_total' => json_decode($hibah->biaya_total),
            'sumber_pendanaan' => json_decode($hibah->sumber_pendanaan),
            'tanggal_pelaksanaan' => TanggalIndonesia($hibah->tgl_mulai) . ' - ' . TanggalIndonesia($hibah->tgl_selesai),
            'tgl_mulai' => TanggalIndonesia($hibah->tgl_mulai),
            'tgl_selesai' => TanggalIndonesia($hibah->tgl_selesai),
        ];

        $mainPdfPath = storage_path('app/temp/proposal_' . $hibah->id . '.pdf');
        if (!file_exists(dirname($mainPdfPath))) {
            mkdir(dirname($mainPdfPath), 0777, true);
        }

        Pdf::loadView('hibah.print_proposal', $data)->save($mainPdfPath);

        $pdf = new Fpdi();

        $pdf->SetTitle('PROPOSAL AJUAN HIBAH - ' . strtoupper($hibah->judul_proposal), true);

        $addPdfToMerger = function ($path) use ($pdf) {
            try {
                $pageCount = $pdf->setSourceFile($path);
                for ($page = 1; $page <= $pageCount; $page++) {
                    $tpl = $pdf->importPage($page);
                    $size = $pdf->getTemplateSize($tpl);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($tpl);
                }
            } catch (\Exception $e) {
                // Jika gagal parsing PDF (kompresi tidak didukung), skip
                $pdf->AddPage();
                $pdf->SetFont('Helvetica', '', 12);
                $pdf->MultiCell(0, 10, "Gagal menambahkan file: {$path}\nError: " . $e->getMessage(), 0, 'L');
            }
        };

        $addPdfToMerger($mainPdfPath);

        $filePaths = [
            'BUKTI TAMBAHAN' => $hibah->file_lain,
            'FILE KONTRAK' => $hibah->file_kontrak,
            'BUKTI TRANSFER TAHAP 1' => $hibah->file_bukti_transfer_tahap_satu,
            'BUKTI TRANSFER TAHAP 2' => $hibah->file_bukti_transfer_tahap_dua,
        ];

        foreach ($filePaths as $title => $relativePath) {
            if ($relativePath) {
                $relativePath = preg_replace('#/+#', '/', $relativePath);
                $fullPath = storage_path('app/public/' . ltrim($relativePath, '/'));

                // Pastikan file benar-benar ada dan bisa diproses
                if (file_exists($fullPath)) {
                    try {
                        // Coba ambil jumlah halaman untuk memvalidasi file PDF
                        $pageCount = $pdf->setSourceFile($fullPath);

                        // Tambahkan halaman judul (jika file valid)
                        $pdf->AddPage();
                        $pageWidth = $pdf->GetPageWidth();
                        $pageHeight = $pdf->GetPageHeight();
                        $pdf->SetFont('Helvetica', 'B', 20);
                        $textWidth = $pdf->GetStringWidth($title);
                        $x = ($pageWidth - $textWidth) / 2;
                        $y = $pageHeight / 2;
                        $pdf->SetXY($x, $y);
                        $pdf->Cell($textWidth, 10, $title, 0, 1, 'C');

                        // Tambahkan isi PDF
                        $addPdfToMerger($fullPath);
                    } catch (\Exception $e) {
                        // Jika gagal dibuka sebagai PDF, skip (tidak tampilkan judul juga)
                        continue;
                    }
                }
            }
        }



        if (file_exists($mainPdfPath)) {
            unlink($mainPdfPath);
        }

        return response($pdf->Output('S', 'AJUAN_HIBAH_' . strtoupper($hibah->judul_proposal) . '.pdf'), 200)
            ->header('Content-Type', 'application/pdf');
    }


    // public function export_proposal($id)
    // {
    //     $hibah = AjuanHibah::withFullJoin()->where('id_hibah', $id)->firstOrFail();

    //     $data = [
    //         'hibah' => $hibah,
    //         'anggota' => json_decode($hibah->anggota),
    //         'jenis_pengeluaran' => json_decode($hibah->jenis_pengeluaran),
    //         'jumlah_pengeluaran' => json_decode($hibah->jumlah_pengeluaran),
    //         'biaya_satuan' => json_decode($hibah->biaya_satuan),
    //         'satuan' => json_decode($hibah->satuan),
    //         'biaya_total' => json_decode($hibah->biaya_total),
    //         'sumber_pendanaan' => json_decode($hibah->sumber_pendanaan),
    //         'tanggal_pelaksanaan' => TanggalIndonesia(@$hibah->tgl_mulai) . ' - ' . TanggalIndonesia(@$hibah->tgl_selesai),
    //         'tgl_mulai' => TanggalIndonesia(@$hibah->tgl_mulai),
    //         'tgl_selesai' => TanggalIndonesia(@$hibah->tgl_selesai),
    //     ];

    //     $urlFileLain = $hibah->file_lain ? asset('storage/' . $hibah->file_lain) : '';
    //     $urlFileKontrak = $hibah->file_kontrak ? asset('storage/' . $hibah->file_kontrak) : '';
    //     $urlFileBuktiTransferTahapSatu = $hibah->file_bukti_transfer_tahap_satu ? asset('storage/' . $hibah->file_bukti_transfer_tahap_satu) : '';
    //     $urlFileBuktiTransferTahapDua = $hibah->file_bukti_transfer_tahap_satu ? asset('storage/' . $hibah->file_bukti_transfer_tahap_satu) : '';

    //     $pdf = Pdf::loadView('hibah.print_proposal', $data);
    //     return $pdf->stream('ajuan_hibah_' . $hibah->id . '.pdf');
    // }

    public function export_laporan($id)
    {
        $hibah = AjuanHibah::withFullJoin()->where('tbl_ajuan_hibah.id_hibah', $id)->firstOrFail();
        $laporanHibah = LaporanHibah::where('id_hibah', $id)->firstOrFail();

        $data = [
            'hibah' => $hibah,
            'laporHibah' => $laporanHibah,
            'anggota' => json_decode($hibah->anggota),
            'jenis_pengeluaran' => json_decode($laporanHibah->jenis_pengeluaran),
            'jumlah_pengeluaran' => json_decode($laporanHibah->jumlah_pengeluaran),
            'satuan' => json_decode($laporanHibah->satuan),
            'biaya_satuan' => json_decode($laporanHibah->biaya_satuan),
            'biaya_total' => json_decode($laporanHibah->biaya_total),
            'tanggal_pelaksanaan' => TanggalIndonesia(@$hibah->tgl_mulai) . ' - ' . TanggalIndonesia(@$hibah->tgl_selesai),
            'tgl_mulai' => TanggalIndonesia(@$hibah->tgl_mulai),
            'tgl_selesai' => TanggalIndonesia(@$hibah->tgl_selesai),
        ];

        $mainPdfPath = storage_path('app/temp/laporan_' . $hibah->id . '.pdf');
        if (!file_exists(dirname($mainPdfPath))) {
            mkdir(dirname($mainPdfPath), 0777, true);
        }

        Pdf::loadView('hibah.print_laporan', $data)->save($mainPdfPath);
        $pdf = new Fpdi();
        $pdf->SetTitle('LAPORAN HIBAH - ' . strtoupper($hibah->judul_proposal), true);

        $addPdfToMerger = function ($path) use ($pdf) {
            try {
                $pageCount = $pdf->setSourceFile($path);
                for ($page = 1; $page <= $pageCount; $page++) {
                    $tpl = $pdf->importPage($page);
                    $size = $pdf->getTemplateSize($tpl);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($tpl);
                }
            } catch (\Exception $e) {
                $pdf->AddPage();
                $pdf->SetFont('Helvetica', '', 12);
                $pdf->MultiCell(0, 10, "Gagal menambahkan file: {$path}\nError: " . $e->getMessage(), 0, 'L');
            }
        };

        // Tambah file utama ke PDF
        $addPdfToMerger($mainPdfPath);

        $filePaths = [
            'DOKUMEN PENDUKUNG' => $laporanHibah->file_pendukung,
            'DOKUMENTASI KEGIATAN' => $laporanHibah->file_dokumentasi,
            'PELAPORAN KEGIATAN KERJA SAMA' => $laporanHibah->file_laporan_kegiatan,
            'BUKTI LAPORAN KEUANGAN' => $laporanHibah->file_transaksi,
            'FILE TAMBAHAN' => $laporanHibah->file_tambahan,
        ];

        foreach ($filePaths as $title => $relativePath) {
            if ($relativePath) {
                $relativePath = preg_replace('#/+#', '/', $relativePath);
                $fullPath = storage_path('app/public/' . ltrim($relativePath, '/'));

                if (file_exists($fullPath)) {
                    try {
                        $pageCount = $pdf->setSourceFile($fullPath);

                        $pdf->AddPage();
                        $pageWidth = $pdf->GetPageWidth();
                        $pageHeight = $pdf->GetPageHeight();
                        $pdf->SetFont('Helvetica', 'B', 20);
                        $textWidth = $pdf->GetStringWidth($title);
                        $x = ($pageWidth - $textWidth) / 2;
                        $y = $pageHeight / 2;
                        $pdf->SetXY($x, $y);
                        $pdf->Cell($textWidth, 10, $title, 0, 1, 'C');

                        $addPdfToMerger($fullPath);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }

        if (file_exists($mainPdfPath)) {
            unlink($mainPdfPath);
        }

        return response($pdf->Output('S', 'LAPORAN_HIBAH_' . strtoupper($hibah->judul_proposal) . '.pdf'), 200)
            ->header('Content-Type', 'application/pdf');
    }


    public function upload_file($file, $path)
    {
        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->makeDirectory($path);
        }
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs($path, $fileName, 'public');
        return $filePath;
    }

    public function setting()
    {
        $data = [
            'li_active' => 'setting',
            'title' => 'Setting',
            'page_title' => 'Setting',
            'dataSetting' => SettingHibah::getData(),
        ];
        return view('hibah/setting', $data);
    }

    public function storeSetting(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                // 'pendanaan_bkui' => 'required|numeric|min:0',

                // Latar Belakang Proposal
                'min_latar_belakang_proposal' => 'required|numeric|min:1',
                'latar_belakang_proposal' => 'required|numeric|min:1|gt:min_latar_belakang_proposal',

                // Tujuan Proposal
                'min_tujuan_proposal' => 'required|numeric|min:1',
                'tujuan_proposal' => 'required|numeric|min:1|gt:min_tujuan_proposal',

                // Target Proposal
                'min_target_proposal' => 'required|numeric|min:1',
                'target_proposal' => 'required|numeric|min:1|gt:min_target_proposal',

                // Detail Institusi Mitra
                'min_detail_institusi_mitra' => 'required|numeric|min:1',
                'detail_institusi_mitra' => 'required|numeric|min:1|gt:min_detail_institusi_mitra',

                // Detail Kerja Sama
                'min_detail_kerma' => 'required|numeric|min:1',
                'detail_kerma' => 'required|numeric|min:1|gt:min_detail_kerma',

                // Indikator Keberhasilan
                'min_indikator_keberhasilan' => 'required|numeric|min:1',
                'indikator_keberhasilan' => 'required|numeric|min:1|gt:min_indikator_keberhasilan',

                // Rencana Proposal
                'min_rencana_proposal' => 'required|numeric|min:1',
                'rencana_proposal' => 'required|numeric|min:1|gt:min_rencana_proposal',
            ],
            [
                // Pendanaan BKUI
                // 'pendanaan_bkui.required' => 'Maksimal Dana Pendanaan BKUI harus diisi.',
                // 'pendanaan_bkui.numeric' => 'Maksimal Dana Pendanaan BKUI harus berupa angka.',
                // 'pendanaan_bkui.min' => 'Maksimal Dana Pendanaan BKUI tidak boleh kurang dari 0.',

                // Latar Belakang Proposal
                'min_latar_belakang_proposal.required' => 'Minimum Latar Belakang Proposal harus diisi.',
                'min_latar_belakang_proposal.numeric' => 'Minimum Latar Belakang Proposal harus berupa angka.',
                'min_latar_belakang_proposal.min' => 'Minimum Latar Belakang Proposal minimal 1 kata.',
                'latar_belakang_proposal.required' => 'Maksimum Latar Belakang Proposal harus diisi.',
                'latar_belakang_proposal.numeric' => 'Maksimum Latar Belakang Proposal harus berupa angka.',
                'latar_belakang_proposal.min' => 'Maksimum Latar Belakang Proposal minimal 1 kata.',
                'latar_belakang_proposal.gt' => 'Maksimum Latar Belakang Proposal harus lebih besar dari Minimum.',

                // Tujuan Proposal
                'min_tujuan_proposal.required' => 'Minimum Tujuan Proposal harus diisi.',
                'min_tujuan_proposal.numeric' => 'Minimum Tujuan Proposal harus berupa angka.',
                'min_tujuan_proposal.min' => 'Minimum Tujuan Proposal minimal 1 kata.',
                'tujuan_proposal.required' => 'Maksimum Tujuan Proposal harus diisi.',
                'tujuan_proposal.numeric' => 'Maksimum Tujuan Proposal harus berupa angka.',
                'tujuan_proposal.min' => 'Maksimum Tujuan Proposal minimal 1 kata.',
                'tujuan_proposal.gt' => 'Maksimum Tujuan Proposal harus lebih besar dari Minimum.',

                // Target Proposal
                'min_target_proposal.required' => 'Minimum Target Proposal harus diisi.',
                'min_target_proposal.numeric' => 'Minimum Target Proposal harus berupa angka.',
                'min_target_proposal.min' => 'Minimum Target Proposal minimal 1 kata.',
                'target_proposal.required' => 'Maksimum Target Proposal harus diisi.',
                'target_proposal.numeric' => 'Maksimum Target Proposal harus berupa angka.',
                'target_proposal.min' => 'Maksimum Target Proposal minimal 1 kata.',
                'target_proposal.gt' => 'Maksimum Target Proposal harus lebih besar dari Minimum.',

                // Detail Institusi Mitra
                'min_detail_institusi_mitra.required' => 'Minimum Detail Institusi Mitra harus diisi.',
                'min_detail_institusi_mitra.numeric' => 'Minimum Detail Institusi Mitra harus berupa angka.',
                'min_detail_institusi_mitra.min' => 'Minimum Detail Institusi Mitra minimal 1 kata.',
                'detail_institusi_mitra.required' => 'Maksimum Detail Institusi Mitra harus diisi.',
                'detail_institusi_mitra.numeric' => 'Maksimum Detail Institusi Mitra harus berupa angka.',
                'detail_institusi_mitra.min' => 'Maksimum Detail Institusi Mitra minimal 1 kata.',
                'detail_institusi_mitra.gt' => 'Maksimum Detail Institusi Mitra harus lebih besar dari Minimum.',

                // Detail Kerja Sama
                'min_detail_kerma.required' => 'Minimum Detail Kerja Sama harus diisi.',
                'min_detail_kerma.numeric' => 'Minimum Detail Kerja Sama harus berupa angka.',
                'min_detail_kerma.min' => 'Minimum Detail Kerja Sama minimal 1 kata.',
                'detail_kerma.required' => 'Maksimum Detail Kerja Sama harus diisi.',
                'detail_kerma.numeric' => 'Maksimum Detail Kerja Sama harus berupa angka.',
                'detail_kerma.min' => 'Maksimum Detail Kerja Sama minimal 1 kata.',
                'detail_kerma.gt' => 'Maksimum Detail Kerja Sama harus lebih besar dari Minimum.',

                // Indikator Keberhasilan
                'min_indikator_keberhasilan.required' => 'Minimum Indikator Keberhasilan harus diisi.',
                'min_indikator_keberhasilan.numeric' => 'Minimum Indikator Keberhasilan harus berupa angka.',
                'min_indikator_keberhasilan.min' => 'Minimum Indikator Keberhasilan minimal 1 kata.',
                'indikator_keberhasilan.required' => 'Maksimum Indikator Keberhasilan harus diisi.',
                'indikator_keberhasilan.numeric' => 'Maksimum Indikator Keberhasilan harus berupa angka.',
                'indikator_keberhasilan.min' => 'Maksimum Indikator Keberhasilan minimal 1 kata.',
                'indikator_keberhasilan.gt' => 'Maksimum Indikator Keberhasilan harus lebih besar dari Minimum.',

                // Rencana Proposal
                'min_rencana_proposal.required' => 'Minimum Rencana Proposal harus diisi.',
                'min_rencana_proposal.numeric' => 'Minimum Rencana Proposal harus berupa angka.',
                'min_rencana_proposal.min' => 'Minimum Rencana Proposal minimal 1 kata.',
                'rencana_proposal.required' => 'Maksimum Rencana Proposal harus diisi.',
                'rencana_proposal.numeric' => 'Maksimum Rencana Proposal harus berupa angka.',
                'rencana_proposal.min' => 'Maksimum Rencana Proposal minimal 1 kata.',
                'rencana_proposal.gt' => 'Maksimum Rencana Proposal harus lebih besar dari Minimum.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Generate ID jika tidak ada
            $id_setting_hibah = $request->id_setting_hibah;
            $dataSetting = SettingHibah::where('id_setting_hibah', $id_setting_hibah)->firstorfail();

            $dataInsert = [
                // 'pendanaan_bkui' => str_replace('.', '', $request->pendanaan_bkui),
                'latar_belakang_proposal' => $request->latar_belakang_proposal,
                'tujuan_proposal' => $request->tujuan_proposal,
                'target_proposal' => $request->target_proposal,
                'detail_institusi_mitra' => $request->detail_institusi_mitra,
                'detail_kerma' => $request->detail_kerma,
                'indikator_keberhasilan' => $request->indikator_keberhasilan,
                'rencana_proposal' => $request->rencana_proposal,

                'min_latar_belakang_proposal' => $request->min_latar_belakang_proposal,
                'min_tujuan_proposal' => $request->min_tujuan_proposal,
                'min_target_proposal' => $request->min_target_proposal,
                'min_detail_institusi_mitra' => $request->min_detail_institusi_mitra,
                'min_detail_kerma' => $request->min_detail_kerma,
                'min_indikator_keberhasilan' => $request->min_indikator_keberhasilan,
                'min_rencana_proposal' => $request->min_rencana_proposal,
            ];

            // Simpan atau update data
            if ($id_setting_hibah) {
                $dataSetting->update($dataInsert);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data berhasil disimpan'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function download_excel(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $headers = [
            'No',
            'Judul Proposal',
            'Jenis Hibah',
            'Institusi Mitra',
            'Ketua Pelaksana',
            'NIDN Ketua',
            'Program Studi',
            'Fakultas',
            'Email',
            'No HP',
            'Penanggung Jawab',
            'Nama Penanggung Jawab',
            'NIDN Penanggung Jawab',
            'Anggota',
            'Peran',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Biaya',
            'Pendanaan BKUI',
            'Pendanaan Lain',
            'Latar Belakang',
            'Tujuan',
            'Detail Institusi Mitra',
            'Jenis Kerma',
            'Detail Kerma',
            'Target',
            'Indikator Keberhasilan',
            'Rencana',
            'Jenis Pengeluaran',
            'Jumlah Pengeluaran',
            'Satuan',
            'Biaya Satuan',
            'Biaya Total',
            'Sumber Pendanaan',
            'Tahun',
            'Link File Lain'
        ];

        // Set header ke baris 1
        foreach ($headers as $i => $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($columnLetter . '1', $header);
        }

        // Data
        $query = AjuanHibah::select(
            'tbl_ajuan_hibah.*',
            // JOIN hasil
            'tbl_laporan_hibah.id_laporan_hibah',
            'kerma_db.nama_institusi',
            'kerma_db.dn_ln',
            'kerma_db.jenis_institusi',

            'lmbg_place_state.nama_lmbg as nama_place_state',
            'lmbg_fakultas.nama_lmbg as nama_fakultas',
            'lmbg_prodi.nama_lmbg as nama_prodi',

            'pengusul.name as nama_pengusul',
            'admin.name as nama_admin',
            'kaprodi.name as nama_verifikator_kaprodi',
            'dekan.name as nama_verifikator_dekan',
            'kaprodi_ref.name as nama_kaprodi_ref',
            'dekan_ref.name as nama_dekan_ref',

            'jenis_hibah.jenis_hibah as nama_jenis_hibah',
            'jenis_hibah.maksimum',
            'jenis_hibah.dl_proposal',
            'jenis_hibah.dl_laporan',
        )
            ->leftJoin('kerma_db', 'kerma_db.id_mou', '=', 'tbl_ajuan_hibah.id_mou')
            ->leftJoin('tbl_laporan_hibah', function ($joinLaporan) {
                $joinLaporan->on('tbl_laporan_hibah.id_hibah', '=', 'tbl_ajuan_hibah.id_hibah')
                    ->whereNull('tbl_laporan_hibah.deleted_at');
            })

            ->leftJoin('ref_lembaga_ums as lmbg_place_state', 'lmbg_place_state.id_lmbg', '=', 'tbl_ajuan_hibah.place_state')
            ->leftJoin('ref_lembaga_ums as lmbg_fakultas', 'lmbg_fakultas.id_lmbg', '=', 'tbl_ajuan_hibah.fakultas')
            ->leftJoin('ref_lembaga_ums as lmbg_prodi', 'lmbg_prodi.id_lmbg', '=', 'tbl_ajuan_hibah.prodi')
            ->leftJoin('users as pengusul', 'pengusul.username', '=', 'tbl_ajuan_hibah.add_by')
            ->leftJoin('users as admin', 'admin.username', '=', 'tbl_ajuan_hibah.verify_admin_by')
            ->leftJoin('users as kaprodi', 'kaprodi.username', '=', 'tbl_ajuan_hibah.verify_kaprodi_by')
            ->leftJoin('users as dekan', 'dekan.username', '=', 'tbl_ajuan_hibah.verify_dekan_by')
            ->leftJoin('users as kaprodi_ref', function ($join) {
                $join->on('kaprodi_ref.jabatan', DB::raw("'Kaprodi'"))
                    ->on('kaprodi_ref.status_tempat', '=', 'tbl_ajuan_hibah.prodi');
            })->leftJoin('users as dekan_ref', function ($join) {
                $join->on('dekan_ref.jabatan', DB::raw("'Dekan'"))
                    ->on('dekan_ref.status_tempat', '=', 'tbl_ajuan_hibah.fakultas');
            })
            ->leftJoin('ref_jenis_hibah as jenis_hibah', 'jenis_hibah.id', '=', 'tbl_ajuan_hibah.jenis_hibah');

        $query->FilterDokumen($request);

        if (session('current_role') == 'user') {
            $query->where("tbl_ajuan_hibah.add_by", Auth::user()->username);
        } elseif (session('current_role') == 'verifikator' && Auth::user()->jabatan == 'Kaprodi') {
            $query->where("tbl_ajuan_hibah.add_by", Auth::user()->username);
            $query->orwhere(function ($q) {
                $q->where("tbl_ajuan_hibah.status_tempat", Auth::user()->status_tempat);
                $q->where("tbl_ajuan_hibah.is_submit", '1');
            });
        } elseif (session('current_role') == 'verifikator' && Auth::user()->jabatan == 'Dekan') {
            $query->where("tbl_ajuan_hibah.add_by", Auth::user()->username);
            $query->orwhere(function ($q) {
                $q->where("tbl_ajuan_hibah.place_state", Auth::user()->place_state);
                $q->where("tbl_ajuan_hibah.is_submit", '1');
            });
        } elseif (session('current_role') == 'admin') {
            $query->where("tbl_ajuan_hibah.is_submit", '1');
        }

        $query->orderByRole();
        $hibahs = $query->get();
        $row = 2;

        foreach ($hibahs as $index => $hibah) {
            $data = [
                $index + 1,
                $hibah->judul_proposal,
                $hibah->nama_jenis_hibah,
                $hibah->institusi_mitra,
                $hibah->ketua_pelaksana,
                $hibah->nidn_ketua_pelaksana,
                $hibah->nama_prodi,
                $hibah->nama_fakultas,
                $hibah->email,
                $hibah->no_hp,

                $hibah->penanggung_jawab_kegiatan,
                $hibah->nama_penanggung_jawab,
                $hibah->nidn_penanggung_jawab,
                implode(", ", json_decode($hibah->anggota, true) ?? []),
                implode(", ", json_decode($hibah->peran, true) ?? []),
                $hibah->tgl_mulai,
                $hibah->tgl_selesai,
                $hibah->biaya,
                $hibah->pendanaan_bkui,
                $hibah->pendanaan_lain,

                strip_tags($hibah->latar_belakang),
                strip_tags($hibah->tujuan),
                strip_tags($hibah->detail_institusi_mitra),
                $hibah->jenis_kerma,
                strip_tags($hibah->detail_kerma),
                strip_tags($hibah->target),
                strip_tags($hibah->indikator_keberhasilan),
                strip_tags($hibah->rencana),
                implode(", ", json_decode($hibah->jenis_pengeluaran, true) ?? []),
                implode(", ", json_decode($hibah->jumlah_pengeluaran, true) ?? []),

                implode(", ", json_decode($hibah->satuan, true) ?? []),
                implode(", ", json_decode($hibah->biaya_satuan, true) ?? []),
                implode(", ", json_decode($hibah->biaya_total, true) ?? []),
                implode(", ", json_decode($hibah->sumber_pendanaan, true) ?? []),
                $hibah->created_at ? $hibah->created_at->format('Y') : '-',
                $hibah->file_lain ? asset('storage/' . $hibah->file_lain) : '-',
            ];

            foreach ($data as $i => $value) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                // $sheet->setCellValue($columnLetter . $row, $value);

                if (in_array($i + 1, [36])) {
                    $sheet->setCellValue($columnLetter . $row, 'Buka File');
                    $sheet->getCell($columnLetter . $row)->getHyperlink()->setUrl($value);
                } else {
                    $sheet->setCellValueExplicit($columnLetter . $row, $value, DataType::TYPE_STRING);
                    // $sheet->setCellValue($columnLetter . $row, $value);
                }
            }

            $row++;
        }

        // Simpan file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'data_ajuan_hibah.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    public function download_laporan_excel(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $headers = [
            'No',
            'Judul Proposal',
            'Jenis Hibah',
            'Institusi Mitra',
            'Ketua Pelaksana',
            'NIDN Ketua',
            'Program Studi',
            'Fakultas',
            'Email',
            'No HP',
            'Penanggung Jawab',
            'Nama Penanggung Jawab',
            'NIDN Penanggung Jawab',
            'Anggota',
            'Peran',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Biaya',
            'Pendanaan BKUI',
            'Pendanaan Lain',
            'Jenis Kerma',
            'Detail Aktivitas Hibah',
            'Target Output dan Outcome',
            'Hasil dan Dampak Kerja Sama',
            'Rencana Tindak Lanjut',
            'Jenis Pengeluaran Laporan',
            'Jumlah Pengeluaran Laporan',
            'Satuan',
            'Biaya Satuan',
            'Biaya Total',
            'Tahun',
            'File Dokumen Pendukung',
            'File Dokumen Kegiatan',
            'File Pelaporan Kerja Sama',
            'File Bukti Laporan Keuangan',
            'File Tambahan',
        ];

        // Set header ke baris 1
        foreach ($headers as $i => $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($columnLetter . '1', $header);
        }

        // Data
        $query = AjuanHibah::select(
            'tbl_ajuan_hibah.*',
            // JOIN hasil
            'tbl_laporan_hibah.id_laporan_hibah',
            'tbl_laporan_hibah.detail_aktivitas',
            'tbl_laporan_hibah.target_laporan',
            'tbl_laporan_hibah.hasil_laporan',
            'tbl_laporan_hibah.rencana_tindak_lanjut',
            'tbl_laporan_hibah.jenis_pengeluaran as jenis_pengeluaran_laporan',
            'tbl_laporan_hibah.jumlah_pengeluaran as jumlah_pengeluaran_laporan',
            'tbl_laporan_hibah.satuan as satuan_laporan',
            'tbl_laporan_hibah.biaya_satuan as biaya_satuan_laporan',
            'tbl_laporan_hibah.biaya_total as biaya_total_laporan',
            'tbl_laporan_hibah.total as total_laporan',
            'tbl_laporan_hibah.file_pendukung',
            'tbl_laporan_hibah.file_dokumentasi',
            'tbl_laporan_hibah.file_laporan_kegiatan',
            'tbl_laporan_hibah.file_transaksi',
            'tbl_laporan_hibah.file_tambahan',

            'kerma_db.nama_institusi',
            'kerma_db.dn_ln',
            'kerma_db.jenis_institusi',

            'lmbg_place_state.nama_lmbg as nama_place_state',
            'lmbg_fakultas.nama_lmbg as nama_fakultas',
            'lmbg_prodi.nama_lmbg as nama_prodi',

            'pengusul.name as nama_pengusul',
            'admin.name as nama_admin',
            'kaprodi.name as nama_verifikator_kaprodi',
            'dekan.name as nama_verifikator_dekan',
            'kaprodi_ref.name as nama_kaprodi_ref',
            'dekan_ref.name as nama_dekan_ref',

            'jenis_hibah.jenis_hibah as nama_jenis_hibah',
            'jenis_hibah.maksimum',
            'jenis_hibah.dl_proposal',
            'jenis_hibah.dl_laporan',
        )
            ->leftJoin('kerma_db', 'kerma_db.id_mou', '=', 'tbl_ajuan_hibah.id_mou')
            ->leftJoin('tbl_laporan_hibah', function ($joinLaporan) {
                $joinLaporan->on('tbl_laporan_hibah.id_hibah', '=', 'tbl_ajuan_hibah.id_hibah')
                    ->whereNull('tbl_laporan_hibah.deleted_at');
            })

            ->leftJoin('ref_lembaga_ums as lmbg_place_state', 'lmbg_place_state.id_lmbg', '=', 'tbl_ajuan_hibah.place_state')
            ->leftJoin('ref_lembaga_ums as lmbg_fakultas', 'lmbg_fakultas.id_lmbg', '=', 'tbl_ajuan_hibah.fakultas')
            ->leftJoin('ref_lembaga_ums as lmbg_prodi', 'lmbg_prodi.id_lmbg', '=', 'tbl_ajuan_hibah.prodi')
            ->leftJoin('users as pengusul', 'pengusul.username', '=', 'tbl_ajuan_hibah.add_by')
            ->leftJoin('users as admin', 'admin.username', '=', 'tbl_ajuan_hibah.verify_admin_by')
            ->leftJoin('users as kaprodi', 'kaprodi.username', '=', 'tbl_ajuan_hibah.verify_kaprodi_by')
            ->leftJoin('users as dekan', 'dekan.username', '=', 'tbl_ajuan_hibah.verify_dekan_by')
            ->leftJoin('users as kaprodi_ref', function ($join) {
                $join->on('kaprodi_ref.jabatan', DB::raw("'Kaprodi'"))
                    ->on('kaprodi_ref.status_tempat', '=', 'tbl_ajuan_hibah.prodi');
            })->leftJoin('users as dekan_ref', function ($join) {
                $join->on('dekan_ref.jabatan', DB::raw("'Dekan'"))
                    ->on('dekan_ref.status_tempat', '=', 'tbl_ajuan_hibah.fakultas');
            })
            ->leftJoin('ref_jenis_hibah as jenis_hibah', 'jenis_hibah.id', '=', 'tbl_ajuan_hibah.jenis_hibah');

        $query->FilterDokumen($request);
        $query->whereNotNull('tbl_laporan_hibah.id_laporan_hibah');

        if (session('current_role') == 'user') {
            $query->where("tbl_ajuan_hibah.add_by", Auth::user()->username);
        } elseif (session('current_role') == 'verifikator' && Auth::user()->jabatan == 'Kaprodi') {
            $query->where("tbl_ajuan_hibah.add_by", Auth::user()->username);
            $query->orwhere(function ($q) {
                $q->where("tbl_ajuan_hibah.status_tempat", Auth::user()->status_tempat);
                $q->where("tbl_ajuan_hibah.is_submit", '1');
            });
        } elseif (session('current_role') == 'verifikator' && Auth::user()->jabatan == 'Dekan') {
            $query->where("tbl_ajuan_hibah.add_by", Auth::user()->username);
            $query->orwhere(function ($q) {
                $q->where("tbl_ajuan_hibah.place_state", Auth::user()->place_state);
                $q->where("tbl_ajuan_hibah.is_submit", '1');
            });
        } elseif (session('current_role') == 'admin') {
            $query->where("tbl_ajuan_hibah.is_submit", '1');
        }

        $query->orderByRole();
        $hibahs = $query->get();
        // return $hibahs;
        $row = 2;

        foreach ($hibahs as $index => $hibah) {
            $data = [
                $index + 1,
                $hibah->judul_proposal,
                $hibah->nama_jenis_hibah,
                $hibah->institusi_mitra,
                $hibah->ketua_pelaksana,
                $hibah->nidn_ketua_pelaksana,
                $hibah->nama_prodi,
                $hibah->nama_fakultas,
                $hibah->email,
                $hibah->no_hp,
                $hibah->penanggung_jawab_kegiatan,
                $hibah->nama_penanggung_jawab,
                $hibah->nidn_penanggung_jawab,
                implode(", ", json_decode($hibah->anggota, true) ?? []),
                implode(", ", json_decode($hibah->peran, true) ?? []),
                $hibah->tgl_mulai,
                $hibah->tgl_selesai,
                $hibah->biaya,
                $hibah->pendanaan_bkui,
                $hibah->pendanaan_lain,
                $hibah->jenis_kerma,
                strip_tags($hibah->detail_aktivitas),
                strip_tags($hibah->target_laporan),
                strip_tags($hibah->hasil_laporan),
                strip_tags($hibah->rencana_tindak_lanjut),
                implode(", ", json_decode($hibah->jenis_pengeluaran_laporan, true) ?? []),
                implode(", ", json_decode($hibah->jumlah_pengeluaran_laporan, true) ?? []),
                implode(", ", json_decode($hibah->satuan_laporan, true) ?? []),
                implode(", ", json_decode($hibah->biaya_satuan_laporan, true) ?? []),
                implode(", ", json_decode($hibah->biaya_total_laporan, true) ?? []),
                $hibah->created_at ? $hibah->created_at->format('Y') : '-',
                $hibah->file_pendukung ? asset('storage/' . $hibah->file_pendukung) : '-',
                $hibah->file_dokumentasi ? asset('storage/' . $hibah->file_dokumentasi) : '-',
                $hibah->file_laporan_kegiatan ? asset('storage/' . $hibah->file_laporan_kegiatan) : '-',
                $hibah->file_transaksi ? asset('storage/' . $hibah->file_transaksi) : '-',
                $hibah->file_tambahan ? asset('storage/' . $hibah->file_tambahan) : '-',
            ];

            foreach ($data as $i => $value) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);

                // if (filter_var($value, FILTER_VALIDATE_URL)) {
                if (in_array($i + 1, [32, 33, 34, 35, 36])) {
                    $sheet->setCellValue($columnLetter . $row, 'Buka File');
                    $sheet->getCell($columnLetter . $row)->getHyperlink()->setUrl($value);
                } else {
                    $sheet->setCellValueExplicit($columnLetter . $row, $value, DataType::TYPE_STRING);
                    // $sheet->setCellValue($columnLetter . $row, $value);
                }
            }

            $row++;
        }

        // Simpan file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'data_laporan_hibah.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    private function getReferensiFilterHibah()
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
            'Expired',
            'Selesai',
        ])->reduce(fn($html, $val) => $html . '<option value="' . e($val) . '">' . e($val) . '</option>', '<option value="">Pilih Status</option>');

        $filter = [
            'judul_proposal' => $makeOptions($judul_proposal, 'Pilih Judul Proposal'),
            'institusi' => $makeOptions($institusi, 'Pilih Institusi'),
            'jenis_hibah' => $makeOptions($jenis_hibah, 'Pilih Jenis Hibah', true),
            'fakultas' => $makeOptions($fakultas, 'Pilih Fakultas', true),
            'program_studi' => $makeOptions($program_studi, 'Pilih Program Studi', true),
            'status' => $option_status,
        ];
        // return response()->json();
        return $filter;
    }


    public function notifikasiHibah()
    {
        $today = Carbon::today();
        $currentRole = session('current_role');
        $user = Auth::user();

        $queryAjuan =  AjuanHibah::select([
            'tbl_ajuan_hibah.*',
            'tbl_laporan_hibah.id_laporan_hibah',
            'jenis_hibah.jenis_hibah as nama_jenis_hibah',
            'jenis_hibah.maksimum',
            'jenis_hibah.dl_proposal',
            'jenis_hibah.dl_laporan',
        ])
            ->whereNull('tbl_ajuan_hibah.date_selesai')
            ->leftJoin('tbl_laporan_hibah', 'tbl_laporan_hibah.id_hibah', '=', 'tbl_ajuan_hibah.id_hibah')
            ->leftJoin('ref_jenis_hibah as jenis_hibah', 'jenis_hibah.id', '=', 'tbl_ajuan_hibah.jenis_hibah');

        if ($currentRole == 'user') {
            $queryAjuan->where('tbl_ajuan_hibah.add_by', $user->username);
        } else if ($currentRole == 'verifikator' && $user->jabatan == 'Kaprodi') {
            $queryAjuan->where('tbl_ajuan_hibah.status_tempat', $user->status_tempat);
        } else if ($currentRole == 'verifikator' && $user->jabatan == 'Dekan') {
            $queryAjuan->where('tbl_ajuan_hibah.place_state', $user->place_state);
        }

        $ajuanHibahList = $queryAjuan->get();
        $jumlahNotif = 0;

        foreach ($ajuanHibahList as $row) {
            $isKaprodi = $row->jabatan_pengusul === 'Kaprodi';
            $isPenanggungJawab = $row->penanggung_jawab_kegiatan;

            // DEADLINE
            $expProposal = ($today > $row->dl_proposal) && !$row->file_kontrak;
            $expLaporan = ($today > $row->dl_laporan) && $row->file_kontrak && !$row->id_laporan_hibah && !$row->date_verify_laporan;

            $status = [
                'submit' => $row->is_submit == '1',
                'selesai' => $row->status_selesai == '1',
                'revisi' => [
                    'kaprodi' => $row->status_revisi_kaprodi,
                    'dekan' => $row->status_revisi_dekan,
                    'admin' => $row->status_revisi_admin,
                    'laporan' => $row->status_revisi_laporan,
                ],
                'verifikasi' => [
                    'kaprodi' => $row->status_verify_kaprodi == '1',
                    'dekan' => $row->status_verify_dekan == '1',
                    'admin' => $row->status_verify_admin == '1',
                    'laporan' => $row->status_verify_laporan == '1',
                    'tahap1' => $row->status_verify_tahap_dua == '1',
                    'tahap2' => $row->status_verify_tahap_dua == '1',
                ],
            ];

            $has = [
                'file_kontrak' => !empty($row->file_kontrak),
                'laporan' => !empty($row->id_laporan_hibah),
            ];
            // LOGIKA NOTIFIKASI
            $notifCases = [
                'user' => (
                    $status['submit']
                    && (
                        $status['revisi']['kaprodi'] === '0' ||
                        $status['revisi']['admin'] === '0' ||
                        $expLaporan
                    )
                    || ($has['laporan'] && $status['revisi']['laporan'] == '0'

                        && !$status['verifikasi']['laporan']

                    )
                ),


                'verifikator_kaprodi' => (
                    $status['submit'] &&
                    (
                        (!$isKaprodi && $isPenanggungJawab === 'kaprodi' && !$status['verifikasi']['kaprodi'])
                        || $status['revisi']['kaprodi'] === '0'
                        || ($row->add_by === $user->username && $expLaporan)
                    )
                ),


                'verifikator_dekan' => (
                    $status['submit'] && ((
                        ($isPenanggungJawab === 'dekan' && !$status['verifikasi']['dekan']) ||
                        $status['revisi']['dekan'] === '0'
                    )
                        || ($row->add_by === $user->username && $expLaporan)
                    )
                ),


                'admin' => (
                    ($status['revisi']['admin'] === '1' || $status['revisi']['admin'] === null) &&
                    ($status['verifikasi']['kaprodi'] || $status['verifikasi']['dekan']) &&
                    (!$status['verifikasi']['admin'] && !$row->file_kontrak)
                ) || (
                    //     $status['verifikasi']['admin'] &&
                    //     $row->file_kontrak &&
                    //     !$status['verifikasi']['laporan'] &&
                    //     $row->id_laporan_hibah
                    // ) || (
                    $status['verifikasi']['laporan'] &&
                    !$status['verifikasi']['tahap2']
                ) || (
                    $status['verifikasi']['tahap2'] && !$status['selesai']
                ) || (
                    $status['verifikasi']['admin'] && !$has['file_kontrak']
                    // ) || (
                    //     $has['file_kontrak'] &&
                    //     !$status['verifikasi']['tahap1'] &&
                    //     !$status['verifikasi']['laporan']
                    // ) || (
                    //     $status['revisi']['admin'] == '0' &&
                    //     ($status['verifikasi']['kaprodi'] || $status['verifikasi']['dekan']) &&
                    //     !$status['verifikasi']['admin'] &&
                    //     !$has['file_kontrak']
                )
            ];

            if (
                ($currentRole === 'user' && $notifCases['user']) ||
                ($currentRole === 'verifikator' && $user->jabatan === 'Kaprodi' && $notifCases['verifikator_kaprodi']) ||
                ($currentRole === 'verifikator' && $user->jabatan === 'Dekan' && $notifCases['verifikator_dekan']) ||
                ($currentRole === 'admin' && $notifCases['admin'])
            ) {
                $jumlahNotif++;
                // $arrId[] = $row->id_hibah;
            }
        }

        return $jumlahNotif;
    }
}
