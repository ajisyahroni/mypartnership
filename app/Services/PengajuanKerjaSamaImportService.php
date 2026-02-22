<?php

namespace App\Services;

use App\Models\PengajuanKerjaSama;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;


class PengajuanKerjaSamaImportService
{
    protected array $dateFields = [
        'mulai',
        'selesai',
        'awal',
        'tgl_draft_upload',
        'tgl_verifikasi_kaprodi',
        'tgl_verifikasi_kabid',
        'tgl_verifikasi_user',
        'tgl_req_ttd',
        'tgl_verifikasi_publish',
        'tgl_selesai',
        'last_reminder_at',
        'created_at',
        'updated_at',
        'deleted_at',
        'timestamp',
        'timestamp_ajuan'
    ];

    protected array $intFields = [
        'jml_productivity',
        'jml_kuesioner'
    ];

    protected array $enumBooleanFields = [
        'status_verify_kaprodi',
        'status_verify_admin',
        'status_verify_user',
        'status_verify_publish'
    ];

    protected array $nullableFields = [
        'jenis_kerjasama_lain',
        'rangking_univ',
        'nama_fk_mitra',
        'nama_dept_mitra',
        'jenis_institusi_lain',
        'kegiatan',
        'id_mou_perpanjang',
        'keterangan',
        'jdwl_keg',
        'draft_by',
        'verifikasi',
        'note',
        'verify_kerma',
        'status_lapor',
    ];

    public function import(
        string $filePath,
        bool $dryRun = false,
        bool $validateOnly = false,
        ?Command $output = null
    ): array {

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray(
            null,      // null value
            true,      // calculate formulas
            true,      // format data
            true       // indexed by column letter
        );

        if (count($rows) < 2) {
            throw new \Exception("File kosong atau tidak valid.");
        }

        $header = array_map('trim', array_values($rows[1]));
        unset($rows[1]);

        $validColumns = Schema::getColumnListing('kerma_db');
        $this->validateHeader($header, $validColumns);

        $success = 0;
        $failed = 0;

        DB::beginTransaction();

        try {

            $progressBar = $output?->getOutput()->createProgressBar(count($rows));
            $progressBar?->start();

            foreach ($rows as $rowIndex => $row) {

                $values = array_values($row);
                $data = array_combine($header, $values);

                if (empty($data['id_mou'])) {
                    $data['id_mou'] = str_replace('-', '', Str::uuid()->toString());
                } else {
                    $data['id_mou'] = trim($data['id_mou']);
                }

                $data = array_intersect_key($data, array_flip($validColumns));
                $data = $this->transformRow($data);

                if (!$dryRun && !$validateOnly) {
                    PengajuanKerjaSama::updateOrCreate(
                        ['id_mou' => $data['id_mou']],
                        $data
                    );
                }

                $success++;
                $progressBar?->advance();
            }

            $progressBar?->finish();

            ($dryRun || $validateOnly)
                ? DB::rollBack()
                : DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return compact('success', 'failed');
    }

    protected function validateHeader(array $header, array $validColumns)
    {
        $diff = array_diff($header, $validColumns);

        if (!empty($diff)) {
            throw new \Exception(
                "Header tidak valid: " . implode(', ', $diff)
            );
        }
    }

    protected function transformRow(array $data): array
    {
        foreach ($data as $key => $value) {

            if ($value === '' || $value === null) {
                if (in_array($key, $this->nullableFields)) {
                    $data[$key] = '';
                }
                continue;
            }

            if (in_array($key, $this->dateFields)) {

                if ($value === '0000-00-00 00:00:00') {
                    $data[$key] = '0000-00-00 00:00:00';
                    continue;
                }

                try {
                    $data[$key] = Carbon::parse($value)
                        ->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    $data[$key] = '0000-00-00 00:00:00';
                }
            }

            if (in_array($key, $this->intFields)) {
                $data[$key] = (int) $value;
            }

            if (in_array($key, $this->enumBooleanFields)) {
                $data[$key] = $value == '1' ? '1' : '0';
            }
        }

        return $data;
    }
}
