<?php
namespace App\Imports;
use App\Models\Product\Product;
use App\Models\Product\ProductMetaTag;
use App\Models\Product\ProductRelatedRelation;
use App\Models\Product\ProductCrossSaleRelation;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class MultiSheetRelatedProductImport implements ToModel, WithHeadingRow
{
   
    public function model(array $row)
    {
        
      $product=Product::where('sku',$row['sku'])->first();
      if($product){
      $meta_ins['meta_title']         = $row['meta_title'];
      $meta_ins['meta_description']   =  $row['meta_description'];
      $meta_ins['meta_keyword']       =  $row['meta_keyword'];
      $meta_ins['product_id']         = $product->id;
      ProductMetaTag::updateOrCreate(['product_id' => $product->id], $meta_ins);
      $related_skus=explode(',',$row['related_sku']);
      $frequent_skus=explode(',',$row['frequent_sku']);
     ProductRelatedRelation::where('from_product_id',$product['id'])->delete();
     ProductCrossSaleRelation::where('from_product_id',$product['id'])->delete();
    foreach ( $related_skus as $related_sku ) {
        $product_data=Product::where('sku',str_replace(' ', '',$related_sku))->first();
        if(isset($product_data)){
        $insRelated['to_product_id'] = $product_data->id;
        $insRelated['from_product_id'] =  $product['id'];
        ProductRelatedRelation::create($insRelated);
        }
        
    }
    
    foreach ( $frequent_skus as $frequent_sku ) {
        $product_data=Product::where('sku',str_replace(' ', '',$frequent_sku))->first();
        if(isset($product_data)){
        $insRelated['from_product_id'] =$product['id'];
        $insRelated['to_product_id'] =$product_data->id;
        ProductCrossSaleRelation::create($insRelated);
        }
        
    }
    }
  }
}
