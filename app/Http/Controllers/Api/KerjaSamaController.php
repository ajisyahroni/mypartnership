<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KerjaSamaResource;
use App\Models\PengajuanKerjaSama;
use Illuminate\Http\Request;

class KerjaSamaController extends Controller
{
    public function index()
    {
        $data = KerjaSamaResource::collection(PengajuanKerjaSama::with(['getLembaga', 'getPengusul', 'getVerifikator', 'getKabid', 'getPenandatangan', 'getUnreadChatCount'])
            ->where('tgl_selesai', '0000-00-00 00:00:00')->latest()
            ->get());

        return response()->json([
            'status' => true,
            'message' => 'Daftar Kerjasama ditemukan.',
            'jumlah' => count($data),
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $data = KerjaSamaResource::collection(PengajuanKerjaSama::with(['getLembaga', 'getPengusul', 'getVerifikator', 'getKabid', 'getPenandatangan', 'getUnreadChatCount'])
            ->where('tgl_selesai', '0000-00-00 00:00:00')
            ->find($id));

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Detail Kerjasama ditemukan.',
            'jumlah' => count($data),
            'data' => $data
        ]);
    }
}
