<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class PengajuanKerjaSama extends Model
{
    use SoftDeletes;
    protected $table = 'kerma_db'; // Pastikan ini nama tabel yang benar
    protected $primaryKey = 'id_mou'; // Gunakan primary key yang benar
    public $incrementing = false; // Jika primary key bukan auto-increment
    protected $keyType = 'string';
    protected $guarded = [];

    protected $appends = ['status_pengajuan', 'status_pengajuan_kerma', 'action_buttons', 'implementasi_button', 'dokumen_button'];

    public function getLembaga()
    {
        return $this->hasOne(RefLembagaUMS::class, 'id_lmbg', 'id_lembaga');
    }

    public function lembagaBaru()
    {
        return $this->hasOne(RefLembagaUMS::class, 'id_lmbg', 'id_lembaga');
    }

    public function lembagaLama()
    {
        return $this->hasOne(RefLembagaUMSOld::class, 'id_lmbg', 'id_lembaga');
    }

    public function getLembagaAttribute()
    {
        return $this->lembagaBaru ?? $this->lembagaLama;
    }

    public function dataBobot()
    {
        return $this->hasOne(RefJenisInstitusiMitra::class, 'klasifikasi', 'jenis_institusi');
    }


    public function getPengusul()
    {
        return $this->hasOne(User::class, 'username', 'add_by');
    }

    public function getVerifyUser()
    {
        return $this->hasOne(User::class, 'username', 'verify_user_by');
    }

    public function getVerifyDokumen()
    {
        return $this->hasOne(User::class, 'username', 'verify_publish_by');
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

    public function getUnreadChatCount()
    {
        return $this->hasMany(Chat::class, 'id_mou', 'id_mou')->where('is_seen', '0')->where('receiver_id', auth()->id());
    }

    public function getJenisInstitusi()
    {
        return $this->hasOne(RefJenisInstitusiMitra::class, 'klasifikasi', 'jenis_institusi');
    }

    public function getStatusPengajuan()
    {
        $status = '';
        $hasVerifiedKaprodi = $this->tgl_verifikasi_kaprodi != '0000-00-00 00:00:00';
        $hasDraft = $this->tgl_draft_upload != '0000-00-00 00:00:00';
        $hasVerifiedKabid = $this->tgl_verifikasi_kabid != '0000-00-00 00:00:00';
        $hasVerifiedUser = $this->tgl_verifikasi_user != '0000-00-00 00:00:00';
        $hasTandaTangan = $this->tgl_req_ttd != '0000-00-00 00:00:00';
        $hasTTDBy = !empty($this->ttd_by);
        $hasVerifiedPublish = $this->tgl_verifikasi_publish != '0000-00-00 00:00:00';
        $hasPublish = $this->tgl_selesai != '0000-00-00 00:00:00';
        $ajuanBaru = $this->stats_kerma;
        $fileMoU = $this->file_mou != null;

        $isVerifikasi = [
            'kaprodi' => $this->status_verify_kaprodi == '1',
            'admin' => $this->status_verify_admin == '1',
            'user' => $this->status_verify_user == '1',
            'publish' => $this->status_verify_publish == '1',
        ];

        $isRevisi = [
            'kaprodi' => $this->status_verify_kaprodi == '0' && $this->status_verify_kaprodi != null,
            'admin' => $this->status_verify_admin == '0' && $this->status_verify_admin != null,
            'user' => $this->status_verify_user == '0' && $this->status_verify_user != null,
            'publish' => $this->status_verify_publish == '0' && $this->status_verify_publish != null,
        ];

        $pengusul = $this->add_by;

        if (!$hasVerifiedKaprodi && $isRevisi['kaprodi']) {
            if (session('current_role') == 'verifikator') {
                return '<span style="font-size:10px!important;" class="badge bg-warning">Proses Revisi Pengusul</span>';
            } else {
                return '<span style="font-size:10px!important;" class="badge bg-danger">Menunggu Revisi Pengusul</span>';
            }
        }

        // VERIFIKASI KAPRODI
        if (!$hasVerifiedKaprodi && !$hasPublish) {
            $arrJabatan = ['Kaprodi', 'Dekan', 'Direktur', 'Kepala', 'Ketua'];
            $jabatanUser  = auth()->user()->jabatan ?? null;
            $thisLembaga  = $this->status_tempat;

            $dtLembaga = RefLembagaUMS::where('nama_lmbg', $thisLembaga)
                ->value('jenis_lmbg');

            if ($dtLembaga == null) {
                $dtLembaga = RefLembagaUMS::where('nama_lmbg_old', $thisLembaga)
                    ->value('jenis_lmbg');
            }

            $jabatanVerifikator = 'Kaprodi';

            if ($dtLembaga === 'Fakultas') {
                $jabatanVerifikator = 'Dekan';
            } elseif ($dtLembaga === 'Unit (Biro/Lembaga)') {
                $jabatanVerifikator = 'Kepala / Direktur';
            }

            if (session('current_role') === 'verifikator') {
                return '<span style="font-size:10px!important;" class="badge bg-danger">
                    Menunggu Verifikasi ' . $jabatanVerifikator . '
                </span>';
            }

            return '<span style="font-size:10px!important;" class="badge bg-warning">
                Proses Verifikasi ' . $jabatanVerifikator . '
            </span>';
        }

        if ($hasVerifiedKaprodi && !$hasVerifiedKabid && $isRevisi['admin'] || ($ajuanBaru == 'Lapor Kerma' && $hasVerifiedKaprodi && !$hasVerifiedPublish && $isRevisi['publish'])) {
            if ($ajuanBaru == 'Lapor Kerma') {
                return '<span style="font-size:10px!important;" class="badge bg-dark">Pengajuan Ditolak Admin</span>';
            } else if ($ajuanBaru == 'Ajuan Baru') {
                if (session('current_role') == 'admin' || auth()->user()->username != $this->add_by) {
                    return '<span style="font-size:10px!important;" class="badge bg-warning">Menunggu Revisi Pengusul</span>';
                } else {
                    return '<span style="font-size:10px!important;" class="badge bg-danger">Proses Revisi Pengusul</span>';
                }
            }
        }

        // VERIFIKASI ADMIN
        if ($hasVerifiedKaprodi && !$hasVerifiedKabid && !$hasPublish && ($ajuanBaru == 'Ajuan Baru')) {
            if (session('current_role') == 'admin') {
                return '<span style="font-size:10px!important;" class="badge bg-danger">Menunggu Verifikasi Admin</span>';
            } else {
                return '<span style="font-size:10px!important;" class="badge bg-warning">Proses Verifikasi Admin</span>';
            }
        }

        // UPLOAD DRAFT
        if ($hasVerifiedKaprodi && $hasVerifiedKabid && !$hasDraft && !$hasPublish && ($ajuanBaru == 'Ajuan Baru')) {
            $urlUploadDraft = url('pengajuan/tambah-pengajuan-baru/' . $this->id_mou . '#draftDokumen');
            if (session('current_role') == 'admin' || session('current_role') == 'user') {
                return '<a href=' . $urlUploadDraft . '><span style="font-size:10px!important;" class="badge bg-danger">Draft Dokumen belum di Upload</span></a>';
            } else {
                return '<a href=' . $urlUploadDraft . '><span style="font-size:10px!important;" class="badge bg-warning">Draft Dokumen belum di Upload</span></a>';
            }
        }

        if ($hasVerifiedKaprodi && $hasVerifiedKabid && !$hasVerifiedUser && $isRevisi['user']) {
            if (session('current_role') == 'admin') {
                return '<span style="font-size:10px!important;" class="badge bg-danger">Menunggu Revisi Admin</span>';
            } else {
                return '<span style="font-size:10px!important;" class="badge bg-warning">Proses Revisi Admin</span>';
            }
        }

        // VERIFIKASI PENGUSUL
        if ($hasVerifiedKaprodi && $hasVerifiedKabid && !$hasVerifiedUser && !$hasPublish && ($ajuanBaru == 'Ajuan Baru')) {
            if (session('current_role') == 'user') {
                return '<span style="font-size:10px!important;" class="badge bg-danger">Menunggu Verifikasi Pengusul</span>';
            } else {
                if ($pengusul == auth()->user()->username && in_array(auth()->user()->jabatan, ['Kaprodi', 'Dekan', 'Direktur', 'Kepala', 'Ketua'])) {
                    return '<span style="font-size:10px!important;" class="badge bg-danger">Menunggu Verifikasi Pengusul</span>';
                } else {
                    return '<span style="font-size:10px!important;" class="badge bg-warning">Proses Verifikasi Pengusul</span>';
                }
            }
        }

        // PIC PENANDATANGANAN
        if (!$hasTTDBy && !$hasPublish  && ($ajuanBaru == 'Ajuan Baru')) {
            if (session('current_role') == 'admin') {
                return '<span style="font-size:10px!important;" class="badge bg-danger">PIC Penandatanganan <br> Belum Dipilih</span>';
            } else {
                return '<span style="font-size:10px!important;" class="badge bg-warning">PIC Penandatanganan <br> Belum Dipilih Admin</span>';
            }
        }


        // UPLOAD DOKUMEN KERJA SAMA & PROSES PENANDATANGANAN
        if ($hasVerifiedKaprodi && $hasVerifiedKabid && $hasVerifiedUser && !$hasTandaTangan && !$hasPublish  && ($ajuanBaru == 'Ajuan Baru')) {
            if ($this->stats_kerma == 'Lapor Kerma') {
                $urlUploadKerma = url('pengajuan/lapor-pengajuan/' . $this->id_mou . '#draftDokumenMoU');
            } else {
                $urlUploadKerma = url('pengajuan/tambah-pengajuan-baru/' . $this->id_mou . '#draftDokumenMoU');
            }

            if ($this->ttd_by == 'Pengusul' && $this->add_by == auth()->user()->username) {
                return '<a href=' . $urlUploadKerma . '><span style="font-size:10px!important;" class="badge bg-danger" data-title-tooltip="Upload Dokumen Kerja Sama oleh ' . $this->ttd_by . '">Upload Dokumen Kerma</span></a>';
            } else if ($this->ttd_by == 'BKUI' && session('current_role') == 'admin') {
                return '<a href=' . $urlUploadKerma . '><span style="font-size:10px!important;" class="badge bg-danger" data-title-tooltip="Sedang di Proses oleh ' . $this->ttd_by . '">Upload Dokumen Kerma</span></a>';
            } else {
                return '<span style="font-size:10px!important;" class="badge bg-warning" data-title-tooltip="Sedang di Proses oleh ' . $this->ttd_by . '">Proses Penandatanganan</span>';
            }
        }

        if (!$hasVerifiedPublish && $isRevisi['publish']) {
            if ($this->stats_kerma == 'Lapor Kerma') {
                $urlUploadKerma = url('pengajuan/lapor-pengajuan/' . $this->id_mou . '#draftDokumenMoU');
            } else {
                $urlUploadKerma = url('pengajuan/tambah-pengajuan-baru/' . $this->id_mou . '#draftDokumenMoU');
            }
            if (session('current_role') == 'admin' && $this->ttd_by == 'Pengusul' || session('current_role') == 'user' && $this->ttd_by == 'BKUI' || session('current_role') == 'verifikator') {
                return '<span style="font-size:10px!important;" class="badge bg-warning">Menunggu Revisi Dokumen Kerja Sama</span>';
            } else {
                return '<a href=' . $urlUploadKerma . '><span style="font-size:10px!important;" data-title-tooltip="Sedang di Proses Revisi oleh ' . $this->ttd_by . '" class="badge bg-danger">Proses Revisi Dokumen Kerja Sama</span></a>';
            }
        }

        // VERIFIKASI DOKUMEN ADMIN
        if (($hasVerifiedKaprodi && $hasVerifiedKabid && $hasVerifiedUser && $hasTandaTangan && !$hasVerifiedPublish && !$hasPublish  && $ajuanBaru == 'Ajuan Baru') || ($ajuanBaru == 'Lapor Kerma' && $hasVerifiedKaprodi)) {
            if (session('current_role') == 'admin' && $fileMoU) {
                return '<span style="font-size:10px!important;" class="badge bg-danger" data-title-tooltip="Proses Menunggu Publish oleh Admin">Menunggu Verifikasi Dokumen Admin</span>';
            } else if ($this->ttd_by == 'Pengusul' && session('current_role') == 'admin' && !$fileMoU) {
                if ($ajuanBaru == 'Ajuan Baru') {
                    return '<span style="font-size:10px!important;" class="badge bg-warning" data-title-tooltip="Sedang di Proses oleh ' . $this->ttd_by . '">Proses Penandatanganan</span>';
                } else if ($ajuanBaru == 'Lapor Kerma') {
                    return '<span style="font-size:10px!important;" class="badge bg-warning" data-title-tooltip="Sedang di Proses oleh ' . $this->ttd_by . '">Belum Upload Dokumen Kerja Sama</span>';
                }
            } else if (($this->ttd_by == 'BKUI' && session('current_role') == 'admin' && !$fileMoU) || $this->ttd_by == 'Pengusul' && session('current_role') == 'user' && !$fileMoU) {
                if ($this->stats_kerma == 'Lapor Kerma') {
                    $urlUploadKerma = url('pengajuan/lapor-pengajuan/' . $this->id_mou . '#draftDokumenMoU');
                } else {
                    $urlUploadKerma = url('pengajuan/tambah-pengajuan-baru/' . $this->id_mou . '#draftDokumenMoU');
                }

                return '<a href=' . $urlUploadKerma . '><span style="font-size:10px!important;" class="badge bg-danger" data-title-tooltip="Sedang di Proses oleh ' . $this->ttd_by . '">Upload Dokumen Kerma</span></a>';
            } else if (session('current_role') == 'verifikator' && !$fileMoU) {
                if ($ajuanBaru == 'Ajuan Baru' && $this->ttd_by == 'Pengusul' && $this->ttd_by == auth()->user()->username) {
                    if ($this->stats_kerma == 'Lapor Kerma') {
                        $urlUploadKerma = url('pengajuan/lapor-pengajuan/' . $this->id_mou . '#draftDokumenMoU');
                    } else {
                        $urlUploadKerma = url('pengajuan/tambah-pengajuan-baru/' . $this->id_mou . '#draftDokumenMoU');
                    }
                    return '<a href=' . $urlUploadKerma . '><span style="font-size:10px!important;" class="badge bg-danger" data-title-tooltip="Sedang di Proses oleh ' . $this->ttd_by . '">Upload Dokumen Kerma</span></a>';
                }

                if ($ajuanBaru == 'Ajuan Baru') {
                    return '<span style="font-size:10px!important;" class="badge bg-warning" data-title-tooltip="Sedang di Proses oleh ' . $this->ttd_by . '">Proses Penandatanganan</span>';
                } else if ($ajuanBaru == 'Lapor Kerma') {
                    return '<span style="font-size:10px!important;" class="badge bg-warning" data-title-tooltip="Sedang di Proses oleh ' . $this->ttd_by . '">Belum Upload Dokumen Kerja Sama</span>';
                }
            } else {
                return '<span style="font-size:10px!important;" class="badge bg-warning" data-title-tooltip="Proses Menunggu Publish oleh Admin">Proses Verifikasi Dokumen Admin</span>';
            }
        }

        // PROSES DOKUMEN RESMI
        if (empty($this->tgl_selesai) || $this->tgl_selesai == '0000-00-00 00:00:00') {
            return '<span style="font-size:10px!important;" class="badge bg-primary">Proses Publish Dokumen Resmi</span>';
        }

        if ($hasPublish) {
            return '<span style="font-size:10px!important;" class="badge bg-success">Dokumen Resmi Telah di Upload</span>';
        }

        return '<span style="font-size:10px!important;" class="badge bg-danger">Unknown</span>';
    }

    public function statusPengajuanKerjaSama()
    {
        $today = now();
        $mulai = Carbon::parse($this->mulai);
        $selesai = Carbon::parse($this->selesai);

        $awal = Carbon::parse($this->awal);
        $periode_kerma = $this->periode_kerma;
        $status_mou = $this->status_mou;

        $ProsesSelesai = $this->tgl_selesai != '0000-00-00 00:00:00';

        $diff = $selesai->diff($today);
        $durasiTersisa = "{$diff->y} Tahun {$diff->m} Bulan {$diff->d} Hari sebelum expired";

        if ($periode_kerma == 'notknown') {
            $statusPengajuanKerjaSama = '<span style="font-size:10px!important;" class="badge bg-success" data-title-tooltip="Kerja sama tidak ada Batasan"> Berjalan </span> <strong style="font-size:12px;">' . ($this->mulai == null || $this->mulai == '0000-00-00' ? Tanggal_Indo($this->awal) : Tanggal_Indo($this->mulai)) . ' -  Kerja sama tidak ada Batasan</strong>';
        } else {
            if ($mulai > $today && !$ProsesSelesai) {
                $statusPengajuanKerjaSama = '<span style="font-size:10px!important;" class="badge bg-warning" data-title-tooltip="Pengajuan masih dalam proses. Mulai: ' . $mulai->format('d-m-Y') . '">Dalam Proses</span><strong style="font-size:12px;"> Pengajuan masih dalam proses. Mulai: ' . $mulai->format('d-m-Y') . '</strong>';
            } elseif ($mulai > $today && $ProsesSelesai) {
                $statusPengajuanKerjaSama = '<span style="font-size:10px!important;" class="badge bg-warning" data-title-tooltip="Kerja Sama dimulai pada tanggal: ' . $mulai->format('d-m-Y') . '">Belum Dimulai</span>';
            } elseif ($mulai <= $today && $selesai >= $today) {
                $statusPengajuanKerjaSama = '<span style="font-size:10px!important;" class="badge bg-success" data-title-tooltip="Kerja sama akan berakhir dalam ' . $durasiTersisa . '"> Berjalan</span> <strong style="font-size:12px;"> Kerja sama akan berakhir dalam ' . $durasiTersisa . '</strong>';
            } elseif ($selesai->lt($today)) {
                $hariTerlambat = (int) $selesai->diffInDays($today);

                if (!empty($this->perpanjangan)) {
                    $statusPengajuanKerjaSama = '<span style="font-size:10px!important;" class="badge bg-info" data-title-tooltip="Sedang dalam tahap perpanjangan. Berakhir: ' . $selesai->format('d-m-Y') . '"> Dalam Perpanjangan</span> <strong style="font-size:12px;"> Sedang dalam tahap perpanjangan. Berakhir: ' . $selesai->format('d-m-Y') . '</strong>';
                }
                $statusPengajuanKerjaSama = '<span style="font-size:10px!important;" class="badge bg-danger" data-title-tooltip="Masa kerja sama telah berakhir pada ' . $selesai->format('d-m-Y') . '. Sudah terlewat ' . $hariTerlambat . ' hari">Expired</span> <strong style="font-size:12px;"> Masa kerja sama telah berakhir pada ' . $selesai->format('d-m-Y') . '. Sudah terlewat ' . $hariTerlambat . ' hari</strong>';
            }
        }


        return $statusPengajuanKerjaSama;
    }

    public function statusPengajuan()
    {
        $today = now();
        $mulai = Carbon::parse($this->mulai);
        $selesai = Carbon::parse($this->selesai);
        $ProsesSelesai = $this->tgl_selesai != '0000-00-00 00:00:00';

        $diff = $selesai->diff($today);
        $durasiTersisa = "{$diff->y} Tahun {$diff->m} Bulan {$diff->d} Hari sebelum expired";

        $periode_kerma = $this->periode_kerma;

        $statusPengajuan = '';
        if ($periode_kerma == 'notknown') {
            $statusPengajuan = '<span style="font-size:10px!important;" class="badge bg-success" data-title-tooltip="Kerja sama tidak ada Batasan"> Berjalan </span>';
        } else {
            if ($mulai > $today && !$ProsesSelesai) {
                $statusPengajuan = '<span style="font-size:10px!important;" class="badge bg-warning" data-title-tooltip="Pengajuan masih dalam proses. Mulai: ' . $mulai->format('d-m-Y') . '">Dalam Proses</span>';
            } elseif ($mulai > $today && $ProsesSelesai) {
                $statusPengajuan = '<span style="font-size:10px!important;" class="badge bg-warning" data-title-tooltip="Kerja Sama dimulai pada tanggal: ' . $mulai->format('d-m-Y') . '">Belum Dimulai</span>';
            } elseif ($mulai <= $today && $selesai >= $today) {
                $statusPengajuan = '<span style="font-size:10px!important;" class="badge bg-success" data-title-tooltip="Kerja sama akan berakhir dalam ' . $durasiTersisa . '"> Berjalan</span>';
            } elseif ($selesai->lt($today)) {
                $hariTerlambat = (int) $selesai->diffInDays($today);

                if (!empty($this->perpanjangan)) {
                    $statusPengajuan = '<span style="font-size:10px!important;" class="badge bg-info" data-title-tooltip="Sedang dalam tahap perpanjangan. Berakhir: ' . $selesai->format('d-m-Y') . '"> Dalam Perpanjangan</span>';
                }
                $statusPengajuan = '<span style="font-size:10px!important;" class="badge bg-danger" data-title-tooltip="Masa kerja sama telah berakhir pada ' . $selesai->format('d-m-Y') . '. Sudah terlewat ' . $hariTerlambat . ' hari">Expired</span>';
            }
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
                $query->where('selesai', '<', $today)->where('periode_kerma', 'bydoc');
            }
        }

        if ($request->filled('status_dokumen')) {
            $status_dokumen = $request->status_dokumen;
            $today = now();

            if ($status_dokumen == 'Dalam Proses') {
                $query->where('mulai', '>', $today);
            } elseif ($status_dokumen == 'Belum Dimulai') {
                $query->where('mulai', '>', $today);
            } elseif ($status_dokumen == 'Berjalan') {
                $query->where('mulai', '<=', $today)->where('selesai', '>=', $today)->whereNull('status_kerma');;
            } elseif ($status_dokumen == 'Dalam Perpanjangan') {
                $query->where('selesai', '<', $today)->where('status_kerma', 'Dalam Perpanjangan');
            } elseif ($status_dokumen == 'Expired') {
                $query->where('selesai', '<', $today)->where('periode_kerma', 'bydoc');
            }
        }

        if ($request->filled('status_verifikasi')) {
            $status_verifikasi = $request->status_verifikasi;
            $tglNull = '0000-00-00 00:00:00';

            if ($request->filled('status_verifikasi')) {
                $status_verifikasi = $request->status_verifikasi;
                $tglNull = '0000-00-00 00:00:00';

                switch ($status_verifikasi) {
                    case 'Proses Verifikasi Kaprodi':
                    case 'Menunggu Verifikasi Kaprodi':
                        $query->where('tgl_verifikasi_kaprodi', $tglNull)
                            ->where('tgl_selesai', $tglNull);
                        break;
                    case 'Menunggu Revisi Pengusul':
                    case 'Proses Revisi Pengusul':
                        $query->where('tgl_verifikasi_kaprodi', $tglNull)
                            ->where('status_verify_kaprodi', '0')
                            ->where('tgl_selesai', $tglNull);
                        break;

                    case 'Draft Dokumen belum di Upload':
                        $query->where('stats_kerma', 'Ajuan Baru')
                            ->where('tgl_verifikasi_kaprodi', '!=', $tglNull)
                            ->whereNull('file_ajuan')
                            ->where('tgl_draft_upload', $tglNull)
                            ->where('tgl_selesai', $tglNull);
                        break;

                    case 'Proses Verifikasi Admin':
                    case 'Menunggu Verifikasi Admin':
                        $query->where('stats_kerma', 'Ajuan Baru')
                            ->where('tgl_verifikasi_kaprodi', '!=', $tglNull)
                            ->where('tgl_verifikasi_kabid', $tglNull)
                            ->where('tgl_selesai', $tglNull);
                        break;

                    case 'Proses Verifikasi Pengusul':
                    case 'Menunggu Verifikasi Pengusul':
                        $query->where('stats_kerma', 'Ajuan Baru')
                            ->where('tgl_verifikasi_kaprodi', '!=', $tglNull)
                            ->where('tgl_verifikasi_kabid', '!=', $tglNull)
                            ->where('tgl_verifikasi_user', $tglNull)
                            ->where('tgl_selesai', $tglNull);
                        break;

                    case 'Menunggu Revisi Admin':
                    case 'Proses Revisi Admin':
                        $query->where('tgl_verifikasi_user', $tglNull)
                            ->where('status_verify_user', '0')
                            ->where('tgl_selesai', $tglNull);
                        break;

                    case 'PIC Penandatanganan Belum Dipilih':
                        $query->where('stats_kerma', 'Ajuan Baru')
                            ->whereNull('ttd_by')
                            ->where('tgl_selesai', $tglNull);
                        break;

                    case 'Proses Penandatanganan':
                    case 'Upload Dokumen Kerma':
                        $query->where('stats_kerma', 'Ajuan Baru')
                            ->where('tgl_verifikasi_kaprodi', '!=', $tglNull)
                            ->where('tgl_verifikasi_kabid', '!=', $tglNull)
                            ->where('tgl_verifikasi_user', '!=', $tglNull)
                            ->whereNotNull('ttd_by')
                            ->where('tgl_selesai', $tglNull)
                            ->where(function ($q) use ($tglNull) {
                                $q->where(function ($q1) use ($tglNull) {
                                    $q1->where('tgl_req_ttd', '!=', $tglNull)
                                        ->whereNull('file_mou');
                                })
                                    ->orWhere(function ($q2) use ($tglNull) {
                                        $q2->where('tgl_req_ttd', $tglNull)
                                            ->whereNull('file_mou');
                                    });
                            });
                        break;


                    case 'Proses Verifikasi Dokumen Admin':
                    case 'Menunggu Verifikasi Dokumen Admin':
                        $query->where(function ($q) use ($tglNull) {
                            $q->where('stats_kerma', 'Ajuan Baru')
                                ->where('tgl_verifikasi_kaprodi', '!=', $tglNull)
                                ->where('tgl_verifikasi_kabid', '!=', $tglNull)
                                ->where('tgl_verifikasi_user', '!=', $tglNull)
                                ->where('tgl_req_ttd', '!=', $tglNull)
                                ->whereNotNull('ttd_by')
                                ->whereNotNull('file_mou')
                                ->where('tgl_verifikasi_publish', $tglNull)
                                ->where('tgl_selesai', $tglNull);
                        })
                            ->orWhere(function ($q) use ($tglNull) {
                                $q->where('stats_kerma', 'Lapor Kerma')
                                    ->where('tgl_verifikasi_kaprodi', '!=', $tglNull)
                                    ->where('tgl_selesai', $tglNull);
                            });
                        break;

                    case 'Proses Publish Dokumen Resmi':
                        $query->where('tgl_selesai', $tglNull);
                        break;

                    case 'Dokumen Resmi Telah di Upload':
                        $query->where('tgl_selesai', '!=', $tglNull);
                        break;

                    case 'Pengajuan Ditolak Admin':
                        $query->where('stats_kerma', '=', 'Lapor Kerma');
                        $query->where('status_verify_admin', '0');
                        break;
                }
            }
        }

        if ($request->filled('dn_ln')) {
            $query->where('dn_ln', $request->dn_ln);
        }

        // if ($request->filled('lembaga_ums')) {
        //     $query->where('id_lembaga', $request->lembaga_ums);
        // }

        if ($request->filled('lembaga_ums')) {
            // $query->where('place_state', $request->lembaga_ums);
            $query->where('status_tempat', $request->lembaga_ums);
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
        if ($request->filled('stats_kerma')) {
            $query->where('stats_kerma', $request->stats_kerma);
        }

        return $query;
    }

    public function getStatusPengajuanAttribute()
    {
        return $this->statusPengajuan(); // atau panggil fungsi logika yang sudah ada
    }
    public function getStatusPengajuanKermaAttribute()
    {
        return $this->statusPengajuanKerjaSama(); // atau panggil fungsi logika yang sudah ada
    }


    public function getActionButtonsAttribute()
    {
        $role = session('current_role');

        $buttons = '<div class="btn-group" role="group">';
        $buttons .= $this->getRoleBasedButtons($role);
        $buttons .= $this->getUtilityButtons();
        $buttons .= '</div>';

        return $buttons;
    }

    protected function getRoleBasedButtons($role)
    {
        return match ($role) {
            'verifikator' => $this->getVerifikatorButtons(),
            'admin'       => $this->getAdminButtons(),
            'user'        => $this->getUserButtons(),
            default       => '',
        };
    }

    protected function getVerifikatorButtons()
    {
        $buttons = '';

        $isVerifiedKaprodi = $this->tgl_verifikasi_kaprodi != '0000-00-00 00:00:00';
        $isVerifiedKabid = $this->tgl_verifikasi_kabid != '0000-00-00 00:00:00';
        $isVerifiedUser = $this->tgl_verifikasi_user != '0000-00-00 00:00:00';
        $hasSignature = $this->tgl_req_ttd != '0000-00-00 00:00:00';

        $statusAjuan = $this->stats_kerma;
        $hasNotVerify = [
            'kaprodi' => $this->status_verify_kaprodi == '0' && $this->status_verify_kaprodi != null,
            'admin' => $this->status_verify_admin == '0' && $this->status_verify_admin != null,
            'user' => $this->status_verify_user == '0' && $this->status_verify_user != null,
            'publish' => $this->status_verify_publish == '0' && $this->status_verify_publish != null,
        ];

        if ($hasNotVerify['admin'] && $statusAjuan == 'Lapor Kerma') {
            return '';
        }

        // Tahap verifikasi bidang
        if (!$hasSignature && !$isVerifiedKaprodi && !$isVerifiedKabid) {
            $buttons .= $this->makeVerifikasiButton('bidang', 1, 'Verifikasi oleh Verifikator', 'btn-success', 'fa-circle-check');
        } elseif (!$hasSignature && $isVerifiedKaprodi && !$isVerifiedKabid) {
            if (!in_array(auth()->user()->jabatan, ['Kaprodi', 'Dekan', 'Kepala', 'Direktur', 'Ketua'])) {
                $buttons .= $this->makeVerifikasiButton('bidang', 0, 'Batalkan Verifikasi oleh Verifikator', 'btn-danger', 'fa-circle-xmark');
            }
        }

        // Tahap verifikasi user (pengusul)
        if (
            $isVerifiedKaprodi &&
            $isVerifiedKabid &&
            $this->stats_kerma === 'Ajuan Baru' &&
            $this->add_by === auth()->user()->username &&
            in_array(auth()->user()->jabatan, ['Kaprodi', 'Dekan', 'Direktur', 'Kepala', 'Ketua'])
        ) {
            $buttons .= $this->makeVerifikasiButton(
                'bidang',
                $isVerifiedUser ? 0 : 1,
                ($isVerifiedUser ? 'Batalkan' : 'Verifikasi') . ' oleh Pengusul',
                $isVerifiedUser ? 'btn-danger' : 'btn-success',
                $isVerifiedUser ? 'fa-circle-xmark' : 'fa-circle-check'
            );
        }

        return $buttons;
    }

    protected function getAdminButtons()
    {
        $buttons = '';

        $isVerifiedKaprodi = $this->tgl_verifikasi_kaprodi != '0000-00-00 00:00:00';
        $isVerifiedKabid = $this->tgl_verifikasi_kabid != '0000-00-00 00:00:00';
        $isVerifiedUser = $this->tgl_verifikasi_user != '0000-00-00 00:00:00';
        $isVerifiedPublish = $this->tgl_verifikasi_publish != '0000-00-00 00:00:00';
        $hasSignature = $this->tgl_req_ttd != '0000-00-00 00:00:00';
        $hasTTDby = !empty($this->ttd_by);
        $ajuanBaru = $this->stats_kerma;
        $fileMoU = $this->file_mou != null;

        $statusAjuan = $this->stats_kerma;
        $hasNotVerify = [
            'kaprodi' => $this->status_verify_kaprodi == '0' && $this->status_verify_kaprodi != null,
            'admin' => $this->status_verify_admin == '0' && $this->status_verify_admin != null,
            'user' => $this->status_verify_user == '0' && $this->status_verify_user != null,
            'publish' => $this->status_verify_publish == '0' && $this->status_verify_publish != null,
        ];

        if ($hasNotVerify['admin'] && $statusAjuan == 'Lapor Kerma') {
            return '';
        }

        if ($isVerifiedKaprodi && $isVerifiedKabid && $isVerifiedUser && !$hasSignature && !$hasTTDby && $ajuanBaru === 'Ajuan Baru') {
            $buttons .= $this->makeTTDButton('Pilih Penandatangan', 'btn-success', 'fa-file-signature');
        } elseif (!$isVerifiedPublish && $isVerifiedKaprodi && $isVerifiedKabid && !$isVerifiedUser && $ajuanBaru === 'Ajuan Baru') {
            $buttons .= $this->makeVerifikasiButton('bidang', 0, 'Batalkan Verifikasi oleh Admin', 'btn-danger', 'fa-circle-xmark');
        } elseif (!$isVerifiedPublish && $isVerifiedKaprodi && !$isVerifiedKabid && $ajuanBaru === 'Ajuan Baru') {
            $buttons .= $this->makeVerifikasiButton('bidang', 1, 'Verifikasi oleh Admin', 'btn-success', 'fa-circle-check');
        } elseif (
            ($isVerifiedPublish && $isVerifiedKaprodi && $isVerifiedKabid && $isVerifiedUser && $ajuanBaru === 'Ajuan Baru' && $fileMoU) ||
            ($ajuanBaru === 'Lapor Kerma' && $isVerifiedKaprodi && $isVerifiedPublish && $fileMoU)
        ) {
            $buttons .= $this->makeVerifikasiButton('dokumen', 0, 'Batalkan Verifikasi Dokumen MoU oleh Admin', 'btn-danger', 'fa-circle-xmark');
        } elseif (
            (!$isVerifiedPublish && $isVerifiedKaprodi && $isVerifiedKabid && $isVerifiedUser && $ajuanBaru === 'Ajuan Baru' && $fileMoU) ||
            ($ajuanBaru === 'Lapor Kerma' && $isVerifiedKaprodi && !$isVerifiedPublish && $fileMoU)
        ) {
            $buttons .= $this->makeVerifikasiButton('dokumen', 1, 'Verifikasi Dokumen MoU oleh Admin', 'btn-success', 'fa-circle-check');
        }

        return $buttons;
    }

    protected function getUserButtons()
    {
        $buttons = '';
        $isVerifiedKaprodi = $this->tgl_verifikasi_kaprodi != '0000-00-00 00:00:00';
        $isVerifiedKabid = $this->tgl_verifikasi_kabid != '0000-00-00 00:00:00';
        $isVerifiedUser = $this->tgl_verifikasi_user != '0000-00-00 00:00:00';

        $statusAjuan = $this->stats_kerma;
        $hasNotVerify = [
            'kaprodi' => $this->status_verify_kaprodi == '0' && $this->status_verify_kaprodi != null,
            'admin' => $this->status_verify_admin == '0' && $this->status_verify_admin != null,
            'user' => $this->status_verify_user == '0' && $this->status_verify_user != null,
            'publish' => $this->status_verify_publish == '0' && $this->status_verify_publish != null,
        ];

        if ($hasNotVerify['admin'] && $statusAjuan == 'Lapor Kerma') {
            return '';
        }

        if ($isVerifiedKaprodi && $isVerifiedKabid && $this->stats_kerma === 'Ajuan Baru') {
            $buttons .= $this->makeVerifikasiButton(
                'bidang',
                $isVerifiedUser ? 0 : 1,
                ($isVerifiedUser ? 'Batalkan' : 'Verifikasi') . ' oleh User',
                $isVerifiedUser ? 'btn-danger' : 'btn-success',
                $isVerifiedUser ? 'fa-circle-xmark' : 'fa-circle-check'
            );
        }

        return $buttons;
    }

    protected function getUtilityButtons()
    {
        $unreadCount = $this->getUnreadChatCount->count();
        $notifBadge = $unreadCount > 0
            ? '<span class="position-absolute top-0 start-80 translate-middle badge rounded-pill bg-danger" style="z-index:10;">' . $unreadCount . '<span class="visually-hidden">unread messages</span></span>'
            : '';

        $hideEditHapus = false;
        $hideEditHapus = false;
        $showHapus = true;
        $role = session('current_role');
        $statusAjuan = $this->stats_kerma;
        $hasVerify = [
            'kaprodi' => $this->status_verify_kaprodi == '1' && $this->status_verify_kaprodi != null,
            'admin' => $this->status_verify_admin == '1' && $this->status_verify_admin != null,
            'user' => $this->status_verify_user == '1' && $this->status_verify_user != null,
            'publish' => $this->status_verify_publish == '1' && $this->status_verify_publish != null,
        ];
        $hasNotVerify = [
            'kaprodi' => $this->status_verify_kaprodi == '0' && $this->status_verify_kaprodi != null,
            'admin' => $this->status_verify_admin == '0' && $this->status_verify_admin != null,
            'user' => $this->status_verify_user == '0' && $this->status_verify_user != null,
            'publish' => $this->status_verify_publish == '0' && $this->status_verify_publish != null,
        ];

        if ($hasVerify['admin'] && $role != 'admin' && $this->ttd_by == 'BKUI') {
            $hideEditHapus = true;
        }
        if ($hasVerify['admin'] && $this->ttd_by == 'Pengusul') {
            $showHapus = false;
        }

        if ($this->add_by != auth()->user()->username && $role != 'admin') {
            $hideEditHapus = true;
        }

        if ($hasVerify['user'] && $role == 'admin') {
            $showHapus = false;
        }

        if ($this->stats_kerma == 'Lapor Kerma' && $hasVerify['kaprodi'] && $hasNotVerify['publish']) {
            $hideEditHapus = true;
        }

        $chatBtn = <<<HTML
            <a href="{$this->getChatUrl()}" data-title-tooltip="Chat" class="btn btn-secondary btn-chat position-relative" data-id_mou="{$this->id_mou}">
                <i class="bx bx-chat"></i>
                {$notifBadge}
            </a>
            <button data-title-tooltip="Lihat Detail" class="btn btn-info btn-detail" data-srcPdf="{$this->getFileMouUrl()}" data-id_mou="{$this->id_mou}">
                <i class="bx bx-show"></i>
            </button>
        HTML;

        $detailBtn = <<<HTML
            <button data-title-tooltip="Lihat Detail" class="btn btn-info btn-detail" data-srcPdf="{$this->getFileMouUrl()}" data-id_mou="{$this->id_mou}">
                <i class="bx bx-show"></i>
            </button>
        HTML;

        if ($hasNotVerify['admin'] && $statusAjuan == 'Lapor Kerma') {
            return $detailBtn;
        }

        $editDelete = '';
        if (!$hideEditHapus) {
            $editDelete = <<<HTML
                <a href="{$this->getEditUrl()}" data-title-tooltip="Edit Pengajuan" class="btn btn-warning btn-edit" data-id_mou="{$this->id_mou}">
                    <i class="bx bx-edit"></i>
                </a>
            HTML;

            if ($showHapus) {
                $editDelete = <<<HTML
                    <a href="{$this->getEditUrl()}" data-title-tooltip="Edit Pengajuan" class="btn btn-warning btn-edit" data-id_mou="{$this->id_mou}">
                        <i class="bx bx-edit"></i>
                    </a>
                    <button data-title-tooltip="Hapus Pengajuan" class="btn btn-danger btn-hapus" data-id_mou="{$this->id_mou}">
                        <i class="bx bx-trash"></i>
                    </button>
                HTML;
            }
        }

        return $chatBtn . $editDelete;
    }


    protected function makeVerifikasiButton($tipe, $status, $tooltip, $btnClass, $iconClass)
    {
        return <<<HTML
        <button data-title-tooltip="{$tooltip}" class="btn {$btnClass} btn-verifikasi" data-tipe="{$tipe}" data-id_mou="{$this->id_mou}" data-status="{$status}">
            <i class="fa-solid {$iconClass}"></i>
        </button>
    HTML;
    }

    protected function makeTTDButton($tooltip, $btnClass, $iconClass)
    {
        return <<<HTML
        <button data-title-tooltip="{$tooltip}" class="btn {$btnClass} btn-ttd" data-id_mou="{$this->id_mou}" data-status="1">
            <i class="fa-solid {$iconClass}"></i>
        </button>
    HTML;
    }

    protected function getChatUrl()
    {
        return route('chat.home', ['id_mou' => $this->id_mou]);
    }

    protected function getEditUrl()
    {
        if ($this->stats_kerma == 'Lapor Kerma') {
            return route('pengajuan.editLaporPengajuan', ['id' => $this->id_mou]);
        } else {
            return route('pengajuan.editBaru', ['id' => $this->id_mou]);
        }
    }

    protected function getFileMouUrl()
    {
        return getDocumentUrl(@$this->file_mou, 'file_mou');
    }



    public function getImplementasiButtonAttribute()
    {
        return '<div class="d-flex justify-content-center">
                            <div class="btn-group" role="group">
                                <button data-title-tooltip="Implementasi" class="btn btn-outline-primary btn-implementasi" data-id_mou="' .
            $this->id_mou .
            '">
                                    <i class="bx bx-task"></i>
                                </button>
                            </div>
                        </div>';
    }

    public function getDokumenButtonAttribute()
    {
        $action = '';

        $action .= '<div class="btn-group" role="group">';

        // $action .= '<button data-title-tooltip="Lihat Detail" class="btn btn-info btn-detail" data-srcPdf="' . asset('storage/' . $this->file_mou) . '" data-id_mou="' . $this->id_mou . '">
        $action .= '<button data-title-tooltip="Lihat Detail" class="btn btn-info btn-detail" data-srcPdf="' . getDocumentUrl(@$this->file_mou, 'file_mou') . '" data-id_mou="' . $this->id_mou . '">
                <i class="bx bx-show"></i>
            </button>';
        // if (session('current_role') == 'admin') {
        //     if ($this->stats_kerma == 'Lapor Kerma') {
        //         $urlEdit = route('pengajuan.editLaporPengajuan', ['id' => $this->id_mou]);
        //     } else {
        //         $urlEdit = route('pengajuan.editBaru', ['id' => $this->id_mou]);
        //     }

        //     $action .= '<a href="' . $urlEdit .
        //         '" data-title-tooltip="Edit Pengajuan" class="btn btn-warning btn-edit" data-id_mou="' .
        //         $this->id_mou .
        //         '">
        //                     <i class="bx bx-edit"></i>
        //                 </a>';

        //     $action .= '<button data-title-tooltip="Hapus Pengajuan" class="btn btn-danger btn-hapus" data-id_mou="' .
        //         $this->id_mou .
        //         '">
        //             <i class="bx bx-trash"></i>
        //         </button>
        //     </div>';
        // }

        return $action;
    }
}
