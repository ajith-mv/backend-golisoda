<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComboProductController extends Controller
{
    public function index(Request $request)
    {
        $title              = "Product Combo Collection";
        $breadCrum          = array('Products', 'Product Combo Collections');
        if($request->ajax())
        {
            return 1;
        }
        return view('platform.combo_product_collection.index', compact('title','breadCrum'));


    }
}
