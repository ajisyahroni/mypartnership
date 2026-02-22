<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Kuesioner extends Model
{
    use HasFactory;

    protected $table = 'qkuesioner';
    protected $primaryKey = 'id_kuesioner'; // Gunakan primary key yang benar
    public $incrementing = false; // Jika primary key bukan auto-increment
    protected $keyType = 'string';
    protected $guarded = [];
    protected $appends = ['que_for_label', 'action_label', 'status_label', 'tanggal_label'];

    public function getQueForLabelAttribute()
    {
        $action = '';
        $action .= '<span style="font-size:10px!important; padding:4px 8px; border-left:3px solid #28a745; background:#f8f9fa; display:inline-block;">
                            ' . $this->que_for . '
                        </span>';
        return $action;
    }
    public function getActionLabelAttribute()
    {
        $action = '';

        $action .= '<div class="btn-group" role="group">';

        // Link Kuesioner - Changed to primary color
        $action .= '<button data-title-tooltip="Link Kuesioner" class="btn btn-purple btn-link_kuesioner" data-id_kuesioner="' . $this->id_kuesioner . '">
                <i class="bx bx-link"></i>
                </button>';

        if ($this->is_kirim == '1') {
            $bgkirim = 'btn-success';
            $tooltip = 'Kirim Ulang Kuesioner';
        } else {
            $bgkirim = 'btn-danger';
            $tooltip = 'Kirim Kuesioner';
        }

        // Kirim Kuesioner - Changed to success color
        $action .= '<button data-title-tooltip="' . $tooltip . '" class="btn ' . $bgkirim . ' btn-kirim_kuesioner" data-id_kuesioner="' . $this->id_kuesioner . '">
                <i class="bx bx-send"></i>
                </button>';

        // Hasil Kuesioner - Changed to warning color
        $action .= '<button data-title-tooltip="Hasil Kuesioner" class="btn btn-dark-gray btn-hasil_kuesioner" data-id_kuesioner="' . $this->id_kuesioner . '">
                <i class="fas fa-clipboard-check"></i>
                </button>';

        // Edit Kuesioner - Changed to info color
        $action .= '<button data-title-tooltip="Edit Kuesioner" class="btn btn-warning btn-edit_kuesioner" data-id_kuesioner="' . $this->id_kuesioner . '">
                <i class="bx bx-edit"></i>
                </button>';

        // Lihat Implementasi - Changed to secondary color
        $action .= '<button data-title-tooltip="Lihat Implementasi" class="btn btn-secondary btn-lihat_implementasi" data-id_kuesioner="' . $this->id_kuesioner . '">
                <i class="bx bx-show"></i>
                </button></div>';

        return $action;
    }

    public function getTanggalLabelAttribute()
    {
        $action = '';

        $action .= '<span data-title-tooltip="' . TanggalIndonesia($this->que_create) . '" style="font-size:10px!important;" class="badge bg-primary">' . TanggalIndonesia($this->que_create) . '</span>';

        return $action;
    }

    public function getStatusLabelAttribute()
    {
        $action = '';
        if ($this->status == 'Open') {
            $bg = 'bg-success';
        } else {
            $bg = 'bg-danger';
        }

        $action .= '<span data-title-tooltip="' . $this->status . '" style="font-size:10px!important;" class="badge ' . $bg . '">' . $this->status . '</span>';

        return $action;
    }
}
