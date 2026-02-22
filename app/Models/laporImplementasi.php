<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class laporImplementasi extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'kerma_evaluasi';
    protected $guarded = [];
    protected $appends = [
        'action_buttons',
        'bukti_pelaksanaan_label',
        'dokumen_kerma_label',
        'lapor_kerma_label',
        'status_pengajuan_kerja_sama_label'
    ];

    public function getBuktiPelaksanaanLabelAttribute()
    {
        $action = '';
        $belumUpload = ($this->file_imp == null || $this->file_imp == '')  && $this->postby == auth()->user()->username;
        $sudahUpload = $this->file_imp != null || $this->file_imp != '';
        $belumUploadSelainPostBy = $this->file_imp == null || $this->file_imp == '' && $this->postby == auth()->user()->username;

        if ($sudahUpload) {
            $action = '<a href="' . getDocumentUrl($this->file_imp, 'file_imp') . '" class="btn btn-primary" target="_blank" data-title-tooltip="Download Bukti Pelaksanaan"><i class="bx bxs-download"></i></a>';
        } else if ($belumUpload) {
            $action .= '<form id="uploadForm-' . $this->id_ev . '-file_imp" enctype="multipart/form-data" style="display: inline;">
                            <input type="file" name="file" style="display:none;" accept=".pdf"
                                onchange="uploadFile(\'' . $this->id_ev . '\', \'file_imp\')" 
                                id="fileInput-' . $this->id_ev . '-file_imp">
                            
                            <span onclick="$(\'#fileInput-' . $this->id_ev . '-file_imp\').click();" 
                                style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545; cursor:pointer;">
                                File Belum diunggah
                            </span>
                        </form>';
        } else if ($belumUploadSelainPostBy) {
            $action .= '<span style="font-size:10px!important;" class="badge bg-danger">Dokumen Belum Diunggah</span>';
        }
        return $action;
    }

    public function getDokumenKermaLabelAttribute()
    {
        $action = '';
        if ($this->file_mou != null && $this->file_mou != '') {
            $action = '<a href="' . getDocumentUrl($this->file_mou, 'file_mou') . '" class="btn btn-primary" target="_blank" data-title-tooltip="Download Dokumen Kerja Sama"><i class="bx bxs-download"></i></a>';
        } else {
            // $action .= '<form id="uploadForm-' . $this->id_ev . '-file_mou" enctype="multipart/form-data" style="display: inline;">
            //                 <input type="file" name="file" style="display:none;" 
            //                     onchange="uploadFile(\'' . $this->id_ev . '\', \'file_mou\')" 
            //                     id="fileInput-' . $this->id_ev . '-file_mou">

            //                 <span onclick="$(\'#fileInput-' . $this->id_ev . '-file_mou\').click();" 
            //                     style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545; cursor:pointer;">
            //                     File Belum diunggah
            //                 </span>
            //             </form>';
            $action .= '<span style="font-size:10px!important;" class="badge bg-danger">Dokumen Belum Diunggah</span>';
        }
        return $action;
    }

    public function getLaporKermaLabelAttribute()
    {
        $action = '';
        $belumUpload = ($this->file_ikuenam == null || $this->file_ikuenam == '')  && $this->postby == auth()->user()->username;
        $sudahUpload = $this->file_ikuenam != null || $this->file_ikuenam != '';
        $belumUploadSelainPostBy = $this->file_ikuenam == null || $this->file_ikuenam == '' && $this->postby == auth()->user()->username;
        if ($sudahUpload) {
            $action = '<a href="' . getDocumentUrl($this->file_ikuenam, 'file_ikuenam') . '" class="btn btn-primary" target="_blank" data-title-tooltip="Download IKU 6"><i class="bx bxs-download"></i></a>';
        } else if ($belumUpload) {
            $action .= '<form id="uploadForm-' . $this->id_ev . '-file_ikuenam" enctype="multipart/form-data" style="display: inline;">
            <input type="file" name="file" style="display:none;" accept=".pdf"
                                onchange="uploadFile(\'' . $this->id_ev . '\', \'file_ikuenam\')" 
                                id="fileInput-' . $this->id_ev . '-file_ikuenam">
                            
                            <span onclick="$(\'#fileInput-' . $this->id_ev . '-file_ikuenam\').click();" 
                                style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545; cursor:pointer;">
                                File Belum diunggah
                            </span>
                            </form>';
        } else if ($belumUploadSelainPostBy) {
            $action .= '<span style="font-size:10px!important;" class="badge bg-danger">Dokumen Belum Diunggah</span>';
        }
        return $action;
    }

    public function getActionButtonsAttribute()
    {

        $role = session('current_role');
        $action = '<div class="btn-group" role="group">';

        $isVerif = $this->status_verifikasi == '1';
        $isNotVerif = $this->status_verifikasi == '0';
        $belumVerif = $this->status_verifikasi == null || $this->status_verifikasi == '';

        // Role: Admin
        if ($role == 'admin') {
            if ($isNotVerif) {
                $action .=
                    '<button data-title-tooltip="Verifikasi oleh Admin" class="btn btn-success btn-verifikasi" data-id_ev="' .
                    $this->id_ev . '" data-status="1"><i class="fa-solid fa-circle-check"></i>
                        </button>';
            } else if ($isVerif) {
                $action .=
                    '<button data-title-tooltip="Batalkan Verifikasi oleh Admin" class="btn btn-danger btn-verifikasi" data-id_ev="' .
                    $this->id_ev . '" data-status="0"> <i class="fa-solid fa-circle-xmark"></i>
                            </button>';
            }
        }

        // Tombol tambahan (Lihat Detail, Edit, Hapus)
        $action .= '<button data-title-tooltip="Lihat Detail" class="btn btn-info btn-detail" data-id_ev="' . $this->id_ev . '">
                                <i class="bx bx-show"></i>
                            </button>';

        // if (($belumVerif || $isNotVerif) &&  $role == 'admin' || ($role != 'admin' && $this->postby == auth()->user()->username)) {
        if (($belumVerif || $isNotVerif) && ($this->postby == auth()->user()->username || $role == 'admin')) {
            $action .= '<a href="' . route('implementasi.edit', ['id' => $this->id_ev]) . '" data-title-tooltip="Edit Implementasi" class="btn btn-warning btn-edit" data-id_ev="' . $this->id_ev . '"><i class="bx bx-edit"></i></a>';

            $action .= '<button data-title-tooltip="Hapus Implementasi" class="btn btn-danger btn-hapus" data-id_ev="' . $this->id_ev . '">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>';
        }

        return $action;
    }
    public function dataPengajuan()
    {
        return $this->hasOne(PengajuanKerjaSama::class, 'id_mou', 'id_mou')->with(['dataBobot']);
    }

    public function dataBobot()
    {
        return $this->hasOne(RefJenisInstitusiMitra::class, 'id_mou', 'id_mou');
    }

    public function getPost()
    {
        return $this->hasOne(User::class, 'username', 'postby');
    }

    public function getLembaga()
    {
        return $this->hasOne(RefLembagaUMS::class, 'id_lmbg', 'id_lembaga');
    }

    public function getstatusPengajuanKerjaSamaLabelAttribute()
    {
        $today = now();
        $mulai = Carbon::parse($this->dataPengajuan->mulai);
        $selesai = Carbon::parse($this->dataPengajuan->selesai);

        $diff = $selesai->diff($today);
        $durasiTersisa = "{$diff->y} Tahun {$diff->m} Bulan {$diff->d} Hari sebelum expired";

        if ($mulai > $today) {
            $statusPengajuanKerjaSama = '<span style="font-size:10px!important;" class="badge bg-warning" data-title-tooltip="Pengajuan masih dalam proses. Mulai: ' . $mulai->format('d-m-Y') . '">Dalam Proses - ' . $selesai->format('Y');
        } elseif ($mulai <= $today && $selesai >= $today) {
            $statusPengajuanKerjaSama = '<span style="font-size:10px!important;" class="badge bg-success" data-title-tooltip="Kerja sama akan berakhir dalam ' . $durasiTersisa . '">Berjalan - ' . $selesai->format('Y');
        } elseif ($selesai->lt($today)) {
            $hariTerlambat = (int) $selesai->diffInDays($today);
            if (!empty($this->perpanjangan)) {
                $statusPengajuanKerjaSama = '<span style="font-size:10px!important;" class="badge bg-info" data-title-tooltip="Sedang dalam tahap perpanjangan. Berakhir: ' . $selesai->format('d-m-Y') . '">Dalam Perpanjangan - ' . $selesai->format('Y');
            }
            $statusPengajuanKerjaSama = '<span style="font-size:10px!important;" class="badge bg-danger" data-title-tooltip="Masa kerja sama telah berakhir pada ' . $selesai->format('d-m-Y') . '. Sudah terlewat ' . $hariTerlambat . ' hari">Expired - ' . $selesai->format('Y');
        }

        return $statusPengajuanKerjaSama;
    }


    public function scopeFilterDokumen(Builder $query, $request)
    {
        if ($request->filled('nama_institusi')) {
            $query->whereHas('dataPengajuan', function ($q) use ($request) {
                $q->where('nama_institusi', $request->nama_institusi);
            });
        }

        if ($request->filled('tahun')) {
            $query->whereHas('dataPengajuan', function ($q) use ($request) {
                $q->whereYear('selesai', $request->tahun);
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('pelaksana')) {
            $query->where('pelaksana_prodi_unit', $request->pelaksana_prodi_unit);
        }

        if ($request->filled('judul')) {
            $query->where('judul', $request->judul);
            $query->orwhere('judul_lain', $request->judul);
        }

        if ($request->filled('postby')) {
            $query->where('postby', $request->postby);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('timestamp', $request->tahun);
        }

        if ($request->filled('status')) {
            $statusVerif = $request->status == 'Terverifikasi' ? '1' : '0';
            $query->where('status_verifikasi', $statusVerif);
        }

        return $query;
    }
}
