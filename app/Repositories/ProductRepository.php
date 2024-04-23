<?php
namespace App\Repositories;

use App\Models\Product\Product;
use Illuminate\Support\Facades\Storage;

class ProductRepository
{
    public function getImageInfoJson($product_id) 
    {
        $info = Product::find( $product_id );
        $imgArray       = [];
        if( isset( $info->productImages ) && !empty( $info->productImages ) ) {
            foreach ($info->productImages as $key => $value) {
                $tmp['name'] = 'image-'.$value->id;
                $tmp['size'] = $value->file_size;
                $tmp['url'] = asset( Storage::url($value->gallery_path));
                $tmp['id'] = $value->id;
                $tmp['order_by'] = $value->order_by;

                $imgArray[] = $tmp;
            }
        } 
        return $imgArray;
        // return json_encode( $imgArray, JSON_UNESCAPED_SLASHES );
    }

    public function getBrochureJson($product_id) {

        $info                   = Product::find( $product_id );
        $brochArr               = [];
        if( isset( $info->brochure_upload ) && !empty( $info->brochure_upload ) ) {
            
            $brochArr['name']   = 'File-'.$info->id;
            $brochArr['size']   = Storage::size('public/'.$info->brochure_upload);
            $brochArr['url']    = asset( Storage::url($info->brochure_upload) );
            $brochArr['id']     = $product_id;

        } 

        return json_encode( $brochArr, JSON_UNESCAPED_SLASHES );
    }

    
}