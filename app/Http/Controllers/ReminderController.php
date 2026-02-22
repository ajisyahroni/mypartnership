<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailMailer;
use App\Jobs\SendReminder;
use App\Models\ConfigWebsite;
use App\Models\MailMessages;
use App\Models\MailSetting;
use App\Models\PengajuanKerjaSama;
use App\Models\RefBentukKerjaSama;
use App\Models\RefGroupRangking;
use App\Models\RefJenisDokumen;
use App\Models\RefJenisDokumenMoU;
use App\Models\RefJenisInstitusiMitra;
use App\Models\RefLembagaUMS;
use App\Models\RefNegara;
use App\Models\RefPerusahaan;
use App\Models\RefTingkatKerjaSama;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

set_time_limit(0);

class ReminderController extends Controller
{
    public function index()
    {

        $dataLengkap = collect(DB::select("
            SELECT kerma_db.*
            FROM kerma_db
            JOIN (
                SELECT username, MIN(id) AS id
                FROM users
                GROUP BY username
            ) ux ON ux.username = kerma_db.add_by
            JOIN users AS u ON u.id = ux.id
            WHERE kerma_db.tgl_selesai = '0000-00-00 00:00:00'
            AND kerma_db.tgl_verifikasi_kabid != '0000-00-00 00:00:00'
            AND (
                    kerma_db.last_reminder_at IS NULL
                    OR kerma_db.last_reminder_at <= NOW() - INTERVAL 24 HOUR
                )
        "));


        $dataProduktif = collect(DB::select("SELECT
                    kerma_db.*
                FROM
                    kerma_db
                    JOIN (
                        SELECT username, MIN(id) AS id
                        FROM users
                        GROUP BY username
                    ) ux ON ux.username = kerma_db.add_by
                    JOIN users AS u ON u.id = ux.id
                    LEFT JOIN kerma_evaluasi ON kerma_evaluasi.id_mou = kerma_db.id_mou
                WHERE
                    kerma_db.tgl_selesai != '0000-00-00 00:00:00' 
                    AND (kerma_db.mulai <= CURDATE() AND kerma_db.selesai >= CURDATE())
                    AND kerma_evaluasi.id_mou IS NULL
                    AND (
                        kerma_db.last_reminder_at IS NULL
                        OR kerma_db.last_reminder_at <= NOW() - INTERVAL 24 HOUR
                    )
                "));

        $dataExpired = collect(DB::select("
            SELECT * FROM kerma_db 
            JOIN (
                SELECT username, MIN(id) AS id
                FROM users
                GROUP BY username
            ) ux ON ux.username = kerma_db.add_by
            JOIN users AS u ON u.id = ux.id
            WHERE tgl_selesai != '0000-00-00 00:00:00'
            AND status_kerma != 'Dalam Perpanjangan'
            AND selesai >= CURDATE()
            AND selesai < DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
            AND (
                    kerma_db.last_reminder_at IS NULL
                    OR kerma_db.last_reminder_at <= NOW() - INTERVAL 24 HOUR
                )
        "));


        $data = [
            'li_active' => 'reminder',
            'li_sub_active' => 'reminder',
            'title' => 'Reminder',
            'page_title' => 'Reminder Kerja Sama',
            'dataLengkap' => $dataLengkap,
            'dataProduktif' => $dataProduktif,
            'dataExpired' => $dataExpired,
        ];

        return view('reminder/index', $data);
    }

    public function list()
    {
        $data = [
            'li_active' => 'reminder',
            'li_sub_active' => 'reminder-list',
            'title' => 'Daftar Reminder',
            'page_title' => 'Daftar Reminder Kerja Sama',
        ];

        return view('reminder/list', $data);
    }

    public function getDataList(Request $request)
    {
        $today = Carbon::now();
        $query = PengajuanKerjaSama::select('*')->with(['getLembaga', 'getPengusul', 'getVerifikator', 'getKabid', 'getPenandatangan']);
        $query->where('tgl_selesai', '0000-00-00 00:00:00')
            ->Where(function ($q) use ($today) {
                $q->whereDate('mulai', '<=', $today)
                    ->whereDate('selesai', '>=', $today);
            });

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('lembaga', function ($row) {
                // return $row->getLembaga->nama_lmbg ?? '';
                return $row->lembaga->nama_lmbg ?? '';
            })
            ->addColumn('status_verifikasi', function ($row) {
                return $row->getStatusPengajuan();
            })
            ->addColumn('status_pengajuan', function ($row) {
                $today = now();
                $mulai = Carbon::parse($row->mulai);
                $selesai = Carbon::parse($row->selesai);

                $diff = $selesai->diff($today);
                $durasiTersisa = "{$diff->y} Tahun {$diff->m} Bulan {$diff->d} Hari sebelum expired";

                if ($mulai > $today) {
                    return '<span class="badge bg-warning" data-title-tooltip="Pengajuan masih dalam proses. Mulai: ' . $mulai->format('d-m-Y') . '">Dalam Proses</span>';
                } elseif ($mulai <= $today && $selesai >= $today) {
                    return '<span class="badge bg-success" data-title-tooltip="Kerja sama akan berakhir dalam ' . $durasiTersisa . '">Berjalan</span>';
                } elseif ($selesai->lt($today)) {
                    // Pastikan Carbon digunakan dengan benar
                    $hariTerlambat = (int) $selesai->diffInDays($today);

                    if (!empty($this->perpanjangan)) {
                        return '<span class="badge bg-info" data-title-tooltip="Sedang dalam tahap perpanjangan. Berakhir: ' . $selesai->format('d-m-Y') . '">Dalam Perpanjangan</span>';
                    }

                    return '<span class="badge bg-danger" data-title-tooltip="Masa kerja sama telah berakhir pada ' . $selesai->format('d-m-Y') . '. Sudah terlewat ' . $hariTerlambat . ' hari">Expired</span>';
                }

                return '<span class="badge bg-secondary">Unknown</span>';
            })
            ->addColumn('masa_berlaku', function ($row) {
                $today = now();
                $mulai = Carbon::parse($row->mulai);
                $selesai = Carbon::parse($row->selesai);

                $diff = $selesai->diff($today);
                $durasiTersisa = "{$diff->y} Tahun {$diff->m} Bulan {$diff->d} Hari sebelum expired";
                return 'Kerja sama akan berakhir dalam ' . $durasiTersisa;
            })
            ->addColumn('wilayah_mitra', function ($row) {
                if ($row->dn_ln == 'Luar Negeri') {
                    return $row->negara_mitra;
                } else {
                    return $row->wilayah_mitra;
                }
            })
            ->addColumn('jenis_institusi_mitra', function ($row) {
                if ($row->jenis_institusi == 'Lain-lain') {
                    return $row->nama_institusi;
                } else {
                    return $row->jenis_institusi;
                }
            })
            ->addColumn('action', function ($row) {
                $action = '<div class="btn-group" role="group">';

                // Tombol tambahan (Chat, Lihat Detail, Edit, Hapus)
                // $action .= '<button data-title-tooltip="Lihat Detail" class="btn btn-info btn-detail" data-srcPdf="' . asset('storage/' . $row->file_mou) . '" data-id_mou="' . $row->id_mou . '">
                //                 <i class="bx bx-show"></i>
                //             </button>
                //             <a href="' . route('pengajuan.editBaru', ['id' => $row->id_mou]) . '" data-title-tooltip="Edit Pengajuan" class="btn btn-warning btn-edit" data-id_mou="' . $row->id_mou . '">
                //                 <i class="bx bx-edit"></i>
                //             </a>
                //             <button data-title-tooltip="Hapus Pengajuan" class="btn btn-danger btn-hapus" data-id_mou="' . $row->id_mou . '">
                //                 <i class="bx bx-trash"></i>
                //             </button>
                //         </div>';
                $action .= '<button data-title-tooltip="Lihat Detail" class="btn btn-info btn-detail" data-srcPdf="' . asset('storage/' . $row->file_mou) . '" data-id_mou="' . $row->id_mou . '">
                                <i class="bx bx-show"></i>
                            </button>
                        </div>';

                return $action;
            })

            ->rawColumns(['action', 'status_pengajuan', 'status_verifikasi'])
            ->make(true);
    }

    public function SendReminder(Request $request)
    {
        $id_mou = $request->id_mou;
        $dataPengajuan = PengajuanKerjaSama::where('id_mou', $id_mou)->first();
        $receiver = User::where('username', $dataPengajuan->add_by)->first()->email;
        $lastReminder = $dataPengajuan->last_reminder_at;
        if ($lastReminder) {
            $nextAllowed = Carbon::parse($lastReminder)->addHours(24); // Waktu reminder berikutnya
            $now = Carbon::now();

            if ($now < $nextAllowed) {
                $remaining = $nextAllowed->diffInHours($now); // sisa jam
                return response()->json([
                    'status' => 'error',
                    'message' => "Reminder hanya dapat dikirim setiap 24 jam. Kirim ulang dalam {$remaining} jam."
                ], 429);
            }
        }

        // --- Lanjut logic reminder (kode anda tetap) ---
        $tipe = $request->tipe;
        $mail = MailSetting::where('is_active', '1')->first();

        if ($tipe == 'expired') {
            $today = now();
            $mulai = Carbon::parse($dataPengajuan->mulai);
            $selesai = Carbon::parse($dataPengajuan->selesai);
            $diff = $selesai->diff($today);
            $durasiTersisa = "{$diff->y} Tahun {$diff->m} Bulan {$diff->d} Hari sebelum expired";

            $mailMessages = MailMessages::where('jenis', 'subjek_exp')->first();

            $title = $mailMessages->subjek;
            $viewEmail = $mailMessages->pesan;

            // $title = $mail->subjek_exp;
            // $msg = str_replace("{@durasiTersisa}", $durasiTersisa, $mail->rmd_exp_kerma);
            $msg = str_replace("{@durasiTersisa}", $durasiTersisa, $viewEmail);
        } elseif ($tipe == 'produktif') {
            $mailMessages = MailMessages::where('jenis', 'subjek_prod_kerma')->first();

            $title = $mailMessages->subjek;
            $msg = $mailMessages->pesan;

            // $title = $mail->subjek_prod_kerma;
            // $msg = $mail->rmd_prod_kerma;
        } elseif ($tipe == 'lengkapi') {
            $mailMessages = MailMessages::where('jenis', 'subjek_data_kerma')->first();

            $title = $mailMessages->subjek;
            $msg = $mailMessages->pesan;

            // $title = $mail->subjek_data_kerma;
            // $msg = $mail->rmd_data_kerma;
        }

        $message = str_replace("{@nama_institusi}", $dataPengajuan->nama_institusi, $msg);

        $dataEmailPIC = [
            'subject' => $title,
            'dataMessage' => $message,
            'sender' => 'MyPartnership',
        ];

        $rendered = view('emails.template', $dataEmailPIC)->render();

        $dataSendMail = [
            'message' => $rendered,
            'title' => $title,
            'institusi' => $dataPengajuan->nama_institusi,
            'session' => session('environment'),
            'sender' => Auth::user()->username,
            'MailSetting' => $mail->toArray(),
            'receiver' => $receiver
        ];
        if ($mailMessages->status == '1') {
            SendEmailMailer::dispatchSync($dataSendMail);
            $dataPengajuan->update([
                'last_reminder_at' => now()
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Reminder telah dikirim ke ' . $receiver
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Setting email untuk pengiriman reminder dinonaktifkan'
            ], 200);
        }
    }


    public function SendBroadcast(Request $request)
    {
        $tipe = $request->tipe;
        $mail = MailSetting::where('is_active', '1')->first();
        if ($tipe == 'expired') {
            $dataPengajuan = collect(DB::select("
                    SELECT
                        kerma.id_mou,
                        kerma.nama_institusi,
                        kerma.mulai,
                        kerma.selesai,
                        kerma.last_reminder_at,
                        u.email
                    FROM
                        kerma_db AS kerma

                        /* Pastikan 1 user per username */
                        JOIN (
                            SELECT username, MIN(id) AS id
                            FROM users
                            GROUP BY username
                        ) ux ON ux.username = kerma.add_by
                        JOIN users AS u ON u.id = ux.id

                    WHERE
                        kerma.tgl_selesai != '0000-00-00 00:00:00'
                        AND kerma.status_kerma != 'Dalam Perpanjangan'
                        AND kerma.selesai >= CURDATE()
                        AND kerma.selesai < DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
                        AND (
                            kerma.last_reminder_at IS NULL
                            OR kerma.last_reminder_at <= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                        );
                "));
        } else if ($tipe == 'produktif') {
            // KERJA SAMA YANG BELUM MENGISI IMPLEMENTASI
            $dataPengajuan = collect(DB::select("SELECT
                            kerma.id_mou,
                            kerma.nama_institusi,
                            kerma.mulai,
                            kerma.selesai,
                            kerma.last_reminder_at,
                            u.email
                        FROM
                            kerma_db AS kerma
                            LEFT JOIN (
                                SELECT DISTINCT id_mou
                                FROM kerma_evaluasi
                            ) ev ON ev.id_mou = kerma.id_mou
                            JOIN (
                                SELECT username, MIN(id) AS id
                                FROM users
                                GROUP BY username
                            ) ux ON ux.username = kerma.add_by
                            JOIN users AS u ON u.id = ux.id

                        WHERE
                            kerma.tgl_selesai != '0000-00-00 00:00:00'
                            AND kerma.mulai <= CURDATE()
                            AND kerma.selesai >= CURDATE()
                            AND ev.id_mou IS NULL
                            AND (
                                kerma.last_reminder_at IS NULL
                                OR kerma.last_reminder_at <= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                            );
                "));
        } else if ($tipe == 'lengkapi') {
            // KERJA SAMA YANG BELUM SELESAI VERIFIKASI DAN DI DALAM MASA BERLAKU
            $dataPengajuan = collect(DB::select("
                        SELECT
                            kerma.*,
                            u.email
                        FROM
                            kerma_db AS kerma
                        JOIN (
                            SELECT username, MIN(id) AS id
                            FROM users
                            GROUP BY username
                        ) ux ON ux.username = kerma.add_by
                        JOIN users AS u ON u.id = ux.id
                        WHERE
                            kerma.tgl_selesai = '0000-00-00 00:00:00'
                        AND kerma.tgl_verifikasi_kabid != '0000-00-00 00:00:00'
                        AND (
                            kerma.last_reminder_at IS NULL
                            OR kerma.last_reminder_at <= NOW() - INTERVAL 24 HOUR
                        );
                    "));
        }

        if (count($dataPengajuan) == 0) {
            return response()->json(['status' => 'info', 'message' => 'Tidak Ada Data yang Dikirim'], 200);
        } else {
            foreach ($dataPengajuan as $key => $dtkerma) {
                $receiver = $dtkerma->email;
                $emailPengusul = $dtkerma->email;
                if ($tipe == 'expired') {
                    $today = now();
                    $mulai = Carbon::parse($dtkerma->mulai);
                    $selesai = Carbon::parse($dtkerma->selesai);
                    $diff = $selesai->diff($today);
                    $durasiTersisa = "{$diff->y} Tahun {$diff->m} Bulan {$diff->d} Hari sebelum expired";

                    $mailMessages = MailMessages::where('jenis', 'subjek_exp')->first();

                    $title = $mailMessages->subjek;
                    $msg = $mailMessages->pesan;

                    // $title = $mail->subjek_exp;
                    // $msg = $mail->rmd_exp_kerma;
                    $msg = (string) str_replace("{@durasiTersisa}", $durasiTersisa, $msg);
                } else if ($tipe == 'produktif') {
                    $mailMessages = MailMessages::where('jenis', 'subjek_prod_kerma')->first();

                    $title = $mailMessages->subjek;
                    $msg = $mailMessages->pesan;

                    // $title = $mail->subjek_prod_kerma;
                    // $msg = $mail->rmd_prod_kerma;
                } else if ($tipe == 'lengkapi') {
                    $mailMessages = MailMessages::where('jenis', 'subjek_data_kerma')->first();

                    $title = $mailMessages->subjek;
                    $msg = $mailMessages->pesan;

                    // $title = $mail->subjek_data_kerma;
                    // $msg = $mail->rmd_data_kerma;
                }

                $message = (string) str_replace("{@nama_institusi}", $dtkerma->nama_institusi, $msg);
                $dataEmailPIC = [
                    'subject' => $title,
                    'dataMessage' => $message,
                    'sender' => 'MyPartnership',
                ];
                $message = view('emails.template', $dataEmailPIC)->render();
                $dataSendMail = [
                    'message' => $message,
                    'title' => $title,
                    'institusi' => $dtkerma->nama_institusi,
                    'session' => session('environment'),
                    'sender' => Auth::user()->username ?? 'sistem',
                    'MailSetting' => $mail->toArray(),
                    'receiver' => $receiver
                ];

                if ($mailMessages->status == '1') {
                    SendEmailMailer::dispatchSync($dataSendMail);

                    DB::table('kerma_db')
                        ->where('id_mou', $dtkerma->id_mou)
                        ->update([
                            'last_reminder_at' => now()
                        ]);
                } else {
                    break;
                }
            }
        }

        if ($mailMessages->status == '1') {
            // return response()->json(['status' => 'success', 'message' => 'Reminder telah dikirim'], 200);
            return response()->json([
                'status' => true,
                'message' => 'Reminder telah dikirim'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Setting email untuk pengiriman reminder dinonaktifkan'
            ], 200);
        }
    }


    public function SendBroadcastCron(Request $request)
    {
        $mail = MailSetting::where('is_active', '1')->first();

        $dataPengajuan = collect(DB::select("
                    SELECT
                        kerma.id_mou,
                        kerma.nama_institusi,
                        kerma.mulai,
                        kerma.selesai,
                        u.email 
                    FROM
                        kerma_db AS kerma
                    JOIN (
                        SELECT username, MIN(id) AS id
                        FROM users
                        GROUP BY username
                    ) ux ON ux.username = kerma.add_by
                    JOIN users AS u ON u.id = ux.id
                    WHERE
                        kerma.tgl_selesai != '0000-00-00 00:00:00'
                        AND kerma.status_kerma != 'Dalam Perpanjangan'
                        AND DATE(kerma.selesai) IN (
                            CURDATE(),
                            DATE_ADD(CURDATE(), INTERVAL 3 MONTH),
                            DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
                        )
                        AND (
                            kerma.last_reminder_at IS NULL
                            OR kerma.last_reminder_at <= NOW() - INTERVAL 24 HOUR
                        );

                "));

        $dataPengajuanProduktif = collect(DB::select("
                    SELECT
                        kd.*,
                        u.email
                    FROM
                        kerma_db kd
                        LEFT JOIN kerma_evaluasi ke ON ke.id_mou = kd.id_mou
                        JOIN (
                            SELECT username, MIN(id) AS id
                            FROM users
                            GROUP BY username
                        ) ux ON ux.username = kd.add_by
                        JOIN users AS u ON u.id = ux.id
                    WHERE
                        kd.tgl_selesai != '0000-00-00 00:00:00'
                        AND ke.id_mou IS NULL
                        AND 
                            (		DATE_ADD(STR_TO_DATE(kd.tgl_selesai, '%Y-%m-%d'),INTERVAL 3 MONTH) = CURDATE()
                            OR
                            DATE_ADD(STR_TO_DATE(kd.tgl_selesai, '%Y-%m-%d'),INTERVAL 6 MONTH) = CURDATE()
                        )
                        AND (
                            kd.last_reminder_at IS NULL
                            OR kd.last_reminder_at <= NOW() - INTERVAL 24 HOUR
                        )
                    ORDER BY
                        kd.created_at DESC;

                "));

        if (count($dataPengajuan) == 0) {
        } else {
            foreach ($dataPengajuan as $key => $dtkerma) {
                $receiver = $dtkerma->email;
                $emailPengusul = $dtkerma->email;
                $today = now();
                $mulai = Carbon::parse($dtkerma->mulai);
                $selesai = Carbon::parse($dtkerma->selesai);
                $diff = $selesai->diff($today);
                $durasiTersisa = "{$diff->y} Tahun {$diff->m} Bulan {$diff->d} Hari sebelum expired";

                $title = $mail->subjek_exp;
                $msg = $mail->rmd_exp_kerma;
                $msg = (string) str_replace("{@durasiTersisa}", $durasiTersisa, $msg);

                $message = (string) str_replace("{@nama_institusi}", $dtkerma->nama_institusi, $msg);
                $dataEmailPIC = [
                    'subject' => $title,
                    'dataMessage' => $message,
                    'sender' => 'MyPartnership',
                ];
                $message = view('emails.template', $dataEmailPIC)->render();
                $session = ConfigWebsite::where('status', 1)->value('keterangan');
                $dataSendMail = [
                    'message' => $message,
                    'title' => $title,
                    'institusi' => $dtkerma->nama_institusi,
                    'session' => $session,
                    'sender' => Auth::user()->username ?? 'sistem',
                    'MailSetting' => $mail->toArray(),
                    'receiver' => $receiver
                ];
                SendEmailMailer::dispatch($dataSendMail);

                DB::table('kerma_db')
                    ->where('id_mou', $dtkerma->id_mou)
                    ->update([
                        'last_reminder_at' => now()
                    ]);
            }
        }

        if (count($dataPengajuanProduktif) == 0) {
        } else {
            foreach ($dataPengajuanProduktif as $key => $dtkerma) {
                $receiver = $dtkerma->email;
                $emailPengusul = $dtkerma->email;
                $today = now();
                $mulai = Carbon::parse($dtkerma->mulai);
                $selesai = Carbon::parse($dtkerma->selesai);
                $diff = $selesai->diff($today);
                $durasiTersisa = "{$diff->y} Tahun {$diff->m} Bulan {$diff->d} Hari sebelum expired";

                $title = $mail->subjek_prod_kerma;
                $msg = $mail->rmd_prod_kerma;

                $message = (string) str_replace("{@nama_institusi}", $dtkerma->nama_institusi, $msg);
                $dataEmailPIC = [
                    'subject' => $title,
                    'dataMessage' => $message,
                    'sender' => 'MyPartnership',
                ];
                $message = view('emails.template', $dataEmailPIC)->render();
                $session = ConfigWebsite::where('status', 1)->value('keterangan');
                $dataSendMail = [
                    'message' => $message,
                    'title' => $title,
                    'institusi' => $dtkerma->nama_institusi,
                    'session' => $session,
                    'sender' => Auth::user()->username ?? 'sistem',
                    'MailSetting' => $mail->toArray(),
                    'receiver' => $receiver
                ];
                SendEmailMailer::dispatch($dataSendMail);

                DB::table('kerma_db')
                    ->where('id_mou', $dtkerma->id_mou)
                    ->update([
                        'last_reminder_at' => now()
                    ]);
            }
        }
    }

    // public function SendBroadcastCronMonth(Request $request)
    // {
    //     $mail = MailSetting::where('is_active', '1')->first();
    //     $dataPengajuan = collect(DB::select("
    //                 SELECT
    //                     kerma.nama_institusi,
    //                     kerma.mulai,
    //                     kerma.selesai,
    //                     u.email 
    //                 FROM
    //                     kerma_db AS kerma
    //                     JOIN users AS u ON u.username = kerma.add_by 
    //                 WHERE
    //                     kerma.tgl_selesai != '0000-00-00 00:00:00' 
    //                     AND kerma.selesai >= CURDATE() 
    //                     AND kerma.selesai < DATE_ADD(
    //                     CURDATE(),
    //                     INTERVAL 6 MONTH)
    //             "));

    //     if (count($dataPengajuan) == 0) {
    //         // return response()->json(['status' => 'info', 'message' => 'Tidak Ada Data yang Dikirim'], 200);
    //     } else {
    //         foreach ($dataPengajuan as $key => $dtkerma) {
    //             $receiver = $dtkerma->email;
    //             $emailPengusul = $dtkerma->email;
    //             $today = now();
    //             $mulai = Carbon::parse($dtkerma->mulai);
    //             $selesai = Carbon::parse($dtkerma->selesai);
    //             $diff = $selesai->diff($today);
    //             $durasiTersisa = "{$diff->y} Tahun {$diff->m} Bulan {$diff->d} Hari sebelum expired";

    //             $title = $mail->subjek_exp;
    //             $msg = $mail->rmd_exp_kerma;
    //             $msg = (string) str_replace("{@durasiTersisa}", $durasiTersisa, $msg);

    //             $message = (string) str_replace("{@nama_institusi}", $dtkerma->nama_institusi, $msg);
    //             $dataEmailPIC = [
    //                 'subject' => $title,
    //                 'dataMessage' => $message,
    //                 'sender' => 'MyPartnership',
    //             ];
    //             $message = view('emails.template', $dataEmailPIC)->render();

    //             $dataSendMail = [
    //                 'message' => $message,
    //                 'title' => $title,
    //                 'institusi' => $dtkerma->nama_institusi,
    //                 'session' => session('environment'),
    //                 'sender' => Auth::user()->username ?? 'sistem',
    //                 'MailSetting' => $mail->toArray(),
    //                 'receiver' => $receiver
    //             ];
    //             SendEmailMailer::dispatchSync($dataSendMail);
    //             // \Artisan::call('queue:work', [
    //             //     '--once' => true,
    //             //     '--delay' => 0,
    //             // ]);
    //         }
    //     }
    // }

    public function getDetailPengajuan(Request $request)
    {
        $dataPengajuan = PengajuanKerjaSama::select('*')
            ->with(['getLembaga', 'getPengusul', 'getVerifikator'])
            ->where('id_mou', $request->id_mou)
            ->first();

        $fileUrl = getDocumentUrl($dataPengajuan->file_mou, 'file_mou');
        $data = [
            'dataPengajuan' => $dataPengajuan,
            'fileUrl' => @$fileUrl,
        ];

        $view = view('pengajuan/detail_data', $data);
        return response()->json(['html' => $view->render(), 'filePdf' =>  $fileUrl], 200);
        // return response()->json(['html' => $view->render(), 'filePdf' => asset('storage/' . $dataPengajuan->file_mou)], 200);
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
