<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $documents = Document::count();
        $documents_end = Document::whereDate('end_date', '<=', Carbon::now())->get()->count();
        $documents_start = Document::whereDate('end_date', '>', Carbon::now())->get()->count();

        return view('home', compact('documents', 'documents_end', 'documents_start'));
    }

    public function echart(): JsonResponse
    {
        $title = '% The Completeness of
        Document';
        $documents_end = Document::whereDate('end_date', '<=', Carbon::now())->get()->count();
        $documents_start = Document::whereDate('end_date', '>', Carbon::now())->get()->count();
        $data = [
            ['Country', 'Indonesia'],
            ['Effective', $documents_start],
            ['Expired', $documents_end]
        ];
        $result = [
            'title' => $title,
            'data' => $data,
        ];

        return response()->json($result);
    }
}
