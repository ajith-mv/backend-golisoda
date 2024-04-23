<?php

namespace App\Exports;

use App\Models\Product\ProductCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductCollectionExport implements FromView
{
    public function view(): View
    {
        $list = ProductCollection::all();
        
        return view('platform.exports.product.product_collection_excel', compact('list'));
    }
}
