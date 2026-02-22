<?php

namespace App\Http\Controllers;

use App\Models\PengajuanKerjaSama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

set_time_limit(300);

class UploadController extends Controller
{
    public function uploadSummernote(Request $request)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filePath = $this->upload_file($file, 'uploads/summernote');
            $url = asset('storage/' . $filePath);
            return $url;
        }

        return response()->json(['error' => 'Tidak ada file'], 400);
    }

    public function upload_file($file, $path)
    {
        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->makeDirectory($path);
        }
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs($path, $fileName, 'public');
        return $filePath;
    }
}
