<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Backup;
use Illuminate\Support\Facades\Log;

class BackupRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run {--files=} {--source=}';
    protected $description = 'Membuat backup file aplikasi tanpa menunggu';

    public function handle()
    {
        $zipFilePath = $this->option('files');
        $sourcePath = $this->option('source');

        // Validasi input
        if (empty($zipFilePath) || empty($sourcePath)) {
            $this->error('Path backup atau source tidak valid.');
            return 1;
        }

        // Pastikan direktori backup ada dan bisa ditulis
        $backupDir = dirname($zipFilePath);
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0777, true);
        }
        if (!is_writable($backupDir)) {
            throw new \Exception('Direktori backup tidak memiliki izin tulis: ' . $backupDir);
        }

        try {
            $zip = new ZipArchive();
            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('Gagal membuat file zip');
            }

            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($sourcePath), \RecursiveIteratorIterator::LEAVES_ONLY);

            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $relativePath = substr($file->getRealPath(), strlen($sourcePath) + 1);
                    $zip->addFile($file->getRealPath(), $relativePath);
                }
            }

            // Pastikan file tidak digunakan sebelum ditutup
            clearstatcache();

            $zip->close();

            if (!file_exists($zipFilePath)) {
                throw new \Exception('File ZIP tidak ditemukan setelah proses backup.');
            }

            // Simpan ke database
            Backup::create([
                'uuid' => Str::uuid(),
                'filename' => basename($zipFilePath),
                'type' => 'files',
                'size' => filesize($zipFilePath),
                'status' => '1',
                'message' => 'Backup berhasil dibuat.',
            ]);

            Log::info("Backup berhasil dibuat: $zipFilePath");
            $this->info("Backup berhasil dibuat: $zipFilePath");

            return 0;
        } catch (\Exception $e) {
            Log::error('Backup Gagal: ' . $e->getMessage());

            // Simpan status error ke database
            Backup::create([
                'uuid' => Str::uuid(),
                'filename' => basename($zipFilePath),
                'type' => 'files',
                'size' => 0,
                'status' => '0',
                'message' => $e->getMessage(),
            ]);

            $this->error('Backup gagal: ' . $e->getMessage());
            return 1;
        }
    }
}
