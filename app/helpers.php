<?php

use App\Helpers\AccessGuard;
use App\Models\Cart;
use App\Models\CartProductAddon;
use App\Models\Master\Customer;
use App\Models\Order;
use App\Models\Product\Product;
use App\Models\Product\ProductVariationOption;
use App\Models\Product\Review;
use App\Models\ProductAddon;
use App\Models\SmsTemplate;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\Master\Variation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

if (!function_exists('gSetting')) {
    function gSetting($column)
    {
        $info = \DB::table('global_settings')->first();
        if (isset($info) && !empty($info)) {
            return $info->$column ?? '';
        } else {
            return false;
        }
    }
}

if (!function_exists('errorArrays')) {
    function errorArrays($errors)
    {
        return array_map(function ($err) {
            return '<div>' . str_replace(',', '', $err) . '</div>';
        }, $errors);
    }
}

function sendGBSSms($sms_type, $details)
{
    $info                   = SmsTemplate::where('sms_type', $sms_type)->first();

    // if (isset($info) && !empty($info)) {

    //     $templateMessage    = $info->template_content;
    //     $templateMessage    = str_replace("{", "", addslashes($templateMessage));
    //     $templateMessage    = str_replace("}", "", $templateMessage);

    //     extract($details);

    //     eval("\$templateMessage = \"$templateMessage\";");

    //     $templateMessage = str_replace("\'", "", $templateMessage);

    //     $params             = array(
    //         'entityid' => $info->peid_no,
    //         'tempid' => $info->tdlt_no,
    //         'sid'   => urlencode(current(explode(",", $info->header)))
    //     );
    //     // dump( $templateMessage );
    //     // dd( $params );
    //     sendSMS($mobile_no, $templateMessage, $params);
    // }

    return true;
}

function sendSMS($numbers, $msg, $params)
{

    // extract($params);
    // $uid = "gbssystems";
    // $pwd = urlencode("18585");
    // // $entityid = "1001409933589317661";
    // // $tempid = "1607100000000238808";
    // $sid = urlencode("GBSCOM");

    // $message = rawurlencode($msg);
    // $numbers = implode(',', $numbers);
    // $dtTime = date('m-d-Y h:i:s A');
    // $data = "&uid=" . $uid . "&pwd=" . $pwd . "&mobile=" . $numbers . "&msg=" . $message . "&sid=" . $sid . "&type=0" . "&dtTimeNow=" . $dtTime . "&entityid=" . $entityid . "&tempid=" . $tempid;
    // // dd( $data );
    // $ch = curl_init("http://smsintegra.com/api/smsapi.aspx?");
    // curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // $response = curl_exec($ch);
    // // echo $response;
    // curl_close($ch);
    // return $response;

    return true;
}

if (!function_exists('access')) {
    function access()
    {
        return new AccessGuard();
    }
}

if (!function_exists('getAmountExclusiveTax')) {
    function getAmountExclusiveTax($productAmount, $gstPercentage)
    {

        $basePrice      = $productAmount ?? 0;
        $gstAmount      = 0;
        if ((int)$gstPercentage > 0) {
            $gstAmount = $productAmount - ($productAmount * (100 / (100 + $gstPercentage)));
            $basePrice = $productAmount - $gstAmount;
        }

        return array('basePrice' => round($basePrice), 'gstAmount' => round($gstAmount), 'tax_percentage' => $gstPercentage);
    }
}

if (!function_exists('getAmountInclusiveTax')) {
    function getAmountInclusiveTax($productAmount, $gstPercentage)
    {
        // GST = (Original Cost * GST rate%) / 100
        $mrpPrice      = $productAmount ?? 0;
        $gstAmount      = 0;
        if ((int)$gstPercentage > 0) {
            $gstAmount = ($productAmount * $gstPercentage) / 100;
            $mrpPrice = $productAmount + $gstAmount;
        }

        return array('mrpPrice' => $mrpPrice, 'gstAmount' => $gstAmount, 'tax_percentage' => $gstPercentage);
    }
}

if (!function_exists('generateProductSku')) {
    function generateProductSku($brand, $sku = '')
    {
        $countNumber    = '0000';
        if (empty($sku)) {
            $sku = 'GS-' . date('m') . '-' . strtoupper($brand) . '-' . $countNumber;
        }


        $checkProduct = Product::where('sku', $sku)->orderBy('id', 'desc')->first();
        if (isset($checkProduct) && !empty($checkProduct)) {
            $old_no = $checkProduct->sku;
            $old_no = explode("-", $old_no);

            $end = end($old_no);
            $old_no = (int)$end + 1;

            if ((4 - strlen($old_no)) > 0) {
                $new_no = '';
                for ($i = 0; $i < (4 - strlen($old_no)); $i++) {
                    $new_no .= '0';
                }
                $ord = $new_no . $old_no;

                $sku =  'GS-' . date('m') . '-' . strtoupper($brand) . '-' . $ord;
            }
        }
        return $sku;
    }
}

