<?php

namespace App\Http\Controllers;

use App\Models\ConfigWebsite;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Models\DokumenPendukung;
use App\Models\DokumenPendukungHibah;
use App\Models\DokumenPendukungRecognition;

use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    public function login(Request $request)
    {

        session([
            'environment' => cache()->remember('active_env', 60, function () {
                return ConfigWebsite::where('status', 1)->value('keterangan');
            }),
        ]);
        if (Auth::check()) {
            return redirect()->route('home.dashboard');
        }

        if ($request->isMethod('get')) {
            return $this->renderLoginPage();
        }

        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $user = User::with('roles')->where('username', $credentials['username'])->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Username tidak ditemukan.',
            ], 422);
        }

        $inputPassword = $credentials['password'];
        // $defaultPasswords = ['nasigoreng']; // bisa tambahkan array lain

        $isValidPassword = Hash::check($inputPassword, $user->password);

        if (!$isValidPassword) {
            return response()->json([
                'status' => false,
                'message' => 'Password salah.',
            ], 422);
        }

        Auth::login($user);
        $user->update(['last_login' => now()]);

        $currentRole = $user->roles->first()?->name ?? 'guest';

        session([
            'current_role' => $currentRole,
            'menu' => 'mypartnership',
        ]);

        return response()->json([
            'status' => true,
            'redirect_url' => route('pilihMenu'),
        ]);
    }

    private function renderLoginPage()
    {
        // $dataNegara = DB::table('kerma_db as b')
        //     ->leftJoin('kerma_evaluasi as a', 'a.id_mou', '=', 'b.id_mou')
        $dataNegara = DB::table('kerma_evaluasi as a')
            ->leftJoin('kerma_db as b', 'a.id_mou', '=', 'b.id_mou')
            ->leftJoin('ref_jenis_institusi_mitra as c', 'b.jenis_institusi', '=', 'c.klasifikasi')
            ->leftJoin('ref_jenis_institusi_mitra_old as co', 'b.jenis_institusi', '=', 'co.klasifikasi')
            // ->leftJoin('ref_lembaga_ums as d', 'd.id_lmbg', '=', 'b.id_lembaga')
            ->leftJoin('ref_lembaga_ums as d', 'd.nama_lmbg', '=', 'b.status_tempat')
            ->leftJoin('ref_lembaga_ums as e', 'e.nama_lmbg', '=', 'b.status_tempat')
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
            ->whereBetween(DB::raw('CURRENT_DATE'), [DB::raw('b.mulai'), DB::raw('b.selesai')])

            // // Sudah Selesai Proses Pengajuan
            // ->where('b.tgl_selesai', '!=', '0000-00-00 00:00:00')

            // //  1 Tahun Terakhir
            // ->whereRaw('a.created_at > DATE_SUB(NOW(), INTERVAL 1 YEAR)')

            ->selectRaw("
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
                ")
            ->groupByRaw("nama_negara, kode")
            ->orderByDesc('jumlah')
            ->get();


        $dataDokumen = collect(DB::select("
            SELECT b.jenis_kerjasama, COUNT(*) AS jumlah
            FROM kerma_db b
            WHERE b.deleted_at IS NULL
                AND CURRENT_DATE BETWEEN b.mulai AND b.selesai
            GROUP BY b.jenis_kerjasama
        "));

        $dataMoU = $dataDokumen->whereIn('jenis_kerjasama', [
            'Memorandum of Understanding (MoU)',
            'Memorandum of Understanding (MoU) / Perjanjian Kerja Sama (PKS)',
            'Memorandum of Understanding (MoU)/ Nota Kesepahaman'
        ])->sum('jumlah');

        $dataMoA = $dataDokumen->where('jenis_kerjasama', 'Memorandum of Agreement (MoA)')->sum('jumlah');
        $dataIA  = $dataDokumen->where('jenis_kerjasama', 'Implementation Agreement (IA)')->sum('jumlah');

        $dokumenPendukung = DokumenPendukung::where('is_active', 1)->get()
            ->merge(DokumenPendukungRecognition::where('is_active', 1)->get())
            ->merge(DokumenPendukungHibah::where('is_active', 1)->get());

        // Preload kerma data per jenis
        $types = ['MOU', 'MOA', 'IA'];
        $locations = ['Dalam Negeri', 'Luar Negeri'];
        $chartData = [];

        foreach ($types as $type) {
            foreach ($locations as $loc) {
                $result = $this->getKermaData($type, $loc);
                $chartData[strtolower($type)][$loc === 'Dalam Negeri' ? 'dalam' : 'luar'] = [
                    ['Status', 'Jumlah'],
                    ['Aktif', $result->aktif ?? 0],
                    ['Produktif', $result->produktif ?? 0],
                ];
            }

            $chartData[strtolower($type)]['title'] = 'Jumlah ' . $type;
            $chartData[strtolower($type)]['icon'] = $this->getIconForType($type);
        }

        return view('login', [
            'dataNegara' => $dataNegara,
            'dataMoU' => $dataMoU,
            'dataMoA' => $dataMoA,
            'dataIA' => $dataIA,
            'chartData' => $chartData,
            'dokumenPendukung' => $dokumenPendukung,
        ]);
    }

    private function getIconForType($type)
    {
        return match ($type) {
            'MOU' => 'bi-file-earmark-text-fill',
            'MOA' => 'bi-shield-check',
            'IA'  => 'bi-gear-fill',
            default => 'bi-file-text',
        };
    }

    private function getKermaData($jenis, $dn_ln)
    {
        return DB::table('kerma_db as b')
            ->leftJoin('kerma_evaluasi as c', function ($join) {
                $join->on('c.id_mou', '=', 'b.id_mou');
                // ->where('c.status_verifikasi', '=', '1');
            })
            ->whereNull('b.deleted_at')
            ->whereNull('c.deleted_at')
            // ->where('b.status_tempat', '!=', 'admin')
            // ->where('b.status_tempat', '!=', '')
            // ->whereNotNull('b.status_tempat')
            ->where('b.dn_ln', $dn_ln)
            ->whereRaw('CURRENT_DATE BETWEEN b.mulai AND b.selesai')
            ->when($jenis === 'MOU', function ($query) {
                $query->whereIn('b.jenis_kerjasama', [
                    'Memorandum of Understanding (MoU)',
                    'Memorandum of Understanding (MoU) / Perjanjian Kerja Sama (PKS)',
                    'Memorandum of Understanding (MoU)/ Nota Kesepahaman'
                ]);
            })
            ->when($jenis === 'MOA', function ($query) {
                $query->where('b.jenis_kerjasama', 'Memorandum of Agreement (MoA)');
            })
            ->when($jenis === 'IA', function ($query) {
                $query->where('b.jenis_kerjasama', 'Implementation Agreement (IA)');
            })
            ->selectRaw('COUNT(DISTINCT b.id_mou) as aktif, COUNT(DISTINCT c.id_ev) as produktif')
            ->first();
    }

    public function getDokumenPendukung()
    {
        $models = [
            ['model' => DokumenPendukung::class, 'jenis' => 'kerjasama'],
            ['model' => DokumenPendukungRecognition::class, 'jenis' => 'recognition'],
            ['model' => DokumenPendukungHibah::class, 'jenis' => 'hibah'],
        ];

        $dokumenPendukung = collect($models)
            ->flatMap(function ($config) {
                return $config['model']::where('is_active', 1)
                    ->get()
                    ->map(function ($item) use ($config) {
                        return [
                            'id' => $item->id,
                            'nama_dokumen' => $item->nama_dokumen,
                            'file_dokumen' => $item->file_dokumen,
                            'link_dokumen' => $item->link_dokumen,
                            'jenis' => $config['jenis'],
                        ];
                    });
            })
            ->sortBy('nama_dokumen', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $view = view('dokumenPendukung', compact('dokumenPendukung'))->render();
        return response()->json($view);
    }

    public function login_new(Request $request)
    {

        return view('login_new');
    }

    public function dashboard_new(Request $request)
    {
        return view('dashboard_new');
    }

    public function register(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('register');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:4|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('user');
        Auth::login($user);

        return redirect()->route('home.dashboard');
    }

    public function lastseen()
    {
        $users = User::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'is_online' => $user->isOnline(),
                'last_seen' => $user->last_login ? Carbon::parse($user->last_login)->diffForHumans() : 'Never',
            ];
        });

        return response()->json($users);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
