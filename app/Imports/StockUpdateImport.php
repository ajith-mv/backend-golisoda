<?php

namespace App\Imports;

use App\Models\Product\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockUpdateImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {

        $sku = $row['sku'];
        $mop_price  = $row['mop_price'];
        $quantity  = $row['quantity'];
        $stock_status = $row['stock_status'];


        $product_info = Product::where('sku', $sku)->first();
        
        if($product_info){
            $base_price_info = getAmountExclusiveTax( $mop_price, $product_info->tax->pecentage);
            $base_price = $base_price_info['basePrice'];

            $ins['discount_percentage'] = 
            $product_info->discount_percentage = getDiscountPercentage(round($mop_price ?? 0), round($product_info->mrp));
            $product_info->price = $base_price;
            $product_info->mrp = $mop_price;
            $product_info->quantity = $quantity;
            if( $stock_status ) {
                $product_info->stock_status = $stock_status;
            }
            
            $product_info->save();
            
        }

    }
}
