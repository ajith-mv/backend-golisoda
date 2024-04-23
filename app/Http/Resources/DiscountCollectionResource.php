<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DiscountCollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        
        $couponCategory     = $this->couponCategory;
        $childTmp           = [];

        $tmp['id'] = $this->id;
        $tmp['coupon_name'] = $this->coupon_name;
        $tmp['coupon_code'] = $this->coupon_code;
        $tmp['coupon_sku'] = $this->coupon_sku;
        $tmp['start_date'] = $this->start_date;
        $tmp['end_date'] = $this->end_date;
        $tmp['quantity'] = $this->quantity;
        $tmp['used_quantity'] = $this->used_quantity;
        $tmp['calculate_type'] = $this->calculate_type;
        $tmp['calculate_value'] = $this->calculate_value;
        $tmp['minimum_order_value'] = $this->minimum_order_value;
        $tmp['is_discount_on'] = $this->is_discount_on;
        $tmp['coupon_type'] = $this->coupon_type;
        $tmp['is_applied_all'] = $this->is_applied_all;
        $tmp['repeated_use_count'] = $this->id;
        $tmp['status'] = $this->status;

        if( isset( $couponCategory ) && !empty( $couponCategory ) ) {
            foreach ($couponCategory as $items ) {
                //get product info
                $proData        = [];
                $category_id    = $items->category->id;
                $productDetails = DB::table('products')
                                        ->select('product_categories.name', 'products.*')
                                        ->join('product_categories', function($join){
                                            $join->on('product_categories.id', '=', 'products.category_id');
                                            $join->orOn( 'products.category_id','=','product_categories.parent_id');
                                        })
                                        ->where(function($query) use( $category_id ){
                                            $query->where('products.category_id', $category_id );
                                            $query->orWhere('product_categories.parent_id', $category_id );
                                        })
                                        ->where('products.status', 'published')
                                        ->groupBy('products.id')
                                        ->get();
                
                if( isset( $productDetails ) && !empty($productDetails) ) {
                    foreach ($productDetails as $proItems ) {

                        $is_discount            = 'no';
                        $discount_percentage    = '';
                        $url                    = Storage::url($proItems->base_image);

                        $salePrices             = getSaleProductPrices( $proItems, $items );

                        $proTmp                 = [];

                        $proTmp['id']                   = $proItems->id;
                        $proTmp['product_name']         = $proItems->product_name;
                        $proTmp['product_url']          = $proItems->product_url;
                        $proTmp['image']                = asset( $url );
                        $proTmp['sku']                  = $proItems->sku;
                        $proTmp['category']             = $proItems->name;
                        $proTmp['strike_price']         = $salePrices['strike_rate'];
                        $proTmp['price']                = $salePrices['price'];
                        $proTmp['mrp_price']            = $proItems->price;
                        $proTmp['stock_status']         = $proItems->stock_status;
                        $proTmp['product_hsn_code']     = $proItems->hsn_code;
                        $proTmp['is_discount']          = $salePrices['has_discount'];
                        $proTmp['discount_percentage']  = $discount_percentage;
                        $proData[]                      = $proTmp;
                        
                    }
                }
                
                $subTmp = [];
                $subTmp['id'] = $items->category->id;
                $subTmp['name'] = $items->category->name;
                $subTmp['description'] = $items->category->description;
                $subTmp['is_home_menu'] = $items->category->is_home_menu;
                $subTmp['image'] = $items->category->image;
                $subTmp['products'] = $proData;

                $childTmp[] = $subTmp;

            }
        }
        $tmp['category']    = $childTmp;

        return $tmp;
    }
}