if (!function_exists('getCustomerNo')) {
    function getCustomerNo()
    {

        $countNumber    = '000001';
        $customer_no    = 'GS' . $countNumber;

        $checkCustomer  = Customer::orderBy('id', 'desc')->first();
        if (isset($checkCustomer) && !empty($checkCustomer)) {
            $old_no = $checkCustomer->customer_no;
            $end = substr($old_no, 4);

            $old_no = (int)$end + 1;

            if ((6 - strlen($old_no)) > 0) {
                $new_no = '';
                for ($i = 0; $i < (6 - strlen($old_no)); $i++) {
                    $new_no .= '0';
                }
                $ord = $new_no . $old_no;

                $customer_no =  'GS' . $ord;
            }
        }
        return $customer_no;
    }
}

if (!function_exists('getOrderNo')) {
    function getOrderNo()
    {

        $countNumber    = '000001';
        $order_no    = 'GS-' . $countNumber;

        $checkCustomer  = Order::orderBy('id', 'desc')->first();

        if (isset($checkCustomer) && !empty($checkCustomer)) {
            $old_no = $checkCustomer->order_no;
            $old_no = explode("-", $old_no);
            $end = end($old_no);
            $old_no = $end + 1;

            if ((6 - strlen($old_no)) > 0) {
                $new_no = '';
                for ($i = 0; $i < (6 - strlen($old_no)); $i++) {
                    $new_no .= '0';
                }
                $ord = $new_no . $old_no;

                $order_no =  'GS-' . $ord;
            }
        }

        return $order_no;
    }
}

if (!function_exists('percentage')) {
    function percentage($amount, $percent)
    {
        return $amount - ($amount * ($percent / 100));
    }
}

if (!function_exists('percentageAmountOnly')) {
    function percentageAmountOnly($amount, $percent)
    {
        return round((round($amount) * ($percent / 100)));
    }
}

if (!function_exists('getSaleProductPrices')) {
    function getSaleProductPrices($productsObjects, $couponsInfo)
    {

        $strike_rate    = 0;
        $price          = $productsObjects->price;
        $today          = date('Y-m-d');
        /****
         * 1.check product discount is applied via product add/edit
         * 2.check overall discount is applied for product category
         */
        $has_discount       = 'no';
        #condition 1:
        if ($today >= $productsObjects->sale_start_date && $today <= $productsObjects->sale_end_date) {
            $strike_rate    = $productsObjects->price;
            $price          = $productsObjects->sale_price;
            $has_discount       = 'yes';
        }

        #condition 2:
        if (isset($couponsInfo) && !empty($couponsInfo)) {
            if ($couponsInfo->quantity > $couponsInfo->used_quantity) {

                #check product amount greater than minimum order value
                if ($couponsInfo->minimum_order_value <= $price) {
                    #then do percentage or fixed amount discount
                    switch ($couponsInfo->calculate_type) {
                        case 'percentage':
                            $strike_rate    = $price;
                            $price          = percentage($price, $couponsInfo->calculate_value);
                            $has_discount   = 'yes';
                            break;
                        case 'fixed_amount':
                            $strike_rate    = $price;
                            $price          = $price - $couponsInfo->calculate_value;
                            $has_discount   = 'yes';
                            break;
                        default:
                            # code...
                            break;
                    }
                }
            }
        }

        return array('strike_rate' => $strike_rate, 'price' => $price, 'has_discount' => $has_discount);
    }
}

