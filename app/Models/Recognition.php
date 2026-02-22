<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Recognition extends Model
{
    protected $table = 'tbl_recognition';
    protected $primaryKey = 'id_rec'; // Gunakan primary key yang benar
    public $incrementing = false; // Jika primary key bukan auto-increment
    protected $keyType = 'string';
    protected $guarded = [];
    protected $appends = ['status_label', 'acceptance_form_label', 'cv_prof_label', 'file_sk_label', 'bukti_pelaksanaan_label', 'action_label', 'timestamp_selesai_label', 'timestamp_ajuan_label', 'tanggal_sk_label'];

    public function getUnreadChatCount()
    {
        return $this->hasMany(ChatRekognisi::class, 'id_rec', 'id_rec')->where('is_seen', '0')->where('receiver_id', auth()->id());
    }

    public function getStatusLabelAttribute()
    {
        $RevisiKaprodi = $this->revisi_kaprodi != null;
        $RevisiAdmin = $this->revisi_admin != null;
        $VerifyKaprodi = $this->status_verify_kaprodi == 0;
        $VerifyAdmin = $this->status_verify_kaprodi == 1 && $this->status_verify_admin == 0;
        $role = session('current_role');

        $status = '';
        $thisLembaga  = auth()->user()->status_tempat;

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

        // dd($jabatanVerifikator);

        if ($VerifyKaprodi && !$RevisiKaprodi) {
            $status .= '<span class="badge bg-danger" data-title-tooltip="Menunggu Verifikasi ' . $jabatanVerifikator . '" style="font-size:10px!important;">Menunggu Verifikasi ' . $jabatanVerifikator . '</span>';
        } else if ($VerifyKaprodi && $RevisiKaprodi) {
            $status .= "<span class='badge bg-danger' 
                data-title-tooltip='Klik untuk Melihat Revisi'
                onclick=\"showRevisi('{$this->id_rec}', 'revisi_kaprodi')\"
                style='font-size:10px!important;'>
                Revisi dari {$jabatanVerifikator}
            </span>";
        } else if ($VerifyAdmin && !$RevisiAdmin) {
            $status .= '<span class="badge bg-danger" data-title-tooltip="Menunggu Verifikasi Admin" style="font-size:10px!important;">Menunggu Verifikasi Admin</span>';
        } else if ($VerifyAdmin && $RevisiAdmin) {
            $status .= "<span class='badge bg-danger' 
                data-title-tooltip='Klik untuk Melihat Revisi'
                onclick=\"showRevisi('{$this->id_rec}', 'revisi_admin')\"
                style='font-size:10px!important;'>
                Revisi dari Admin
            </span>";
        } else {
            $status .= '<span class="badge bg-success" data-title-tooltip="Terverifikasi" style="font-size:10px!important;">Terverifikasi</span>';
        }

        return $status;
    }

    public function getAcceptanceFormLabelAttribute()
    {
        $VerifyKaprodi = $this->status_verify_kaprodi == 0;
        $VerifyAdmin = $this->status_verify_kaprodi == 1 && $this->status_verify_admin == 0;
        $role = session('current_role');

        $action = '';
        switch ($role) {
            case 'user':
            case 'verifikator':
                if ($VerifyKaprodi) {
                    $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545;">
                        Menunggu Verifikasi Kaprodi
                    </span>';
                } else if ($VerifyAdmin) {
                    $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545;">
                        Menunggu Verifikasi Admin
                    </span>';
                } else {
                    if ($this->acceptance_form != null && $this->acceptance_form != '') {
                        $action .= '<div class="btn-group" role="group">';
                        $filePath = getDocumentUrl(@$this->acceptance_form, 'file_rekognisi');
                        if (str_starts_with($filePath, 'http')) {
                            $iframeSrc = 'https://docs.google.com/gview?url=' . $filePath . '&embedded=true';
                        } else {
                            $iframeSrc = $filePath;
                        }
                        $action .= '<button class="btn btn-success btn-lihat-file" data-title-tooltip="Lihat File Acceptance Form" data-id_rec="' . $this->id_rec . '" data-url="' . $iframeSrc . '" data-rec="' . $this->rec . '">
                                           <i class="bx bx-file"></i> 
                        </button>';
                        $action .= '</div>';
                    } else {
                        if ($this->add_by == auth()->user()->username) {
                            $action .= '<form id="uploadForm-' . $this->id_rec . '-acceptance_form" enctype="multipart/form-data" style="display: inline;">
                                            <input type="file" name="file" style="display:none;" accept=".pdf"
                                                onchange="uploadFile(\'' . $this->id_rec . '\', \'acceptance_form\')" 
                                                id="fileInput-' . $this->id_rec . '-acceptance_form">
                                            
                                            <span onclick="$(\'#fileInput-' . $this->id_rec . '-acceptance_form\').click();" 
                                                style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545; cursor:pointer;">
                                                Unggah File Acceptance Form
                                            </span>
                                        </form>';
                        } else {
                            $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545;">
                                Belum Upload File Acceptance Form
                            </span>';
                        }
                    }
                }
                break;
            case 'admin':
                if ($this->acceptance_form != null && $this->acceptance_form != '') {
                    $action .= '<div class="btn-group" role="group">';
                    $filePath = getDocumentUrl(@$this->acceptance_form, 'file_rekognisi');
                    if (str_starts_with($filePath, 'http')) {
                        $iframeSrc = 'https://docs.google.com/gview?url=' . $filePath . '&embedded=true';
                    } else {
                        $iframeSrc = $filePath;
                    }
                    $action .= '<button class="btn btn-success btn-lihat-file" data-title-tooltip="Lihat File Acceptance Form" data-id_rec="' . $this->id_rec . '" data-url="' . $iframeSrc . '" data-rec="' . $this->rec . '">
                                           <i class="bx bx-file"></i> 
                        </button>';

                    $action .= '</div>';
                } else {
                    $action .= '<form id="uploadForm-' . $this->id_rec . '-acceptance_form" enctype="multipart/form-data" style="display: inline;">
                                <input type="file" name="file" style="display:none;" accept=".pdf"
                                    onchange="uploadFile(\'' . $this->id_rec . '\', \'acceptance_form\')" 
                                    id="fileInput-' . $this->id_rec . '-acceptance_form">
                                
                                <span onclick="$(\'#fileInput-' . $this->id_rec . '-acceptance_form\').click();" 
                                    style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545; cursor:pointer;">
                                    Unggah File Acceptance Form
                                </span>
                            </form>';
                }
                break;
            default:
                break;
        }

        return $action;
    }

    public function getCvProfLabelAttribute()
    {
        $action = '';
        $VerifyKaprodi = $this->status_verify_kaprodi == 0;
        $VerifyAdmin = $this->status_verify_kaprodi == 1 && $this->status_verify_admin == 0;
        $role = session('current_role');

        switch ($role) {
            case 'user':
            case 'verifikator':
                if ($VerifyKaprodi) {
                    $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545;">
                        Menunggu Verifikasi Kaprodi
                    </span>';
                } else if ($VerifyAdmin) {
                    $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545;">
                        Menunggu Verifikasi Admin
                    </span>';
                } else {
                    if ($this->cv_prof != null && $this->cv_prof != '') {
                        $action .= '<div class="btn-group" role="group">';
                        $filePath = getDocumentUrl(@$this->cv_prof, 'file_rekognisi');
                        if (str_starts_with($filePath, 'http')) {
                            $iframeSrc = 'https://docs.google.com/gview?url=' . $filePath . '&embedded=true';
                        } else {
                            $iframeSrc = $filePath;
                        }
                        $action .= '<button class="btn btn-primary btn-lihat-file" data-title-tooltip="Lihat File Acceptance Form" data-id_rec="' . $this->id_rec . '" data-url="' . $iframeSrc . '" data-rec="' . $this->rec . '">
                                           <i class="bx bx-file"></i> 
                        </button>';
                        $action .= '</div>';
                    } else {
                        if ($this->add_by == auth()->user()->username) {
                            $action .= '<form id="uploadForm-' . $this->id_rec . '-cv_prof" enctype="multipart/form-data" style="display: inline;">
                                        <input type="file" name="file" style="display:none;" accept=".pdf"
                                            onchange="uploadFile(\'' . $this->id_rec . '\', \'cv_prof\')" 
                                            id="fileInput-' . $this->id_rec . '-cv_prof">
                                        
                                        <span onclick="$(\'#fileInput-' . $this->id_rec . '-cv_prof\').click();" 
                                            style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545; cursor:pointer;">
                                            Unggah File CV Prof
                                        </span>
                                    </form>';
                        } else {
                            $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545;">
                                Belum Upload File CV Prof
                            </span>';
                        }
                    }
                }
                break;
            case 'admin':
                if ($this->cv_prof != null && $this->cv_prof != '') {
                    $action .= '<div class="btn-group" role="group">';

                    $filePath = getDocumentUrl(@$this->cv_prof, 'file_rekognisi');
                    if (str_starts_with($filePath, 'http')) {
                        $iframeSrc = 'https://docs.google.com/gview?url=' . $filePath . '&embedded=true';
                    } else {
                        $iframeSrc = $filePath;
                    }
                    $action .= '<button class="btn btn-primary btn-lihat-file" data-title-tooltip="Lihat File Acceptance Form" data-id_rec="' . $this->id_rec . '" data-url="' . $iframeSrc . '" data-rec="' . $this->rec . '">
                                           <i class="bx bx-file"></i> 
                        </button>';

                    $action .= '</div>';
                } else {
                    $action .= '<form id="uploadForm-' . $this->id_rec . '-cv_prof" enctype="multipart/form-data" style="display: inline;">
                                <input type="file" name="file" style="display:none;" accept=".pdf"
                                    onchange="uploadFile(\'' . $this->id_rec . '\', \'cv_prof\')" 
                                    id="fileInput-' . $this->id_rec . '-cv_prof">
                                
                                <span onclick="$(\'#fileInput-' . $this->id_rec . '-cv_prof\').click();" 
                                    style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545; cursor:pointer;">
                                    Unggah File CV Prof
                                </span>
                            </form>';
                }
                break;
            default:
                break;
        }

        return $action;
    }

    public function getFileSkLabelAttribute()
    {
        $action = '';
        $VerifyKaprodi = $this->status_verify_kaprodi == 0;
        $VerifyAdmin = $this->status_verify_kaprodi == 1 && $this->status_verify_admin == 0;
        $role = session('current_role');
        switch ($role) {
            case 'user':
            case 'verifikator':
                if ($VerifyKaprodi) {
                    $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545;">
                        Menunggu Verifikasi Kaprodi
                    </span>';
                } else if ($VerifyAdmin) {
                    $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545;">
                        Menunggu Verifikasi Admin
                    </span>';
                } else {
                    if ($this->file_sk != null && $this->file_sk != '') {
                        $action .= '<div class="btn-group" role="group">';
                        $filePath = getDocumentUrl(@$this->file_sk, 'file_rekognisi');
                        if (str_starts_with($filePath, 'http')) {
                            $iframeSrc = 'https://docs.google.com/gview?url=' . $filePath . '&embedded=true';
                        } else {
                            $iframeSrc = $filePath;
                        }
                        $action .= '<button class="btn btn-info btn-lihat-file" data-title-tooltip="Lihat File Acceptance Form" data-id_rec="' . $this->id_rec . '" data-url="' . $iframeSrc . '" data-rec="' . $this->rec . '">
                                           <i class="bx bx-file"></i> 
                        </button>';
                        $action .= '</div>';
                    } else {
                        $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545;">
                            Admin Belum Mengunggah File SK
                        </span>';
                    }
                }
                break;
            case 'admin':
                if ($VerifyKaprodi) {
                    $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545;">
                        Menunggu Verifikasi Kaprodi
                    </span>';
                } else if ($VerifyAdmin) {
                    $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545;">
                        Menunggu Verifikasi Admin
                    </span>';
                } else {
                    if ($this->file_sk != null && $this->file_sk != '') {
                        $action .= '<div class="btn-group" role="group">';

                        $filePath = getDocumentUrl(@$this->file_sk, 'file_rekognisi');
                        if (str_starts_with($filePath, 'http')) {
                            $iframeSrc = 'https://docs.google.com/gview?url=' . $filePath . '&embedded=true';
                        } else {
                            $iframeSrc = $filePath;
                        }
                        $action .= '<button class="btn btn-info btn-lihat-file" data-title-tooltip="Lihat File Acceptance Form" data-id_rec="' . $this->id_rec . '" data-url="' . $iframeSrc . '" data-rec="' . $this->rec . '">
                                           <i class="bx bx-file"></i> 
                        </button>';

                        $action .= '</div>';
                    } else {
                        $action .= '<form id="uploadForm-' . $this->id_rec . '-file_sk" enctype="multipart/form-data" style="display: inline;">
                                <input type="file" name="file" style="display:none;" accept=".pdf"
                                    onchange="uploadFile(\'' . $this->id_rec . '\', \'file_sk\')" 
                                    id="fileInput-' . $this->id_rec . '-file_sk">
                                
                                <span onclick="$(\'#fileInput-' . $this->id_rec . '-file_sk\').click();" 
                                    style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545; cursor:pointer;">
                                    Unggah File SK
                                </span>
                            </form>';
                    }
                }
                break;
            default:
                break;
        }


        return $action;
    }

    public function getBuktiPelaksanaanLabelAttribute()
    {
        $action = '';
        $VerifyKaprodi = $this->status_verify_kaprodi == 0;
        $VerifyAdmin = $this->status_verify_kaprodi == 1 && $this->status_verify_admin == 0;
        $role = session('current_role');

        switch ($role) {
            case 'user':
            case 'verifikator':
                if ($VerifyKaprodi) {
                    $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545;">
                        Menunggu Verifikasi Kaprodi
                    </span>';
                } else if ($VerifyAdmin) {
                    $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545;">
                        Menunggu Verifikasi Admin
                    </span>';
                } else {
                    if ($this->bukti_pelaksanaan != null && $this->bukti_pelaksanaan != '') {
                        $action .= '<div class="btn-group" role="group">';
                        $filePath = getDocumentUrl(@$this->bukti_pelaksanaan, 'file_rekognisi');
                        if (str_starts_with($filePath, 'http')) {
                            $iframeSrc = 'https://docs.google.com/gview?url=' . $filePath . '&embedded=true';
                        } else {
                            $iframeSrc = $filePath;
                        }

                        $action .= '<button class="btn btn-dark btn-lihat-file" data-title-tooltip="Lihat File Acceptance Form" data-id_rec="' . $this->id_rec . '" data-url="' . $iframeSrc . '" data-rec="' . $this->rec . '">
                                           <i class="bx bx-file"></i> 
                        </button>';
                        $action .= '</div>';
                    } else {
                        if ($this->add_by == auth()->user()->username) {
                            $action .= '<form id="uploadForm-' . $this->id_rec . '-bukti_pelaksanaan" enctype="multipart/form-data" style="display: inline;">
                                        <input type="file" name="file" style="display:none;" accept=".pdf"
                                            onchange="uploadFile(\'' . $this->id_rec . '\', \'bukti_pelaksanaan\')" 
                                            id="fileInput-' . $this->id_rec . '-bukti_pelaksanaan">
                                        
                                        <span onclick="$(\'#fileInput-' . $this->id_rec . '-bukti_pelaksanaan\').click();" 
                                            style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545; cursor:pointer;">
                                            Unggah File Bukti Pelaksanaan
                                        </span>
                                    </form>';
                        } else {
                            $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545;">
                                Pengusul Belum Upload File Bukti Pelaksanaan
                            </span>';
                        }
                    }
                }
                break;
            case 'admin':
                if ($VerifyKaprodi) {
                    $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545;">
                        Menunggu Verifikasi Kaprodi
                    </span>';
                } else if ($VerifyAdmin) {
                    $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545;">
                        Menunggu Verifikasi Admin
                    </span>';
                } else {
                    if ($this->bukti_pelaksanaan != null && $this->bukti_pelaksanaan != '') {
                        $action .= '<div class="btn-group" role="group">';

                        $filePath = getDocumentUrl(@$this->bukti_pelaksanaan, 'file_rekognisi');
                        $iframeSrc = 'https://docs.google.com/gview?url=' . $filePath . '&embedded=true';
                        $action .= '<button class="btn btn-dark btn-lihat-file" data-title-tooltip="Lihat File Acceptance Form" data-id_rec="' . $this->id_rec . '" data-url="' . $iframeSrc . '" data-rec="' . $this->rec . '">
                                           <i class="bx bx-file"></i> 
                        </button>';

                        $action .= '</div>';
                    } else {
                        $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545;">
                            Pengusul Belum Mengunggah File
                        </span>';
                        // $action .= '<form id="uploadForm-' . $this->id_rec . '-bukti_pelaksanaan" enctype="multipart/form-data" style="display: inline;">
                        //             <input type="file" name="file" style="display:none;" 
                        //                 onchange="uploadFile(\'' . $this->id_rec . '\', \'bukti_pelaksanaan\')" 
                        //                 id="fileInput-' . $this->id_rec . '-bukti_pelaksanaan">

                        //             <span onclick="$(\'#fileInput-' . $this->id_rec . '-bukti_pelaksanaan\').click();" 
                        //                 style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block; color:#dc3545; cursor:pointer;">
                        //                 Unggah File Bukti Pelaksanaan
                        //             </span>
                        //         </form>';
                    }
                }
                break;
            default:
                break;
        }

        return $action;
    }

    public function getActionLabelAttribute()
    {
        $unreadCount = $this->getUnreadChatCount->count();
        $VerifyKaprodi = $this->status_verify_kaprodi == 0;
        $VerifyAdmin = $this->status_verify_kaprodi == 1 && ($this->status_verify_admin == 0 || $this->status_verify_admin == null);
        $role = session('current_role');

        $action = '';

        $action .= '<div class="btn-group" role="group">';

        $action .= '<button class="btn btn-primary btn-detail-recognition" 
            data-title-tooltip="Lihat Detail"
            data-id_rec="' . $this->id_rec . '">
            <i class="bx bx-show"></i> 
        </button>';


        $notifBadge = $unreadCount > 0 ? '<span class="position-absolute top-0 start-80 translate-middle badge rounded-pill bg-danger" style="z-index:10;">' . $unreadCount . '
                <span class="visually-hidden">unread messages</span>
            </span>' : '';

        if (($this->add_by == auth()->user()->username) || ($role == 'verifikator' && $this->faculty == auth()->user()->place_state) || ($role == 'admin')) {
            // Edit Kuesioner - Changed to info color
            $action .= '<a href="' . route('recognition.edit', ['id_rec' => $this->id_rec]) . '" data-title-tooltip="Edit Rekognisi" class="btn btn-warning btn-edit_Kuesioner" data-rec="' . $this->rec . '">
                    <i class="bx bx-edit"></i>
                    </a>';
            // Tombol tambahan (Chat, Lihat Detail, Edit, Hapus)
            $action .= '<a href="' . route('chatRecognisi.home', ['id_rec' => $this->id_rec]) . '" data-title-tooltip="Chat Rekognisi" class="btn btn-secondary btn-chat position-relative" data-id_rec="' . $this->id_rec . '">
                                    <i class="bx bx-chat"></i>
                                    ' . $notifBadge . '
                                </a>';
        }

        switch ($role) {
            case 'verifikator':
                $thisLembaga  = auth()->user()->status_tempat;

                $idLembaga = RefLembagaUMS::where('nama_lmbg', $thisLembaga)
                    ->value('id_lmbg');

                if ($idLembaga == null) {
                    $idLembaga = RefLembagaUMS::where('nama_lmbg_old', $thisLembaga)
                        ->value('id_lmbg');
                }

                if ($this->faculty == auth()->user()->place_state || ($this->faculty == $idLembaga && auth()->user()->jabatan == 'Dekan')) {
                    if ($VerifyKaprodi) {
                        $action .= '<button class="btn btn-success btn-verify" data-title-tooltip="Verifikasi" data-tipe="kaprodi" data-status="1" data-id_rec="' . $this->id_rec . '">
                            <i class="bx bx-check"></i>
                            </button>';
                    } else {
                        $action .= '<button class="btn btn-danger btn-verify" data-title-tooltip="Batalkan Verifikasi" data-tipe="kaprodi" data-status="0" data-id_rec="' . $this->id_rec . '">
                            <i class="bx bx-x"></i>
                            </button>';
                    }
                }
                break;
            case 'admin':
                if ($VerifyKaprodi) {
                } else if ($VerifyAdmin) {
                    $action .= '<button class="btn btn-success btn-verify" data-title-tooltip="Verifikasi" data-tipe="admin" data-status="1" data-id_rec="' . $this->id_rec . '">
                        <i class="bx bx-check"></i>
                        </button>';
                } else if ($this->status_verify_admin == '1') {
                    $action .= '<button class="btn btn-danger btn-verify" data-title-tooltip="Batalkan Verifikasi" data-tipe="admin" data-status="0" data-id_rec="' . $this->id_rec . '">
                        <i class="bx bx-x"></i>
                        </button>';
                }
                break;
            default:
                break;
        }

        // if (session('current_role') == 'admin') {
        //     if ($this->status == "revisi" || $this->status == null) {
        //         $action .= '<button class="btn btn-success btn-verify" data-title-tooltip="Verify" data-status="1" data-id_rec="' . $this->id_rec . '">
        //                 <i class="bx bx-check"></i>
        //                 </button>';
        //     } else {
        //         $action .= '<button class="btn btn-danger btn-verify" data-title-tooltip="Verify" data-status="0" data-id_rec="' . $this->id_rec . '">
        //                 <i class="bx bx-x"></i>
        //                 </button>';
        //     }
        // }

        $action .= '</div>';

        return $action;
    }

    public function getTimestampSelesaiLabelAttribute()
    {
        $action = '';

        if ($this->timestamp_selesai == null) {
            $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #dc3545; background:#f8f9fa; display:inline-block;">
                        Belum Selesai
                    </span>';
        } else {
            $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #28a745; background:#f8f9fa; display:inline-block;">
                            ' . TanggalIndonesia($this->timestamp_selesai) . '
                        </span>';
        }

        return $action;
    }

    public function getTimestampAjuanLabelAttribute()
    {
        $action = '';

        $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #f4511e; background:#f8f9fa; display:inline-block;">
                    ' . TanggalIndonesia($this->timestamp_ajuan) . '
                </span>';


        return $action;
    }

    public function getTanggalSKLabelAttribute()
    {
        $action = '';

        $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #f4511e; background:#f8f9fa; display:inline-block;">
                    ' . Tanggal_Indo($this->mulai) . ' - ' . Tanggal_Indo($this->selesai) . '
                </span>';


        return $action;
    }
}
