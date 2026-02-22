<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AjuanHibah extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_ajuan_hibah';
    protected $primaryKey = 'id_hibah'; // Gunakan primary key yang benar
    public $incrementing = false; // Jika primary key bukan auto-increment
    protected $keyType = 'string';
    protected $guarded = [];

    protected $appends = [
        'status_label',
        'tanggal_pelaksanaan_label',
        'jenis_hibah_label',
        'file_kontrak_label',
        'action_buttons_label',
    ];

    public function scopeWithFullJoin(Builder $query): Builder
    {
        return $query->select(
            'tbl_ajuan_hibah.*',
            'tbl_ajuan_hibah.jenis_hibah as id_jenis_hibah',
            'tbl_laporan_hibah.id_laporan_hibah',
            'kerma_db.nama_institusi',
            'kerma_db.dn_ln',
            'kerma_db.jenis_institusi',
            'lmbg_place_state.nama_lmbg as nama_place_state',
            'lmbg_fakultas.nama_lmbg as nama_fakultas',
            'lmbg_prodi.nama_lmbg as nama_prodi',
            'pengusul.name as nama_pengusul',
            'admin.name as nama_admin',
            'kaprodi.name as nama_verifikator_kaprodi',
            'dekan.name as nama_verifikator_dekan',
            'kaprodi_ref.name as nama_kaprodi_ref',
            'dekan_ref.name as nama_dekan',
            'jenis_hibah.jenis_hibah',
            'jenis_hibah.maksimum',
            'jenis_hibah.dl_proposal',
            'jenis_hibah.dl_laporan'
        )
            ->leftJoin('kerma_db', 'kerma_db.id_mou', '=', 'tbl_ajuan_hibah.id_mou')
            ->leftJoin('tbl_laporan_hibah', 'tbl_laporan_hibah.id_hibah', '=', 'tbl_ajuan_hibah.id_hibah')
            ->leftJoin('ref_lembaga_ums as lmbg_place_state', 'lmbg_place_state.id_lmbg', '=', 'tbl_ajuan_hibah.place_state')
            ->leftJoin('ref_lembaga_ums as lmbg_fakultas', 'lmbg_fakultas.id_lmbg', '=', 'tbl_ajuan_hibah.fakultas')
            ->leftJoin('ref_lembaga_ums as lmbg_prodi', 'lmbg_prodi.id_lmbg', '=', 'tbl_ajuan_hibah.prodi')
            ->leftJoin('users as pengusul', 'pengusul.username', '=', 'tbl_ajuan_hibah.add_by')
            ->leftJoin('users as admin', 'admin.username', '=', 'tbl_ajuan_hibah.verify_admin_by')
            ->leftJoin('users as kaprodi', 'kaprodi.username', '=', 'tbl_ajuan_hibah.verify_kaprodi_by')
            ->leftJoin('users as dekan', 'dekan.username', '=', 'tbl_ajuan_hibah.verify_dekan_by')
            ->leftJoin('users as kaprodi_ref', function ($join) {
                $join->on('kaprodi_ref.jabatan', DB::raw("'Kaprodi'"))
                    ->on('kaprodi_ref.status_tempat', '=', 'tbl_ajuan_hibah.prodi');
            })->leftJoin('users as dekan_ref', function ($join) {
                $join->on('dekan_ref.jabatan', DB::raw("'Dekan'"))
                    ->on('dekan_ref.status_tempat', '=', 'tbl_ajuan_hibah.fakultas');
            })
            ->leftJoin('ref_jenis_hibah as jenis_hibah', 'jenis_hibah.id', '=', 'tbl_ajuan_hibah.jenis_hibah');
    }

    public function scopeFilteredByRole(Builder $query): Builder
    {
        if (session('current_role') !== 'admin') {
            $query->where('tbl_ajuan_hibah.add_by', auth()->user()->username);
        }

        return $query;
    }

    public function getPengusul()
    {
        return $this->hasOne(User::class, 'username', 'add_by');
    }

    public function getVerifikatorAdmin()
    {
        return $this->hasOne(User::class, 'username', 'verify_admin_by');
    }
    public function getVerifikatorKaprodi()
    {
        return $this->hasOne(User::class, 'username', 'verify_dekan_by');
    }
    public function getVerifikatorDekan()
    {
        return $this->hasOne(User::class, 'username', 'verify_dekan_by');
    }

    public function scopeFilterDokumen(Builder $query, $request)
    {
        if ($request->filled('status')) {
            $status = $request->status;
            $tglNull = '0000-00-00 00:00:00';

            switch ($status) {
                case 'Menunggu Verifikasi Kaprodi':
                    $query->where(function ($q) {
                        $q->whereNull('tbl_ajuan_hibah.status_verify_kaprodi')
                            ->where(function ($sub) {
                                $sub->where('tbl_ajuan_hibah.status_revisi_kaprodi', '1')
                                    ->orWhereNull('tbl_ajuan_hibah.status_revisi_kaprodi');
                            })
                            ->where(function ($sub) {
                                $sub->whereNull('tbl_ajuan_hibah.status_verify_dekan')
                                    ->whereNull('tbl_ajuan_hibah.status_verify_admin')
                                    ->whereNull('tbl_ajuan_hibah.file_kontrak')
                                    ->whereNull('tbl_ajuan_hibah.status_verify_laporan');
                            });
                    });
                    break;

                case 'Menunggu Verifikasi Dekan':
                    $query->where(function ($q) {
                        $q->whereNull('tbl_ajuan_hibah.status_verify_dekan')
                            ->where(function ($sub) {
                                $sub->where('tbl_ajuan_hibah.status_revisi_dekan', '1')
                                    ->orWhereNull('tbl_ajuan_hibah.status_revisi_dekan');
                            })
                            ->whereNull('tbl_ajuan_hibah.status_verify_admin')
                            ->whereNull('tbl_ajuan_hibah.file_kontrak')
                            ->whereNull('tbl_ajuan_hibah.status_verify_laporan');
                    });
                    break;

                case 'Under Review Admin':
                    $query->where(function ($q) {
                        $q->where(function ($sub) {
                            $sub->where('tbl_ajuan_hibah.status_verify_kaprodi', '1')
                                ->orWhere('tbl_ajuan_hibah.status_verify_dekan', '1');
                        })
                            ->where(function ($sub) {
                                $sub->where('tbl_ajuan_hibah.status_revisi_admin', '1')
                                    ->orWhereNull('tbl_ajuan_hibah.status_revisi_admin');
                            })
                            ->where(function ($vadmin) {
                                $vadmin->whereNull('tbl_ajuan_hibah.status_verify_admin')
                                    ->orwhere('tbl_ajuan_hibah.status_verify_admin', '0');
                            })
                            ->whereNull('tbl_ajuan_hibah.file_kontrak')
                            ->whereNull('tbl_ajuan_hibah.status_verify_laporan');
                    });
                    break;

                case 'Upload TTD Kontrak':
                    $query->where(function ($q) {
                        $q->where(function ($sub) {
                            $sub->where('tbl_ajuan_hibah.status_verify_kaprodi', '1')
                                ->orWhere('tbl_ajuan_hibah.status_verify_dekan', '1');
                        })
                            ->where('tbl_ajuan_hibah.status_verify_admin', '1')
                            ->whereNull('tbl_ajuan_hibah.file_kontrak');
                    });
                    break;
                case 'Proses TTD Kontrak Admin':
                    $query->where(function ($q) {
                        $q->where(function ($sub) {
                            $sub->where('tbl_ajuan_hibah.status_verify_kaprodi', '1')
                                ->orWhere('tbl_ajuan_hibah.status_verify_dekan', '1');
                        })
                            ->where('tbl_ajuan_hibah.status_verify_admin', '1')
                            ->whereNull('tbl_ajuan_hibah.file_kontrak');
                    });
                    break;

                case 'Proses Pencairan Tahap 1':
                    $query->where(function ($q) {
                        $q->where(function ($sub) {
                            $sub->where('tbl_ajuan_hibah.status_verify_kaprodi', '1')
                                ->orWhere('tbl_ajuan_hibah.status_verify_dekan', '1');
                        })
                            ->where('tbl_ajuan_hibah.status_verify_admin', '1')
                            ->whereNotNull('tbl_ajuan_hibah.file_kontrak')
                            ->whereNull('tbl_ajuan_hibah.status_verify_tahap_satu');
                    });
                    break;

                case 'Menunggu Verifikasi Laporan':
                    $query->where(function ($q) {
                        $q->where(function ($sub) {
                            $sub->where('tbl_ajuan_hibah.status_verify_kaprodi', '1')
                                ->orWhere('tbl_ajuan_hibah.status_verify_dekan', '1');
                        })
                            ->where('tbl_ajuan_hibah.status_verify_admin', '1')
                            ->whereNotNull('tbl_ajuan_hibah.file_kontrak')
                            ->whereNotNull('tbl_laporan_hibah.id_laporan_hibah')
                            ->whereNull('tbl_ajuan_hibah.date_verify_laporan')
                            ->where(function ($sub) {
                                $sub->where('tbl_ajuan_hibah.status_revisi_laporan', '1')
                                    ->orWhereNull('tbl_ajuan_hibah.status_revisi_laporan');
                            });
                    });
                    break;

                case 'Proses Pencairan Tahap 2':
                    $query->where(function ($q) {
                        $q->where(function ($sub) {
                            $sub->where('tbl_ajuan_hibah.status_verify_kaprodi', '1')
                                ->orWhere('tbl_ajuan_hibah.status_verify_dekan', '1');
                        })
                            ->where('tbl_ajuan_hibah.status_verify_admin', '1')
                            ->whereNotNull('tbl_ajuan_hibah.file_kontrak')
                            ->whereNotNull('tbl_laporan_hibah.id_laporan_hibah')
                            ->where('tbl_ajuan_hibah.status_verify_laporan', '1')
                            ->whereNull('tbl_ajuan_hibah.status_verify_tahap_dua');
                    });
                    break;

                case 'Selesai':
                    $query->where('tbl_ajuan_hibah.status_selesai', '1')->whereNotNull('tbl_ajuan_hibah.date_selesai');
                    break;

                case 'Expired':
                    $today = Carbon::today()->toDateString();
                    $query->where(function ($q) use ($today) {
                        $q->where(function ($sub) use ($today) {
                            $sub->where('tbl_ajuan_hibah.file_kontrak', null)
                                ->where('jenis_hibah.dl_proposal', '<', $today);
                        })->orWhere(function ($sub) use ($today) {
                            $sub->whereNotNull('tbl_ajuan_hibah.file_kontrak')
                                ->where('jenis_hibah.dl_laporan', '<', $today)
                                ->whereNull('tbl_laporan_hibah.id_laporan_hibah')
                                ->whereNull('tbl_ajuan_hibah.status_verify_laporan');
                        });
                    });
                    break;
            }
        }

        if ($request->filled('judul_proposal')) {
            $query->where('tbl_ajuan_hibah.judul_proposal', $request->judul_proposal);
        }
        if ($request->filled('nama_institusi')) {
            $query->where('tbl_ajuan_hibah.institusi_mitra', $request->nama_institusi);
        }

        if ($request->filled('program_studi')) {
            $query->where('tbl_ajuan_hibah.prodi', $request->program_studi);
        }

        if ($request->filled('fakultas')) {
            $query->where('tbl_ajuan_hibah.fakultas', $request->fakultas);
        }

        if ($request->filled('jenis_hibah')) {
            $query->where('tbl_ajuan_hibah.jenis_hibah', $request->jenis_hibah);
        }


        return $query;
    }


    public function getStatusLabelAttribute()
    {
        $today = Carbon::today();
        $tglDLProposal = Carbon::parse($this->dl_proposal);
        $tglDLLaporan = Carbon::parse($this->dl_laporan);
        $role = session('current_role');

        $status = '';
        $isSubmitted = $this->is_submit == '1';
        $isKaprodi = $this->jabatan_pengusul == 'Kaprodi';
        $isPenanggungJawab = $this->penanggung_jawab_kegiatan;
        $jenisHibah = $this->jenis_hibah;

        // Status Boolean
        $verif = [
            'kaprodi' => $this->status_verify_kaprodi == '1',
            'dekan' => $this->status_verify_dekan == '1',
            'admin' => $this->status_verify_admin == '1',
            'tahap_satu' => $this->status_verify_tahap_satu == '1',
            'laporan' => $this->status_verify_laporan == '1',
            'tahap_dua' => $this->status_verify_tahap_dua == '1',
            'selesai' => $this->status_selesai == '1',
        ];

        $revisi = [
            'kaprodi' => $this->status_revisi_kaprodi,
            'dekan' => $this->status_revisi_dekan,
            'admin' => $this->status_revisi_admin,
            'laporan' => $this->status_revisi_laporan,
        ];

        // Data Kehadiran
        $has = [
            'file_kontrak' => !is_null($this->file_kontrak),
            'laporan' => !is_null($this->id_laporan_hibah),
            'verify_laporan' => !is_null($this->date_verify_laporan),
            'verify_tahap_satu' => !is_null($this->date_verify_tahap_satu),
            'verify_tahap_dua' => !is_null($this->date_verify_tahap_dua),
            'selesai' => !is_null($this->date_selesai),
        ];

        // === Penentuan Expired Proposal ===
        $expProposal = false;
        // if ($role === 'user') {
        $expProposal = $today->gt($tglDLProposal) && (!$verif['kaprodi'] && !$verif['dekan']);
        // } elseif (in_array($role, ['admin', 'verifikator'])) {
        //     $expProposal = $today->gt($tglDLProposal->addDays(1)) && (!$verif['kaprodi'] && !$verif['dekan']);
        // } else {
        //     $expProposal = $today->gt($tglDLProposal) && !$has['file_kontrak'];
        // }

        $expLaporan = $today->gt($tglDLLaporan) && $has['file_kontrak'] && !$has['laporan'] && !$has['verify_laporan'];

        // === Label Status ===
        if (!$isSubmitted) {
            if ($jenisHibah && $expProposal) {
                return $this->badge('Expired', 'bg-dark', 'Sudah Melebihi Batas Deadline Proposal');
            }
            return $this->badge('Draft Pengajuan', 'bg-warning', 'Draft Pengajuan');
        }

        if ($expProposal) {
            $daysDiff = $today->diffInDays($tglDLProposal, false);
            if ($daysDiff == -1) {
                if ($verif['kaprodi'] == 0 || $verif['dekan'] == 0) {
                    if ($isPenanggungJawab == 'dekan') {
                        return $this->revisiBadge('Revisi dari Dekan', 'catatan_dekan', auth()->user()->jabatan === 'Dekan' || $role === 'verifikator');
                    } else if ($isPenanggungJawab == 'kaprodi') {
                        return $this->revisiBadge('Revisi dari Kaprodi', 'catatan_kaprodi', auth()->user()->jabatan === 'Kaprodi' || $role === 'verifikator');
                    }
                } else {
                    return $this->badge('Expires Soon', 'bg-danger', 'Hari terakhir verifikasi pengajuan.');
                }
            } elseif ($daysDiff < -1) {
                return $this->badge('Expired', 'bg-dark', 'Sudah melebihi batas deadline proposal.');
            }
        }


        // === Status State Conditions ===
        if (($revisi['kaprodi'] === '1' || is_null($revisi['kaprodi']))
            && $isPenanggungJawab === 'kaprodi'
            && !$isKaprodi && !$verif['kaprodi'] && !$verif['dekan'] && !$verif['admin'] && !$has['file_kontrak'] && !$verif['laporan'] && !$has['laporan']
        ) {
            return $this->badge('Menunggu Verifikasi Kaprodi', auth()->user()->jabatan === 'Kaprodi' || $role === 'verifikator' ? 'bg-danger' : 'bg-primary');
        }

        if ($revisi['kaprodi'] === '0' && !$isKaprodi && !$verif['kaprodi'] && !$verif['dekan'] && !$verif['admin'] && !$has['file_kontrak']) {
            return $this->revisiBadge('Revisi dari Kaprodi', 'catatan_kaprodi', auth()->user()->jabatan === 'Kaprodi' || $role === 'verifikator');
        }

        if (($revisi['dekan'] === '1' || is_null($revisi['dekan'])) && $isPenanggungJawab === 'dekan' && !$verif['dekan'] && !$verif['admin'] && !$has['file_kontrak']) {
            return $this->badge('Menunggu Verifikasi Dekan', auth()->user()->jabatan === 'Dekan' || ($role === 'verifikator' && $this->add_by != auth()->user()->username) ? 'bg-danger' : 'bg-primary');
        }

        if ($revisi['dekan'] === '0' && !$verif['dekan'] && !$verif['admin'] && !$has['file_kontrak']) {
            return $this->revisiBadge(
                'Menunggu Revisi Pengusul',
                'catatan_dekan',
                $this->add_by != auth()->user()->username
                // auth()->user()->jabatan === 'Dekan' || $role === 'verifikator'
            );
        }


        if (($revisi['admin'] === '1' || is_null($revisi['admin'])) && ($verif['kaprodi'] || $verif['dekan']) && !$verif['admin'] && !$has['file_kontrak']) {
            return $this->badge('Under Review Admin', $role === 'admin' ? 'bg-danger' : 'bg-primary');
        }

        if ($revisi['admin'] === '0' && ($verif['kaprodi'] || $verif['dekan']) && !$verif['admin'] && !$has['file_kontrak']) {
            return $this->revisiBadge('Menunggu Revisi Pengusul oleh Admin', 'catatan_admin', $role === 'admin' || auth()->user()->username != $this->add_by);
        }

        if (($verif['kaprodi'] || $verif['dekan']) && $verif['admin'] && !$has['file_kontrak']) {
            return $this->badge('Upload TTD Kontrak', $role === 'admin' ? 'bg-danger' : 'bg-primary');
        }

        if (($verif['kaprodi'] || $verif['dekan']) && $verif['admin'] && $has['file_kontrak'] && !$verif['tahap_satu']) {
            return $this->badge('Proses Pencairan Tahap 1', $role === 'admin' ? 'bg-danger' : 'bg-primary', '', '1');
        }

        if ($verif['admin'] && $has['file_kontrak'] && !$has['laporan'] && $verif['tahap_satu']) {
            if ($expLaporan) {
                return "<a href='" . route('hibah.isiLaporan', ['id_hibah' => $this->id_hibah]) . "'>" . $this->badge('Overdue', 'bg-danger', 'Sudah Melebihi Batas Deadline Laporan. Segera Mengisi Laporan') . "</a>";
            }
            if (auth()->user()->username === $this->add_by) {
                return "<a href='" . route('hibah.isiLaporan', ['id_hibah' => $this->id_hibah]) . "' class='btn btn-sm btn-danger' data-title-tooltip='Edit Laporan Hibah'>Edit Laporan Hibah</a>";
            }
            return $this->badge('Pengusul Belum Mengisi Laporan', 'bg-primary');
        }

        if (($revisi['laporan'] === '1' || is_null($revisi['laporan'])) && ($verif['kaprodi'] || $verif['dekan']) && $verif['admin'] && $has['file_kontrak'] && $has['laporan'] && !$verif['laporan']) {
            return $this->badge('Menunggu Verifikasi Laporan', $role === 'admin' ? 'bg-danger' : 'bg-primary', '', null, true);
        }

        if ($revisi['laporan'] === '0' && $verif['admin'] && $has['file_kontrak'] && $has['laporan'] && !$verif['laporan']) {
            return $this->revisiBadge('Menunggu Revisi Laporan oleh Pengusul', 'catatan_laporan', $role === 'admin');
        }

        if (($verif['kaprodi'] || $verif['dekan']) && $verif['admin'] && $has['file_kontrak'] && $has['laporan'] && $verif['laporan'] && !$verif['tahap_dua']) {
            return $this->badge('Proses Pencairan Tahap 2', $role === 'admin' ? 'bg-danger' : 'bg-primary', '', '2');
        }

        if ($verif['selesai'] && $has['selesai']) {
            return $this->badge('Selesai', 'bg-success', 'Selesai');
        }

        if ($revisi['admin'] === '0' && ($verif['kaprodi'] || $verif['dekan']) && !$verif['admin']) {
            return $this->revisiBadge('Menunggu Revisi Pengusul oleh Admin', 'catatan_admin', $role === 'admin');
        }

        return $this->badge('Selesaikan Ajuan', $role === 'admin' ? 'bg-danger' : 'bg-primary', 'Pencairan Tahap 2 Selesai');
    }

    private function badge($label, $color, $tooltip = '', $tahap = null, $isLaporan = false)
    {
        $tooltip = $tooltip == '' ? $label : $tooltip;
        $extra = $tahap ? " data-tahap='$tahap'" : '';
        $extra .= $isLaporan ? " class='btn-detail-laporan' data-id_laporan_hibah='{$this->id_laporan_hibah}'" : '';
        return "<span class='badge $color'$extra style='font-size:10px!important;' data-title-tooltip='$tooltip'>$label</span>";
    }

    private function revisiBadge($label, $catatanKey, $isPrimary)
    {
        $color = $isPrimary ? 'bg-primary' : 'bg-danger';
        return "<span class='badge $color' style='font-size:10px!important;cursor:pointer;' data-title-tooltip='Klik untuk Melihat Revisi' onclick=\"showRevisi('" . $this->id_hibah . "','" . $catatanKey . "')\">$label</span>";
    }


    public function getTanggalPelaksanaanLabelAttribute()
    {
        $tglMulai = $this->tgl_mulai ? TanggalIndonesia($this->tgl_mulai) : '-';
        $tglSelesai = $this->tgl_selesai ? TanggalIndonesia($this->tgl_selesai) : '-';
        return "<span class='badge bg-primary' style='font-size:10px!important;'>Mulai: {$tglMulai}</span> <span class='badge bg-secondary' style='font-size:10px!important;'>Selesai: {$tglSelesai}</span>";
    }
    public function getJenisHibahLabelAttribute()
    {
        if ($this->jenis_hibah != null) {
            $tglDLProposal = $this->dl_proposal ? TanggalIndonesia($this->dl_proposal) : '-';
            $tglDLLaporan = $this->dl_laporan ? TanggalIndonesia($this->dl_laporan) : '-';
            return "<span class='badge bg-primary' style='font-size:10px!important;'>{$this->nama_jenis_hibah}</span><span class='badge bg-dark' style='font-size:10px!important;'>DL Proposal: {$tglDLProposal}</span> <span class='badge bg-secondary' style='font-size:10px!important;'>DL Laporan: {$tglDLLaporan}</span>";
        } else {
            return "<span class='badge bg-danger' style='font-size:10px!important;'>Belum Memilih Jenis Hibah</span>";
        }
    }

    public function getFileKontrakLabelAttribute()
    {
        $isVerifKaprodi = $this->status_verify_kaprodi == '1';
        $isVerifDekan = $this->status_verify_dekan == '1';
        $isVerifAdmin = $this->status_verify_admin == '1';
        $isFileKontrak = $this->file_kontrak != null;

        $MenungguProsesTTD = ($isVerifKaprodi || $isVerifDekan) && ($isVerifAdmin && !$isFileKontrak);

        $today = Carbon::today();
        // $tglDLProposal = $this->dl_proposal;
        // $tglDLLaporan = $this->dl_laporan;
        $tglDLProposal = Carbon::parse($this->dl_proposal);
        $tglDLLaporan = Carbon::parse($this->dl_laporan);

        $verif = [
            'kaprodi' => $this->status_verify_kaprodi == '1',
            'dekan' => $this->status_verify_dekan == '1',
            'admin' => $this->status_verify_admin == '1',
            'tahap_satu' => $this->status_verify_tahap_satu == '1',
            'laporan' => $this->status_verify_laporan == '1',
            'tahap_dua' => $this->status_verify_tahap_dua == '1',
            'selesai' => $this->status_selesai == '1',
        ];

        // $expProposal = ($today > $tglDLProposal) && !$isFileKontrak;
        $expProposal = $today->gt($tglDLProposal) && (!$verif['kaprodi'] && !$verif['dekan']);

        $expLaporan = ($today > $tglDLLaporan) && $isFileKontrak && $this->id_laporan_hibah == null && $this->date_verify_laporan == null;

        if ($expProposal || $expLaporan) {
            if ($expProposal) {
                return '-';
            } else {
                $action = '<div class="btn-group" role="group">';

                $action .= '<button class="btn btn-sm btn-dark btn-lihat-file"  data-title-tooltip="Lihat File Kontrak" data-id_hibah="' . $this->id_hibah . '" data-url="' . asset("storage/" . $this->file_kontrak)  . '" >
                                           <i class="bx bx-file"></i> 
                        </button>';

                $action .= '</div>';
                return $action;
                // return '<a href="' . asset("storage/" . $this->file_kontrak) . '" class="btn btn-sm btn-primary"><i
                //             class="bx bx-download"></i></a>';
            }
        } else {

            if ($this->file_kontrak) {
                $action = '<div class="btn-group" role="group">';

                $action .= '<button class="btn btn-sm btn-dark btn-lihat-file"  data-title-tooltip="Lihat File Kontrak" data-id_hibah="' . $this->id_hibah . '" data-url="' . asset("storage/" . $this->file_kontrak)  . '" >
                                           <i class="bx bx-file"></i> 
                        </button>';

                $action .= '</div>';
                return $action;
                // return '<a href="' . asset("storage/" . $this->file_kontrak) . '" target="_blank" class="btn btn-sm btn-primary"><i
                //             class="bx bx-download"></i></a>';
            } else {
                if ($MenungguProsesTTD) {
                    if (session('current_role') == 'admin') {
                        $id = $this->id_hibah;
                        $flag = 'file_kontrak';
                        return '
                                <form id="uploadForm-' . $id . '-' . $flag . '" enctype="multipart/form-data" style="display: inline;" onsubmit="return false;">
                                    <input type="file" name="file" style="display:none;" 
                                        onchange="uploadFile(\'' . $id . '\', \'' . $flag . '\')" 
                                        id="fileInput-' . $id . '-' . $flag . '" accept=".pdf" />
                                    <button type="button" onclick="$(\'#fileInput-' . $id . '-' . $flag . '\').click();" 
                                            class="btn btn-sm btn-warning" data-title-tooltip="Upload File Kontrak">
                                        <i class="bx bx-upload"></i>
                                    </button>

                                    <div class="spinner-border text-primary ms-2 d-none" role="status" 
                                        id="loader-' . $id . '-' . $flag . '" style="width: 1rem; height: 1rem;">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </form>';
                    } else {
                        return '<small class="text-danger">Belum Ada File Upload</small>';
                    }
                } else {
                    return '<small class="text-danger">Proses Verifikasi Belum Selesai</small>';
                }
            }
        }
    }

    // public function getActionButtonsLabelAttribute()
    // {
    //     $today = Carbon::today();

    //     // Variabel Status
    //     $isKaprodi         = $this->jabatan_pengusul == 'Kaprodi';
    //     $isPenanggungJawab = $this->penanggung_jawab_kegiatan;
    //     $statusVerifyKaprodi  = $this->status_verify_kaprodi == '1';
    //     $statusVerifyDekan    = $this->status_verify_dekan == '1';
    //     $statusVerifyAdmin    = $this->status_verify_admin == '1';
    //     $statusVerifyTahapSatu = $this->status_verify_tahap_satu == '1';
    //     $statusVerifyLaporan  = $this->status_verify_laporan == '1';
    //     $statusVerifyTahapDua = $this->status_verify_tahap_dua == '1';
    //     $statusSelesai        = $this->status_selesai == '1';
    //     $statusSubmit        = $this->is_submit == '1';

    //     // Revisi
    //     $revisiKaprodi  = $this->status_revisi_kaprodi === '0';
    //     $revisiDekan    = $this->status_revisi_dekan === '0';
    //     $revisiAdmin    = $this->status_revisi_admin === '0';
    //     $revisiLaporan  = $this->status_revisi_laporan === '0';

    //     // File / Dokumen
    //     $isFileKontrak   = !empty($this->file_kontrak);
    //     $isUploadLaporan = !empty($this->id_laporan_hibah);


    //     // Deadline
    //     $isExpiredProposal = $today > $this->dl_proposal && !$isFileKontrak;
    //     $isOverdueLaporan  = $today > $this->dl_laporan && !$isUploadLaporan && $statusVerifyTahapSatu;

    //     // Button Container
    //     $action  = '<div class="btn-group" role="group">';
    //     $editUrl = route('hibah.edit', ['id_hibah' => $this->id_hibah]);
    //     $isiLaporanUrl = route('hibah.isiLaporan', ['id_hibah' => $this->id_hibah]);

    //     // Tombol Print
    //     $action .= '
    //         <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-title-tooltip="Print Ajuan Hibah">
    //             <i class="bx bx-printer"></i>
    //         </button>
    //         <ul class="dropdown-menu">
    //             <li><a class="dropdown-item btn-export-proposal" href="#" data-id_hibah="' . $this->id_hibah . '"><i class="bx bx-file"></i> Cetak Proposal</a></li>
    //             <li><a class="dropdown-item btn-export-laporan" href="#" data-id_hibah="' . $this->id_hibah . '"><i class="bx bx-book"></i> Cetak Laporan</a></li>
    //         </ul>
    //     ';

    //     // Tombol Detail
    //     $action .= '<button data-title-tooltip="Detail Ajuan Hibah" class="btn btn-secondary btn-detail" data-id_hibah="' . $this->id_hibah . '"><i class="bx bx-detail"></i></button>';

    //     if (!$statusSubmit) {
    //         $action .= '<button onclick="window.location.href=\'' . $editUrl . '\'" class="btn btn-warning" data-title-tooltip="Edit Draft"><i class="bx bx-edit"></i></button>';
    //         $action .= '<button class="btn btn-danger btn-hapus" data-title-tooltip="Hapus Draft" data-id_hibah="' . $this->id_hibah . '"><i class="bx bx-trash"></i></button>';
    //     } else {
    //         // if (session('current_role') != 'admin' && $this->id_laporan_hibah && $statusVerifyLaporan || session('current_role') == 'admin' && $this->id_laporan_hibah) {
    //         //     $action .= '<button data-title-tooltip="Detail Laporan Hibah" class="btn btn-purple btn-detail-laporan" data-id_laporan_hibah="' . $this->id_laporan_hibah . '"><i class="bx bx-clipboard"></i></button>';
    //         // }

    //         // ADMIN - Verifikasi Pencairan Tahap 1 & 2
    //         if ($statusVerifyAdmin && $isFileKontrak && !$statusVerifyTahapSatu && session('current_role') == 'admin') {
    //             $action .= '<button class="btn btn-success verifikasi-pencairan" data-title-tooltip="Verifikasi Pencairan Tahap 1" data-tahap="1" data-id_hibah="' . $this->id_hibah . '"><i class="bx bx-check"></i></button>';
    //         } elseif ($statusVerifyAdmin && $isFileKontrak && $statusVerifyTahapSatu && $statusVerifyLaporan && !$statusVerifyTahapDua && session('current_role') == 'admin') {
    //             $action .= '<button class="btn btn-success verifikasi-pencairan" data-title-tooltip="Verifikasi Pencairan Tahap 2" data-tahap="2" data-id_hibah="' . $this->id_hibah . '"><i class="bx bx-check"></i></button>';
    //         } elseif (!$statusSelesai && $statusVerifyTahapDua && session('current_role') == 'admin') {
    //             $action .= '<button class="btn btn-success btn-verify" data-title-tooltip="Selesaikan Ajuan" data-tipe="selesai" data-status="1" data-id_hibah="' . $this->id_hibah . '"><i class="bx bx-check"></i></button>';
    //         }

    //         // Jika belum expired & belum selesai
    //         if (!$statusSelesai && !$isExpiredProposal) {
    //             // Tombol Edit & Delete
    //             if ($today < $this->dl_proposal) {
    //                 $action .= '<button onclick="window.location.href=\'' . $editUrl . '\'" class="btn btn-warning" data-title-tooltip="Edit Ajuan Hibah"><i class="bx bx-edit"></i></button>';
    //                 $action .= '<button class="btn btn-danger btn-hapus" data-title-tooltip="Hapus Data" data-id_hibah="' . $this->id_hibah . '"><i class="bx bx-trash"></i></button>';
    //             }

    //             // Verifikasi Kaprodi
    //             if ($isPenanggungJawab == 'kaprodi' && !$statusVerifyKaprodi && auth()->user()->jabatan == 'Kaprodi' && session('current_role') == 'verifikator') {
    //                 $action .= '<button class="btn btn-success btn-verify" data-title-tooltip="Verifikasi Kaprodi" data-tipe="kaprodi" data-status="1" data-id_hibah="' . $this->id_hibah . '"><i class="bx bx-check"></i></button>';
    //             }
    //             // Verifikasi Dekan
    //             elseif ($isPenanggungJawab == 'dekan' && !$statusVerifyDekan && auth()->user()->jabatan == 'Dekan' && session('current_role') == 'verifikator') {
    //                 $action .= '<button class="btn btn-success btn-verify" data-title-tooltip="Verifikasi Dekan" data-tipe="dekan" data-status="1" data-id_hibah="' . $this->id_hibah . '"><i class="bx bx-check"></i></button>';
    //             }
    //             // Verifikasi Admin
    //             elseif (!$statusVerifyAdmin && session('current_role') == 'admin' && (($statusVerifyKaprodi && $isPenanggungJawab == 'kaprodi') || ($statusVerifyDekan && $isPenanggungJawab == 'dekan'))) {
    //                 $action .= '<button class="btn btn-success btn-verify" data-title-tooltip="Verifikasi Admin" data-tipe="admin" data-status="1" data-id_hibah="' . $this->id_hibah . '"><i class="bx bx-check"></i></button>';
    //             }
    //             // Verifikasi Laporan oleh Admin
    //             elseif ($isUploadLaporan && !$statusVerifyLaporan && session('current_role') == 'admin') {
    //                 $action .= '<button class="btn btn-success btn-verify" data-title-tooltip="Verifikasi Laporan" data-tipe="laporan" data-status="1" data-id_hibah="' . $this->id_hibah . '"><i class="bx bx-check"></i></button>';
    //             }

    //             // Tombol Isi Laporan
    //             // if (($statusVerifyTahapSatu && !$isUploadLaporan) || $revisiLaporan || $isOverdueLaporan) {
    //             if (($statusVerifyTahapSatu && !$statusVerifyTahapDua) || $revisiLaporan || $isOverdueLaporan) {
    //                 if (auth()->user()->username == $this->add_by) {
    //                     $action .= '<button onclick="window.location.href=\'' . $isiLaporanUrl . '\'" class="btn btn-primary" data-title-tooltip="Edit Laporan"><i class="bx bxs-report"></i></button>';
    //                 }
    //             }
    //         }
    //     }


    //     $action .= '</div>';
    //     return $action;
    // }

    public function getActionButtonsLabelAttribute()
    {
        $today = Carbon::today();
        $role = session('current_role');
        $isOwner = auth()->user()->username === $this->add_by;

        // === Status Boolean ===
        $verif = [
            'kaprodi' => $this->status_verify_kaprodi == '1',
            'dekan' => $this->status_verify_dekan == '1',
            'admin' => $this->status_verify_admin == '1',
            'tahap_satu' => $this->status_verify_tahap_satu == '1',
            'laporan' => $this->status_verify_laporan == '1',
            'tahap_dua' => $this->status_verify_tahap_dua == '1',
            'selesai' => $this->status_selesai == '1',
        ];

        $tanggal = [
            'kaprodi' => $this->date_verify_kaprodi != null,
            'dekan' => $this->date_verify_dekan != null,
            'admin' => $this->date_verify_admin != null,
            'laporan' => $this->date_verify_laporan != null,
            'tahap_satu' => $this->date_verify_tahap_satu != null,
            'tahap_dua' => $this->date_verify_tahap_dua != null,
            'selesai' => $this->date_verify_selesai != null,
        ];

        $revisi = [
            'kaprodi' => $this->status_revisi_kaprodi === '0',
            'dekan' => $this->status_revisi_dekan == '0',
            'admin' => $this->status_revisi_admin === '0',
            'laporan' => $this->status_revisi_laporan === '0',
        ];

        $has = [
            'file_kontrak' => !empty($this->file_kontrak),
            'laporan' => !empty($this->id_laporan_hibah),
        ];

        // === Deadline ===

        // $tglDLProposal = Carbon::parse($this->dl_proposal);
        // // $expProposal = $today > $this->dl_proposal && !$has['file_kontrak'];
        // $expProposal = $today->gt($tglDLProposal) && (!$verif['kaprodi'] && !$verif['dekan']);

        $tglDLProposal = Carbon::parse($this->dl_proposal);
        $expProposal = $today->gt($tglDLProposal) && (!$verif['kaprodi'] && !$verif['dekan']);


        $expLaporan = $today > $this->dl_laporan && !$has['laporan'] && $verif['tahap_satu'];

        $action = '<div class="btn-group" role="group">';
        $editUrl = route('hibah.edit', ['id_hibah' => $this->id_hibah]);
        $isiLaporanUrl = route('hibah.isiLaporan', ['id_hibah' => $this->id_hibah]);
        $daysDiff = $today->diffInDays($tglDLProposal, false);
        $action .= '<button type="button"
                        class="btn btn-light btn-dropdown-print"
                        data-id_hibah="' . $this->id_hibah . '"
                        data-dropdown-content="print-' . $this->id_hibah . '"
                        data-title-tooltip="Print Ajuan Hibah">
                        <i class="bx bx-printer"></i>
                    </button>

                    <div class="d-none" id="print-' . $this->id_hibah . '">
                        <ul class="custom-dropdown-menu">
                            <li>
                                <a href="#" class="dropdown-item btn-export-proposal" data-id_hibah="' . $this->id_hibah . '">
                                    <i class="bx bx-file"></i> Cetak Proposal
                                </a>
                            </li>';

        if ($has['laporan']) {
            $action .= '<li>
                        <a href="#" class="dropdown-item btn-export-laporan" data-id_hibah="' . $this->id_hibah . '">
                            <i class="bx bx-book"></i> Cetak Laporan
                        </a>
                    </li>';
        }

        $action .= '</ul></div>';


        // === Detail Button ===
        $action .= '<button class="btn btn-secondary btn-detail" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Detail Ajuan Hibah"><i class="bx bx-detail"></i></button>';

        // === Draft ===
        if (!$this->is_submit) {
            $action .= '<button onclick="window.location.href=\'' . $editUrl . '\'" class="btn btn-warning" data-title-tooltip="Edit Draft"><i class="bx bx-edit"></i></button>';
            $action .= '<button class="btn btn-danger btn-hapus" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Hapus Draft"><i class="bx bx-trash"></i></button>';
            $action .= '</div>';
            return $action;
        }

        // return ($verif['kaprodi'] || $verif['dekan']);
        // if ($expProposal && (in_array('0', [$revisi['kaprodi'], $revisi['dekan'], $revisi['dekan']]))) {
        //     $action .= '<button onclick="window.location.href=\'' . $editUrl . '\'" class="btn btn-warning" data-title-tooltip="Edit Ajuan Hibah"><i class="bx bx-edit"></i></button>';
        //     $action .= '<button class="btn btn-danger btn-hapus" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Hapus Data"><i class="bx bx-trash"></i></button>';
        // }

        // === Isi/Edit Laporan ===
        if (($verif['tahap_satu'] && !$verif['laporan'] && !$verif['tahap_dua']) || $revisi['laporan'] || $expLaporan) {
            if ($isOwner) {
                $action .= '<button onclick="window.location.href=\'' . $isiLaporanUrl . '\'" class="btn btn-primary" data-title-tooltip="Isi / Edit Laporan"><i class="bx bxs-report"></i></button>';
            }
        }

        // === Tombol Edit ===
        // if (!$verif['selesai'] && (!$expProposal && $today < $this->dl_proposal) || $daysDiff == -1) {
        // if (!$has['file_kontrak'] && (!$verif['selesai'] && $daysDiff == -1 || ($daysDiff < -1 && ($verif['kaprodi'] || $verif['dekan'])))) {
        if (!$has['file_kontrak'] && (!$verif['selesai'] && $daysDiff == -1 || ($daysDiff < -1 && ($verif['kaprodi'] || $verif['dekan'])))) {
            if (auth()->user()->username == $this->add_by) {
                $action .= '<button onclick="window.location.href=\'' . $editUrl . '\'" class="btn btn-warning" data-title-tooltip="Edit Ajuan Hibah"><i class="bx bx-edit"></i></button>';
                $action .= '<button class="btn btn-danger btn-hapus" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Hapus Data"><i class="bx bx-trash"></i></button>';
            } else {
                $action .= '<button onclick="window.location.href=\'' . $editUrl . '\'" class="btn btn-warning" data-title-tooltip="Lihat Ajuan Hibah"><i class="bx bx-edit"></i></button>';
            }
        }

        if (
            ($revisi['kaprodi'] ||  $revisi['dekan'] || $revisi['admin']) && (!$verif['laporan'] && !$verif['selesai']) &&
            auth()->user()->username == $this->add_by
        ) {
            $action .= '<button onclick="window.location.href=\'' . $editUrl . '\'" class="btn btn-warning" data-title-tooltip="Revisi Ajuan Hibah"><i class="bx bx-edit"></i></button>';
        }

        // === Admin Action ===
        if ($role === 'admin') {
            if ($verif['admin'] && $has['file_kontrak'] && !$verif['tahap_satu']) {
                $action .= '<button class="btn btn-success verifikasi-pencairan" data-tahap="1" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Verifikasi Pencairan Tahap 1"><i class="bx bx-check"></i></button>';
            } elseif ($verif['tahap_satu'] && $verif['laporan'] && !$verif['tahap_dua']) {
                $action .= '<button class="btn btn-success verifikasi-pencairan" data-tahap="2" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Verifikasi Pencairan Tahap 2"><i class="bx bx-check"></i></button>';
            } elseif ($verif['tahap_dua'] && !$verif['selesai']) {
                $action .= '<button class="btn btn-success btn-verify" data-tipe="selesai" data-status="1" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Selesaikan Ajuan"><i class="bx bx-check"></i></button>';
            }
            // === Verifikasi Laporan ===
            else if ($has['laporan'] && !$verif['laporan'] && $role === 'admin' && ($verif['kaprodi'] || $verif['dekan']) && $verif['admin'] && $has['file_kontrak']) {
                $action .= '<button class="btn btn-success btn-verify" data-tipe="laporan" data-status="1" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Verifikasi Laporan"><i class="bx bx-check"></i></button>';
            }
        }

        if ($expProposal) {
            if ($daysDiff == -1) {
                // === Verifikator Action ===
                if (!$verif['kaprodi'] && $this->penanggung_jawab_kegiatan == 'kaprodi' && auth()->user()->jabatan == 'Kaprodi' && $role == 'verifikator') {
                    $action .= '<button class="btn btn-success btn-verify" data-tipe="kaprodi" data-status="1" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Verifikasi Kaprodi"><i class="bx bx-check"></i></button>';
                } elseif (!$verif['dekan'] && $this->penanggung_jawab_kegiatan == 'dekan' && auth()->user()->jabatan == 'Dekan' && $role == 'verifikator') {
                    $action .= '<button class="btn btn-success btn-verify" data-tipe="dekan" data-status="1" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Verifikasi Dekan"><i class="bx bx-check"></i></button>';
                } elseif (!$verif['admin'] && $role === 'admin' && ($verif['kaprodi'] || $verif['dekan'])) {
                    $action .= '<button class="btn btn-success btn-verify" data-tipe="admin" data-status="1" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Verifikasi Admin"><i class="bx bx-check"></i></button>';
                }
            }
        } else {
            if (!$verif['kaprodi'] && $this->penanggung_jawab_kegiatan == 'kaprodi' && auth()->user()->jabatan == 'Kaprodi' && $role == 'verifikator') {
                $action .= '<button class="btn btn-success btn-verify" data-tipe="kaprodi" data-status="1" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Verifikasi Kaprodi"><i class="bx bx-check"></i></button>';
            } elseif (!$verif['dekan'] && $this->penanggung_jawab_kegiatan == 'dekan' && auth()->user()->jabatan == 'Dekan' && $role == 'verifikator') {
                $action .= '<button class="btn btn-success btn-verify" data-tipe="dekan" data-status="1" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Verifikasi Dekan"><i class="bx bx-check"></i></button>';
            } elseif (!$verif['admin'] && $role === 'admin' && ($verif['kaprodi'] || $verif['dekan'])) {
                $action .= '<button class="btn btn-success btn-verify" data-tipe="admin" data-status="1" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Verifikasi Admin"><i class="bx bx-check"></i></button>';
            }
        }

        // === Tambahkan tombol batalkan verifikasi jika status verifikasi = 1 ===
        if ($role === 'verifikator') {
            if (!$has['file_kontrak'] && $verif['kaprodi'] && auth()->user()->jabatan == 'Kaprodi' && $this->penanggung_jawab_kegiatan == 'kaprodi') {
                $action .= '<button class="btn btn-danger btn-verify" data-tipe="kaprodi" data-status="0" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Batalkan Verifikasi Kaprodi"><i class="bx bx-x"></i></button>';
            } elseif (!$has['file_kontrak'] && $verif['dekan'] && auth()->user()->jabatan == 'Dekan' && $this->penanggung_jawab_kegiatan == 'dekan') {
                $action .= '<button class="btn btn-danger btn-verify" data-tipe="dekan" data-status="0" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Batalkan Verifikasi Dekan"><i class="bx bx-x"></i></button>';
            }
        }

        if ($role === 'admin' && !$verif['selesai']) {
            if ($verif['admin'] && !$verif['tahap_satu']) {
                $action .= '<button class="btn btn-danger btn-verify" data-tipe="admin" data-status="0" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Batalkan Verifikasi Admin"><i class="bx bx-x"></i></button>';
            } else if ($verif['laporan'] && !$verif['tahap_dua']) {
                $action .= '<button class="btn btn-danger btn-verify" data-tipe="laporan" data-status="0" data-id_hibah="' . $this->id_hibah . '" data-title-tooltip="Batalkan Verifikasi Laporan"><i class="bx bx-x"></i></button>';
            }
        }

        $action .= '</div>';
        return $action;
    }

    public function scopeOrderByRole($query)
    {
        $role = session('current_role');

        switch ($role) {
            case 'user':
                return $query->orderByRaw("
                CASE
                    WHEN (
                        jenis_hibah.dl_proposal < CURDATE() AND
                        tbl_ajuan_hibah.file_kontrak IS NULL
                    ) OR
                    (
                        tbl_ajuan_hibah.file_kontrak IS NOT NULL
                        AND jenis_hibah.dl_laporan < CURDATE()
                        AND tbl_laporan_hibah.id_laporan_hibah IS NULL
                        AND tbl_ajuan_hibah.status_verify_laporan IS NULL
                    ) THEN 96

                    WHEN tbl_ajuan_hibah.status_selesai = 1 THEN 97
                    WHEN (
                        tbl_ajuan_hibah.is_submit = 0
                    ) THEN 2
                    
                    WHEN (
                        DATEDIFF(CURDATE(), jenis_hibah.dl_proposal) = 1 AND 
                        tbl_ajuan_hibah.status_verify_kaprodi IS NULL AND 
                        tbl_ajuan_hibah.status_verify_dekan IS NULL
                    ) THEN 1

                    WHEN (
                        tbl_ajuan_hibah.status_revisi_kaprodi = 0 OR 
                        tbl_ajuan_hibah.status_revisi_dekan = 0 OR 
                        tbl_ajuan_hibah.status_revisi_admin = 0
                    ) THEN 3

                    WHEN (
                        tbl_ajuan_hibah.file_kontrak IS NOT NULL AND
                        jenis_hibah.dl_laporan < CURDATE() AND
                        tbl_laporan_hibah.id_laporan_hibah IS NULL
                    ) THEN 1

                    WHEN (
                        tbl_ajuan_hibah.file_kontrak IS NOT NULL AND
                        tbl_laporan_hibah.id_laporan_hibah IS NULL
                    ) THEN 5

                    WHEN tbl_ajuan_hibah.status_selesai != 1 THEN 6


                    WHEN (
                        jenis_hibah.dl_laporan < CURDATE() AND
                        tbl_ajuan_hibah.file_kontrak IS NOT NULL AND
                        tbl_laporan_hibah.id_laporan_hibah IS NULL AND
                        tbl_ajuan_hibah.date_verify_laporan IS NULL
                    ) THEN 7

                    ELSE 50
                END ASC
            ");

            case 'verifikator':
                if (auth()->user()->jabatan == 'Kaprodi') {
                    $verif = 'WHEN (
                        tbl_ajuan_hibah.status_verify_kaprodi IS NULL 
                    ) THEN 2
                     
                     WHEN (
                        tbl_ajuan_hibah.status_revisi_kaprodi = 0
                    ) THEN 3
                    ';
                } else if (auth()->user()->jabatan == 'Dekan') {
                    $verif = 'WHEN (
                        tbl_ajuan_hibah.status_verify_dekan IS NULL 
                    ) THEN 2
                     WHEN (
                        tbl_ajuan_hibah.status_revisi_dekan = 0
                    ) THEN 3';
                } else {
                    $verif = '';
                }

                return $query->orderByRaw("
                    CASE
                        WHEN (
                        jenis_hibah.dl_proposal < CURDATE() AND
                        tbl_ajuan_hibah.file_kontrak IS NULL
                        ) OR
                        (
                            tbl_ajuan_hibah.file_kontrak IS NOT NULL
                            AND jenis_hibah.dl_laporan < CURDATE()
                            AND tbl_laporan_hibah.id_laporan_hibah IS NULL
                            AND tbl_ajuan_hibah.status_verify_laporan IS NULL
                        ) THEN 96

                        WHEN tbl_ajuan_hibah.status_selesai = 1 THEN 97

                        WHEN (
                            DATEDIFF(CURDATE(), jenis_hibah.dl_proposal) = 1 AND 
                            tbl_ajuan_hibah.status_verify_kaprodi IS NULL AND 
                            tbl_ajuan_hibah.status_verify_dekan IS NULL
                        ) THEN 1

                        {$verif}

                        WHEN (
                            tbl_ajuan_hibah.status_verify_kaprodi = 1 AND 
                            tbl_ajuan_hibah.status_verify_dekan = 1 AND
                            tbl_ajuan_hibah.file_kontrak IS NULL
                        ) THEN 4

                        WHEN (
                            jenis_hibah.dl_laporan < CURDATE() AND
                            tbl_ajuan_hibah.file_kontrak IS NOT NULL AND
                            tbl_laporan_hibah.id_laporan_hibah IS NULL AND
                            tbl_ajuan_hibah.date_verify_laporan IS NULL
                        ) THEN 4

                        WHEN tbl_ajuan_hibah.status_selesai != 1 THEN 5

                        ELSE 50
                    END ASC
                ");

            case 'admin':
                return $query->orderByRaw("
                CASE
                    WHEN (
                        jenis_hibah.dl_proposal < CURDATE() AND
                        tbl_ajuan_hibah.file_kontrak IS NULL
                    ) OR
                    (
                        tbl_ajuan_hibah.file_kontrak IS NOT NULL
                        AND jenis_hibah.dl_laporan < CURDATE()
                        AND tbl_laporan_hibah.id_laporan_hibah IS NULL
                        AND tbl_ajuan_hibah.status_verify_laporan IS NULL
                    ) THEN 96

                    WHEN tbl_ajuan_hibah.status_selesai = 1 THEN 97

                    WHEN (tbl_ajuan_hibah.status_verify_kaprodi = 1 OR tbl_ajuan_hibah.status_verify_dekan = 1) AND (tbl_ajuan_hibah.status_verify_admin = 0 OR tbl_ajuan_hibah.status_verify_admin IS NULL) THEN
                        1
                    WHEN (tbl_ajuan_hibah.status_verify_kaprodi = 1 OR tbl_ajuan_hibah.status_verify_dekan = 1) AND tbl_ajuan_hibah.status_verify_admin = 1 AND tbl_ajuan_hibah.file_kontrak IS NULL THEN
                        1
                    WHEN tbl_ajuan_hibah.status_verify_admin IS NULL THEN 1

                    WHEN tbl_ajuan_hibah.status_revisi_admin = 0 THEN 2

                    WHEN tbl_ajuan_hibah.status_selesai != 1 THEN 3

                    WHEN (
                        jenis_hibah.dl_laporan < CURDATE() AND
                        tbl_ajuan_hibah.file_kontrak IS NOT NULL AND
                        tbl_laporan_hibah.id_laporan_hibah IS NULL AND
                        tbl_ajuan_hibah.date_verify_laporan IS NULL
                    ) THEN 3

                    ELSE 50
                END ASC
            ");

            default:
                return $query;
        }
    }
}
