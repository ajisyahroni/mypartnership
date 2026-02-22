<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailMailer;
use App\Jobs\SendEmailVerifikasiImplementasi;
use App\Jobs\SendPICNotificationEmail;
use App\Mail\PICNotification;
use App\Models\DokumenPendukung;
use App\Models\EmailLog;
use App\Models\Kuesioner;
use App\Models\laporImplementasi;
use App\Models\LogActivity;
use App\Models\MailMessages;
use App\Models\MailSetting;
use App\Models\PengajuanKerjaSama;
use App\Models\RefBentukKerjaSama;
use App\Models\RefJenisInstitusiMitra;
use App\Models\RefKategoriImplementasi;
use App\Models\RefLembagaUMS;
use App\Models\RefTingkatKerjaSama;
use App\Models\User;
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

ini_set('memory_limit', '512M'); // atau 1G


class ImplementasiController extends Controller
{
    public function index()
    {

        $data = [
            'li_active' => 'kerja_sama',
            'li_sub_active' => 'lapor_implementasi',
            'title' => 'Lapor Implementasi',
            'page_title' => 'Lapor Implementasi',
        ];
        if (!empty(session('sendEmailData'))) {
            $data['sendEmailData'] = session('sendEmailData');
        }

        if (in_array(session('current_role'), ['user', 'verifikator'])) {
            $data['li_active'] = 'lapor_implementasi';
        }


        return view('implementasi/index', $data);
    }

    public function getData(Request $request)
    {
        $orderColumnIndex = $request->input('order.0.column');

        $query = laporImplementasi::select('kerma_evaluasi.*', 'kerma_db.nama_institusi as nama_institusi', 'kerma_db.file_mou')
            ->with(['getPost', 'getLembaga', 'dataPengajuan'])
            ->join('kerma_db', 'kerma_db.id_mou', '=', 'kerma_evaluasi.id_mou');
        $query->FilterDokumen($request);

        // Filter by search
        if ($request->has('search') && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->whereHas('dataPengajuan', function ($q) use ($search) {
                $q->where('nama_institusi', 'like', "%{$search}%");
            });
            $query->orwhere('kerma_evaluasi.judul', 'like', "%{$search}%");
            $query->orwhere('kerma_evaluasi.judul_lain', 'like', "%{$search}%");
            $query->orwhere('kerma_evaluasi.bentuk_kegiatan', 'like', "%{$search}%");
        }

        if ($orderColumnIndex == 0) {
            $query->orderByRaw("kerma_evaluasi.postby = ? DESC", [Auth::user()->username]);
            // if (session('current_role') == 'admin') {
            $query->orderByRaw("kerma_evaluasi.status_verifikasi = ? DESC", '0');
            // }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('pelapor', function ($row) {
                return $row->getPost->name ?? '';
            })
            ->addColumn('judul', function ($row) {
                return $row->judul != 'Lain-lain' ? $row->judul : $row->judul_lain;
            })
            ->addColumn('nama_institusi', function ($row) {
                return $row->dataPengajuan->nama_institusi ?? '';
            })
            ->addColumn('tingkat_kerjasama', function ($row) {
                return $row->dataPengajuan->dn_ln == 'Dalam Negeri' ? $row->dataPengajuan->dn_ln . ' ' . $row->dataPengajuan->wilayah_mitra : $row->dataPengajuan->dn_ln . ' ' . $row->dataPengajuan->negara_mitra;
            })
            ->addColumn('bukti_pelaksanaan', function ($row) {
                // $action = '';
                // if ($row->file_imp != null && $row->file_imp != '') {
                //     $action = '<a href="' . getDocumentUrl($row->file_imp, 'file_imp') . '" class="btn btn-primary" target="_blank" data-title-tooltip="Download Bukti Pelaksanaan"><i class="bx bxs-download"></i></a>';
                // } else {
                //     $action .= '<form id="uploadForm-' . $row->file_imp . '-file_imp" enctype="multipart/form-data" style="display: inline;">
                //             <input type="file" name="file" style="display:none;" 
                //                 onchange="uploadFile(\'' . $row->file_imp . '\', \'file_imp\')" 
                //                 id="fileInput-' . $row->file_imp . '-file_imp">

                //             <span onclick="$(\'#fileInput-' . $row->file_imp . '-file_imp\').click();" 
                //                 style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545; cursor:pointer;">
                //                 File Belum diunggah
                //             </span>
                //         </form>';
                // }
                // return $action;
                return $row->bukti_pelaksanaan_label;
            })
            ->addColumn('dokumen_kerjasama', function ($row) {
                return $row->dokumen_kerma_label;
                // if ($row->file_mou != null && $row->file_mou != '') {
                //     $action = '<a href="' . getDocumentUrl($row->file_mou, 'file_mou') . '" class="btn btn-primary" target="_blank" data-title-tooltip="Download Bukti Pelaksanaan"><i class="bx bxs-download"></i></a>';
                // } else {
                //     $action .= '<form id="uploadForm-' . $row->file_mou . '-file_mou" enctype="multipart/form-data" style="display: inline;">
                //             <input type="file" name="file" style="display:none;" 
                //                 onchange="uploadFile(\'' . $row->file_mou . '\', \'file_mou\')" 
                //                 id="fileInput-' . $row->file_mou . '-file_mou">

                //             <span onclick="$(\'#fileInput-' . $row->file_mou . '-file_mou\').click();" 
                //                 style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545; cursor:pointer;">
                //                 File Belum diunggah
                //             </span>
                //         </form>';
                // }
                // return $action;
            })
            ->addColumn('lapor_kerma', function ($row) {
                return $row->lapor_kerma_label;
                // $action = '';
                // if ($row->file_ikuenam != null && $row->file_ikuenam != '') {
                //     $action = '<a href="' . getDocumentUrl($row->file_ikuenam, 'file_ikuenam') . '" class="btn btn-primary" target="_blank" data-title-tooltip="Download Bukti Pelaksanaan"><i class="bx bxs-download"></i></a>';
                // } else {
                //     $action .= '<form id="uploadForm-' . $row->file_ikuenam . '-file_ikuenam" enctype="multipart/form-data" style="display: inline;">
                //             <input type="file" name="file" style="display:none;" 
                //                 onchange="uploadFile(\'' . $row->file_ikuenam . '\', \'file_ikuenam\')" 
                //                 id="fileInput-' . $row->file_ikuenam . '-file_ikuenam">

                //             <span onclick="$(\'#fileInput-' . $row->file_ikuenam . '-file_ikuenam\').click();" 
                //                 style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545; cursor:pointer;">
                //                 File Belum diunggah
                //             </span>
                //         </form>';
                // }
                // return $action;

            })
            ->addColumn('tahun_berakhir', function ($row) {
                // return $row->statusPengajuanKerjaSama();
                return $row->status_pengajuan_kerja_sama_label;
            })
            ->addColumn('category', function ($row) {
                $category = preg_replace('/\s*\(.*?\)/', '', $row->category);
                $action = '<span class="badge bg-primary" data-title-tooltip="' . $category . '" style="font-size:10px!important;">' . $category . '</span>';

                return $action;
            })
            ->addColumn('status_verifikasi', function ($row) {
                if ($row->status_verifikasi == '0') {
                    $badge = 'bg-danger';
                    $status = 'Belum Diverifikasi';
                } else {
                    $badge = 'bg-success';
                    $status = 'Terverifikasi';
                }

                $action = '<span class="badge ' . $badge . '" style="font-size:10px!important;" data-title-tooltip="' . $status . '">' . $status . '</span>';

                return $action;
            })
            ->addColumn('action', function ($row) {
                return $row->action_buttons;
            })

