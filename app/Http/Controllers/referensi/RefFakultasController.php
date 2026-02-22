<?php

namespace App\Http\Controllers\referensi;

use App\Http\Controllers\Controller;
use App\Models\PengajuanKerjaSama;
use App\Models\RefBentukKerjaSama;
use App\Models\RefGroupRangking;
use App\Models\RefJenisDokumen;
use App\Models\RefJenisDokumenMoU;
use App\Models\RefJenisInstitusiMitra;
use App\Models\RefLembagaUMS;
use App\Models\RefNegara;
use App\Models\RefPerusahaan;
use App\Models\RefTingkatKerjaSama;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

set_time_limit(300);

class RefFakultasController extends Controller
{
    public function index()
    {

        $data = [
            'li_active' => 'referensi',
            'li_sub_active' => 'dokumen_instansi',
            'li_sub_menu_active' => 'fakultas',
            'tingkat_kerjasama' => RefTingkatKerjaSama::all(),
            'title' => 'Referensi Fakultas',
            'page_title' => 'Referensi Fakultas',
        ];

        return view('admin/referensi/fakultas/index', $data);
    }

    public function getData(Request $request)
    {
        $query = RefJenisDokumen::select('*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $role = session('current_role');
                $action = '';
                if ($role == 'admin') {
                    // Tombol tambahan (Lihat Detail, Edit, Hapus)
                    $action .= '<div class="btn-group">
                            <button data-title-tooltip="Edit Jenis Dokumen" class="btn btn-warning btn-edit" data-ttd="' . $row->ttd_by . '"  data-lingkup_unit="' . $row->lingkup_unit . '" data-alias="' . $row->alias . '" data-nama_dokumen="' . $row->nama_dokumen . '" data-uuid="' . $row->uuid . '">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button data-title-tooltip="Hapus Jenis Dokumen" class="btn btn-danger btn-hapus" data-uuid="' . $row->uuid . '">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>';
                }
                return $action;
            })

            ->rawColumns(['action'])
            ->make(true);
    }


    public function store(Request $request)
    {
        // return $request->all();
        $validator = Validator::make(
            $request->all(),
            [
                'nama_dokumen' => 'required',
                'alias' => 'required',
                'ttd' => 'required',
                'lingkup_unit' => 'required|array', // Pastikan lingkup_unit adalah array
                'lingkup_unit.*' => 'required', // Pastikan tiap item dalam array memiliki nilai
            ],
            [
                'nama_dokumen.required' => 'Jenis Kerja Sama harus diisi.',
                'alias.required' => 'Tingkat Kerja Sama harus diisi.',
                'ttd.required' => 'Penandatangan harus diisi.',
                'lingkup_unit.required' => 'Lingkup Unit harus dipilih.',
                'lingkup_unit.*.required' => 'Setiap Lingkup Unit harus diisi.',
            ]
        );


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Generate ID jika tidak ada
            $uuid = $request->uuid ?? Str::uuid();

            $dataInsert = [
                'nama_dokumen' => $request->nama_dokumen,
                'alias' => $request->alias,
                'ttd_by' => $request->ttd,
                'lingkup_unit' => implode(',', $request->lingkup_unit ?? []),
            ];

            // Simpan atau update data
            if ($request->uuid) {
                RefJenisDokumen::where('uuid', $uuid)->update($dataInsert);
            } else {
                $dataInsert['uuid'] = $uuid;
                RefJenisDokumen::create($dataInsert);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data berhasil disimpan'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // public function switch_status(Request $request)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $user = User::where('uuid', $request->uuid)->firstOrFail();
    //         $user->is_active = $request->status == '1' ? 1 : 0;
    //         $user->save();
    //         DB::commit();
    //         return response()->json(['status'=>true, 'message' => 'Status User Berhasil Update']);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['status'=>false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
    //     }
    // }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = RefJenisDokumen::where('uuid', $request->uuid)->firstOrFail();
            $user->delete();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Jenis Dokumen Berhasil di Hapus']);
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

        DB::beginTransaction();
        try {
            if ($tipe == 'bidang') {
                if ($role == 'verifikator') {
                    $data = [
                        'tgl_verifikasi_kaprodi' => $status == '1' ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00',
                        'verify_kaprodi_by' => $verify_by
                    ];
                } elseif ($role == 'admin') {
                    $data = [
                        'tgl_verifikasi_kabid' => $status == '1' ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00',
                        'verify_kabid_by' => $verify_by
                    ];
                } elseif ($role == 'user') {
                    $data = [
                        'tgl_verifikasi_user' => $status == '1' ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00',
                        'verify_user_by' => $verify_by
                    ];
                }
            } else {
                if ($role == 'admin') {
                    $data = [
                        'tgl_verifikasi_publish' => $status == '1' ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00',
                        'verify_publish_by' => $verify_by,
                        'tgl_selesai' => $status == '1' ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00'
                    ];
                }
            }


            $pengajuan = PengajuanKerjaSama::where('id_mou', $request->id_mou)->firstOrFail();
            $pengajuan->update($data);
            DB::commit();

            if ($status == '1') {
                $message = 'Verifikasi';
            } else {
                $message = 'Batalkan';
            }
            return response()->json(['status' => true, 'message' => 'Pengajuan Berhasil di ' . $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
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

    public function upload_file($file, $path)
    {
        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->makeDirectory($path);
        }
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs($path, $fileName, 'public');
        return $filePath;
    }
}
