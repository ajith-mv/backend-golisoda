<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuickLink;
use Illuminate\Http\Request;

class QuickLinkController extends Controller
{
    public function index()
    {
        $data = QuickLink::where('status','published')->orderBy('order_by','asc')->get();
        return response()->json(['data'=>$data]); 
    }
}