function getProductApiData($product_data, $customer_id = '', $variation_option_id = null)
{
    //    dd( $product_data->productCategory->name );

    $category               = $product_data->productCategory;
    $price_data = getProductPrices($product_data);

    $pro                    = [];
    $total_variation_amount = 0;
    $default_value = [];

    if (isset($variation_option_id) && !empty($variation_option_id)) {
        $variation_option_data = ProductVariationOption::whereIn('id', $variation_option_id)
            ->where('product_id', $product_data->id)
            ->selectRaw("SUM(amount) AS total_amount")
            ->groupBy('product_id')
            ->get();
        if (isset($variation_option_data)) {
            $total_variation_amount = $variation_option_data[0]->total_amount;
        }
    } else {
        $variation_option_data = ProductVariationOption::where('product_id', $product_data->id)
            ->where('is_default', 1)
            ->get();

        if (isset($variation_option_data)) {

            foreach ($variation_option_data as $value) {

                $variation = Variation::where('id', $value->variation_id)->first();
                if ($variation) {
                    $title = $variation->title ?? '';
                    $id = $value->id ?? '';
                    $default_value[$title] = $id;
                    $total_variation_amount = $total_variation_amount + $value->amount;
                }
            }
        }
    }
    $pro['default_value '] = $default_value;
    $pro['total_variation_amount'] = $total_variation_amount;
    if ($price_data['overall_discount_percentage'] != 0) {
        $pro['price']           = $price_data['price'] + $total_variation_amount;
        $pro['discount_percentage'] = $price_data['overall_discount_percentage'];
        $pro['strike_price']    = $price_data['strike_rate'];
        $pro['save_price']      = round($price_data['strike_rate'] - $price_data['price']);
    } else {
        $pro['price']           = $product_data->mrp + $total_variation_amount;
        $pro['discount_percentage'] = $product_data->discount_percentage != 0 ? abs($product_data->discount_percentage) : getDiscountPercentage($product_data->mrp, $product_data->strike_price);
        $pro['strike_price']    = $product_data->strike_price;
        $pro['save_price']      = round($product_data->strike_price - $product_data->mrp);
    }
    $pro['id']              = $product_data->id;
    $pro['product_name']    = $product_data->product_name;
    $pro['category_name']   = $product_data->productCategory->name ?? '';
    $pro['category_slug']   = $product_data->productCategory->slug ?? '';
    $pro['brand_name']      = $product_data->productBrand->brand_name ?? '';
    $pro['brand_id']        = $product_data->brand_id ?? '';
    $pro['hsn_code']        = $product_data->hsn_code;
    $pro['product_url']     = $product_data->product_url;
    $pro['sku']             = $product_data->sku;
    $pro['stock_status']    = $product_data->stock_status;
    $pro['is_featured']     = $product_data->is_featured;
    $pro['is_best_selling'] = $product_data->is_best_selling;
    $pro['is_new']          = $product_data->is_new;
    $pro['image']           = $product_data->base_image;
    $pro['max_quantity']    = $product_data->quantity;
    $imagePath              = $product_data->base_image;
    $pro['no_of_items']    = $product_data->no_of_items;
    $pro['material_ingredients']    = $product_data->material_ingredients;
    $pro['features']    = $product_data->features;
    $pro['benefits']    = $product_data->benefits;

    if (!Storage::exists($imagePath)) {
        $path               = asset('userImage/no_Image.jpg');
    } else {
        $url                = Storage::url($imagePath);
        $path               = asset($url);
    }

    $pro['image']                   = $path;

    /**
     * check product has customer reveiws
     */
    $reviews = '';
    $wishlist = '';
    $is_cart = '';
    $has_purchased = false;
    $common_reviews = Review::select(DB::raw('count(*) as total, CAST(AVG(star) as Decimal(10,1) ) AS rating'))->where(['product_id' => $product_data->id, 'status' => 'approved'])->first();
    $has_pickup_store = true;
    if (isset($customer_id) && !empty($customer_id)) {
        $reviews = Review::where(['product_id' => $product_data->id, 'customer_id' => $customer_id])->first();
        $wishlist = Wishlist::where(['product_id' => $product_data->id, 'customer_id' => $customer_id])->first();
        $is_cart = Cart::where(['product_id' => $product_data->id, 'customer_id' => $customer_id])->first();
        $purchased_data = Order::join('order_products', 'order_products.order_id', '=', 'orders.id')
            ->where('orders.customer_id', $customer_id)
            ->where('orders.status', 'delivered')
            ->where('order_products.product_id', $product_data->id)->first();

        if ($purchased_data) {
            $has_purchased = true;
        }
        /**
         * check cart has different brand
         */
        $checkCart          = Cart::with(['products', 'products.productCategory'])->when($customer_id != '', function ($q) use ($customer_id) {
            $q->where('customer_id', $customer_id);
        })->get();

        $brand_array = [];
        if (isset($checkCart) && !empty($checkCart)) {
            foreach ($checkCart as $cartitems) {
                $proitems = $cartitems->products;
                $brand_array[] = $proitems->brand_id;
            }
        }

        if (count(array_unique($brand_array)) > 1) {
            $has_pickup_store = false;
        } else {
            if (!empty($brand_array)) {
                $current_cart_brand_id = current($brand_array);

                if ($product_data->brand_id != $current_cart_brand_id) {
                    $has_pickup_store = false;
                }
            }
        }
    }
    $pro['has_pickup_store'] = $has_pickup_store;
    $pro['has_purchased'] = $has_purchased;
    $pro['is_review'] = $reviews ? true : false;
    $pro['common_review'] = $common_reviews;
    $pro['is_wishlist'] = $wishlist ? true : false;
    $pro['is_cart'] = $is_cart ? true : false;
    $pro['cart_id'] = $is_cart->id ?? 0;
    $pro['warranty_available'] = $product_data->warranty ? $product_data->warranty->toArray() : [];

    $pro['description']             = $product_data->description;
    $gallery = [];
    if (isset($product_data->productImages) && !empty($product_data->productImages)) {
        foreach ($product_data->productImages as $att) {

            $gallery_url            = Storage::url($att->gallery_path);
            $path                   = asset($gallery_url);

            $gallery[] = $path;
        }
    } else {
        $gallery[] = $pro['image'];
    }

    $pro['gallery'] = $gallery;
    $pro['attributes']              = $product_data->productAttributes;
    $pro['overview']                = $product_data->productOverviewAttributes;
    $related_arr                    = [];
    if (isset($product_data->productRelated) && !empty($product_data->productRelated)) {
        foreach ($product_data->productRelated as $related) {

            $productInfo            = Product::find($related->to_product_id);
            $category               = $productInfo->productCategory;

            $tmp2                    = [];
            $tmp2['id']              = $productInfo->id;
            $tmp2['product_name']    = $productInfo->product_name;
            $tmp2['category_name']   = $category->name ?? '';
            $tmp2['brand_name']      = $productInfo->productBrand->brand_name ?? '';
            $tmp2['hsn_code']        = $productInfo->hsn_code;
            $tmp2['product_url']     = $productInfo->product_url;
            $tmp2['sku']             = $productInfo->sku;
            $tmp2['stock_status']    = $productInfo->stock_status;
            $tmp2['is_featured']     = $productInfo->is_featured;
            $tmp2['is_best_selling'] = $productInfo->is_best_selling;
            $tmp2['is_new']          = $productInfo->is_new;
            $tmp2['price']           = $productInfo->mrp;
            $tmp2['strike_price']    = $productInfo->strike_price;
            $tmp2['save_price']      = $productInfo->strike_price - $productInfo->mrp;
            $tmp2['discount_percentage'] = $productInfo->discount_percentage != 0 ? abs($productInfo->discount_percentage) : getDiscountPercentage($productInfo->mrp, $productInfo->strike_price);

            $tmp2['image']           = $productInfo->base_image;

            $imagePath              = $productInfo->base_image;

            if (!Storage::exists($imagePath)) {
                $path               = asset('assets/logo/no_Image.jpg');
            } else {
                $url                = Storage::url($imagePath);
                $path               = asset($url);
            }

            $tmp2['image']           = $path;
            $related_arr[]          = $tmp2;
        }
    }

    $frequently_purchased           = [];
    if (isset($product_data->productCrossSale) && !empty($product_data->productCrossSale)) {
        foreach ($product_data->productCrossSale as $related) {
            $productInfo            = Product::find($related->to_product_id);

            $category               = $productInfo->productCategory;
            if ($productInfo->stock_status == 'in_stock') {

                $tmp2                    = [];
                $tmp2['id']              = $productInfo->id;
                $tmp2['product_name']    = $productInfo->product_name;
                $tmp2['category_name']   = $category->name ?? '';
                $tmp2['brand_name']      = $productInfo->productBrand->brand_name ?? '';
                $tmp2['hsn_code']        = $productInfo->hsn_code;
                $tmp2['product_url']     = $productInfo->product_url;
                $tmp2['sku']             = $productInfo->sku;
                $tmp2['stock_status']    = $productInfo->stock_status;
                $tmp2['is_featured']     = $productInfo->is_featured;
                $tmp2['is_best_selling'] = $productInfo->is_best_selling;
                $tmp2['is_new']          = $productInfo->is_new;
                $tmp2['price']           = $productInfo->mrp;
                $tmp2['strike_price']    = $productInfo->strike_price;
                $tmp2['save_price']      = $productInfo->strike_price - $productInfo->mrp;
                $tmp2['discount_percentage'] = $productInfo->discount_percentage != 0 ? abs($productInfo->discount_percentage) : getDiscountPercentage($productInfo->mrp, $productInfo->strike_price);
                $tmp2['image']           = $productInfo->base_image;

                $imagePath              = $productInfo->base_image;

                if (!Storage::exists($imagePath)) {
                    $path               = asset('assets/logo/no_Image.jpg');
                } else {
                    $url                = Storage::url($imagePath);
                    $path               = asset($url);
                }

                $tmp2['image']           = $path;
                $frequently_purchased[]  = $tmp2;
            }
        }
    }

    $description_arr = [];

    if (isset($product_data->productDescription) && !empty($product_data->productDescription)) {
        foreach ($product_data->productDescription as $items) {
            $temp = [];
            $temp['title'] = $items->title;
            $temp['description'] = $items->description;

            if (!Storage::exists($items->desc_image)) {
                $path               = asset('assets/logo/no_Image.jpg');
            } else {
                $url                = Storage::url($items->desc_image);
                $path               = asset($url);
            }

            $temp['desc_image'] = $path;

            $description_arr[] = $temp;
        }
    }

    $addon_arr = [];


    if (isset($product_data->productAddons) && !empty($product_data->productAddons)) {

        foreach ($product_data->productAddons as $items) {

            $addon_items = ProductAddon::find($items->product_addon_id);

            $temp = [];
            if ($addon_items) {

                $temp['id'] = $addon_items->id;
                $temp['title'] = $addon_items->title;
                $temp['description'] = $addon_items->description;

                if (!Storage::exists($addon_items->icon)) {
                    $path               = asset('assets/logo/no_Image.jpg');
                } else {
                    $url                = Storage::url($addon_items->icon);
                    $path               = asset($url);
                }

                $temp['icon'] = $path;
                $addon_item_array = [];
                // dd( $addon_items->items );
                if (isset($addon_items->items) && !empty($addon_items->items)) {
                    foreach ($addon_items->items as $aitem) {

                        $is_selected = false;
                        if (isset($is_cart->id) && !empty($is_cart->id)) {

                            $is_selected = addonHasSelected($aitem->id, $product_data->id, $is_cart->id);
                        }
                        $tmp = [];
                        $tmp['id'] = $aitem->id;
                        $tmp['label'] = $aitem->label;
                        $tmp['amount'] = $aitem->amount;
                        $tmp['is_selected'] = $is_selected;
                        $addon_item_array[] = $tmp;
                    }
                }
            }
            $temp['items'] = $addon_item_array;

            $addon_arr[] = $temp;
        }
    }
    if (isset($product_data->productCategory->productAddonsByCategory) && count($product_data->productCategory->productAddonsByCategory) > 0) {

        $product_addons = $product_data->productCategory->productAddonsByCategory;
        foreach ($product_addons as $paddon) {

            $addon_items = ProductAddon::find($paddon->product_addon_id);
            $temp = [];
            if ($addon_items) {

                $temp['id'] = $addon_items->id;
                $temp['title'] = $addon_items->title;
                $temp['description'] = $addon_items->description;

                if (!Storage::exists($addon_items->icon)) {
                    $path               = asset('assets/logo/no_Image.jpg');
                } else {
                    $url                = Storage::url($addon_items->icon);
                    $path               = asset($url);
                }

                $temp['icon'] = $path;
                $addon_item_array = [];
                // dd( $addon_items->items );
                if (isset($addon_items->items) && !empty($addon_items->items)) {
                    foreach ($addon_items->items as $aitem) {

                        $is_selected = false;
                        if (isset($is_cart->id) && !empty($is_cart->id)) {

                            $is_selected = addonHasSelected($aitem->id, $product_data->id, $is_cart->id);
                        }
                        $tmp = [];
                        $tmp['id'] = $aitem->id;
                        $tmp['label'] = $aitem->label;
                        $tmp['amount'] = $aitem->amount;
                        $tmp['is_selected'] = $is_selected;
                        $addon_item_array[] = $tmp;
                    }
                }
            }
            $temp['items'] = $addon_item_array;

            $addon_arr[] = $temp;
        }
    }
    if (isset($product_data->productCategory->parent->productAddonsByCategory) && count($product_data->productCategory->parent->productAddonsByCategory) > 0) {

        $product_addons = $product_data->productCategory->parent->productAddonsByCategory;
        foreach ($product_addons as $paddon) {

            $addon_items = ProductAddon::find($paddon->product_addon_id);
            $temp = [];
            if ($addon_items) {

                $temp['id'] = $addon_items->id;
                $temp['title'] = $addon_items->title;
                $temp['description'] = $addon_items->description;

                if (!Storage::exists($addon_items->icon)) {
                    $path               = asset('assets/logo/no_Image.jpg');
                } else {
                    $url                = Storage::url($addon_items->icon);
                    $path               = asset($url);
                }

                $temp['icon'] = $path;
                $addon_item_array = [];
                // dd( $addon_items->items );
                if (isset($addon_items->items) && !empty($addon_items->items)) {
                    foreach ($addon_items->items as $aitem) {

                        $is_selected = false;
                        if (isset($is_cart->id) && !empty($is_cart->id)) {

                            $is_selected = addonHasSelected($aitem->id, $product_data->id, $is_cart->id);
                        }
                        $tmp = [];
                        $tmp['id'] = $aitem->id;
                        $tmp['label'] = $aitem->label;
                        $tmp['amount'] = $aitem->amount;
                        $tmp['is_selected'] = $is_selected;
                        $addon_item_array[] = $tmp;
                    }
                }
            }
            $temp['items'] = $addon_item_array;

            $addon_arr[] = $temp;
        }
    }



    $video_link = [];
    if (isset($product_data->productUrl) && count($product_data->productUrl) > 0) {
        foreach ($product_data->productUrl as $uitem) {
            $video_link[] = array(
                'thumbnail' => $uitem->thumbnail_url ?? '',
                'video_link' => $uitem->video_url ?? ''
            );
        }
    }

    $variation_option = [];
    if (isset($product_data->productVariationOption) && !empty($product_data->productVariationOption)) {
        foreach ($product_data->productVariationOption as $product_variation_option) {
            $variation_option[$product_variation_option->variation->title][] = [
                'variation_option_id' => $product_variation_option->id,
                'variation_id' => $product_variation_option->variation_id,
                'variation_name' => $product_variation_option->variation->title,
                'variation_value' => $product_variation_option->value,
                'variation_amount' => $product_variation_option->amount,
                'is_default' => $product_variation_option->is_default,
            ];
        }
    }
    $pro['variation_option'] = $variation_option;
    $pro['addons'] = $addon_arr;
    $pro['description_products'] = $description_arr;
    $pro['frequently_purchased'] = $frequently_purchased;
    $pro['related_products']    = $related_arr;
    $pro['meta'] = $product_data->productMeta;
    $pro['video_link'] = $video_link;

    return $pro;
}

