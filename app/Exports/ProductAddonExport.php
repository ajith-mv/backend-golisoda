<?php

namespace App\Exports;


use App\Models\Master\Pincode;
use App\Models\ProductAddon;
use App\Models\ProductAddonItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class ProductAddonExport implements FromView
{
    public function view(): View
    {
        $list = ProductAddon::select('product_addons.title','products.product_name','product_addons.description','product_addons.status','product_addons.order_by','product_addons.created_at','users.name as users_name')
        ->join('products', 'products.id', '=', 'product_addons.product_id')
        ->join('users', 'users.id', '=', 'product_addons.added_by')
        ->get();
        return view('platform.exports.product_addon.excel', compact('list'));
    }
}
