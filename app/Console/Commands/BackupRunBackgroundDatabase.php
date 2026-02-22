<?php

namespace App\Console\Commands;

use App\Models\Backup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BackupRunBackgroundDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run-background-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database in the background.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            // Pastikan variabel environment sudah terisi
            $dbHost = env('DB_HOST', '127.0.0.1');
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME', 'root');
            $dbPass = env('DB_PASSWORD', '');

            // Path backup
            $backupPath = storage_path('app/public/backups/');
            $fileName = 'Dabatabase backup-' . date('Y-m-d_H-i-s') . '.sql';
            $filePath = $backupPath . $fileName;

            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0777, true);
            }

            // Perintah mysqldump dengan menangkap error
            $command = sprintf('mysqldump --host=%s --user=%s --password=%s %s 2>&1 > %s', escapeshellarg($dbHost), escapeshellarg($dbUser), escapeshellarg($dbPass), escapeshellarg($dbName), escapeshellarg($filePath));

            // Eksekusi perintah dan tangkap output serta status
            exec($command, $output, $returnVar);

            // Cek apakah terjadi error
            if ($returnVar !== 0) {
                Log::error('Backup gagal:', [
                    'command' => $command,
                    'output' => implode("\n", $output),
                    'return_code' => $returnVar,
                ]);
            }

            // Cek apakah file benar-benar memiliki isi
            if (!filesize($filePath)) {
                Log::error('Backup kosong. Kemungkinan terjadi kesalahan pada mysqldump.', [
                    'command' => $command,
                    'output' => implode("\n", $output),
                    'return_code' => $returnVar,
                ]);
            }

            $message = ($returnVar === 0) ? 'Backup berhasil.' : 'Backup gagal: ' . implode("\n", $output);

            $data = [
                'uuid' => Str::uuid(),
                'filename' => $fileName,
                'type' => 'database',
                'size' => filesize($filePath),
                'status' => $returnVar === 0 ? '1' : '0',
                'message' => $message,
            ];

            // Simpan ke database jika backup berhasil
            Backup::create($data);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Terjadi kesalahan pada backup database:', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            Backup::create([
                'uuid' => Str::uuid(),
                'filename' => $fileName,
                'type' => 'files',
                'size' => 0,
                'status' => '0',
                'message' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
