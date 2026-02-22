<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\PengajuanKerjaSama;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Jobs\SendEmailChat;
use App\Jobs\SendEmailMailer;
use App\Jobs\SendEmailMailerChat;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Mail\NewMessageNotification;
use App\Models\MailMessages;
use App\Models\MailSetting;
use App\Models\RefLembagaUMS;
use Illuminate\Support\Facades\Mail;

class ChatController extends Controller
{
    public function index($id_mou, $sender = null)
    {
        $role = session('current_role');
        $dataPengajuan = PengajuanKerjaSama::where('id_mou', $id_mou)->first();

        // $getVerifikator = User::where('place_state', $dataPengajuan->place_state)
        $getVerifikator = User::where('status_tempat', $dataPengajuan->status_tempat)
            ->whereIn('jabatan', ['Kaprodi', 'Dekan', 'Direktur', 'Kepala', 'Ketua'])
            ->pluck('username')
            ->toArray();

        $lembagaOld = RefLembagaUMS::where('nama_lmbg_old', $dataPengajuan->status_tempat)->first()->nama_lmbg ?? '';
        if (count($getVerifikator) == 0) {
            $getVerifikator = User::where('status_tempat', $lembagaOld)
                ->whereIn('jabatan', ['Kaprodi', 'Dekan', 'Direktur', 'Kepala', 'Ketua'])
                ->pluck('username')
                ->toArray();
        }

        if (count($getVerifikator) == 0) {
            $getVerifikator = User::where('status_tempat', $dataPengajuan->status_tempat)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'verifikator');
                })
                // ->whereIn('jabatan', ['Kaprodi'])
                ->pluck('username')
                ->toArray();
        }

        $mail = MailSetting::where('is_active', '0')->first();
        $AdminDefault = ['admin_bkui'];
        $trimAdmin = str_replace(' ', '', $mail->email_receiver);
        $arrAdmin = explode(',', $trimAdmin);
        $getAdmin = array_merge($AdminDefault, $arrAdmin);

        $getPengusul = User::where('username', $dataPengajuan->add_by)
            ->pluck('username')
            ->toArray();

        if ($role == 'admin') {
            $arrUser = array_merge($getVerifikator, $getPengusul);
        } else if ($role == 'verifikator') {
            $arrUser = array_merge($getAdmin, $getPengusul);
        } else if ($role == 'user') {
            $arrUser = array_merge($getAdmin, $getVerifikator);
        } else {
            $arrUser = [];
        }

        $queryUsers = User::whereIn('username', $arrUser);
        if ($role != 'admin') {
            $queryUsers->orwhereIn('email', $arrAdmin);
        }
        $queryUsers->with(['roles:id,name', 'getJabatan', 'getChat' => function ($query) use ($id_mou) {
            $query->where('is_seen', '0')
                ->where('receiver_id', auth()->user()->id)
                ->where('id_mou', $id_mou);
        }])
            ->orderByDesc(function ($query) {
                $query->selectRaw('count(*)')
                    ->from('chats')
                    ->whereColumn('chats.sender_id', 'users.id');
            });
        $users = $queryUsers->get();
        $data = [
            'title' => 'Dokumen Kerja Sama ' . $dataPengajuan->nama_institusi,
            'page_title' => 'Dokumen Kerja Sama ' . $dataPengajuan->nama_institusi,
            'dataPengajuan' => $dataPengajuan,
            'users' => $users,
            'id_mou' => $id_mou,
        ];
        $data['sender'] = $sender;

        return view('chat.index', $data);
    }

    public function loadMessages($id_mou, $receiver_id = null)
    {
        if ($receiver_id) {
            $messages = Chat::where('id_mou', $id_mou)
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

            Chat::where('id_mou', $id_mou)
                ->where('receiver_id', Auth::id())
                ->where('sender_id', $receiver_id)
                ->where('is_seen', false)
                ->orwhereNull('is_seen')
                ->update(['is_seen' => true]);
        } else {
            $messages = 0;
        }

        $view = view('chat.load_message', compact('messages', 'receiver_id'))->render();


        return response()->json($view);
    }

    // Mengirim pesan
    public function sendMessage(Request $request)
    {
        $message = Chat::create([
            'id_mou' => $request->id_mou,
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_seen' => false,
        ]);

        $mailMessages = MailMessages::where('jenis', 'subjek_chat_notif')->first();

        return response()->json([
            'status' => true,
            'message' => $message,
            'sendmail' => $mailMessages->status == '1' ? true : false,
        ]);
        // return response()->json($message);
    }

    public function sendMail(Request $request)
    {
        try {

            // Ambil data pengirim & penerima
            $sender = Auth::user();
            $receiver = User::find($request->receiver_id);
            $dataMessage = Chat::where('id', $request->id_chat)->with(['sender', 'receiver'])->first();
            $dataEmail = PengajuanKerjaSama::where('id_mou', $dataMessage->id_mou)->first()->toArray();
            $nama_institusi = $dataEmail['nama_institusi'];

            $mail = MailSetting::where('is_active', '1')->first();
            $role = session('current_role');

            // if ($role == 'admin') {
            // $title = $mail->subjek_chat_notif;
            // $viewEmail = $mail->chat_notif_email;

            $mailMessages = MailMessages::where('jenis', 'subjek_chat_notif')->first();
            $title = $mailMessages->subjek;
            $viewEmail = $mailMessages->pesan;

            if ($mailMessages->status == '1') {
                $arrReceiver = [$receiver->email];
                foreach ($arrReceiver as $email) {
                    $message = (string) str_replace("{@nama_institusi}", $nama_institusi, $viewEmail);
                    $nama_receiver = User::where('email', $email)->first()->name;
                    $message = view('emails.new_message', [
                        'nama_receiver' => $nama_receiver ?? 'Pengguna',
                        'nama_sender' => Auth::user()->name,
                        'judulPesan' => 'Pesan Baru',
                        'message' => $message,
                        'chat' => $dataMessage->message,
                        'url_chat' => url('chat/index/' . $dataMessage->id_mou . '/' . $dataMessage->sender_id)
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
                    // \Artisan::call('queue:work', [
                    //     '--once' => true,
                    //     '--delay' => 0,
                    // ]);
                }
                // Berhasil kirim email
                return response()->json([
                    'status' => true,
                    'message' => 'Email berhasil dikirim ke ' . $receiver->email . '!'
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Email berhasil dikirim ke ' . $receiver->email . '!'
                ]);
            }
            // SendEmailChat::dispatchSync($sender, $message, $receiver, session('environment'));
            // Kirim email


        } catch (\Exception $e) {
            // Gagal mengirim email
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage()
            ], 500);
        }
    }
}
