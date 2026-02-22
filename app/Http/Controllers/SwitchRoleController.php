<?php

namespace App\Http\Controllers;

use App\Models\AjuanHibah;
use App\Models\laporImplementasi;
use App\Models\PengajuanKerjaSama;
use App\Models\ProspectPartner;
use App\Models\Recognition;
use App\Models\RefLembagaUMS;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SwitchRoleController extends Controller
{
    public function setRole(Request $request)
    {
        $role = $request->role;

        if (session('menu') == 'mypartnership') {
            $url = route('home.dashboard');
        } else if (session('menu') == 'recognition') {
            $url = route('recognition.home');
        } else if (session('menu') == 'partner') {
            $url = route('potential_partner.home');
        } else if (session('menu') == 'hibah') {
            $url = route('hibah.home');
        }

        if (Auth::user()->hasRole($role)) {
            Session::put('current_role', $role);
            return response()->json(['status' => 'success', 'role' => $role, 'redirect_url' => $url]);
        }

        return response()->json(['status' => 'error'], 403);
    }

    public function setMenu(Request $request)
    {
        $menu = $request->menu;

        if ($menu == 'mypartnership') {
            $url = route('home.dashboard');
        } else if ($menu == 'recognition') {
            $url = route('recognition.home');
        } else if ($menu == 'partner') {
            $url = route('potential_partner.home');
        } else if ($menu == 'hibah') {
            $url = route('hibah.home');
        }

        Session::put('menu', $menu);
        return response()->json(['status' => 'success', 'menu' => $menu, 'redirect_url' => $url]);
    }

    public function pilihMenu(Request $request)
    {
        $data = [
            'notif_kerjasama' => $this->notifikasiKerma(),
            'notif_rekognisi' => $this->notifikasiRekognisi(),
            'notif_partner' => $this->notifikasiPartner(),
            'notif_hibah' => $this->notifikasiHibah(),
        ];
        return view('pilih_menu', $data);
    }

    private function notifikasiKerma()
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

        return $totalNotifikasi;
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

                $query->orwhere(function ($q) use ($tanggalNull, $username) {
                    $q->where('status_verify_kaprodi', '=', '0')
                        ->where('tgl_selesai', $tanggalNull)
                        ->where('add_by', $username);
                });
                $query->orwhere(function ($q) use ($tanggalNull, $username) {
                    $q->where('status_verify_admin', '=', '0')
                        ->where('stats_kerma', 'Ajuan Baru')
                        ->where('tgl_selesai', $tanggalNull)
                        ->where('add_by', $username);
                });
                $query->orwhere(function ($q) use ($tanggalNull, $username) {
                    $q->where('status_verify_publish', '=', '0')
                        ->where('tgl_selesai', $tanggalNull)
                        ->where('add_by', $username);
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
                $superunit = RefLembagaUMS::where('nama_lmbg', $user->status_tempat)->first()->namasuper ?? null;
                $arrUnit = [$user->status_tempat];
                // if ($superunit != null) {
                //     $arrUnit = [$user->status_tempat, $superunit];
                // }

                $query->whereIn('status_tempat', $arrUnit)
                    ->where('tgl_verifikasi_kaprodi', $tanggalNull);

                $query->orwhere(function ($q) {
                    $q->where('add_by', Auth::user()->username)
                        ->where('status_verify_admin', '0');
                });
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
            $id_lmbg_dekan = RefLembagaUMS::where('nama_lmbg', $user->status_tempat)->first()->id_lmbg;
            if ($id_lmbg_dekan == null) {
                $id_lmbg_dekan = RefLembagaUMS::where('nama_lmbg_old', $user->status_tempat)->first()->id_lmbg;
            }
            $queryAjuan->where('tbl_ajuan_hibah.place_state', $id_lmbg_dekan);
        }

        // dd($queryAjuan->tosql(), $queryAjuan->getBindings(), $currentRole);
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

        // $dataUser = AjuanHibah::select(
        //     'tbl_ajuan_hibah.*',
        //     'tbl_laporan_hibah.id_laporan_hibah',
        //     'ref_jenis_hibah.dl_proposal',
        //     'ref_jenis_hibah.dl_laporan'
        // )
        //     ->leftJoin('tbl_laporan_hibah', 'tbl_laporan_hibah.id_hibah', '=', 'tbl_ajuan_hibah.id_hibah')
        //     ->leftJoin('ref_jenis_hibah', 'ref_jenis_hibah.id', '=', 'tbl_ajuan_hibah.jenis_hibah')
        //     ->whereIn('tbl_ajuan_hibah.id_hibah', $arrId)
        //     ->get();

        // return response()->json([
        //     'hibah_mobile' => $jumlahNotif ? '<span class="badge rounded-pill bg-danger">' . $jumlahNotif . '</span>' : '',
        //     'hibah' => $jumlahNotif ? '<span class="badge rounded-pill bg-danger position-absolute start-100 translate-middle" style="top: 20px;">' . $jumlahNotif . '</span>' : '',
        //     'menu_hibah' => $jumlahNotif ? '<span class="badge rounded-pill bg-danger position-absolute translate-middle" style="top: 20px;left:90%!important;">' . $jumlahNotif . '</span>' : '',
        //     'dataUser' => $dataUser->isNotEmpty(),
        //     'dataModal' => view('hibah/dataUserNotif', compact('dataUser'))->render(),
        // ]);
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

        // $jumlahNotif = (int) $notifCounts->user_notif + (int) $notifCounts->verifikator_notif + (int) $notifCounts->admin_notif;

        // $dataNotifikasi = [
        //     'rekognisi'          => $jumlahNotif > 0 ? '<span class="badge rounded-pill bg-danger position-absolute start-100 translate-middle" style="top: 20px;">' . $jumlahNotif . '</span>' : '',
        //     'rekognisi_mobile'   => $jumlahNotif > 0 ? '<span class="badge rounded-pill bg-danger ">' . $jumlahNotif . '</span>' : '',
        //     'menu_rekognisi'     => $jumlahNotif > 0 ? '<span class="badge rounded-pill bg-danger position-absolute translate-middle" style="top: 20px;left:90%!important;">' . $jumlahNotif . '</span>' : '',
        //     'rekognisi_user'     => $notifCounts->user_notif > 0 ? '<span class="badge rounded-pill bg-danger position-absolute start-100 translate-middle" style="top: 20px;">' . $notifCounts->user_notif . '</span>' : '',
        //     'rekognisi_verifikator' => $notifCounts->verifikator_notif > 0 ? '<span class="badge rounded-pill bg-danger position-absolute start-100 translate-middle" style="top: 20px;">' . $notifCounts->verifikator_notif . '</span>' : '',
        //     'rekognisi_admin'    => $notifCounts->admin_notif > 0 ? '<span class="badge rounded-pill bg-danger position-absolute start-100 translate-middle" style="top: 20px;">' . $notifCounts->admin_notif . '</span>' : '',
        //     'dataUser'           => false, // default
        // ];

        // return response()->json($dataNotifikasi);
    }

    public function notifikasiPartner()
    {
        $username = Auth::user()->username;

        $notifCounts = ProspectPartner::selectRaw("
        SUM(
            CASE 
                WHEN tbl_prospect_partner.status IS NULL
                     OR tbl_prospect_partner.status != 'verifikasi'
                THEN 1 ELSE 0 END
        ) AS notif_admin,

        SUM(
            CASE 
                WHEN tbl_prospect_partner.revisi IS NOT NULL
                     AND tbl_prospect_partner.status != 'verifikasi'
                     AND tbl_prospect_partner.date_verify IS NOT NULL
                     AND tbl_prospect_partner.userid = '$username'
                THEN 1 ELSE 0 END
        ) AS notif_user
        ")->first();

        $jumlahNotif = session('current_role') == 'admin' ? $notifCounts->notif_admin : $notifCounts->notif_user;

        return $jumlahNotif;

        //     $dataNotifikasi = [
        //         'partner_admin_mobile'   => $notifCounts->notif_admin > 0 ? '<span class="badge rounded-pill bg-danger ">' . $notifCounts->notif_admin . '</span>' : '',
        //         'partner_user_mobile'   => $notifCounts->notif_user > 0 ? '<span class="badge rounded-pill bg-danger ">' . $notifCounts->notif_user . '</span>' : '',
        //         'partner_user'     => $notifCounts->notif_user > 0 ? '<span class="badge rounded-pill bg-danger position-absolute start-100 translate-middle" style="top: 20px;">' . $notifCounts->notif_user . '</span>' : '',
        //         'partner_admin'     => $notifCounts->notif_admin > 0 ? '<span class="badge rounded-pill bg-danger position-absolute start-100 translate-middle" style="top: 20px;">' . $notifCounts->notif_admin . '</span>' : '',
        //     ];

        // return response()->json($dataNotifikasi);
    }
}
