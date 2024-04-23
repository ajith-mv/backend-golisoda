<?php

namespace App\Exports;

use App\Models\Product\ProductAttributeSet;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use App\Models\Product\Product;
use App\Models\Product\ProductWithAttributeSet;
use Illuminate\Support\Facades\DB;

class ProductAttributeSetBulkExport implements FromView
{
    public function view(): View
    {
       // $list = ProductAttributeSet::all();
       //\DB::connection()->enableQueryLog();
      
       $list=Product::leftJoin('product_with_attribute_sets','products.id','=','product_with_attribute_sets.product_id')
       ->leftJoin('product_attribute_sets','product_attribute_sets.id','=','product_with_attribute_sets.product_attribute_set_id')
       ->select('products.sku as prod_sku','product_attribute_sets.title as att_title',
       'product_attribute_sets.is_searchable as search','product_with_attribute_sets.is_overview as overview',
       'product_with_attribute_sets.title as keys','product_with_attribute_sets.attribute_values as value',
       'product_with_attribute_sets.order_by as order_by_att')->get();
       
     //  $queries = \DB::getQueryLog();

       //dd($queries);
      //echo $list->toSql();
        return view('platform.exports.product.product_attribute_set_excel', compact('list'));
    }
}
