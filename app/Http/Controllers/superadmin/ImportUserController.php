<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use App\Models\RefJabatan;
use App\Models\RefJenisDokumen;
use App\Models\RefLembagaUMS;
use App\Models\Roles;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportUserController extends Controller
{
    protected $sessionKey = 'preview_rows';

    public function index()
    {
        return view('import.index');
    }


    public function uploadPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        try {
            $spreadsheet = IOFactory::load($path);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak dapat dibaca.'
            ], 422);
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rowsAssoc = [];

        foreach ($sheet->getRowIterator(2) as $row) {

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = trim((string)$cell->getValue());
            }

            $username  = $cells[1] ?? null;
            $nama      = $cells[2] ?? null;
            $jabatan   = $cells[3] ?? null;
            $prodi     = $cells[4] ?? null;
            $superunit = $cells[5] ?? null;

            if (empty($nama) && empty($jabatan) && empty($prodi)) continue;

            $error = empty($nama) ? "Nama wajib diisi" : null;

            // ⬅️ overwrite jika username sama
            $rowsAssoc[$username] = [
                'id' => Str::uuid()->getHex(),
                'uniid' => $username,
                'nama' => $nama,
                'jabatan' => $jabatan,
                'prodi' => $prodi,
                'superunit' => $superunit,
                'error' => $error,
            ];
        }

        // simpan hanya unique rows (latest overwrite)
        session([$this->sessionKey => array_values($rowsAssoc)]);

        return response()->json([
            'success' => true,
            'rows' => array_values($rowsAssoc)
        ]);
    }


    public function updateRow(Request $request)
    {
        $rows = session('preview_rows', []);

        foreach ($rows as &$r) {
            if ($r['id'] == $request->id) {
                $r['uniid'] = $request->uniid;
                $r['nama'] = $request->nama;
                $r['jabatan'] = $request->jabatan;
                $r['prodi'] = $request->prodi;
                $r['superunit'] = $request->superunit;
            }
        }

        session(['preview_rows' => $rows]);

        return response()->json(['success' => true]);
    }

    public function deleteRow(Request $request)
    {
        $rows = session('preview_rows', []);

        $rows = array_filter($rows, fn($r) => $r['id'] != $request->id);

        session(['preview_rows' => array_values($rows)]);

        return response()->json(['success' => true]);
    }

    public function saveAll()
    {
        set_time_limit(0);

        $rows = session('preview_rows', []);
        if (empty($rows)) {
            return response()->json(['error' => 'Tidak ada data untuk disimpan'], 400);
        }

        $dedup = [];
        foreach ($rows as $r) {
            $dedup[$r['uniid']] = $r;
        }
        $rows = array_values($dedup);

        $usernames = array_column($rows, 'uniid');

        $existingUsers = User::whereIn('username', $usernames)
            ->get()
            ->keyBy('username');

        $jabatanRef = RefJabatan::pluck('id', 'nama_jabatan')->toArray();
        $unitRef = RefLembagaUMS::pluck('id_lmbg', 'nama_lmbg')->toArray();

        $failedRows = [];
        $insertList = [];
        $updateList = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $value) {

                $username  = trim($value['uniid']);
                $jabatanUser = trim($value['jabatan']);
                $namaUser  = trim($value['nama']);
                $namaUnit  = trim($value['prodi']);
                $superUnit = trim($value['superunit']);

                if ($username == '?' || empty($username)) {
                    $failedRows[] = [
                        'row' => $value,
                        'reason' => 'Username kosong atau tidak valid'
                    ];
                    continue;
                }
                if (!isset($jabatanRef[$jabatanUser])) {
                    $failedRows[] = [
                        'row' => $value,
                        'reason' => "Jabatan '$jabatanUser' tidak ditemukan"
                    ];
                    continue;
                }

                $statusUser = in_array($jabatanUser, ['Direktur', 'Kepala', 'Dekan', 'Kaprodi', 'Ketua'])
                    ? 'verifikator'
                    : 'user';

                if (!isset($unitRef[$namaUnit])) {
                    $failedRows[] = [
                        'row' => $value,
                        'reason' => "Prodi/Unit '$namaUnit' tidak ditemukan"
                    ];
                    continue;
                }

                if ($superUnit == 'Universitas Muhammadiyah Surakarta') {
                    $placeState = 'lmbg1001';
                } else {
                    if (!isset($unitRef[$superUnit])) {
                        $failedRows[] = [
                            'row' => $value,
                            'reason' => "Superunit '$superUnit' tidak ditemukan"
                        ];
                        continue;
                    }
                    $placeState = $unitRef[$superUnit];
                }

                $data = [
                    'jabatan'      => $jabatanUser,
                    'name'         => $namaUser,
                    'display_name' => $namaUser,
                    'status_user'  => $statusUser,
                    'uniid'        => $username,
                    'place_state'  => $placeState,
                    'status_tempat' => $namaUnit,
                ];

                if (isset($existingUsers[$username])) {
                    $updateList[$username] = $data;
                } else {
                    $insertList[] = array_merge($data, [
                        'uuid'              => Str::uuid()->getHex(),
                        'username'          => $username,
                        'password'          => Hash::make(Str::random(16)),
                        'is_active'         => 1,
                        'email_verified_at' => now(),
                        'email'             => $username . '@ums.ac.id',
                    ]);
                }
            }


            $roleMap = [
                'admin'         => ['admin', 'verifikator', 'user'],
                'administrator' => ['admin', 'verifikator', 'user'],
                'verifikator'   => ['verifikator', 'user'],
                'user'          => ['user'],
            ];

            if (!empty($insertList)) {
                User::insert($insertList);

                $insertedUsernames = array_column($insertList, 'username');

                $insertedUsers = User::whereIn('username', $insertedUsernames)->get();

                foreach ($insertedUsers as $user) {
                    $roles = $roleMap[$user->status_user] ?? ['user'];
                    $user->syncRoles($roles);
                }
            }


            if (!empty($updateList)) {
                foreach ($updateList as $username => $data) {
                    User::where('username', $username)->update($data);

                    $user = User::where('username', $username)->first();
                    if ($user) {
                        $roles = $roleMap[$user->status_user] ?? ['user'];
                        $user->syncRoles($roles);
                    }
                }
            }

            // dd($insertList, $updateList);

            DB::commit();

            session()->forget('preview_rows');

            return response()->json([
                'success' => true,
                'failed_rows' => $failedRows
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function clearPreview()
    {
        session()->forget('preview_rows');
        return response()->json(['success' => true]);
    }

    public function downloadTemplate()
    {
        $jabatanList = RefJabatan::pluck('nama_jabatan')->toArray();
        $prodiList = RefLembagaUMS::whereNot('superunit', 'lmbg1001')->pluck('nama_lmbg')->toArray();

        $superunitArr = RefLembagaUMS::where('superunit', 'lmbg1001')->pluck('nama_lmbg')->toArray();
        $superunitList = array_merge(['Universitas Muhammadiyah Surakarta'], $superunitArr);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Template");

        // Header
        $sheet->fromArray([
            ['no', 'uniid/username', 'nama', 'jabatan', 'unit', 'superunit']
        ], null, 'A1');

        // Highlight example row
        $sheet->getStyle('A2:F2')->getFill()->setFillType('solid')
            ->getStartColor()->setRGB('FFFF00');

        // Hidden sheet for dropdown lists
        $listSheet = $spreadsheet->createSheet();
        $listSheet->setTitle("Lists");
        $listSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        $listSheet->fromArray(array_chunk($jabatanList, 1), null, 'A1');
        $listSheet->fromArray(array_chunk($prodiList, 1), null, 'B1');
        $listSheet->fromArray(array_chunk($superunitList, 1), null, 'C1');


        // Data validation
        for ($r = 2; $r <= 500; $r++) {
            $sheet->getCell("D$r")->getDataValidation()
                ->setType(DataValidation::TYPE_LIST)
                ->setFormula1('=Lists!$A$1:$A$' . count($jabatanList))
                ->setShowDropDown(true);

            $sheet->getCell("E$r")->getDataValidation()
                ->setType(DataValidation::TYPE_LIST)
                ->setFormula1('=Lists!$B$1:$B$' . count($prodiList))
                ->setShowDropDown(true);

            $sheet->getCell("F$r")->getDataValidation()
                ->setType(DataValidation::TYPE_LIST)
                ->setFormula1('=Lists!$C$1:$C$' . count($superunitList))
                ->setShowDropDown(true);
        }

        // ============================
        // FIX: CLEAR OUTPUT BUFFER
        // ============================
        if (ob_get_length()) {
            ob_end_clean();
        }

        // STREAMING DOWNLOAD
        $fileName = "template_import_user.xlsx";
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }


    public function downloadFailedRows()
    {
        $failed = session('failed_rows', []);

        if (empty($failed)) {
            return back()->with('error', 'Tidak ada data error untuk diunduh.');
        }

        // Hapus buffer output supaya XLSX tidak corrupt
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = ['UniId', 'Nama', 'Jabatan', 'Prodi', 'Superunit', 'Reason'];
        $sheet->fromArray($headers, null, 'A1');

        // Isi data
        $row = 2;

        foreach ($failed as $item) {
            $r = $item['row'];

            $sheet->fromArray([
                $r['uniid'] ?? '',
                $r['nama'] ?? '',
                $r['jabatan'] ?? '',
                $r['prodi'] ?? '',
                $r['superunit'] ?? '',
                $item['reason'] ?? '',
            ], null, "A{$row}");

            $row++;
        }

        // Nama file
        $fileName = "failed_rows_" . date('Ymd_His') . ".xlsx";
        $tempFile = storage_path("app/{$fileName}");

        // Simpan ke file temp
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tempFile);

        session()->forget('failed_rows');
        // Download & hapus setelah dikirim
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
