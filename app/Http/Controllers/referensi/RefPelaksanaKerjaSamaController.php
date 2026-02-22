<?php

namespace App\Http\Controllers\referensi;

use App\Http\Controllers\Controller;
use App\Models\PengajuanKerjaSama;
use App\Models\RefTingkatKerjaSama;
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

class RefPelaksanaKerjaSamaController extends Controller
{
    public function index()
    {

        $data = [
            'li_active' => 'referensi',
            'li_sub_active' => 'dokumen_kerjasama',
            'li_sub_menu_active' => 'pelaksana_kerjasama',
            'title' => 'Referensi Pelaksana Kerja Sama',
            'page_title' => 'Referensi Pelaksana Kerja Sama',
        ];

        return view('admin/referensi/pelaksana_kerjasama/index', $data);
    }

    public function getData(Request $request)
    {
        $query = RefTingkatKerjaSama::select('*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $role = session('current_role');
                $action = '';
                if ($role == 'admin') {
                    // Tombol tambahan (Lihat Detail, Edit, Hapus)
                    $action .= '<div class="btn-group">
                            <button data-title-tooltip="Edit Pelaksana Kerja Sama" class="btn btn-warning btn-edit" data-nama="' . $row->nama . '"  data-check="' . $row->check . '" data-label="' . $row->label . '" data-id="' . $row->id . '">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button data-title-tooltip="Hapus Pelaksana Kerja Sama" class="btn btn-danger btn-hapus" data-id="' . $row->id . '">
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
                'nama' => 'required',
                'label' => 'required',
                'check' => 'required',
            ],
            [
                'nama.required' => 'Jenis Kerja Sama harus diisi.',
                'label.required' => 'Tingkat Kerja Sama harus diisi.',
                'check.required' => 'Penandatangan harus diisi.',
            ]
        );


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $dataInsert = [
                'nama' => $request->nama,
                'check' => $request->check,
                'label' => $request->label,
            ];

            // Simpan atau update data
            if ($request->id) {
                RefTingkatKerjaSama::where('id', $request->id)->update($dataInsert);
            } else {
                RefTingkatKerjaSama::create($dataInsert);
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
            $referensi = RefTingkatKerjaSama::where('id', $request->id)->firstOrFail();
            $referensi->delete();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Pelaksana Kerja Sama Berhasil di Hapus']);
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
