<?php

namespace App\Imports;

use App\Models\Category\MainCategory;
use App\Models\Category\SubCategory;
use App\Models\Master\Brands;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductLink;
use App\Models\Settings\Tax;
use App\Models\Product\ProductMeasurement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class MultiSheetProductImport implements ToModel, WithHeadingRow
{
   
    public function model(array $row)
    {
       
        ini_set('max_execution_time', 3000);
        /***
         * 1.check tax exist
         * 2.check category exist
         * 3.check subcategory exist
         * 4.check brand exist         
         */
       
        $status = 'published'; 
        
        $ins = $cat_ins = $tax_ins = $subcat_ins = $brand_ins = $link_ins = [];
        $category           = $row['category'] ?? null;
        $sub_category       = $row['sub_category'] ?? null;
        $tax                = 0;  
        if(isset($row['tax %'])){
            $tax                = $row['tax %']; 
        }
       
         
        if( isset( $category ) && !empty( $category ) ) {
            
            #check taxt exits if not create 
            $taxPercentage  = $tax;
            $checkTax       = Tax::where('pecentage', $taxPercentage)->first();
            if( isset($checkTax) && !empty( $checkTax ) ) {
                $tax_id     = $checkTax->id;
            } else {
                $tax_ins['title'] = 'Tax '.intval($taxPercentage);
                $tax_ins['pecentage'] = $taxPercentage ?? 0;
                $tax_ins['order_by'] = 0;

                $tax_id = Tax::create($tax_ins)->id;
                 
            }
   
            #do insert or update if data exist or not
            $checkCategory = ProductCategory::where('name', trim($category) )->first();
           
       
               
            if( isset( $checkCategory ) && !empty( $checkCategory ) ) {
                $category_id                = $checkCategory->id;
            } else {
                #insert new category                
                $cat_ins['name']            = $category;
                $cat_ins['parent_id']       = 0;
                $cat_ins['description']     = $row['category_description'] ?? null;
                $cat_ins['status']          = 'published';
                $cat_ins['is_featured']     = '0';
                $cat_ins['added_by']        = Auth::id();                
                $cat_ins['tag_line']        = $row['category_tagline'] ?? null;                
                $cat_ins['tax_id']          = $tax_id;
                $cat_ins['is_home_menu']    = 'yes'; 
                $cat_ins['slug']            = Str::slug($category);
                
                $category_id                = ProductCategory::create($cat_ins)->id;
                
            }
          
        
            if( !empty($category_id)) {

                #check subcategory exist or create new one
                $checkSubCategory = ProductCategory::where(['name' => trim($sub_category), 'parent_id' => $category_id] )->first();
                

                if( isset( $checkSubCategory ) && !empty( $checkSubCategory ) ) {
                    $sub_category_id                = $checkSubCategory->id;
                } else if( $sub_category ) {
                    #insert new sub category
                    $subcat_ins['tax_id']           = $tax_id;
                    $subcat_ins['is_home_menu']     = 'no';
                    $subcat_ins['added_by']         = Auth::id();
                    $subcat_ins['name']             = trim($sub_category);
                    $subcat_ins['description']      = $row['subcategory_description'] ?? null;
                    $subcat_ins['order_by']         = 0;
                    $subcat_ins['tag_line']         = $row['subcategory_tagline'] ?? null;
                    $subcat_ins['status']           = 'published';
                    $subcat_ins['parent_id']        = $category_id;
                    $subcat_ins['is_featured']      = '0';
    
                    $parent_name = '';
                    if( isset( $category_id ) && !empty( $category_id ) ) {
                        $parentInfo                 = ProductCategory::find($category_id);
                        $parent_name                = $parentInfo->name ?? '';
                    }
        
                    $subcat_ins['slug']             = Str::slug($sub_category.' '.$parent_name);
                    $sub_category_id                = ProductCategory::create($subcat_ins);
    
                }

            }
           
            #check brand exist or create new one
            $checkBrand                         = Brands::where('brand_name', trim($row['brand']))->first();
            
            if( isset( $checkBrand ) && !empty( $checkBrand ) ) {
                $brand_id                       = $checkBrand->id;
            } else {
                #insert new brand
                $brand_ins['brand_name']    = trim($row['brand']);
                $brand_ins['slug']          = Str::slug($row['brand']);
                $brand_ins['order_by']      = 0;
                $brand_ins['status']        = 'published';

                $brand_id                   = Brands::create($brand_ins)->id;
            }

            #check label exist or create new one
            $label_id = null;
            if( isset( $row['label'] ) && !empty( $row['label'])) {
                $label_info = SubCategory::where('name', $row['label'])->first();
                if( $label_info ) {
                    $label_id = $label_info->id;
                } else {
                    $main_category = MainCategory::where('slug', 'product-labels')->first();
                    #insert new label 
                    $label_ins = [];
                    $label_ins['parent_id'] = $main_category->id;
                    $label_ins['name'] = $row['label'];
                    $label_ins['slug'] = Str::slug($row['label']);
                    $label_ins['status'] = 'published';
                    $label_ins['added_by'] = Auth::id();

                    $label_id = SubCategory::create($label_ins)->id;
                    
                }
            }
            
            
            $sku            = Str::replace('.','-',$row['sku']);
			
            $productInfo = Product::where('sku', $sku)->first();
            $base_price = $row['base_price'] ?? 0;
            if( !$base_price ) {

                $base_price_info = getAmountExclusiveTax( $row['mop_price'], $taxPercentage);
                $base_price = $base_price_info['basePrice'];
                
            }
            
            $ins['product_name'] = trim($row['product_name']);
            $ins['hsn_code'] = $row['hsn_no'] ?? null;
            $ins['product_url']=Str::slug(trim($row['product_name']));
            $ins['sku'] = $sku;
            $ins['strike_price'] = round((float)$row['mrp_price']);
            $ins['price'] = round((float)$base_price);
            if($row['mop_price']==0){
                  $ins['mrp'] = round((float)$row['mrp_price'] ?? 0);
            }else{
                  $ins['mrp'] = round((float)$row['mop_price'] ?? 0);
            }
            $ins['discount_percentage'] = getDiscountPercentage(round((float)$row['mop_price'] ?? 0), round((float)$row['mrp_price']));
            $ins['status'] = $status;
            $ins['quantity'] = $row['quantity'] ?? 1;
            $ins['stock_status'] = ( $row['stock_status'] == 'in_stock') ? 'in_stock' : NULL;
            $ins['brand_id'] = $brand_id;
            $ins['category_id'] = $sub_category_id ?? $category_id;
            $ins['is_featured'] = ( isset($row['featured']) &&  $row['featured']=='Yes' ) ? 1 : 0;
            $ins['description'] = $row['description'] ?? NULL;
            $ins['tax_id'] = $tax_id;
            $ins['label_id'] = $label_id;
            $ins['status'] = ( $row['status'] == 'published') ? 'published' : 'unpublished';
               $ins['no_of_items'] = $row['no_of_items'] ?? NULL;
               $ins['material_ingredients'] = $row['material_ingredients'] ?? NULL;
               $ins['features'] =$row['features'] ?? NULL;
               $ins['benefits'] = $row['benefits'] ?? NULL;
            $ins['added_by'] = Auth::id();
           
			if( isset( $productInfo ) && !empty( $productInfo ) ) {
                
            	DB::table('products')->where('id', $productInfo->id)->update($ins);
            	$product_id = $productInfo->id;
            
            } else {
            	$product_id     = Product::create($ins)->id;
            }
            if(isset($product_id)){
           
            if( isset($row['weight']) && isset($row['width']) && !empty($row['weight']) && !empty( $row['width'])) {
                ProductMeasurement::where('product_id', $product_id )->delete();
                $measure['product_id']  = $product_id;
                $measure[ 'weight' ]    = $row['weight'] ?? 0;
                $measure[ 'width' ]     = $row['width']  ?? 0;
                $measure[ 'hight' ]     = $row['hight']  ?? 0;
                $measure[ 'length' ]    = $row['length'] ?? 0;
                ProductMeasurement::create($measure);
            }
            }
            if( isset( $row['video_link']) && !empty( $row['video_link'])) {
                $link_ins['product_id'] = $product_id;
                $link_ins['url'] = $row['video_link'];
                $link_ins['url_type'] = 'video_link';
                ProductLink::create($link_ins);
            }
             
            
        }
       
    }
}