            ->rawColumns(['action', 'category', 'bukti_pelaksanaan', 'dokumen_kerjasama', 'lapor_kerma', 'tahun_berakhir', 'status_verifikasi'])
            ->make(true);
    }

    public function getDataGroup(Request $request)
    {
        $query = laporImplementasi::select(
            'kerma_db.nama_institusi as nama_institusi',
            DB::raw('MIN(kerma_evaluasi.id_ev) as id_ev'),
            DB::raw('MIN(kerma_evaluasi.id_mou) as id_mou'),
            DB::raw('COUNT(*) as total_laporan')
        )
            ->join('kerma_db', 'kerma_db.id_mou', '=', 'kerma_evaluasi.id_mou') // Ganti sesuai foreign key-mu
            ->groupBy('kerma_db.nama_institusi');


        // Filter by search
        if ($request->has('search') && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->whereHas('dataPengajuan', function ($q) use ($search) {
                $q->where('nama_institusi', 'like', "%{$search}%");
            });
        }


        $query->FilterDokumen($request);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('pelapor', function ($row) {
                return $row->getPost->name ?? '';
            })
            ->addColumn('judul', function ($row) {
                return $row->judul != 'Lain-lain' ? $row->judul : $row->judul_lain;
            })
            ->addColumn('nama_institusi', function ($row) {
                return $row->dataPengajuan->nama_institusi ?? '';
            })
            ->addColumn('jenis_institusi_mitra', function ($row) {
                return $row->dataPengajuan->jenis_institusi ?? '';
            })
            ->addColumn('tingkat_kerjasama', function ($row) {
                return $row->dataPengajuan->dn_ln != null ? $row->dataPengajuan->dn_ln == 'Dalam Negeri' ? $row->dataPengajuan->dn_ln . ' ' . $row->dataPengajuan->wilayah_mitra : $row->dataPengajuan->dn_ln . ' ' . $row->dataPengajuan->negara_mitra : '';
            })
            ->addColumn('action', function ($row) {
                $role = session('current_role');
                $action = '<div class="btn-group" role="group">';
                $action .=
                    '<button class="btn btn-info btn-show-detail" data-nama_institusi="' . $row->nama_institusi . '">
                            <i class="bx bx-folder"></i>
                        </button>';

                $action .= '</div>';

                return $action;
            })

            ->rawColumns(['action', 'category', 'bukti_pelaksanaan', 'dokumen_kerjasama', 'lapor_kerma', 'tahun_berakhir'])
            ->make(true);
    }

    public function getDetailLembaga($institusi)
    {
        $data = laporImplementasi::select('kerma_evaluasi.*', 'kerma_db.nama_institusi')
            ->where('kerma_db.nama_institusi', $institusi)
            ->join('kerma_db', 'kerma_db.id_mou', '=', 'kerma_evaluasi.id_mou')
            // ->with(['dataPengajuan', 'getPost'])
            ->get();

        $view = view('implementasi/detail_laporan', ['dataPengajuan' => $data])->render();

        return response()->json($view);
    }


    public function tambah($id_ev = null)
    {
        session(['implementasi_key' => Str::random(40)]);
        $data = [
            'title' => 'Lapor Implementasi Kerja Sama',
            'li_active' => 'kerja_sama',
            'li_sub_active' => 'lapor_implementasi',
            'page_title' => 'Lapor Implementasi Kerja Sama',
            'tingkat_kerjasama' => RefTingkatKerjaSama::all(),
            'kategori' => RefKategoriImplementasi::all(),
            'fakultas' => RefLembagaUMS::where('jenis_lmbg', 'Fakultas')->get(),
            'program_studi' => RefLembagaUMS::where('jenis_lmbg', 'Program Studi')->get(),
            'unit' => RefLembagaUMS::where('jenis_lmbg', 'Unit (Biro/Lembaga)')->get(),
            'stats_kerma' => 'Ajuan Baru',
            'bentuk_kerjasama' => RefBentukKerjaSama::all(),
            'prodi_user' => RefLembagaUMS::where('nama_lmbg', auth()->user()->status_tempat)->first()->id_lmbg ?? '',
            'fak_user' => auth()->user()->place_state,
        ];

        if (session('current_role') == 'user') {
            $data['li_active'] = 'lapor_implementasi';
        }

        // Ambil Dokumen MoU yang sudah pernah diajukan
        // $data['jenis_mou'] = PengajuanKerjaSama::whereDate('selesai', '>=', now())
        //     ->get();

        $data['jenis_mou'] = PengajuanKerjaSama::whereNot(function ($q) {
            $q->where('stats_kerma', '=', 'Lapor Kerma')
                ->where('status_verify_publish', '=', '0')
                ->whereNotNull('status_verify_publish');
        })
            ->get();

        // dd(PengajuanKerjaSama::where(function ($q) {
        //     $q->where('stats_kerma', '=', 'Lapor Kerma')
        //         ->whereNot('status_verify_publish', '=', '0');
        // })
        //     ->toSql());

        if ($id_ev != null) {
            $dataImplementasi = laporImplementasi::where('id_ev', $id_ev)->first();
            if ($dataImplementasi->status_verifikasi == '1') {
                abort(403);
            }

            $data['page_title'] = 'Edit Lapor Implementasi Kerja Sama';
            $data['logFileIkuenam'] = getLogFile($id_ev, 'file_ikuenam');
            $data['logFileIMP'] = getLogFile($id_ev, 'file_imp');
            $data['id_ev'] = $id_ev;
            $data['dataImplementasi'] = $dataImplementasi;
            return view('implementasi/edit', $data);
        } else {
            return view('implementasi/tambah', $data);
        }
    }

    public function store(Request $request)
    {
        if ($request->implementasi_key !== session('implementasi_key') || session('implementasi_key') == null) {
            return response()->json(['error' => 'Form tidak valid, silakan reload tab terbaru.'], 403);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'id_mou' => 'required',
                'pelaksana_prodi_unit' => 'required',
                'judul' => 'required',
                'bentuk_kegiatan' => 'required',
                'tgl_mulai' => 'required',
                'category' => 'required',
                'pic_kegiatan' => 'required|email',
                'nama_pic_kegiatan' => 'required',
                'jabatan_pic_kegiatan' => 'required',
                'telp_pic_kegiatan' => 'required|min:6',

                'email_pic_internal' => 'required|email',
                'nama_pic_internal' => 'required',
                'jabatan_pic_internal' => 'required',
                'telp_pic_internal' => 'required|min:6',

                'file_imp' => 'nullable|max:5120|mimes:pdf',
                'file_ikuenam' => 'nullable|max:5120|mimes:pdf',
            ],
            [
                'id_mou.required' => 'Dokumen Kerja Sama Harus di Isi.',
                'pelaksana_prodi_unit.required' => 'Pelaksana Harus di Isi.',
                'judul.required' => 'Judul Kegiatan Harus di Isi.',
                'bentuk_kegiatan.required' => 'Bentuk Kegiatan Harus di Isi.',
                'tgl_mulai.required' => 'Tanggal Mulai Pelaksana Harus di Isi.',
                'category.required' => 'Kategori Harus di Isi.',

                'pic_kegiatan.required' => 'PIC Kegiatan Harus di Isi.',
                'pic_kegiatan.email' => 'Email PIC Kegiatan Tidak Valid.',
                'nama_pic_kegiatan.required' => 'Nama PIC Kegiatan Harus di Isi.',
                'jabatan_pic_kegiatan.required' => 'Jabatan PIC Kegiatan Harus di Isi.',
                'telp_pic_kegiatan.required' => 'Telepon PIC Kegiatan Harus di Isi.',
                'telp_pic_kegiatan.min' => 'Nomor telepon PIC internal minimal 6 digit.',

                'email_pic_internal.required' => 'PIC Internal Harus di Isi.',
                'email_pic_internal.email' => 'Email PIC Internal Tidak Valid.',
                'nama_pic_internal.required' => 'Nama PIC Internal Harus di Isi.',
                'jabatan_pic_internal.required' => 'Jabatan PIC Internal Harus di Isi.',
                'telp_pic_internal.required' => 'Telepon PIC Internal Harus di Isi.',
                'telp_pic_internal.min' => 'Nomor telepon PIC internal minimal 6 digit.',


                'file_imp.max' => 'File Dokumen Bukti Pelaksanaan maksimal 5MB.',
                'file_imp.mimes' => 'File Dokumen Bukti Pelaksanaan Berformat PDF.',
                'file_imp.mimes' => 'File Dokumen Berformat PDF.',

                'file_ikuenam.max' => 'File IKU 6 maksimal 5MB.',
                'file_ikuenam.mimes' => 'File IKU 6 Harus Berformat PDF.',
            ],
        );

        if ($request->id_ev) {
            $dataExistImplementasi = laporImplementasi::where('id_ev', $request->id_ev)->firstorFail();
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Generate ID jika tidak ada
            $id_ev = $request->id_ev ?? 'PDF' . Str::uuid()->getHex();
            $dataInsert = [
                'judul' => $request->judul,
                'pelaksana_prodi_unit' => $request->pelaksana_prodi_unit,
                'tgl_mulai' => $request->tgl_mulai,
                'tgl_selesai' => $request->tgl_selesai,
                'deskripsi_singkat' => $request->deskripsi_singkat,
                'lvl_fak' => $request->lvl_fak,
                'lvl_prodi' => $request->lvl_prodi,
                'lvl_unit' => $request->lvl_unit,
                'bentuk_kegiatan' => implode(',', $request->bentuk_kegiatan),
                'bentuk_kegiatan_lain' => $request->bentuk_kegiatan_lain,
                'category' => $request->category,

                'pic_kegiatan' => $request->pic_kegiatan,
                'nama_pic_kegiatan' => $request->nama_pic_kegiatan,
                'jabatan_pic_kegiatan' => $request->jabatan_pic_kegiatan,
                'telp_pic_kegiatan' => $request->telp_pic_kegiatan,

                'email_pic_internal' => $request->email_pic_internal,
                'nama_pic_internal' => $request->nama_pic_internal,
                'jabatan_pic_internal' => $request->jabatan_pic_internal,
                'telp_pic_internal' => $request->telp_pic_internal,

                'link_pub_internal' => $request->link_pub_internal,
                'link_pub_eksternal' => $request->link_pub_eksternal,

                'timestamp' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $ExistDokumenKerma = PengajuanKerjaSama::where('id_mou', $request->id_mou)->firstorFail();
            if ($ExistDokumenKerma) {
                $dataInsert['id_mou'] = $request->id_mou;
            }

            if ($request->lvl_unit != null) {
                $dataInsert['id_lembaga'] = $request->lvl_unit;
            } elseif ($request->lvl_prodi != null) {
                $dataInsert['id_lembaga'] = $request->lvl_prodi;
            } elseif ($request->lvl_fak != null) {
                $dataInsert['id_lembaga'] = $request->lvl_fak;
            }
            $allFiles = [];
            // Simpan file Implementasi Kerja Sama
            if ($request->hasFile('file_imp')) {
                $file_imp = $request->file('file_imp');
                $path = 'uploads/file_imp';
                $filePath = $this->upload_file($file_imp, $path);
                $dataInsert['file_imp'] = $filePath;

                $dtFiles = [
                    'jenis' => 'file_imp',
                    'path' => $filePath
                ];

                $allFiles[] = $dtFiles;
            }

            // Simpan file IKUENAM
            if ($request->hasFile('file_ikuenam')) {
                $file_ikuenam = $request->file('file_ikuenam');
                $path = 'uploads/file_ikuenam';
                $filePathIKU = $this->upload_file($file_ikuenam, $path);
                $dataInsert['file_ikuenam'] = $filePathIKU;

                $dtFiles = [
                    'jenis' => 'file_ikuenam',
                    'path' => $filePathIKU
                ];

                $allFiles[] = $dtFiles;
            }

            $skorImpementasi = RefJenisInstitusiMitra::where('klasifikasi', $ExistDokumenKerma->jenis_institusi)->first();
            if (empty($skorImpementasi)) {
                $skorImpementasi = DB::table('ref_jenis_institusi_mitra_old')->where('klasifikasi', $ExistDokumenKerma->jenis_institusi)->first();
            }
            if ($skorImpementasi) {
                $dataInsert['score_impl'] = $skorImpementasi->bobot_ums;
            }

            if (session('current_role') == 'admin') {
                $dataInsert['tgl_verifikasi'] = date('Y-m-d H:i:s');
                $dataInsert['verify_by'] = Auth::user()->username;
                $dataInsert['status_verifikasi'] = '1';
            }

            // Simpan atau update data
            if ($request->id_ev != null) {
                $insert = laporImplementasi::where('id_ev', $request->id_ev)->update($dataInsert);
                $inputData = 0;
                $dataLogketerangan = 'Update';
            } else {
                $dataInsert['id_ev'] = $id_ev;
                $dataInsert['postby'] = Auth::user()->username;
                $dataInsert['det_prodi'] = Auth::user()->place_state;
                $insert = laporImplementasi::create($dataInsert);
                $jml_productivity = $ExistDokumenKerma->jml_productivity + 1;
                PengajuanKerjaSama::where('id_mou', $request->id_mou)->update(['jml_productivity' => $jml_productivity]);
                $inputData = 1;
                $dataLogketerangan = 'Baru';
            }


            $dataEmail = laporImplementasi::where('id_ev', $id_ev)->first()->toArray();
            foreach ($allFiles as $key => $value) {
                $dataLog = [
                    'table' => 'kerma_evaluasi',
                    'id_table' => $id_ev,
                    'jenis' => $value['jenis'],
                    'path' => $value['path'],
                    'keterangan' => $dataLogketerangan,
                    'add_by' => Auth::user()->username,
                    'role' => session('current_role')
                ];
                LogActivity::create($dataLog);
            }

            DB::commit();

            if ($insert) {
                // DB::afterCommit(function () use ($dataEmail, $inputData) {
                $id_kuesioner = 'QUE' . Str::uuid()->getHex();
                $dataKuesioner = [
                    'id_kuesioner' => $id_kuesioner,
                    'id_ev' => $dataEmail['id_ev'],
                    'id_mou' => $dataEmail['id_mou'],
                    'place_stat' => Auth::user()->place_state,
                    'que_title' => 'Partner Satisfaction Survey',
                    'que_create' => date('Y-m-d H:i:s'),
                    'que_for' => 'University-Partner',
                    'is_kirim' => '1',
                    'status' => 'Open',
                ];

                $mail = MailSetting::where('is_active', '1')->first();
                $mailMessages = MailMessages::where('jenis', 'subjek_pic_kegiatan')->first();

                $role = session('current_role');
                $subjek = $mailMessages->subjek;
                $viewEmail = $mailMessages->pesan;

                // $subjek = $mail->subjek_pic_kegiatan;
                // $viewEmail = $mail->pic_kegiatan;

                if ($mailMessages->status == '1') {

                    // $urlkuesioner = '<a href="' . route('kuesioner.survei', ['id_kuesioner' => $id_kuesioner]) . '" target="_blank" style="background: #216aae; color: #fff; padding: 8px 16px; text-decoration: none; border-radius: 5px; font-size: 12px; display: inline-block;">🔗 Buka Link Kuesioner</a>';
                    $urlkuesioner = route('kuesioner.survei', ['id_kuesioner' => 'QUEcea0a8f5846e40e4885759dbcae225fd']);

                    $judul = $dataEmail['judul'] ?? ($dataEmail['judul_lain'] ?? '-');
                    $title = str_replace("{@nama_kegiatan}", $judul, $subjek);
                    $message = str_replace("{@link}", $urlkuesioner, $viewEmail);
                    $dataEmailPIC = [
                        'subject' => $judul,
                        'dataMessage' => $message,
                        'dataMessageEn' => translateEn($message),
                        'sender' => 'MyPartnership',
                        'url' => $urlkuesioner,
                    ];
                    $message = view('emails.pic_kegiatan', $dataEmailPIC)->render();

                    if (empty(trim($message))) {
                        $message = "Tidak ada pesan yang tersedia.";
                    }

                    if ($role == 'admin') {
                        $arrReceiver = [$dataEmail['pic_kegiatan']];
                    } else {
                        $arrReceiver = array_map('trim', explode(',', $mail->email_receiver));
                    }

                    $dataEmail['sender'] = 'MyPartnership';

                    $emailsToSend = [];

                    if ($inputData == 1) {
                        Kuesioner::create($dataKuesioner);
                        $dataSendMail = [
                            'arrReceiver' => $arrReceiver,
                            'message' => $message,
                            'title' => $title,
                            'institusi' => $judul,
                            'session' => session('environment'),
                            'sender' => Auth::user()->username,
                            // 'receiver' => $email
                        ];

                        $emailsToSend[] = $dataSendMail;
                    }
                    session(['sendEmailData' => $emailsToSend]);
                }
                session()->forget('implementasi_key');

                return response()->json(['status' => true, 'message' => 'Data berhasil disimpan', 'route' => route('implementasi.home')], 200);
                // });
            }

            return response()->json(['status' => false, 'message' => 'Terdapat Masalah Ketika Penyimpanan'], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function cekEmail()
    {
        $inputData = 1;
        $dataEmail = laporImplementasi::where('id_ev', 'PDF724c781f-2eb5-479c-89ba-1d3f70693911')->first()->toArray();
        $mail = MailSetting::where('is_active', '1')->first();

        $role = session('current_role');
        $subjek = $mail->subjek_pic_kegiatan;
        $viewEmail = $mail->pic_kegiatan;

        // $urlkuesioner = '<a href="' . route('kuesioner.survei', ['id_kuesioner' => 'QUEcea0a8f5846e40e4885759dbcae225fd']) . '" target="_blank" style="background: #216aae; color: #fff; padding: 8px 16px; text-decoration: none; border-radius: 5px; font-size: 12px; display: inline-block;"> Buka Link Kuesioner</a>';
        $urlkuesioner = route('kuesioner.survei', ['id_kuesioner' => 'QUEcea0a8f5846e40e4885759dbcae225fd']);
        $judul = $dataEmail['judul'] ?? ($dataEmail['judul_lain'] ?? '-');
        $title = str_replace("{@nama_kegiatan}", $judul, $subjek);
        $message = str_replace("{@link}", $urlkuesioner, $viewEmail);
        $dataEmailPIC = [
            'subject' => $judul,
            'dataMessage' => $message,
            'dataMessageEn' => translateEn($message),
            'sender' => 'MyPartnership',
            'url' => $urlkuesioner,
        ];
        $message = view('emails.pic_kegiatan', $dataEmailPIC)->render();

        if (empty(trim($message))) {
            $message = "Tidak ada pesan yang tersedia.";
        }

        if ($role == 'admin') {
            $arrReceiver = [$dataEmail['pic_kegiatan']];
        } else {
            $arrReceiver = array_map('trim', explode(',', $mail->email_receiver));
        }

        $dataEmail['sender'] = 'MyPartnership';
        if ($inputData == 1) {
            foreach ($arrReceiver as $email) {
                $dataSendMail = [
                    'message' => $message,
                    'title' => $title,
                    'institusi' => $judul,
                    'session' => session('environment'),
                    'sender' => Auth::user()->username,
                    'MailSetting' => $mail->toArray(),
                    'receiver' => $email
                ];
                SendEmailMailer::dispatchSync($dataSendMail);
                // \Artisan::call('queue:work', [
                //     '--once' => true,
                //     '--delay' => 0,
                // ]);
            }
        }
    }

    public function verifikasi(Request $request)
    {
        $status = $request->status;
        $tipe = $request->tipe;
        $verify_by = Auth::user()->username;
        $role = session('current_role');

        DB::beginTransaction();
        try {
            // if ($tipe == 'bidang') {
            $data = [
                'tgl_verifikasi' => $status == '1' ? date('Y-m-d H:i:s') : null,
                'verify_by' => $verify_by,
                'status_verifikasi' => $status,
            ];

            $pengajuan = laporImplementasi::where('id_ev', $request->id_ev)->firstOrFail();
            $pengajuan->update($data);


            DB::commit();

            $dataEmail = laporImplementasi::select('kerma_evaluasi.*', 'kerma_db.nama_institusi')->where('id_ev', $request->id_ev)
                ->join('kerma_db', 'kerma_db.id_mou', '=', 'kerma_evaluasi.id_mou')
                ->first()->toArray();

            if ($pengajuan) {
                $statusVerifikasi = $status;
                $dataEmail['sender'] = 'MyPartnership UMS';

                $mail = MailSetting::where('is_active', '1')->first();
                $role = session('current_role');

                $mailMessages = MailMessages::where('jenis', 'subjek_verifikasi_implementasi')->first();

                $title = $mailMessages->subjek;
                $viewEmail = $mailMessages->pesan;
                // $title = $mail->subjek_verifikasi_implementasi;
                // $viewEmail = $mail->verifikasi_implementasi;

                if ($mailMessages->status == '1') {

                    $message = str_replace(
                        ['{@nama_institusi}', '{@status}', '{@judul_kegiatan}', '{@verifikator}'],
                        [
                            $dataEmail['nama_institusi'],
                            // 'CEKCEK',
                            $statusVerifikasi == '1' ? 'Verifikasi' : 'Batalkan Verifikasi',
                            // 'Judul',
                            $dataEmail['judul'] ? $dataEmail['judul'] : $dataEmail['judul_lain'],
                            $role == 'user' ? 'Pengusul' : ucwords($role)
                        ],
                        $viewEmail
                    );

                    if (empty(trim($message))) {
                        $message = "Tidak ada pesan yang tersedia.";
                    }

                    if ($role == 'verifikator' || $role == 'user') {
                        // Kirim Ke Admin
                        // $sender = Auth::user()->name;
                        $arrReceiver = array_map('trim', explode(',', $mail->email_receiver));
                    } else if ($role == 'admin') {
                        // Kirim Ke User
                        // $sender = 'Admin';
                        $arrReceiver = User::where('username', $dataEmail['postby'])
                            ->distinct()
                            ->pluck('email')
                            ->toArray();
                    }

                    $dataEmail['sender'] = 'MyPartnership';
                    $dataEmailPIC = [
                        'subject' => $title,
                        'dataMessage' => $message,
                        'sender' => 'MyPartnership',
                    ];
                    $message = view('emails.template', $dataEmailPIC)->render();
                    $emailsToSend = [];

                    // foreach ($arrReceiver as $email) {
                    //     $dataSendMail = [
                    //         'message' => $message,
                    //         'title' => $title,
                    //         'institusi' => $dataEmail['nama_institusi'],
                    //         'session' => session('environment'),
                    //         'sender' => Auth::user()->username,
                    //         'MailSetting' => $mail->toArray(),
                    //         'receiver' => $email
                    //     ];
                    //     SendEmailMailer::dispatchSync($dataSendMail);
                    // }
                    $dataSendMail = [
                        'arrReceiver' => $arrReceiver,
                        'message' => $message,
                        'title' => $title,
                        'institusi' => $dataEmail['nama_institusi'],
                        'session' => session('environment'),
                        'sender' => Auth::user()->username,
                        'MailSetting' => $mail->toArray(),
                    ];
                    $emailsToSend[] = $dataSendMail;
                    // SendEmailVerifikasiImplementasi::dispatchSync($dataEmail, $role, $statusVerifikasi, session('environment'));
                    session(['sendEmailData' => $emailsToSend]);
                }
            }

            if ($status == '1') {
                $pesan = 'Verifikasi';
            } else {
                $pesan = 'Batalkan';
            }
            return response()->json(['status' => true, 'message' => 'Implementasi Berhasil di ' . $pesan]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function kirimEmail($id_ev)
    {
        $dataEmail = laporImplementasi::where('id_ev', $id_ev)->first()->toArray();
        $mail = MailSetting::where('is_active', '1')->first();
        $role = session('current_role');

        $mailMessages = MailMessages::where('jenis', 'subjek_pic_kegiatan')->first();
        $subjek = $mailMessages->subjek;
        $viewEmail = $mailMessages->pesan;

        if ($mailMessages->status == '1') {

            // $subjek = $mail->subjek_pic_kegiatan;
            // $viewEmail = $mail->pic_kegiatan;

            $judul = $dataEmail['judul'] ?? ($dataEmail['judul_lain'] ?? '-');
            $title = str_replace("{@nama_kegiatan}", $judul, $subjek);
            $message = str_replace("{@nama_kegiatan}", $judul, $viewEmail);

            if (empty(trim($message))) {
                $message = "Tidak ada pesan yang tersedia.";
            }

            if ($role == 'admin') {
                $arrReceiver = [$dataEmail['pic_kegiatan']];
            } else {
                $arrReceiver = array_map('trim', explode(',', $mail->email_receiver));
            }

            $dataEmail['sender'] = 'MyPartnership';
            $dataEmailPIC = [
                'subject' => $judul,
                'dataMessage' => $message,
                'sender' => 'MyPartnership',
            ];
            $message = view('emails.template', $dataEmailPIC)->render();
            foreach ($arrReceiver as $email) {
                $dataSendMail = [
                    'message' => $message,
                    'title' => $title,
                    'institusi' => $judul,
                    'session' => session('environment'),
                    'sender' => Auth::user()->username,
                    'MailSetting' => $mail->toArray(),
                    'receiver' => $email
                ];
                SendEmailMailer::dispatchSync($dataSendMail);
                // \Artisan::call('queue:work', [
                //     '--once' => true,
                //     '--delay' => 0,
                // ]);
            }
        }
        // SendPICNotificationEmail::dispatchSync($dataEmail, session('current_role'), session('environment'));
    }

    public function getDetailImplementasi(Request $request)
    {
        $dataImplementasi = laporImplementasi::select('*')
            ->with(['getPost', 'getLembaga', 'dataPengajuan', 'dataPengajuan.dataBobot'])
            ->where('id_ev', $request->id_ev)
            ->first();
        $file_mou = PengajuanKerjaSama::where('id_mou', $dataImplementasi->id_mou)->first()->file_mou ?? null;

        // return $dataImplementasi->file_ikuenam;
        $fileUrlImplementasi = getDocumentUrl($dataImplementasi->file_imp, 'file_imp');
        $fileUrlIkuenam = getDocumentUrl($dataImplementasi->file_ikuenam, 'file_ikuenam');
        $fileUrlMoU = $file_mou != null ? getDocumentUrl($file_mou, 'file_mou') : null;

        $data = [
            'dataImplementasi' => $dataImplementasi,
            'fileUrlImplementasi' => @$fileUrlImplementasi,
            'fileUrlIkuenam' => @$fileUrlIkuenam,
            'fileUrlMoU' => @$fileUrlMoU,
        ];

        $view = view('implementasi/detail_data', $data);
        return response()->json(['html' => $view->render()], 200);
        // return response()->json(['html' => $view->render(), 'filePdf' => asset('storage/' . $dataImplementasi->file_imp)], 200);
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = laporImplementasi::where('id_ev', $request->id_ev)->firstOrFail();
            if ($user->status_verifikasi == '1') {
                abort(403);
            }

            $ExistDokumenKerma = PengajuanKerjaSama::where('id_mou', $user->id_mou)->firstOrFail();
            $jml_productivity = $ExistDokumenKerma->jml_productivity == '0' ? '0' : $ExistDokumenKerma->jml_productivity - 1;
            PengajuanKerjaSama::where('id_mou', $user->id_mou)->update(['jml_productivity' => $jml_productivity]);

            $user->delete();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Dokumen Berhasil di Hapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function uploadFileImplementasi(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'file' => 'required|file|mimes:pdf|max:5120',
                'id_ev' => 'required|string',
                'flag' => 'required|in:file_imp,file_ikuenam,file_mou'
            ],
            [
                'file.required' => 'Tidak ada file yang diunggah.',
                'file.file' => 'File yang diunggah tidak valid.',
                'file.max' => 'Ukuran file maksimal adalah 5MB.',
                'file.mimes' => 'File Dokumen Berformat PDF.',

                'id_ev.required' => 'ID Implementasi tidak boleh kosong.',
                'id_ev.string' => 'ID Implementasi harus berupa teks.',
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
            $dataExist = laporImplementasi::where('id_ev', $request->id_ev)->firstOrFail();

            if ($request->hasFile('file')) {
                $flag = $request->file('file');
                $path = "uploads/implementasi/{$request->flag}";
                $fileUpload = $this->upload_file($flag, $path);
                $dataInsert[$request->flag] = $fileUpload;
            } else {
                if (!empty($dataExist) && $dataExist->{$request->flag} != null && $dataExist->{$request->flag} != '') {
                } else {
                    $dataInsert['{$request->flag}'] = '';
                }
            }

            laporImplementasi::where('id_ev', $request->id_ev)->update($dataInsert);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data berhasil disimpan'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
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

    public function sendMail($data, $role)
    {
        $mail = MailSetting::where('id_setting', '2')->first();
        $subjek = $mail->subjek_pic_kegiatan;
        $viewEmail = $mail->pic_kegiatan;

        try {
            $sender = Auth::user()->name;
            $arrReceiver = (array) $data['pic_kegiatan'];

            $subjek = (string) str_replace("{@nama_kegiatan}",  $data['judul'] != '' ? ucfirst($data['judul']) : ucfirst($data['judul_lain']), $subjek);
            $message = (string) str_replace("{@nama_kegiatan}", $data['judul'] != '' ? ucfirst($data['judul']) : ucfirst($data['judul_lain']), $viewEmail);

            // Jika $message kosong, beri fallback default
            if (empty(trim($message))) {
                $message = "Tidak ada pesan yang tersedia.";
            }

            $dataMessage = [
                'message' => $message,
                'sender' => $sender,
                'subject' => $subjek
            ];

            // Kirim email
            foreach ($arrReceiver as $email) {
                Mail::to($email)->send(new PICNotification($dataMessage, $subjek));
            }

            // Berhasil kirim email
            return response()->json([
                'status' => true,
                'message' => 'Email berhasil terkirim!'
            ]);
        } catch (\Exception $e) {
            // Gagal mengirim email
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendEmail(Request $request)
    {
        try {
            $dataEmail = session('sendEmailData');

            if (!is_array($dataEmail)) {
                throw new \Exception("Format dataEmail tidak valid.");
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

    public function download_implementasi_excel(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $orderColumnIndex = $request->input('order.0.column');

        $query = laporImplementasi::select('kerma_evaluasi.*', 'kerma_db.nama_institusi as nama_institusi', 'kerma_db.file_mou')
            ->with(['getPost', 'getLembaga', 'dataPengajuan'])
            ->join('kerma_db', 'kerma_db.id_mou', '=', 'kerma_evaluasi.id_mou');
        $query->FilterDokumen($request);

        // Filter by search
        if ($request->has('search') && $request->search['value'] != '') {
            $search = $request->search['value'];

            $query->whereHas('dataPengajuan', function ($q) use ($search) {
                $q->where('nama_institusi', 'like', "%{$search}%");
            });
            $query->orwhere('kerma_evaluasi.judul', 'like', "%{$search}%");
            $query->orwhere('kerma_evaluasi.judul_lain', 'like', "%{$search}%");
            $query->orwhere('kerma_evaluasi.bentuk_kegiatan', 'like', "%{$search}%");
        }

        if ($orderColumnIndex == 0) {
            $query->orderByRaw("kerma_evaluasi.postby = ? DESC", [Auth::user()->username]);
            if (session('current_role') == 'admin') {
                $query->orderByRaw("kerma_evaluasi.status_verifikasi = ? DESC", '0');
            }
        }

        $dataImplementasi = $query->get();

        // Header kolom
        $headers = [
            'No',
            'Kategori',
            'Mitra Kerja Sama',
            'Tingkat Kerja Sama',
            'Pelaksana',
            'Lembaga',
            'Judul Kegiatan',
            'Bentuk Kegiatan/Manfaat',
            'Link Bukti Pelaksanaan',
            'Link Dokumen Kerja Sama',
            'Link Lapor Kerja Sama',
            'Nama PIC Kegiatan',
            'Jabatan PIC Kegiatan',
            'Telepon PIC Kegiatan',
            'Email PIC Kegiatan',
            'Tanggal Mulai Kegiatan',
            'Tanggal Selesai Kegiatan',
            'Tanggal Verifikasi',
            'Pelapor',
            'Link Publikasi Internal',
            'Link Publikasi Eksternal',
        ];


        // Set header ke baris 1
        foreach ($headers as $i => $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($columnLetter . '1', $header);
        }

        // return $pengajuans;
        $row = 2;

        foreach ($dataImplementasi as $index => $imp) {
            $nama_institusi = $imp->dataPengajuan->nama_institusi ?? '-';
            $file_mou = $imp->dataPengajuan->file_mou;
            $nama_lembaga = $imp->dataLembaga->nama_lmbg ?? '-';

            $data = [
                $index + 1,
                $imp->category,
                $nama_institusi,
                $imp->dn_ln . ' - ' . ($imp->dn_ln == 'Dalam Negeri' ? $imp->wilayah_mitra : $imp->negara_mitra),
                $imp->pelaksana_prodi_unit,
                $nama_lembaga,
                $imp->judul ? strip_tags($imp->judul) : strip_tags($imp->judul_lain),
                $imp->bentuk_kegiatan ? strip_tags($imp->bentuk_kegiatan) : strip_tags($imp->bentuk_kegiatan_lain),

                $imp->file_imp ? asset(getDocumentUrl($imp->file_imp, 'file_imp')) : '-',
                $file_mou ? asset(getDocumentUrl($file_mou, 'file_mou')) : '-',
                $imp->file_ikuenam ? asset(getDocumentUrl($imp->file_ikuenam, 'file_ikuenam')) : '-',

                $imp->nama_pic_kegiatan,
                $imp->jabatan_pic_kegiatan,
                $imp->telp_pic_kegiatan,
                $imp->email_pic_kegiatan,

                Tanggal_Indo($imp->tgl_mulai),
                Tanggal_Indo($imp->tgl_selesai),
                Tanggal_Indo($imp->tgl_verifikasi),
                $imp->postby,

                $imp->link_pub_internal ? strip_tags($imp->link_pub_internal) : '-',
                $imp->link_pub_eksternal ? strip_tags($imp->link_pub_eksternal) : '-',

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
        $fileName = 'data_lapor_implementasi.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    private function applyRoleFilter(&$query)
    {
        $user = Auth::user();
        switch (session('current_role')) {
            case 'user':
                $query->where('add_by', $user->username);
                break;
            case 'verifikator':
                $query->where('place_state', $user->place_state);
                break;
            case 'admin':
            default:
                // No additional filter
                break;
        }
    }
}