function getProductCompareApiData($product_data, $customer_id = '')
{

    $category               = $product_data->productCategory;
    $pro                    = [];
    $pro['id']              = $product_data->id;
    $pro['product_name']    = $product_data->product_name;
    $pro['category_name']   = $product_data->productCategory->name ?? '';
    $pro['brand_name']      = $product_data->productBrand->brand_name ?? '';
    $pro['hsn_code']        = $product_data->hsn_code;
    $pro['product_url']     = $product_data->product_url;
    $pro['sku']             = $product_data->sku;
    $pro['stock_status']    = $product_data->stock_status;
    $pro['is_featured']     = $product_data->is_featured;
    $pro['is_best_selling'] = $product_data->is_best_selling;
    $pro['is_new']          = $product_data->is_new;
    $pro['price']           = $product_data->mrp;
    $pro['strike_price']    = $product_data->strike_price;
    $pro['save_price']      = $product_data->strike_price - $product_data->mrp;
    $pro['discount_percentage'] = abs($product_data->discount_percentage);
    $pro['image']           = $product_data->base_image;
    $pro['max_quantity']    = $product_data->quantity;
    $imagePath              = $product_data->base_image;

    if (!Storage::exists($imagePath)) {
        $path               = asset('userImage/no_Image.jpg');
    } else {
        $url                = Storage::url($imagePath);
        $path               = asset($url);
    }

    $pro['image']                   = $path;
    return $pro;
}

