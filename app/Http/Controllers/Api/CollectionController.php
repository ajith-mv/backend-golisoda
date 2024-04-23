<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollectionResource;
use App\Models\Product\Product;
use App\Models\Product\ProductCollection;
use App\Models\RecentView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CollectionController extends Controller
{
    public function getProductCollections(Request $request)
    {
        $order_by = $request->order_by;

        $details = ProductCollection::where(['show_home_page' => 'yes', 'is_handpicked_collection' => 'no', 'status' => 'published', 'can_map_discount' => 'no'])
            ->when($order_by != '', function ($q) use ($order_by) {
                return $q->where('order_by', $order_by);
            })
            ->orderBy('order_by', 'asc')->get();

        return ProductCollectionResource::collection($details);
    }

    public function getProductCollectionByOrder(Request $request)
    {
        $order_by = $request->order_by;

        $details = ProductCollection::where(['show_home_page' => 'yes', 'status' => 'published', 'is_handpicked_collection' => 'no', 'can_map_discount' => 'no'])
            ->when($order_by != '', function ($q) use ($order_by) {
                return $q->where('order_by', $order_by);
            })
            ->orderBy('order_by', 'asc')->first();

        return ProductCollectionResource::collection($details);
    }

    public function getRecentViews(Request $request)
    {
        $customer_id = $request->customer_id;

        $recentDetails = RecentView::where('customer_id', $customer_id)->orderBy('created_at', 'desc')->get();
        $recentData = [];
        if (isset($recentDetails) && !empty($recentDetails)) {
            foreach ($recentDetails as $items) {
                $productInfo = Product::find($items->product_id);

                $category               = $productInfo->productCategory;
                $salePrices             = getProductPrice($productInfo);

                $pro                    = [];
                $pro['has_data']        = 'yes';
                $pro['id']              = $productInfo->id;
                $pro['product_name']    = $productInfo->product_name;
                $pro['category_name']   = $category->name ?? '';
                $pro['category_slug']   = $category->slug ?? '';
                $pro['parent_category_name']   = $category->parent->name ?? '';
                $pro['parent_category_slug']   = $category->parent->slug ?? '';
                $pro['brand_name']      = $productInfo->productBrand->brand_name ?? '';
                $pro['hsn_code']        = $productInfo->hsn_code;
                $pro['product_url']     = $productInfo->product_url;
                $pro['sku']             = $productInfo->sku;
                $pro['stock_status']    = $productInfo->stock_status;
                $pro['is_featured']     = $productInfo->is_featured;
                $pro['is_best_selling'] = $productInfo->is_best_selling;
                $pro['is_new']          = $productInfo->is_new;
                $pro['sale_prices']     = $salePrices;
                $pro['mrp_price']       = $productInfo->price;
                $pro['videolinks']      = $productInfo->productVideoLinks;
                $pro['links']           = $productInfo->productLinks;
                $pro['image']           = $productInfo->base_image;
                $pro['max_quantity']    = $productInfo->quantity;

                $imagePath              = $productInfo->base_image;

                if (!Storage::exists($imagePath)) {
                    $path               = asset('assets/logo/no_Image.jpg');
                } else {
                    $url                = Storage::url($imagePath);
                    $path               = asset($url);
                }

                $pro['image']                   = $path;
                $recentData[] = $pro;
                // print_r( count($items->products) );
            }
        }
        return $recentData;
    }
}
