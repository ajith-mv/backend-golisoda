<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Models\Master\Variation;
use App\Models\Master\VariationGroup;

class ProductVariationController extends Controller
{
    public function getAttributeRow(Request $request)
    {
        if (!$request->category_id) {
            return response()->json([
                "status" => false,
                "message"   => "Please select a category .",
                "error"   => "Category not selected."
            ], 400);
        }

        $category_id             = $request->category_id;
        $product_id             = $request->product_id;
        $info                   = Product::find($product_id);
        $attributes             = '';
        $variationids = [];
        
        $variationGroupdata = VariationGroup::where('status', 1)
            ->whereJsonContains('category_id', $category_id)
            ->get();
        if ($variationGroupdata) {
            foreach ($variationGroupdata as $group) {
                $variationids[] = json_decode($group->variation_id, true);
            }
            $variations = Variation::whereIn('id', array_values(array_unique(array_merge(...$variationids))))->get();
            return view('platform.product.form.variation._items', compact('attributes', 'info', 'variations'));
        } else {
            return response()->json([
                "status" => false,
                "message"   => "No Variation selected this category  .",
                "error"   => "No Variation selected this category  ."
            ], 400);
        }
    }

    public function getvariationvalue(Request $request)
    {
        $variationId = $request->variation;
        $variation_value = $request->variation_value;
        $variation = Variation::find($variationId);
        $values = json_decode($variation->value, true);
        return response()->json([
            'values' => $values
        ]);
    }
}
