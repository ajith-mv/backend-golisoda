<?php
namespace App\Repositories\Api;

use App\Models\Product\ProductCategory;

class MenuRepository
{

    public function makeTopMenu()
    {
        $topMenus   = ProductCategory::select('id', 'name')->where(['is_home_menu' => 'yes', 'status' => 'published', 'parent_id' => 0])->orderBy('order_by', 'asc')->get();
        $menus      = [];
        if( isset( $topMenus ) && !empty( $topMenus )) {
            foreach ($topMenus as $item ) {
                $childTmp           = [];
                $tmp[ 'id' ]        = $item->id;
                $tmp[ 'name' ]      = $item->name;

                if( isset( $item->childTopMenuCategory ) && !empty( $item->childTopMenuCategory ) ) {
                    foreach ($item->childTopMenuCategory as $child ) {
                        $innerTmp['id']     = $child->id;
                        $innerTmp['name']   = $child->name;

                        $childTmp[]         = $innerTmp;
                    }
                }
                $tmp['child']       = $childTmp;
                $menus[]            = $tmp;
            }
        } 
        return $menus;
    }

    public function makeAllMenu()
    {
        $topMenus   = ProductCategory::select('id', 'name')->where(['status' => 'published', 'parent_id' => 0])->orderBy('order_by', 'asc')->get();
        $menus      = [];
        if( isset( $topMenus ) && !empty( $topMenus )) {
            foreach ($topMenus as $item ) {
                $childTmp           = [];
                $tmp[ 'id' ]        = $item->id;
                $tmp[ 'name' ]      = $item->name;

                if( isset( $item->childTopMenuCategory ) && !empty( $item->childTopMenuCategory ) ) {
                    foreach ($item->childTopMenuCategory as $child ) {
                        $innerTmp['id']     = $child->id;
                        $innerTmp['name']   = $child->name;

                        $childTmp[]         = $innerTmp;
                    }
                }
                $tmp['child']       = $childTmp;
                $menus[]            = $tmp;
            }
        } 
        return $menus;
    }

}