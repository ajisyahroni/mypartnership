<?php

namespace App\Http\Controllers;

use App\Models\AjuanHibah;
use App\Models\JawabanFeedback;
use App\Models\JawabanSkalaPenilaian;
use App\Models\laporImplementasi;
use App\Models\MasukanSurvei;
use App\Models\PengajuanKerjaSama;
use App\Models\QPartner;
use App\Models\Recognition;
use App\Models\RefJenisDokumen;
use App\Models\RefJenisInstitusiMitra;
use App\Models\RefLembagaUMS;
use App\Models\RefNegara;
use App\Models\RefPertanyaanFeedback;
use App\Models\RefSkalaPenilaian;
use App\Models\RefTingkatKerjaSama;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SurveiController extends Controller
{

    public function index()
    {
        $data = [
            'li_active' => 'survei',
            'title' => 'Rekap Survei',
            'page_title' => 'Rekap Survei',
        ];

        return view('survei.index', $data);
    }

    public function getDataInternal()
    {
        $defaultJawaban = ['Sangat Tidak Puas', 'Tidak Puas', 'Puas', 'Sangat Puas'];

        $dataSurvei = DB::table('jawaban_feedback')
            ->select(
                'ref_pertanyaan_feedback.pertanyaan',
                'ref_pertanyaan_feedback.judul',
                DB::raw("
                CASE
                    WHEN jawaban_feedback.jenis IN ('Lapor Kerma','Ajuan Baru') THEN 'Kerja Sama'
                    ELSE jawaban_feedback.jenis
                END AS jenis
            "),
                DB::raw("
                CASE
                    WHEN jawaban_feedback.jenis IN ('Lapor Kerma','Ajuan Baru') THEN 'chartKerma'
                    WHEN jawaban_feedback.jenis = 'Implementasi' THEN 'chartImplementasi'
                    WHEN jawaban_feedback.jenis = 'Rekognisi' THEN 'chartRekognisi'
                    WHEN jawaban_feedback.jenis = 'Hibah' THEN 'chartHibah'
                END AS id_chart
            "),
                'jawaban',
                DB::raw("COUNT(jawaban_feedback.jawaban) as jumlah")
            )
            ->leftJoin('ref_pertanyaan_feedback', 'jawaban_feedback.id_pertanyaan_feedback', '=', 'ref_pertanyaan_feedback.id')
            ->groupBy(
                'ref_pertanyaan_feedback.judul',
                'ref_pertanyaan_feedback.pertanyaan',
                'jawaban_feedback.jenis',
                'jawaban_feedback.jawaban'
            )
            ->orderBy('jawaban_feedback.jenis')
            ->orderByRaw("FIELD(jawaban, 'Sangat Tidak Puas','Tidak Puas','Puas','Sangat Puas')")
            ->get();

        $grouped = [];
        $no = 1;

        foreach ($dataSurvei as $item) {
            $pertanyaan = $item->pertanyaan;
            $grouped[$pertanyaan] ??= [
                'judul' => $item->judul,
                'pertanyaan' => $item->pertanyaan,
                'jenis' => $item->jenis,
                'id_chart' => $item->id_chart . '_' . $no++,
                'data' => []
            ];

            $grouped[$pertanyaan]['data'][$item->jawaban] = $item->jumlah;
        }

        // Lengkapi jawaban kosong & urutkan
        foreach ($grouped as &$g) {
            foreach ($defaultJawaban as $jawaban) {
                $g['data'][$jawaban] ??= 0;
            }

            uksort($g['data'], fn($a, $b) => array_search($a, $defaultJawaban) <=> array_search($b, $defaultJawaban));
        }
        unset($g); // clear reference

        $arrPertanyaanKerma = RefPertanyaanFeedback::whereIn('jenis', ['Ajuan Baru', 'Lapor Kerma'])
            ->pluck('pertanyaan')
            ->toArray();

        $data = [
            'DataSurvei' => array_values($grouped),
            'arrPertanyaanKerma' => $arrPertanyaanKerma,
            'dataJenis' => ['Kerja Sama', 'Implementasi', 'Rekognisi', 'Hibah'],
        ];

        $view = view('survei.internal', $data)->render();

        return response()->json([
            'internal' => $view,
        ]);
    }


    public function getMasukan(Request $request)
    {
        $jenis = $request->jenis;

        if ($jenis == 'Kerja Sama') {
            $whereIn = ['Lapor Kerma', 'Ajuan Baru'];
        } else {
            $whereIn = [$jenis];
        }


        $dataMasukan = DB::table('masukan_survei')
            ->select(
                'masukan_survei.jenis',
                'masukan_survei.jawaban',
                'masukan_survei.created_at',
                'users.name as nama',
            )
            ->leftJoin('users', 'users.username', '=', 'masukan_survei.add_by')
            ->whereIn('jenis', $whereIn)
            ->orderBy('masukan_survei.created_at', 'desc')
            ->get();

        $data = [
            'dataMasukan' => $dataMasukan,
        ];

        $view = view('survei.tabel_masukan', $data)->render();

        return response()->json([
            'tabel_masukan' => $view,
        ]);
    }


    public function getDataEksternal()
    {
        $qpartner = QPartner::select('*');
        // if ($id_kuesioner) {
        //     $qpartner = QPartner::where('id_kuesioner', $id_kuesioner);
        // }
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

        $view = view('survei.eksternal', $data)->render();

        return response()->json([
            'eksternal' => $view,
        ]);
    }

    public function getSurvei()
    {
        $DataSurvei = PengajuanKerjaSama::select('*')
            ->where('kerma_db.add_by', Auth::user()->username)
            ->where('tgl_selesai', '!=', '0000-00-00 00:00:00')
            ->get();
        // return $DataSurvei;
        $surveiNotComplete = [];
        foreach ($DataSurvei as $pengajuan) {
            $pertanyaanFeedback = RefPertanyaanFeedback::where('jenis', $pengajuan->stats_kerma)->get();

            $feedbackComplete = true;
            foreach ($pertanyaanFeedback as $pertanyaan) {
                $feedback = JawabanFeedback::where('id_table', $pengajuan->id_mou)
                    ->where('id_pertanyaan_feedback', $pertanyaan->id)
                    ->whereNotNull('jawaban')
                    ->first();

                if (!$feedback || trim($feedback->jawaban) === '') {
                    $feedbackComplete = false;
                    break;  // Keluar dari loop jika ada yang belum terisi
                }
            }

            if (!$feedbackComplete) {
                $surveiNotComplete[] = $pengajuan->id_mou;
            }
        }
        $data = [];
        $data['surveiNotComplete'] = $surveiNotComplete;
        $data['DataSurvei'] = PengajuanKerjaSama::select('*')
            ->whereIn('id_mou', $surveiNotComplete)
            ->get();

        return response()->json([
            'form' => view('survei.notifikasi_feedback', $data)->render(),
            'surveiNotComplete' => $surveiNotComplete,
        ]);
    }

    public function getSurveiImplementasi()
    {
        $DataSurvei = laporImplementasi::select('kerma_evaluasi.id_ev', 'kerma_db.nama_institusi as nama_institusi')
            ->with(['getPost', 'getLembaga', 'dataPengajuan'])
            ->join('kerma_db', 'kerma_db.id_mou', '=', 'kerma_evaluasi.id_mou')
            ->where('kerma_evaluasi.postby', auth()->user()->username)
            ->where('kerma_evaluasi.status_verifikasi', '1')
            ->get();
        // return $DataSurvei;
        $surveiNotComplete = [];
        foreach ($DataSurvei as $survei) {
            $pertanyaanFeedback = RefPertanyaanFeedback::where('jenis', 'Implementasi')->get();

            $feedbackComplete = true;
            foreach ($pertanyaanFeedback as $pertanyaan) {
                $feedback = JawabanFeedback::where('id_table', $survei->id_ev)
                    ->where('id_pertanyaan_feedback', $pertanyaan->id)
                    ->whereNotNull('jawaban')
                    ->first();

                if (!$feedback || trim($feedback->jawaban) === '') {
                    $feedbackComplete = false;
                    break;  // Keluar dari loop jika ada yang belum terisi
                }
            }

            if (!$feedbackComplete) {
                $surveiNotComplete[] = $survei->id_ev;
            }
        }
        $data = [];
        $data['surveiNotComplete'] = $surveiNotComplete;
        $data['DataSurvei'] = laporImplementasi::select('kerma_evaluasi.*', 'kerma_db.nama_institusi as nama_institusi')
            ->join('kerma_db', 'kerma_db.id_mou', '=', 'kerma_evaluasi.id_mou')
            ->whereIn('kerma_evaluasi.id_ev', $surveiNotComplete)
            ->get();

        return response()->json([
            'form' => view('survei.notifikasi_feedback_implementasi', $data)->render(),
            'surveiNotComplete' => $surveiNotComplete,
        ]);
    }

    public function getSurveiRecognition()
    {

        $query = Recognition::select('tbl_recognition.id_rec', 'ref_lembaga_ums.nama_lmbg as faculty');
        $query->leftJoin('ref_lembaga_ums', 'ref_lembaga_ums.id_lmbg', '=', 'tbl_recognition.faculty');
        $query->where('tbl_recognition.add_by', Auth::user()->username);
        $query->where('tbl_recognition.status_verify_kaprodi', '1');
        $query->where('tbl_recognition.status_verify_admin', '1');
        $DataSurvei = $query->get();

        $surveiNotComplete = [];
        foreach ($DataSurvei as $survei) {
            $pertanyaanFeedback = RefPertanyaanFeedback::where('jenis', 'Rekognisi')->get();

            $feedbackComplete = true;
            foreach ($pertanyaanFeedback as $pertanyaan) {
                $feedback = JawabanFeedback::where('id_table', $survei->id_rec)
                    ->where('id_pertanyaan_feedback', $pertanyaan->id)
                    ->whereNotNull('jawaban')
                    ->first();

                if (!$feedback || trim($feedback->jawaban) === '') {
                    $feedbackComplete = false;
                    break;  // Keluar dari loop jika ada yang belum terisi
                }
            }

            if (!$feedbackComplete) {
                $surveiNotComplete[] = $survei->id_rec;
            }
        }
        $data = [];
        $data['surveiNotComplete'] = $surveiNotComplete;

        $data['DataSurvei'] = Recognition::select('tbl_recognition.*', 'ref_lembaga_ums.nama_lmbg as faculty')
            ->leftJoin('ref_lembaga_ums', 'ref_lembaga_ums.id_lmbg', '=', 'tbl_recognition.faculty')
            ->whereIn('tbl_recognition.id_rec', $surveiNotComplete)
            ->get();

        return response()->json([
            'form' => view('survei.notifikasi_feedback_recognition', $data)->render(),
            'surveiNotComplete' => $surveiNotComplete,
        ]);
    }

    public function getSurveiHibah()
    {

        $query = AjuanHibah::select(
            'tbl_ajuan_hibah.id_hibah',
            // JOIN hasil
        );

        $query->where("tbl_ajuan_hibah.add_by", Auth::user()->username);
        $query->where("tbl_ajuan_hibah.status_selesai", '1');
        $DataSurvei = $query->get();

        $surveiNotComplete = [];
        foreach ($DataSurvei as $survei) {
            $pertanyaanFeedback = RefPertanyaanFeedback::where('jenis', 'Hibah')->get();

            $feedbackComplete = true;
            foreach ($pertanyaanFeedback as $pertanyaan) {
                $feedback = JawabanFeedback::where('id_table', $survei->id_hibah)
                    ->where('id_pertanyaan_feedback', $pertanyaan->id)
                    ->whereNotNull('jawaban')
                    ->first();

                if (!$feedback || trim($feedback->jawaban) === '') {
                    $feedbackComplete = false;
                    break;  // Keluar dari loop jika ada yang belum terisi
                }
            }

            if (!$feedbackComplete) {
                $surveiNotComplete[] = $survei->id_hibah;
            }
        }
        $data = [];
        $data['surveiNotComplete'] = $surveiNotComplete;

        $data['DataSurvei'] = AjuanHibah::select(
            'tbl_ajuan_hibah.id_hibah',
            'tbl_ajuan_hibah.judul_proposal',
            'tbl_ajuan_hibah.institusi_mitra',
            'tbl_ajuan_hibah.tgl_mulai',
            'tbl_ajuan_hibah.tgl_selesai',
        )->whereIn('tbl_ajuan_hibah.id_hibah', $surveiNotComplete)->get();

        return response()->json([
            'form' => view('survei.notifikasi_feedback_hibah', $data)->render(),
            'surveiNotComplete' => $surveiNotComplete,
        ]);
    }


    public function showSurveyForm($id_table, $status_kerma)
    {
        // Ambil data pertanyaan terkait jenis pengajuan (misalnya 'Ajuan Baru' atau 'Lapor Kerma')
        $pertanyaanFeedback = RefPertanyaanFeedback::where('jenis', $status_kerma)->get(); // Ganti sesuai dengan jenis
        $skalaPenilaian = RefSkalaPenilaian::all();

        // Ambil jawaban feedback dan jawaban skala penilaian jika ada
        $jawabanFeedback = JawabanFeedback::where('id_table', $id_table)->get();
        $jawabanSurvei = MasukanSurvei::where('id_table', $id_table)->first();

        $jenis_jawaban = $status_kerma; // Default jenis jawaban

        // Kirim form survei ke modal
        return response()->json([
            'form' => view('survei.survei_form', compact('jenis_jawaban', 'pertanyaanFeedback', 'id_table', 'skalaPenilaian', 'jawabanSurvei', 'jawabanFeedback'))->render(),
        ]);
    }

    public function submitSurvey(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'masukan' => 'nullable|string|max:255',

        ], [
            'masukan.max' => 'Masukan/saran maksimal 255 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Validasi dan simpan jawaban survei ke database
            foreach ($request->jawaban as $pertanyaanId => $jawaban) {
                // Mengecek apakah jawaban untuk pertanyaan ini sudah ada
                $existingJawaban = JawabanFeedback::where('id_table', $request->id_table)
                    ->where('id_pertanyaan_feedback', $pertanyaanId)
                    ->first();

                if ($existingJawaban) {
                    // Jika jawaban sudah ada, maka update jawaban tersebut
                    $insertFeedback = $existingJawaban->update([
                        'jawaban' => $jawaban,
                        'add_by' => Auth::user()->username,
                    ]);
                } else {
                    // Jika jawaban belum ada, maka buat jawaban baru
                    $insertFeedback = JawabanFeedback::create([
                        'jenis' => $request->jenis_jawaban,
                        'id_table' => $request->id_table,
                        'id_pertanyaan_feedback' => $pertanyaanId,
                        'jawaban' => $jawaban,
                        'add_by' => Auth::user()->username,
                    ]);
                }
            }

            // Simpan atau update masukan/saran
            if ($request->has('masukan')) {
                $existMasukan = MasukanSurvei::where('id_table', $request->id_table)->first();

                if ($existMasukan) {
                    // Jika masukan sudah ada, maka update
                    $insertMasukan = $existMasukan->update([
                        'jawaban' => $request->masukan, // Menyimpan masukan
                        'add_by' => Auth::user()->username,  // Jika perlu menyimpan siapa yang mengisi
                    ]);
                } else {
                    // Jika masukan belum ada, maka buat masukan baru
                    $insertMasukan = MasukanSurvei::create([
                        'id_table' => $request->id_table,
                        'jenis' => $request->jenis_jawaban,
                        'jawaban' => strip_tags($request->masukan), // Menyimpan masukan
                        'add_by' => Auth::user()->username,  // Jika perlu menyimpan siapa yang mengisi
                    ]);
                }
            }

            if ($insertFeedback && $insertMasukan) {
                return response()->json(['status' => true, 'message' => 'Survei berhasil dikirim!']);
            } else {
                return response()->json(['status' => false, 'message' => 'Gagal mengirim survei. Silakan coba lagi.'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
