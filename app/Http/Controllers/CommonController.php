<?php

namespace App\Http\Controllers;

use App\Models\Category\MainCategory;
use App\Models\Master\Brands;
use App\Models\Product\ProductCategory;
use App\Models\Warranty;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function getProductCategoryList(Request $request)
    {
        $category_id            = $request->id;
        $productCategory        = ProductCategory::where('status', 'published')->get();
        return view('platform.product.form.parts._category', compact('productCategory', 'category_id'));
    }

    public function getProductBrandList(Request $request)
    {
        $brand_id      = $request->id;
        $brands        = Brands::where('status', 'published')->get();
        return view('platform.product.form.parts._brand', compact('brands', 'brand_id'));
    }

    public function getProductWarrantyList(Request $request)
    {
        $warranty_id = $request->id;
        $warranties = Warranty::where('status', 'published')->get();
        return view('platform.product.form.parts._warranty', compact('warranties', 'warranty_id'));
    }

    public function getProductDynamicList(Request $request)
    {
        
        $tag                = $request->tag;
        $category           = MainCategory::where('status', 'published')->where('slug', $tag)->first();
        if( $tag == 'product-labels') {
            $productLabels  = $category;
            $label_id       = $request->id;
            return view('platform.product.form.parts._labels', compact('productLabels', 'label_id'));
        } else {
            $productTags    = $category;
            $tag_id         = $request->id;
            return view('platform.product.form.parts._tags', compact('productTags', 'tag_id'));
        }
    }
}
