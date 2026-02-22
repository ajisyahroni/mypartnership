<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


use Stichoza\GoogleTranslate\GoogleTranslate;

// if (!function_exists('translateEn')) {
//     function translateEn($text)
//     {
//         try {
//             $tr = new GoogleTranslate('translateEn');
//             return $tr->translate($text);
//         } catch (\Exception $e) {
//             return $text; // fallback ke teks asli kalau gagal
//         }
//     }
// }
function sanitize_input($html)
{
    // 1. Tag aman (ditambah table support)
    $allowed_tags = '
        <p><br><b><strong><i><em><u>
        <ul><ol><li>
        <table><thead><tbody><tr><td><th>
        <img><a>
    ';

    // 2. Hapus tag di luar whitelist
    $html = strip_tags($html, $allowed_tags);

    // 3. Hapus event handler: onclick=, onload=, onerror=, dll.
    $html = preg_replace('/\s*on\w+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/i', '', $html);

    // 4. Hapus inline style="..."
    $html = preg_replace('/\s*style\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/i', '', $html);

    // 5. Blok protocol berbahaya
    $html = preg_replace('/(javascript:|vbscript:|data:text\/html)/i', '', $html);

    // 6. Hapus tag berbahaya
    $dangerous_tags = [
        'script',
        'iframe',
        'object',
        'embed',
        'svg',
        'style',
        'meta',
        'link',
        'base',
        'form',
        'video',
        'audio',
        'canvas',
        'marquee'
    ];
    foreach ($dangerous_tags as $tag) {
        $html = preg_replace('/<' . $tag . '[^>]*>/i', '', $html);
        $html = preg_replace('/<\/' . $tag . '>/i', '', $html);
    }

    // 7. Hapus kamuflase tag berbahaya <scr<script>ipt>
    $html = preg_replace('/scr[^>]*ipt/i', '', $html);

    // 8. Sanitasi atribut menjadi benar-benar aman
    $html = preg_replace_callback(
        '/<([^>]+)>/',
        function ($matches) {
            return '<' . strict_allowed_attributes($matches[1]) . '>';
        },
        $html
    );

    // 9. Pastikan tabel tidak berada di dalam tag yang memecah layout
    $html = preg_replace('/<p>\s*(<table[^>]*>)/i', '$1', $html);
    $html = preg_replace('/(<\/table>)\s*<\/p>/i', '$1', $html);

    $html = preg_replace('/<(div|span)[^>]*>\s*(<table[^>]*>)/i', '$2', $html);
    $html = preg_replace('/(<\/table>)\s*<\/(div|span)>/i', '$1', $html);

    return $html;
}


function strict_allowed_attributes($tagContent)
{
    // Attribute yang benar-benar aman
    $allowed_attributes = ['href', 'src', 'colspan', 'rowspan'];

    // Ambil nama tag
    preg_match('/^\s*\/?\s*([a-z0-9]+)/i', $tagContent, $m);
    $tag = strtolower($m[1] ?? '');

    // Ambil semua attribute
    preg_match_all('/([a-z0-9\-]+)="([^"]*)"/i', $tagContent, $matches, PREG_SET_ORDER);

    $clean = $tag;

    foreach ($matches as $attr) {
        $name  = strtolower($attr[1]);
        $value = trim($attr[2]);

        // <a href="">
        if ($name === 'href') {
            if (preg_match('/^(https?:|#)/i', $value)) {
                $clean .= ' href="' . htmlspecialchars($value, ENT_QUOTES) . '"';
            }
        }

        // <img src="">
        if ($name === 'src') {

            // URL aman
            if (preg_match('/^https?:\/\//i', $value)) {
                $clean .= ' src="' . htmlspecialchars($value, ENT_QUOTES) . '"';
            }

            // Base64 aman, tapi tidak boleh SVG
            if (preg_match('/^data:image\/(png|jpg|jpeg|gif);base64/i', $value)) {
                $clean .= ' src="' . htmlspecialchars($value, ENT_QUOTES) . '"';
            }
        }

        // colspan / rowspan khusus numeric
        if (in_array($name, ['colspan', 'rowspan']) && preg_match('/^[0-9]+$/', $value)) {
            $clean .= ' ' . $name . '="' . $value . '"';
        }
    }

    return $clean;
}




if (!function_exists('translateEn')) {
    function translateEn($html)
    {
        try {
            $tr = new GoogleTranslate('en');

            $doc = new \DOMDocument();
            libxml_use_internal_errors(true); // untuk menghindari warning HTML invalid
            $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            libxml_clear_errors();

            // Fungsi rekursif untuk translate hanya teks node
            $translateNode = function ($node) use (&$translateNode, $tr) {
                foreach ($node->childNodes as $child) {
                    if ($child->nodeType === XML_TEXT_NODE) {
                        $translatedText = $tr->translate(trim($child->nodeValue));
                        $child->nodeValue = $translatedText;
                    } elseif ($child->hasChildNodes()) {
                        $translateNode($child); // rekursif
                    }
                }
            };

            $body = $doc->getElementsByTagName('body')->item(0);
            $translateNode($body);

            // Ambil innerHTML dari body (tanpa <html><body>)
            $innerHTML = '';
            foreach ($body->childNodes as $child) {
                $innerHTML .= $doc->saveHTML($child);
            }

            return $innerHTML;
        } catch (\Exception $e) {
            return $html; // fallback ke aslinya jika gagal
        }
    }
}


