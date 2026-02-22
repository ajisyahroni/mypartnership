<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelpdeskController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImplementasiController;
use App\Http\Controllers\KuesionerController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ReferensiController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\SinkronasiController;
use App\Http\Controllers\SurveiController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\Switch_roleController;
use App\Http\Controllers\SwitchRoleController;
use App\Http\Controllers\UploadController;
use App\Models\MailRecord;
use App\Models\PengajuanKerjaSama;

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::get('/logindokumenPendukung', [AuthController::class, 'getDokumenPendukung'])->name('login.dokumenPendukung');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/last-seen', [AuthController::class, 'lastseen'])->name('lastSeen');

// Route::get('/sinkronisasiSistem', [SinkronasiController::class, 'sinkronisasi'])->name('sinkronisasiSistem');
// Route::get('/sinkronisasiKerma', [SinkronasiController::class, 'kerma_db'])->name('sinkronisasiKerma');
// Route::get('/sinkronisasiImplementasi', [SinkronasiController::class, 'kerma_evaluasi'])->name('sinkronisasiImplementasi');
// Route::get('/sinkronisasiLembaga', [SinkronasiController::class, 'sinkronisasiLembaga'])->name('sinkronisasiLembaga');
// Route::get('/sinkronisasiKuesioner', [SinkronasiController::class, 'sinkronisasiKuesioner'])->name('sinkronisasiKuesioner');
// Route::get('/sinkronisasiRekognisi', [SinkronasiController::class, 'sinkronisasiRekognisi'])->name('sinkronisasiRekognisi');
// Route::get('/sinkronisasiProspekPartner', [SinkronasiController::class, 'sinkronisasiProspekPartner'])->name('sinkronisasiProspekPartner');
// Route::get('/sinkronisasiQPartner', [SinkronasiController::class, 'sinkronisasiQPartner'])->name('sinkronisasiQPartner');
// Route::get('/updateLembagaUMS', [SinkronasiController::class, 'updateLembagaUMS'])->name('updateLembagaUMS');
// Route::get('/updateLembagaUser', [SinkronasiController::class, 'updateLembagaUser'])->name('updateLembagaUser');
// Route::get('/updateLembagaKermaDB', [SinkronasiController::class, 'updateLembagaKermaDB'])->name('updateLembagaKermaDB');
// Route::get('/updateLembagaKermaEvaluasi', [SinkronasiController::class, 'updateLembagaKermaEvaluasi'])->name('updateLembagaKermaEvaluasi');
// Route::get('/updateJabatanBaru', [SinkronasiController::class, 'updateJabatanBaru'])->name('updateJabatanBaru');
// Route::get('/updateJabatanUser', [SinkronasiController::class, 'updateJabatanUser'])->name('updateJabatanUser');
// Route::get('/sinkronisasiSkorImplementasi', [SinkronasiController::class, 'sinkronisasiSkorImplementasi'])->name('sinkronisasiSkorImplementasi');
// Route::get('/saveMailMessagesFromSetting', [MailController::class, 'saveMailMessagesFromSetting'])->name('saveMailMessagesFromSetting');


Route::get('/login_new', [AuthController::class, 'login_new'])->name('login_new');
Route::get('/send-reminder-expired', [ReminderController::class, 'SendBroadcastCron'])->name('SendBroadcastCron');
Route::get('/send-reminder-expired-month', [ReminderController::class, 'SendBroadcastCronMonth'])->name('SendBroadcastCronMonth');
// Sinkronasi
Route::get('/sinkron_ttdby', [SinkronasiController::class, 'sinkron_ttdby'])->name('sinkron_ttdby');
Route::get('/kirimEmail/{id}', [ImplementasiController::class, 'kirimEmail'])->name('kirimEmail');

// Route::get('/auth/google/{intent}', [GoogleController::class, 'redirectToGoogle'])->name('redirectToGoogle');
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('redirectToGoogle');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('handleGoogleCallback');

Route::get('/survei/{id_table}/{jenis}', [SurveiController::class, 'showSurveyForm'])->name('showSurveyForm');

Route::post('/survei/submit', [SurveiController::class, 'submitSurvey'])->name('submitSurvey');
Route::get('/survei/getSurvei', [SurveiController::class, 'getSurvei'])->name('getSurvei');
Route::get('/survei/getSurveiImplementasi', [SurveiController::class, 'getSurveiImplementasi'])->name('getSurveiImplementasi');
Route::get('/survei/getSurveiRecognition', [SurveiController::class, 'getSurveiRecognition'])->name('getSurveiRecognition');
Route::get('/survei/getSurveiHibah', [SurveiController::class, 'getSurveiHibah'])->name('getSurveiHibah');


Route::get('kuesioner/survei/{id_kuesioner}', [KuesionerController::class, 'survei'])->name('kuesioner.survei');
Route::post('kuesioner/survei/submitFormSurvey', [KuesionerController::class, 'submitFormSurvey'])->name('kuesioner.submitFormSurvey');
Route::get('kuesioner/successfully', [KuesionerController::class, 'successfully'])->name('kuesioner.successfully');

