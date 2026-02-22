<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailMailer;
use App\Models\Kuesioner;
use App\Models\laporImplementasi;
use App\Models\MailMessages;
use App\Models\MailSetting;
use App\Models\PengajuanKerjaSama;
use App\Models\QPartner;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;

class KuesionerController extends Controller
{
    public function index()
    {
        $data = [
            'li_active' => 'Kuesioner',
            'title' => 'Daftar Kuesioner',
            'page_title' => 'Kuesioner UMS',
        ];

        return view('kuesioner/index', $data);
    }

    public function survei($id_kuesioner = null)
    {
        $arrTahun = [
            '< 2 Years' => '(< 2 Tahun)',
            '2 - 4 Year' => '(2 - 4 Tahun)',
            '4 - 6 Year' => '(4 - 6 Tahun)',
            '> 6 Years' => '(> 6 Tahun)',
        ];

        $arrKategori = [
            'Study' => '(Pendidikan)',
            'Research' => '(Penelitian)',
            'Community Service' => '(Pengabdian Masyarakat)',
            'Al Islam & Kemuhammadiyahan' => '(Studi Keislaman)',
            'Others' => '(Lainnya)',
        ];

        $questions = [
            'qcommunication' => [
                'en' => 'UMS maintains good communication with partner in the development of the collaboration.',
                'id' => 'UMS menjalin komunikasi yang baik dengan mitra dalam pengembangan kerja sama.',
            ],
            'qmaintained' => [
                'en' => 'UMS maintain and respect the collaboration established with partner.',
                'id' => 'UMS menjaga dan menghormati kerja sama yang telah dilakukan bersama mitra.',
            ],
            'qbenefits' => [
                'en' => 'The collaboration brings mutual benefits to both parties.',
                'id' => 'Kerja sama memberikan manfaat bagi kedua belah pihak.',
            ],
            'qcommitment' => [
                'en' => 'UMS has a good commitment to continue the collaboration with partner.',
                'id' => 'UMS memiliki komitmen yang baik untuk melanjutkan kerja sama dengan mitra.',
            ],
            'qoverall' => [
                'en' => 'In overall, how is the collaboration managed by UMS?',
                'id' => 'Secara umum, bagaimana penilaian anda terhadap kerja sama yang dikelola oleh UMS?',
            ],
        ];

        $choices = [
            'Poor' => '(Rendah)',
            'Fair' => '(Cukup)',
            'Good' => '(Bagus)',
            'Very Good' => '(Sangat Bagus)',
            'Excellent' => '(Luar Biasa)',
        ];

        $data = [
            'arrKategori' => $arrKategori,
            'questions' => $questions,
            'choices' => $choices,
            'arrTahun' => $arrTahun,
        ];
        if ($id_kuesioner) {
            $qkuesioner = Kuesioner::select(
                'qkuesioner.*',
                'kerma_db.id_mou',
                'kerma_db.nama_institusi as nama_institusi',
            )
                ->join('kerma_evaluasi', 'kerma_evaluasi.id_ev', '=', 'qkuesioner.id_ev')
                ->join('kerma_db', 'kerma_db.id_mou', '=', 'kerma_evaluasi.id_mou')
                ->where('id_kuesioner', $id_kuesioner)
                ->first();

            if (!$qkuesioner) {
                return redirect()->route('kuesioner.survei', ['id_kuesioner' => $id_kuesioner])->with('error', 'Kuesioner tidak ditemukan.');
            }

            $qpartner = QPartner::select('*')
                ->where('id_kuesioner', $id_kuesioner)
                ->first();

            if (!empty($qpartner)) {
                return redirect()->route('kuesioner.successfully');
                $AnswerType = array_map('trim', explode(',', $qpartner->qtype));
            } else {
                $AnswerType = [];
            }

            $AnswerChoices = [
                'qcommunication' => $qpartner->qcommunication ?? '',
                'qmaintained' => $qpartner->qmaintained ?? '',
                'qbenefits' => $qpartner->qbenefits ?? '',
                'qcommitment' => $qpartner->qcommitment ?? '',
                'qoverall' => $qpartner->qoverall ?? '',
            ];

            $data['nama_institusi'] = $qkuesioner->nama_institusi;
            $data['id_mou'] = $qkuesioner->id_mou;
            $data['id_kuesioner'] = $qkuesioner->id_kuesioner;
            $data['qpartner'] = $qpartner;
            $data['AnswerType'] = $AnswerType;
            $data['AnswerChoices'] = $AnswerChoices;
        }

        return view('kuesioner.soal_kuesioner', $data);
    }

