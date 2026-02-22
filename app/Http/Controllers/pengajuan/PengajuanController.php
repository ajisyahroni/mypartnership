<?php

namespace App\Http\Controllers\pengajuan;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailDraft;
use App\Jobs\SendEmailMailer;
use App\Jobs\SendEmailPengajuan;
use App\Jobs\SendEmailPublish;
use App\Jobs\SendEmailVerifikasiPengajuan;
use App\Mail\PengirimanEmail;
use App\Models\ConfigWebsite;
use App\Models\JawabanFeedback;
use App\Models\JawabanSkalaPenilaian;
use App\Models\laporImplementasi;
use App\Models\LogActivity;
use App\Models\MailMessages;
use App\Models\MailSetting;
use App\Models\MasukanSurvei;
use App\Models\PengajuanKerjaSama;
use App\Models\RefBentukKerjaSama;
use App\Models\RefGroupRangking;
use App\Models\RefJenisDokumen;
use App\Models\RefJenisDokumenMoU;
use App\Models\RefJenisInstitusiMitra;
use App\Models\RefLembagaUMS;
use App\Models\RefNegara;
use App\Models\RefPertanyaanFeedback;
use App\Models\RefPerusahaan;
use App\Models\RefSkalaPenilaian;
use App\Models\RefTingkatKerjaSama;
use App\Models\SettingBobot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

set_time_limit(300);


class PengajuanController extends Controller
{
    public function index()
    {
        $filterPengajuan = $this->getReferensiDokumen();
        // return session()->all();
        $data = [
            'li_active' => 'kerjasama',
            'li_sub_active' => 'pengajuan_kerjasama',
            'title' => 'Pengajuan Dokumen Kerja Sama',
            'page_title' => 'Pengajuan Dokumen Kerja Sama',
            'filterPengajuan' => $filterPengajuan,
        ];

        if (!empty(session('sendEmailData'))) {
            $data['sendEmailData'] = session('sendEmailData');
        }

        if (in_array(session('current_role'), ['user', 'verifikator'])) {
            $data['li_active'] = 'pengajuan_kerjasama';
        }

        return view('pengajuan/index', $data);
    }

    public function getData(Request $request)
    {
        $user = Auth::user();
        $role = session('current_role');
        $tanggalNull = '0000-00-00 00:00:00';
        $orderColumnIndex = $request->input('order.0.column');

        $query = PengajuanKerjaSama::with([
            'getLembaga:nama_lmbg',
            'getPengusul',
            'getVerifikator',
            'getKabid',
            'getPenandatangan',
            'getUnreadChatCount'
        ]);
        $query->where(function ($q) {
            $q->where('created_at', '<', date('2025-12-12'));
            $q->where('stats_kerma', 'Ajuan Baru');
            $q->where('tgl_selesai', '0000-00-00 00:00:00');
        });

        $query->orwhere(function ($q) {
            $q->where('created_at', '>', date('2025-12-12'));
        });

        $query->where('tgl_selesai', $tanggalNull)
            ->FilterDokumen($request)
            ->select('*')
            ->selectRaw("
                    CASE
                        WHEN stats_kerma ='Lapor Kerma' AND status_verify_publish = '0' AND status_verify_publish IS NOT NULL
		                THEN 99
                        WHEN ? = 'user' AND add_by = ? 
                            AND tgl_verifikasi_kaprodi != ? 
                            AND tgl_verifikasi_kabid != ? 
                            AND tgl_verifikasi_user = ? THEN 1
                        WHEN ? = 'verifikator' AND status_verify_kaprodi = ? THEN 2
                        WHEN ? = 'verifikator' AND place_state = ? 
                            AND tgl_verifikasi_kaprodi = ? THEN 1
                        WHEN ? = 'admin' AND (
                            (tgl_verifikasi_kaprodi != ? AND tgl_verifikasi_kabid = ? AND (status_verify_admin = ? OR status_verify_admin IS NULL)) OR
                            (tgl_verifikasi_kabid != ? AND tgl_verifikasi_user != ? AND 
                            ttd_by = 'BKUI'
                            AND  
                            tgl_verifikasi_publish = ?)
                        ) THEN 1
                        ELSE 2
                    END as urutan
                ", [
                $role,
                $user->username,
                $tanggalNull,
                $tanggalNull,
                $tanggalNull,

                $role,
                '0',
                $role,
                $user->place_state,
                $tanggalNull,

                $role,
                $tanggalNull,
                $tanggalNull,
                '0',
                $tanggalNull,
                $tanggalNull,
                // $tanggalNull,
                $tanggalNull
            ]);

        $this->applyRoleFilter($query);

        if ($orderColumnIndex == 0) {
            $query->orderBy('urutan')
                ->orderByDesc('created_at');
        }
        // return $user->place_state;
        // return $query->toSql();

