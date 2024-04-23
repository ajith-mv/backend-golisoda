<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Product\ProductWithAttributeSet;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index(Request $request)
    {
        
        $product_ids = $request->product_id;
        $compare = [];
        if( $product_ids ) {
            // $product_ids = explode(',', $product_id);
            $usedAttributeHeads = ProductWithAttributeSet::whereIn('product_id', $product_ids)
                                    ->where('status', 'published')
                                    ->groupBy('title')
                                    ->orderBy('order_by')
                                    ->get();
            // dd( $usedAttributeHeads );
            $datas = Product::whereIn('id', $product_ids)->get();
          
            if( isset( $datas ) && !empty($datas)){
                foreach ($datas as $item ) {
                    $tmp[] = getProductCompareApiData($item);
                    $compare['products'] = $tmp;
                }
            }
            
            $attributes = [];
            if( isset( $usedAttributeHeads ) && !empty( $usedAttributeHeads ) ) {
                foreach ($usedAttributeHeads as $pro_items) {
                    $each_row_attr = [];
                    for ($i=0; $i < count($compare['products']); $i++) { 
                        
                        $attributes_datas = ProductWithAttributeSet::where('product_id', $compare['products'][$i]['id'])
                                        ->where('status', 'published')
                                        ->where('title', $pro_items->title)
                                        ->first();
                        if( $attributes_datas ) {
                            $each_row_attr[] = array(
                                                    'product' => $compare['products'][$i], 
                                                    'value' =>$attributes_datas->attribute_values                                                     
                                                );
                        } else {
                            $each_row_attr[] = array('product' => $compare['products'][$i], 'value' => '-' );
                        }
                        
                    }
                    
                    $attributes[$pro_items->title] = $each_row_attr;
                }
                $compare['informations'] = $attributes;
            }
            
            $error = 0;
            $message = 'Success';
        } else {
            $error = '1';
            $message = 'Product data is required';
        }
        return array('error' => $error, 'message' => $message, 'data' => $compare);
    }
}
