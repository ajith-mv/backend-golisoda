<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuAllResource;
use App\Http\Resources\MenuResource;
use App\Models\Product\ProductCategory;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    
    public function getTopMenu(Request $request)
    {
        $slug           = $request->slug;
        
        $data           = ProductCategory::where(['is_home_menu' => 'yes', 'status' => 'published'])
                            ->when( $slug != '', function($q) use($slug){
                                return $q->where('slug', $slug);
                            })
                            ->when( $slug == '', function($q) {
                                return $q->where('parent_id', 0);
                            })
                            ->orderBy('order_by', 'asc')
                            ->get();
        return MenuResource::collection($data);
        
    }

    public function getAllMenu()
    {

        $all_category = ProductCategory::where('status', 'published')->where('parent_id', 0)->orderBy('order_by', 'asc')->get();

        $category = [];
        if( isset( $all_category ) && !empty( $all_category ) ) {
            foreach ($all_category as $cat_item ) {
                $category[$cat_item->id] = array('id' => $cat_item->id, 'name' => $cat_item->name, 'slug' => $cat_item->slug);
                $category[$cat_item->id]['child'] = [];
                // dump( $cat_item->childCategory );
                if( isset( $cat_item->childCategory ) && !empty( $cat_item->childCategory ) ) {
                    foreach ($cat_item->childCategory as $sub_item) {
                                             
                        if( count($sub_item->products ) > 0 ) {                            
                            $category[$cat_item->id]['child'][] = array('id' => $sub_item->id, 'name' => $sub_item->name, 'slug' => $sub_item->slug);
                        }

                    }
                }
            }
        }
        $new_menu = [];
        if( !empty( $category ) ) {
            foreach ($category as $key => $value) {
                
                $new_menu[] = $value;
            }
        }

        return array( 'data' => $new_menu);

    }

}