// if (!function_exists('getLogFile')) {
/**
 * Mengambil log file berdasarkan nama tabel, id table, dan jenis file
 *
 * @param string $nama_table
 * @param int $id_mou
 * @param string $jenis
 * @return \Illuminate\Support\Collection
 */
function getLogFile($id, $jenis)
{
    return DB::table('tbl_log_activity')
        ->select("tbl_log_activity.*", 'users.name as pengupload')
        ->leftJoin('users', 'users.username', '=', "tbl_log_activity.add_by")
        ->where('tbl_log_activity.id_table', $id)
        ->where('tbl_log_activity.jenis', $jenis)
        ->orderBy('tbl_log_activity.created_at', 'ASC')
        ->get();
}
// }


// if (! function_exists('phpSizeToBytes')) {
function phpSizeToBytes($size)
{
    $unit = strtolower(substr($size, -1));
    $value = (int) rtrim($size, 'KMGkmg');

    switch ($unit) {
        case 'g':
            return $value * 1024 * 1024 * 1024;
        case 'm':
            return $value * 1024 * 1024;
        case 'k':
            return $value * 1024;
        default:
            return (int) $size;
    }
}
// }

// if (! function_exists('cekMaxUpload')) {
function cekMaxUpload($request, $arrayFile)
{
    $phpMaxUpload = phpSizeToBytes(ini_get('upload_max_filesize'));
    $phpMaxPost   = phpSizeToBytes(ini_get('post_max_size'));

    // Ambil batas paling kecil (yang berlaku)
    $phpLimit = min($phpMaxUpload, $phpMaxPost);

    // Cek manual untuk semua file
    foreach ($arrayFile as $fileKey) {
        if ($request->hasFile($fileKey)) {
            if ($request->file($fileKey)->getSize() > $phpLimit) {

                return back()
                    ->withErrors([
                        $fileKey => "Ukuran file melebihi batas server (" . ini_get('upload_max_filesize') . ")."
                    ])
                    ->withInput();
            }
        }
    }
}
// }


// if (!function_exists('getDocumentUrl')) {
function getDocumentUrl(?string $filename, string $type): ?string
{
    if (!$filename || !is_string($filename)) return null;

    $filename = ltrim($filename, '/');

    $legacyPath = 'legacy/uploads/' . $filename;

    if (Storage::disk('local')->exists($legacyPath)) {
        // return route('legacy.file', ['path' => $legacyPath]);
        return storage_path('app/' . $legacyPath);
    }

    // Mapping tipe file ke folder lama
    $oldBasePath = 'public/uploads/mypartnership_old/';
    $oldPathMap = [
        'file_mitra'                    => 'media/doc/ajuan',
        'file_ajuan'                    => 'media/doc/ajuan',
        'file_mou'                      => 'media/doc/file',
        'file_imp'                      => 'media/doc/imp',
        'file_ikuenam'                  => 'media/doc/imp',
        'file_rekognisi'               => 'media/doc/adjunct',
        'file_sk'                       => 'media/doc/adjunct/sk',
        'buktipelaksanaan_rekognisi'   => 'media/doc/adjunct/buktipelaksanaan',
        'partner'                       => 'dok/prospect',
    ];

    // === CEK PATH BARU (default storage path: storage/app/public)
    $newPath = 'public/' . $filename;
    if (Storage::exists($newPath)) {
        return Storage::url($newPath); // Sudah full URL seperti: /storage/...
    }

    // === CEK PATH LAMA (mypartnership_old)
    if (isset($oldPathMap[$type])) {
        $oldRelativePath = $oldPathMap[$type] . '/' . $filename;
        $oldStoragePath = $oldBasePath . $oldRelativePath;

        if (Storage::exists($oldStoragePath)) {
            return Storage::url($oldStoragePath);
        }

        return 'https://new-mypartnership.ums.ac.id/' . $oldRelativePath;
    }

    // Jika tidak ditemukan di kedua lokasi
    return null;
}
// }

function Tanggal_Indo($datetime)
{
    if (empty($datetime)) {
        return '-';
    }

    try {
        $date = Carbon::parse($datetime);
    } catch (\Exception $e) {
        return $datetime; // fallback kalau formatnya aneh
    }

    $bulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    $tanggal = $date->day;
    $bulanIndo = $bulan[$date->month];
    $tahun = $date->year;

    return "{$tanggal} {$bulanIndo} {$tahun}";
}

function TanggalIndonesia($datetime)
{
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    $date = Carbon::parse($datetime);
    $tanggal = $date->day;
    $bulanIndo = $bulan[$date->month];
    $tahun = $date->year;
    $jam = $date->format('H:i:s');

    if ($date->format('H:i:s') === '00:00:00') {
        return "$tanggal $bulanIndo $tahun"; // Tampilkan hanya tanggal jika tidak ada waktu
    } else {
        return "$tanggal $bulanIndo $tahun $jam"; // Tampilkan tanggal + waktu jika ada
    }
}

function TanggalLengkap($datetime)
{
    $hari = [
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    ];
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    $date = Carbon::parse($datetime);
    $namaHari = $hari[$date->dayOfWeek];
    $tanggal = $date->day;
    $bulanIndo = $bulan[$date->month];
    $tahun = $date->year;
    $jam = $date->format('H:i:s');

    if ($date->format('H:i:s') === '00:00:00') {
        return "$namaHari, $tanggal $bulanIndo $tahun";
    } else {
        return "$namaHari, $tanggal $bulanIndo $tahun $jam";
    }
}
