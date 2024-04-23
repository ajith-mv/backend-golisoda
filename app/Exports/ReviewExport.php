<?php

namespace App\Exports;


use App\Models\Product\Review;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReviewExport implements FromView
{
    public function view(): View
    {
        $list = Review::select('reviews.*','customers.first_name as customer_name','products.product_name as product_name')
        ->leftJoin('customers','customers.id','=','reviews.customer_id')
        ->leftJoin('products','products.id','=','reviews.product_id')
        ->get();
        return view('platform.exports.customer_review.excel', compact('list'));
    }
}