Route::get('/cariInstitusi', [ReferensiController::class, 'cariInstitusi'])->name('cariInstitusi');
Route::get('/cariTingkatKerjaSama', [ReferensiController::class, 'cariTingkatKerjaSama'])->name('cariTingkatKerjaSama');
Route::get('/cariNegaraMitra', [ReferensiController::class, 'cariNegaraMitra'])->name('cariNegaraMitra');
Route::get('/cariJenisDokumen', [ReferensiController::class, 'cariJenisDokumen'])->name('cariJenisDokumen');
Route::get('/cariLembaga', [ReferensiController::class, 'cariLembaga'])->name('cariLembaga');
Route::get('/getReferensiImplementasi', [ReferensiController::class, 'getReferensiImplementasi'])->name('getReferensiImplementasi');
Route::get('/getReferensiDokumen', [ReferensiController::class, 'getReferensiDokumen'])->name('getReferensiDokumen');
Route::get('/getReferensiFilterHibah', [ReferensiController::class, 'getReferensiFilterHibah'])->name('getReferensiFilterHibah');
Route::get('/getDataNotifikasi', [NotifikasiController::class, 'getDataNotifikasi'])->name('getDataNotifikasi');
Route::get('/getDataNotifikasiHibah', [NotifikasiController::class, 'getDataNotifikasiHibah'])->name('getDataNotifikasiHibah');
Route::get('/getDataNotifikasiRecognition', [NotifikasiController::class, 'getDataNotifikasiRecognition'])->name('getDataNotifikasiRecognition');
Route::get('/getNotifikasiPartner', [NotifikasiController::class, 'getNotifikasiPartner'])->name('getNotifikasiPartner');

// SummerNote
Route::post('/upload-gambar-summernote', [UploadController::class, 'uploadSummernote']);

Route::get('/sendMailPengajuan', [PengajuanKerjaSama::class, 'sendMailPengajuan'])->name('sendMailPengajuan');

Route::name('front.')->group(base_path('routes/front.php'));

Route::prefix('api')->name('api.')->group(base_path('routes/api.php'));

Route::middleware(['web', 'auth'])->group(function () {
    Route::prefix('home')->name('home.')->group(base_path('routes/home.php'));

    Route::post('/set-role', [SwitchRoleController::class, 'setRole'])->name('setRole');
    Route::post('/set-menu', [SwitchRoleController::class, 'setMenu'])->name('setMenu');
    Route::get('/choose-menu', [SwitchRoleController::class, 'pilihMenu'])->name('pilihMenu');

    Route::group(['middleware' => ['currentRole:superadmin|admin']], function () {
        Route::middleware('currentRole:superadmin|admin')
            ->prefix('user-management')
            ->name('user-management.')
            ->group(base_path('routes/user-management.php'));
    });

    Route::group(['middleware' => ['currentRole:superadmin|admin']], function () {
        Route::middleware('currentRole:superadmin|admin')
            ->prefix('import-user')
            ->name('import-user.')
            ->group(base_path('routes/import-user.php'));
    });

    Route::group(['middleware' => ['currentRole:superadmin']], function () {
        Route::middleware('currentRole:superadmin')
            ->prefix('role-permission')
            ->name('role-permission.')
            ->group(base_path('routes/role-permission.php'));
    });

    Route::group(['middleware' => ['currentRole:superadmin']], function () {
        Route::middleware('currentRole:superadmin')
            ->prefix('backup')
            ->name('backup.')
            ->group(base_path('routes/backup.php'));
    });

    Route::group(['middleware' => ['currentRole:verifikator|user|admin']], function () {
        Route::middleware('currentRole:verifikator|user|admin')
            ->prefix('pengajuan')
            ->name('pengajuan.')
            ->group(base_path('routes/pengajuan.php'));
    });

    Route::group(['middleware' => ['currentRole:superadmin|verifikator|user|admin']], function () {
        Route::middleware('currentRole:superadmin|verifikator|user|admin')
            ->prefix('dokumen')
            ->name('dokumen.')
            ->group(base_path('routes/dokumen.php'));
    });

    Route::group(['middleware' => ['currentRole:superadmin|verifikator|user|admin']], function () {
        Route::middleware('currentRole:superadmin|verifikator|user|admin')
            ->prefix('implementasi')
            ->name('implementasi.')
            ->group(base_path('routes/implementasi.php'));
    });

    Route::group(['middleware' => ['currentRole:superadmin|verifikator|user|admin']], function () {
        Route::middleware('currentRole:superadmin|verifikator|user|admin')
            ->prefix('kuesioner')
            ->name('kuesioner.')
            ->group(base_path('routes/kuesioner.php'));
    });

    Route::group(['middleware' => ['currentRole:superadmin|verifikator|user|admin']], function () {
        Route::middleware('currentRole:superadmin|verifikator|user|admin')
            ->prefix('reminder')
            ->name('reminder.')
            ->group(base_path('routes/reminder.php'));
    });

    Route::group(['middleware' => ['currentRole:superadmin|verifikator|user|admin']], function () {
        Route::middleware('currentRole:superadmin|verifikator|user|admin')
            ->prefix('mail')
            ->name('mail.')
            ->group(base_path('routes/mail.php'));
    });

    Route::group(['middleware' => ['currentRole:superadmin|verifikator|user|admin']], function () {
        Route::middleware('currentRole:superadmin|verifikator|user|admin')
            ->prefix('chat')
            ->name('chat.')
            ->group(base_path('routes/chat.php'));
    });

    Route::group(['middleware' => ['currentRole:superadmin|verifikator|user|admin']], function () {
        Route::middleware('currentRole:superadmin|verifikator|user|admin')
            ->prefix('chatRecognisi')
            ->name('chatRecognisi.')
            ->group(base_path('routes/chatRecognisi.php'));
    });

    Route::group(['middleware' => ['currentRole:admin']], function () {
        Route::middleware('currentRole:admin')
            ->prefix('referensi')
            ->name('referensi.')
            ->group(base_path('routes/referensi.php'));
    });

    Route::group(['middleware' => ['currentRole:admin|user|verifikator']], function () {
        Route::middleware('currentRole:admin|user|verifikator')
            ->prefix('dokumenPendukung')
            ->name('dokumenPendukung.')
            ->group(base_path('routes/dokumenPendukung.php'));
    });

    Route::group(['middleware' => ['currentRole:admin|verifikator|user']], function () {
        Route::middleware('currentRole:admin|verifikator|user')
            ->prefix('recognition')
            ->name('recognition.')
            ->group(base_path('routes/recognition.php'));
    });

    Route::group(['middleware' => ['currentRole:admin|verifikator|user']], function () {
        Route::middleware('currentRole:admin|verifikator|user')
            ->prefix('potential_partner')
            ->name('potential_partner.')
            ->group(base_path('routes/potential_partner.php'));
    });

    Route::group(['middleware' => ['currentRole:admin|verifikator|user']], function () {
        Route::middleware('currentRole:admin|verifikator|user')
            ->prefix('hibah-kerjasama')
            ->name('hibah.')
            ->group(base_path('routes/hibah.php'));
    });

    Route::group(['middleware' => ['currentRole:admin']], function () {
        Route::middleware('currentRole:admin')
            ->prefix('dokumenPendukungRecognition')
            ->name('dokumenPendukungRecognition.')
            ->group(base_path('routes/dokumenPendukungRecognition.php'));
    });

    Route::group(['middleware' => ['currentRole:admin|user|verifikator|eksekutif']], function () {
        Route::middleware('currentRole:admin|user|verifikator|eksekutif')
            ->prefix('survei')
            ->name('survei.')
            ->group(base_path('routes/survei.php'));
    });
});

