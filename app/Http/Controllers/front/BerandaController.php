<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\MediaVideoFoto;
use App\Models\Faq;
use App\Models\News;
use App\Models\Question;
use App\Models\Cabor;
use App\Models\Sponsor;
use App\Models\Slider;
use App\Models\Tourist;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index()
    {
        return redirect('login');
        $data = [];
        return view('front/beranda/home', $data);
    }

    // public function kirim(Request $request)
    // {
    //     $request->validate([
    //         'nama' => 'required|string|max:255',
    //         'email' => 'required|string|email|unique:questions,email',
    //         'pertanyaan' => 'required|string',
    //     ]);

    //     $question = new Question();
    //     $question->nama = $request->input('nama');
    //     $question->email = $request->input('email');
    //     $question->pertanyaan = $request->input('pertanyaan');
    //     $question->tgl_pertanyaan = Carbon::now();

    //     $question->save();

    //     return response()->json(['success' => 'Pesan Anda telah terkirim!']);
    // }

    // public function search(Request $request)
    // {
    //     $query = $request->input('query');

    //     if ($query) {
    //         $data_faq = Faq::where('question', 'LIKE', "%{$query}%")->get();
    //         $data_news = News::where('title', 'LIKE', "%{$query}%")
    //             ->orWhere('content', 'LIKE', "%{$query}%")
    //             ->get();
    //         $data_questions = Question::where('pertanyaan', 'LIKE', "%{$query}%")->get();

    //         $dataAll = [
    //             'data_faq' => $data_faq,
    //             'data_news' => $data_news,
    //             'data_questions' => $data_questions,
    //         ];

    //         // dd($dataAll);
    //         return view('front/search_results', $dataAll);
    //     } else {
    //         return redirect('/')->with('error', 'Search query cannot be empty.');
    //     }
    // }
}
