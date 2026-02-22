<?php

namespace App\Http\Controllers;

use App\Models\AttendFakultas;
use App\Models\AttendProdi;
use App\Models\DokumenPendukungRecognition;
use App\Models\Kuesioner;
use App\Models\LogActivity;
use App\Models\PengajuanKerjaSama;
use App\Models\Recognition;
use App\Models\RefFakultasRecognition;
use App\Models\RefLembagaUMS;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RecognitionController extends Controller
{
    public function index()
    {
        $data = [
            'li_active' => 'dashboard',
            'title' => 'Program Rekognisi',
            'page_title' => 'Program Rekognisi',
            'ajuan_baru' => Recognition::where('timestamp_selesai', null)->count(),
            'ajuan_selesai' => Recognition::where('timestamp_selesai', '!=', null)->count(),
            'dokumenPendukung' => DokumenPendukungRecognition::where('is_active', 1)->get(),
        ];

        $data['notif_verifikator'] = 0;
        if (session('current_role') == 'verifikator') {
            $data['notif_verifikator'] = $this->notifikasiRekognisi();
        }

        return view('recognition/index', $data);
    }


    public function notifikasiRekognisi()
    {
        $username = Auth::user()->username;
        $placeState = Auth::user()->place_state;
        $notifCounts = Recognition::selectRaw("
        SUM(
            CASE 
                WHEN tbl_recognition.add_by = ?
                 AND tbl_recognition.status_verify_admin = 1
                 AND (tbl_recognition.file_sk IS NULL OR tbl_recognition.file_sk = '')
                THEN 1 ELSE 0 END
        ) AS user_notif,

        SUM(
            CASE 
                WHEN (tbl_recognition.status_verify_kaprodi IS NULL OR tbl_recognition.status_verify_kaprodi = 0) AND tbl_recognition.faculty = ?
                THEN 1 ELSE 0 END
        ) AS verifikator_notif,

        SUM(
            CASE 
                WHEN (tbl_recognition.status_verify_kaprodi = 1 AND 
                      (tbl_recognition.status_verify_admin IS NULL OR tbl_recognition.status_verify_admin = 0))
                THEN 1 ELSE 0 END
        ) +
        SUM(
            CASE 
                WHEN (tbl_recognition.status_verify_admin = 1 AND 
                      (tbl_recognition.file_sk IS NULL OR tbl_recognition.file_sk = ''))
                THEN 1 ELSE 0 END
        ) AS admin_notif
        ", [$username, $placeState])->first();

        if (session('current_role') == 'admin') {
            return (int) $notifCounts->admin_notif;
        } else if (session('current_role') == 'verifikator') {
            return  (int) $notifCounts->verifikator_notif;
        } else if (session('current_role') == 'user') {
            return (int) $notifCounts->user_notif;
        }
    }

    public function InboundStaffRecognition()
    {
        $filterRecognition = $this->getReferensiDokumen();
        $data = [
            'li_active' => 'recognition',
            'title' => 'Data Ajuan Rekrutmen Adjunct Professor',
            'page_title' => 'Data Ajuan Rekrutmen Adjunct Professor',
            'filterRecognition' => $filterRecognition
        ];

        return view('recognition/staff', $data);
    }

    public function getData(Request $request)
    {
        $role = session('current_role');
        $orderColumnIndex = $request->input('order.0.column');

        $query = Recognition::select('tbl_recognition.*', 'ref_lembaga_ums.nama_lmbg as nama_fakultas');
        $query->leftJoin('ref_lembaga_ums', 'ref_lembaga_ums.id_lmbg', '=', 'tbl_recognition.faculty');
        if ($request->id_fakultas != null && $request->id_fakultas == 'user') {
            $query->where('tbl_recognition.add_by', Auth::user()->username);
        } elseif ($request->id_fakultas != null) {
            $query->where('tbl_recognition.faculty', $request->id_fakultas);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('selesai', $request->tahun);
        }

        if ($orderColumnIndex == 0) {
            switch ($role) {
                case 'user':
                    $query->orderByRaw(
                        "
                        CASE 
                            WHEN tbl_recognition.add_by = ? AND tbl_recognition.bukti_pelaksanaan = '' THEN 0
                            WHEN tbl_recognition.add_by = ? AND tbl_recognition.status_verify_kaprodi = 0 THEN 1
                            WHEN tbl_recognition.add_by = ? AND tbl_recognition.status_verify_admin = 0 THEN 1
                            WHEN tbl_recognition.status_verify_kaprodi IS NULL OR tbl_recognition.status_verify_kaprodi = 0 THEN 2
                            WHEN tbl_recognition.status_verify_admin IS NULL OR tbl_recognition.status_verify_admin = 0 THEN 3 
                            WHEN tbl_recognition.add_by = ? THEN 4
                            ELSE 3 
                        END ASC",
                        [
                            Auth::user()->username,
                            Auth::user()->username,
                            Auth::user()->username,
                            Auth::user()->username
                        ]
                    );
                    break;

                case 'verifikator':
                    $query->orderByRaw("
                        CASE 
                            WHEN tbl_recognition.status_verify_kaprodi IS NULL OR tbl_recognition.status_verify_kaprodi = 0 THEN 0 
                            ELSE 1 
                        END ASC
                    ")->orderBy('tbl_recognition.id_rec', 'DESC');
                    break;

                case 'admin':
                    $query->orderByRaw("
                        CASE 
                            WHEN (tbl_recognition.status_verify_kaprodi = 1 AND (tbl_recognition.status_verify_admin IS NULL OR tbl_recognition.status_verify_admin = 0)) THEN 0
                            WHEN (tbl_recognition.status_verify_admin = 1 AND (tbl_recognition.file_sk IS NULL OR tbl_recognition.file_sk = '')) THEN 1
                            ELSE 2
                        END ASC
                    ")->orderBy('tbl_recognition.id_rec', 'DESC');
                    break;

                default:
                    $query->orderBy('tbl_recognition.id_rec', 'DESC');
                    break;
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('status_label', function ($row) {
                return $row->status_label;
            })
            ->addColumn('acceptance_form', function ($row) {
                return $row->acceptance_form_label;
            })
            ->addColumn('cv_prof', function ($row) {
                return $row->cv_prof_label;
            })
            ->addColumn('file_sk', function ($row) {
                return $row->file_sk_label;
            })
            ->addColumn('bukti_pelaksanaan', function ($row) {
                return $row->bukti_pelaksanaan_label;
            })
            ->addColumn('action', function ($row) {
                return $row->action_label;
            })
            ->addColumn('timestamp_selesai', function ($row) {
                return $row->timestamp_selesai_label;
            })
            ->addColumn('timestamp_ajuan', function ($row) {
                return $row->timestamp_ajuan_label;
            })
            ->addColumn('tanggal_sk', function ($row) {
                return $row->tanggal_sk_label;
            })
            ->rawColumns(['action', 'tanggal_sk', 'status_label', 'acceptance_form', 'cv_prof', 'file_sk', 'bukti_pelaksanaan', 'timestamp_ajuan', 'timestamp_selesai'])
            ->make(true);
    }

    public function tambah()
    {
        // return $this->sinkronLembaga();
        $idLmbgFakultas = RefLembagaUMS::where('nama_lmbg', Auth::user()->status_tempat)->value('id_lmbg');
        if ($idLmbgFakultas == null) {
            $idLmbgFakultas = RefLembagaUMS::where('nama_lmbg_old', Auth::user()->status_tempat)->value('id_lmbg');
        }
        $fakultasUser = Auth::user()->place_state == 'lmbg1001' ? $idLmbgFakultas : Auth::user()->status_tempat;

        $data = [
            'li_active' => 'recognition',
            'li_sub_active' => 'data_ajuan',
            'title' => 'Buat Ajuan Rekognisi',
            'page_title' => 'Buat Ajuan Rekognisi',
            'prodiUser' => Auth::user()->status_tempat,
            'fakultasUser' => $fakultasUser,
            'fakultas' => RefLembagaUMS::where('jenis_lmbg', 'Fakultas')->get(),
            'program_studi' => RefLembagaUMS::where('jenis_lmbg', 'Program Studi')->get(),
        ];
        session(['rekognisi_key' => Str::random(40)]);
        $data['hasVerify'] = false;

        $data['logFileAF'] = [];
        $data['logFileCV'] = [];
        $data['logFileSK'] = [];
        $data['logFileBP'] = [];

        return view('recognition/tambah', $data);
    }

    // public function sinkronLembaga()
    // {
    //     $recognition = Recognition::all();
    //     $ProgramStudi = AttendProdi::all();

    //     foreach ($recognition as $key => $value) {
    //         $fakultas = AttendFakultas::where('id', $value->faculty)->first();
    //         $prodi = AttendProdi::where('depart', $value->department)->first();
    //         $ProdiLembaga = RefLembagaUMS::where('id_lmbg', $prodi->id_lmbg)->first();

    //         $dataUpdate = [
    //             'faculty' => $fakultas->id_lmbg,
    //             'department' => $ProdiLembaga->nama_lmbg
    //         ];
    //         $value->update($dataUpdate);
    //     }
    // }

    public function edit($id_rec)
    {
        $rec = Recognition::where('id_rec', $id_rec)->first();
        $data = [
            'li_active' => 'recognition',
            'li_sub_active' => 'data_ajuan',
            'title' => 'Edit Ajuan',
            'page_title' => 'Edit Ajuan',
            'dataRecognisi' => $rec,
            'id_rec' => $id_rec,
            'fakultas' => RefLembagaUMS::where('jenis_lmbg', 'Fakultas')->get(),
            'program_studi' => RefLembagaUMS::where('jenis_lmbg', 'Program Studi')->get(),
        ];
        session(['rekognisi_key' => Str::random(40)]);

        $data['hasVerify'] = false;
        if ($rec->status_verify_admin == 1) {
            $data['hasVerify'] = true;
        }
        $data['logFileAF'] = getLogFile($id_rec, 'acceptance_form');
        $data['logFileCV'] = getLogFile($id_rec, 'cv_prof');
        $data['logFileSK'] = getLogFile($id_rec, 'file_sk');
        $data['logFileBP'] = getLogFile($id_rec, 'bukti_pelaksanaan');

        return view('recognition/tambah', $data);
    }

    public function store(Request $request)
    {
        if ($request->rekognisi_key !== session('rekognisi_key') || session('rekognisi_key') == null) {
            return response()->json(['error' => 'Form tidak valid, silakan reload tab terbaru.'], 403);
        }

        if ($request->id_rec) {
            $dataExist = Recognition::where('id_rec', $request->id_rec)->firstOrFail();
        }

        $id_rec = $request->id_rec ?? str_replace('-', '', Str::uuid());
        $dataExist = Recognition::where('id_rec', $id_rec)->first();
        if (!empty($dataExist) && $dataExist->status_verify_admin == 1) {
            $validator = Validator::make(
                $request->all(),
                [
                    'file_sk' => 'nullable|mimes:pdf|max:5120',
                    'bukti_pelaksanaan' => 'nullable|mimes:pdf|max:5120',
                ],
                [
                    'file_sk.max' => 'File SK maksimal 5MB.',
                    'file_sk.mimes' => 'File SK harus berformat PDF.',

                    'bukti_pelaksanaan.max' => 'Bukti Pelaksanaan maksimal 5MB.',
                    'bukti_pelaksanaan.mimes' => 'Bukti Pelaksanaan harus berformat PDF.',
                ],
            );
        } else {
            $validator = Validator::make(
                $request->all(),
                [
                    'faculty' => 'required',
                    'departement' => 'nullable',
                    'nama_prof' => 'required|string|max:255',
                    'univ_asal' => 'required|string|max:255',
                    'bidang_kepakaran' => 'required|string|max:255',
                    'acceptance_form' => 'nullable|mimes:pdf|max:5120',
                    'mulai' => 'required',
                    'selesai' => 'required',
                    'cv_prof' => 'nullable|mimes:pdf|max:5120',
                ],
                [
                    'faculty.required' => 'Fakultas harus diisi.',
                    // 'departement.required' => 'Program Studi harus diisi.',
                    'nama_prof.required' => 'Nama Professor harus diisi.',
                    'nama_prof.max' => 'Nama Professor maksimal 255 karakter.',
                    'univ_asal.required' => 'Asal Universitas harus diisi.',
                    'univ_asal.max' => 'Asal Universitas maksimal 255 karakter.',
                    'bidang_kepakaran.required' => 'Bidang Kepakaran harus diisi.',
                    'bidang_kepakaran.max' => 'Bidang Kepakaran maksimal 255 karakter.',
                    'mulai.required' => 'Tanggal Mulai SK harus diisi.',
                    'selesai.required' => 'Tanggal Selesai SK harus diisi.',

                    'acceptance_form.max' => 'File Acceptance Form maksimal 5MB.',
                    'cv_prof.max' => 'File CV Professor maksimal 5MB.',
                    'acceptance_form.mimes' => 'File Acceptance Form harus berformat PDF.',
                    'cv_prof.mimes' => 'File CV Professor harus berformat PDF.',
                ],
            );
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $allFiles = [];

            if (!empty($dataExist) && $dataExist->status_verify_admin == '1') {
                $dataInsert = [];
                if ($request->hasFile('file_sk')) {
                    $file_sk = $request->file('file_sk');
                    $path = "uploads/recognition/file_sk";
                    $fileSK = $this->upload_file($file_sk, $path);
                    $dataInsert['file_sk'] = $fileSK;

                    $dtFiles = [
                        'jenis' => 'file_sk',
                        'path' => $fileSK
                    ];

                    $allFiles[] = $dtFiles;
                } else {
                    if (!empty($dataExist) && $dataExist->file_sk != null && $dataExist->file_sk != '') {
                    } else {
                        $dataInsert['file_sk'] = '';
                    }
                }

                if ($request->hasFile('bukti_pelaksanaan')) {
                    $bukti_pelaksanaan = $request->file('bukti_pelaksanaan');
                    $path = "uploads/recognition/bukti_pelaksanaan";
                    $fileBukti = $this->upload_file($bukti_pelaksanaan, $path);
                    $dataInsert['bukti_pelaksanaan'] = $fileBukti;

                    $dtFiles = [
                        'jenis' => 'bukti_pelaksanaan',
                        'path' => $fileBukti
                    ];

                    $allFiles[] = $dtFiles;
                } else {
                    if (!empty($dataExist) && $dataExist->bukti_pelaksanaan != null && $dataExist->bukti_pelaksanaan != '') {
                    } else {
                        $dataInsert['bukti_pelaksanaan'] = '';
                    }
                }
            } else {
                $id_department = RefLembagaUMS::where('nama_lmbg', $request->departement)->first()->id_lmbg;

                $dataInsert = [
                    'faculty' => $request->faculty,
                    'id_department' => $id_department,
                    'department' => $request->departement,
                    'nama_prof' => strip_tags($request->nama_prof),
                    'univ_asal' => strip_tags($request->univ_asal),
                    'bidang_kepakaran' => strip_tags($request->bidang_kepakaran),

                    'mulai' => $request->mulai,
                    'selesai' => $request->selesai,
                ];


                // Simpan file Acceptance Form
                if ($request->hasFile('acceptance_form')) {
                    $acceptance_form = $request->file('acceptance_form');
                    $path = "uploads/recognition/acceptance_form";
                    $fileAcceptanceForm = $this->upload_file($acceptance_form, $path);
                    $dataInsert['acceptance_form'] = $fileAcceptanceForm;

                    $dtFiles = [
                        'jenis' => 'acceptance_form',
                        'path' => $fileAcceptanceForm
                    ];

                    $allFiles[] = $dtFiles;
                } else {
                    if (!empty($dataExist) && $dataExist->acceptance_form != null && $dataExist->acceptance_form != '') {
                    } else {
                        $dataInsert['acceptance_form'] = '';
                    }
                }

                // Simpan file CV Professor
                if ($request->hasFile('cv_prof')) {
                    $cv_prof = $request->file('cv_prof');
                    $path = "uploads/recognition/cv_prof";
                    $fileCVProf = $this->upload_file($cv_prof, $path);
                    $dataInsert['cv_prof'] = $fileCVProf;

                    $dtFiles = [
                        'jenis' => 'cv_prof',
                        'path' => $fileCVProf
                    ];

                    $allFiles[] = $dtFiles;
                } else {
                    if (!empty($dataExist) && $dataExist->cv_prof != null && $dataExist->cv_prof != '') {
                    } else {
                        $dataInsert['cv_prof'] = '';
                    }
                }


                if (session('current_role') == 'verifikator') {
                    $dataInsert['date_verify_kaprodi'] = date('Y-m-d H:i:s');
                    $dataInsert['status_verify_kaprodi'] = '1';
                    $dataInsert['verify_kaprodi_by'] = auth()->user()->username;
                }
                if (session('current_role') == 'admin') {
                    $dataInsert['date_verify_kaprodi'] = date('Y-m-d H:i:s');
                    $dataInsert['status_verify_kaprodi'] = '1';
                    $dataInsert['verify_kaprodi_by'] = auth()->user()->username;

                    $dataInsert['date_verify_admin'] = date('Y-m-d H:i:s');
                    $dataInsert['status_verify_admin'] = '1';
                    $dataInsert['verify_admin_by'] = auth()->user()->username;
                }

                // Simpan atau update data
                if ($request->id_rec) {
                    if ($dataExist->status_verify_kaprodi == '0') {
                        $dataInsert['status_verify_kaprodi'] = null;
                        $dataInsert['date_verify_kaprodi'] = null;
                        $dataInsert['verify_kaprodi_by'] = null;
                        $dataInsert['revisi_kaprodi'] = null;
                    }

                    if ($dataExist->status_verify_admin == '0') {
                        $dataInsert['status_verify_admin'] = null;
                        $dataInsert['date_verify_admin'] = null;
                        $dataInsert['verify_admin_by'] = null;
                        $dataInsert['revisi_admin'] = null;
                    }
                    $tipe = "edit";
                } else {
                    $tipe = "submit";
                    $dataInsert['add_by'] = Auth::user()->username;
                    $dataInsert['timestamp_ajuan'] = Carbon::now();
                    $dataInsert['file_sk'] = '';
                    $dataInsert['bukti_pelaksanaan'] = '';
                    $dataInsert['id_rec'] = $id_rec;
                }
            }

            if ($request->id_rec) {
                $insert = Recognition::where('id_rec', $id_rec)->update($dataInsert);
                $dataLogketerangan = 'Update';
            } else {
                $insert = Recognition::create($dataInsert);
                $dataLogketerangan = 'Baru';
            }


            if ($insert) {
                // $dataEmail = Recognition::where('id_rec', $id_rec)->first()->toArray();
                foreach ($allFiles as $key => $value) {
                    $dataLog = [
                        'table' => 'tbl_recognition',
                        'id_table' => $id_rec,
                        'jenis' => $value['jenis'],
                        'path' => $value['path'],
                        'keterangan' => $dataLogketerangan,
                        'add_by' => Auth::user()->username,
                        'role' => session('current_role')
                    ];
                    LogActivity::create($dataLog);
                }

                DB::commit();
                session()->forget('rekognisi_key');
                return response()->json(['status' => true, 'message' => 'Data berhasil disimpan', 'route' => route('recognition.dataAjuanSaya')], 200);
            }
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan ketika Menyimpan '], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function showRevisi(Request $request)
    {
        $id_rec = $request->id_rec;
        $field = $request->field;
        $dataRecognition = Recognition::where('id_rec', $id_rec)->firstOrFail();

        $urlEdit = route('recognition.edit', ['id_rec' => $dataRecognition->id_rec]);
        return response()->json(view('recognition/showRevisi', ['dataRecognition' => $dataRecognition, 'urlEdit' => $urlEdit, 'catatan' => $dataRecognition->{$field}])->render());
    }

    public function getDetailRecognition(Request $request)
    {
        $dataRecognition = Recognition::select('tbl_recognition.*', 'users.name as nama_pengusul')
            ->leftJoin('users', 'users.username', '=', 'tbl_recognition.add_by')->where('tbl_recognition.id_rec', $request->id_rec)->first();

        $pathAF = getDocumentUrl($dataRecognition->acceptance_form, 'file_rekognisi');
        $pathCV = getDocumentUrl($dataRecognition->cv_prof, 'file_rekognisi');
        $pathSK = getDocumentUrl($dataRecognition->file_sk, 'file_sk');
        $pathBP = getDocumentUrl($dataRecognition->bukti_pelaksanaan, 'file_rekognisi');
        if (str_starts_with($pathAF, 'http')) {
            $fileUrlAF = 'https://docs.google.com/gview?url=' . $pathAF . '&embedded=true';
        } else {
            $fileUrlAF = $pathAF;
        }
        if (str_starts_with($pathCV, 'http')) {
            $fileUrlCV = 'https://docs.google.com/gview?url=' . $pathCV . '&embedded=true';
        } else {
            $fileUrlCV = $pathCV;
        }
        if (str_starts_with($pathSK, 'http')) {
            $fileUrlSK = 'https://docs.google.com/gview?url=' . $pathSK . '&embedded=true';
        } else {
            $fileUrlSK = $pathSK;
        }
        if (str_starts_with($pathBP, 'http')) {
            $fileUrlBP = 'https://docs.google.com/gview?url=' . $pathBP . '&embedded=true';
        } else {
            $fileUrlBP = $pathBP;
        }


        // $fileUrl = getDocumentUrl($dataRecognition->file_mou, 'file_mou');
        $data = [
            'dataRecognition' => $dataRecognition,
            'fileUrlAF' => @$fileUrlAF,
            'fileUrlCV' => @$fileUrlCV,
            'fileUrlSK' => @$fileUrlSK,
            'fileUrlBP' => @$fileUrlBP,
            // 'fileUrl' => @$fileUrl
        ];

        // dd($data);

        $view = view('recognition/detail_data', $data);
        return response()->json(['html' => $view->render()], 200);
    }

    public function verifikasi(Request $request)
    {
        DB::beginTransaction();
        try {
            $tipe = $request->tipe;

            $dataInsert = [];
            // $dataInsert['revisi'] = $request->revisi;
            if ($tipe == 'kaprodi') {
                $dataInsert['revisi_kaprodi'] = $request->revisi;
                $dataInsert['status_verify_kaprodi'] = $request->status;
                $dataInsert['date_verify_kaprodi'] = date('Y-m-d H:i:s');
                $dataInsert['verify_kaprodi_by'] = Auth::user()->username ?? '';
            } else {
                $dataInsert['revisi_admin'] = $request->revisi;
                $dataInsert['status_verify_admin'] = $request->status;
                $dataInsert['date_verify_admin'] = date('Y-m-d H:i:s');
                $dataInsert['verify_admin_by'] = Auth::user()->username ?? '';
            }

            if ($request->status == '1' && $tipe == 'admin') {
                $dataInsert['timestamp_selesai'] = Carbon::now();
            }

            $partner = Recognition::where('id_rec', $request->id_rec)->firstOrFail();
            $partner->update($dataInsert);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data Berhasil di Simpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }


    public function dataAjuanSaya()
    {
        $filterRecognition = $this->getReferensiDokumen();
        $data = [
            'li_active' => 'ajuan_saya',
            'title' => 'Data Ajuan Adjunct Professor',
            'page_title' => 'Data Ajuan Adjunct Professor',
            'filterRecognition' => $filterRecognition
        ];

        return view('recognition/AjuanSaya', $data);
    }

    public function dataAjuan()
    {
        $data = [
            'li_active' => 'data_ajuan',
            'title' => 'Data Ajuan Adjunct Professor',
            'page_title' => 'Data Ajuan Adjunct Professor',
        ];

        return view('recognition/dataAjuan', $data);
    }

    public function getDataAjuan(Request $request)
    {
        $query = DB::table('tbl_recognition')->select(
            'ref_lembaga_ums.id_lmbg',
            'ref_lembaga_ums.nama_lmbg as faculty',
            DB::raw("SUM(CASE WHEN (tbl_recognition.status_verify_kaprodi IS NULL OR tbl_recognition.status_verify_kaprodi = 0) THEN 1 ELSE 0 END) AS ajuan_masuk"),
            DB::raw("COUNT(CASE WHEN tbl_recognition.status_verify_kaprodi = 1 THEN 1 END) AS ajuan_selesai"),
            DB::raw("COUNT(CASE WHEN (tbl_recognition.status_verify_kaprodi IS NULL OR tbl_recognition.status_verify_kaprodi = 0) AND tbl_recognition.faculty = ? THEN 1 END) AS unread_verifikator"),
            DB::raw("COUNT(CASE WHEN (tbl_recognition.status_verify_admin IS NULL OR tbl_recognition.status_verify_admin = 0) THEN 1 END) AS unread_admin"),
        )
            ->addBinding(Auth::user()->place_state, 'select')
            ->join('ref_lembaga_ums', 'ref_lembaga_ums.id_lmbg', '=', 'tbl_recognition.faculty');

        if ($request->filled('tahun')) {
            $query->whereYear('selesai', $request->tahun);
        }

        $query->groupBy('ref_lembaga_ums.id_lmbg', 'ref_lembaga_ums.nama_lmbg')
            ->havingRaw('ajuan_masuk > 0 OR ajuan_selesai > 0');

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($search = $request->input('search.value')) {
                    $query->where('ref_lembaga_ums.nama_lmbg', 'like', "%{$search}%");
                }
            })
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $unreadCount = '';
                if (session('current_role') == 'admin') {
                    $unreadCount = $row->unread_admin;
                } elseif (session('current_role') == 'verifikator') {
                    $unreadCount = $row->unread_verifikator;
                }

                $notifBadge = $unreadCount > 0
                    ? '<span class="position-absolute top-0 start-80 translate-middle badge rounded-pill bg-danger" style="z-index:1;">'
                    . $unreadCount . '</span>'
                    : '';

                return '<a href="' . route('recognition.detailDataAjuan', ['id_fak' => $row->id_lmbg]) . '"
                        class="btn btn-secondary position-relative">
                        <i class="fa-solid fa-building-columns"></i>' . $notifBadge . '
                    </a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function detailDataAjuan($id_fak)
    {
        $filterRecognition = $this->getReferensiDokumen();
        $fakultas = RefLembagaUMS::findorfail($id_fak);
        $data = [
            'li_active' => 'data_ajuan',
            'title' => 'Detail Ajuan ' . $fakultas->nama_lmbg,
            'page_title' => 'Detail Ajuan ' . $fakultas->nama_lmbg,
            'id_fak' => $id_fak,
            'filterRecognition' => $filterRecognition
        ];

        return view('recognition/detailDataAjuan', $data);
    }

    public function dokumenPendukungRecognition()
    {
        $role = session('current_role');
        $data = [
            'li_active' => 'dokumen_pendukung_rekognisi',
            'title' => 'Dokumen Pendukung Rekognisi',
            'page_title' => 'Dokumen Pendukung Rekognisi',
        ];
        if ($role == 'admin') {
            $data['dokumenPendukung'] = DokumenPendukungRecognition::all();
        } else {
            $data['dokumenPendukung'] = DokumenPendukungRecognition::where('is_active', '1')->get();
        }

        return view('recognition/dokumenPendukung', $data);
    }

    public function loadAllDokumenIframe()
    {
        try {
            $role = session('current_role');

            if ($role == 'admin') {
                $dokumenList = DokumenPendukungRecognition::all();
            } else {
                $dokumenList = DokumenPendukungRecognition::where('is_active', '1')->get();
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

    public function setDokumen(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataUpdate = [
                'is_active' => $request->is_active
            ];
            $dokumen = DokumenPendukungRecognition::where('id', $request->id)->firstOrFail();
            $dokumen->update($dataUpdate);
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
            $cekDokumen = DokumenPendukungRecognition::where('uuid', $uuid)->first();

            $dataInsert = [
                'nama_dokumen' => $request->nama_dokumen,
                'file_dokumen' =>  $cekDokumen->file_dokumen ?? null,
                'link_dokumen' => null
            ];

            // Simpan file SK Mitra
            if ($request->hasFile('file_dokumen')) {
                $file_dokumen = $request->file('file_dokumen');
                $path = "uploads/dokumen_pendukung_recognition";
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
                DokumenPendukungRecognition::create($dataInsert);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data berhasil disimpan'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function hapus_fileDokumenPendukung(Request $request)
    {
        DB::beginTransaction();
        try {
            $cekDokumen = DokumenPendukungRecognition::where('uuid', $request->uuid)->firstOrFail();
            if ($cekDokumen && $cekDokumen->file_dokumen) {
                Storage::disk('public')->delete($cekDokumen->file_dokumen);
            }
            $cekDokumen->update(['file_dokumen' => null]);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'File Berhasil di Hapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroyDokumenPendukung(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = DokumenPendukungRecognition::where('uuid', $request->uuid)->firstOrFail();
            $user->delete();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Dokumen Berhasil di Hapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }


    public function laporKegiatan()
    {
        $data = [
            'li_active' => 'recognition',
            'li_sub_active' => 'lapor_kegiatan_lampau',
            'title' => 'Lapor Kegiatan Rekognisi',
            'page_title' => 'Lapor Kegiatan Rekognisi',
        ];

        return view('recognition/dataAjuan', $data);
    }

    public function getDatalaporKegiatan(Request $request)
    {

        $query = Recognition::select(
            'ref_lembaga_ums.id_lmbg',
            'ref_lembaga_ums.nama_lmbg as faculty',
            DB::raw("COUNT(CASE WHEN tbl_recognition.timestamp_selesai IS NULL THEN 1 END) AS ajuan_masuk"),
            DB::raw("COUNT(CASE WHEN tbl_recognition.timestamp_selesai IS NOT NULL THEN 1 END) AS ajuan_selesai")
        )
            ->join('ref_lembaga_ums', 'ref_lembaga_ums.id_lmbg', '=', 'tbl_recognition.faculty')
            ->groupBy('ref_lembaga_ums.id_lmbg', 'ref_lembaga_ums.nama_lmbg')
            ->havingRaw('ajuan_masuk > 0 OR ajuan_selesai > 0')
            ->get();


        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '';

                $action .= '<div class="btn-group" role="group">';

                $action .= '<a href="' . route('recognition.detailDataAjuan', ['id_fak' => $row->id]) . '" data-title-tooltip="Detail Ajuan Fakultas" class="btn btn-secondary position-relative">
                                <i class="fa-solid fa-building-columns"></i>
                            </a>';

                return $action;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function tambahLaporKegiatan()
    {
        $data = [
            'li_active' => 'data_ajuan',
            'li_sub_active' => 'lapor_kegiatan_lampau',
            'title' => 'Tambah Rekognisi',
            'page_title' => 'Tambah Rekognisi',
        ];

        return view('recognition/tambah', $data);
    }
    public function editLaporKegiatan()
    {
        $data = [
            'li_active' => 'data_ajuan',
            'li_sub_active' => 'lapor_kegiatan_lampau',
            'title' => 'Tambah Rekognisi',
            'page_title' => 'Tambah Rekognisi',
        ];

        return view('recognition/tambah', $data);
    }

    public function storeLaporKegiatan(Request $request) {}

    public function hapusLaporKegiatan(Request $request) {}

    public function uploadFileRecognition(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'file' => 'required|file|mimes:pdf|max:5120',
                'id_rec' => 'required|string',
                'flag' => 'required|in:file_sk,cv_prof,acceptance_form,bukti_pelaksanaan'
            ],
            [
                'file.required' => 'Tidak ada file yang diunggah.',
                'file.file' => 'File yang diunggah tidak valid.',
                'file.max' => 'Ukuran file maksimal adalah 5MB.',
                'file.mimes' => 'File harus Berformat PDF.',
                'id_rec.required' => 'ID rekognisi tidak boleh kosong.',
                'id_rec.string' => 'ID rekognisi harus berupa teks.',
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
            $dataExist = Recognition::where('id_rec', $request->id_rec)->firstOrFail();

            if ($request->hasFile('file')) {
                $flag = $request->file('file');
                $path = "uploads/recognition/{$request->flag}";
                $fileUpload = $this->upload_file($flag, $path);
                $dataInsert[$request->flag] = $fileUpload;
            } else {
                if (!empty($dataExist) && $dataExist->{$request->flag} != null && $dataExist->{$request->flag} != '') {
                } else {
                    $dataInsert['{$request->flag}'] = '';
                }
            }

            $insert = Recognition::where('id_rec', $request->id_rec)->update($dataInsert);

            if ($insert) {
                $dataLog = [
                    'table' => 'tbl_recognition',
                    'id_table' => $request->id_rec,
                    'jenis' => $request->flag,
                    'path' => $path,
                    'keterangan' => 'Baru',
                    'add_by' => Auth::user()->username,
                    'role' => session('current_role')
                ];
                LogActivity::create($dataLog);

                DB::commit();
                return response()->json(['status' => true, 'message' => 'Data berhasil disimpan'], 200);
            } else {
                return response()->json(['status' => false, 'error' => 'Terjadi kesalahan'], 500);
            }
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

    public function download_excel(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $headers = [
            'No',
            'ID Recognition',
            'Fakultas (ID)',
            'Nama Fakultas',
            'Program Studi / Departemen',
            'Nama Professor',
            'Universitas Asal',
            'Bidang Kepakaran',
            'File Acceptance Form',
            'File CV Professor',
            'File SK',
            'Bukti Pelaksanaan',
            'Tanggal Ajuan',
            'Tanggal Selesai',
            'Status Verifikasi Kaprodi',
            'Tanggal Verifikasi Kaprodi',
            'Verifikator Kaprodi',
            'Revisi Kaprodi',
            'Status Verifikasi Admin',
            'Tanggal Verifikasi Admin',
            'Verifikator Admin',
            'Revisi Admin',
            'Dibuat Oleh',
            'Tanggal Dibuat',
            'Tanggal Diperbarui',
        ];

        // Set header ke baris 1
        foreach ($headers as $i => $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($columnLetter . '1', $header);
        }
        // Data

        $role = session('current_role');
        $query = Recognition::select('tbl_recognition.*', 'ref_lembaga_ums.nama_lmbg as nama_fakultas');
        $query->leftJoin('ref_lembaga_ums', 'ref_lembaga_ums.id_lmbg', '=', 'tbl_recognition.faculty');
        if ($request->id_fakultas != null && $request->id_fakultas == 'user') {
            $query->where('tbl_recognition.add_by', Auth::user()->username);
        } elseif ($request->id_fakultas != null) {
            $query->where('tbl_recognition.faculty', $request->id_fakultas);
        }

        switch ($role) {
            case 'user':
                $query->orderByRaw(
                    "
            CASE 
                WHEN tbl_recognition.add_by = ? THEN 0 
                ELSE 1 
            END ASC",
                    [Auth::user()->username]
                );
                break;

            case 'verifikator':
                $query->orderByRaw("
            CASE 
                WHEN tbl_recognition.status_verify_kaprodi IS NULL OR tbl_recognition.status_verify_kaprodi = 0 THEN 0 
                ELSE 1 
            END ASC
        ")->orderBy('tbl_recognition.id_rec', 'DESC');
                break;

            case 'admin':
                $query->orderByRaw("
            CASE 
                WHEN (tbl_recognition.status_verify_kaprodi = 1 AND (tbl_recognition.status_verify_admin IS NULL OR tbl_recognition.status_verify_admin = 0)) THEN 0
                WHEN (tbl_recognition.status_verify_admin = 1 AND (tbl_recognition.file_sk IS NULL OR tbl_recognition.file_sk = '')) THEN 1
                ELSE 2
            END ASC
        ")->orderBy('tbl_recognition.id_rec', 'DESC');
                break;

            default:
                $query->orderBy('tbl_recognition.id_rec', 'DESC');
                break;
        }

        $rekognisi = $query->get();
        // $rekognisi = $query->limit(1)->get();
        $row = 2;

        foreach ($rekognisi as $index => $r) {
            $data = [
                $index + 1,
                $r->id_rec,
                $r->faculty,
                $r->nama_fakultas,
                $r->department,
                $r->nama_prof,
                $r->univ_asal,
                $r->bidang_kepakaran,
                $r->acceptance_form ? asset(getDocumentUrl($r->acceptance_form, 'file_rekognisi')) : '-',
                $r->cv_prof ? asset(getDocumentUrl($r->cv_prof, 'file_rekognisi')) : '-',
                $r->file_sk ? asset(getDocumentUrl($r->file_sk, 'file_sk')) : '-',
                $r->bukti_pelaksanaan ? asset(getDocumentUrl($r->bukti_pelaksanaan, 'buktipelaksanaan_rekognisi')) : '-',
                $r->timestamp_ajuan,
                $r->timestamp_selesai,
                $r->status_verify_kaprodi === 1 ? 'Terverifikasi' : 'Belum',
                $r->date_verify_kaprodi,
                $r->verify_kaprodi_by,
                $r->revisi_kaprodi,
                $r->status_verify_admin === 1 ? 'Terverifikasi' : 'Belum',
                $r->date_verify_admin,
                $r->verify_admin_by,
                $r->revisi_admin,
                $r->add_by,
                $r->created_at,
                $r->updated_at,
            ];

            foreach ($data as $i => $value) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);

                // Jika kolom file dengan link (kolom 8–11 = acceptance, cv, sk, bukti pelaksanaan)
                if (in_array($i, [8, 9, 10, 11]) && $value !== '-') {
                    $sheet->setCellValue($columnLetter . $row, 'Unduh File');
                    $sheet->getCell($columnLetter . $row)->getHyperlink()->setUrl($value);
                } else {
                    $sheet->setCellValueExplicit($columnLetter . $row, $value ?? '-', DataType::TYPE_STRING);
                }
            }

            $row++;
        }


        // Simpan file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'data_rekognisi ' . Carbon::now() . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    private function getReferensiDokumen()
    {
        $tahun = Recognition::selectRaw('YEAR(mulai) as tahun')
            ->whereNotNull("mulai")
            ->distinct()->orderBy('tahun')->pluck('tahun')->toArray();
        $filter = [
            'tahun' => $this->buildOptions($tahun, 'Pilih Tahun'),
        ];

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
}
