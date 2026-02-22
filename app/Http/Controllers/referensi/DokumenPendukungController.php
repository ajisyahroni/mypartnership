<?php

namespace App\Http\Controllers\referensi;

use App\Http\Controllers\Controller;
use App\Models\DokumenPendukung;
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

class DokumenPendukungController extends Controller
{
    public function index()
    {
        $role = session('current_role');
        $data = [
            'li_active' => 'dokumen_pendukung',
            'title' => 'Dokumen Pendukung',
            'page_title' => 'Dokumen Pendukung',
            'role' => session('current_role'),
            'dokumenPendukung' => DokumenPendukung::all(),
        ];
        if ($role == 'admin') {
            $data['dokumenPendukung'] = DokumenPendukung::all();
        } else {
            $data['dokumenPendukung'] = DokumenPendukung::where('is_active', '1')->get();
        }

        return view('admin/referensi/dokumen_pendukung/index', $data);
    }

    public function loadAllDokumenIframe()
    {
        try {
            $role = session('current_role');

            if ($role == 'admin') {
                $dokumenList = DokumenPendukung::all();
            } else {
                $dokumenList = DokumenPendukung::where('is_active', '1')->get();
            }

            $results = [];

            foreach ($dokumenList as $dokumen) {
                $filePath = $dokumen->link_dokumen ?? asset('storage/' . rawurlencode($dokumen->file_dokumen));
                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                $iframeSrc = '';
                $success = true;
                $message = '';

                if ($dokumen->link_dokumen != null) {
                    $iframeSrc = $dokumen->link_dokumen;
                } else {
                    if (in_array($ext, ['pdf', 'png', 'jpg', 'jpeg', 'webp'])) {
                        $iframeSrc = $filePath;
                    } elseif (in_array($ext, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'])) {
                        $iframeSrc = 'https://docs.google.com/gview?url=' . urlencode($filePath) . '&embedded=true';
                    } else {
                        $success = false;
                        $message = 'Format file tidak didukung untuk preview';
                    }
                }

                if ($success) {
                    if (str_starts_with($iframeSrc, 'https://www.youtube.com')) {
                        $html = '<iframe width="100%" height="1200" src="' . $iframeSrc . '"
                            title="YouTube" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>';
                    } else {
                        $html = '<iframe 
                                src="' . $iframeSrc . '" 
                                frameborder="0" 
                                width="100%" 
                                height="1200"
                                style="border: none; border-radius: 8px; opacity: 0;"
                                onload="this.style.opacity=\'1\'"
                                class="dokumen-iframe">
                            </iframe>';
                    }
                } else {
                    $html = '<div class="alert alert-warning">
                            <i class="bx bx-info-circle me-2"></i>
                            ' . $message . '
                        </div>';
                }

                $results[] = [
                    'uuid' => $dokumen->uuid,
                    'success' => $success,
                    'html' => $html,
                    'message' => $message
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $results,
                'total' => count($results)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nama_dokumen' => 'required',
                'file_dokumen' => 'nullable|mimes:pdf,docx,doc|max:10240',
                'link_dokumen' => 'nullable|url',
            ],
            [
                'file_dokumen.max' => 'File Dokumen maksimal 10MB.',
                'file_dokumen.mimes' => 'File Dokumen Berformat PDF, DOCX, dan DOC.',
                'link_dokumen.url' => 'Format link tidak valid.',
            ]
        );


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('file_dokumen') && $request->link_dokumen) {
            return response()->json(['status' => false, 'message' => "Pilih Salah Satu Masukkan Link atau Upload Dokumen"], 200);
        }

        DB::beginTransaction();
        try {
            // Generate ID jika tidak ada
            $uuid = $request->uuid ?? Str::uuid();
            $cekDokumen = DokumenPendukung::where('uuid', $uuid)->first();

            $dataInsert = [
                'nama_dokumen' => $request->nama_dokumen,
                'file_dokumen' => $cekDokumen->file_dokumen ?? null,
                'link_dokumen' => null
            ];

            // Simpan file SK Mitra
            if ($request->hasFile('file_dokumen')) {
                $file_dokumen = $request->file('file_dokumen');
                $path = "uploads/dokumen_pendukung";
                $filePath = $this->upload_file($file_dokumen, $path);
                $dataInsert['file_dokumen'] = $filePath;
            } else if ($request->link_dokumen) {
                // if ($cekDokumen && $cekDokumen->file_dokumen) {
                //     Storage::disk('public')->delete($cekDokumen->file_dokumen);
                // }
                $dataInsert['link_dokumen'] = $request->link_dokumen;
            }

            // Simpan atau update data
            if ($cekDokumen) {
                $cekDokumen->update($dataInsert);
            } else {
                $dataInsert['uuid'] = $uuid;
                DokumenPendukung::create($dataInsert);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Data berhasil disimpan'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function hapus_file(Request $request)
    {
        DB::beginTransaction();
        try {
            $cekDokumen = DokumenPendukung::where('uuid', $request->uuid)->firstOrFail();
            if ($cekDokumen && $cekDokumen->file_dokumen) {
                Storage::disk('public')->delete($cekDokumen->file_dokumen);
            }
            $cekDokumen->update(['file_dokumen' => null]);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'File Berhasil di Hapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = DokumenPendukung::where('uuid', $request->uuid)->firstOrFail();
            $user->delete();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Dokumen Berhasil di Hapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function setDokumen(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataUpdate = [
                'is_active' => $request->is_active
            ];
            $dokumen = DokumenPendukung::where('id', $request->id)->firstOrFail();
            $dokumen->update($dataUpdate);
            if ($request->is_active) {
                $status = 'Aktifkan';
            } else {
                $status = 'dinonaktifkan';
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Dokumen Berhasil di ' . $status]);
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
