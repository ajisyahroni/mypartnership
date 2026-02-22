<?php

namespace App\Http\Controllers;

use App\Models\MailMessages;
use App\Models\MailRecord;
use App\Models\MailSetting;
use App\Models\PengajuanKerjaSama;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MailController extends Controller
{
    public function index()
    {
        $data = [
            'li_active' => 'mail',
            'li_sub_active' => 'mail_records',
            'title' => 'Mail Log Kerja Sama',
            'page_title' => 'Mail Log Kerja Sama',
        ];

        return view('admin/mail/index', $data);
    }

    public function getData(Request $request)
    {
        $query = MailRecord::select([
            'id_record',
            'subject_sent',
            'status_sent',
            'tanggal_sent',
            'pesan_sent',
            'institusi',
        ])->whereDate('tanggal_sent', '>=', Carbon::now()->subDay(60))
            ->orderBy('tanggal_sent', 'DESC')->get();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($row) {
                return TanggalLengkap($row->tanggal_sent);
            })
            ->addColumn('pesan_sent', function ($row) {
                return $row->pesan_sent;
            })
            ->addColumn('show_debug', function ($row) {
                return '<a href="javascript:void(0)" data-id="' . $row->id_record . '" class="btn btn-info btn-sm btn-show-mail">Show</a>';
            })
            ->rawColumns(['show_debug', 'pesan_sent'])
            ->make(true);
    }


    public function getDetailMail(Request $request)
    {
        $dataMail = MailRecord::select('*')
            ->where('id_record', $request->id)
            ->first();

        $data = [
            'dataMail' => $dataMail,
        ];

        $view = view('admin/mail/show_message', $data);
        return response()->json(['html' => $view->render()], 200);
    }

    public function setting()
    {
        $data = [
            'li_active' => 'mail',
            'li_sub_active' => 'mail_settings',
            'title' => 'Mail Setting',
            'page_title' => 'Mail Setting',
        ];

        return view('admin/mail/settings', $data);
    }

    public function getDataSetting(Request $request)
    {
        $query = MailSetting::select('*')->get();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama', function ($row) {
                $nama = '<span class="text-dark">Nama : <b> ' . $row->nama . '</b></span><br>';
                $nama .= '<span class="text-dark">Email : <b>' . $row->email . '</b></span>';
                return $nama;
            })
            ->addColumn('subjek_reply_to', function ($row) {
                $subjek = '<span class="text-dark">Subjek : <b>' . $row->subjek_reply_to . '</b></span><br>';
                $subjek .= '<span class="text-dark">Reply To : <b>' . $row->reply_to . '</b></span>';
                return $subjek;
            })
            ->addColumn('email_receiver', function ($row) {
                $usernames = explode(',', $row->email_receiver); // Pisahkan berdasarkan koma
                $listItems = '';

                foreach ($usernames as $username) {
                    $listItems .= '<li>' . trim($username) . '</li>';
                }

                return '<ol class="text-dark mb-0">' . $listItems . '</ol>';
            })
            ->addColumn('action', function ($row) {
                $button = '<div class="btn-group">';
                $button .= '<button id="btnEdit" class="btn btn-warning btn-sm btn-edit" data-id_setting="' . $row->id_setting . '" data-email_receiver="' . $row->email_receiver . '" data-pass="' . $row->pass . '" data-email="' . $row->email . '" data-nama="' . $row->nama . '" data-user="' . $row->user . '" data-host="' . $row->host . '" data-subjek_reply_to="' . $row->subjek_reply_to . '" data-reply_to="' . $row->reply_to . '" data-port="' . $row->port . '"> <i class="fas fa-edit me-1"></i> Edit</button>';
                $button .= '</div>';
                return $button;
            })
            ->rawColumns(['nama', 'subjek_reply_to', 'action', 'email_receiver'])
            ->make(true);
    }

    public function store_setting(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'nama' => 'required',
            'user' => 'required',
            'email_receiver' => 'required',
        ]);

        DB::beginTransaction();
        try {

            $data = [
                'email' => $request->email != null ? $request->email : '',
                'pass' => $request->pass != null ? $request->pass : '',
                'host' => $request->host != null ? $request->host : '',
                'port' => $request->port != null ? $request->port : '',
                'nama' => $request->nama != null ? $request->nama : '',
                'user' => $request->user != null ? $request->user : '',
                'reply_to' => $request->reply_to != null ? $request->reply_to : '',
                'subjek_reply_to' => $request->subjek_reply_to != null ? $request->subjek_reply_to : '',
                'email_receiver' => $request->email_receiver != null ? $request->email_receiver : '',
            ];

            $insert = MailSetting::updateOrCreate(['id_setting' => $request->id_setting], $data);

            DB::commit();
            if ($insert) {
                return response()->json(['status' => true, 'message' => "Data berhasil disimpan"], 200);
            } else {
                return response()->json(['status' => false, 'message' => "Data gagal disimpan"], 200);
            }
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function switch_status(Request $request)
    {
        DB::beginTransaction();
        try {
            MailSetting::query()->update(['is_active' => '0']);
            $data = [];
            $data['is_active'] = '1';

            if ($request->status == '1') {
                MailSetting::where('id_setting', $request->id_setting)->update($data);
            } else {
                MailSetting::whereNot('id_setting', $request->id_setting)->update($data);
            }


            DB::commit();
            return response()->json(['status' => true, 'message' => "Data berhasil disimpan"], 200);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // public function isi_pesan()
    // {
    //     $mail = MailSetting::where('is_active', '1')->first();

    //     $subjek = [
    //         $mail->subjek_exp,
    //         $mail->subjek_data_kerma,
    //         $mail->subjek_prod_kerma,
    //         $mail->subjek_ajuan,
    //         $mail->subjek_lapor,
    //         $mail->subjek_edit_ajuan,
    //         $mail->subjek_edit_lapor,
    //         $mail->subjek_upload_mou,
    //         $mail->subjek_draft,
    //         $mail->subjek_kuesioner,
    //         $mail->subjek_chat_notif,
    //         $mail->subjek_chat_notif_to_adm,
    //         $mail->subjek_perubahan_draft,
    //         $mail->subjek_verifikasi,
    //         $mail->subjek_verifikasi_implementasi,
    //         $mail->subjek_mou,
    //         $mail->subjek_pic_kegiatan,
    //         $mail->subjek_kirim_pic_pengajuan,
    //     ];

    //     $isiPesan = [
    //         $mail->rmd_exp_kerma,
    //         $mail->rmd_data_kerma,
    //         $mail->rmd_prod_kerma,
    //         $mail->ajuan_email,
    //         $mail->lapor_email,
    //         $mail->ajuan_edit_email,
    //         $mail->lapor_edit_email,
    //         $mail->upload_mou,
    //         $mail->draft_email,
    //         $mail->kuesioner_email,
    //         $mail->chat_notif_email,
    //         $mail->chat_notif_email_to_adm,
    //         $mail->draft_perubahan_draft,
    //         $mail->verifikasi,
    //         $mail->verifikasi_implementasi,
    //         $mail->dokumen_mou,
    //         $mail->pic_kegiatan,
    //         $mail->kirim_pic_pengajuan,
    //     ];

    //     $fields = [
    //         'rmd_exp_kerma',
    //         'rmd_data_kerma',
    //         'rmd_prod_kerma',
    //         'ajuan_email',
    //         'lapor_email',
    //         'ajuan_edit_email',
    //         'lapor_edit_email',
    //         'upload_mou',
    //         'draft_email',
    //         'kuesioner_email',
    //         'chat_notif_email',
    //         'chat_notif_email_to_adm',
    //         'draft_perubahan_draft',
    //         'verifikasi',
    //         'verifikasi_implementasi',
    //         'dokumen_mou',
    //         'pic_kegiatan',
    //         'kirim_pic_pengajuan',
    //     ];

    //     $data = [
    //         'li_active' => 'mail',
    //         'li_sub_active' => 'mail_settings',
    //         'title' => 'Setting Tampilan Pesan',
    //         'page_title' => 'Setting Tampilan Pesan',
    //         'dataSubjek' => $subjek,
    //         'dataIsiPesan' => $isiPesan,
    //         'fields' => $fields,
    //     ];

    //     return view('admin/mail/isi_pesan', $data);
    // }
    public function isi_pesan()
    {
        $mail = MailSetting::where('is_active', '1')->first();

        $mailMessages = MailMessages::all()->keyBy('jenis');
        // foreach ($mailMessages as $key => $value) {
        //     return $key;
        //     return $value['subjek'];
        // }

        // $subjek = [
        //     $mail->subjek_exp,
        //     $mail->subjek_data_kerma,
        //     $mail->subjek_prod_kerma,
        //     $mail->subjek_ajuan,
        //     $mail->subjek_lapor,
        //     $mail->subjek_edit_ajuan,
        //     $mail->subjek_edit_lapor,
        //     $mail->subjek_upload_mou,
        //     $mail->subjek_draft,
        //     $mail->subjek_kuesioner,
        //     $mail->subjek_chat_notif,
        //     $mail->subjek_chat_notif_to_adm,
        //     $mail->subjek_perubahan_draft,
        //     $mail->subjek_verifikasi,
        //     $mail->subjek_verifikasi_implementasi,
        //     $mail->subjek_mou,
        //     $mail->subjek_pic_kegiatan,
        //     $mail->subjek_kirim_pic_pengajuan,
        // ];

        // $isiPesan = [
        //     $mail->rmd_exp_kerma,
        //     $mail->rmd_data_kerma,
        //     $mail->rmd_prod_kerma,
        //     $mail->ajuan_email,
        //     $mail->lapor_email,
        //     $mail->ajuan_edit_email,
        //     $mail->lapor_edit_email,
        //     $mail->upload_mou,
        //     $mail->draft_email,
        //     $mail->kuesioner_email,
        //     $mail->chat_notif_email,
        //     $mail->chat_notif_email_to_adm,
        //     $mail->draft_perubahan_draft,
        //     $mail->verifikasi,
        //     $mail->verifikasi_implementasi,
        //     $mail->dokumen_mou,
        //     $mail->pic_kegiatan,
        //     $mail->kirim_pic_pengajuan,
        // ];

        // $fields = [
        //     'rmd_exp_kerma',
        //     'rmd_data_kerma',
        //     'rmd_prod_kerma',
        //     'ajuan_email',
        //     'lapor_email',
        //     'ajuan_edit_email',
        //     'lapor_edit_email',
        //     'upload_mou',
        //     'draft_email',
        //     'kuesioner_email',
        //     'chat_notif_email',
        //     'chat_notif_email_to_adm',
        //     'draft_perubahan_draft',
        //     'verifikasi',
        //     'verifikasi_implementasi',
        //     'dokumen_mou',
        //     'pic_kegiatan',
        //     'kirim_pic_pengajuan',
        // ];

        $data = [
            'li_active' => 'mail',
            'li_sub_active' => 'mail_settings',
            'title' => 'Setting Tampilan Pesan',
            'page_title' => 'Setting Tampilan Pesan',
            // 'dataSubjek' => $subjek,
            // 'dataIsiPesan' => $isiPesan,
            // 'fields' => $fields,
            'mailMessages' => $mailMessages
        ];

        return view('admin/mail/isi_pesan', $data);
    }

    // public function store_isi_pesan_old(Request $request)
    // {
    //     try {
    //         $field = $request->field;
    //         $content = $request->content;
    //         $subjek = $request->subjek;

    //         $allowedFields = [
    //             'rmd_exp_kerma',
    //             'rmd_data_kerma',
    //             'rmd_prod_kerma',
    //             'ajuan_email',
    //             'lapor_email',
    //             'ajuan_edit_email',
    //             'lapor_edit_email',
    //             'upload_mou',
    //             'draft_email',
    //             'kuesioner_email',
    //             'chat_notif_email',
    //             'chat_notif_email_to_adm',
    //             'draft_perubahan_draft',
    //             'verifikasi',
    //             'verifikasi_implementasi',
    //             'dokumen_mou',
    //             'pic_kegiatan',
    //             'kirim_pic_pengajuan',
    //         ];

    //         if (!in_array($field, $allowedFields)) {
    //             return response()->json(['status' => false, 'message' => 'Field tidak diizinkan.'], 400);
    //         }

    //         $mailSetting = MailSetting::all();
    //         foreach ($mailSetting as $dataMail) {
    //             $dataMail->$field = $content;

    //             // Simpan subjek juga (cari subjek yang berpasangan)
    //             switch ($field) {
    //                 case 'rmd_exp_kerma':
    //                     $dataMail->subjek_exp = $subjek;
    //                     break;
    //                 case 'rmd_data_kerma':
    //                     $dataMail->subjek_data_kerma = $subjek;
    //                     break;
    //                 case 'rmd_prod_kerma':
    //                     $dataMail->subjek_prod_kerma = $subjek;
    //                     break;
    //                 case 'ajuan_email':
    //                     $dataMail->subjek_ajuan = $subjek;
    //                     break;
    //                 case 'lapor_email':
    //                     $dataMail->subjek_lapor = $subjek;
    //                     break;
    //                 case 'ajuan_edit_email':
    //                     $dataMail->subjek_edit_ajuan = $subjek;
    //                     break;
    //                 case 'lapor_edit_email':
    //                     $dataMail->subjek_edit_lapor = $subjek;
    //                     break;
    //                 case 'upload_mou':
    //                     $dataMail->subjek_upload_mou = $subjek;
    //                     break;
    //                 case 'draft_email':
    //                     $dataMail->subjek_draft = $subjek;
    //                     break;
    //                 case 'kuesioner_email':
    //                     $dataMail->subjek_kuesioner = $subjek;
    //                     break;
    //                 case 'chat_notif_email':
    //                     $dataMail->subjek_chat_notif = $subjek;
    //                     break;
    //                 case 'chat_notif_email_to_adm':
    //                     $dataMail->subjek_chat_notif_to_adm = $subjek;
    //                     break;
    //                 case 'draft_perubahan_draft':
    //                     $dataMail->subjek_perubahan_draft = $subjek;
    //                     break;
    //                 case 'verifikasi':
    //                     $dataMail->subjek_verifikasi = $subjek;
    //                     break;
    //                 case 'verifikasi_implementasi':
    //                     $dataMail->subjek_verifikasi_implementasi = $subjek;
    //                     break;
    //                 case 'dokumen_mou':
    //                     $dataMail->subjek_mou = $subjek;
    //                     break;
    //                 case 'pic_kegiatan':
    //                     $dataMail->subjek_pic_kegiatan = $subjek;
    //                     break;
    //                 case 'kirim_pic_pengajuan':
    //                     $dataMail->subjek_kirim_pic_pengajuan = $subjek;
    //                     break;
    //             }

    //             $dataMail->save();
    //         }

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Data berhasil diupdate.'
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Gagal: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function store_isi_pesan(Request $request)
    {
        DB::beginTransaction();
        try {
            $field = $request->field;
            $content = $request->content;
            $subjek = $request->subjek;

            // return $request->all();
            // $allowedFields = [
            //     'rmd_exp_kerma',
            //     'rmd_data_kerma',
            //     'rmd_prod_kerma',
            //     'ajuan_email',
            //     'lapor_email',
            //     'ajuan_edit_email',
            //     'lapor_edit_email',
            //     'upload_mou',
            //     'draft_email',
            //     'kuesioner_email',
            //     'chat_notif_email',
            //     'chat_notif_email_to_adm',
            //     'draft_perubahan_draft',
            //     'verifikasi',
            //     'verifikasi_implementasi',
            //     'dokumen_mou',
            //     'pic_kegiatan',
            //     'kirim_pic_pengajuan',
            // ];

            // if (!in_array($field, $allowedFields)) {
            //     return response()->json(['status' => false, 'message' => 'Field tidak diizinkan.'], 400);
            // }

            $mailMessages = MailMessages::where('jenis', $field)->first();
            if ($mailMessages) {
                $mailMessages->pesan = $content;
                $mailMessages->subjek = $subjek;
                $mailMessages->save();
            }

            // $mailSetting = MailSetting::all();
            // foreach ($mailSetting as $dataMail) {
            //     $dataMail->$field = $content;

            //     // Simpan subjek juga (cari subjek yang berpasangan)
            //     switch ($field) {
            //         case 'rmd_exp_kerma':
            //             $dataMail->subjek_exp = $subjek;
            //             break;
            //         case 'rmd_data_kerma':
            //             $dataMail->subjek_data_kerma = $subjek;
            //             break;
            //         case 'rmd_prod_kerma':
            //             $dataMail->subjek_prod_kerma = $subjek;
            //             break;
            //         case 'ajuan_email':
            //             $dataMail->subjek_ajuan = $subjek;
            //             break;
            //         case 'lapor_email':
            //             $dataMail->subjek_lapor = $subjek;
            //             break;
            //         case 'ajuan_edit_email':
            //             $dataMail->subjek_edit_ajuan = $subjek;
            //             break;
            //         case 'lapor_edit_email':
            //             $dataMail->subjek_edit_lapor = $subjek;
            //             break;
            //         case 'upload_mou':
            //             $dataMail->subjek_upload_mou = $subjek;
            //             break;
            //         case 'draft_email':
            //             $dataMail->subjek_draft = $subjek;
            //             break;
            //         case 'kuesioner_email':
            //             $dataMail->subjek_kuesioner = $subjek;
            //             break;
            //         case 'chat_notif_email':
            //             $dataMail->subjek_chat_notif = $subjek;
            //             break;
            //         case 'chat_notif_email_to_adm':
            //             $dataMail->subjek_chat_notif_to_adm = $subjek;
            //             break;
            //         case 'draft_perubahan_draft':
            //             $dataMail->subjek_perubahan_draft = $subjek;
            //             break;
            //         case 'verifikasi':
            //             $dataMail->subjek_verifikasi = $subjek;
            //             break;
            //         case 'verifikasi_implementasi':
            //             $dataMail->subjek_verifikasi_implementasi = $subjek;
            //             break;
            //         case 'dokumen_mou':
            //             $dataMail->subjek_mou = $subjek;
            //             break;
            //         case 'pic_kegiatan':
            //             $dataMail->subjek_pic_kegiatan = $subjek;
            //             break;
            //         case 'kirim_pic_pengajuan':
            //             $dataMail->subjek_kirim_pic_pengajuan = $subjek;
            //             break;
            //     }

            //     $dataMail->save();
            // }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diupdate.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatusPesan(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'index' => 'required',
                'status' => 'required|in:0,1',
            ]);

            $index = $request->index;
            $status = $request->status;

            $mailMessages = MailMessages::where('jenis', $index)->first();

            $mailMessages->status = $status;
            $mailMessages->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // public function saveMailMessagesFromSetting()
    // {
    //     DB::transaction(function () {

    //         $mail = MailSetting::where('is_active', 1)->first();

    //         if (!$mail) {
    //             throw new \Exception('Mail setting aktif tidak ditemukan');
    //         }

    //         $mapping = [
    //             'subjek_exp' => ['subjek_exp', 'rmd_exp_kerma'],
    //             'subjek_data_kerma' => ['subjek_data_kerma', 'rmd_data_kerma'],
    //             'subjek_prod_kerma' => ['subjek_prod_kerma', 'rmd_prod_kerma'],
    //             'subjek_ajuan' => ['subjek_ajuan', 'ajuan_email'],
    //             'subjek_lapor' => ['subjek_lapor', 'lapor_email'],
    //             'subjek_edit_ajuan' => ['subjek_edit_ajuan', 'ajuan_edit_email'],
    //             'subjek_edit_lapor' => ['subjek_edit_lapor', 'lapor_edit_email'],
    //             'subjek_upload_mou' => ['subjek_upload_mou', 'upload_mou'],
    //             'subjek_draft' => ['subjek_draft', 'draft_email'],
    //             'subjek_kuesioner' => ['subjek_kuesioner', 'kuesioner_email'],
    //             'subjek_chat_notif' => ['subjek_chat_notif', 'chat_notif_email'],
    //             'subjek_chat_notif_to_adm' => ['subjek_chat_notif_to_adm', 'chat_notif_email_to_adm'],
    //             'subjek_perubahan_draft' => ['subjek_perubahan_draft', 'draft_perubahan_draft'],
    //             'subjek_verifikasi' => ['subjek_verifikasi', 'verifikasi'],
    //             'subjek_verifikasi_implementasi' => ['subjek_verifikasi_implementasi', 'verifikasi_implementasi'],
    //             'subjek_mou' => ['subjek_mou', 'dokumen_mou'],
    //             'subjek_pic_kegiatan' => ['subjek_pic_kegiatan', 'pic_kegiatan'],
    //             'subjek_kirim_pic_pengajuan' => ['subjek_kirim_pic_pengajuan', 'kirim_pic_pengajuan'],
    //         ];

    //         $dataInsert = [];

    //         foreach ($mapping as $jenis => [$subjekField, $pesanField]) {
    //             if (empty($mail->$subjekField) && empty($mail->$pesanField)) {
    //                 continue;
    //             }

    //             $dataInsert[] = [
    //                 'jenis'      => $jenis,
    //                 'subjek'     => $mail->$subjekField,
    //                 'pesan'      => $mail->$pesanField,
    //                 'status'     => 1,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ];
    //         }

    //         MailMessages::insert($dataInsert);
    //     });
    // }
}
