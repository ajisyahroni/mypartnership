<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PengajuanKerjaSamaImportService;

class ImportPengajuanKerjaSama extends Command
{
    protected $signature = 'pengajuan:import 
                            {file : Path file spreadsheet}
                            {--dry-run : Simulasi tanpa simpan}
                            {--validate-only : Hanya validasi tanpa insert}';

    protected $description = 'Import data PengajuanKerjaSama ke tabel kerma_db';

    public function handle(PengajuanKerjaSamaImportService $service)
    {
        $file = $this->argument('file');
        $dryRun = $this->option('dry-run');
        $validateOnly = $this->option('validate-only');

        if (!file_exists($file)) {
            $this->error("File tidak ditemukan: {$file}");
            return Command::FAILURE;
        }

        $this->info("Memulai proses import...");

        $result = $service->import(
            filePath: $file,
            dryRun: $dryRun,
            validateOnly: $validateOnly,
            output: $this
        );

        $this->line("-------------------------");
        $this->info("SUCCESS : {$result['success']}");
        $this->warn("FAILED  : {$result['failed']}");
        $this->line("-------------------------");

        return Command::SUCCESS;
    }
}