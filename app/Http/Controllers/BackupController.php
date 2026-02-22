<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use ZipArchive;

set_time_limit(300);

class BackupController extends Controller
{
    // Menampilkan halaman backup
    public function index()
    {
        $data = [
            'li_active' => 'backup',
        ];
        return view('superadmin.backup.index', $data);
    }

    public function getData(Request $request)
    {
        $query = Backup::select('*');
        $data = $query->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('encrypt_id', function ($row) {
                return Crypt::encryptString($row->id);
            })
            ->addColumn('type', function ($row) {
                return ucfirst($row->type);
            })
            ->addColumn('size', function ($row) {
                return number_format($row->size / 1024, 2) . ' KB';
            })
            ->addColumn('tanggal', function ($row) {
                return $row->created_at;
            })
            ->make(true);
    }

    // Membuat backup database
    public function backupDatabase()
    {
        $dbName = env('DB_DATABASE');
        // Pastikan DB_NAME tidak kosong
        if (empty($dbName)) {
            return response()->json(['error' => 'Nama database tidak ditemukan.'], 400);
        }

        $commandCheck = 'which mysqldump';
        exec($commandCheck, $checkOutput, $checkReturnVar);

        if ($checkReturnVar !== 0) {
            Log::error('mysqldump tidak ditemukan di server.', [
                'command' => $commandCheck,
                'output' => implode("\n", $checkOutput),
                'return_code' => $checkReturnVar,
            ]);
            return response()->json(['error' => 'Perintah mysqldump tidak ditemukan di server.'], 500);
        }

        $artisanPath = base_path('artisan'); // Path lengkap ke artisan file
        $command = sprintf('start /B php ' . $artisanPath . ' backup:run-background-database');
        // $command = sprintf('php ' . $artisanPath . ' backup:run-background-database > /dev/null 2>&1 &', $output, $returnVar); // Linux
        pclose(popen($command, 'r'));

        return response()->json([
            'message' => 'Database dalam proses Generate!<br><span style="font-size:12px;">Silahkan tunggu beberapa menit lagi.</span>',
        ]);
    }

    public function backupFiles()
    {
        try {
            $backupDir = storage_path('app/public/backups/');

            // Pastikan folder backup ada
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0777, true);
            }

            $zipFileName = 'backup-files-' . date('Y-m-d_H-i-s') . '.zip';
            $zipFilePath = $backupDir . $zipFileName;
            $sourcePath = base_path(); // Direktori utama aplikasi
            $artisanPath = base_path('artisan');

            // Perintah untuk menjalankan Artisan command di background
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $command = "start /B php \"$artisanPath\" backup:run --files=\"$zipFilePath\" --source=\"$sourcePath\"";
                pclose(popen($command, 'r'));
            } else {
                $command = "nohup php \"$artisanPath\" backup:run --files=\"$zipFilePath\" --source=\"$sourcePath\" > /dev/null 2>&1 &";
                exec($command);
            }
            // return $command;

            return response()->json([
                'message' => 'Backup sedang diproses, silakan cek nanti.',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // Mengunduh backup berdasarkan UUID
    public function downloadBackup(Request $request)
    {
        $backup = Backup::where('uuid', $request->uuid)->firstOrFail();
        $filePath = "backups/{$backup->filename}";
        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json(['error' => 'File backup tidak ditemukan!'], 404);
        }

        return response()->json(['status' => true, 'message' => 'File backup berhasil di download', 'urlDownload' => 'storage/' . $filePath], 200);
    }

    // Menghapus backup berdasarkan UUID
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $backup = Backup::where('uuid', $request->uuid)->firstOrFail();
            Storage::disk('public')->delete("backups/{$backup->filename}");
            $backup->delete();

            DB::commit();
            return response()->json(['status' => true, 'message' => 'File backup berhasil dihapus!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