if (!function_exists('getProductPrice')) {
    function getProductPrice($productsObjects)
    { // this function not used check all files confirm and delete it

        $strike_rate            = 0;
        $price                  = $productsObjects->mrp;
        $today                  = date('Y-m-d');
        /****
         * 1.check product discount is applied via product add/edit
         * 2.check overall discount is applied for product category
         */
        $discount               = [];
        $overall_discount_percentage = 0;
        $has_discount           = 'no';

        #condition 1:
        if ($today >= $productsObjects->sale_start_date && $today <= $productsObjects->sale_end_date) {

            $strike_rate        = $productsObjects->mrp;
            $price              = $productsObjects->sale_price;
            $has_discount       = 'yes';
            if ($productsObjects->productDiscount->discount_type == 'percentage') {
                $overall_discount_percentage += $productsObjects->productDiscount->discount_value;
            }
            $discount[]         = array('discount_type' => $productsObjects->productDiscount->discount_type, 'discount_value' => $productsObjects->productDiscount->discount_value, 'discount_name' => '');
        }

        #condition 2:
        $getDiscountDetails     = \DB::table('coupon_categories')
            ->select('product_categories.name', 'coupons.*')
            ->join('coupons', 'coupons.id', '=', 'coupon_categories.coupon_id')
            ->join('product_categories', 'product_categories.id', '=', 'coupon_categories.category_id')
            ->join('products', function ($join) {
                $join->on('products.category_id', '=', 'product_categories.id');
                $join->orOn('products.category_id', '=', 'product_categories.parent_id');
            })
            ->where('coupons.status', 'published')
            ->where('is_discount_on', 'yes')
            ->whereDate('coupons.start_date', '<=', date('Y-m-d'))
            ->whereDate('coupons.end_date', '>=', date('Y-m-d'))
            ->where('products.id', $productsObjects->id)
            ->orderBy('coupons.order_by', 'asc')
            ->get();

        $coupon_used            = [];

        if (isset($getDiscountDetails) && !empty($getDiscountDetails)) {
            foreach ($getDiscountDetails as $items) {

                // if( $items->quantity > $items->used_quantity ) {

                #check product amount greater than minimum order value
                if ($items->minimum_order_value <= $price) {
                    #then do percentage or fixed amount discount
                    $tmp['coupon_details']  = $items;

                    switch ($items->calculate_type) {

                        case 'percentage':
                            $strike_rate    = $price;
                            $tmp['discount_amount'] = percentageAmountOnly($price, $items->calculate_value);
                            $price          = percentage($price, $items->calculate_value);
                            $discount[]         = array('discount_type' => $items->calculate_type, 'discount_value' => $items->calculate_value, 'discount_name' => $items->coupon_name);
                            $overall_discount_percentage += $items->calculate_value;
                            $has_discount   = 'yes';
                            break;
                        case 'fixed_amount':
                            $strike_rate    = $price;
                            $tmp['discount_amount'] = $items->calculate_value;
                            $discount[]         = array('discount_type' => $items->calculate_type, 'discount_value' => $items->calculate_value, 'discount_name' => $items->coupon_name);
                            $price          = $price - $items->calculate_value;
                            $has_discount   = 'yes';
                            break;
                        default:

                            break;
                    }
                    $coupon_used[]          = $tmp;
                }
                // }
            }
        }

        #condition 3:
        $getDiscountCollection  = \DB::table('coupon_product_collection')
            ->select('coupons.*', 'product_collections.collection_name')
            ->join('coupons', 'coupons.id', '=', 'coupon_product_collection.coupon_id')
            ->join('product_collections', 'product_collections.id', '=', 'coupon_product_collection.product_collection_id')
            ->join('product_collections_products', 'product_collections_products.product_collection_id', '=', 'coupon_product_collection.product_collection_id')
            ->join('products', 'products.id', '=', 'product_collections_products.product_id')
            ->where('coupons.status', 'published')
            ->where('is_discount_on', 'yes')
            ->where('coupons.from_coupon', 'product_collection')
            ->whereDate('coupons.start_date', '<=', date('Y-m-d'))
            ->whereDate('coupons.end_date', '>=', date('Y-m-d'))
            ->where('products.id', $productsObjects->id)
            ->orderBy('coupons.order_by', 'asc')
            ->get();

        if (isset($getDiscountCollection) && !empty($getDiscountCollection)) {
            foreach ($getDiscountCollection as $items) {

                #check product amount greater than minimum order value
                if ($items->minimum_order_value <= $price) {
                    #then do percentage or fixed amount discount
                    $tmp['coupon_details']  = $items;

                    switch ($items->calculate_type) {

                        case 'percentage':
                            $strike_rate    = $price;
                            $tmp['discount_amount'] = percentageAmountOnly($price, $items->calculate_value);
                            $price          = percentage($price, $items->calculate_value);
                            $discount[]     = array('discount_type' => $items->calculate_type, 'discount_value' => $items->calculate_value, 'discount_name' => $items->coupon_name);
                            $overall_discount_percentage += $items->calculate_value;
                            $has_discount   = 'yes';
                            break;
                        case 'fixed_amount':
                            $strike_rate    = $price;
                            $tmp['discount_amount'] = $items->calculate_value;
                            $discount[]     = array('discount_type' => $items->calculate_type, 'discount_value' => $items->calculate_value, 'discount_name' => $items->coupon_name);
                            $price          = $price - $items->calculate_value;
                            $has_discount   = 'yes';
                            break;
                        default:

                            break;
                    }
                    $coupon_used[]          = $tmp;
                }
            }
        }

        $coupon_used['strike_rate']     = number_format($strike_rate, 2);
        $coupon_used['price']           = number_format($price, 2);
        $coupon_used['price_original']  = $price;
        $coupon_used['discount']        = $discount;
        $coupon_used['overall_discount_percentage'] = $overall_discount_percentage;

        return $coupon_used;
    }
}
if (!function_exists('getProductPrices')) {
    function getProductPrices($productsObjects)
    { // this function not used check all files confirm and delete it

        $strike_rate            = 0;
        $price                  = $productsObjects->mrp;
        $today                  = date('Y-m-d');
        /****
         * 1.check product discount is applied via product add/edit
         * 2.check overall discount is applied for product category
         */
        $discount               = [];
        $overall_discount_percentage = 0;
        $has_discount           = 'no';

        #condition 1:
        if (isset($productsObjects->productDiscount) && $productsObjects->productDiscount->discount_type == 'percentage') {
            if ($today >= $productsObjects->sale_start_date && $today <= $productsObjects->sale_end_date) {

                $strike_rate        = $productsObjects->mrp;
                $price              = $productsObjects->sale_price;
                $has_discount       = 'yes';
                if (isset($productsObjects->productDiscount) && $productsObjects->productDiscount->discount_type == 'percentage') {
                    $overall_discount_percentage += $productsObjects->productDiscount->discount_value;
                }
                $discount[]         = array('discount_type' => $productsObjects->productDiscount->discount_type, 'discount_value' => $productsObjects->productDiscount->discount_value, 'discount_name' => '');
            }
        } elseif (isset($productsObjects->productDiscount) && $productsObjects->productDiscount->discount_type == 'fixed_amount') {
            $strike_rate        = $productsObjects->mrp;
            $price              = $productsObjects->sale_price;
            $has_discount       = 'yes';
            $overall_discount_percentage = round($productsObjects->productDiscount->amount) ?? 0;
            $discount[]         =  $productsObjects->productDiscount->amount;
        }

        #condition 2:
        $getDiscountDetails     = \DB::table('coupon_categories')
            ->select('product_categories.name', 'coupons.*')
            ->join('coupons', 'coupons.id', '=', 'coupon_categories.coupon_id')
            ->join('product_categories', 'product_categories.id', '=', 'coupon_categories.category_id')
            ->join('products', function ($join) {
                $join->on('products.category_id', '=', 'product_categories.id');
                $join->orOn('products.category_id', '=', 'product_categories.parent_id');
            })
            ->where('coupons.status', 'published')
            ->where('is_discount_on', 'yes')
            ->whereDate('coupons.start_date', '<=', date('Y-m-d'))
            ->whereDate('coupons.end_date', '>=', date('Y-m-d'))
            ->where('products.id', $productsObjects->id)
            ->orderBy('coupons.order_by', 'asc')
            ->get();

        $coupon_used            = [];

        if (isset($getDiscountDetails) && !empty($getDiscountDetails)) {
            foreach ($getDiscountDetails as $items) {

                // if( $items->quantity > $items->used_quantity ) {

                #check product amount greater than minimum order value
                if ($items->minimum_order_value <= $price) {
                    #then do percentage or fixed amount discount
                    $tmp['coupon_details']  = $items;

                    switch ($items->calculate_type) {

                        case 'percentage':
                            $strike_rate    = $price;
                            $tmp['discount_amount'] = percentageAmountOnly($price, $items->calculate_value);
                            $price          = percentage($price, $items->calculate_value);
                            $discount[]         = array('discount_type' => $items->calculate_type, 'discount_value' => $items->calculate_value, 'discount_name' => $items->coupon_name);
                            $overall_discount_percentage += $items->calculate_value;
                            $has_discount   = 'yes';
                            break;
                        case 'fixed_amount':
                            $strike_rate    = $price;
                            $tmp['discount_amount'] = $items->calculate_value;
                            $discount[]         = array('discount_type' => $items->calculate_type, 'discount_value' => $items->calculate_value, 'discount_name' => $items->coupon_name);
                            $price          = $price - $items->calculate_value;
                            $has_discount   = 'yes';
                            break;
                        default:

                            break;
                    }
                    $coupon_used[]          = $tmp;
                }
                // }
            }
        }

        #condition 3:
        $getDiscountCollection  = \DB::table('coupon_product_collection')
            ->select('coupons.*', 'product_collections.collection_name')
            ->join('coupons', 'coupons.id', '=', 'coupon_product_collection.coupon_id')
            ->join('product_collections', 'product_collections.id', '=', 'coupon_product_collection.product_collection_id')
            ->join('product_collections_products', 'product_collections_products.product_collection_id', '=', 'coupon_product_collection.product_collection_id')
            ->join('products', 'products.id', '=', 'product_collections_products.product_id')
            ->where('coupons.status', 'published')
            ->where('is_discount_on', 'yes')
            ->where('coupons.from_coupon', 'product_collection')
            ->whereDate('coupons.start_date', '<=', date('Y-m-d'))
            ->whereDate('coupons.end_date', '>=', date('Y-m-d'))
            ->where('products.id', $productsObjects->id)
            ->orderBy('coupons.order_by', 'asc')
            ->get();

        if (isset($getDiscountCollection) && !empty($getDiscountCollection)) {
            foreach ($getDiscountCollection as $items) {

                #check product amount greater than minimum order value
                if ($items->minimum_order_value <= $price) {
                    #then do percentage or fixed amount discount
                    $tmp['coupon_details']  = $items;

                    switch ($items->calculate_type) {

                        case 'percentage':
                            $strike_rate    = $price;
                            $tmp['discount_amount'] = percentageAmountOnly($price, $items->calculate_value);
                            $price          = percentage($price, $items->calculate_value);
                            $discount[]     = array('discount_type' => $items->calculate_type, 'discount_value' => $items->calculate_value, 'discount_name' => $items->coupon_name);
                            $overall_discount_percentage += $items->calculate_value;
                            $has_discount   = 'yes';
                            break;
                        case 'fixed_amount':
                            $strike_rate    = $price;
                            $tmp['discount_amount'] = $items->calculate_value;
                            $discount[]     = array('discount_type' => $items->calculate_type, 'discount_value' => $items->calculate_value, 'discount_name' => $items->coupon_name);
                            $price          = $price - $items->calculate_value;
                            $has_discount   = 'yes';
                            break;
                        default:

                            break;
                    }
                    $coupon_used[]          = $tmp;
                }
            }
        }

        $coupon_used['strike_rate']     = number_format($strike_rate, 2);
        $coupon_used['price']           = number_format($price, 2);
        $coupon_used['price_original']  = $price;
        $coupon_used['discount']        = $discount;
        $coupon_used['overall_discount_percentage'] = $overall_discount_percentage;

        return $coupon_used;
    }
}

