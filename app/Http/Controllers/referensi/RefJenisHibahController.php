<?php

namespace App\Http\Controllers\referensi;

use App\Http\Controllers\Controller;
use App\Models\RefJenisHibah;
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

class RefJenisHibahController extends Controller
{
    public function index()
    {

        $data = [
            'li_active' => 'jenis_hibah',
            'title' => 'Referensi Jenis Hibah',
            'page_title' => 'Referensi Jenis Hibah',
        ];

        return view('admin/referensi/jenis_hibah/index', $data);
    }

    public function getData(Request $request)
    {
        $query = RefJenisHibah::select('*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('dl_proposal', function ($row) {
                return Tanggal_Indo($row->dl_proposal);
            })
            ->addColumn('dl_laporan', function ($row) {
                return Tanggal_Indo($row->dl_laporan);
            })
            ->addColumn('action', function ($row) {
                $role = session('current_role');
                $action = '';
                if ($role == 'admin') {
                    // Tombol tambahan (Lihat Detail, Edit, Hapus)
                    $action .= '<div class="btn-group">
                            <button data-title-tooltip="Edit Jenis Hibah" class="btn btn-warning btn-edit" data-jenis_hibah="' . $row->jenis_hibah . '" data-dl_laporan="' . $row->dl_laporan . '"  data-dl_proposal="' . $row->dl_proposal . '" data-maksimum="' . $row->maksimum . '" data-id="' . $row->id . '">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button data-title-tooltip="Hapus Jenis Hibah" class="btn btn-danger btn-hapus" data-id="' . $row->id . '">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>';
                }
                return $action;
            })
            ->addColumn('maksimum', function ($row) {
                return rupiah($row->maksimum);
            })

            ->rawColumns(['action', 'maksimum'])
            ->make(true);
    }

    public function switch_status(Request $request)
    {
        DB::beginTransaction();
        try {
            $jenis_hibah = RefJenisHibah::where('id', $request->id)->firstOrFail();
            $deadline = $jenis_hibah->dl_proposal;

            if ($request->is_active == '1' && now()->greaterThan($deadline)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengaktifkan: Tanggal sudah melewati batas DL Proposal.'
                ], 200);
            }

            if (!in_array($request->is_active, ['1', '0'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengubah status.'
                ], 200);
            }

            $jenis_hibah->is_active = $request->is_active == '1' ? '1' : '0';
            $jenis_hibah->save();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Status Jenis Hibah Berhasil Update'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        // return $request->all();
        $validator = Validator::make(
            $request->all(),
            [
                'jenis_hibah' => 'required',
                'maksimum' => 'required',
                'dl_proposal' => 'required',
                'dl_laporan' => 'required',
            ],
            [
                'jenis_hibah.required' => 'Jenis Hibah harus diisi.',
                'maksimum.required' => 'Maksimum harus diisi.',
                'dl_proposal.required' => 'Deadline Proposal harus diisi.',
                'dl_laporan.required' => 'Deadline Laporan harus diisi.',
            ]
        );


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $dataInsert = [
                'jenis_hibah' => $request->jenis_hibah,
                'maksimum' => str_replace('.', '', $request->maksimum),
                'dl_proposal' => $request->dl_proposal,
                'dl_laporan' => $request->dl_laporan,
            ];

            // Simpan atau update data
            if ($request->id) {
                RefJenisHibah::where('id', $request->id)->update($dataInsert);
            } else {
                RefJenisHibah::create($dataInsert);
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
            $user = RefJenisHibah::where('id', $request->id)->firstOrFail();
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