        // dd($query->toSql(), $query->getBindings());
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('lembaga', fn($row) => $row->status_tempat ?? '')
            ->addColumn('status_verifikasi', fn($row) => $row->getStatusPengajuan())
            ->addColumn('status_pengajuan', fn($row) => $row->status_pengajuan)
            ->addColumn('wilayah_mitra', fn($row) => $row->dn_ln === 'Luar Negeri' ? $row->negara_mitra : $row->wilayah_mitra)
            ->addColumn('jenis_institusi_mitra', fn($row) => $row->jenis_institusi === 'Lain-lain' ? $row->nama_institusi : $row->jenis_institusi)
            ->addColumn('implementasi', fn($row) => $row->implementasi_button)
            ->addColumn('action', fn($row) => $row->action_buttons)
            ->addColumn('mulai', fn($row) => $row->periode_kerma == 'bydoc' ? Tanggal_Indo($row->mulai) : ($row->mulai == null || $row->mulai == '0000-00-00' ? Tanggal_Indo($row->awal) : Tanggal_Indo($row->mulai)))
            ->rawColumns(['implementasi', 'action', 'status_pengajuan', 'status_verifikasi'])
            ->make(true);
    }


    private function applyRoleFilter(&$query)
    {
        $user = Auth::user();
        $statusTempat = $user->status_tempat;
        $tanggalNull = '0000-00-00 00:00:00';

        switch (session('current_role')) {
            case 'user':
                $query->where('add_by', $user->username);
                break;
            case 'verifikator':
                $superunit = RefLembagaUMS::where('nama_lmbg', $user->status_tempat)->first()->namasuper ?? null;
                $arrUnit = [$user->status_tempat];
                // if ($superunit != null) {
                //     $arrUnit = [$user->status_tempat, $superunit];
                // }

                // $query->whereIn('status_tempat', [$user->status_tempat, $superunit])
                $query->whereIn('status_tempat', $arrUnit);
                // $query->where('place_state', $user->place_state);
                // $query->where('status_tempat', $user->status_tempat);
                break;
            case 'admin':
            default:
                // No additional filter
                break;
        }
    }

    public function getPriorityData()
    {
        $tanggalNull = '0000-00-00 00:00:00';
        $query = PengajuanKerjaSama::where('tgl_selesai', $tanggalNull);
        $user = Auth::user();

        switch (session('current_role')) {
            case 'user':
                $query->where('add_by', $user->username)
                    ->whereNot('tgl_verifikasi_kaprodi', $tanggalNull)
                    ->whereNot('tgl_verifikasi_kabid', $tanggalNull)
                    ->where('tgl_verifikasi_user', $tanggalNull);
                break;
            case 'verifikator':
                $query->where('place_state', $user->place_state)
                    ->where('tgl_verifikasi_kaprodi', $tanggalNull);
                break;
            case 'admin':
                $query->where(function ($q) use ($tanggalNull) {
                    $q->where(function ($sub) use ($tanggalNull) {
                        $sub->whereNot('tgl_verifikasi_kaprodi', $tanggalNull)
                            ->where('tgl_verifikasi_kabid', $tanggalNull);
                    })
                        ->orWhere(function ($sub) use ($tanggalNull) {
                            $sub->whereNot('tgl_verifikasi_kabid', $tanggalNull)
                                ->whereNot('tgl_verifikasi_user', $tanggalNull)
                                // ->whereNot('tgl_req_ttd', $tanggalNull)
                                ->where('tgl_verifikasi_publish', $tanggalNull);
                            $sub->where('ttd_by', 'BKUI');
                            $sub->where(function ($q) use ($tanggalNull) {
                                $q->where(function ($q1) use ($tanggalNull) {
                                    $q1->where('tgl_req_ttd', '!=', $tanggalNull)
                                        ->whereNull('file_mou');
                                })
                                    ->orWhere(function ($q2) use ($tanggalNull) {
                                        $q2->where('tgl_req_ttd', $tanggalNull)
                                            ->whereNull('file_mou');
                                    });
                            });
                        });
                });
                break;
            default:
                break;
        }

        return $query->pluck('id_mou');
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
        // return response()->json(['html' => $view->render(), 'filePdf' =>  asset('storage/' . $dataPengajuan->file_mou)], 200);
        return response()->json(['html' => $view->render(), 'filePdf' =>  $fileUrl], 200);
    }

    public function getDetailVerifikasi(Request $request)
    {
        $dataPengajuan = PengajuanKerjaSama::select('*')
            ->with(['getLembaga', 'getPengusul', 'getVerifikator', 'getVerifyUser', 'getKabid', 'getPenandatangan'])
            ->where('id_mou', $request->id_mou)
            ->firstOrFail();

        $fileUrl = getDocumentUrl($dataPengajuan->file_mou, 'file_mou');
        $fileUrlDraft = getDocumentUrl($dataPengajuan->file_ajuan, 'file_ajuan');
        $fileUrlMitra = getDocumentUrl($dataPengajuan->file_sk_mitra, 'file_mitra');

        $dokumenPerpanjangan = PengajuanKerjaSama::where('id_mou', $dataPengajuan->id_mou_perpanjang)->first() ?? null;


        $data = [
            'dataPengajuan' => $dataPengajuan,
            'fileUrl' => @$fileUrl,
            'fileUrlDraft' => @$fileUrlDraft,
            'fileUrlMitra' => @$fileUrlMitra,
            'tipe' => $request->tipe,
            'dokumenPerpanjangan' => $dokumenPerpanjangan
        ];

        $data['hasVerify'] = [
            'kaprodi' => $dataPengajuan->status_verify_kaprodi == '1',
            'admin' => $dataPengajuan->status_verify_admin == '1',
            'user' => $dataPengajuan->status_verify_user == '1',
            'publish' => $dataPengajuan->status_verify_publish == '1',
        ];
        $data['hasNotVerify'] = [
            'kaprodi' => $dataPengajuan->status_verify_kaprodi == '0',
            'admin' => $dataPengajuan->status_verify_admin == '0',
            'user' => $dataPengajuan->status_verify_user == '0',
            'publish' => $dataPengajuan->status_verify_publish == '0',
        ];

        $view = view('pengajuan/form_verifikasi', $data);
        // return response()->json(['html' => $view->render(), 'filePdf' =>  asset('storage/' . $dataPengajuan->file_mou)], 200);
        return response()->json(['html' => $view->render(), 'filePdf' =>  $fileUrl, 'fileDraft' => $fileUrlDraft], 200);
    }

    public function tambahBaru($id_mou = null)
    {
        $data = [
            'title' => 'Ajukan Dokumen Baru',
            'page_title' => 'Ajukan Dokumen Baru',
            'li_active' => 'kerjasama',
            'li_sub_active' => 'pengajuan_kerjasama',
            'jenis_dokumen' => RefJenisDokumen::all(),
            'tingkat_kerjasama' => RefTingkatKerjaSama::all(),
            'fakultas' => RefLembagaUMS::where('jenis_lmbg', 'Fakultas')->get(),
            'program_studi' => RefLembagaUMS::where('jenis_lmbg', 'Program Studi')->get(),
            'unit' => RefLembagaUMS::where('jenis_lmbg', 'Unit (Biro/Lembaga)')->get(),
            'jenis_institusi_mitra' => RefJenisInstitusiMitra::all(),
            'negara_mitra' => RefNegara::all(),
            'bentuk_kerjasama' => RefBentukKerjaSama::all(),
            'perusahaans' => RefPerusahaan::all(),
            'rangking_universitas' => RefGroupRangking::with(['getRangking'])->get(),
            'stats_kerma' => 'Ajuan Baru',
            'prodi_user' => RefLembagaUMS::where('nama_lmbg', auth()->user()->status_tempat)->first()->id_lmbg ?? '',
            'fak_user' => auth()->user()->place_state,
        ];

        session(['ajuan_kerma_key' => Str::random(40)]);


        if (session('current_role') == 'user' || session('current_role') == 'verifikator') {
            $data['li_active'] = 'pengajuan_kerjasama';
        }

        // Ambil Dokumen MoU yang sudah pernah diajukan
        $data['jenis_mou'] = PengajuanKerjaSama::where(function ($q) {
            $q->where('jenis_kerjasama', 'like', '%Memorandum of Understanding (MoU)%')
                ->orWhere('jenis_kerjasama', 'like', '%Memorandum of Agreement (MoA)%')
                ->orWhere('jenis_kerjasama', 'like', '%MoA%')
                ->orWhere('jenis_kerjasama', 'like', '%MoU%');
        })
            ->whereNot('tgl_selesai', '0000-00-00 00:00:00')
            ->where(function ($q) {
                $q->where('periode_kerma', 'notknown')
                    ->where(function ($subQ) {
                        $subQ->whereDate('awal', '<=', now());
                        $subQ->orwhereDate('mulai', '<=', now());
                    })
                    ->orWhere(function ($q) {
                        $q->where('periode_kerma', 'bydoc')
                            ->whereDate('mulai', '<=', now())
                            ->whereDate('selesai', '>=', now());
                    });
            })
            ->get();


        $queryDokumen = DB::table('kerma_db as kerma')
            ->where(function ($q) {
                $q->where('kerma.tgl_selesai', '!=', '0000-00-00 00:00:00')
                    ->whereDate('kerma.selesai', '>=', now());
            });

        if ($id_mou != null) {
            $queryDokumen->orWhere('kerma.id_mou', $id_mou);
        }

        $data['dokumen_perpanjang'] = $queryDokumen->get();
        // return $data['dokumen_perpanjang'];

        if ($id_mou != null) {
            $data['page_title'] = 'Edit Dokumen Baru';
            $data['id_mou'] = $id_mou;
            $data['logFileSKMitra'] = getLogFile($id_mou, 'file_sk_mitra');
            $data['logFileMoU'] = getLogFile($id_mou, 'file_mou');
            $data['logFileAjuan'] = getLogFile($id_mou, 'file_ajuan');
            $dataPengajuan = PengajuanKerjaSama::where('id_mou', $id_mou)->where('stats_kerma', 'Ajuan Baru')->firstOrFail();
            $data['dataPengajuan'] = $dataPengajuan;

            if (
                $dataPengajuan->tgl_selesai != '0000-00-00 00:00:00' ||
                ($dataPengajuan->status_verify_admin == '1' && session('current_role') != 'admin' && $dataPengajuan->ttd_by == 'BKUI')
            ) {
                return abort(403);
            }

            if ($dataPengajuan->stats_kerma == 'Ajuan Baru') {
                $draft = false;
                if ($dataPengajuan->file_ajuan != null) {
                    $draft = true;
                }
            } else {
                $draft = true;
            }
            $data['fileDraft'] = $draft;

            return view('pengajuan/edit', $data);
        } else {
            return view('pengajuan/tambah', $data);
        }
    }

    public function laporPengajuan($id_mou = null)
    {
        $data = [
            'title' => 'Simpan Dokumen',
            'page_title' => 'Simpan Dokumen',
            'jenis_dokumen' => RefJenisDokumen::all(),
            'tingkat_kerjasama' => RefTingkatKerjaSama::all(),
            // 'jenis_mou' => RefJenisDokumenMoU::all(),
            // 'jenis_mou' => PengajuanKerjaSama::where('jenis_kerjasama', 'Memorandum of Understanding (MoU)')->get(),
            'fakultas' => RefLembagaUMS::where('jenis_lmbg', 'Fakultas')->get(),
            'program_studi' => RefLembagaUMS::where('jenis_lmbg', 'Program Studi')->get(),
            'unit' => RefLembagaUMS::where('jenis_lmbg', 'Unit (Biro/Lembaga)')->get(),
            'jenis_institusi_mitra' => RefJenisInstitusiMitra::all(),
            'negara_mitra' => RefNegara::all(),
            'perusahaans' => RefPerusahaan::all(),
            'bentuk_kerjasama' => RefBentukKerjaSama::all(),
            'rangking_universitas' => RefGroupRangking::with(['getRangking'])->get(),
            'stats_kerma' => 'Lapor Kerma',
            'prodi_user' => RefLembagaUMS::where('nama_lmbg', auth()->user()->status_tempat)->first()->id_lmbg ?? '',
            'fak_user' => auth()->user()->place_state
        ];

        $data['jenis_mou'] = PengajuanKerjaSama::where(function ($q) {
            $q->where('jenis_kerjasama', 'like', '%Memorandum of Understanding (MoU)%')
                ->orWhere('jenis_kerjasama', 'like', '%Memorandum of Agreement (MoA)%')
                ->orWhere('jenis_kerjasama', 'like', '%MoA%')
                ->orWhere('jenis_kerjasama', 'like', '%MoU%');
        })
            ->whereNot('tgl_selesai', '0000-00-00 00:00:00')
            ->where(function ($q) {
                $q->whereIn('status_mou', ['Aktif', 'Tidak Aktif', 'aktif', 'tidak aktif'])
                    ->whereDate('mulai', '<=', now());
            })
            ->orWhere(function ($q) {
                $q->whereNull('status_mou')
                    ->whereDate('mulai', '<=', now())
                    ->whereDate('selesai', '>=', now());
            })
            ->get();

        session(['ajuan_kerma_key' => Str::random(40)]);


        if ($id_mou != null) {
            $data['page_title'] = 'Edit Simpan Dokumen';
            $data['id_mou'] = $id_mou;
            $data['logFileSKMitra'] = getLogFile($id_mou, 'file_sk_mitra');
            $data['logFileMoU'] = getLogFile($id_mou, 'file_mou');
            $dataPengajuan = PengajuanKerjaSama::where('id_mou', $id_mou)->where('stats_kerma', 'Lapor Kerma')->firstOrFail();

            $data['dataPengajuan'] = $dataPengajuan;
            $data['fileDraft'] = true;

            if (
                $dataPengajuan->tgl_selesai != '0000-00-00 00:00:00' ||
                ($dataPengajuan->status_verify_admin == '0') ||
                ($dataPengajuan->status_verify_admin == '1' && session('current_role') != 'admin')
            ) {
                return abort(403);
            }

            return view('pengajuan/edit', $data);
        } else {
            return view('pengajuan/tambah', $data);
        }
    }

    public function store_baru(Request $request)
    {
        if ($request->ajuan_kerma_key !== session('ajuan_kerma_key') || session('ajuan_kerma_key') == null) {
            return response()->json(['error' => 'Form tidak valid, silakan reload tab terbaru.'], 403);
        }
        $statsKermaAllowed = ['Ajuan Baru', 'Lapor Kerma'];

        if (!in_array($request->stats_kerma, $statsKermaAllowed)) {
            return response()->json(['error' => 'Form tidak valid, silakan reload tab terbaru.'], 403);
        }

        if ($request->id_mou) {
            $kerma = PengajuanKerjaSama::where('id_mou', $request->id_mou)->firstOrFail();
        }

        $rules = [
            'jenis_kerjasama' => 'required',
            'prodi_unit' => 'required',
            'nama_institusi' => 'required|max:255',
            'no_dokumen' => 'max:255',
            'nama_institusi' => 'required_if:jenis_institusi,Lain-lain',
            'dn_ln' => 'required',
            'alamat_mitra' => 'required|string|max:255',
            'wilayah_mitra' => 'required_if:dn_ln,Dalam Negeri',
            'negara_mitra' => 'required_if:dn_ln,Luar Negeri',
            'mulai' => 'required|date',
            'file_sk_mitra' => 'nullable|max:5120|mimes:pdf',
            // 'file_mou' => 'required_if:stats_kerma,Lapor Kerma|max:5120|mimes:pdf',
            'file_ajuan' => 'nullable|max:5120|mimes:doc,docx',
            'place_state' => 'required',
        ];

        if ($request->stats_kerma === 'Lapor Kerma') {
            if (empty($kerma->file_mou)) {
                $rules['file_mou'] = 'required|mimes:pdf|max:5120';
            } else {
                $rules['file_mou'] = 'nullable|mimes:pdf|max:5120';
            }
        }
        if ($request->stats_kerma === 'Ajuan Baru') {
            $rules['status_kerma'] = 'required';
            $rules['mou_perpanjangan'] = 'required_if:status_kerma,Dalam Perpanjangan';
        }


        $message = [

            'jenis_kerjasama.required' => 'Jenis Kerja Sama harus diisi.',
            'prodi_unit.required' => 'Tingkat Kerja Sama harus diisi.',
            'place_state.required' => 'Anda Belum Mempunyai Lembaga. Segera Hubungi Admin.',
            'no_dokumen.max' => 'Nomor Dokumen Maksimal 255 Karakter.',
            'nama_institusi.required' => 'Nama Institusi Mitra harus diisi.',
            'nama_institusi.max' => 'Nama Institusi Mitra Maksimal 255 Karakter.',
            'nama_institusi.required_if' => 'Nama Institusi harus diisi.',
            'dn_ln.required' => 'Dalam Negeri/Luar Negeri harus diisi.',
            'alamat_mitra.required' => 'Alamat Mitra harus diisi.',
            'alamat_mitra.max' => 'Alamat Mitra Maksimal 255 Karakter.',
            'mulai.required' => 'Tanggal Mulai Kerja Sama harus diisi.',
            'mulai.date' => 'Format tanggal mulai tidak valid.',
            'file_sk_mitra.mimes' => 'File SK Mitra Harus Berformat PDF.',
            'file_sk_mitra.max' => 'File SK Mitra maksimal 5MB.',
            'file_mou.max' => 'File Dokumen Kerja Sama maksimal 5MB.',
            'file_mou.mimes' => 'File Dokumen Kerja Sama Harus Berformat PDF.',
            'file_mou.required_if' => 'File Dokumen Kerja Sama Harus Diisi.',
            'file_ajuan.mimes' => 'File Harus Berformat .doc atau .docx.',
            'file_ajuan.max' => 'File Draft maksimal 5MB.',

        ];

        if ($request->stats_kerma === 'Ajuan Baru') {
            $message['status_kerma.required'] = 'Status Dokumen harus diisi.';
            $message['mou_perpanjangan.required_if'] = 'Dokumen yang diperpanjang harus di isi.';
        }

        // if ($request->stats_kerma == 'Ajuan Baru') {
        $rules = array_merge($rules, [
            'nama_internal_pic' => 'required|max:255',
            'lvl_internal_pic' => 'required|max:255',
            'email_internal_pic' => 'required|email',
            'telp_internal_pic' => 'required|min:6',
            'nama_eksternal_pic' => 'required|max:255',
            'lvl_eksternal_pic' => 'required|max:255',
            'email_eksternal_pic' => 'required|email',
            'telp_eksternal_pic' => 'required|min:6',
        ]);

        $message = array_merge($message, [
            'nama_internal_pic.required' => 'Tanda tangan PIC internal harus diisi.',
            'nama_internal_pic.max' => 'Nama PIC Internal Maksimal 200 Karakter.',
            'nama_eksternal_pic.max' => 'Nama PIC Eksternal Maksimal 200 Karakter.',

            'lvl_internal_pic.required' => 'Jabatan PIC internal harus diisi.',
            'lvl_internal_pic.max' => 'Jabatan PIC Internal Maksimal 200 Karakter.',
            'lvl_eksternal_pic.max' => 'Jabatan PIC Eksternal Maksimal 200 Karakter.',

            'email_internal_pic.required' => 'Email PIC internal harus diisi.',
            'email_internal_pic.email' => 'Format email PIC internal tidak valid.',
            'telp_internal_pic.required' => 'Telepon PIC internal harus diisi.',
            'telp_internal_pic.min' => 'Nomor telepon PIC internal minimal 6 digit.',
            'nama_eksternal_pic.required' => 'Tanda tangan PIC eksternal harus diisi.',
            'lvl_eksternal_pic.required' => 'Jabatan PIC eksternal harus diisi.',
            'email_eksternal_pic.required' => 'Email PIC eksternal harus diisi.',
            'email_eksternal_pic.email' => 'Format email PIC eksternal tidak valid.',
            'telp_eksternal_pic.required' => 'Telepon PIC eksternal harus diisi.',
            'telp_eksternal_pic.min' => 'Nomor telepon PIC internal minimal 6 digit.',
        ]);

        if ($request->stats_kerma === 'Lapor Kerma') {
            if (empty($kerma->file_mou)) {
                $message['file_mou.required'] = 'Dokumen Kerja Sama harus diisi';
                $message['file_mou.mimes'] = 'Dokumen Kerja Sama harus berformat PDF';
                $message['file_mou.max'] = 'Dokumen Kerja Sama maksimal 5 MB';
            } else {
                $message['file_mou.mimes'] = 'Dokumen Kerja Sama harus berformat PDF';
                $message['file_mou.max'] = 'Dokumen Kerja Sama maksimal 5 MB';
            }
        }
        // }

        if ($request->id_mou) {
            $dataExist = PengajuanKerjaSama::where('id_mou', $request->id_mou)->firstOrFail();
            $hasRevisi = [
                'kaprodi' => $dataExist->status_verify_kaprodi == '0' && $dataExist->status_verify_kaprodi != null,
                'admin' => $dataExist->status_verify_admin == '0' && $dataExist->status_verify_admin != null,
                'user' => $dataExist->status_verify_user == '0' && $dataExist->status_verify_user != null,
                'publish' => $dataExist->status_verify_publish == '0' && $dataExist->status_verify_publish != null,
            ];

            if ($dataExist->status_verify_admin == '1' || $dataExist->status_verify_user == '1') {
                $rules = [
                    'file_mou' => 'nullable|max:5120|mimes:pdf',
                    'file_ajuan' => 'nullable|max:5120|mimes:doc,docx',
                    'place_state' => 'required',
                ];

                $message = [
                    'place_state.required' => 'Anda Belum Mempunyai Lembaga. Segera Hubungi Admin.',
                    'file_mou.max' => 'File Dokumen Kerja Sama maksimal 5MB.',
                    'file_mou.mimes' => 'File Dokumen Kerja Sama Harus Berformat PDF.',
                    'file_ajuan.mimes' => 'File Harus Berformat .doc atau .docx.',
                    'file_ajuan.max' => 'File Draft maksimal 5MB.',

                ];
            }
        }


        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        DB::beginTransaction();
        try {
            // Generate ID jika tidak ada
            $id_mou = $request->id_mou ?? Str::uuid()->getHex();
            $dataExist = PengajuanKerjaSama::where('id_mou', $id_mou)->first();
            $dataInsert = [
                'no_dokumen' => $request->no_dokumen,
                'dokumen_mou' => $request->pilih_mou,
                'prodi_unit' => $request->prodi_unit,
                'jenis_kerjasama' => $request->jenis_kerjasama,
                'jenis_kerjasama_lain' => '',
                'kegiatan' => '',
                'keterangan' => '',
                'jdwl_keg' => '',
                'verifikasi' => '',
                'note' => '',
                'jml_kuesioner' => '0',
                'jml_productivity' => '0',
                'verify_kerma' => '',
                'status_lapor' => '',
                'stats_kerma' => $request->stats_kerma ?? '',
                'jenis_institusi' => $request->jenis_institusi ?? '',
                'jenis_perusahaan' => $request->jenis_perusahaan ?? '',
                'rangking_univ' => $request->rangking_univ ?? '',
                'nama_fk_mitra' => $request->nama_fk_mitra ?? '',
                'nama_dept_mitra' => $request->nama_dept_mitra ?? '',
                'jenis_institusi_lain' => $request->jenis_institusi_lain ?? '',
                'nama_institusi' => $request->nama_institusi,
                'dn_ln' => $request->dn_ln,
                'wilayah_mitra' => $request->wilayah_mitra,
                'negara_mitra' => $request->negara_mitra,
                'alamat_mitra' => $request->alamat_mitra,
                'ttd_internal' => implode(';', $request->ttd_internal ?? []),
                'lvl_internal' => implode(';', $request->lvl_internal ?? []),
                'ttd_eksternal' => implode(';', $request->ttd_eksternal ?? []),
                'lvl_eksternal' => implode(';', $request->lvl_eksternal ?? []),
                'lvl_eksternal_else' => $request->nama_institusi,
                'kontribusi' => implode(',', $request->kontribusi ?? []),
                'kontribusi_lain' => $request->kontribusi_lain,
                'periode_kerma' => $request->periode_kerma,
                'mulai' => $request->mulai,
                'selesai' => $request->selesai,
                'status_mou' => $request->status_mou,
                'timestamp' => Carbon::now(),
            ];

            $dataInsert['id_mou_perpanjang'] = '';
            if ($request->status_kerma == 'Dalam Perpanjangan') {
                PengajuanKerjaSama::where('id_mou', $request->mou_perpanjangan)->update(['status_kerma' => 'Dalam Perpanjangan']);
                $dataInsert['id_mou_perpanjang'] = $request->mou_perpanjangan;
            }

            if (!empty($dataExist) && $request->status_kerma == 'Ajuan Baru') {
                PengajuanKerjaSama::where('id_mou', $dataExist->id_mou_perpanjang)->update(['status_kerma' => null]);
                $dataInsert['id_mou_perpanjang'] = '';
            }

            // if ($request->stats_kerma == 'Ajuan Baru') {
            $dataInsert = array_merge($dataInsert, [
                'nama_internal_pic' => $request->nama_internal_pic,
                'lvl_internal_pic' => $request->lvl_internal_pic,
                'email_internal_pic' => $request->email_internal_pic,
                'telp_internal_pic' => $request->telp_internal_pic,
                'nama_eksternal_pic' => $request->nama_eksternal_pic,
                'lvl_eksternal_pic' => $request->lvl_eksternal_pic,
                'email_eksternal_pic' => $request->email_eksternal_pic,
                'telp_eksternal_pic' => $request->telp_eksternal_pic,
            ]);
            // }

            $ttd_by = RefJenisDokumen::where('nama_dokumen', $request->jenis_kerjasama)->first()->ttd_by ?? 'BKUI';
            $dataInsert['ttd_by'] = $ttd_by;

            if (session('current_role') == 'verifikator' && in_array(Auth::user()->jabatan, ['Kaprodi', 'Dekan', 'Kepala', 'Direktur'])) {
                $dataInsert['tgl_verifikasi_kaprodi'] = date('Y-m-d H:i:s');
                $dataInsert['verify_kaprodi_by'] = Auth::user()->username;

                $dataInsert['status_verify_kaprodi'] = '1';

                $dataInsert['input_kerma'] = 'Ajuan User';
            } elseif (session('current_role') == 'admin') {
                $dataInsert['tgl_verifikasi_kaprodi'] = date('Y-m-d H:i:s');
                $dataInsert['verify_kaprodi_by'] = Auth::user()->username;
                $dataInsert['status_verify_kaprodi'] = '1';

                $dataInsert['tgl_verifikasi_kabid'] = date('Y-m-d H:i:s');
                $dataInsert['verify_kabid_by'] = Auth::user()->username;
                $dataInsert['status_verify_admin'] = '1';

                $dataInsert['tgl_verifikasi_user'] = date('Y-m-d H:i:s');
                $dataInsert['verify_user_by'] = Auth::user()->username;
                $dataInsert['status_verify_user'] = '1';

                $dataInsert['tgl_verifikasi_publish'] = date('Y-m-d H:i:s');
                $dataInsert['verify_publish_by'] = Auth::user()->username;
                $dataInsert['status_verify_publish'] = '1';

                $dataInsert['tgl_selesai'] = date('Y-m-d H:i:s');
                $dataInsert['input_kerma'] = 'Ajuan BKUI';
            } else {
                $dataInsert['input_kerma'] = 'Ajuan User';
            }

            $allFiles = [];
            // Simpan file SK Mitra
            if ($request->hasFile('file_sk_mitra')) {
                $file_sk_mitra = $request->file('file_sk_mitra');
                $path = "uploads/pengajuan/sk_mitra";
                $filePathMitra = $this->upload_file($file_sk_mitra, $path);
                $dataInsert['file_sk_mitra'] = $filePathMitra;

                $dtFiles = [
                    'jenis' => 'file_sk_mitra',
                    'path' => $filePathMitra
                ];

                $allFiles[] = $dtFiles;
            } else {
                if (!empty($dataExist) && $dataExist->file_sk_mitra != null && $dataExist->file_sk_mitra != '') {
                } else {
                    $dataInsert['file_sk_mitra'] = null;
                }
            }

            // Simpan file MoU
            if ($request->hasFile('file_ajuan') && $request->stats_kerma == "Ajuan Baru") {
                $file_ajuan = $request->file('file_ajuan');
                $path = "uploads/pengajuan/ajuan";
                $filePathAjuan = $this->upload_file($file_ajuan, $path);
                $dataInsert['file_ajuan'] = $filePathAjuan;
                $dataInsert['tgl_draft_upload'] = date('Y-m-d H:i:s');

                $dtFiles = [
                    'jenis' => 'file_ajuan',
                    'path' => $filePathAjuan
                ];

                $dataInsert['draft_by'] = session('current_role');

                $allFiles[] = $dtFiles;
            } else {
                if (!empty($dataExist) && $dataExist->file_ajuan != null && $dataExist->file_ajuan != '') {
                } else {
                    $dataInsert['file_ajuan'] = null;
                    $dataInsert['draft_by'] = '';
                }
            }

            // Simpan file MoU
            if ($request->hasFile('file_mou')) {
                $file_mou = $request->file('file_mou');
                $path = "uploads/pengajuan/mou";
                $filePathMoU = $this->upload_file($file_mou, $path);
                $dataInsert['file_mou'] = $filePathMoU;
                if ($request->stats_kerma == "Ajuan Baru") {
                    if (session('current_role') == 'admin') {
                        $dataInsert['tgl_req_ttd'] = date('Y-m-d H:i:s');
                        $dataInsert['tgl_verifikasi_publish'] = date('Y-m-d H:i:s');
                        $dataInsert['verify_publish_by'] = Auth::user()->username;
                        $dataInsert['tgl_selesai'] = date('Y-m-d H:i:s');
                    } else {
                        $dataInsert['tgl_req_ttd'] = date('Y-m-d H:i:s');
                    }
                }

                $dtFiles = [
                    'jenis' => 'file_mou',
                    'path' => $filePathMoU
                ];

                $allFiles[] = $dtFiles;
            } else {
                if (!empty($dataExist) && $dataExist->file_mou != null && $dataExist->file_mou != '') {
                } else {
                    $dataInsert['file_mou'] = null;
                }
            }
            // return response()->json(['error' => $dataInsert], 500);

            // Menyesuaikan `prodi_unit`
            switch ($request->prodi_unit) {
                case 'Fakultas':
                    $dataInsert['lvl_fak'] = $request->lvl_fak;
                    $dataInsert['id_lembaga'] = $request->lvl_fak;
                    // $dataInsert['place_state'] = $request->lvl_fak;
                    break;
                case 'Program Studi':
                    $dataInsert['lvl_prodi'] = $request->lvl_prodi;
                    $dataInsert['id_lembaga'] = $request->lvl_prodi;
                    // $dataInsert['place_state'] = $request->lvl_prodi;
                    break;
                case 'Unit (Biro/Lembaga)':
                    $dataInsert['lvl_unit'] = $request->lvl_unit;
                    $dataInsert['id_lembaga'] = $request->lvl_unit;
                    break;
            }

            // Simpan atau update data
            // $dataInsert['place_state'] = $request->place_state;
            if ($request->id_mou) {
                $resetStatus = function ($role) use (&$dataInsert) {
                    $dataInsert["status_verify_{$role}"] = null;
                    $dataInsert['note'] = '';
                };

                if ($hasRevisi['kaprodi']) {
                    $resetStatus('kaprodi');
                } elseif ($hasRevisi['admin']) {
                    $resetStatus('admin');
                } elseif ($hasRevisi['user']) {
                    if ($dataExist->status_verify_admin == '1') {
                        $dataInsert = [];
                        if (
                            $request->hasFile('file_ajuan')
                            && $request->stats_kerma == "Ajuan Baru"
                        ) {
                            $dataInsert['file_ajuan'] = $filePathAjuan;
                            $dataInsert['tgl_draft_upload'] = now();
                        }
                    }
                    $resetStatus('user');
                } elseif ($hasRevisi['publish']) {
                    if ($dataExist->status_verify_admin == '1') {
                        $dataInsert = [];
                        if (
                            $request->hasFile('mou')
                        ) {
                            $dataInsert['file_mou'] = $filePathMoU;
                            if ($request->stats_kerma == "Ajuan Baru") {
                                if (session('current_role') == 'admin') {
                                    $dataInsert['tgl_req_ttd'] = date('Y-m-d H:i:s');
                                    $dataInsert['tgl_verifikasi_publish'] = date('Y-m-d H:i:s');
                                    $dataInsert['verify_publish_by'] = Auth::user()->username;
                                    $dataInsert['tgl_selesai'] = date('Y-m-d H:i:s');
                                } else {
                                    $dataInsert['tgl_req_ttd'] = date('Y-m-d H:i:s');
                                }
                            }
                        }
                    }
                    $resetStatus('publish');
                } else if ($dataExist->status_verify_admin == '1' && $dataExist->status_verify_user != '1') {
                    $dataInsert = [];
                    if (
                        $request->hasFile('file_ajuan')
                        && $request->stats_kerma == "Ajuan Baru"
                    ) {
                        $dataInsert['file_ajuan'] = $filePathAjuan;
                        $dataInsert['tgl_draft_upload'] = now();
                    }
                } else if ($dataExist->status_verify_user == '1') {
                    $dataInsert = [];
                    if (
                        $request->hasFile('file_mou')
                    ) {
                        $dataInsert['file_mou'] = $filePathMoU;
                        if ($request->stats_kerma == "Ajuan Baru") {
                            if (session('current_role') == 'admin') {
                                $dataInsert['tgl_req_ttd'] = date('Y-m-d H:i:s');
                                $dataInsert['tgl_verifikasi_publish'] = date('Y-m-d H:i:s');
                                $dataInsert['verify_publish_by'] = Auth::user()->username;
                                $dataInsert['tgl_selesai'] = date('Y-m-d H:i:s');
                            } else {
                                $dataInsert['tgl_req_ttd'] = date('Y-m-d H:i:s');
                            }
                        }
                    }
                }

                $tipe = "edit";
                $insert = PengajuanKerjaSama::where('id_mou', $id_mou)->update($dataInsert);

                $inputData = 0;
                $dataLogketerangan = 'Update';
            } else {
                $tipe = "submit";
                $dataInsert['place_state'] = Auth::user()->place_state;
                $dataInsert['status_tempat'] = Auth::user()->status_tempat;
                $dataInsert['add_by'] = Auth::user()->username;
                $dataInsert['id_mou'] = $id_mou;
                $insert = PengajuanKerjaSama::create($dataInsert);
                $inputData = 1;
                $dataLogketerangan = 'Baru';
            }

            $dataEmail = PengajuanKerjaSama::where('id_mou', $id_mou)->first()->toArray();
            foreach ($allFiles as $key => $value) {
                $dataLog = [
                    'table' => 'kerma_db',
                    'id_table' => $id_mou,
                    'jenis' => $value['jenis'],
                    'path' => $value['path'],
                    'keterangan' => $dataLogketerangan,
                    'add_by' => Auth::user()->username,
                    'role' => session('current_role')
                ];
                LogActivity::create($dataLog);
            }

            DB::commit();
            // $insert = true;
            if ($insert) {
                if (session('current_role') == 'verifikator') {
                    $receiver = 'admin';
                } else {
                    $receiver = 'verifikator';
                }

                $idMou = $request->id_mou;
                $fileAjuan = $request->hasFile('file_ajuan');
                $fileMOU = $request->hasFile('file_mou');
                $dataEmail['sender'] = 'MyPartnership UMS';

                $mail = MailSetting::where('is_active', '1')->first();
                $role = session('current_role');
                $nama_institusi = $dataEmail['nama_institusi'];
                $AddBy = $dataEmail['add_by'];
                $statusKerma = $dataEmail['stats_kerma'];
                $placeState = $dataEmail['place_state'];

                $notifikasi_upload_draft = $fileAjuan && $idMou;
                $notifikasi_upload_mou = $fileMOU && $idMou;
                if ($notifikasi_upload_draft) {
                    // EMAIL NOTIFIKASI DRAFT SUDAH DI UPLOAD
                    $tipeEmail = 'upload_draft';

                    $mailMessages = MailMessages::where('jenis', 'subjek_draft')->first();
                    $title = $mailMessages->subjek;
                    $viewEmail = $mailMessages->pesan;

                    // if ($mailMessages->status == '1') {

                    // $title = $mail->subjek_draft;
                    // $viewEmail = $mail->draft_email;

                    // $arrReceiver = array_map('trim', explode(',', $mail->email_receiver));
                    if ($role == 'verifikator' || $role == 'user') {
                        // Kirim Ke Admin
                        // Ambil data pengirim & penerima
                        $arrReceiver = array_map('trim', explode(',', $mail->email_receiver));
                    } else if ($role == 'admin') {
                        // Kirim Ke User
                        $arrReceiver = User::where('username', $dataEmail['add_by'])
                            ->distinct()
                            ->pluck('email')
                            ->toArray();
                    }
                    $message = (string) str_replace("{@nama_institusi}", $dataEmail['nama_institusi'], $viewEmail);
                    if (empty(trim($message))) {
                        $message = "Tidak ada pesan yang tersedia.";
                    }
                    $dataEmailPIC = [
                        'subject' => $title,
                        'dataMessage' => $message,
                        'sender' => 'MyPartnership',
                    ];
                    $message = view('emails.template', $dataEmailPIC)->render();
                    if (!empty($mailMessages) && $mailMessages->status == 1) {
                        foreach ($arrReceiver as $receiver) {
                            $dataSendMail = [
                                'message' => $message,
                                'title' => $title,
                                'institusi' => $nama_institusi,
                                'session' => session('environment'),
                                'sender' => Auth::user()->username,
                                'MailSetting' => $mail->toArray(),
                                'receiver' => $receiver
                            ];
                            // SendEmailMailer::dispatchSync($dataSendMail);
                            SendEmailMailer::dispatch($dataSendMail);
                        }
                    }

                    // SendEmailDraft::dispatchSync($dataEmail, session('current_role'), session('environment'));
                } else if ($notifikasi_upload_mou) {
                    // EMAIL NOTIFIKASI MOU SUDAH DI UPLOAD
                    $tipeEmail = 'mou_upload';

                    $mailMessages = MailMessages::where('jenis', 'subjek_upload_mou')->first();
                    $title = $mailMessages->subjek;
                    $viewEmail = $mailMessages->pesan;

                    // $title = $mail->subjek_upload_mou;
                    // $viewEmail = $mail->upload_mou;

                    if ($role == 'verifikator' || $role == 'user') {
                        // Kirim Ke Admin
                        $arrReceiver = array_map('trim', explode(',', $mail->email_receiver));
                    } else if ($role == 'admin') {
                        // Kirim Ke User
                        $arrReceiver = User::where('username', $AddBy)
                            ->distinct()
                            ->pluck('email')
                            ->toArray();
                    }

                    $institusiReplace = (string) str_replace("{@nama_institusi}", $nama_institusi, $viewEmail);
                    // $pesanReplace = (string) str_replace("{@pesan}", $role == 'user' ? 'Silahkan Verifikasi Dokumen Kerja Sama untuk di Publish' : 'Silahkan Cek Di Menu Dokumen Kerja Sama UMS', $institusiReplace);
                    $message = (string) str_replace("{@role}", $role == 'user' ? 'Admin' : 'Pengusul', $institusiReplace);

                    $dataEmailPIC = [
                        'subject' => $title,
                        'dataMessage' => $message,
                        'sender' => 'MyPartnership',
                    ];
                    $message = view('emails.template', $dataEmailPIC)->render();
                    if (!empty($mailMessages) && $mailMessages->status == 1) {
                        foreach ($arrReceiver as $receiver) {
                            $dataSendMail = [
                                'message' => $message,
                                'title' => $title,
                                'institusi' => $nama_institusi,
                                'session' => session('environment'),
                                'sender' => Auth::user()->username,
                                'MailSetting' => $mail->toArray(),
                                'receiver' => $receiver
                            ];
                            // SendEmailMailer::dispatchSync($dataSendMail);
                            SendEmailMailer::dispatch($dataSendMail);
                        }
                    }
                } else {
                    if ($tipe == 'edit' && $statusKerma == 'Ajuan Baru') {
                        $tipeEmail = 'edit';
                        // EMAIL NOTIFIKASI EDIT AJUAN BARU
                        $mailMessages = MailMessages::where('jenis', 'subjek_edit_ajuan')->first();
                        $title = $mailMessages->subjek;
                        $viewEmail = $mailMessages->pesan;

                        // $title = $mail->subjek_edit_ajuan;
                        // $viewEmail = $mail->ajuan_edit_email;
                    } else if ($tipe == 'submit' && $statusKerma == 'Ajuan Baru') {
                        $tipeEmail = 'create';

                        $mailMessages = MailMessages::where('jenis', 'subjek_ajuan')->first();
                        $title = $mailMessages->subjek;
                        $viewEmail = $mailMessages->pesan;
                        // EMAIL NOTIFIKASI CREATE AJUAN BARU
                        // $title = $mail->subjek_ajuan;
                        // $viewEmail = $mail->ajuan_email;
                    } else if ($tipe == 'edit' && $statusKerma == 'Lapor Kerma') {
                        $tipeEmail = 'edit_lapor';
                        // EMAIL NOTIFIKASI EDIT LAPOR DOKUMEN
                        $mailMessages = MailMessages::where('jenis', 'subjek_edit_lapor')->first();
                        $title = $mailMessages->subjek;
                        $viewEmail = $mailMessages->pesan;

                        // $title = $mail->subjek_edit_lapor;
                        // $viewEmail = $mail->lapor_edit_email;
                    } else if ($tipe == 'submit' && $statusKerma == 'Lapor Kerma') {
                        $tipeEmail = 'create_lapor';
                        $mailMessages = MailMessages::where('jenis', 'subjek_lapor')->first();
                        $title = $mailMessages->subjek;
                        $viewEmail = $mailMessages->pesan;

                        // EMAIL NOTIFIKASI SUBMIT LAPOR DOKUMEN
                        // $title = $mail->subjek_lapor;
                        // $viewEmail = $mail->lapor_email;
                    }

                    if ($receiver == 'admin') {
                        $arrReceiver = array_map('trim', explode(',', $mail->email_receiver));
                    } else {
                        // $arrReceiver = User::where('place_state', $placeState)
                        // $arrReceiver = User::where('status_tempat', Auth::user()->status_tempat)
                        $arrReceiver = User::where('status_tempat', $dataEmail['status_tempat'])
                            ->whereHas('roles', function ($query) {
                                $query->whereIn('jabatan', ['Kaprodi', 'Dekan', 'Direktur', 'Kepala', 'Ketua']);
                            })
                            ->distinct()
                            ->pluck('email')
                            ->toArray();
                        if (count($arrReceiver) == 0) {
                            $arrReceiver = User::where('status_tempat', $dataEmail['status_tempat'])
                                ->whereHas('roles', function ($query) {
                                    $query->where('name', 'verifikator');
                                })
                                ->distinct()
                                ->pluck('email')
                                ->toArray();
                        }
                    }

                    // return response()->json(['status'=>true, 'message' => 'Data berhasil disimpan', 'arrReceiver' => $arrReceiver], 500);

                    $message = (string) str_replace("{@nama_institusi}", $nama_institusi, $viewEmail);

                    // Jika $message kosong, beri fallback default
                    if (empty(trim($message))) {
                        $message = "Tidak ada pesan yang tersedia.";
                    }
                    $dataEmailPIC = [
                        'subject' => $title,
                        'dataMessage' => $message,
                        'sender' => 'MyPartnership',
                    ];
                    $message = view('emails.template', $dataEmailPIC)->render();
                    if (!empty($mailMessages) && $mailMessages->status == 1) {
                        foreach ($arrReceiver as $receiver) {
                            $dataSendMail = [
                                'message' => $message,
                                'title' => $title,
                                'institusi' => $nama_institusi,
                                'session' => session('environment'),
                                'sender' => Auth::user()->username,
                                'MailSetting' => $mail->toArray(),
                                'receiver' => $receiver
                            ];
                            // SendEmailMailer::dispatchSync($dataSendMail);
                            SendEmailMailer::dispatch($dataSendMail);
                        }
                    }
                }

                // if ($request->stats_kerma == 'Ajuan Baru' && $inputData == 1) {
                if ($inputData == 1) {
                    $tipeEmail = 'pic';
                    // EMAIL NOTIFIKASI PIC KETIKA CREATE PENGAJUAN 
                    $arrReceiverPIC = [
                        $request->email_internal_pic,
                        $request->email_eksternal_pic,
                    ];

                    $mailMessagesPIC = MailMessages::where('jenis', 'subjek_kirim_pic_pengajuan')->first();
                    $titlePIC = $mailMessagesPIC->subjek;
                    $viewEmailPIC = $mailMessagesPIC->pesan;

                    // $titlePIC = $mail->subjek_kirim_pic_pengajuan;
                    // $viewEmailPIC = $mail->kirim_pic_pengajuan;

                    $messagePIC = (string) str_replace("{@nama_institusi}", $dataEmail['nama_institusi'], $viewEmailPIC);
                    if (empty(trim($messagePIC))) {
                        $messagePIC = "Tidak ada pesan yang tersedia.";
                    }
                    $dataEmailPIC = [
                        'subject' => $titlePIC,
                        'dataMessage' => $messagePIC,
                        'sender' => 'MyPartnership',
                    ];
                    $messagePIC = view('emails.template', $dataEmailPIC)->render();
                    if (!empty($mailMessagesPIC) && $mailMessagesPIC->status == 1) {
                        foreach ($arrReceiverPIC as $receiver) {
                            $dataSendMailPIC = [
                                'message' => $messagePIC,
                                'title' => $titlePIC,
                                'institusi' => $nama_institusi,
                                'session' => session('environment'),
                                'sender' => Auth::user()->username,
                                'MailSetting' => $mail->toArray(),
                                'receiver' => $receiver
                            ];
                            SendEmailMailer::dispatch($dataSendMailPIC);
                            // SendEmailMailer::dispatchSync($dataSendMailPIC);
                        }
                    }
                }

                // if (!empty($mailMessages) && $mailMessages->status == 1 || !empty($mailMessagesPIC) && $mailMessagesPIC->status == 1) {
                //     $emailsToSend = [];
                //     $dataSendMail = [
                //         'arrReceiver' => $arrReceiver,
                //         'tipeEmail' => $tipeEmail,
                //         'message' => $message,
                //         'title' => $title,
                //         'institusi' => $nama_institusi,
                //         'session' => session('environment'),
                //         'sender' => Auth::user()->username,
                //         // 'MailSetting' => $mail->toArray(),
                //         'receiver' => $receiver,
                //     ];
                //     $emailsToSend[] = $dataSendMail;

                //     if ($inputData == 1) {
                //         $dataSendMailPIC = [
                //             'arrReceiver' => $arrReceiverPIC,
                //             'tipeEmail' => $tipeEmail,
                //             'message' => $messagePIC,
                //             'title' => $titlePIC,
                //             'institusi' => $nama_institusi,
                //             'session' => session('environment'),
                //             'sender' => Auth::user()->username,
                //             // 'MailSetting' => $mail->toArray(),
                //             'receiver' => $receiver
                //         ];
                //         $emailsToSend[] = $dataSendMailPIC;
                //     }

                //     session(['sendEmailData' => $emailsToSend]);
                // }

                session()->forget('ajuan_kerma_key');

                // return response()->json(['status' => true, 'dataSendMail' => $emailsToSend, 'message' => 'Data berhasil disimpan', 'route' => route('pengajuan.home')], 200);
                return response()->json(['status' => true, 'message' => 'Data berhasil disimpan', 'route' => route('pengajuan.home')], 200);
            }

            return response()->json(['status' => false, 'message' => 'Terdapat Masalah Ketika Penyimpanan'], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function sendEmail(Request $request)
    {
        try {
            // $dataEmail = $request->input('dataEmail');
            $dataEmail = session('sendEmailData');

            if (!is_array($dataEmail)) {
                throw new \Exception("Tidak ada email yang dikirim.");
            }

            $mail = MailSetting::where('is_active', 1)->first();
            session()->forget('sendEmailData');

            foreach ($dataEmail as $value) {

                $arrReceiver     = $value['arrReceiver'] ?? [];
                $message         = $value['message'] ?? null;
                $title           = $value['title'] ?? null;
                $nama_institusi  = $value['institusi'] ?? null;
                $tipeEmail       = $value['tipeEmail'] ?? null;

                foreach ($arrReceiver as $receiver) {
                    $dataSendMail = [
                        'message'     => $message,
                        'title'       => $title,
                        'institusi'   => $nama_institusi,
                        'session'     => session('environment'),
                        'sender'      => Auth::user()->username,
                        'MailSetting' => $mail,
                        'receiver'    => $receiver,
                    ];

                    SendEmailMailer::dispatchSync($dataSendMail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Email berhasil dikirim!'
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage()
            ], 500);
        }
    }




    public function switch_status(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::where('uuid', $request->uuid)->firstOrFail();
            $user->is_active = $request->status == '1' ? 1 : 0;
            $user->save();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Status User Berhasil Update']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $pengajuan = PengajuanKerjaSama::where('id_mou', $request->id_mou)->where('tgl_selesai', '0000-00-00 00:00:00')->firstOrFail();
            $pengajuan->delete();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Pengajuan Berhasil di Hapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function verifikasi(Request $request)
    {
        $status = $request->status;
        $tipe = $request->tipe;
        $verify_by = Auth::user()->username;
        $role = session('current_role');

        $statusAllowed = ['1', '0'];
        $tipeAllowed = ['bidang', 'dokumen'];

        if (!in_array($status, $statusAllowed) || !in_array($tipe, $tipeAllowed)) {
            return response()->json(['error' => 'Form tidak valid, silakan reload tab terbaru.'], 403);
        }

        $pengajuan = PengajuanKerjaSama::where('id_mou', $request->id_mou)->where('tgl_selesai', '0000-00-00 00:00:00')->firstOrFail();

        $verifikasi_status_kaprodi = 'Berhasil Di Verifikasi Kaprodi';
        $tolak_status_kaprodi = 'Ditolak Kaprodi';
        $verifikasi_status_admin = 'Berhasil Di Verifikasi Admin';
        $tolak_status_admin = 'Ditolak Admin';
        $verifikasi_status_pengusul = 'Berhasil Di Verifikasi Pengusul';
        $tolak_status_pengusul = 'Ditolak Pengusul';
        $verifikasi_status_publish = 'Dokumen Sudah Dipublish';
        $tolak_status_publish = 'Dokumen Ditolak Admin Publish';

        DB::beginTransaction();
        try {
            if ($request->hasFile('file_ajuan')) {
                $this->uploadDraft($request);
            }
            if ($tipe == 'bidang') {
                if ($role == 'verifikator') {
                    if ($pengajuan->tgl_verifikasi_kaprodi != '0000-00-00 00:00:00' && $pengajuan->tgl_verifikasi_kabid != '0000-00-00 00:00:00' && $pengajuan->add_by == Auth::user()->username) {
                        $data = [
                            'tgl_verifikasi_user' => $status == '1' ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00',
                            'verify_user_by' => $verify_by
                        ];
                        $data['status_verify_user'] = $status;
                        $data['verifikasi'] = $status == '1' ? $verifikasi_status_pengusul : $tolak_status_pengusul;
                    } else {
                        $data = [
                            'tgl_verifikasi_kaprodi' => $status == '1' ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00',
                            'verify_kaprodi_by' => $verify_by
                        ];
                        $data['status_verify_kaprodi'] = $status;
                        $data['verifikasi'] = $status == '1' ? $verifikasi_status_kaprodi : $tolak_status_kaprodi;
                    }
                } elseif ($role == 'admin') {
                    $data = [
                        'tgl_verifikasi_kabid' => $status == '1' ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00',
                        'verify_kabid_by' => $verify_by
                    ];
                    $data['status_verify_admin'] = $status;
                    $data['verifikasi'] = $status == '1' ? $verifikasi_status_admin : $tolak_status_admin;
                } elseif ($role == 'user') {
                    $data = [
                        'tgl_verifikasi_user' => $status == '1' ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00',
                        'verify_user_by' => $verify_by
                    ];
                    $data['status_verify_user'] = $status;
                    $data['verifikasi'] = $status == '1' ? $verifikasi_status_pengusul : $tolak_status_pengusul;
                }
            } else {
                if ($role == 'admin') {
                    $data = [
                        'tgl_verifikasi_publish' => $status == '1' ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00',
                        'verify_publish_by' => $verify_by,
                        'tgl_selesai' => $status == '1' ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00'
                    ];
                    $data['status_verify_publish'] = $status;
                    $data['verifikasi'] = $status == '1' ? $verifikasi_status_publish : $tolak_status_publish;
                }
            }

            $data['note'] = $status == '1' ? '' : $request->note ?? '';


            $pengajuan->update($data);

            if ($status == '1') {
                $dataEmail = PengajuanKerjaSama::where('id_mou', $request->id_mou)->first()->toArray();
                $mail = MailSetting::where('is_active', '1')->first();

                $mailMessages = MailMessages::where('jenis', 'subjek_verifikasi')->first();

                $role = session('current_role');
                $nama_institusi = $dataEmail['nama_institusi'];
                $addby = $dataEmail['add_by'];
                // $title = $mail->subjek_verifikasi;
                // $viewEmail = $mail->verifikasi;

                $title = $mailMessages->subjek;
                $viewEmail = $mailMessages->pesan;

                if ($mailMessages->status == '1') {
                    if ($role == 'verifikator' || $role == 'user') {
                        $arrReceiver = array_map('trim', explode(',', $mail->email_receiver));
                    } else if ($role == 'admin') {
                        $arrReceiver = User::where('username', $addby)
                            ->distinct()
                            ->pluck('email')
                            ->toArray();
                    }

                    $institusiReplace = (string) str_replace("{@nama_institusi}", $nama_institusi, $viewEmail);
                    $statusReplace = (string) str_replace("{@status}", $status == '1' ? 'Verifikasi' : 'Batalkan Verifikasi', $institusiReplace);
                    $message = (string) str_replace("{@verifikator}", $role == 'user' ? 'Pengusul' : ucwords($role), $statusReplace);

                    if (empty(trim($message))) {
                        $message = "Tidak ada pesan yang tersedia.";
                    }

                    if ($pengajuan) {
                        $statusVerifikasi = $status;
                        $dataEmail['sender'] = 'MyPartnership UMS';
                        $dataEmailPIC = [
                            'subject' => $title,
                            'dataMessage' => $message,
                            'sender' => 'MyPartnership',
                        ];
                        $message = view('emails.template', $dataEmailPIC)->render();
                        foreach ($arrReceiver as $receiver) {
                            $dataSendMail = [
                                'message' => $message,
                                'title' => $title,
                                'institusi' => $nama_institusi,
                                'session' => session('environment'),
                                'sender' => Auth::user()->username,
                                'MailSetting' => $mail->toArray(),
                                'receiver' => $receiver
                            ];
                            // SendEmailMailer::dispatchSync($dataSendMail);
                            SendEmailMailer::dispatch($dataSendMail);
                        }
                    }

                    // $emailsToSend = [];
                    // $dataSendMail = [
                    //     'arrReceiver' => $arrReceiver,
                    //     'message' => $message,
                    //     'title' => $title,
                    //     'institusi' => $nama_institusi,
                    //     'session' => session('environment'),
                    //     'sender' => Auth::user()->username,
                    // ];
                    // $emailsToSend[] = $dataSendMail;
                    // session(['sendEmailData' => $emailsToSend]);
                }

                $message = 'Verifikasi';
            } else {
                $message = 'Tolak';
            }

            DB::commit();
            return response()->json(['status' => true, '$status' => $status, 'message' => 'Pengajuan Berhasil di ' . $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function uploadDraft($request)
    {
        $rules = [
            'file_ajuan' => 'nullable|max:5120|mimes:doc,docx',
        ];
        $message = [
            'file_ajuan.mimes' => 'File Harus Berformat .doc atau .docx.',
            'file_ajuan.max' => 'File Draft maksimal 5MB.',
        ];

        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }


        $allFiles = [];
        $dataInsert = [];
        $id_mou = $request->id_mou;
        $dataExist = PengajuanKerjaSama::where('id_mou', $request->id_mou)->firstOrFail();
        DB::beginTransaction();
        try {
            if ($request->hasFile('file_ajuan') && $dataExist->stats_kerma == "Ajuan Baru") {
                $file_ajuan = $request->file('file_ajuan');
                $path = "uploads/pengajuan/ajuan";
                $filePathAjuan = $this->upload_file($file_ajuan, $path);
                $dataInsert['file_ajuan'] = $filePathAjuan;
                $dataInsert['tgl_draft_upload'] = date('Y-m-d H:i:s');

                $dtFiles = [
                    'jenis' => 'file_ajuan',
                    'path' => $filePathAjuan
                ];

                $dataInsert['draft_by'] = session('current_role');

                $allFiles[] = $dtFiles;
            } else {
                if (!empty($dataExist) && $dataExist->file_ajuan != null && $dataExist->file_ajuan != '') {
                } else {
                    $dataInsert['file_ajuan'] = null;
                    $dataInsert['draft_by'] = '';
                }
            }
            $insert = PengajuanKerjaSama::where('id_mou', $request->id_mou)->update($dataInsert);
            if ($insert) {
                foreach ($allFiles as $key => $value) {
                    $dataLog = [
                        'table' => 'kerma_db',
                        'id_table' => $id_mou,
                        'jenis' => $value['jenis'],
                        'path' => $value['path'],
                        'keterangan' => 'Baru',
                        'add_by' => Auth::user()->username,
                        'role' => session('current_role')
                    ];
                    LogActivity::create($dataLog);
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function pilihTTD(Request $request)
    {
        $ttd = $request->ttd;

        DB::beginTransaction();
        try {
            $data = [
                // 'tgl_req_ttd' => date('Y-m-d H:i:s'),
                'ttd_by' => $ttd,
            ];

            $pengajuan = PengajuanKerjaSama::where('id_mou', $request->id_mou)->firstOrFail();
            $pengajuan->update($data);
            DB::commit();

            return response()->json(['status' => true, 'message' => 'Penandatangan Berhasil di Pilih']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function setting()
    {
        $data = [
            'li_active' => 'setting',
            'title' => 'Setting',
            'page_title' => 'Setting',
            'dataSetting' => SettingBobot::getData(),
            'dataConfig' => ConfigWebsite::where('status', '1')->first(),

        ];

        return view('pengajuan/setting', $data);
    }

    public function storeSetting(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'dalam_negeri' => 'required',
                'luar_negeri' => 'required',
            ],
            [
                'dalam_negeri.required' => 'Bobot Dalam Negeri Harus Di Isi.',
                'luar_negeri.required' => 'Bobot Luar Negeri Di Isi.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Generate ID jika tidak ada
            $id_setting_bobot = $request->id_setting_bobot;
            $dataSetting = SettingBobot::where('id_setting_bobot', $id_setting_bobot)->first();

            $dataInsert = [
                'dalam_negeri' => str_replace('.', '', $request->dalam_negeri),
                'luar_negeri' => str_replace('.', '', $request->luar_negeri),
                'nomor_hp' => $request->nomor_hp,
                'email' => $request->email,
                'instagram' => $request->instagram,
                'facebook' => $request->facebook,
                'twitter' => $request->twitter,
                'tiktok' => $request->tiktok,
                'website_ums' => $request->website_ums,
                'website_bkui' => $request->website_bkui,
                'google_client_redirect' => $request->google_client_redirect,
            ];

            // Simpan atau update data
            if ($id_setting_bobot) {
                $dataSetting->update($dataInsert);
            }

            if ($request->session) {
                ConfigWebsite::query()->update(['status' => '0']);
                // Set status jadi 1 untuk data dengan keterangan sesuai request
                ConfigWebsite::where('keterangan', $request->session)
                    ->update(['status' => '1']);
                session(['environment' => $request->session]);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data berhasil disimpan'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
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

    public function download_pengajuan_excel(Request $request)
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
        $priorityIds = $this->getPriorityData();
        $priorityIdsArray = $priorityIds->toArray();

        $query = PengajuanKerjaSama::with([
            'getLembaga',
            'getPengusul:name,username',
            'getVerifikator:name,username',
            'getKabid:name,username',
            'getPenandatangan',
            'getUnreadChatCount'
        ])->where('tgl_selesai', '0000-00-00 00:00:00')
            ->FilterDokumen($request);

        $this->applyRoleFilter($query);

        // Prioritize specific MOU IDs
        if (!empty($priorityIdsArray)) {
            $quotedIds = array_map(fn($id) => "'" . addslashes($id) . "'", $priorityIdsArray);
            $priorityIdsString = implode(",", $quotedIds);
            $query->orderByRaw("FIELD(id_mou, $priorityIdsString) DESC");
        } else {
            $query->orderByDesc('created_at');
        }

        // $pengajuans = $query->limit(1)->get();
        $pengajuans = $query->get();
        // return $pengajuans;
        $row = 2;

        foreach ($pengajuans as $index => $p) {
            $data = [
                $index + 1,
                $p->id_mou,
                $p->nama_institusi,
                $p->dn_ln,
                $p->alamat_mitra,
                $p->dn_ln == 'Dalam Negeri' ? $p->wilayah_mitra : $p->negara_mitra,
                $p->jenis_kerjasama,
                $p->jenis_kerjasama_lain,
                $p->jenis_institusi,
                $p->jenis_institusi_lain,
                $p->rangking_univ,
                $p->lembaga,
                $p->nama_internal_pic,
                $p->lvl_internal_pic,
                $p->email_internal_pic,
                $p->telp_internal_pic,
                $p->nama_eksternal_pic,
                $p->lvl_eksternal_pic,
                $p->email_eksternal_pic,
                $p->telp_eksternal_pic,
                $p->ttd_internal,
                $p->lvl_internal,
                $p->ttd_eksternal,
                $p->lvl_eksternal,
                $p->kontribusi,
                $p->kontribusi_lain,
                $p->periode_kerma,
                $p->mulai,
                $p->selesai,
                strip_tags($p->status_pengajuan),
                strip_tags($p->status_verifikasi),
                $p->file_ajuan,
                $p->tgl_draft_upload,
                $p->tgl_verifikasi_kaprodi,
                $p->tgl_verifikasi_kabid,
                $p->tgl_verifikasi_user,
                $p->tgl_req_ttd,
                $p->status_mou,
                optional($p->getLembaga)->nama_lmbg ?? $p->lembaga,
                optional($p->getPengusul)->name,
                $p->input_kerma,
                $p->stats_kerma,
                $p->created_at ? $p->created_at->format('Y') : '-',
                $p->file_ajuan ? asset(getDocumentUrl($p->file_ajuan, 'file_ajuan')) : '-',
                $p->file_mou ? asset(getDocumentUrl($p->file_mou, 'file_mou')) : '-',
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
        $fileName = 'data_pengajuan.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
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

    public function notif_verifikator()
    {
        $tanggalNull = '0000-00-00 00:00:00';
        $role = session('current_role');
        $username = Auth::user()->username;
        $placeState = Auth::user()->place_state;

        $pengajuanQuery = PengajuanKerjaSama::where('tgl_selesai', $tanggalNull);

        $this->applyRoleFilter($pengajuanQuery, $role, $username, $placeState, $tanggalNull);
        $jumlahPengajuan = $pengajuanQuery->count();

        $jumlahImplementasi = 0;
        if ($role === 'admin') {
            $jumlahImplementasi = laporImplementasi::where('status_verifikasi', '0')->count();
        }

        $totalNotifikasi = $jumlahPengajuan + $jumlahImplementasi;

        return $jumlahPengajuan;
    }

    private function getStatusVerifikasiOptions(): string
    {
        $role = session('current_role');
        // $options = ['' => 'Pilih Status Verifikasi'];
        $options = [];

        if ($role === 'verifikator') {
            $options['Menunggu Verifikasi Kaprodi'] = 'Menunggu Verifikasi Kaprodi';
            $options['Proses Revisi Pengusul'] = 'Proses Revisi Pengusul';
        } else if ($role === 'admin') {
            $options['Proses Verifikasi Kaprodi'] = 'Proses Verifikasi Kaprodi';
            $options['Proses Revisi Pengusul'] = 'Proses Revisi Pengusul';
        } else if ($role === 'user') {
            $options['Proses Verifikasi Kaprodi'] = 'Proses Verifikasi Kaprodi';
            $options['Menunggu Revisi Pengusul'] = 'Menunggu Revisi Pengusul';
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
}
