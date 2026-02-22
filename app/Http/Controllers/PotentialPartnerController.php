<?php

namespace App\Http\Controllers;

use App\Models\Kuesioner;
use App\Models\LogActivity;
use App\Models\PengajuanKerjaSama;
use App\Models\ProspectPartner;
use App\Models\SettingPotentialPartner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;

class PotentialPartnerController extends Controller
{
    public function index()
    {
        $lastRecord = ProspectPartner::orderBy('id', 'desc')->limit(5)->get();
        $rangking = ProspectPartner::selectRaw('
                        userid,
                        name_user,
                        SUM(point) as total_point
                    ')
            ->where('status', 'verifikasi')
            ->where('role', 'user')
            ->groupBy('userid', 'name_user')
            ->orderByDesc('total_point')
            ->limit(5)
            ->get();

        $dataNegara = collect(DB::select("SELECT
                    b.name as nama_negara,
                    COUNT(b.name) as jumlah
                FROM
                    tbl_prospect_partner a
                    JOIN ref_countries b ON a.country = b.id
                    GROUP BY
                    b.name"));

        $rewardPoint = $rangking->where('userid', Auth::user()->username)->first();

        $data = [
            'li_active' => 'dashboard',
            'title' => 'Dashboard | Data Mitra Potensial',
            'page_title' => 'Data Mitra Potensial',
            'lastRecord' => $lastRecord ?? null,
            'rewardPoint' => $rewardPoint->total_point ?? 0,
            'rangking' => $rangking,
            'dataNegara' => $dataNegara,
        ];

        return view('potential_partner/index', $data);
    }

    public function tambah()
    {
        $data = [
            'li_active' => 'activity',
            'title' => 'Aktivitas',
            'page_title' => 'Mitra Potensial',
            'country' => DB::table('ref_countries')->get(),
        ];
        session(['partner_key' => Str::random(40)]);

        return view('potential_partner/tambah', $data);
    }

    public function edit($id)
    {
        $partner = ProspectPartner::find($id);
        $data = [
            'li_active' => 'activity',
            'title' => 'Aktivitas',
            'page_title' => 'Mitra Potensial',
            'country' => DB::table('ref_countries')->get(),
            'dataActivity' => $partner,
        ];
        session(['partner_key' => Str::random(40)]);

        if ($partner->status == 'verifikasi') {
            abort(404);
        }

        $data['logFileCN1'] = getLogFile($id, 'cardname1');
        $data['logFileCN2'] = getLogFile($id, 'cardname2');

        return view('potential_partner/edit', $data);
    }

    public function activity()
    {
        $data = [
            'li_active' => 'activity',
            'title' => 'Aktivitas',
            'page_title' => 'Mitra Potensial',
        ];

        return view('potential_partner/activity', $data);
    }

    public function reward()
    {
        $data = [
            'li_active' => 'reward',
            'title' => 'Reward',
            'page_title' => 'Prospective Partner',
        ];

        return view('potential_partner/reward', $data);
    }

    public function profile()
    {
        $data = [
            'li_active' => 'profile',
            'title' => 'Profile',
            'page_title' => 'Prospective Partner',
        ];

        return view('potential_partner/profile', $data);
    }

    public function getDataActivity(Request $request)
    {
        $orderColumnIndex = $request->input('order.0.column');
        $query = ProspectPartner::select(
            'tbl_prospect_partner.*',
            'ref_countries.name as country_name',
        )
            ->join('ref_countries', 'ref_countries.id', '=', 'tbl_prospect_partner.country');

        if ($orderColumnIndex == 0) {
            if (session('current_role') == 'admin') {
                $query->orderByRaw("CASE WHEN tbl_prospect_partner.status = 'verifikasi' THEN 0 ELSE 1 END DESC")
                    ->orderBy('tbl_prospect_partner.created_at', 'asc');
            } else {
                $query
                    ->orderByRaw("
                            CASE 
                                WHEN tbl_prospect_partner.userid = ? THEN 0 
                                ELSE 1 
                            END
                        ", [Auth::user()->username])
                    ->orderByRaw("
                            CASE 
                                WHEN tbl_prospect_partner.status = 'verifikasi' THEN 0 
                                ELSE 1 
                            END
                        ")
                    ->orderBy('tbl_prospect_partner.created_at', 'asc');
            }
        }



        // $query->get();

        return DataTables::of($query)
            ->addIndexColumn()
            ->filterColumn('country_name', function ($query, $keyword) {
                $query->where('ref_countries.name', 'like', '%' . $keyword . '%');
            })
            ->addColumn('action', function ($row) {
                return $row->action_label;
            })
            ->addColumn('status_label', function ($row) {
                return $row->status_label;
            })
            ->rawColumns(['action', 'status_label'])
            ->make(true);
    }

    public function store(Request $request)
    {
        if ($request->partner_key !== session('partner_key') || session('partner_key') == null) {
            return response()->json(['error' => 'Form tidak valid, silakan reload tab terbaru.'], 403);
        }

        if ($request->id) {
            $dataExist = ProspectPartner::where('id', $request->id)->firstorFail();
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required',
                    'occupation' => 'required',
                    'phonenumber' => 'required',
                    'researchint' => 'required',
                    'institution' => 'required',
                    'country' => 'required',
                    // 'website' => 'required',
                    // 'address' => 'required',
                ],
                [
                    'name.required' => 'Nama harus diisi.',
                    'email.required' => 'Email harus diisi.',
                    'occupation.required' => 'Judul Pekerjaan harus diisi.',
                    'phonenumber.required' => 'Nomor HP harus diisi.',
                    'researchint.required' => 'Minat Penelitian harus diisi.',
                    'institution.required' => 'Institusi harus diisi.',
                    'country.required' => 'Negara harus diisi.',
                    // 'website.required' => 'Website harus diisi.',
                    // 'address.required' => 'Alamat harus diisi.',
                ],
            );
        } else {
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required',
                    'occupation' => 'required',
                    'phonenumber' => 'required',
                    'researchint' => 'required',
                    'institution' => 'required',
                    'country' => 'required',
                    // 'website' => 'required',
                    // 'address' => 'required',
                    'cardname1' => 'required|mimes:jpg,jpeg,png|max:5120',
                    'cardname2' => 'nullable|mimes:jpg,jpeg,png|max:5120',
                ],
                [
                    'name.required' => 'Nama harus diisi.',
                    'email.required' => 'Email harus diisi.',
                    'occupation.required' => 'Judul Pekerjaan harus diisi.',
                    'phonenumber.required' => 'Nomor HP harus diisi.',
                    'researchint.required' => 'Minat Penelitian harus diisi.',
                    'institution.required' => 'Institusi harus diisi.',
                    'country.required' => 'Negara harus diisi.',
                    // 'website.required' => 'Website harus diisi.',
                    // 'address.required' => 'Alamat harus diisi.',
                    'cardname1.required' => 'File Kartu Nama A Harus diisi.',
                    'cardname1.max' => 'File Kartu Nama A maksimal 5MB.',
                    'cardname1.mimes' => 'File Kartu Nama A Harus Berformat JPG, JPEG, PNG.',
                    'cardname2.max' => 'File Kartu Nama B maksimal 5MB.',
                    'cardname2.mimes' => 'File Kartu Nama B Harus Berformat JPG, JPEG, PNG.',
                ],
            );
        }


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Generate ID jika tidak ada
            $id = $request->id;
            if ($request->id) {
                $dataExist = ProspectPartner::where('id', $request->id)->firstorfail();
            }
            $dataInsert = [
                'name' => $request->name ?? '',
                'email' => $request->email ?? '',
                'occupation' => $request->occupation ?? '',
                'phonenumber' => $request->phonenumber ?? '',
                'socmed' => $request->socmed ?? '',
                'researchint' => $request->researchint ?? '',
                'institution' => $request->institution ?? '',
                'website' => $request->website ?? '',
                'country' => $request->country ?? '',
                'address' => $request->address ?? '',
                'userid' => Auth::user()->username ?? '',
                'name_user' => Auth::user()->name ?? '',
                'date_add' => date('Y-m-d H:i:s'),
            ];

            if (session('current_role') != 'admin') {
                $dataInsert['role'] = 'user';
            } else {
                $dataInsert['role'] = 'admin';
                $dataInsert['status'] = 'verifikasi';
            }

            $allFiles = [];
            if ($request->hasFile('cardname1')) {
                $cardname1 = $request->file('cardname1');
                $path = "uploads/potential_partner";
                $fileCardName1 = $this->upload_file($cardname1, $path);
                $dataInsert['cardname1'] = $fileCardName1;

                $dtFiles = [
                    'jenis' => 'cardname1',
                    'path' => $fileCardName1
                ];

                $allFiles[] = $dtFiles;
            }

            if ($request->hasFile('cardname2')) {
                $cardname2 = $request->file('cardname2');
                $path = "uploads/potential_partner";
                $fileCardName2 = $this->upload_file($cardname2, $path);
                $dataInsert['cardname2'] = $fileCardName2;

                $dtFiles = [
                    'jenis' => 'cardname2',
                    'path' => $fileCardName2
                ];

                $allFiles[] = $dtFiles;
            }

            // Simpan atau update data
            if ($request->id) {
                if ($dataExist->status == 'revisi') {
                    $dataInsert['status'] = null;
                    $dataInsert['date_verify'] = null;
                    $dataInsert['userid_verify'] = null;
                    $dataInsert['name_user_verify'] = null;
                    $dataInsert['revisi'] = null;
                }
                $insert = ProspectPartner::where('id', $request->id)->update($dataInsert);
                $dataLogketerangan = 'Update';
            } else {
                $dataInsert['role'] = session('current_role');
                $insert = ProspectPartner::create($dataInsert);
                $dataLogketerangan = 'Baru';
            }
            if ($insert) {
                foreach ($allFiles as $key => $value) {
                    $dataLog = [
                        'table' => 'tbl_prospect_partner',
                        'id_table' => $id,
                        'jenis' => $value['jenis'],
                        'path' => $value['path'],
                        'keterangan' => $dataLogketerangan,
                        'add_by' => Auth::user()->username,
                        'role' => session('current_role')
                    ];
                    LogActivity::create($dataLog);
                }

                session()->forget('partner_key');
                DB::commit();
                return response()->json(['status' => true, 'message' => 'Data berhasil disimpan', 'route' => route('potential_partner.activity')], 200);
            }
            return response()->json(['status' => false, 'message' => 'Terdapat Masalah Ketika Penyimpanan'], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $partner = ProspectPartner::where('id', $request->id)->firstOrFail();
            $partner->delete();
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
            $bobotNilai = SettingPotentialPartner::getData();
            $dataInsert = [];

            $dataInsert['status'] = $request->status;
            $dataInsert['revisi'] = $request->revisi;

            $dataInsert['date_verify'] = date('Y-m-d H:i:s');
            $dataInsert['userid_verify'] = Auth::user()->username ?? '';
            $dataInsert['name_user_verify'] = Auth::user()->name ?? '';
            $partner = ProspectPartner::where('id', $request->id)->firstOrFail();

            if ($request->status == 'verifikasi') {
                if ($partner->country != '102') {
                    $dataInsert['point'] = $bobotNilai->poin_ln;
                } else {
                    $dataInsert['point'] = $bobotNilai->poin_dn;
                }
            } else {
                $dataInsert['point'] = 0;
            }

            $partner->update($dataInsert);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data Berhasil di Simpan']);
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
            'dataSetting' => SettingPotentialPartner::getData(),

        ];

        return view('potential_partner/setting', $data);
    }

    public function storeSetting(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'poin_dn' => 'required',
                'poin_ln' => 'required',
            ],
            [
                'poin_dn.required' => 'Poin Dalam Negeri Harus Di Isi.',
                'poin_ln.required' => 'Poin Luar Negeri Harus Di Isi.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Generate ID jika tidak ada
            $id_setting_partner = $request->id_setting_partner;
            $dataSetting = SettingPotentialPartner::where('id_setting_partner', $id_setting_partner)->first();

            $dataInsert = [
                'poin_dn' => $request->poin_dn,
                'poin_ln' => $request->poin_ln,
            ];

            // Simpan atau update data
            if ($id_setting_partner) {
                $dataSetting->update($dataInsert);
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

    public function download_excel(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if (session('current_role') == 'admin') {
            $headers = [
                'No',
                'Nama',
                'Email',
                'Jabatan',
                'No. Telepon',
                'Sosial Media',
                'Bidang Minat Penelitian',
                'Institusi',
                'Website',
                'Negara',
                'Alamat',
                'Link Dokumen 1',
                'Link Dokumen 2',
                'Poin',
                'Username Penginput',
                'Nama Penginput',
                'Status',
                'Tanggal Input',
                'Tanggal Verifikasi',
                'User Verifikator',
                'Nama Verifikator',
                'Revisi',
                'Tanggal Dibuat',
                'Tanggal Diperbarui',
            ];
        } else {
            $headers = [
                'No',
                'Nama',
                'Email',
                'Jabatan',
                'Sosial Media',
                'Bidang Minat Penelitian',
                'Institusi',
                'Website',
                'Negara',
                'Alamat',
                'Link Dokumen 1',
                'Link Dokumen 2',
                'Poin',
                'Username Penginput',
                'Nama Penginput',
                'Status',
                'Tanggal Input',
                'Tanggal Verifikasi',
                'User Verifikator',
                'Nama Verifikator',
                'Revisi',
                'Tanggal Dibuat',
                'Tanggal Diperbarui',
            ];
        }


        // Set header ke baris 1
        foreach ($headers as $i => $header) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($columnLetter . '1', $header);
        }

        // Data
        $query = ProspectPartner::select(
            'tbl_prospect_partner.*',
            'ref_countries.name as country_name',
        )
            ->join('ref_countries', 'ref_countries.id', '=', 'tbl_prospect_partner.country');


        if (session('current_role') == 'admin') {
            $query->orderByRaw("CASE WHEN tbl_prospect_partner.status = 'verifikasi' THEN 0 ELSE 1 END DESC")
                ->orderBy('tbl_prospect_partner.created_at', 'asc');
        } else {
            $query
                ->orderByRaw("
                            CASE 
                                WHEN tbl_prospect_partner.userid = ? THEN 0 
                                ELSE 1 
                            END
                        ", [Auth::user()->username])
                ->orderByRaw("
                            CASE 
                                WHEN tbl_prospect_partner.status = 'verifikasi' THEN 0 
                                ELSE 1 
                            END
                        ")
                ->orderBy('tbl_prospect_partner.created_at', 'asc');
        }

        $partner = $query->get();

        $row = 2;

        // return $partner;
        foreach ($partner as $index => $p) {
            if (session('current_role') == 'admin') {
                $data = [
                    $index + 1,
                    $p->name,
                    $p->email,
                    $p->occupation,
                    $p->phonenumber,
                    $p->socmed,
                    $p->researchint,
                    $p->institution,
                    $p->website,
                    $p->country_name,
                    $p->address,
                    $p->cardname1 ? asset('storage/' . $p->cardname1) : '-',
                    $p->cardname2 ? asset('storage/' . $p->cardname2) : '-',
                    $p->point,
                    $p->userid,
                    $p->name_user,
                    $p->status ?? 'Menunggu Verifikasi',
                    $p->date_add,
                    $p->date_verify,
                    $p->userid_verify,
                    $p->name_user_verify,
                    $p->revisi ?? '-',
                    $p->created_at,
                    $p->updated_at,
                ];
            } else {
                $data = [
                    $index + 1,
                    $p->name,
                    $p->email,
                    $p->occupation,
                    $p->socmed,
                    $p->researchint,
                    $p->institution,
                    $p->website,
                    $p->country_name,
                    $p->address,
                    $p->cardname1 ? asset('storage/' . $p->cardname1) : '-',
                    $p->cardname2 ? asset('storage/' . $p->cardname2) : '-',
                    $p->point,
                    $p->userid,
                    $p->name_user,
                    $p->status ?? 'Menunggu Verifikasi',
                    $p->date_add,
                    $p->date_verify,
                    $p->userid_verify,
                    $p->name_user_verify,
                    $p->revisi ?? '-',
                    $p->created_at,
                    $p->updated_at,
                ];
            }



            foreach ($data as $i => $value) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);

                // Jika kolom file dengan link (kolom 8–11 = acceptance, cv, sk, bukti pelaksanaan)
                if (in_array($i, [11, 12]) && $value !== '-') {
                    $sheet->setCellValue($columnLetter . $row, 'Lihat Dokumen');
                    $sheet->getCell($columnLetter . $row)->getHyperlink()->setUrl($value);
                } else {
                    $sheet->setCellValueExplicit($columnLetter . $row, $value ?? '-', DataType::TYPE_STRING);
                }
            }

            $row++;
        }


        // Simpan file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'data_daftar mitra potensial ' . Carbon::now() . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
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

        $jumlahNotif = session('current_role') == 'admin' ? $notifCounts->admin_notif : $notifCounts->notif_user;

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
