<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class PengajuanKerjaSamaOld extends Model
{
    use SoftDeletes;
    protected $table = 'kerma_db_'; // Pastikan ini nama tabel yang benar
    protected $primaryKey = 'id_mou'; // Gunakan primary key yang benar
    public $incrementing = false; // Jika primary key bukan auto-increment
    protected $keyType = 'string';
    protected $guarded = [];

    public function getLembaga()
    {
        return $this->hasOne(RefLembagaUMS::class, 'id_lmbg', 'id_lembaga');
    }

    public function getPengusul()
    {
        return $this->hasOne(User::class, 'username', 'add_by');
    }

    public function getVerifikator()
    {
        return $this->hasOne(User::class, 'username', 'verify_kaprodi_by');
    }

    public function getKabid()
    {
        return $this->hasOne(User::class, 'username', 'verify_kabid_by');
    }

    public function getPenandatangan()
    {
        return $this->hasOne(User::class, 'username', 'ttd_by');
    }

    public function getJenisInstitusi()
    {
        return $this->hasOne(RefJenisInstitusiMitra::class, 'klasifikasi', 'jenis_institusi');
    }

    public function getStatusPengajuan()
    {
        $status = '';

        if ($this->tgl_verifikasi_kaprodi == '0000-00-00 00:00:00' || empty($this->tgl_verifikasi_kaprodi)) {
            $status .= '<span class="badge bg-info">Proses Verifikasi Kaprodi</span> ';
        } else if ($this->tgl_draft_upload == '0000-00-00 00:00:00' || empty($this->tgl_draft_upload)) {
            $status .= '<span class="badge bg-secondary">Draft Dokumen belum di Upload</span>';
        } else if ($this->tgl_verifikasi_kabid == '0000-00-00 00:00:00' || empty($this->tgl_verifikasi_kabid)) {
            $status .= '<span class="badge bg-warning">Proses Verifikasi Admin</span> ';
        } else if ($this->tgl_verifikasi_user == '0000-00-00 00:00:00' || empty($this->tgl_verifikasi_user)) {
            $status .= '<span class="badge bg-primary">Proses Verifikasi User</span> ';
        } else if ($this->tgl_req_ttd == '0000-00-00 00:00:00' && empty($this->tgl_req_ttd)) {
            $status .= '<span class="badge bg-danger">PIC Penandatanganan <br> Belum Dipilih</span> ';
        } else if ($this->tgl_req_ttd != '0000-00-00 00:00:00' && $this->tgl_selesai == '0000-00-00 00:00:00') {
            $status .= '<span class="badge bg-primary" data-title-tooltip="Sedang di Proses oleh ' . $this->ttd_by . '">Proses Penandatanganan</span> ';
        } else if ($this->tgl_selesai == '0000-00-00 00:00:00' || empty($this->tgl_selesai)) {
            $status = '<span class="badge bg-primary">Proses Publish Dokumen Resmi</span>';
        } else if ($this->tgl_selesai != '0000-00-00 00:00:00') {
            $status = '<span class="badge bg-success">Dokumen Resmi Telah di Upload</span>';
        }

        return $status;
    }

    public function statusPengajuanKerjaSama()
    {
        $today = now();
        $mulai = Carbon::parse($this->mulai);
        $selesai = Carbon::parse($this->selesai);

        $diff = $selesai->diff($today);
        $durasiTersisa = "{$diff->y} Tahun {$diff->m} Bulan {$diff->d} Hari sebelum expired";

        if ($mulai > $today) {
            $statusPengajuanKerjaSama = '<span class="badge bg-warning" data-title-tooltip="Pengajuan masih dalam proses. Mulai: ' . $mulai->format('d-m-Y') . '">Dalam Proses</span><strong style="font-size:12px;">Pengajuan masih dalam proses. Mulai: ' . $mulai->format('d-m-Y') . '</strong>';
        } elseif ($mulai <= $today && $selesai >= $today) {
            $statusPengajuanKerjaSama = '<span class="badge bg-success" data-title-tooltip="Kerja sama akan berakhir dalam ' . $durasiTersisa . '">Berjalan</span> <strong style="font-size:12px;">Kerja sama akan berakhir dalam ' . $durasiTersisa . '</strong>';
        } elseif ($selesai->lt($today)) {
            $hariTerlambat = (int) $selesai->diffInDays($today);

            if (!empty($this->perpanjangan)) {
                $statusPengajuanKerjaSama = '<span class="badge bg-info" data-title-tooltip="Sedang dalam tahap perpanjangan. Berakhir: ' . $selesai->format('d-m-Y') . '">Dalam Perpanjangan</span> <strong style="font-size:12px;">Sedang dalam tahap perpanjangan. Berakhir: ' . $selesai->format('d-m-Y') . '</strong>';
            }
            $statusPengajuanKerjaSama = '<span class="badge bg-danger" data-title-tooltip="Masa kerja sama telah berakhir pada ' . $selesai->format('d-m-Y') . '. Sudah terlewat ' . $hariTerlambat . ' hari">Expired</span> <strong style="font-size:12px;">Masa kerja sama telah berakhir pada ' . $selesai->format('d-m-Y') . '. Sudah terlewat ' . $hariTerlambat . ' hari</strong>';
        }

        return $statusPengajuanKerjaSama;
    }

    public function statusPengajuan()
    {
        $today = now();
        $mulai = Carbon::parse($this->mulai);
        $selesai = Carbon::parse($this->selesai);

        $diff = $selesai->diff($today);
        $durasiTersisa = "{$diff->y} Tahun {$diff->m} Bulan {$diff->d} Hari sebelum expired";

        $statusPengajuan = '';
        if ($mulai > $today) {
            $statusPengajuan .= '<span class="badge bg-warning" data-title-tooltip="Pengajuan masih dalam proses. Mulai: ' . $mulai->format('d-m-Y') . '">Dalam Proses</span>';
        } elseif ($mulai <= $today && $selesai >= $today && $this->status_kerma == null) {
            $statusPengajuan .= '<span class="badge bg-success" data-title-tooltip="Kerja sama akan berakhir dalam ' . $durasiTersisa . '">Berjalan</span>';
        } elseif ($selesai < $today && $this->status_kerma == 'Dalam Perjalanan') {
            $statusPengajuan .= '<span class="badge bg-info" data-title-tooltip="Sedang dalam tahap perpanjangan. Berakhir: ' . $selesai->format('d-m-Y') . '">Dalam Perpanjangan</span>';
        } elseif ($selesai < $today && $this->status_kerma == null) {
            $hariTerlambat = (int) $selesai->diffInDays($today);

            $statusPengajuan .= '<span class="badge bg-danger" data-title-tooltip="Masa kerja sama telah berakhir pada ' . $selesai->format('d-m-Y') . '. Sudah terlewat ' . $hariTerlambat . ' hari">Expired</span>';
        }

        return $statusPengajuan;
    }

    public function scopeFilterDokumen(Builder $query, $request)
    {
        if ($request->filled('status')) {
            $status = $request->status;
            $today = now();

            if ($status == 'Dalam Proses') {
                $query->where('mulai', '>', $today);
            } elseif ($status == 'Berjalan') {
                $query->where('mulai', '<=', $today)->where('selesai', '>=', $today)->whereNull('status_kerma');;
            } elseif ($status == 'Dalam Perpanjangan') {
                $query->where('selesai', '<', $today)->where('status_kerma', 'Dalam Perpanjangan'); // Pastikan ada perpanjangan
            } elseif ($status == 'Expired') {
                $query->where('selesai', '<', $today)->whereNull('status_kerma'); // Tidak ada perpanjangan
            }
        }

        if ($request->filled('dn_ln')) {
            $query->where('dn_ln', $request->dn_ln);
        }

        if ($request->filled('lembaga_ums')) {
            $query->where('id_lembaga', $request->lembaga_ums);
        }

        if ($request->filled('jenis_dokumen')) {
            $query->where('jenis_kerjasama', $request->jenis_dokumen);
        }

        if ($request->filled('jenis_institusi_mitra')) {
            $query->where('jenis_institusi', $request->jenis_institusi_mitra);
        }

        if ($request->filled('nama_institusi')) {
            $query->where('nama_institusi', $request->nama_institusi);
        }

        if ($request->filled('tingkat_kerjasama')) {
            $query->where('prodi_unit', $request->tingkat_kerjasama);
        }

        if ($request->filled('negara_mitra')) {
            $query->where('negara_mitra', $request->negara_mitra);
        }

        if ($request->filled('wilayah_mitra')) {
            $query->where('wilayah_mitra', $request->wilayah_mitra);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('timestamp', $request->tahun);
        }
        // if ($request->filled('tgl_mulai')) {
        //     $query->whereDate('tgl_mulai', '>=', $request->tgl_mulai);
        // }

        // if ($request->filled('tgl_selesai')) {
        //     $query->whereDate('tgl_selesai', '<=', $request->tgl_selesai);
        // }

        return $query;
    }
}
