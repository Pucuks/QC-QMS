<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GooglePieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fruit = Document::all();
        $veg =  Document::whereDate('end_date', '<=', Carbon::now())->count()->get();
        $grains = Document::whereDate('end_date', '>', Carbon::now())->count()->get();
        $fruit_count = count($fruit);
        $veg_count = count($veg);
        $grains_count = count($grains);

        // return view('home', compact('fruit_count', 'veg_count', 'grains_count'));
    }
}
