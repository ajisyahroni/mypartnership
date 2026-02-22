<?php

namespace App\Http\Controllers\referensi;

use App\Http\Controllers\Controller;
use App\Models\RefLembagaUMS;
use App\Models\RefTingkatKerjaSama;
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

class RefLembagaUMSController extends Controller
{
    public function index()
    {

        $data = [
            'li_active' => 'referensi',
            'li_sub_active' => 'dokumen_instansi',
            'li_sub_menu_active' => 'lembaga_ums',
            'title' => 'Referensi Lembaga UMS',
            'jenis_lmbg' => RefTingkatKerjaSama::all(),
            'super_unit' => RefLembagaUMS::where('superunit', 'lmbg1001')->get(),
            'page_title' => 'Referensi Lembaga UMS',
        ];

        return view('admin/referensi/lembaga_ums/index', $data);
    }

    public function getData(Request $request)
    {
        $query = RefLembagaUMS::select('*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $role = session('current_role');
                $action = '';
                if ($role == 'admin') {
                    // Tombol tambahan (Lihat Detail, Edit, Hapus)
                    $action .= '<div class="btn-group">
                            <button data-title-tooltip="Edit Lembaga" class="btn btn-warning btn-edit" data-id_lmbg="' . $row->id_lmbg . '" data-super_unit="' . $row->superunit . '"  data-status_tempat="' . $row->status_tempat . '" data-nama_lmbg="' . $row->nama_lmbg . '" data-namalmbg_singkat="' . $row->namalmbg_singkat . '" data-jenis_lmbg="' . $row->jenis_lmbg . '">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button data-title-tooltip="Hapus Lembaga" class="btn btn-danger btn-hapus" data-id_lmbg="' . $row->id_lmbg . '">
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
                'id_lmbg' => 'required',
                'nama_lmbg' => 'required',
                'jenis_lmbg' => 'required',
                'super_unit' => 'required',
            ],
            [
                'id_lmbg.required' => 'Id Lembaga harus diisi.',
                'nama_lmbg.required' => 'Nama Lembaga harus diisi.',
                'jenis_lmbg.required' => 'Jenis Lembaga harus diisi.',
                'super_unit.required' => 'Super Unit harus diisi.',
            ]
        );


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $isExist = RefLembagaUMS::where('id_lmbg', $request->id_lmbg)->first();
            $namasuper = RefLembagaUMS::where('id_lmbg', $request->super_unit)->firstorfail();

            $dataInsert = [
                'id_lmbg' => $request->id_lmbg,
                'status_tempat' => $request->status_tempat,
                'nama_lmbg' => $request->nama_lmbg,
                'namalmbg_singkat' => $request->namalmbg_singkat,
                'jenis_lmbg' => $request->jenis_lmbg,
                'superunit' => $request->super_unit,
                'namasuper' => $namasuper->nama_lmbg,
                'place_state' => $request->super_unit,
            ];

            // Simpan atau update data
            if ($isExist) {
                RefLembagaUMS::where('id_lmbg', $request->id_lmbg)->update($dataInsert);
            } else {
                RefLembagaUMS::create($dataInsert);
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
            $user = RefLembagaUMS::where('id_lmbg', $request->id_lmbg)->firstOrFail();
            $user->delete();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data Berhasil di Hapus']);
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
