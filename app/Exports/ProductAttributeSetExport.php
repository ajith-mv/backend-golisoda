<?php

namespace App\Exports;

use App\Models\Product\ProductAttributeSet;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ProductAttributeSetExport implements FromView
{
    public function view(): View
    {
        $list = ProductAttributeSet::all();
        return view('platform.exports.product.product_attribute_excel', compact('list'));
    }
}
