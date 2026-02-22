<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProspectPartner extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_prospect_partner';
    protected $guarded = [];
    protected $appends = ['action_label', 'status_label'];


    public function getStatusLabelAttribute()
    {
        $RevisiAdmin = $this->revisi != null;
        $statusVerify = $this->status == 'verifikasi';
        $role = session('current_role');

        $status = '';
        if (!$statusVerify && !$RevisiAdmin) {
            $status .= '<span class="badge bg-danger" data-title-tooltip="Menunggu Verifikasi Admin" style="font-size:10px!important;">Menunggu Verifikasi Admin</span>';
        } else if (!$statusVerify && $RevisiAdmin) {
            $status .= '<span class="badge bg-danger" data-title-tooltip="' . $this->revisi . '" style="font-size:10px!important;">Revisi dari Admin</span>';
        } else if ($statusVerify) {
            $status .= '<span class="badge bg-success" data-title-tooltip="Terverifikasi" style="font-size:10px!important;">Terverifikasi</span>';
        }

        return $status;
    }

    public function getActionLabelAttribute()
    {
        $action = '';

        $action .= '<div class="btn-group" role="group">';

        $nomorHp = 'data-phonenumber="' . $this->phonenumber . '"';
        // Link Kuesioner - Changed to primary color
        $action .= '<button data-title-tooltip="View Partner" class="btn btn-sm btn-light btn-view-partner" data-id="' . $this->id . '"
                data-name="' . $this->name . '"
                data-email="' . $this->email . '"
                data-occupation="' . $this->occupation . '"
                ' . (session('current_role') == 'admin' ? 'data-phonenumber="' . $this->phonenumber . '" ' : '') . '
                data-socmed="' . $this->socmed . '"
                data-researchint="' . $this->researchint . '"
                data-institution="' . $this->institution . '"
                data-website="' . $this->website . '"
                data-country="' . $this->country . '"
                data-country_name="' . $this->country_name . '"
                data-address="' . $this->address . '"
                data-point="' . $this->point . '"
                data-userid="' . $this->userid . '"
                data-name_user="' . $this->name_user . '"
                data-url-cardname1="' . getDocumentUrl($this->cardname1, 'partner') . '"
                data-cardname1="' . $this->cardname1 . '"
                data-url-cardname2="' . getDocumentUrl($this->cardname2, 'partner') . '"
                data-cardname2="' . $this->cardname2 . '"
                 >
                <i class="bx bx-group"></i> Lihat Mitra
                </button>';

        $editUrl = route('potential_partner.edit', ['id' => $this->id]);

        if (($this->status != 'verifikasi' && auth()->user()->username == $this->userid) || session('current_role') == 'admin' && $this->status != 'verifikasi') {
            $action .= '<button onclick="window.location.href=\'' . $editUrl . '\'" class="btn btn-sm btn-warning" data-title-tooltip="Edit Partner">
                        <i class="bx bx-edit"></i> Edit
                    </button>';

            $action .= '<button class="btn btn-sm btn-danger btn-delete" data-title-tooltip="Delete" data-id="' . $this->id . '">
                            <i class="bx bx-trash"></i> Hapus
                            </button>';
        }


        if (session('current_role') == 'admin') {
            if ($this->status == "revisi" || $this->status == null) {
                $action .= '<button class="btn btn-sm btn-success btn-verify" data-title-tooltip="Verify" data-status="1" data-id="' . $this->id . '">
                        <i class="bx bx-check"></i> Verifikasi
                        </button>';
            } else {
                $action .= '<button class="btn btn-sm btn-danger btn-verify" data-title-tooltip="Batalkan Verify" data-status="0" data-id="' . $this->id . '">
                        <i class="bx bx-x"></i> Batalkan Verifikasi
                        </button>';
            }
        }


        $action .= '</div>';


        return $action;
    }
}