    public function submitFormSurvey(Request $request)
    {
        DB::beginTransaction();
        try {
            $rules = [
                'id_kuesioner' => 'required|exists:qkuesioner,id_kuesioner',
                'id_mou' => 'required|exists:kerma_db,id_mou',
                'qtype' => 'required',
                'qinstitution' => 'required',
                'qperiod' => 'required',
                'qcommunication' => 'required',
                'qmaintained' => 'required',
                'qbenefits' => 'required',
                'qcommitment' => 'required',
                'qoverall' => 'required',
            ];

            $message = [
                'id_kuesioner.required'   => 'ID Kuesioner wajib diisi.',
                'id_kuesioner.exists'     => 'ID Kuesioner tidak valid.',

                'id_mou.required'         => 'ID MoU wajib diisi.',
                'id_mou.exists'           => 'ID MoU tidak valid.',

                'qtype.required'          => 'Minimal satu aktivitas kerja sama harus dipilih.',
                'qinstitution.required'   => 'Nama institusi wajib diisi.',
                'qperiod.required'        => 'Periode kerja sama wajib dipilih.',

                'qcommunication.required' => 'Penilaian komunikasi wajib dipilih.',
                'qmaintained.required'    => 'Penilaian keberlanjutan wajib dipilih.',
                'qbenefits.required'      => 'Penilaian manfaat wajib dipilih.',
                'qcommitment.required'    => 'Penilaian komitmen wajib dipilih.',
                'qoverall.required'       => 'Penilaian keseluruhan wajib dipilih.',
            ];


            $validator = Validator::make($request->all(), $rules, $message);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            // return $request->all();
            $id_kuesioner = $request->id_kuesioner;
            $id_mou = $request->id_mou;
            $qpartner = QPartner::where('id_kuesioner', $id_kuesioner)->first();

            $dataInsert = [
                'id_kuesioner' => $id_kuesioner,
                'id_mou' => $id_mou,
                'qtype' => implode(',', $request->qtype),
                'qinstitution' => $request->qinstitution,
                'qperiod' => $request->qperiod,
                'qcommunication' => $request->qcommunication,
                'qmaintained' => $request->qmaintained,
                'qbenefits' => $request->qbenefits,
                'qcommitment' => $request->qcommitment,
                'qoverall' => $request->qoverall,
                'created_at' => now(),
            ];

            if (!$qpartner) {
                $insert = QPartner::create($dataInsert);

                $ExistDokumenKerma = PengajuanKerjaSama::where('id_mou', $id_mou)->firstOrFail();
                $jml_kuesioner = $ExistDokumenKerma->jml_kuesioner == '0' ? '0' : $ExistDokumenKerma->jml_kuesioner - 1;
                PengajuanKerjaSama::where('id_mou', $id_mou)->update(['jml_kuesioner' => $jml_kuesioner]);
            } else {
                $insert = QPartner::where('id_kuesioner', $id_kuesioner)->update($dataInsert);
            }
            if ($insert) {
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Kuesioner berhasil disimpan.', 'route' => route('kuesioner.successfully')], 200);
            }
            return response()->json(['success' => false, 'message' => 'Terdapat Masalah Ketika Penyimpanan.'], 500);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function successfully()
    {
        $data = [
            'li_active' => 'Kuesioner',
            'title' => 'Kuesioner Berhasil',
            'page_title' => 'Kuesioner Berhasil',
        ];

        return view('kuesioner/success', $data);
    }

    public function getData(Request $request)
    {
        $query = Kuesioner::select(
            'qkuesioner.*',
            'kerma_db.nama_institusi as nama_institusi',
            'kerma_db.wilayah_mitra',
            'kerma_db.dn_ln',
            'kerma_db.negara_mitra',
            'kerma_evaluasi.bentuk_kegiatan',
            'kerma_evaluasi.pic_kegiatan'
        )
            ->join('kerma_evaluasi', 'kerma_evaluasi.id_ev', '=', 'qkuesioner.id_ev')
            ->join('kerma_db', 'kerma_db.id_mou', '=', 'kerma_evaluasi.id_mou');
        $query->get();

        return DataTables::of($query)
            ->addIndexColumn()
            ->filterColumn('nama_institusi', function ($query, $keyword) {
                $query->where('kerma_db.nama_institusi', 'like', "%{$keyword}%");
            })
            ->filterColumn('pic_kegiatan', function ($query, $keyword) {
                $query->where('kerma_evaluasi.pic_kegiatan', 'like', "%{$keyword}%");
            })
            ->filterColumn('bentuk_kegiatan', function ($query, $keyword) {
                $query->where('kerma_evaluasi.bentuk_kegiatan', 'like', "%{$keyword}%");
            })

            ->addColumn('que_create', function ($row) {
                return $row->tanggal_label;
            })
            ->addColumn('que_for', function ($row) {
                return $row->que_for_label;
            })
            ->addColumn('status', function ($row) {
                return $row->status_label;
            })
            ->addColumn('action', function ($row) {
                return $row->action_label;
            })
            ->rawColumns(['action', 'que_for', 'status', 'que_create'])
            ->make(true);
    }

    public function hasilKuesioner($id_kuesioner = null)
    {
        $qpartner = QPartner::select('*');
        if ($id_kuesioner) {
            $qpartner = QPartner::where('id_kuesioner', $id_kuesioner);
        }
        $qpartner = $qpartner->get();

        $qkategori = $qpartner->pluck('qtype');

        $kategoriMap = [
            'Study' => 'Pendidikan',
            'Research' => 'Penelitian',
            'Community Service' => 'Pengabdian Masyarakat',
            'Al Islam & Kemuhammadiyahan' => 'Al Islam & Kemuhammadiyahan',
            'Others' => 'Others',
        ];

        $kategoriNilai = ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
        $pertanyaan = ['qcommunication', 'qmaintained', 'qbenefits', 'qcommitment', 'qoverall'];

        $hasil = [];

        foreach ($kategoriNilai as $kategori) {
            $nilai = [];

            foreach ($pertanyaan as $kolom) {
                $jumlah = $qpartner->where($kolom, $kategori)->count();
                $nilai[] = $jumlah;
            }

            $hasil[] = [
                'name' => $kategori,
                'data' => $nilai,
            ];
        }

        $chartSeries = json_encode($hasil);


        // Hitung frekuensi kategori
        $frekuensi = [];
        // return $qkategori;
        foreach ($qkategori as $key => $row) {
            $items = array_map('trim', explode(',', $row));
            foreach ($items as $item) {
                $label = $kategoriMap[$item] ?? $item;
                if (!isset($frekuensi[$label])) {
                    $frekuensi[$label] = 0;
                }
                $frekuensi[$label]++;
            }
        }

        $kategoriChart = [];
        foreach ($frekuensi as $name => $y) {
            $kategoriChart[] = [
                'name' => $name,
                'y' => $y,
            ];
        }

        $surveyCategories = [
            '1. UMS maintains good communication with partner in the development of the collaboration.',
            '2. UMS maintain and respect the collaboration established with partner.',
            '3. The collaboration brings mutual benefits to both parties.',
            '4. UMS has a good commitment to continue the collaboration with partner.',
            '5. In overall, how is the collaboration is managed by UMS?'
        ];

        // return $kategoriChart;

        $data = [
            'li_active' => 'Kuesioner',
            'title' => 'Hasil Kuesioner',
            'page_title' => 'Hasil Kuesioner',
            'kategoriChart' => $kategoriChart,
            'SurveyChart' => $chartSeries,
            'surveyCategories' => $surveyCategories,
            'qpartner' => $qpartner
        ];

        return view('kuesioner/hasil', $data);
    }

    public function getDetail(Request $request)
    {
        $query = Kuesioner::select(
            'qkuesioner.*',
            'kerma_db.nama_institusi',
            'kerma_db.wilayah_mitra',
            'kerma_db.dn_ln',
            'kerma_db.negara_mitra',
            'kerma_evaluasi.bentuk_kegiatan',
            'kerma_evaluasi.category',
            'kerma_evaluasi.pic_kegiatan',
            'kerma_evaluasi.postby',
            'kerma_evaluasi.file_imp',
            'kerma_evaluasi.ev_content',
        );
        $query->join('kerma_evaluasi', 'kerma_evaluasi.id_ev', '=', 'qkuesioner.id_ev');
        $query->join('kerma_db', 'kerma_db.id_mou', '=', 'kerma_evaluasi.id_mou');
        $query->get();

        $dataKuesioner =  $query->where('qkuesioner.id_kuesioner', $request->id_kuesioner)->first();
        $data = [
            'dataKuesioner' => $dataKuesioner,
        ];

        $view = view('kuesioner/detail', $data);
        return response()->json(['html' => $view->render()], 200);
        // return response()->json(['html' => $view->render(), 'filePdf' => asset('storage/' . $dataPengajuan->file_mou)], 200);
    }

    public function getEditKuesioner(Request $request)
    {
        $query = Kuesioner::select(
            'qkuesioner.*',
        );
        $query->get();

        $dataKuesioner = $query->where('qkuesioner.id_kuesioner', $request->id_kuesioner)->first();

        $data = [
            'dataKuesioner' => $dataKuesioner,
        ];

        $view = view('kuesioner/edit', $data);
        return response()->json(['html' => $view->render()], 200);
        // return response()->json(['html' => $view->render(), 'filePdf' => asset('storage/' . $dataPengajuan->file_mou)], 200);
    }

    public function getLinkKuesioner(Request $request)
    {
        $query = Kuesioner::select(
            'qkuesioner.*',
        );
        $query->get();

        $dataKuesioner = $query->where('qkuesioner.id_kuesioner', $request->id_kuesioner)->first();
        $data = [
            'dataKuesioner' => $dataKuesioner,
            'urlKuesioner' => route('kuesioner.survei', ['id_kuesioner' => $dataKuesioner->id_kuesioner])

        ];

        $view = view('kuesioner/link', $data);
        return response()->json(['html' => $view->render()], 200);
    }

    public function getKirimEmail(Request $request)
    {
        $query = Kuesioner::select(
            'qkuesioner.*',
            'kerma_evaluasi.pic_kegiatan',
            'kerma_db.dn_ln'
        );
        $query->join('kerma_evaluasi', 'kerma_evaluasi.id_ev', '=', 'qkuesioner.id_ev');
        $query->join('kerma_db', 'kerma_db.id_mou', '=', 'kerma_evaluasi.id_mou');
        $query->get();

        $dataKuesioner = $query->where('qkuesioner.id_kuesioner', $request->id_kuesioner)->first();
        $mail = MailSetting::where('is_active', '1')->first();

        $mailMessages = MailMessages::where('jenis', 'subjek_pic_kegiatan')->first();

        $title = $mailMessages->subjek;
        $viewEmail = $mailMessages->pesan;

        // if ($mailMessages->status == '1') {
        // $viewEmail = $mail->pic_kegiatan;

        $urlkuesioner =  route('kuesioner.survei', ['id_kuesioner' => $request->id_kuesioner]);
        $message = str_replace("{@link}", $urlkuesioner, $viewEmail);

        $data = [
            'dataKuesioner' => $dataKuesioner,
            'isiPesan' => $message,
        ];

        $view = view('kuesioner/kirim', $data);

        // }
        return response()->json(['html' => $view->render()], 200);
        // return response()->json(['html' => $view->render(), 'filePdf' => asset('storage/' . $dataPengajuan->file_mou)], 200);
    }

    public function kirimEmail(Request $request)
    {

        try {
            $mail = MailSetting::where('is_active', '1')->first();
            // return $request->all();
            $role = session('current_role');
            $subjek = $mail->subjek_pic_kegiatan;
            // $viewEmail = $mail->pic_kegiatan;

            $kuesioner = Kuesioner::find($request->id_kuesioner);

            $dataEmail = laporImplementasi::where('id_ev', $kuesioner->id_ev)->first()->toArray();

            if ($dataEmail['pic_kegiatan']) {
                $judul = $dataEmail['judul'] ?? ($dataEmail['judul_lain'] ?? '-');
                $title = str_replace("{@nama_kegiatan}", $judul, $subjek);
                // $message = str_replace("{@nama_kegiatan}", $judul, $viewEmail);
                $message = $request->isi_pesan;

                $dataEmailPIC = [
                    'subject' => $judul,
                    'judulPesan' => 'Pemberitahuan Kuesioner MyPartnership',
                    'dataMessage' => $message,
                    'sender' => 'MyPartnership',
                    'url' => route('kuesioner.survei', ['id_kuesioner' => $request->id_kuesioner]),
                ];
                // $message = view('emails.template', $dataEmailPIC)->render();
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

                $kuesioner->is_kirim = '1';
                $kuesioner->que_create = date('Y-m-d H:i:s');
                $kuesioner->save();

                return response()->json(['success' => true, 'message' => 'Email Berhasil Dikirim'], 200);
            } else {
                return response()->json(['error' => 'Email Mitra Tidak Ada.'], 500);
            }
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $dataKuesioner = Kuesioner::find($request->id_kuesioner);
            $dataKuesioner->que_title = $request->que_title;
            $dataKuesioner->status = $request->status;
            $dataKuesioner->que_for = $request->que_for;
            $dataKuesioner->que_create = $request->que_create;
            $dataKuesioner->save();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Kuesioner berhasil disimpan'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $th->getMessage()], 500);
        }
    }
}
