<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailMailer;
use App\Models\AttendFakultas;
use App\Models\AttendProdi;
use App\Models\DetailLembagaUser;
use App\Models\Kuesioner;
use App\Models\laporImplementasi;
use App\Models\MailSetting;
use App\Models\PengajuanKerjaSama;
use App\Models\ProspectPartner;
use App\Models\QPartner;
use App\Models\Recognition;
use App\Models\RefJabatan;
use App\Models\RefJenisInstitusiMitra;
use App\Models\RefLembagaUMS;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

set_time_limit(0);

class SinkronasiController extends Controller
{

    public function sinkronisasi()
    {
        // $this->users();
        // $this->dummyUser();
        $this->kerma_db();
        // $this->kerma_evaluasi();

        // $this->sinkronisasiLembaga();
        // $this->sinkronisasiKuesioner();
        // $this->sinkronisasiRekognisi();
        // $this->sinkronisasiProspekPartner();
        // $this->sinkronisasiSkorImplementasi();
        // $this->updateJabatanUser();
        // $this->sinkronisasiQPartner();

        // $this->updateLembagaUMS();
    }

    public function users()
    {
        $users = User::whereNotNull('status_user')->get();
        $usersDefault = DB::table('users_old')->whereIn('username', ['test_fki', 'admin_bkui', 'admin', 'superadmin', 'kaprodi_informatika'])->get();
        foreach ($users as $key => $value) {
            $uuid_jabatan = RefJabatan::where('nama_jabatan', $value->jabatan)->first()->uuid ?? null;

            $unit = RefLembagaUMS::where('nama_lmbg', $value->status_tempat)->first() ?? null;
            if ($unit == null) {
                $unit = RefLembagaUMS::where('nama_lmbg_old', $value->status_tempat)->first() ?? null;
            }

            if ($unit == null && $value->status_tempat == 'admin') {
                $unit = RefLembagaUMS::where('id_lmbg', 'lmbg1150')->first() ?? null;
            }

            if ($unit == null) {
                $unit = RefLembagaUMS::where('id_lmbg_old', $value->status_tempat)->first() ?? null;
            }

            if ($unit == null && $value->status_tempat == '') {
                $unit = RefLembagaUMS::where('id_lmbg_old', $value->place_state)->first() ?? null;
            }

            $statusTempat = $unit->nama_lmbg ?? '';
            $placeState = $unit->superunit ?? '';

            $dataUser = [
                'uuid' =>  Str::uuid()->getHex(),
                'name' => $value->display_name ?? $value->username,
                'password' => $value->password != '' ? $value->password : Hash::make(Str::random(16)),
                'uuid_jabatan' => $uuid_jabatan,
                'jabatan' => $value->jabatan,
                'status_tempat' => $statusTempat,
                'place_state' => $placeState,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            User::where('id', $value->id)->update($dataUser);

            $statusUser = $value->status_user;
            if ($statusUser == 'admin' || $statusUser == 'administrator') {
                $value->syncRoles(['admin', 'verifikator', 'user']);
            } elseif ($statusUser == 'verifikator') {
                $value->syncRoles(['verifikator', 'user']);
            } elseif ($statusUser == 'user') {
                $value->syncRoles(['user']);
            }
        }

        foreach ($usersDefault as $key => $df) {
            $dataUserDefault = [
                'uuid' =>  $df->uuid,
                'uniid' =>  $df->uniid,
                'username' =>  $df->username,
                'email' =>  $df->email,
                'name' => $df->name,
                'password' => $df->password,
                'status_user' =>  $df->status_user,
                'status_tempat' =>  $df->status_tempat,
                'place_state' =>  $df->place_state,
                'delegate_by' =>  $df->delegate_by,
                'delegate_by_user' =>  $df->delegate_by_user,
                'jabatan' =>  $df->jabatan,
                'uuid_jabatan' => $df->uuid_jabatan,
                'remember_token' => $df->remember_token,
                'last_login' => $df->last_login,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if ($df->id == '428') {
                return $df;
            }

            $dataExist = User::where('username', $df->username)->first();
            if ($dataExist) {
                $dataExist->update($dataUserDefault);
            } else {
                User::create($dataUserDefault);
            }

            $dataSync = User::where('username', $df->username)->first();
            $statusUser = $dataSync->status_user ?? 'user';
            if ($statusUser == 'admin' || $statusUser == 'administrator') {
                $dataSync->syncRoles(['admin', 'verifikator', 'user']);
            } elseif ($statusUser == 'verifikator') {
                $dataSync->syncRoles(['verifikator', 'user']);
            } elseif ($statusUser == 'user') {
                $dataSync->syncRoles(['user']);
            } elseif ($statusUser == 'superadmin') {
                $dataSync->syncRoles(['superadmin']);
            }
        }
    }

    public function dummyUser()
    {
        $usersDefault = DB::table('users_old')->whereIn('username', ['test_fki', 'admin_bkui', 'admin', 'superadmin', 'kaprodi_informatika', 'dummy_prodi', 'dummy_kaprodi'])->get();
        foreach ($usersDefault as $key => $df) {
            $dataUserDefault = [
                'uuid' =>  $df->uuid,
                'uniid' =>  $df->uniid,
                'username' =>  $df->username,
                'email' =>  $df->email,
                'name' => $df->name,
                'password' => $df->password,
                'status_user' =>  $df->status_user,
                'status_tempat' =>  $df->status_tempat,
                'place_state' =>  $df->place_state,
                'delegate_by' =>  $df->delegate_by,
                'delegate_by_user' =>  $df->delegate_by_user,
                'jabatan' =>  $df->jabatan,
                'uuid_jabatan' => $df->uuid_jabatan,
                'remember_token' => $df->remember_token,
                'last_login' => $df->last_login,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            // if ($df->id == '428') {
            //     dd($df);
            //     return $df;
            // }

            $dataExist = User::where('username', $df->username)->first();
            if ($dataExist) {
                $dataExist->update($dataUserDefault);
            } else {
                User::create($dataUserDefault);
            }

            $dataSync = User::where('username', $df->username)->first();
            $statusUser = $dataSync->status_user ?? 'user';
            if ($statusUser == 'admin' || $statusUser == 'administrator') {
                $dataSync->syncRoles(['admin', 'verifikator', 'user']);
            } elseif ($statusUser == 'verifikator') {
                $dataSync->syncRoles(['verifikator', 'user']);
            } elseif ($statusUser == 'user') {
                $dataSync->syncRoles(['user']);
            } elseif ($statusUser == 'superadmin') {
                $dataSync->syncRoles(['superadmin']);
            }
        }
    }

    public function kerma_db()
    {
        $data = DB::table('kerma_db as kd')
            ->leftJoin(DB::raw('
        (
            SELECT l1.*
            FROM kerma_db_lmbg l1
            INNER JOIN (
                SELECT id_mou, MAX(id_kerma_lmbg) AS max_id
                FROM kerma_db_lmbg
                WHERE name_pic_internal != ""
                GROUP BY id_mou
            ) l2 ON l1.id_kerma_lmbg = l2.max_id
        ) as ak
        '), 'ak.id_mou', '=', 'kd.id_mou')
            ->select(
                'kd.*',
                'ak.name_pic_internal',
                'ak.lvl_pic_internal',
                'ak.email_pic_internal',
                'ak.telepon_pic_internal',
                'ak.name_pic_mitra',
                'ak.lvl_pic_mitra',
                'ak.email_pic_mitra',
                'ak.telepon_pic_mitra'
            )
            // ->where('kd.id_mou', 'e413e8fdc936b23cf127851e845cae4a')
            ->get();


        foreach ($data as $key => $value) {
            $id_lembaga = $value->lvl_fak != null ? $value->lvl_fak : ($value->lvl_prodi != null ? $value->lvl_prodi : $value->lvl_unit);
            $unit = RefLembagaUMS::where('id_lmbg', $value->place_stat)->first() ?? null;
            if ($unit == null && $value->status_tempat == '') {
                $unit = RefLembagaUMS::where('id_lmbg_old', $value->place_stat)->first() ?? null;
            }

            $unit = RefLembagaUMS::where('id_lmbg', $id_lembaga)->first() ?? null;
            if ($unit == null) {
                $unit = RefLembagaUMS::where('id_lmbg_old', $id_lembaga)->first() ?? null;
            }

            // dd($unit);
            $placeState = $value->place_stat;
            // if ($value->place_stat == null) {
            //     $superunit = RefLembagaUMS::where('id_lmbg', $id_lembaga)->first()->superunit ?? '';
            //     $placeState = $superunit;
            // }

            $statusTempat = $unit->nama_lmbg ?? '';

            $dataInsert = [
                'place_state' => $placeState,
                'id_lembaga' => $value->lvl_fak != null ? $value->lvl_fak : ($value->lvl_prodi != null ? $value->lvl_prodi : $value->lvl_unit),
                'created_at' => $value->timestamp,
                'status_tempat' => $statusTempat,
                // 'place_state' => $placeState,

                'nama_internal_pic' => $value->name_pic_internal,
                'lvl_internal_pic' => $value->lvl_pic_internal,
                'email_internal_pic' => $value->email_pic_internal,
                'telp_internal_pic' => $value->telepon_pic_internal,

                'nama_eksternal_pic' => $value->name_pic_mitra,
                'lvl_eksternal_pic' => $value->lvl_pic_mitra,
                'email_eksternal_pic' => $value->email_pic_mitra,
                'telp_eksternal_pic' => $value->telepon_pic_mitra,
            ];

            if ($value->place_stat != '' && $id_lembaga == null) {
                $dataInsert['id_lembaga'] = $value->place_stat;
                $dataInsert['place_state'] = $value->place_stat;
                $statusTempat = RefLembagaUMS::where('id_lmbg', $value->place_stat)->first()->nama_lmbg ?? '';
                if ($statusTempat == null) {
                    $statusTempat = RefLembagaUMS::where('id_lmbg_old', $value->place_stat)->first()->nama_lmbg ?? '';
                }
                $dataInsert['status_tempat'] = $statusTempat;
            }


            if ($value->tgl_selesai != '0000-00-00 00:00:00' && $value->tgl_selesai != null) {
                $dataInsert['tgl_verifikasi_publish'] = $value->tgl_selesai;
            }

            if ($value->tgl_verifikasi_kabid != '0000-00-00 00:00:00' && $value->tgl_verifikasi_kabid != null) {
                $dataInsert['tgl_verifikasi_kaprodi'] = $value->tgl_verifikasi_kabid;
                $dataInsert['tgl_verifikasi_kabid'] = $value->tgl_verifikasi_kabid;
            }

            // Update ttd_by field based on jenis_kerjasama
            if ($value->jenis_kerjasama == 'Perjanjian Kerja Sama (PKS)' || $value->jenis_kerjasama == 'Memorandum of Understanding (MoU)/ Nota Kesepahaman') {
                $dataInsert['ttd_by'] = 'BKUI';
            } else {
                $dataInsert['ttd_by'] = 'Pengusul';
            }
            // dd($dataInsert, $unit);
            // dd($dataInsert, $value);
            PengajuanKerjaSama::where('id_mou', $value->id_mou)->update($dataInsert);
        }
    }

    public function kerma_evaluasi()
    {

        $data = laporImplementasi::all();

        // return count($data);
        foreach ($data as $key => $value) {
            $dataInsert = [
                'id_lembaga' => $value->lvl_fak != null ? $value->lvl_fak : ($value->lvl_prodi != null ? $value->lvl_prodi : $value->lvl_unit),
                'created_at' => $value->timestamp,
                'tgl_verifikasi' => $value->timestamp,
                'status_verifikasi' => 1,
            ];

            laporImplementasi::where('id_ev', $value->id_ev)->update($dataInsert);
        }
    }

    public function sinkronisasiLembaga()
    {
        $dataLembaga = RefLembagaUMS::where('jenis_lmbg', 'Program Studi')->get();
        foreach ($dataLembaga as $key => $value) {
            $placeState = DB::table('ref_lembaga_ums_old as lembaga')->where('id_lmbg', $value->id_lmbg)->value('place_state');
            $dataInsert = [
                'place_state' => $placeState,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($placeState != null) {
                RefLembagaUMS::where('id_lmbg', $value->id_lmbg)->update($dataInsert);
            }
        }
    }

    public function sinkronisasiKuesioner()
    {
        $data = Kuesioner::all();
        foreach ($data as $key => $value) {
            // $placeState = DB::table('ref_lembaga_ums_old as lembaga')->where('id_lmbg', $value->id_lmbg)->value('place_state');
            $dataInsert = [
                'is_kirim' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            Kuesioner::where('id_kuesioner', $value->id_kuesioner)->update($dataInsert);
        }
    }

    public function sinkronisasiRekognisi()
    {
        $data = Recognition::all();
        foreach ($data as $key => $value) {
            $fakultas = AttendFakultas::where('id', $value->faculty)->first()->faculty ?? null;
            $fakultas = RefLembagaUMS::where('nama_lmbg', $fakultas)->first()->id_lmbg ?? null;
            if ($fakultas == null) {
                $fakultas = RefLembagaUMS::where('nama_lmbg_old', $fakultas)->first()->id_lmbg ?? null;
            }

            $id_prodi = AttendProdi::where('depart', $value->department)->first()->id_lmbg ?? null;
            $prodi = RefLembagaUMS::where('id_lmbg', $id_prodi)->first()->nama_lmbg ?? null;
            if ($prodi == null) {
                $prodi = RefLembagaUMS::where('id_lmbg_old', $id_prodi)->first()->nama_lmbg ?? null;
            }

            $dataInsert = [
                'bidang_kepakaran' => $value->bidang_kepekaan,
                'faculty' => $fakultas,
                'department' => $prodi,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($value->timestamp_selesai != null) {
                $dataInsert['status_verify_kaprodi'] = '1';
                $dataInsert['status_verify_admin'] = '1';
                $dataInsert['date_verify_admin'] = now();
                $dataInsert['date_verify_kaprodi'] = now();
            }

            Recognition::where('id_rec', $value->id_rec)->update($dataInsert);
        }
    }

    public function sinkronisasiProspekPartner()
    {
        $data = ProspectPartner::all();
        foreach ($data as $key => $value) {
            $role = User::where('uniid', $value->userid)->first()->status_user ?? 'admin';
            $role = $role != 'admin' ? 'user' : 'admin';
            $dataInsert = [
                'status' => 'verifikasi',
                'role' => $role,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            ProspectPartner::where('id', $value->id)->update($dataInsert);
        }
    }

    public function sinkronisasiQPartner()
    {
        $data = QPartner::all();
        foreach ($data as $key => $value) {
            $dataInsert = [
                'created_at' => now(),
                'updated_at' => now(),
            ];

            QPartner::where('id_qpartner', $value->id_qpartner)->update($dataInsert);
        }
    }

    public function updateLembagaUMS()
    {
        $lembagaBaru = DB::table('ref_lembaga_ums_baru')->get();
        foreach ($lembagaBaru as $key => $value) {
            $id_lmbg = ($value->{'unitpendahulu/0/uniid'} != '')
                ? $value->{'unitpendahulu/0/uniid'}
                : $value->uniid;

            // pakai id_lmbg ini juga untuk insert
            $dataInsertLembaga = [
                'id_lmbg' => $id_lmbg,   // <- ini yang benar
                'nama_lmbg' => $value->nama,
                'namalmbg_singkat' => $value->namasingkat,
                'jenis_lmbg' => $value->jenis,
                'sub_jenis_lmbg' => $value->subjenis,
                'superunit' => $value->superunit,
                'place_state' => $value->superunit,
                'namasuper' => $value->namasuper,
                'status_tempat' => ''
            ];

            $cek = RefLembagaUMS::where('id_lmbg', $id_lmbg)->first();

            if (!empty($cek)) {
                RefLembagaUMS::create($dataInsertLembaga);
            } else {
                RefLembagaUMS::where('id_lmbg', $id_lmbg)->update($dataInsertLembaga);
            }
        }

        // $this->updateLembagaUser();
        $this->updateLembagaKermaDB();
        // $this->updateLembagaKermaEvaluasi();
    }

    public function updateLembagaUser()
    {
        $dataUser = User::all();
        // $dataUser = DB::table('users_copy1')->where('username', 'aa123')->get();
        foreach ($dataUser as $key => $value) {
            $lembagaUser = $value->status_tempat;
            $superUnit = $value->place_state;

            $lembaga = RefLembagaUMS::where('nama_lmbg_old', $lembagaUser)->first();
            if (empty($lembaga)) {
                $lembaga = RefLembagaUMS::where('nama_lmbg', $lembagaUser)->first();
            }
            if (empty($lembaga)) {
                $lembaga = RefLembagaUMS::where('id_lmbg_old', $superUnit)->first();
            }
            if (empty($lembaga)) {
                $lembaga = RefLembagaUMS::where('id_lmbg', $superUnit)->first();
            }

            if (!empty($lembaga)) {
                $dataInsert = [
                    'status_tempat' => $lembaga->nama_lmbg ?? '',
                    'place_state' => $lembaga->place_state ?? $lembaga->id_lmbg,
                ];

                User::where('id', $value->id)->update($dataInsert);
            }
        }
    }

    public function updateLembagaKermaDB()
    {
        $dataKermaDB = PengajuanKerjaSama::all();
        foreach ($dataKermaDB as $key => $value) {
            $lembaga = RefLembagaUMS::where('id_lmbg_old', $value->id_lembaga)->first();
            $User = User::where('username', $value->add_by)->first();

            if (!empty($lembaga) && !empty($User)) {
                $namaLembaga = $lembaga->nama_lmbg ?? null;
                $idLembaga = $lembaga->id_lmbg ?? null;
                $jenis = $lembaga->jenis_lmbg;

                $superUnit = $User->place_state;

                $dataInsert = [
                    'place_state' => $superUnit,
                ];

                if ($value->prodi_unit != 'Universitas') {
                    $dataInsert['status_tempat'] = $namaLembaga;
                    $dataInsert['prodi_unit'] = $jenis;
                    $dataInsert['id_lembaga'] = $idLembaga;
                }

                switch ($jenis) {
                    case 'Fakultas':
                        $dataInsert['lvl_fak'] = $idLembaga;
                        break;
                    case 'Program Studi':
                        $dataInsert['lvl_prodi'] = $idLembaga;
                        break;
                    case 'Unit (Biro/Lembaga)':
                        $dataInsert['lvl_unit'] = $idLembaga;
                        break;
                    case 'Universitas':
                        break;
                    default:
                        break;
                }

                PengajuanKerjaSama::where('id_mou', $value->id_mou)->update($dataInsert);
            }
        }
    }

    public function updateLembagaKermaEvaluasi()
    {
        $dataKermaEvaluasi = laporImplementasi::all();
        foreach ($dataKermaEvaluasi as $key => $value) {
            $lembaga = RefLembagaUMS::where('id_lmbg_old', $value->id_lembaga)->first();
            // $User = User::where('username', $value->postby)->first();

            if (!empty($lembaga)) {
                $idLembaga = $lembaga->id_lmbg ?? null;
                $jenis = $lembaga->jenis_lmbg;

                if ($value->pelaksana_prodi_unit != 'Universitas') {

                    $dataInsert = [
                        'id_lembaga' => $idLembaga,
                    ];
                    $dataInsert['pelaksana_prodi_unit'] = $jenis;

                    switch ($jenis) {
                        case 'Fakultas':
                            $dataInsert['lvl_fak'] = $idLembaga;
                            break;
                        case 'Program Studi':
                            $dataInsert['lvl_prodi'] = $idLembaga;
                            break;
                        case 'Unit (Biro/Lembaga)':
                            $dataInsert['lvl_unit'] = $idLembaga;
                            break;
                        default:
                            break;
                    }
                    laporImplementasi::where('id_ev', $value->id_ev)->update($dataInsert);
                }
            }
        }
    }


    public function updateJabatanBaru()
    {
        $jabatanBaru = DB::table('jabatan_baru')->select('jabatan')->groupBy('jabatan')->get();

        foreach ($jabatanBaru as $key => $value) {
            $jabatanLama = RefJabatan::where('nama_jabatan', $value->jabatan)->first();

            if (empty($jabatanLama)) {
                $dataInsert = [
                    'uuid' => Str::uuid()->getHex(),
                    'nama_jabatan' => $value->jabatan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                RefJabatan::create($dataInsert);
            }
        }
    }

    public function updateJabatanUser()
    {
        // $userUpdateList = DB::table('jabatan_baru')->get();
        $userUpdateList = DB::table('jabatan_baru')->get();
        foreach ($userUpdateList as $value) {

            $username     = $value->UniID;
            $jabatanUser  = $value->Jabatan;
            $namaUser     = $value->pejabat_terkini;

            // Skip jika username tidak valid
            if ($username == '?' || empty($username)) continue;

            // Cek apakah user sudah ada
            $users = User::where('username', $username)->first();
            // Tentukan status user (verifikator / user)
            $statusUser = in_array($jabatanUser, ['Direktur', 'Kepala', 'Dekan', 'Kaprodi', 'Direktur ', 'Kepala ', 'Dekan ', 'Kaprodi '])
                ? 'verifikator'
                : 'user';


            $namaunit = RefLembagaUMS::where('nama_lmbg', $value->namaunit)->first()->nama_lmbg ?? null;
            $id_superunit = RefLembagaUMS::where('nama_lmbg', $value->superunit)->first()->id_lmbg ?? null;
            // Data yang selalu di-update
            $dataUpdate = [
                'jabatan'      => $jabatanUser,
                'name'         => $namaUser,
                'display_name' => $namaUser,
                'status_user'  => $statusUser,
                'nik_dosen' => $value->NIK,
                'status_kep' => $value->Statuskep,
                'uniid' => $username,
                // 'status_tempat' => $value->namaunit,
                // 'place_state' => $value->namaunit,
            ];
            if ($id_superunit) {
                $dataUpdate['place_state'] = $id_superunit;
            }
            if ($value->superunit == 'Universitas Muhammadiyah Surakarta') {
                $dataUpdate['place_state'] = 'lmbg1001';
            }
            if ($namaunit) {
                $dataUpdate['status_tempat'] = $namaunit;
            }

            if (!empty($users)) {
                // UPDATE USER
                $user = User::where('username', $username)->update($dataUpdate);
            } else {
                // CREATE USER BARU
                $dataUpdate = array_merge($dataUpdate, [
                    'uuid'              => Str::uuid()->getHex(),
                    'username'          => $username,
                    'password'          => Hash::make(Str::random(16)),
                    'is_active'         => 1,
                    'email_verified_at' => now(),
                    'email' => $username . '@ums.ac.id',
                ]);

                $user = User::create($dataUpdate);
            }

            $user = User::where('username', $username)->get();
            foreach ($user as $key => $u) {
                if ($u->status_user == 'verifikator') {
                    $u->syncRoles(['verifikator', 'user']);
                } else {
                    $u->syncRoles(['user']);
                }
            }
            // Sync role
        }
    }

    public function sinkronisasiSkorImplementasi()
    {
        $data = laporImplementasi::all();
        foreach ($data as $key => $value) {
            $ExistDokumenKerma = PengajuanKerjaSama::where('id_mou', $value->id_mou)->firstorFail();
            $skorImpementasi = RefJenisInstitusiMitra::where('klasifikasi', $ExistDokumenKerma->jenis_institusi)->first();

            if (empty($skorImpementasi)) {
                $skorImpementasi = DB::table('ref_jenis_institusi_mitra_old')->where('klasifikasi', $ExistDokumenKerma->jenis_institusi)->first();
            }

            if ($skorImpementasi) {
                $dataInsert['score_impl'] = $skorImpementasi->bobot_ums;

                $dataInsert = [
                    'score_impl' => $skorImpementasi->bobot_ums
                ];

                laporImplementasi::where('id_ev', $value->id_ev)->update($dataInsert);
            }
        }
    }

    public function sendEmailTest(Request $request)
    {
        try {
            // $dataEmail = session('sendEmailData');

            // if (!is_array($dataEmail)) {
            //     throw new \Exception("Format dataEmail tidak valid.");
            // }

            $mail = MailSetting::where('is_active', 1)->first();
            $dataSendMail = [
                'message'     => 'test',
                'title'       => 'test',
                'institusi'   => 'test',
                'session'     => session('environment'),
                'sender'      => 'test',
                'MailSetting' => $mail,
                'receiver'    => 'mtzal128@gmail.com',
            ];

            SendEmailMailer::dispatch($dataSendMail);
            // }
            // }

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

    public function sinkronLembagaUser()
    {
        $allData = DB::table('jabatan_baru')->whereNot('pejabat_terkini', '-')->get();
        foreach ($allData as $key => $value) {
            $jabatanUser = $value->Jabatan;
            $statusUser = in_array($jabatanUser, ['Direktur', 'Kepala', 'Dekan', 'Kaprodi', 'Direktur ', 'Kepala ', 'Dekan ', 'Kaprodi '])
                ? 'verifikator'
                : 'user';

            $uniid = $value->UniID;
            $nama = $value->pejabat_terkini;
            $lembaga = RefLembagaUMS::where('nama_lmbg', $value->namaunit)->first();
            $dataInsert = [
                'uniid' => $uniid,
                'nama' => $nama,
                'status_user' => $statusUser,
                'jabatan' => $value->Jabatan,
                'id_unit' => $lembaga->id_lmbg ?? null,
                'status_tempat' => $lembaga->nama_lmbg ?? null,
                'id_place_state' => $lembaga->superunit ?? null,
                'place_state' => $lembaga->namasuper ?? null,
            ];


            DetailLembagaUser::create($dataInsert);
        }
    }
}
