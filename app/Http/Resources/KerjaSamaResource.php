<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KerjaSamaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_mou' => $this->id_mou,
            'no_dokumen' => $this->no_dokumen,
            'nama_institusi' => $this->nama_institusi,
            'status_tempat' => $this->status_tempat,
            'mulai' => $this->tgl_mulai,
            'selesai' => $this->tgl_selesai,
        ];
    }
}
