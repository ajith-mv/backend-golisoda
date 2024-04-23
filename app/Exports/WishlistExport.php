<?php

namespace App\Exports;

use App\Models\Wishlist;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class WishlistExport implements FromView
{
   
    public function view() :View
    {
        $list = Wishlist::select('wishlists.*','customers.first_name','products.product_name','products.price')
        ->join('customers','customers.id','=','wishlists.customer_id')
        ->leftJoin('products','products.id','=','wishlists.product_id')
        ->get();
        return view('platform.exports.wishlist.excel', compact('list'));
    }
}