use PHPMailer\PHPMailer\PHPMailer;

Route::get('/test-email', function () {
    $recipients = [
        'mtzal128@gmail.com',
        'mutazfarisi1@gmail.com',
        // 'muhammaddhimas000@gmail.com',
    ];

    foreach ($recipients as $email) {
        $debugLog = '';

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mtzal128@gmail.com';
        $mail->Password = 'citrhmnpueypcwot'; // ganti dengan app password valid
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function ($str, $level) use (&$debugLog) {
            $debugLog .= '<span style="display:block;color:black;">' . gmdate('Y-m-d H:i:s') . " [{$level}] " . htmlspecialchars($str) . "</span>\n";
        };

        $mail->setFrom('mtzal128@gmail.com', 'UMS Notification');
        $mail->addReplyTo('no-reply@yourdomain.com', 'No Reply');
        $mail->addAddress($email); // dinamis per email

        $mail->Subject = 'SMTP Test';
        $mail->Body = 'Test message dari sistem Laravel + PHPMailer.';

        try {
            $mail->send();

            MailRecord::create([
                'status_sent' => 'Sukses',
                'subject_sent' => $mail->Subject,
                'pesan_sent' => $mail->Body,
                'institusi' => 'SMTP Test',
                'send_to' => $email,
                'email_from' => 'mtzal128@gmail.com',
                'debug_error' => '<pre><br><strong>Berhasil kirim:</strong>' . htmlspecialchars($mail->ErrorInfo) . '<br>' . $debugLog . '</pre>',
                'tanggal_sent' => now(),
                'created_at' => now(),
            ]);
        } catch (Exception $e) {
            MailRecord::create([
                'status_sent' => 'Gagal',
                'subject_sent' => $mail->Subject,
                'pesan_sent' => $mail->Body,
                'institusi' => 'SMTP Test',
                'send_to' => $email,
                'email_from' => 'mtzal128@gmail.com',
                'debug_error' => '<pre><br><strong>Gagal kirim:</strong> ' . htmlspecialchars($mail->ErrorInfo) . '<br>' . $debugLog . '</pre>',
                'tanggal_sent' => now(),
                'created_at' => now(),
            ]);
        }

        // Reset PHPMailer untuk pengiriman berikutnya
        $mail->clearAddresses();
        $mail->clearReplyTos();
    }

    return 'Pengiriman ke semua email selesai, log disimpan.';
});
