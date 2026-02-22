<?php

namespace App\Http\Controllers\referensi;

use App\Http\Controllers\Controller;
use App\Models\PengajuanKerjaSama;
use App\Models\RefJabatan;
use App\Models\RefNegara;
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

class RefJabatanController extends Controller
{
    public function index()
    {

        $data = [
            'li_active' => 'referensi',
            'li_sub_active' => 'dokumen_instansi',
            'li_sub_menu_active' => 'jabatan',
            'title' => 'Referensi Jabatan UMS',
            'page_title' => 'Referensi Jabatan UMS',
        ];

        return view('admin/referensi/jabatan/index', $data);
    }

    public function getData(Request $request)
    {
        $query = RefJabatan::select('*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $role = session('current_role');
                $action = '';
                if ($role == 'admin') {
                    // Tombol tambahan (Lihat Detail, Edit, Hapus)
                    $action .= '<div class="btn-group">
                            <button data-title-tooltip="Edit Jabatan" class="btn btn-warning btn-edit" data-id="' . $row->id . '" data-nama_jabatan="' . $row->nama_jabatan . '" >
                                <i class="bx bx-edit"></i>
                            </button>
                            <button data-title-tooltip="Hapus Jabatan" class="btn btn-danger btn-hapus" data-id="' . $row->id . '">
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
                'nama_jabatan' => 'required',
            ],
            [
                'nama_jabatan.required' => 'Nama Jabatan harus diisi.',
            ]
        );


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $dataInsert = [
                'nama_jabatan' => $request->nama_jabatan,
            ];

            // Simpan atau update data
            if ($request->id) {
                RefJabatan::where('id', $request->id)->update($dataInsert);
            } else {
                $dataInsert['uuid'] = Str::uuid();
                RefJabatan::create($dataInsert);
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
            $user = RefJabatan::where('id', $request->id)->firstOrFail();
            $user->delete();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Jabatan Berhasil di Hapus']);
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