function getIndianCurrency(float $number)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(
        0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
    );
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_length) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
}

function getDiscountPercentage($mop, $mrp)
{
    if ($mop != 0.0 && $mrp != 0.0) {
        return abs(round((($mop / $mrp) * 100) - 100));
    }
    return 0;
}

function addonHasSelected($item_id, $product_id, $cart_id)
{
    $cart_data = CartProductAddon::where(['cart_id' => $cart_id, 'product_id' => $product_id, 'addon_item_id' => $item_id])->first();
    if ($cart_data) {
        return true;
    } else {
        return false;
    }
}

function getEndWarrantyDate($warranty_period, $warranty_type)
{
    if ($warranty_period && $warranty_type) {
        switch ($warranty_type) {
            case 'Year':
                return date('Y-m-d', strtotime('+' . $warranty_period . ' year'));
                break;
            case 'Month':
                return date("Y-m-d", strtotime("+" . $warranty_period . " month"));
                break;

            default:
                //for day case
                return date("Y-m-d", strtotime("+" . $warranty_period . " day"));
                break;
        }
    }
}

function generateOtp()
{

    $otp = random_int(100000, 999999);
    $order_info = Order::where('delivery_otp', $otp)->first();
    if ($order_info) {
        $otp = random_int(100000, 999999);
    }
    return $otp;
}


function ccEncrypt($plainText, $key)
{
    $key = ccHextobin(md5($key));
    $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
    $openMode = openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
    $encryptedText = bin2hex($openMode);
    return $encryptedText;
}

function ccDecrypt($encryptedText, $key)
{
    $key = ccHextobin(md5($key));
    $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
    $encryptedText = ccHextobin($encryptedText);
    $decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
    return $decryptedText;
}
//*********** Padding Function *********************

function pkcs5_pad($plainText, $blockSize)
{
    $pad = $blockSize - (strlen($plainText) % $blockSize);
    return $plainText . str_repeat(chr($pad), $pad);
}

//********** Hexadecimal to Binary function for php 4.0 version ********

function ccHextobin($hexString)
{
    $length = strlen($hexString);
    $binString = "";
    $count = 0;
    while ($count < $length) {
        $subString = substr($hexString, $count, 2);
        $packedString = pack("H*", $subString);
        if ($count == 0) {
            $binString = $packedString;
        } else {
            $binString .= $packedString;
        }

        $count += 2;
    }
    return $binString;
}
