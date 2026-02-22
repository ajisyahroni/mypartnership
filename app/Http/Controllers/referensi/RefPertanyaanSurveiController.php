<?php

namespace App\Http\Controllers\referensi;

use App\Http\Controllers\Controller;
use App\Models\RefPertanyaanFeedback;
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

class RefPertanyaanSurveiController extends Controller
{
    public function index()
    {

        $data = [
            'li_active' => 'referensi',
            'li_sub_active' => 'pertanyaan_survei',
            'title' => 'Referensi Pertanyaan Survei',
            'page_title' => 'Referensi Pertanyaan Survei',
        ];
        return view('admin/referensi/pertanyaan_survei/index', $data);
    }

    public function getData(Request $request)
    {
        $query = RefPertanyaanFeedback::select('*');

        if ($request->jenis != null) {
            $query->where('jenis', $request->jenis);
        }
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $role = session('current_role');
                $action = '';
                if ($role == 'admin') {
                    // Tombol tambahan (Lihat Detail, Edit, Hapus)
                    $action .= '<div class="btn-group">
                            <button data-title-tooltip="Edit Data" class="btn btn-warning btn-edit" data-jenis="' . $row->jenis . '"  data-judul="' . $row->judul . '" data-pertanyaan="' . $row->pertanyaan . '" data-id="' . $row->id . '">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button data-title-tooltip="Hapus Data" class="btn btn-danger btn-hapus" data-id="' . $row->id . '">
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
                'jenis' => 'required',
                'pertanyaan' => 'required',
                'judul' => 'required',
            ],
            [
                'jenis.required' => 'Jenis harus diisi.',
                'pertanyaan.required' => 'Tipe harus diisi.',
                'judul.required' => 'Judul harus diisi.',
            ]
        );


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $dataInsert = [
                'jenis' => $request->jenis,
                'pertanyaan' => $request->pertanyaan,
                'judul' => $request->judul,
            ];

            // Simpan atau update data
            if ($request->id) {
                RefPertanyaanFeedback::where('id', $request->id)->update($dataInsert);
            } else {
                RefPertanyaanFeedback::create($dataInsert);
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
            $user = RefPertanyaanFeedback::where('id', $request->id)->firstOrFail();
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
