<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailChatRekognisi;
use App\Jobs\SendEmailMailer;
use App\Models\Chat;
use App\Models\PengajuanKerjaSama;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ChatRekognisi;
use App\Models\MailSetting;
use App\Models\Recognition;
use App\Models\RefLembagaUMS;
use Illuminate\Support\Facades\Mail;

class ChatRekognisiController extends Controller
{
    public function index($id_rec, $sender = null)
    {
        $role = session('current_role');
        $dataRekognisi = Recognition::where('id_rec', $id_rec)->first();
        $mail = MailSetting::where('is_active', '1')->first();
        $AdminDefault = ['admin_bkui'];
        $arrAdmin = explode(',', $mail->email_receiver);
        $getAdmin = array_merge($AdminDefault, $arrAdmin);
        $getPengusul = User::where('username', $dataRekognisi->add_by)
            ->pluck('username')
            ->toArray();

        $getVerifikator = User::where('status_tempat', $dataRekognisi->department)
            ->whereIn('jabatan', ['Kaprodi', 'Dekan', 'Direktur', 'Kepala', 'Ketua'])
            ->pluck('username')
            ->toArray();

        $lembagaOld = RefLembagaUMS::where('nama_lmbg_old', $dataRekognisi->department)->first()->nama_lmbg ?? '';
        if (count($getVerifikator) == 0) {
            $getVerifikator = User::where('status_tempat', $lembagaOld)
                ->whereIn('jabatan', ['Kaprodi', 'Dekan', 'Direktur', 'Kepala', 'Ketua'])
                ->pluck('username')
                ->toArray();
        }

        if (count($getVerifikator) == 0) {
            $getVerifikator = User::where('status_tempat', $dataRekognisi->department)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'verifikator');
                })
                ->pluck('username')
                ->toArray();
        }


        if ($role == 'admin') {
            $arrUser = array_merge($getPengusul, $getVerifikator);
        } else if ($role == 'verifikator') {
            if ($getPengusul[0] == Auth::user()->username) {
                $getPengusul = [];
            }
            $arrUser = array_merge($getPengusul, $getAdmin);
        } else if ($role == 'user') {
            $arrUser = array_merge($getAdmin, $getVerifikator);
        } else {
            $arrUser = [];
        }

        $queryUsers = User::whereIn('username', $arrUser);
        // if ($role != 'admin') {
        //     $queryUsers->orwhereIn('email', $arrAdmin);
        // }
        $queryUsers->with(['roles:id,name', 'getJabatan', 'getChatRekognisi' => function ($query) use ($id_rec) {
            $query->where('is_seen', '0')
                ->where('receiver_id', auth()->user()->id)
                ->where('id_rec', $id_rec);
        }])
            ->orderByDesc(function ($query) {
                $query->selectRaw('count(*)')
                    ->from('chats_rekognisi')
                    ->whereColumn('chats_rekognisi.sender_id', 'users.id');
            });

        $users = $queryUsers->get();
        // dd($arrAdmin);
        // dd($arrUser);
        $data = [
            'title' => 'Chat Ajuan Rekognisi',
            'page_title' => 'Chat Ajuan Rekognisi',
            'dataRekognisi' => $dataRekognisi,
            'users' => $users,
            'id_rec' => $id_rec,
        ];
        $data['sender'] = $sender;

        return view('chat_rekognisi.index', $data);
    }

    // Mengambil pesan antara user yang sedang login dan penerima
    public function loadMessages($id_rec, $receiver_id = null)
    {
        if ($receiver_id) {
            $messages = ChatRekognisi::where('id_rec', $id_rec)
                ->where(function ($query) use ($receiver_id) {
                    $query->where(function ($subQuery) use ($receiver_id) {
                        $subQuery->where('sender_id', Auth::id())
                            ->where('receiver_id', $receiver_id);
                    })->orWhere(function ($subQuery) use ($receiver_id) {
                        $subQuery->where('sender_id', $receiver_id)
                            ->where('receiver_id', Auth::id());
                    });
                })
                ->orderBy('created_at')
                ->get();

            ChatRekognisi::where('id_rec', $id_rec)
                ->where('receiver_id', Auth::id()) // Hanya pesan yang diterima oleh user yang login
                ->where('sender_id', $receiver_id) // Dari pengirim yang sedang dilihat
                ->where('is_seen', false) // Hanya yang belum terbaca
                ->orwhereNull('is_seen') // Hanya yang belum terbaca
                ->update(['is_seen' => true]);
        } else {
            $messages = 0;
        }

        $view = view('chat_rekognisi.load_message', compact('messages', 'receiver_id'))->render();


        return response()->json($view);
    }

    // Mengirim pesan
    public function sendMessage(Request $request)
    {
        $message = ChatRekognisi::create([
            'id_rec' => $request->id_rec,
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_seen' => false,
        ]);

        return response()->json($message);
    }

    public function sendMail(Request $request)
    {
        try {

            // Ambil data pengirim & penerima
            $sender = Auth::user();
            $receiver = User::find($request->receiver_id);
            // $message = ChatRekognisi::where('id', $request->id_chat)->with(['sender', 'receiver'])->first();
            $dataMessage = ChatRekognisi::where('id', $request->id_chat)->with(['sender', 'receiver'])->first();
            $dataEmail = Recognition::where('id_rec', $dataMessage->id_rec)->first()->toArray();
            $nama_institusi = $dataEmail['department'];
            $nama_prof = $dataEmail['nama_prof'];

            $mail = MailSetting::where('is_active', '1')->first();
            $role = session('current_role');

            // if ($role == 'admin') {
            $title = str_replace('Kerja Sama', 'Rekognisi', $mail->subjek_chat_notif);
            $arrReceiver = [$receiver->email];
            // } else {
            //     $arrReceiver = array_map('trim', explode(',', $mail->email_receiver));
            //     $title = str_replace('Kerja Sama', 'Rekognisi', $mail->subjek_chat_notif_to_adm);
            // }

            // dd($arrReceiver);

            // $message = (string) str_replace("{@nama_institusi}", $nama_institusi, $viewEmail);

            foreach ($arrReceiver as $email) {
                $nama_receiver = User::where('email', $email)->first()->name;
                $message = view('emails.new_message_recognisi', [
                    'nama_receiver' => $nama_receiver ?? 'Pengguna',
                    'nama_sender' => Auth::user()->name,
                    'nama_prof' => $nama_prof ?? '',
                    'department' => $nama_institusi ?? '',
                    'judulPesan' => 'Pesan Baru Rekognisi',
                    'chat' => $dataMessage->message,
                    'url_chat' => url('chatRecognisi/index/' . $dataMessage->id_rec . '/' . $dataMessage->sender_id)
                ])->render();

                $dataSendMail = [
                    'message' => $message,
                    'title' => $title,
                    'institusi' => $nama_institusi,
                    'session' => session('environment'),
                    'sender' => Auth::user()->username,
                    'MailSetting' => $mail->toArray(),
                    'receiver' => $email
                ];
                SendEmailMailer::dispatchSync($dataSendMail);
            }

            // SendEmailChatRekognisi::dispatchSync($sender, $message, $receiver, session('environment'));
            // Kirim email

            // Berhasil kirim email
            return response()->json([
                'status' => true,
                'message' => 'Email berhasil dikirim ke ' . $receiver->email . '!'
            ]);
        } catch (\Exception $e) {
            // Gagal mengirim email
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage()
            ], 500);
        }
    }
}
