<?php

namespace App\Exports;

use App\Models\Product\Product;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class RelatedProductAttributeSetExport implements FromView
{
    public function view(): View
    {
        $list = Product::with('productMeta','productRelated.Product','productCrossSale.product')->get();
        return view('platform.exports.product.related_product_attribute_excel', compact('list'));
    }
}
