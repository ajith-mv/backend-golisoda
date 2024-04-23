<?php

namespace App\Exports;


use App\Models\Product\ProductCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class ProductCategoryExport implements FromView
{
    public function view(): View
    {
        $list = ProductCategory::all();
        
        return view('platform.exports.product.product_category_excel', compact('list'));
    }
}
