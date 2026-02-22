<?php

namespace App\Http\Controllers;

use App\Http\Resources\KerjaSamaResource;
use App\Models\DokumenPendukung;
use App\Models\DokumenPendukungHibah;
use App\Models\DokumenPendukungRecognition;
use App\Models\laporImplementasi;
use App\Models\PengajuanKerjaSama;
use App\Models\PengajuanKerjaSamaOld;
use App\Models\RefLembagaUMS;
use App\Models\SettingBobot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        $filter = $request->query('q');
        $placeState = $request->query('ps');
        $whereStatusTempat = "";
        $tglNull = '0000-00-00 00:00:00';

        $EloquentStatusTempat = null;
        if (!empty($filter) && strtolower($filter) !== 'universitas') {
            if (!empty($placeState)) {
                $whereStatusTempat .= "AND b.place_state = '$placeState'";
            } else {
                $whereStatusTempat .= "AND b.status_tempat = '$filter'";
            }
        }

        $settingBobot = SettingBobot::getData();
        $statusTempat = Auth::user()->status_tempat;

        $statusTempatOld = RefLembagaUMS::where('nama_lmbg', $statusTempat)->first()->nama_lmbg_old ?? null;

        // Ambil data 1 & 5 tahun
        $dataProdi1Tahun = collect($this->QueryEvaluasi(1, $settingBobot, null, null));
        $dataProdi5Tahun = collect($this->QueryEvaluasi(5, $settingBobot, null, null));

        // Hitung skor dan rata-rata
        $skorProdi1Tahun = (clone $dataProdi1Tahun)->where('status_tempat', $statusTempat)->sum('jumlah_skor');
        $skorProdi5Tahun = (clone $dataProdi5Tahun)->where('status_tempat', $statusTempat)->sum('jumlah_skor');
        // dd($skorProdi5Tahun);

        if ($skorProdi1Tahun == 0) {
            $skorProdi1Tahun = (clone $dataProdi1Tahun)->where('status_tempat', $statusTempatOld)->sum('jumlah_skor');
        }
        if ($skorProdi5Tahun == 0) {
            $skorProdi5Tahun = (clone $dataProdi5Tahun)->where('status_tempat', $statusTempatOld)->sum('jumlah_skor');
        }

        // dd($dataProdi5Tahun, $skorProdi5Tahun, $statusTempat, $statusTempatOld);

        $rata1Tahun = $this->QueryRataRata(1, $settingBobot);
        $rata5Tahun = $this->QueryRataRata(5, $settingBobot);

        $refKategoriImplementasi = collect(DB::select("SELECT name FROM ref_kategori_implementasi"));
        // $dataCategoryQuery = $this->QueryCategory(1, $placeState, $filter);
        $dataCategoryQuery = $this->QueryCategory(null, $placeState, $filter);


        $dataBentukKerjaSama = [
            'Universitas' => [
                'Dalam Negeri' => [],
                'Luar Negeri' => []
            ],
            'Fakultas' => [
                'Dalam Negeri' => [],
                'Luar Negeri' => []
            ],
            'Program Studi' => [
                'Dalam Negeri' => [],
                'Luar Negeri' => []
            ]
        ];

        // Langkah 1: Inisialisasi semua kategori dengan 0
        foreach (['Universitas', 'Fakultas', 'Program Studi'] as $level) {
            foreach (['Dalam Negeri', 'Luar Negeri'] as $lokasi) {
                foreach ($refKategoriImplementasi as $ref) {
                    $kategori = $ref->name;
                    $dataBentukKerjaSama[$level][$lokasi][$kategori] = 0;
                }
            }
        }

        // dd($dataBentukKerjaSama);

        // Langkah 2: Hitung data aktual
        foreach ($dataCategoryQuery as $item) {
            $statusTempat = strtolower($item->status_tempat);
            $kategori = $item->name;
            $lokasi = $item->dn_ln === 'Luar Negeri' ? 'Luar Negeri' : 'Dalam Negeri';

            // Tentukan level institusi
            if (str_starts_with($statusTempat, 'program studi')) {
                $level = 'Program Studi';
            } elseif (str_starts_with($statusTempat, 'fakultas')) {
                $level = 'Fakultas';
            }

            // Tambahkan jumlahnya
            $dataBentukKerjaSama['Universitas'][$lokasi][$kategori]++;
            if (isset($dataBentukKerjaSama[$level][$lokasi][$kategori])) {
                $dataBentukKerjaSama[$level][$lokasi][$kategori]++;
            }
        }

        $dataJenisInstitusi = $this->QueryJenisInstitusi(null, $placeState, $filter);
        $dataJenisInstitusiQuery = $this->QueryDataJenisInstitusi(null, $placeState, $filter);

        $dataJenisInstitusi = [
            'Universitas' => [
                'Dalam Negeri' => [],
                'Luar Negeri' => []
            ],
            'Fakultas' => [
                'Dalam Negeri' => [],
                'Luar Negeri' => []
            ],
            'Program Studi' => [
                'Dalam Negeri' => [],
                'Luar Negeri' => []
            ]
        ];

        foreach ($dataJenisInstitusiQuery as $item) {
            $statusTempat = strtolower($item->status_tempat);
            $jenisInstitusi = $item->jenis_institusi;
            $lokasi = $item->dn_ln === 'Luar Negeri' ? 'Luar Negeri' : 'Dalam Negeri';

            // ==== Universitas (selalu dihitung di semua kondisi)
            if (!isset($dataJenisInstitusi['Universitas'][$lokasi][$jenisInstitusi])) {
                $dataJenisInstitusi['Universitas'][$lokasi][$jenisInstitusi] = 0;
            }
            $dataJenisInstitusi['Universitas'][$lokasi][$jenisInstitusi]++;

            // ==== Fakultas atau Program Studi (berdasarkan status_tempat)
            if (str_starts_with($statusTempat, 'program studi')) {
                $level = 'Program Studi';
            } elseif (str_starts_with($statusTempat, 'fakultas')) {
                $level = 'Fakultas';
            }

            if ($level) {
                if (!isset($dataJenisInstitusi[$level][$lokasi][$jenisInstitusi])) {
                    $dataJenisInstitusi[$level][$lokasi][$jenisInstitusi] = 0;
                }
                $dataJenisInstitusi[$level][$lokasi][$jenisInstitusi]++;
            }
        }

        $dataNegaraQuery = $this->QueryNegara(null, $placeState, $filter);
        $dataNegara2 = $this->QueryNegara2(null, $placeState, $filter);

        $dataNegara = [
            'Universitas' => [],
            'Fakultas' => [],
            'Program Studi' => [],
        ];

        foreach ($dataNegaraQuery as $item) {
            $statusTempat = strtolower($item->status_tempat);
            $negara = $item->nama_negara;

            $dataNegara['Universitas'][$negara] = ($dataNegara['Universitas'][$negara] ?? 0) + 1;
            if (str_starts_with($statusTempat, 'program studi')) {
                $dataNegara['Program Studi'][$negara] = ($dataNegara['Program Studi'][$negara] ?? 0) + 1;
            } elseif (str_starts_with($statusTempat, 'fakultas')) {
                $dataNegara['Fakultas'][$negara] = ($dataNegara['Fakultas'][$negara] ?? 0) + 1;
            }
        }

        $formattedCountry = collect($dataNegara)->map(function ($negaraList) {
            return collect($negaraList)->map(function ($jumlah, $negara) {
                return [
                    'nama_negara' => $negara,
                    'jumlah' => $jumlah,
                ];
            })->values();
        });


        $dataNegaraProduktifQuery = $this->QueryNegaraProduktif(null, $placeState, $filter);
        $dataNegaraProduktif2 = $this->QueryNegaraProduktif2(null, $placeState, $filter);

        $dataNegaraProduktif = [
            'Universitas' => [],
            'Fakultas' => [],
            'Program Studi' => [],
        ];

        foreach ($dataNegaraProduktifQuery as $item) {
            $statusTempat = strtolower($item->status_tempat); // agar lebih fleksibel
            $negara = $item->nama_negara;

            $dataNegaraProduktif['Universitas'][$negara] = ($dataNegaraProduktif['Universitas'][$negara] ?? 0) + 1;
            if (str_starts_with($statusTempat, 'program studi')) {
                $dataNegaraProduktif['Program Studi'][$negara] = ($dataNegaraProduktif['Program Studi'][$negara] ?? 0) + 1;
            } elseif (str_starts_with($statusTempat, 'fakultas')) {
                $dataNegaraProduktif['Fakultas'][$negara] = ($dataNegaraProduktif['Fakultas'][$negara] ?? 0) + 1;
            }
        }

        $formattedCountryProduktif = collect($dataNegaraProduktif)->map(function ($negaraList) {
            return collect($negaraList)->map(function ($jumlah, $negara) {
                return [
                    'nama_negara' => $negara,
                    'jumlah' => $jumlah,
                ];
            })->values();
        });

        // return $this->dataFakultas();
        // return $formattedCountry;

        $refLembagaUms = RefLembagaUMS::whereIn('jenis_lmbg', ['Program Studi', 'Fakultas'])->orderByRaw("jenis_lmbg = 'Fakultas' DESC")->orderBy('jenis_lmbg')->orderBy('nama_lmbg')->get();

        // Query dasar
        $baseQueryKermaAktif = $this->QueryKerma(null, $placeState, $filter);
        $baseQueryKermaProduktif = $this->QueryKermaProduktif(null, $placeState, $filter);

        // Clone query dasar untuk count DN dan LN
        $kermaDN = (clone $baseQueryKermaAktif)->where('dn_ln', 'Dalam Negeri')->count();
        $kermaLN = (clone $baseQueryKermaAktif)->where('dn_ln', 'Luar Negeri')->count();

        $kermaDNProduktif = (clone $baseQueryKermaProduktif)->selectRaw(
            'DISTINCT b.id_mou,
                b.*'
        )->where('dn_ln', 'Dalam Negeri')->count();
        $kermaLNProduktif = (clone $baseQueryKermaProduktif)->selectRaw(
            'DISTINCT b.id_mou,
                b.*'
        )->where('dn_ln', 'Luar Negeri')->count();

        // Query untuk tren grafik
        $trenGrafis = (clone $baseQueryKermaAktif)
            ->selectRaw("
        YEAR(created_at) as tahun,
        COUNT(id_mou) as total,
        SUM(CASE WHEN dn_ln = 'Dalam Negeri' THEN 1 ELSE 0 END) as dn,
        SUM(CASE WHEN dn_ln = 'Luar Negeri' THEN 1 ELSE 0 END) as ln
        ")
            ->groupByRaw('YEAR(created_at)')
            ->orderByRaw('YEAR(created_at)')
            ->get();

        $trenGrafisProduktif = (clone $baseQueryKermaProduktif)
            ->selectRaw("
        YEAR(a.created_at) as tahun,
        COUNT(a.id_mou) as total,
        SUM(CASE WHEN b.dn_ln = 'Dalam Negeri' THEN 1 ELSE 0 END) as dn,
        SUM(CASE WHEN b.dn_ln = 'Luar Negeri' THEN 1 ELSE 0 END) as ln
        ")
            ->groupByRaw('YEAR(a.created_at)')
            ->orderByRaw('YEAR(a.created_at)')
            ->get();

        $dataProdi = collect($this->QueryEvaluasi(null, $settingBobot, null, null));
        $filterData = function ($prefix, $field) use ($dataProdi) {
            return $dataProdi
                ->filter(fn($item) => str_starts_with($item->jenis_lembaga, $prefix))
                ->sortByDesc(fn($item) => (float) $item->$field)
                ->values()
                ->take(5);
        };

        $KerjaSamaLembaga = $this->QueryKermaLembaga(null, $settingBobot, null);

        $notif_verifikator = 0;
        if (in_array(session('current_role'), ['verifikator'])) {
            $notif_verifikator = $this->notif_verifikator();
        }

        $jenisLembagaUser = RefLembagaUMS::where('nama_lmbg', Auth::user()->status_tempat)->first()->jenis_lmbg ?? null;
        return view('home.index', [
            'li_active' => 'dashboard',
            'q' => $filter,
            'jenisLembagaUser' => $jenisLembagaUser,
            'placeState' => $placeState,
            'kermaDN' => $kermaDN,
            'kermaLN' => $kermaLN,
            'kermaDNProduktif' => $kermaDNProduktif,
            'kermaLNProduktif' => $kermaLNProduktif,
            'trenGrafis' => $trenGrafis,
            'trenGrafisProduktif' => $trenGrafisProduktif,
            'ProdiScore1' => $skorProdi1Tahun,
            'ProdiScore5' => $skorProdi5Tahun,
            'AverageScore1' => $rata1Tahun,
            'AverageScore5' => $rata5Tahun,
            'RefLembagaUMS' => $refLembagaUms,
            'KerjaSamaLembaga' => $KerjaSamaLembaga,
            'dataProdi' => $filterData('Program Studi', 'jumlah_skor'),
            'dataFakultas' => $filterData('Fakultas', 'jumlah_skor'),
            'dataProdiMitra' => $filterData('Program Studi', 'jumlah_mitra'),
            'dataFakultasMitra' => $filterData('Fakultas', 'jumlah_mitra'),
            'dataProdiProduktif' => $filterData('Program Studi', 'jumlah_produktivitas'),
            'dataFakultasProduktif' => $filterData('Fakultas', 'jumlah_produktivitas'),
            'dataNegara' => $formattedCountry,
            'dataNegara2' => $dataNegara2,
            'dataNegaraProduktif' => $formattedCountryProduktif,
            'dataNegaraProduktif2' => $dataNegaraProduktif2,
            'dataBentukKerjaSama' => $dataBentukKerjaSama,
            'dataJenisInstitusi' => $dataJenisInstitusi,
            'notif_verifikator' => $notif_verifikator,
        ]);
    }

    public function sinkronisasi()
    {
        // Sinkronisasi Data User
        $users = User::whereNotNull('status_user')->get();
        foreach ($users as $key => $value) {
            $statusUser = $value->status_user;
            if ($statusUser == 'admin' || $statusUser == 'administrator') {
                $value->syncRoles(['admin', 'verifikator', 'user']);
            } elseif ($statusUser == 'verifikator') {
                $value->syncRoles(['verifikator', 'user']);
            } elseif ($statusUser == 'user') {
                $value->syncRoles(['user']);
            }
        }
    }

    public function getDataSebaranMitra(Request $request)
    {
        $filter = $request->input('filter');

        // Contoh dummy query
        $data = [];

        if ($filter == 'universitas') {
            $data = DB::table('mitra')
                ->select('nama_negara', DB::raw('COUNT(*) as jumlah'))
                ->groupBy('nama_negara')
                ->get();
        } elseif ($filter == 'fakultas') {
            $data = DB::table('mitra')
                ->select('nama_negara', DB::raw('COUNT(*) as jumlah'))
                ->whereNotNull('fakultas')
                ->groupBy('nama_negara')
                ->get();
        } elseif ($filter == 'program_studi') {
            $data = DB::table('mitra')
                ->select('nama_negara', DB::raw('COUNT(*) as jumlah'))
                ->whereNotNull('program_studi')
                ->groupBy('nama_negara')
                ->get();
        }

        return response()->json($data);
    }


    // public function detailSkor(Request $request)
    // {
    //     $settingBobot = SettingBobot::getData();
    //     $statusTempat = Auth::user()->status_tempat;

    //     if ($request->type == 'ProdiScore') {
    //         $baseQuery = DB::table('kerma_evaluasi as a')
    //             ->leftJoin('kerma_db as b', 'a.id_mou', '=', 'b.id_mou')
    //             ->leftJoin('ref_jenis_institusi_mitra as c', 'b.jenis_institusi', '=', 'c.klasifikasi')
    //             ->leftJoin('ref_jenis_institusi_mitra_old as co', 'b.jenis_institusi', '=', 'co.klasifikasi')
    //             ->leftJoin('ref_lembaga_ums as d', 'b.place_state', '=', 'd.id_lmbg')
    //             ->select(
    //                 'b.nama_institusi',
    //                 'b.dn_ln',
    //                 'b.place_state',
    //                 'b.jenis_institusi',
    //                 'c.bobot_dikti',
    //                 DB::raw('COALESCE(c.bobot_ums, co.bobot_ums) as bobot_ums'),
    //                 'b.status_tempat',
    //                 DB::raw("CASE 
    //             WHEN b.dn_ln = 'Luar Negeri' THEN (COALESCE(c.bobot_ums, co.bobot_ums) * {$settingBobot->luar_negeri}) 
    //             ELSE COALESCE(c.bobot_ums, co.bobot_ums) * {$settingBobot->dalam_negeri} 
    //             END AS jumlah_skor")
    //             )
    //             ->whereNull('a.deleted_at')
    //             ->where('b.place_state', '!=', '')
    //             ->where('b.place_state', '!=', 'admin')
    //             ->where('b.status_tempat', '!=', 'admin')
    //             ->whereNotNull('b.status_tempat')
    //             ->where(function ($query) {
    //                 $query->whereNotNull('c.bobot_ums')->orwhereNotNull('co.bobot_ums');
    //             })
    //             ->where('a.created_at', '>', now()->subYears($request->tahun))
    //             ->whereRaw('CURRENT_DATE BETWEEN b.mulai AND b.selesai')
    //             ->where('b.status_tempat', $statusTempat)
    //             ->get();
    //     } else {
    //         $baseQuery = DB::table('kerma_evaluasi as a')
    //             ->leftJoin('kerma_db as b', 'a.id_mou', '=', 'b.id_mou')
    //             ->leftJoin('ref_jenis_institusi_mitra as c', 'b.jenis_institusi', '=', 'c.klasifikasi')
    //             ->leftJoin('ref_jenis_institusi_mitra_old as co', 'b.jenis_institusi', '=', 'co.klasifikasi')
    //             ->select(
    //                 'b.status_tempat',
    //                 DB::raw("SUM(
    //             CASE 
    //                 WHEN b.dn_ln = 'Luar Negeri' THEN (COALESCE(c.bobot_ums, co.bobot_ums) * {$settingBobot->luar_negeri}) 
    //                 ELSE COALESCE(c.bobot_ums, co.bobot_ums) * {$settingBobot->dalam_negeri} 
    //             END
    //         ) AS jumlah_skor")
    //             )
    //             ->whereNull('a.deleted_at')
    //             ->where('b.place_state', '!=', '')
    //             ->where('b.place_state', '!=', 'admin')
    //             ->where('b.status_tempat', '!=', 'admin')
    //             ->whereNotNull('b.status_tempat')
    //             ->where(function ($query) {
    //                 $query->whereNotNull('c.bobot_ums')->orWhereNotNull('co.bobot_ums');
    //             })
    //             ->whereRaw('CURRENT_DATE BETWEEN b.mulai AND b.selesai')
    //             ->where('a.created_at', '>', now()->subYears($request->tahun))
    //             ->groupBy('b.status_tempat')
    //             ->get();
    //     }


    //     // Ambil semua data skor
    //     $allScores = $baseQuery;
    //     // return $statusTempat;
    //     // return $allScores;
    //     $data = [
    //         'view' => view('home.detailSkor', [
    //             'data' => $allScores,
    //             'type' => $request->type,
    //             'dataSetting' => $settingBobot,
    //         ])->render(),
    //     ];

    //     return response()->json($data);
    // }

    public function detailSkor(Request $request)
    {
        $settingBobot = SettingBobot::getData();
        $statusTempat = Auth::user()->status_tempat;
        $statusTempatOld = RefLembagaUMS::where('nama_lmbg', $statusTempat)->first()->nama_lmbg_old ?? null;

        $arrStatusTempat = [$statusTempat, $statusTempatOld];

        if ($request->type == 'ProdiScore') {
            // $baseQuery = $this->QuerydataSkor($request->tahun, $settingBobot, $statusTempat);
            $baseQuery = $this->QuerydataSkor($request->tahun, $settingBobot, $arrStatusTempat);
        } else {
            $baseQuery = $this->QuerydataRataRata($request->tahun, $settingBobot);
        }

        // Ambil semua data skor
        $allScores = $baseQuery;
        // return $statusTempat;
        // return $allScores;
        $data = [
            'view' => view('home.detailSkor', [
                'data' => $allScores,
                'type' => $request->type,
                'dataSetting' => $settingBobot,
            ])->render(),
        ];

        return response()->json($data);
    }

    public function detailKerma(Request $request)
    {
        $filter = $request->query('q');
        $placeState = $request->query('ps');
        $query = $this->QueryKerma(null, $placeState, $filter);
        $query->where('dn_ln', $request->type);
        $dokumens = $query->get();

        $data = [
            'view' => view('home.detailKerma', [
                'type' => $request->type,
                'data' => $dokumens,
            ])->render(),
        ];

        return response()->json($data);
    }

    public function detailInstansi(Request $request)
    {
        $placeState = $request->placeState ?? null;
        $settingBobot = SettingBobot::getData();
        $dataKerma = $this->QueryDetailKermaLembaga(null, $settingBobot, $placeState);

        $data = [
            'view' => view('home.detailInstansi', [
                'type' => $request->type,
                'data' => $dataKerma,
            ])->render(),
        ];

        return response()->json($data);
    }

    // public function detailSelengkapnya_old(Request $request)
    // {
    //     $tipe = $request->tipe;
    //     $jenis = $request->jenis;

    //     $settingBobot = SettingBobot::getData();
    //     $statusTempat = Auth::user()->status_tempat;

    //     // === Helper Closure ===
    //     // $getEvaluasiQuery = function ($interval) use ($settingBobot) {
    //     //     return DB::select("
    //     //     SELECT
    //     //         b.status_tempat,
    //     //         COUNT(*) AS jumlah_implementasi,    
    //     //         COUNT(DISTINCT a.id_ev) AS jumlah_produktivitas,
    //     //         COUNT(DISTINCT a.id_mou) AS jumlah_mitra,
    //     //         COUNT(DISTINCT CASE WHEN b.dn_ln = 'Luar Negeri' THEN a.id_ev END) AS jumlah_produktivitas_kerma_ln,
    //     //         COUNT(DISTINCT CASE WHEN b.dn_ln = 'Dalam Negeri' THEN a.id_ev END) AS jumlah_produktivitas_kerma_dn,
    //     //         COUNT(DISTINCT CASE WHEN b.dn_ln = 'Luar Negeri' THEN b.id_mou END) AS jumlah_mitra_kerma_ln,
    //     //         COUNT(DISTINCT CASE WHEN b.dn_ln = 'Dalam Negeri' THEN b.id_mou END) AS jumlah_mitra_kerma_dn,
    //     //         SUM(CASE WHEN b.dn_ln = 'Luar Negeri' THEN 1 ELSE 0 END) AS kerma_ln,
    //     //         SUM(CASE WHEN b.dn_ln = 'Dalam Negeri' THEN 1 ELSE 0 END) AS kerma_dn,
    //     //         COALESCE(c.bobot_ums,co.bobot_ums) as bobot_ums,
    //     //         SUM(CASE WHEN b.dn_ln = 'Luar Negeri' THEN (COALESCE(c.bobot_ums,co.bobot_ums) * ?) ELSE COALESCE(c.bobot_ums,co.bobot_ums) * ? END) AS jumlah_skor
    //     //     FROM
    //     //         kerma_evaluasi a
    //     //         LEFT JOIN kerma_db b ON a.id_mou = b.id_mou
    //     //         LEFT JOIN ref_jenis_institusi_mitra c ON b.jenis_institusi = c.klasifikasi
    //     //         LEFT JOIN ref_jenis_institusi_mitra_old co ON b.jenis_institusi = co.klasifikasi
    //     //     WHERE
    //     //         a.deleted_at IS NULL 
    //     //         AND b.place_state != '' 
    //     //         AND b.place_state != 'admin' 
    //     //         AND b.status_tempat != 'admin' 
    //     //         AND b.status_tempat IS NOT NULL 
    //     //         AND (c.bobot_ums IS NOT NULL OR co.bobot_ums IS NOT NULL)
    //     //         AND a.created_at > DATE_SUB(NOW(), INTERVAL {$interval} YEAR)
    //     //     GROUP BY
    //     //         b.status_tempat, COALESCE(c.bobot_ums,co.bobot_ums)
    //     //     HAVING jumlah_skor IS NOT NULL
    //     // ", [$settingBobot->luar_negeri, $settingBobot->dalam_negeri]);
    //     // };
    //     $getEvaluasiQuery = function ($interval) use ($settingBobot) {
    //         return DB::select("
    //         SELECT
    //             b.status_tempat,
    //             COUNT(*) AS jumlah_implementasi,    
    //             COUNT(DISTINCT a.id_ev) AS jumlah_produktivitas,
    //             COUNT(DISTINCT a.id_mou) AS jumlah_mitra,
    //             COUNT(DISTINCT CASE WHEN b.dn_ln = 'Luar Negeri' THEN a.id_ev END) AS jumlah_produktivitas_kerma_ln,
    //             COUNT(DISTINCT CASE WHEN b.dn_ln = 'Dalam Negeri' THEN a.id_ev END) AS jumlah_produktivitas_kerma_dn,
    //             COUNT(DISTINCT CASE WHEN b.dn_ln = 'Luar Negeri' THEN b.id_mou END) AS jumlah_mitra_kerma_ln,
    //             COUNT(DISTINCT CASE WHEN b.dn_ln = 'Dalam Negeri' THEN b.id_mou END) AS jumlah_mitra_kerma_dn,
    //             SUM(CASE WHEN b.dn_ln = 'Luar Negeri' THEN 1 ELSE 0 END) AS kerma_ln,
    //             SUM(CASE WHEN b.dn_ln = 'Dalam Negeri' THEN 1 ELSE 0 END) AS kerma_dn,
    //             SUM(CASE WHEN b.dn_ln = 'Luar Negeri' THEN (COALESCE(c.bobot_ums,co.bobot_ums) * ?) ELSE COALESCE(c.bobot_ums,co.bobot_ums) * ? END) AS jumlah_skor
    //         FROM
    //             kerma_evaluasi a
    //             LEFT JOIN kerma_db b ON a.id_mou = b.id_mou
    //             LEFT JOIN ref_jenis_institusi_mitra c ON b.jenis_institusi = c.klasifikasi
    //             LEFT JOIN ref_jenis_institusi_mitra_old co ON b.jenis_institusi = co.klasifikasi
    //         WHERE
    //             a.deleted_at IS NULL 
    //             AND b.place_state != '' 
    //             AND b.place_state != 'admin' 
    //             AND b.status_tempat != 'admin' 
    //             AND b.status_tempat IS NOT NULL 
    //             AND (c.bobot_ums IS NOT NULL OR co.bobot_ums IS NOT NULL)

    //             AND CURRENT_DATE BETWEEN b.mulai AND b.selesai 

    //             AND a.created_at > DATE_SUB(NOW(), INTERVAL {$interval} YEAR)
    //         GROUP BY
    //             b.status_tempat
    //         HAVING jumlah_skor IS NOT NULL
    //     ", [$settingBobot->luar_negeri, $settingBobot->dalam_negeri]);
    //     };

    //     // Ambil data 1 & 5 tahun
    //     $dataProdi1Tahun = collect($getEvaluasiQuery(1));
    //     $dataProdi5Tahun = collect($getEvaluasiQuery(5));

    //     // === Prodi dan Fakultas Berdasarkan Data 1 Tahun ===
    //     $filterData = function ($prefix, $field) use ($dataProdi1Tahun) {
    //         return $dataProdi1Tahun
    //             ->filter(fn($item) => str_starts_with($item->status_tempat, $prefix))
    //             ->sortByDesc(fn($item) => (float) $item->$field)
    //             ->values();
    //     };


    //     if ($tipe == 'peringkat' && $jenis == 'prodi') {
    //         $data = $filterData('Program Studi', 'jumlah_skor');
    //     } elseif ($tipe == 'peringkat' && $jenis == 'fakultas') {
    //         $data = $filterData('Fakultas', 'jumlah_skor');
    //     } else if ($tipe == 'produktif' && $jenis == 'prodi') {
    //         $data = $filterData('Program Studi', 'jumlah_produktivitas');
    //     } else if ($tipe == 'mitra' && $jenis == 'prodi') {
    //         $data = $filterData('Program Studi', 'jumlah_mitra');
    //     } else if ($tipe == 'produktif' && $jenis == 'fakultas') {
    //         $data = $filterData('Fakultas', 'jumlah_produktivitas');
    //     } else if ($tipe == 'mitra' && $jenis == 'fakultas') {
    //         $data = $filterData('Fakultas', 'jumlah_mitra');
    //     }

    //     $data = [
    //         'view' => view('home.detailSelengkapnya', [
    //             'data' => $data,
    //             'tipe' => $tipe,
    //             'jenis' => $jenis,
    //         ])->render(),
    //     ];

    //     return response()->json($data);
    // }

    public function detailSelengkapnya(Request $request)
    {
        $tipe = $request->tipe;
        $jenis = $request->jenis;

        $settingBobot = SettingBobot::getData();
        $statusTempat = Auth::user()->status_tempat;

        $dataProdi = collect($this->QueryEvaluasi(null, $settingBobot, null, null));

        $filterData = function ($prefix, $field) use ($dataProdi) {
            return $dataProdi
                // ->filter(fn($item) => str_starts_with($item->status_tempat, $prefix))
                ->filter(fn($item) => str_starts_with($item->jenis_lembaga, $prefix))
                ->sortByDesc(fn($item) => (float) $item->$field)
                ->values();
        };

        if ($tipe == 'peringkat' && $jenis == 'prodi') {
            $data = $filterData('Program Studi', 'jumlah_skor');
        } elseif ($tipe == 'peringkat' && $jenis == 'fakultas') {
            $data = $filterData('Fakultas', 'jumlah_skor');
        } else if ($tipe == 'produktif' && $jenis == 'prodi') {
            $data = $filterData('Program Studi', 'jumlah_produktivitas');
        } else if ($tipe == 'mitra' && $jenis == 'prodi') {
            $data = $filterData('Program Studi', 'jumlah_mitra');
        } else if ($tipe == 'produktif' && $jenis == 'fakultas') {
            $data = $filterData('Fakultas', 'jumlah_produktivitas');
        } else if ($tipe == 'mitra' && $jenis == 'fakultas') {
            $data = $filterData('Fakultas', 'jumlah_mitra');
        }

        $data = [
            'view' => view('home.detailSelengkapnya', [
                'data' => $data,
                'tipe' => $tipe,
                'jenis' => $jenis,
            ])->render(),
        ];

        return response()->json($data);
    }


    private function dataFakultas()
    {
        $query = DB::table('kerma_db as b')
            ->leftJoin('kerma_evaluasi as a', 'a.id_mou', '=', 'b.id_mou')
            ->leftJoin('ref_jenis_institusi_mitra as c', 'b.jenis_institusi', '=', 'c.klasifikasi')
            ->leftJoin('ref_jenis_institusi_mitra_old as co', 'b.jenis_institusi', '=', 'co.klasifikasi')
            ->leftJoin('ref_lembaga_ums as d', 'd.id_lmbg', '=', 'b.id_lembaga')
            ->leftJoin('ref_lembaga_ums_old as e', 'e.id_lmbg', '=', 'b.id_lembaga')
            ->leftJoin('ref_countries as rc', 'rc.NAME', '=', 'b.negara_mitra')
            ->selectRaw("
                    COALESCE(d.place_state, d.id_lmbg) AS id_fakultas,
                    b.status_tempat,
                    COUNT(DISTINCT b.id_mou) AS jumlah_kerma,
                    COUNT(DISTINCT CASE WHEN b.dn_ln = 'Luar Negeri' THEN b.id_mou END) AS jumlah_kerma_ln,
                    COUNT(DISTINCT CASE WHEN b.dn_ln = 'Dalam Negeri' THEN b.id_mou END) AS jumlah_kerma_dn,

                    COUNT(DISTINCT b.nama_institusi) AS jumlah_mitra,
                    COUNT(DISTINCT CASE WHEN b.dn_ln = 'Luar Negeri' THEN b.nama_institusi END) AS jumlah_mitra_ln,
                    COUNT(DISTINCT CASE WHEN b.dn_ln = 'Dalam Negeri' THEN b.nama_institusi END) AS jumlah_mitra_dn,

                    COUNT(DISTINCT a.id_ev) AS jumlah_produktivitas,
                    COUNT(DISTINCT CASE WHEN b.dn_ln = 'Luar Negeri' THEN a.id_ev END) AS jumlah_produktivitas_ln,
                    COUNT(DISTINCT CASE WHEN b.dn_ln = 'Dalam Negeri' THEN a.id_ev END) AS jumlah_produktivitas_dn,

                    SUM(
                        CASE
                            WHEN b.dn_ln = 'Luar Negeri' THEN (a.score_impl * 3)
                            ELSE a.score_impl * 1
                        END
                    ) AS jumlah_skor
                ")
            ->whereNull('a.deleted_at')
            ->whereNull('b.deleted_at')
            ->whereNotIn('b.place_state', ['', 'admin'])
            ->where('b.status_tempat', '!=', 'admin')
            ->whereNotNull('b.status_tempat')
            ->whereNotNull('b.id_lembaga');


        // filter tambahan (bisa aktifkan kalau perlu):
        // $query->whereNotNull('a.id_ev');
        // $query->whereBetween(DB::raw('CURRENT_DATE'), [DB::raw('b.mulai'), DB::raw('b.selesai')]);
        $query->where(function ($q) {
            $q->where('b.periode_kerma', 'notknown')->where('b.status_mou', 'Aktif')
                ->orWhere(function ($q2) {
                    $q2->where('b.periode_kerma', 'bydoc')
                        ->whereBetween(
                            DB::raw('CURRENT_DATE'),
                            [DB::raw('b.mulai'), DB::raw('b.selesai')]
                        );
                });
        });
        // $query->where('b.tgl_selesai', '!=', '0000-00-00 00:00:00');
        // $query->whereRaw('a.created_at > DATE_SUB(NOW(), INTERVAL 1 YEAR)');

        $query->groupByRaw('COALESCE(d.place_state, d.id_lmbg)')
            ->orderByDesc('jumlah_kerma');

        return $query->get();
    }


    private function QueryEvaluasi($interval = null, $settingBobot, $instansi = null, $limit = null)
    {
        // ( COALESCE (c.bobot_ums, co.bobot_ums) * ? ) ELSE COALESCE ( c.bobot_ums, co.bobot_ums ) * ? 
        // COALESCE(d.place_state,d.id_lmbg) AS id_fakultas,
        $query = DB::table('kerma_evaluasi as a')
            ->leftJoin('kerma_db as b', 'a.id_mou', '=', 'b.id_mou')
            ->leftJoin('ref_jenis_institusi_mitra as c', 'b.jenis_institusi', '=', 'c.klasifikasi')
            ->leftJoin('ref_jenis_institusi_mitra_old as co', 'b.jenis_institusi', '=', 'co.klasifikasi')
            // ->leftJoin('ref_lembaga_ums as d', 'd.id_lmbg', '=', 'b.id_lembaga')
            // ->leftJoin('ref_lembaga_ums_old as e', 'e.id_lmbg', '=', 'b.id_lembaga')
            ->leftJoin('ref_lembaga_ums as d', 'd.nama_lmbg', '=', 'b.status_tempat')
            ->leftJoin('ref_lembaga_ums_old as e', 'e.nama_lmbg', '=', 'b.status_tempat')
            ->selectRaw("
                CASE 
                    WHEN d.id_lmbg IS NULL THEN
                        (SELECT id_lmbg FROM ref_lembaga_ums WHERE id_lmbg_old = e.id_lmbg)
                    ELSE
                        (SELECT id_lmbg FROM ref_lembaga_ums WHERE id_lmbg = d.id_lmbg)
                END AS id_fakultas,

                b.status_tempat,

                CASE
                    WHEN d.id_lmbg IS NULL THEN
                        ( SELECT jenis_lmbg FROM ref_lembaga_ums WHERE id_lmbg_old = e.id_lmbg ) 
                    ELSE 
                        ( SELECT jenis_lmbg FROM ref_lembaga_ums WHERE id_lmbg = d.id_lmbg ) 
                END AS jenis_lembaga,

                	
                count(DISTINCT b.id_mou) AS jumlah_kerma,
                COUNT( DISTINCT CASE WHEN b.dn_ln = 'Luar Negeri' THEN b.id_mou END ) AS jumlah_kerma_ln,
                COUNT( DISTINCT CASE WHEN b.dn_ln = 'Dalam Negeri' THEN b.id_mou END ) AS jumlah_kerma_dn,
                
                COUNT(DISTINCT b.nama_institusi) AS jumlah_mitra,
                COUNT(DISTINCT CASE WHEN b.dn_ln = 'Luar Negeri' THEN b.nama_institusi END) AS jumlah_mitra_kerma_ln,
                COUNT(DISTINCT CASE WHEN b.dn_ln = 'Dalam Negeri' THEN b.nama_institusi END) AS jumlah_mitra_kerma_dn,
                    
                COUNT( DISTINCT a.id_ev ) AS jumlah_produktivitas,
                COUNT( DISTINCT CASE WHEN b.dn_ln = 'Luar Negeri' THEN a.id_ev END ) AS jumlah_produktivitas_kerma_ln,
                COUNT( DISTINCT CASE WHEN b.dn_ln = 'Dalam Negeri' THEN a.id_ev END ) AS jumlah_produktivitas_kerma_dn,
                    
                SUM(
                    CASE
                        WHEN b.dn_ln = 'Luar Negeri' THEN
                        a.score_impl * ? ELSE a.score_impl * ? 
                    END 
                ) AS jumlah_skor 
                ", [$settingBobot->luar_negeri, $settingBobot->dalam_negeri])
            ->whereNull('a.deleted_at')
            ->whereNull('b.deleted_at')
            ->whereNotIn('b.place_state', ['', 'admin'])
            ->where('b.status_tempat', '!=', 'admin')
            ->whereNotNull('b.status_tempat');
        // ->whereNotNull('b.id_lembaga');

        // $query->whereNot('b.tgl_selesai', '0000-00-00 00:00:00');
        // $query->whereRaw('CURRENT_DATE BETWEEN b.mulai AND b.selesai');
        $query->whereRaw("(b.periode_kerma = 'notknown' AND b.status_mou = 'Aktif' OR (b.periode_kerma = 'bydoc' AND CURRENT_DATE BETWEEN b.mulai AND b.selesai))");
        if ($interval) {
            $query->whereRaw("a.created_at > DATE_SUB(NOW(), INTERVAL {$interval} YEAR)");
        }
        if ($instansi) {
            $query->where('b.status_tempat', 'like', $instansi . '%');
        }
        if ($limit) {
            $query->limit($limit);
        }

        $query->groupByRaw(
            'id_fakultas, b.status_tempat, jenis_lembaga'
        );
        $query->havingRaw('jumlah_skor IS NOT NULL');
        $query->orderByRaw('jumlah_skor DESC');

        // // dd($instansi);
        // dd($query->toSql(), $query->getBindings());
        return $query->get();
    }

    private function QueryRataRata($interval = null, $settingBobot)
    {
        $sub = DB::table('kerma_evaluasi as a')
            ->leftJoin('kerma_db as b', 'a.id_mou', '=', 'b.id_mou')
            ->leftJoin('ref_jenis_institusi_mitra as c', 'b.jenis_institusi', '=', 'c.klasifikasi')
            ->leftJoin('ref_jenis_institusi_mitra_old as co', 'b.jenis_institusi', '=', 'co.klasifikasi')
            ->leftJoin('ref_lembaga_ums as d', 'd.nama_lmbg', '=', 'b.status_tempat')
            ->leftJoin('ref_lembaga_ums_old as e', 'e.nama_lmbg', '=', 'b.status_tempat')
            ->selectRaw("
            b.status_tempat,
            SUM(
                CASE 
                    WHEN b.dn_ln = 'Luar Negeri' 
                        THEN a.score_impl * ?
                    ELSE a.score_impl * ?
                END
            ) AS jumlah_skor
            ", [$settingBobot->luar_negeri, $settingBobot->dalam_negeri])
            ->whereNull('a.deleted_at')
            ->whereNull('b.deleted_at')
            ->whereNotIn('b.place_state', ['', 'admin'])
            ->where('b.status_tempat', '!=', 'admin')
            ->whereNotNull('b.status_tempat');
        // ->whereNotNull('b.id_lembaga');

        // $sub->whereRaw('CURRENT_DATE BETWEEN b.mulai AND b.selesai');
        $sub->whereRaw("(b.periode_kerma = 'notknown' AND b.status_mou = 'Aktif' OR (b.periode_kerma = 'bydoc' AND CURRENT_DATE BETWEEN b.mulai AND b.selesai))");

        if ($interval) {
            $sub->whereRaw("a.created_at > DATE_SUB(NOW(), INTERVAL {$interval} YEAR)");
        }

        $sub->groupBy('b.status_tempat');

        return DB::query()
            ->fromSub($sub, 'sub')
            ->avg('jumlah_skor');
    }

    private function QuerydataSkor($interval = null, $settingBobot, $statusTempat)
    {
        $baseQuery = DB::table('kerma_evaluasi as a')
            ->leftJoin('kerma_db as b', 'a.id_mou', '=', 'b.id_mou')
            ->leftJoin('ref_jenis_institusi_mitra as c', 'b.jenis_institusi', '=', 'c.klasifikasi')
            ->leftJoin('ref_jenis_institusi_mitra_old as co', 'b.jenis_institusi', '=', 'co.klasifikasi')
            // ->leftJoin('ref_lembaga_ums as d', 'b.place_state', '=', 'd.id_lmbg')
            // ->leftJoin('ref_lembaga_ums as d', 'd.id_lmbg', '=', 'b.id_lembaga')
            // ->leftJoin('ref_lembaga_ums_old as e', 'e.id_lmbg', '=', 'b.id_lembaga')
            ->leftJoin('ref_lembaga_ums as d', 'd.nama_lmbg', '=', 'b.status_tempat')
            ->leftJoin('ref_lembaga_ums_old as e', 'e.nama_lmbg', '=', 'b.status_tempat')
            ->select(
                'b.nama_institusi',
                'a.bentuk_kegiatan',
                'a.judul',
                'a.judul_lain',
                'b.dn_ln',
                'b.place_state',
                'b.jenis_institusi',
                'c.bobot_dikti',
                DB::raw('COALESCE(c.bobot_ums, co.bobot_ums) as bobot_ums'),
                'b.status_tempat',
                DB::raw("CASE 
                WHEN b.dn_ln = 'Luar Negeri' THEN a.score_impl  * {$settingBobot->luar_negeri} 
                ELSE a.score_impl * {$settingBobot->dalam_negeri} 
                END AS jumlah_skor")
            )
            ->whereNull('a.deleted_at')
            ->where('b.place_state', '!=', '')
            ->where('b.place_state', '!=', 'admin')
            ->where('b.status_tempat', '!=', 'admin')
            ->whereNotNull('b.status_tempat');

        if ($interval) {
            $baseQuery->whereRaw("a.created_at > DATE_SUB(NOW(), INTERVAL {$interval} YEAR)");
        }
        // $baseQuery->whereRaw('CURRENT_DATE BETWEEN b.mulai AND b.selesai');
        $baseQuery->whereRaw("(b.periode_kerma = 'notknown' AND b.status_mou = 'Aktif' OR (b.periode_kerma = 'bydoc' AND CURRENT_DATE BETWEEN b.mulai AND b.selesai))");
        // $baseQuery->where('b.status_tempat', $statusTempat);
        $baseQuery->whereIn('b.status_tempat', $statusTempat);
        $baseQuery->orderByRaw('jumlah_skor DESC');
        return $baseQuery->get();
    }

    private function QuerydataRataRata($interval = null, $settingBobot)
    {
        $baseQuery = DB::table('kerma_evaluasi as a')
            ->leftJoin('kerma_db as b', 'a.id_mou', '=', 'b.id_mou')
            ->leftJoin('ref_jenis_institusi_mitra as c', 'b.jenis_institusi', '=', 'c.klasifikasi')
            ->leftJoin('ref_jenis_institusi_mitra_old as co', 'b.jenis_institusi', '=', 'co.klasifikasi')
            ->select(
                'b.status_tempat',
                DB::raw("SUM(
                CASE 
                    WHEN b.dn_ln = 'Luar Negeri' THEN a.score_impl * {$settingBobot->luar_negeri}
                    ELSE a.score_impl * {$settingBobot->dalam_negeri} 
                END
            ) AS jumlah_skor")
            )
            ->whereNull('a.deleted_at')
            ->whereNull('b.deleted_at')
            ->whereNotIn('b.place_state', ['', 'admin'])
            ->where('b.status_tempat', '!=', 'admin')
            ->whereNotNull('b.status_tempat');
        // ->whereNotNull('b.id_lembaga');

        // $baseQuery->whereRaw('CURRENT_DATE BETWEEN b.mulai AND b.selesai');
        $baseQuery->whereRaw("(b.periode_kerma = 'notknown' AND b.status_mou = 'Aktif' OR (b.periode_kerma = 'bydoc' AND CURRENT_DATE BETWEEN b.mulai AND b.selesai))");
        if ($interval) {
            $baseQuery->whereRaw("a.created_at > DATE_SUB(NOW(), INTERVAL {$interval} YEAR)");
        }

        $baseQuery->groupBy('b.status_tempat');
        $baseQuery->orderByRaw('jumlah_skor DESC');
        return $baseQuery->get();
    }

    private function QueryCategory($interval = null, $placeState, $filter)
    {
        $query = DB::table('ref_kategori_implementasi as a')
            ->select([
                'a.name',
                'b.status_tempat',
                'b.dn_ln',
            ])
            ->leftJoin('kerma_evaluasi as c', 'c.category', '=', 'a.kategori')
            ->leftJoin('kerma_db as b', 'c.id_mou', '=', 'b.id_mou')
            ->leftJoin('ref_jenis_institusi_mitra as d', 'b.jenis_institusi', '=', 'd.klasifikasi')
            ->leftJoin('ref_jenis_institusi_mitra_old as co', 'b.jenis_institusi', '=', 'co.klasifikasi')
            ->whereNull('c.deleted_at')
            ->whereNull('b.deleted_at')
            ->whereNotIn('b.place_state', ['', 'admin'])
            ->where('b.status_tempat', '!=', 'admin')
            ->whereNotNull('b.status_tempat');
        // ->whereNotNull('b.id_lembaga');

        // $query->whereRaw("CURRENT_DATE BETWEEN b.mulai AND b.selesai");
        $query->whereRaw("(b.periode_kerma = 'notknown' AND b.status_mou = 'Aktif' OR (b.periode_kerma = 'bydoc' AND CURRENT_DATE BETWEEN b.mulai AND b.selesai))");

        if ($interval) {
            $query->whereRaw("c.created_at > DATE_SUB(NOW(), INTERVAL ? YEAR)", [$interval]);
        }

        if (!empty($filter) && strtolower($filter) !== 'universitas') {
            if (!empty($placeState)) {
                $query->where('b.place_state', $placeState);
            } else {
                $query->where('b.status_tempat', $filter);
            }
        }

        return $query->get();
    }


    private function QueryJenisInstitusi($interval = null, $placeState, $filter)
    {
        $query = DB::table('kerma_evaluasi as a')
            ->select([
                'b.jenis_institusi',
                'b.dn_ln',
                DB::raw('COUNT(a.id_ev) as jumlah'),
            ])
            ->leftJoin('kerma_db as b', 'a.id_mou', '=', 'b.id_mou')
            ->leftJoin('ref_jenis_institusi_mitra as c', 'b.jenis_institusi', '=', 'c.klasifikasi')
            ->leftJoin('ref_jenis_institusi_mitra_old as co', 'b.jenis_institusi', '=', 'co.klasifikasi')
            ->whereNull('a.deleted_at')
            ->whereNull('b.deleted_at')
            ->whereNotIn('b.place_state', ['', 'admin'])
            ->where('b.status_tempat', '!=', 'admin')
            ->whereNotNull('b.status_tempat');
        // ->whereNotNull('b.id_lembaga');

        // $query->whereRaw('CURRENT_DATE BETWEEN b.mulai AND b.selesai');
        $query->whereRaw("(b.periode_kerma = 'notknown' AND b.status_mou = 'Aktif' OR (b.periode_kerma = 'bydoc' AND CURRENT_DATE BETWEEN b.mulai AND b.selesai))");
        if ($interval) {
            $query->whereRaw('a.created_at > DATE_SUB(NOW(), INTERVAL ? YEAR)', [$interval]);
        }
        if (!empty($filter) && strtolower($filter) !== 'universitas') {
            if (!empty($placeState)) {
                $query->where('b.place_state', $placeState);
            } else {
                $query->where('b.status_tempat', $filter);
            }
        }

        $query->groupBy('b.jenis_institusi', 'b.dn_ln');
        return $query->get();
    }

    private function QueryDataJenisInstitusi($interval, $placeState, $filter)
    {
        $query = DB::table('kerma_evaluasi as a')
            ->select([
                'b.status_tempat',
                'b.jenis_institusi',
                'b.dn_ln',
            ])
            ->leftJoin('kerma_db as b', 'a.id_mou', '=', 'b.id_mou')
            ->leftJoin('ref_jenis_institusi_mitra as c', 'b.jenis_institusi', '=', 'c.klasifikasi')
            ->leftJoin('ref_jenis_institusi_mitra_old as co', 'b.jenis_institusi', '=', 'co.klasifikasi')
            ->whereNull('a.deleted_at')
            ->whereNull('b.deleted_at')
            ->whereNotIn('b.place_state', ['', 'admin'])
            ->where('b.status_tempat', '!=', 'admin')
            ->whereNotNull('b.status_tempat');
        // ->whereNotNull('b.id_lembaga');

        // $query->whereRaw('CURRENT_DATE BETWEEN b.mulai AND b.selesai');
        $query->whereRaw("(b.periode_kerma = 'notknown' AND b.status_mou = 'Aktif' OR (b.periode_kerma = 'bydoc' AND CURRENT_DATE BETWEEN b.mulai AND b.selesai))");
        if ($interval) {
            $query->whereRaw('a.created_at > DATE_SUB(NOW(), INTERVAL ? YEAR)', [$interval]);
        }
        if (!empty($filter) && strtolower($filter) !== 'universitas') {
            if (!empty($placeState)) {
                $query->where('b.place_state', $placeState);
            } else {
                $query->where('b.status_tempat', $filter);
            }
        }

        return $query->get();
    }


    private function QueryNegara($interval = null, $placeState, $filter)
    {
        $query = DB::table('kerma_evaluasi as a')
            // $query = DB::table('kerma_db as b')
            ->selectRaw("
                    b.id_mou,
                    b.status_tempat,
                    CASE 
                        WHEN b.dn_ln = 'Dalam Negeri' THEN 'Indonesia' 
                        ELSE b.negara_mitra 
                    END as nama_negara
                ")
            ->leftJoin('kerma_db as b', 'a.id_mou', '=', 'b.id_mou')
            // ->leftJoin('kerma_evaluasi as a', 'a.id_mou', '=', 'b.id_mou')
            ->leftJoin('ref_jenis_institusi_mitra as c', 'b.jenis_institusi', '=', 'c.klasifikasi')
            ->leftJoin('ref_jenis_institusi_mitra_old as co', 'b.jenis_institusi', '=', 'co.klasifikasi')
            ->whereNull('a.deleted_at')
            ->whereNull('b.deleted_at')
            ->whereNotIn('b.place_state', ['', 'admin'])
            ->where('b.status_tempat', '!=', 'admin')
            ->where('b.status_tempat', '!=', '');
        // ->whereNotNull('b.id_lembaga');
        // kalau $whereStatusTempat adalah kondisi dinamis, bisa tambahkan pakai when:
        if (!empty($filter) && strtolower($filter) !== 'universitas') {
            if (!empty($placeState)) {
                $query->where('b.place_state', $placeState);
            } else {
                $query->where('b.status_tempat', $filter);
            }
        }

        // $query->whereBetween(DB::raw('CURRENT_DATE'), [DB::raw('b.mulai'), DB::raw('b.selesai')]);
        $query->where(function ($q) {
            $q->where('b.periode_kerma', 'notknown')->where('b.status_mou', 'Aktif')
                ->orWhere(function ($q2) {
                    $q2->where('b.periode_kerma', 'bydoc')
                        ->whereBetween(
                            DB::raw('CURRENT_DATE'),
                            [DB::raw('b.mulai'), DB::raw('b.selesai')]
                        );
                });
        });
        if ($interval) {
            $query->where('a.created_at', '>', DB::raw("DATE_SUB(NOW(), INTERVAL {$interval} YEAR)"));
        }

        return $query->get();
    }

    private function QueryNegara2($interval = null, $placeState, $filter)
    {
        $query = DB::table('kerma_db as b')
            ->leftJoin('kerma_evaluasi as a', 'a.id_mou', '=', 'b.id_mou')
            ->leftJoin('ref_jenis_institusi_mitra as c', 'b.jenis_institusi', '=', 'c.klasifikasi')
            ->leftJoin('ref_jenis_institusi_mitra_old as co', 'b.jenis_institusi', '=', 'co.klasifikasi')
            // ->leftJoin('ref_lembaga_ums as d', 'd.id_lmbg', '=', 'b.id_lembaga')
            // ->leftJoin('ref_lembaga_ums_old as e', 'e.id_lmbg', '=', 'b.id_lembaga')
            ->leftJoin('ref_lembaga_ums as d', 'd.nama_lmbg', '=', 'b.status_tempat')
            ->leftJoin('ref_lembaga_ums_old as e', 'e.nama_lmbg', '=', 'b.status_tempat')
            ->leftJoin('ref_countries as rc', 'rc.NAME', '=', 'b.negara_mitra')
            ->whereNull('a.deleted_at')
            ->whereNull('b.deleted_at')
            ->where('b.place_state', '!=', '')
            ->where('b.place_state', '!=', 'admin')
            ->where('b.status_tempat', '!=', 'admin')
            ->where('b.status_tempat', '!=', '')
            // ->whereNotNull('b.id_lembaga')

            // // Sudah Lapor Implementasi
            // ->whereNotNull('a.id_ev')
            // // Kerma Aktif
            // ->whereBetween(DB::raw('CURRENT_DATE'), [DB::raw('b.mulai'), DB::raw('b.selesai')]);
            ->where(function ($q) {
                $q->where('b.periode_kerma', 'notknown')->where('b.status_mou', 'Aktif')
                    ->orWhere(function ($q2) {
                        $q2->where('b.periode_kerma', 'bydoc')
                            ->whereBetween(
                                DB::raw('CURRENT_DATE'),
                                [DB::raw('b.mulai'), DB::raw('b.selesai')]
                            );
                    });
            });

        if ($interval) {
            $query->where('a.created_at', '>', DB::raw("DATE_SUB(NOW(), INTERVAL {$interval} YEAR)"));
        }

        // Tambahkan filter jika ada
        if (!empty($filter) && strtolower($filter) !== 'universitas') {
            if (!empty($placeState)) {
                $query->where('b.place_state', $placeState);
            } else {
                $query->where('b.status_tempat', $filter);
            }
        }

        // // Sudah Selesai Proses Pengajuan
        // ->where('b.tgl_selesai', '!=', '0000-00-00 00:00:00')

        // //  1 Tahun Terakhir
        // ->whereRaw('a.created_at > DATE_SUB(NOW(), INTERVAL 1 YEAR)')

        $query->selectRaw("
                    CASE
                        WHEN b.dn_ln = 'Dalam Negeri' THEN 'Indonesia'
                        WHEN b.dn_ln = 'Luar Negeri' AND b.negara_mitra = 'Indonesia' THEN 'Luar Negeri - Indonesia'
                        ELSE b.negara_mitra
                    END AS nama_negara,
                    COUNT(DISTINCT b.id_mou) AS jumlah,
                    CASE
                        WHEN b.dn_ln = 'Dalam Negeri' THEN 'id'
                        ELSE LOWER(rc.iso2)
                    END AS kode
                ");
        $query->groupByRaw("nama_negara, kode");
        $query->orderByDesc('jumlah');
        return $query->get();
    }

    private function QueryNegaraProduktif($interval = null, $placeState, $filter)
    {
        $query = DB::table('kerma_evaluasi as a')
            // $query = DB::table('kerma_db as b')
            ->selectRaw("
                    b.id_mou,
                    b.status_tempat,
                    CASE 
                        WHEN b.dn_ln = 'Dalam Negeri' THEN 'Indonesia' 
                        ELSE b.negara_mitra 
                    END as nama_negara
                ")
            ->leftJoin('kerma_db as b', 'a.id_mou', '=', 'b.id_mou')
            // ->leftJoin('kerma_evaluasi as a', 'a.id_mou', '=', 'b.id_mou')
            ->leftJoin('ref_jenis_institusi_mitra as c', 'b.jenis_institusi', '=', 'c.klasifikasi')
            ->leftJoin('ref_jenis_institusi_mitra_old as co', 'b.jenis_institusi', '=', 'co.klasifikasi')
            ->whereNull('a.deleted_at')
            ->whereNull('b.deleted_at')
            ->whereNotIn('b.place_state', ['', 'admin'])
            ->where('b.status_tempat', '!=', 'admin')
            ->where('b.status_tempat', '!=', '')
            // ->whereNotNull('b.id_lembaga')
            ->whereNotNull('a.id_ev');
        // kalau $whereStatusTempat adalah kondisi dinamis, bisa tambahkan pakai when:
        if (!empty($filter) && strtolower($filter) !== 'universitas') {
            if (!empty($placeState)) {
                $query->where('b.place_state', $placeState);
            } else {
                $query->where('b.status_tempat', $filter);
            }
        }

        // $query->whereBetween(DB::raw('CURRENT_DATE'), [DB::raw('b.mulai'), DB::raw('b.selesai')]);
        $query->where(function ($q) {
            $q->where('b.periode_kerma', 'notknown')->where('b.status_mou', 'Aktif')
                ->orWhere(function ($q2) {
                    $q2->where('b.periode_kerma', 'bydoc')
                        ->whereBetween(
                            DB::raw('CURRENT_DATE'),
                            [DB::raw('b.mulai'), DB::raw('b.selesai')]
                        );
                });
        });
        if ($interval) {
            $query->where('a.created_at', '>', DB::raw("DATE_SUB(NOW(), INTERVAL {$interval} YEAR)"));
        }

        return $query->get();
    }

    private function QueryNegaraProduktif2($interval = null, $placeState, $filter)
    {
        $query = DB::table('kerma_evaluasi as a')
            ->leftJoin('kerma_db as b', 'a.id_mou', '=', 'b.id_mou')
            ->leftJoin('ref_jenis_institusi_mitra as c', 'b.jenis_institusi', '=', 'c.klasifikasi')
            ->leftJoin('ref_jenis_institusi_mitra_old as co', 'b.jenis_institusi', '=', 'co.klasifikasi')
            // ->leftJoin('ref_lembaga_ums as d', 'd.id_lmbg', '=', 'b.id_lembaga')
            // ->leftJoin('ref_lembaga_ums_old as e', 'e.id_lmbg', '=', 'b.id_lembaga')
            // ->leftJoin('ref_lembaga_ums as d', 'd.id_lmbg', '=', 'b.place_state')
            // ->leftJoin('ref_lembaga_ums_old as e', 'e.id_lmbg', '=', 'b.place_state')
            ->leftJoin('ref_lembaga_ums as d', 'd.nama_lmbg', '=', 'b.status_tempat')
            ->leftJoin('ref_lembaga_ums_old as e', 'e.nama_lmbg', '=', 'b.status_tempat')
            ->leftJoin('ref_countries as rc', 'rc.NAME', '=', 'b.negara_mitra')
            ->whereNull('a.deleted_at')
            ->whereNull('b.deleted_at')
            ->where('b.place_state', '!=', '')
            ->where('b.place_state', '!=', 'admin')
            ->where('b.status_tempat', '!=', 'admin')
            ->where('b.status_tempat', '!=', '')
            // ->whereNotNull('b.id_lembaga')

            // // Sudah Lapor Implementasi
            ->whereNotNull('a.id_ev')
            // // Kerma Aktif
            // ->whereBetween(DB::raw('CURRENT_DATE'), [DB::raw('b.mulai'), DB::raw('b.selesai')]);
            ->where(function ($q) {
                $q->where('b.periode_kerma', 'notknown')->where('b.status_mou', 'Aktif')
                    ->orWhere(function ($q2) {
                        $q2->where('b.periode_kerma', 'bydoc')
                            ->whereBetween(
                                DB::raw('CURRENT_DATE'),
                                [DB::raw('b.mulai'), DB::raw('b.selesai')]
                            );
                    });
            });

        if ($interval) {
            $query->where('a.created_at', '>', DB::raw("DATE_SUB(NOW(), INTERVAL {$interval} YEAR)"));
        }

        // Tambahkan filter jika ada
        if (!empty($filter) && strtolower($filter) !== 'universitas') {
            if (!empty($placeState)) {
                $query->where('b.place_state', $placeState);
            } else {
                $query->where('b.status_tempat', $filter);
            }
        }

        // // Sudah Selesai Proses Pengajuan
        // ->where('b.tgl_selesai', '!=', '0000-00-00 00:00:00')

        // //  1 Tahun Terakhir
        // ->whereRaw('a.created_at > DATE_SUB(NOW(), INTERVAL 1 YEAR)')

        $query->selectRaw("
                    CASE
                        WHEN b.dn_ln = 'Dalam Negeri' THEN 'Indonesia'
                        WHEN b.dn_ln = 'Luar Negeri' AND b.negara_mitra = 'Indonesia' THEN 'Luar Negeri - Indonesia'
                        ELSE b.negara_mitra
                    END AS nama_negara,
                    COUNT(b.id_mou) AS jumlah,
                    CASE
                        WHEN b.dn_ln = 'Dalam Negeri' THEN 'id'
                        ELSE LOWER(rc.iso2)
                    END AS kode
                ");
        $query->groupByRaw("nama_negara, kode");
        $query->orderByDesc('jumlah');
        return $query->get();
    }

    private function QueryKerma($interval = null, $placeState, $filter)
    {
        $query = DB::table('kerma_db')
            ->whereRaw('deleted_at IS NULL
                    AND place_state NOT IN ("","admin")
                    AND status_tempat != "admin"
                    AND status_tempat != "" 
                    AND status_tempat IS NOT NULL
                    ');

        // Tambahkan filter jika ada
        if (!empty($filter) && strtolower($filter) !== 'universitas') {
            if (!empty($placeState)) {
                $query->where('place_state', $placeState);
            } else {
                $query->where('status_tempat', $filter);
            }
        }

        // $query->whereRaw('CURRENT_DATE BETWEEN mulai AND selesai');
        $query->whereRaw("(periode_kerma = 'notknown' AND status_mou = 'Aktif' OR (periode_kerma = 'bydoc' AND CURRENT_DATE BETWEEN mulai AND selesai))");
        if ($interval) {
            $query->where('created_at', '>', DB::raw("DATE_SUB(NOW(), INTERVAL {$interval} YEAR)"));
        }

        return $query;
    }

    private function QueryKermaProduktif($interval = null, $placeState, $filter)
    {
        $query = DB::table('kerma_db as b')
            ->leftJoin('kerma_evaluasi as a', 'a.id_mou', '=', 'b.id_mou')
            ->whereRaw(
                'b.deleted_at IS NULL
                    AND a.deleted_at IS NULL
                    AND b.place_state NOT IN ("","admin")
                    AND b.status_tempat != "admin"
                    AND b.status_tempat != "" 
                    AND b.status_tempat IS NOT NULL'
            );

        $query->whereNotNull('a.id_ev');
        // Tambahkan filter jika ada
        if (!empty($filter) && strtolower($filter) !== 'universitas') {
            if (!empty($placeState)) {
                $query->where('b.place_state', $placeState);
            } else {
                $query->where('status_tempat', $filter);
            }
        }

        // $query->whereRaw('CURRENT_DATE BETWEEN mulai AND selesai');
        $query->whereRaw("(b.periode_kerma = 'notknown' AND b.status_mou = 'Aktif' OR (b.periode_kerma = 'bydoc' AND CURRENT_DATE BETWEEN b.mulai AND b.selesai))");
        if ($interval) {
            $query->where('b.created_at', '>', DB::raw("DATE_SUB(NOW(), INTERVAL {$interval} YEAR)"));
        }

        return $query;
    }

    private function QueryKermaLembaga($interval = null, $settingBobot, $placeState = null)
    {
        // COALESCE(d.nama_lmbg, e.nama_lmbg) AS nama_fakultas,
        // (SELECT nama_lmbg FROM ref_lembaga_ums where id_lmbg = COALESCE(d.place_state, d.id_lmbg)) AS nama_fakultas,
        $dataKerma = DB::table('kerma_db as b')
            ->leftJoin('kerma_evaluasi as a', 'a.id_mou', '=', 'b.id_mou')
            ->leftJoin('ref_jenis_institusi_mitra as c', 'b.jenis_institusi', '=', 'c.klasifikasi')
            ->leftJoin('ref_jenis_institusi_mitra_old as co', 'b.jenis_institusi', '=', 'co.klasifikasi')
            // ->leftJoin('ref_lembaga_ums as d', 'd.id_lmbg', '=', 'b.id_lembaga')
            // ->leftJoin('ref_lembaga_ums_old as e', 'e.id_lmbg', '=', 'b.id_lembaga')
            ->leftJoin('ref_lembaga_ums as d', 'd.nama_lmbg', '=', 'b.status_tempat')
            ->leftJoin('ref_lembaga_ums_old as e', 'e.nama_lmbg', '=', 'b.status_tempat')
            ->leftJoin('ref_countries as rc', 'rc.NAME', '=', 'b.negara_mitra')
            ->selectRaw("
            CASE 
                WHEN d.id_lmbg IS NULL THEN
                    (SELECT nama_lmbg FROM ref_lembaga_ums WHERE id_lmbg_old = e.id_lmbg)
                ELSE
                    (SELECT nama_lmbg FROM ref_lembaga_ums WHERE id_lmbg = d.id_lmbg)
            END AS nama_fakultas,

            COUNT(DISTINCT b.id_mou) AS jumlah_kerma,
            COUNT(DISTINCT CASE WHEN b.dn_ln = 'Luar Negeri' THEN b.id_mou END) AS jumlah_kerma_ln,
            COUNT(DISTINCT CASE WHEN b.dn_ln = 'Dalam Negeri' THEN b.id_mou END) AS jumlah_kerma_dn,

            COUNT(DISTINCT b.nama_institusi) AS jumlah_mitra,
            COUNT(DISTINCT CASE WHEN b.dn_ln = 'Luar Negeri' THEN b.nama_institusi END) AS jumlah_mitra_ln,
            COUNT(DISTINCT CASE WHEN b.dn_ln = 'Dalam Negeri' THEN b.nama_institusi END) AS jumlah_mitra_dn,

            COUNT(DISTINCT a.id_ev) AS jumlah_produktivitas,
            COUNT(DISTINCT CASE WHEN b.dn_ln = 'Luar Negeri' THEN a.id_ev END) AS jumlah_produktivitas_ln,
            COUNT(DISTINCT CASE WHEN b.dn_ln = 'Dalam Negeri' THEN a.id_ev END) AS jumlah_produktivitas_dn,

            SUM(
                CASE
                    WHEN b.dn_ln = 'Luar Negeri' 
                        THEN a.score_impl * {$settingBobot->luar_negeri}
                    ELSE a.score_impl * {$settingBobot->dalam_negeri}
                END
            ) AS jumlah_skor
        ")
            ->whereNull('a.deleted_at')
            ->whereNull('b.deleted_at')
            ->whereNotIn('b.place_state', ['', 'admin'])
            ->where('b.status_tempat', '!=', 'admin')
            ->whereNotNull('b.status_tempat');
        // ->whereNotNull('b.id_lembaga');

        // $dataKerma->whereBetween(DB::raw('CURRENT_DATE'), [DB::raw('b.mulai'), DB::raw('b.selesai')]);
        $dataKerma->where(function ($q) {
            $q->where('b.periode_kerma', 'notknown')->where('b.status_mou', 'Aktif')
                ->orWhere(function ($q2) {
                    $q2->where('b.periode_kerma', 'bydoc')
                        ->whereBetween(
                            DB::raw('CURRENT_DATE'),
                            [DB::raw('b.mulai'), DB::raw('b.selesai')]
                        );
                });
        });
        if ($interval) {
            $dataKerma->where('created_at', '>', DB::raw("DATE_SUB(NOW(), INTERVAL {$interval} YEAR)"));
        }

        $dataKerma->groupByRaw('nama_fakultas');
        $dataKerma->orderByDesc('jumlah_kerma');
        // dd($dataKerma->toSql(), $settingBobot->luar_negeri, $settingBobot->dalam_negeri, $interval);
        return $dataKerma->get();
    }

    private function QueryDetailKermaLembaga($interval = null, $settingBobot, $placeState = null)
    {
        // WHERE id_lmbg = COALESCE(d.place_state, d.id_lmbg)
        $dataKerma = DB::table('kerma_db as b')
            ->leftJoin('kerma_evaluasi as a', 'a.id_mou', '=', 'b.id_mou')
            ->leftJoin('ref_jenis_institusi_mitra as c', 'b.jenis_institusi', '=', 'c.klasifikasi')
            ->leftJoin('ref_jenis_institusi_mitra_old as co', 'b.jenis_institusi', '=', 'co.klasifikasi')
            // ->leftJoin('ref_lembaga_ums as d', 'd.id_lmbg', '=', 'b.id_lembaga')
            // ->leftJoin('ref_lembaga_ums_old as e', 'e.id_lmbg', '=', 'b.id_lembaga')
            // ->leftJoin('ref_lembaga_ums as d', 'd.id_lmbg', '=', 'b.place_state')
            // ->leftJoin('ref_lembaga_ums_old as e', 'e.id_lmbg', '=', 'b.place_state')
            ->leftJoin('ref_lembaga_ums as d', 'd.nama_lmbg', '=', 'b.status_tempat')
            ->leftJoin('ref_lembaga_ums_old as e', 'e.nama_lmbg', '=', 'b.status_tempat')
            ->leftJoin('ref_countries as rc', 'rc.NAME', '=', 'b.negara_mitra')
            ->selectRaw("
            CASE 
                WHEN d.id_lmbg IS NULL THEN
                    (SELECT nama_lmbg FROM ref_lembaga_ums WHERE id_lmbg_old = e.id_lmbg)
                ELSE
                    (SELECT nama_lmbg FROM ref_lembaga_ums WHERE id_lmbg = d.id_lmbg)
            END AS nama_fakultas,

            COUNT(DISTINCT a.id_ev) AS jumlah_produktivitas,
            COUNT(DISTINCT CASE WHEN b.dn_ln = 'Luar Negeri' THEN a.id_ev END) AS jumlah_produktivitas_ln,
            COUNT(DISTINCT CASE WHEN b.dn_ln = 'Dalam Negeri' THEN a.id_ev END) AS jumlah_produktivitas_dn,

            b.id_mou,
            b.status_tempat,
            b.nama_institusi,
            b.kontribusi,
            b.prodi_unit,
            b.dn_ln,
            b.negara_mitra,
            b.wilayah_mitra,
            b.jenis_kerjasama,
            b.jenis_institusi,
            b.mulai,
            b.selesai,
            b.file_mou,
            b.status_mou,
            b.periode_kerma,
            b.awal,
            b.timestamp
        ")
            ->whereNull('a.deleted_at')
            ->whereNull('b.deleted_at')
            ->whereNotIn('b.place_state', ['', 'admin'])
            ->where('b.status_tempat', '!=', 'admin')
            ->whereNotNull('b.status_tempat');
        // ->whereNotNull('b.id_lembaga');

        // filter aktif (masih berlaku)
        // $dataKerma->whereBetween(DB::raw('CURRENT_DATE'), [DB::raw('b.mulai'), DB::raw('b.selesai')]);
        $dataKerma->where(function ($q) {
            $q->where('b.periode_kerma', 'notknown')->where('b.status_mou', 'Aktif')
                ->orWhere(function ($q2) {
                    $q2->where('b.periode_kerma', 'bydoc')
                        ->whereBetween(
                            DB::raw('CURRENT_DATE'),
                            [DB::raw('b.mulai'), DB::raw('b.selesai')]
                        );
                });
        });

        // filter interval tahun
        if ($interval) {
            $dataKerma->where('b.timestamp', '>', DB::raw("DATE_SUB(NOW(), INTERVAL {$interval} YEAR)"));
        }

        // filter berdasarkan alias (nama_fakultas) → pakai HAVING
        if ($placeState) {
            $dataKerma->havingRaw('nama_fakultas = ?', [$placeState]);
        }

        $dataKerma->groupBy([
            'b.id_mou',
            'b.status_tempat',
            'b.nama_institusi',
            'b.kontribusi',
            'b.prodi_unit',
            'b.dn_ln',
            'b.negara_mitra',
            'b.wilayah_mitra',
            'b.jenis_kerjasama',
            'b.jenis_institusi',
            'b.mulai',
            'b.selesai',
            'b.file_mou',
            'b.status_mou',
            'b.periode_kerma',
            'b.awal',
            'b.timestamp',
            'd.place_state',
            'd.id_lmbg',
            'e.id_lmbg',
        ]);


        // urutkan berdasarkan created_at
        $dataKerma->orderByDesc('jumlah_produktivitas', 'b.created_at');
        // dd($dataKerma->toSql(), $dataKerma->getBindings());
        return $dataKerma->get();
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

    protected function applyRoleFilter($query, $role, $username, $placeState, $tanggalNull)
    {
        $user = Auth::user();
        switch ($role) {
            case 'user':
                $query->where('add_by', $username)
                    ->whereNot('tgl_verifikasi_kaprodi', $tanggalNull)
                    ->whereNot('tgl_verifikasi_kabid', $tanggalNull)
                    ->where('tgl_verifikasi_user', $tanggalNull);

                $query->orwhere(function ($q) use ($tanggalNull) {
                    $q->where('status_verify_kaprodi', '=', '0')
                        ->where('tgl_selesai', $tanggalNull);
                });
                $query->orwhere(function ($q) use ($tanggalNull) {
                    $q->where('status_verify_admin', '=', '0')
                        ->where('stats_kerma', 'Ajuan Baru')
                        ->where('tgl_selesai', $tanggalNull);
                });
                $query->orwhere(function ($q) use ($tanggalNull) {
                    $q->where('status_verify_publish', '=', '0')
                        ->where('tgl_selesai', $tanggalNull);
                });

                $query->orwhere(function ($q) use ($username, $tanggalNull) {
                    $q->where('tgl_verifikasi_kaprodi', '!=', $tanggalNull)
                        ->where('tgl_verifikasi_kabid', '!=', $tanggalNull)
                        ->where('tgl_verifikasi_user', '!=', $tanggalNull)
                        ->where('ttd_by', 'Pengusul')
                        ->where('add_by', $username)
                        ->where('tgl_selesai', $tanggalNull);

                    $q->where(function ($q) use ($tanggalNull) {
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


                break;

            case 'verifikator':
                // $query->where('place_state', $placeState)
                $superunit = RefLembagaUMS::where('nama_lmbg', $user->status_tempat)->first()->namasuper ?? null;
                $arrUnit = [$user->status_tempat];
                if ($superunit != null) {
                    $arrUnit = [$user->status_tempat, $superunit];
                }

                $query->whereIn('status_tempat', $arrUnit)
                    ->where('tgl_verifikasi_kaprodi', $tanggalNull);
                break;

            case 'admin':
                $query->where(function ($q) use ($tanggalNull) {
                    $q->where(function ($sub) use ($tanggalNull) {
                        $sub->whereNot('tgl_verifikasi_kaprodi', $tanggalNull)
                            ->where('tgl_verifikasi_kabid', $tanggalNull);
                    })->orWhere(function ($sub) use ($tanggalNull) {
                        $sub->whereNot('tgl_verifikasi_kabid', $tanggalNull)
                            ->whereNot('tgl_verifikasi_user', $tanggalNull)
                            ->whereNot('tgl_req_ttd', $tanggalNull)
                            ->where('tgl_verifikasi_publish', $tanggalNull);
                    });
                });
                $query->where('ttd_by', 'BKUI');
                break;
        }
    }
}
