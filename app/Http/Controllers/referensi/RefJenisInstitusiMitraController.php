<?php

namespace App\Http\Controllers\referensi;

use App\Http\Controllers\Controller;
use App\Models\RefJenisAliasInstitusiMitra;
use App\Models\RefJenisInstitusiMitra;
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

class RefJenisInstitusiMitraController extends Controller
{
    public function index()
    {

        $data = [
            'li_active' => 'referensi',
            'li_sub_active' => 'dokumen_instansi',
            'li_sub_menu_active' => 'jenis_institusi_mitra',
            'title' => 'Referensi Jenis Institusi Mitra',
            'jenis_alias' => RefJenisAliasInstitusiMitra::all(),
            'page_title' => 'Referensi Jenis Institusi Mitra',
        ];

        return view('admin/referensi/jenis_institusi_mitra/index', $data);
    }

    public function getData(Request $request)
    {
        $query = RefJenisInstitusiMitra::select('*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $role = session('current_role');
                $action = '';
                if ($role == 'admin') {
                    // Tombol tambahan (Lihat Detail, Edit, Hapus)
                    $action .= '<div class="btn-group">
                            <button data-title-tooltip="Edit Jenis Dokumen" class="btn btn-warning btn-edit" data-id="' . $row->id . '"  data-klasifikasi="' . $row->klasifikasi . '" data-alias="' . $row->alias . '" data-keterangan="' . $row->keterangan . '" data-bobot_dikti="' . $row->bobot_dikti . '"data-bobot_ums="' . $row->bobot_ums . '">
                            <i class="bx bx-edit"></i>
                            </button>
                            <button data-title-tooltip="Hapus Jenis Dokumen" class="btn btn-danger btn-hapus" data-id="' . $row->id . '">
                            <i class="bx bx-trash"></i>
                            </button>
                            </div>';
                }
                return $action;
            })
            ->addColumn('alias', function ($row) {
                return $row->alias ?? '';
            })
            ->addColumn('bobot_dikti', function ($row) {
                return $row->bobot_dikti ?? '';
            })
            ->addColumn('bobot_ums', function ($row) {
                return $row->bobot_ums ?? '';
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
                'klasifikasi' => 'required',
                'bobot_ums' => 'required', // Pastikan bobot_ums adalah array
            ],
            [
                'klasifikasi.required' => 'Klasifikasi harus diisi.',
                'bobot_ums.required' => 'Bobot UMS Harus Diisi.',
            ]
        );


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {

            $dataInsert = [
                'klasifikasi' => $request->klasifikasi,
                'alias' => $request->alias,
                'keterangan' => $request->keterangan,
                'bobot_dikti' => $request->bobot_dikti,
                'bobot_ums' => $request->bobot_ums,
            ];

            // Simpan atau update data
            if ($request->id) {
                RefJenisInstitusiMitra::where('id', $request->id)->update($dataInsert);
            } else {
                RefJenisInstitusiMitra::create($dataInsert);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data berhasil disimpan'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }


    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = RefJenisInstitusiMitra::where('id', $request->id)->firstOrFail();
            $user->delete();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Jenis Institusi Mitra Berhasil di Hapus']);
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
