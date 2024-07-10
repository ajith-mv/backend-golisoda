<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartProductAddon;
use App\Models\CartProductVariationOption;
use App\Models\Master\Variation;
use App\Models\Offers\CouponCategory;
use App\Models\Offers\Coupons;
use App\Models\Order;
use App\Models\Product\Product;
use App\Models\Product\ProductVariationOption;
use App\Models\Settings\Tax;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class Couponcontroller extends Controller
{
    public function applyCoupon(Request $request)
    {
        $coupon_code = $request->coupon_code;
        $customer_id = $request->customer_id;
        $shipping_fee_id = $request->shipping_fee_id ?? '';
        $carts     = Cart::where('customer_id', $customer_id)->get();
        $isApplied = Order::where('customer_id', $customer_id)->where('coupon_code', $coupon_code)->first();

        if (!is_null($isApplied)) {
            return response([
                "status" => 'error',
                "message" => 'You have already used the coupon.'
            ]);
        }

        if ($carts) {
            $coupon = Coupons::where('coupon_code', $coupon_code)
                ->where('is_discount_on', 'no')
                ->whereDate('coupons.start_date', '<=', date('Y-m-d'))
                ->whereDate('coupons.end_date', '>=', date('Y-m-d'))
                ->first();

            if (isset($coupon) && !empty($coupon)) {
                /**
                 * 1. check quantity is available to use
                 * 2. check coupon can apply for cart products
                 * 3. get percentage or fixed amount
                 * 4. get percentage or fixed amount For Total Order Amount
                 * 
                 * coupon type 1- product, 2-customer, 3-category
                 */
                $has_product = 0;
                $product_amount = 0;
                $has_product_error = 0;
                $overall_discount_percentage = 0;
                $couponApplied = [];

                if ($coupon->quantity > 0) {
                    switch ($coupon->coupon_type) {
                        case '1':
                            # product ...
                            if (isset($coupon->couponProducts) && !empty($coupon->couponProducts)) {
                                $couponApplied['coupon_type'] = array('discount_type' => $coupon->calculate_type, 'discount_value' => $coupon->calculate_value);
                                foreach ($coupon->couponProducts as $items) {
                                    $cartCountcheck = Cart::where('customer_id', $customer_id)->where('product_id', $items->product_id)->first();
                                    if (isset($cartCountcheck) && is_null($cartCountcheck->id)) {
                                        $response['status'] = 'error';
                                        $response['message'] = 'Coupon not applicable';
                                        return $response ?? '';
                                    }

                                    $cartCountNew = Cart::where('customer_id', $customer_id)->where('product_id', $items->product_id)->pluck('id')->toArray();
                                    $product_info = Product::find($items->product_id);

                                    $cart_variation_options = CartProductVariationOption::where('product_id', $items->product_id)->whereIn('cart_id', $cartCountNew)->groupBy('cart_id')->selectRaw("gbs_cart_product_variation_options.*, SUM(amount) AS total_amount")->get();
                                    foreach ($cart_variation_options as $cart_variation_option) {

                                        if (isset($cart_variation_option) && !empty($cart_variation_option)) {
                                            $cartData = Cart::find($cart_variation_option->cart_id);
                                            $strike_price = $product_info->strike_price + $cart_variation_option->total_amount;
                                            $cartData->sub_total = round($strike_price * $cartData->quantity);
                                            $cartData->coupon_id = $coupon->id;
                                            $cartData->update();
                                        } else {
                                            if (isset($cartCountcheck) && $cartCountcheck->quantity != NUll) {
                                                $cartCountcheck->sub_total = round($product_info->strike_price * $cartCountcheck->quantity);

                                                $cartCountcheck->update();
                                            }
                                        }
                                    }

                                    $cartCount = Cart::where('customer_id', $customer_id)->where('product_id', $items->product_id)->selectRaw("gbs_carts.*, SUM(quantity) as quantity, SUM(sub_total) as sub_total")->groupBy('product_id')->first();
                                    if ($cartCount) {
                                        if ($cartCount->sub_total >= $coupon->minimum_order_value) {
                                            /**
                                             * Check percentage or fixed amount
                                             */
                                            switch ($coupon->calculate_type) {

                                                case 'percentage':
                                                    $product_amount += percentageAmountOnly($cartCount->sub_total, $coupon->calculate_value);
                                                    $tmp['discount_amount'] = percentageAmountOnly($cartCount->sub_total, $coupon->calculate_value);
                                                    $tmp['product_id'] = $cartCount->product_id;
                                                    $tmp['coupon_applied_amount'] = $cartCount->sub_total;
                                                    // $tmp['coupon_type'] = array('discount_type' => $coupon->calculate_type, 'discount_value' => $coupon->calculate_value);
                                                    $overall_discount_percentage += $coupon->calculate_value;
                                                    $has_product++;
                                                    $couponApplied[] = $tmp;
                                                    break;
                                                case 'fixed_amount':
                                                    $product_amount += $coupon->calculate_value;
                                                    $tmp['discount_amount'] = $coupon->calculate_value;
                                                    $tmp['product_id'] = $cartCount->product_id;
                                                    $tmp['coupon_applied_amount'] = $cartCount->sub_total;
                                                    // $tmp['coupon_type'] = array('discount_type' => $coupon->calculate_type, 'discount_value' => $coupon->calculate_value);
                                                    $has_product++;
                                                    $couponApplied[] = $tmp;

                                                    break;
                                                default:

                                                    break;
                                            }

                                            $response['coupon_info'] = $couponApplied;
                                            $response['overall_applied_discount'] = $overall_discount_percentage;
                                            $response['coupon_amount'] = $product_amount;
                                            $response['coupon_id'] = $coupon->id;
                                            $response['coupon_code'] = $coupon->coupon_code;
                                            $response['status'] = 'success';
                                            $response['message'] = 'Coupon applied';
                                            /** upddate cart coupon amount */
                                            if (isset($shipping_fee_id) && !empty($shipping_fee_id)) {
                                                $shippingfee_info = ShippingCharge::select('id', 'shipping_title', 'minimum_order_amount', 'charges', 'is_free')->find($shipping_fee_id);
                                            }
                                            $update_data = [
                                                'coupon_id' => $coupon->id,
                                                'coupon_amount' => $product_amount,
                                                'coupon_percentage' => $coupon->calculate_value ?? null,
                                                'coupon_code' => $coupon->coupon_code ?? null,
                                                'coupon_type' => $coupon->calculate_type ?? null,
                                                'shipping_fee_id' => $shippingfee_info->id ?? null,
                                                'shipping_fee' => $shippingfee_info->charges ?? null
                                            ];
                                            DB::table('carts')->where('id', $cartCount->id)->update($update_data);
                                            $response['cart_info'] = $this->getCartListAll($customer_id, null, null, null, $shipping_fee_id, $response['coupon_amount']);
                                        }
                                    } else {
                                        $has_product_error++;
                                    }
                                }
                                if ($has_product == 0 && $has_product_error > 0) {
                                    $is_coupon = 0;
                                    $response['status'] = 'error';
                                    $response['message'] = 'Cart order does not meet coupon minimum order amount';
                                }
                            } else {
                                $response['status'] = 'error';
                                $response['message'] = 'Coupon not applicable';
                            }
                            break;
                            // case '2':
                            //     # customer ...
                            //     break;
                        case '4':
                            # minimum order value ...
                            $cart_ids = [];
                            $cartCheck = Cart::selectRaw('gbs_carts.id, GROUP_CONCAT(gbs_carts.id) as cart_id')
                                ->join('products', 'products.id', '=', 'carts.product_id')
                                ->where('carts.customer_id', $customer_id)
                                // ->groupBy('carts.product_id')
                                ->first();
                            if (isset($cartCheck) && is_null($cartCheck->id)) {
                                $response['status'] = 'error';
                                $response['message'] = 'Coupon not applicable';
                                return $response ?? '';
                            }
                            $cart_ids = explode(',', $cartCheck->cart_id);
                            foreach ($cart_ids as $cart_id) {
                                $cartData = Cart::find($cart_id);
                                $cart_variation_options = CartProductVariationOption::where('product_id', $cartData->product_id)->where('cart_id', $cart_id)->groupBy('cart_id')->selectRaw("SUM(amount) AS total_amount")->first();
                                $product_info = Product::find($cartData->product_id);
                                if (isset($cart_variation_options) && !empty($cart_variation_options)) {
                                    $strike_price = $product_info->strike_price + $cart_variation_options->total_amount;
                                    $cartData->sub_total = round($strike_price * $cartData->quantity);
                                    $cartData->coupon_id = $coupon->id;
                                    $cartData->update();
                                } else {
                                    $cartData->coupon_id = $coupon->id;
                                    $cartData->sub_total = round($product_info->strike_price * $cartData->quantity);
                                    $cartData->update();
                                }
                            }
                            $checkCartData = Cart::selectRaw('gbs_carts.id, SUM(gbs_carts.sub_total) as category_total')
                                ->whereIn('id', $cart_ids)
                                ->where('coupon_id', $coupon->id)
                                ->where('customer_id', $customer_id)
                                ->groupBy('coupon_id')
                                ->first();
                            if (isset($checkCartData) && ($checkCartData->category_total < $coupon->minimum_order_value)) {
                                $response['status'] = 'error';
                                $response['message'] = 'Coupon not applicable';
                                return $response ?? '';
                            }
                            if (isset($checkCartData) && !empty($checkCartData)) {

                                if ($checkCartData->category_total >= $coupon->minimum_order_value) {
                                    /**
                                     * check percentage or fixed amount
                                     */
                                    switch ($coupon->calculate_type) {

                                        case 'percentage':
                                            $product_amount = percentageAmountOnly($checkCartData->category_total, $coupon->calculate_value);
                                            $tmp['discount_amount'] = percentageAmountOnly($checkCartData->category_total, $coupon->calculate_value);
                                            $tmp['coupon_id'] = $coupon->id;
                                            $tmp['coupon_code'] = $coupon->coupon_code;
                                            $tmp['coupon_applied_amount'] = number_format((float)$checkCartData->category_total, 2, '.', '');
                                            $tmp['coupon_type'] = array('discount_type' => $coupon->calculate_type, 'discount_value' => $coupon->calculate_value);
                                            $overall_discount_percentage = $coupon->calculate_value;
                                            $couponApplied = $tmp;
                                            break;
                                        case 'fixed_amount':
                                            $product_amount += $coupon->calculate_value;
                                            $tmp['discount_amount'] = $coupon->calculate_value;
                                            $tmp['coupon_id'] = $coupon->id;
                                            $tmp['coupon_code'] = $coupon->coupon_code;
                                            $tmp['coupon_applied_amount'] = number_format((float)$checkCartData->sub_total, 2, '.', '');
                                            $tmp['coupon_type']         = array('discount_type' => $coupon->calculate_type, 'discount_value' => $coupon->calculate_value);
                                            $has_product++;
                                            $couponApplied[] = $tmp;

                                            break;
                                        default:

                                            break;
                                    }

                                    $response['coupon_info'] = $couponApplied;
                                    $response['overall_applied_discount'] = $overall_discount_percentage;
                                    $response['coupon_amount'] = number_format((float)$product_amount, 2, '.', '');
                                    $response['coupon_id'] = $coupon->id;
                                    $response['coupon_code'] = $coupon->coupon_code;
                                    $response['status'] = 'success';
                                    $response['message'] = 'Coupon applied';

                                    /** upddate cart coupon amount */
                                    if (isset($shipping_fee_id) && !empty($shipping_fee_id)) {
                                        $shippingfee_info = ShippingCharge::select('id', 'shipping_title', 'minimum_order_amount', 'charges', 'is_free')->find($shipping_fee_id);
                                    }
                                    $update_data = [
                                        'coupon_id' => $coupon->id,
                                        'coupon_amount' => $product_amount,
                                        'coupon_percentage' => $coupon->calculate_value ?? null,
                                        'coupon_code' => $coupon->coupon_code ?? null,
                                        'coupon_type' => $coupon->calculate_type ?? null,
                                        'shipping_fee_id' => $shippingfee_info->id ?? null,
                                        'shipping_fee' => $shippingfee_info->charges ?? null
                                    ];
                                    DB::table('carts')->where('id', $checkCartData->id)->update($update_data);

                                    $response['cart_info'] = $this->getCartListAll($customer_id, null, null, null, $shipping_fee_id, $response['coupon_amount']);
                                }
                            } else {
                                $is_coupon = 0;
                                $response['status'] = 'error';
                                $response['message'] = 'Cart order does not meet coupon minimum order amount';
                            }
                            break;
                        case '3':

                            # category ...
                            $cart_ids = [];
                            $cartCheck = Cart::selectRaw('gbs_carts.id, GROUP_CONCAT(gbs_carts.id) as cart_id')
                                ->join('products', 'products.id', '=', 'carts.product_id')
                                ->join('product_categories', 'product_categories.id', '=', 'products.category_id')
                                ->join('coupon_categories', function ($join) {
                                    $join->on('coupon_categories.category_id', '=', 'product_categories.id');
                                    // $join->orOn('coupon_categories.category_id', '=', 'product_categories.parent_id');
                                })
                                ->where('coupon_categories.coupon_id', $coupon->id)
                                ->where('carts.customer_id', $customer_id)
                                // ->groupBy('carts.product_id')
                                ->first();
                            if (isset($cartCheck) && is_null($cartCheck->id)) {
                                $response['status'] = 'error';
                                $response['message'] = 'Coupon not applicable';
                                return $response ?? '';
                            }
                            $cart_ids = explode(',', $cartCheck->cart_id);
                            foreach ($cart_ids as $cart_id) {
                                $cartData = Cart::find($cart_id);
                                $cart_variation_options = CartProductVariationOption::where('product_id', $cartData->product_id)->where('cart_id', $cart_id)->groupBy('cart_id')->selectRaw("SUM(amount) AS total_amount")->first();
                                $product_info = Product::find($cartData->product_id);
                                if (isset($cart_variation_options) && !empty($cart_variation_options)) {
                                    $strike_price = $product_info->strike_price + $cart_variation_options->total_amount;
                                    $cartData->sub_total = round($strike_price * $cartData->quantity);
                                    $cartData->coupon_id = $coupon->id;
                                    $cartData->update();
                                } else {
                                    $cartData->coupon_id = $coupon->id;
                                    $cartData->sub_total = round($product_info->strike_price * $cartData->quantity);
                                    $cartData->update();
                                }
                            }
                            $checkCartData = Cart::selectRaw('gbs_carts.id, SUM(gbs_carts.sub_total) as category_total')
                                ->whereIn('id', $cart_ids)
                                ->where('coupon_id', $coupon->id)
                                ->where('customer_id', $customer_id)
                                ->groupBy('coupon_id')
                                ->first();
                            if (isset($checkCartData) && !empty($checkCartData)) {

                                if ($checkCartData->category_total >= $coupon->minimum_order_value) {
                                    /**
                                     * check percentage or fixed amount
                                     */
                                    switch ($coupon->calculate_type) {

                                        case 'percentage':
                                            $product_amount = percentageAmountOnly($checkCartData->category_total, $coupon->calculate_value);
                                            $tmp['discount_amount'] = percentageAmountOnly($checkCartData->category_total, $coupon->calculate_value);
                                            $tmp['coupon_id'] = $coupon->id;
                                            $tmp['coupon_code'] = $coupon->coupon_code;
                                            $tmp['coupon_applied_amount'] = number_format((float)$checkCartData->category_total, 2, '.', '');
                                            $tmp['coupon_type'] = array('discount_type' => $coupon->calculate_type, 'discount_value' => $coupon->calculate_value);
                                            $overall_discount_percentage = $coupon->calculate_value;
                                            $couponApplied = $tmp;
                                            break;
                                        case 'fixed_amount':
                                            $product_amount += $coupon->calculate_value;
                                            $tmp['discount_amount'] = $coupon->calculate_value;
                                            $tmp['coupon_id'] = $coupon->id;
                                            $tmp['coupon_code'] = $coupon->coupon_code;
                                            $tmp['coupon_applied_amount'] = number_format((float)$checkCartData->sub_total, 2, '.', '');
                                            $tmp['coupon_type']         = array('discount_type' => $coupon->calculate_type, 'discount_value' => $coupon->calculate_value);
                                            $has_product++;
                                            $couponApplied[] = $tmp;

                                            break;
                                        default:

                                            break;
                                    }

                                    $response['coupon_info'] = $couponApplied;
                                    $response['overall_applied_discount'] = $overall_discount_percentage;
                                    $response['coupon_amount'] = number_format((float)$product_amount, 2, '.', '');
                                    $response['coupon_id'] = $coupon->id;
                                    $response['coupon_code'] = $coupon->coupon_code;
                                    $response['status'] = 'success';
                                    $response['message'] = 'Coupon applied';

                                    /** upddate cart coupon amount */
                                    if (isset($shipping_fee_id) && !empty($shipping_fee_id)) {
                                        $shippingfee_info = ShippingCharge::select('id', 'shipping_title', 'minimum_order_amount', 'charges', 'is_free')->find($shipping_fee_id);
                                    }
                                    $update_data = [
                                        'coupon_id' => $coupon->id,
                                        'coupon_amount' => $product_amount,
                                        'coupon_percentage' => $coupon->calculate_value ?? null,
                                        'coupon_code' => $coupon->coupon_code ?? null,
                                        'coupon_type' => $coupon->calculate_type ?? null,
                                        'shipping_fee_id' => $shippingfee_info->id ?? null,
                                        'shipping_fee' => $shippingfee_info->charges ?? null
                                    ];
                                    DB::table('carts')->where('id', $checkCartData->id)->update($update_data);

                                    $response['cart_info'] = $this->getCartListAll($customer_id, null, null, null, $shipping_fee_id, $response['coupon_amount']);
                                }
                            } else {
                                $is_coupon = 0;
                                $response['status'] = 'error';
                                $response['message'] = 'Cart order does not meet coupon minimum order amount';
                            }
                            break;
                        case '5':
                            # brands ...
                            $cart_ids = [];
                            $cartCheck = Cart::selectRaw('gbs_carts.id, GROUP_CONCAT(gbs_carts.id) as cart_id')
                                ->join('products', 'products.id', '=', 'carts.product_id')
                                ->join('brands', 'brands.id', '=', 'products.brand_id')
                                ->join('coupon_brands', function ($join) {
                                    $join->on('coupon_brands.brand_id', '=', 'brands.id');
                                })
                                ->where('coupon_brands.coupon_id', $coupon->id)
                                ->where('carts.customer_id', $customer_id)
                                ->first();
                            if (isset($cartCheck) && is_null($cartCheck->id)) {
                                $response['status'] = 'error';
                                $response['message'] = 'Coupon not applicable';
                                return $response ?? '';
                            }


                            $cart_ids = explode(',', $cartCheck->cart_id);
                            foreach ($cart_ids as $cart_id) {
                                $cartData = Cart::find($cart_id);
                                $cart_variation_options = CartProductVariationOption::where('product_id', $cartData->product_id)->where('cart_id', $cart_id)->groupBy('cart_id')->selectRaw("SUM(amount) AS total_amount")->first();
                                $product_info = Product::find($cartData->product_id);
                                if (isset($cart_variation_options) && !empty($cart_variation_options)) {
                                    $strike_price = $product_info->strike_price + $cart_variation_options->total_amount;
                                    $cartData->sub_total = round($strike_price * $cartData->quantity);
                                    $cartData->coupon_id = $coupon->id;
                                    $cartData->update();
                                } else {
                                    $cartData->coupon_id = $coupon->id;
                                    $cartData->sub_total = round($product_info->strike_price * $cartData->quantity);
                                    $cartData->update();
                                }
                            }
                            $checkCartData = Cart::selectRaw('gbs_carts.id, SUM(gbs_carts.sub_total) as category_total')
                                ->whereIn('id', $cart_ids)
                                ->where('coupon_id', $coupon->id)
                                ->where('customer_id', $customer_id)
                                ->groupBy('coupon_id')
                                ->first();
                            if (isset($checkCartData) && !empty($checkCartData)) {

                                if ($checkCartData->category_total >= $coupon->minimum_order_value) {
                                    /**
                                     * check percentage or fixed amount
                                     */
                                    switch ($coupon->calculate_type) {

                                        case 'percentage':
                                            $product_amount = percentageAmountOnly($checkCartData->category_total, $coupon->calculate_value);
                                            log::debug($product_amount);
                                            $tmp['discount_amount'] = percentageAmountOnly($checkCartData->category_total, $coupon->calculate_value);
                                            $tmp['coupon_id'] = $coupon->id;
                                            $tmp['coupon_code'] = $coupon->coupon_code;
                                            $tmp['coupon_applied_amount'] = number_format((float)$checkCartData->category_total, 2, '.', '');
                                            $tmp['coupon_type'] = array('discount_type' => $coupon->calculate_type, 'discount_value' => $coupon->calculate_value);
                                            $overall_discount_percentage = $coupon->calculate_value;
                                            $couponApplied = $tmp;
                                            break;
                                        case 'fixed_amount':
                                            $product_amount += $coupon->calculate_value;
                                            $tmp['discount_amount'] = $coupon->calculate_value;
                                            $tmp['coupon_id'] = $coupon->id;
                                            $tmp['coupon_code'] = $coupon->coupon_code;
                                            $tmp['coupon_applied_amount'] = number_format((float)$checkCartData->sub_total, 2, '.', '');
                                            $tmp['coupon_type']         = array('discount_type' => $coupon->calculate_type, 'discount_value' => $coupon->calculate_value);
                                            $has_product++;
                                            $couponApplied[] = $tmp;

                                            break;
                                        default:

                                            break;
                                    }

                                    $response['coupon_info'] = $couponApplied;
                                    $response['overall_applied_discount'] = $overall_discount_percentage;
                                    $response['coupon_amount'] = number_format((float)$product_amount, 2, '.', '');
                                    $response['coupon_id'] = $coupon->id;
                                    $response['coupon_code'] = $coupon->coupon_code;
                                    $response['status'] = 'success';
                                    $response['message'] = 'Coupon applied';
                                    $update_data = [
                                        'coupon_id' => $coupon->id,
                                        'coupon_amount' => $product_amount,
                                        'coupon_percentage' => $coupon->calculate_value ?? null,
                                        'coupon_code' => $coupon->coupon_code ?? null,
                                        'coupon_type' => $coupon->calculate_type ?? null,
                                        'shipping_fee_id' => $shippingfee_info->id ?? null,
                                        'shipping_fee' => $shippingfee_info->charges ?? null
                                    ];
                                    DB::table('carts')->where('id', $checkCartData->id)->update($update_data);
                                    $response['cart_info'] = $this->getCartListAll($customer_id, null, null, null, $shipping_fee_id, $response['coupon_amount']);
                                } else {
                                    $is_coupon = 0;
                                    $response['status'] = 'error';
                                    $response['message'] = 'Cart order does not meet coupon minimum order amount';
                                }
                            } else {
                                $response['status'] = 'error';
                                $response['message'] = 'Coupon not applicable';
                            }
                            break;

                        default:
                            # code...
                            break;
                    }
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Coupon Limit reached';
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Coupon code not available';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'There is no products on the cart';
        }
        return $response ?? '';
    }

    public function removeCoupon(Request $request)
    {
        $customer_id = $request->customer_id;
        $shipping_fee_id = $request->shipping_fee_id ?? '';
        $carts          = Cart::where('customer_id', $customer_id)->get();
        $update_data = [
            'coupon_id' => null,
            'coupon_amount' => null,
            'coupon_percentage' => null,
            'coupon_code' => null,
            'coupon_type' => null,
        ];
        if (count($carts) > 0) {
            foreach ($carts as $cart) {
                $cart_data = Cart::find($cart->id);
                $cart_data->sub_total = $cart_data->price * $cart->quantity;
                $cart_data->update();
            }
        }

        DB::table('carts')->where('customer_id', $customer_id)->update($update_data);
        $response['cart_info'] = $this->getCartListAll(
            $customer_id,
            null,
            null,
            null,
            $shipping_fee_id,
            null,
            'remove'
        );
        $response['status']    = 'success';
        $response['message']   = 'Coupon removed successfully';
        return $response;
    }


    function getCartListAll($customer_id = null, $guest_token = null,  $shipping_info = null, $shipping_type = null, $selected_shipping = null, $coupon_amount = null, $type = null)
    {

        if ($selected_shipping) {
            $shippingfee_info = ShippingCharge::select('id', 'shipping_title', 'minimum_order_amount', 'charges', 'is_free')->find($selected_shipping);
        }

        $checkCart          = Cart::with(['products', 'products.productCategory'])->when($customer_id != '', function ($q) use ($customer_id) {
            $q->where('customer_id', $customer_id);
        })->when($customer_id == '' && $guest_token != '', function ($q) use ($guest_token) {
            $q->where('guest_token', $guest_token);
        })->get();

        $tmp                         = [];
        $grand_total                 = 0;
        $tax_total                   = 0;
        $product_tax_exclusive_total = 0;
        $tax_percentage              = 0;
        $total_addon_amount          = 0;
        $cartTemp                    = [];
        $brand_array                 = [];
        $has_pickup_store            = true;
        if (isset($checkCart) && !empty($checkCart)) {
            foreach ($checkCart as $citems) {

                $items = $citems->products;
                $tax = [];
                $tax_percentage = 0;
                $product_info = Product::find($citems->product_id);
                $variation_option_id = [];

                $total_variation_amount = 0;
                $total_discount_amount = 0;
                if (isset($citems->variationOptions) && !empty($citems->variationOptions)) {
                    foreach ($citems->variationOptions as $variationids) {
                        $variation_option_id[] = $variationids->variation_option_id;
                    }
                }
                if (isset($variation_option_id) && !empty($variation_option_id)) {
                    $variation_option_data = ProductVariationOption::whereIn('id', $variation_option_id)
                        ->where('product_id', $product_info->id)
                        ->get();

                    foreach ($variation_option_data as $value) {

                        $variation = Variation::where('id', $value->variation_id)->first();
                        if ($variation) {
                            $title = $variation->title ?? '';
                            $selected_value[$title] = $value->value ?? '';
                            $amount = $value->amount;
                            $discount_amount = $value->discount_amount;
                            $total_variation_amount = $total_variation_amount + $amount;
                            $total_discount_amount = $total_discount_amount + $discount_amount;
                        }
                    }
                }
                // if($citems){

                //          if(isset($product_info->productCategory->tax)){
                //               $tax_data=($product_info->productCategory->tax->pecentage / 100);  
                //             }else if(isset($product_info->tax)){
                //               $tax_data=($product_info->tax->pecentage / 100);  
                //             }else{
                //                 $tax_data=(0 / 100);
                //             }
                //         }
                if (isset($selected_value) && (!empty($selected_value))) {
                    $items->mrp = ($items->strike_price + $total_variation_amount) - $total_discount_amount;;
                    $strike_price = $items->strike_price + $total_variation_amount;
                    // $items->discount_percentage = ($total_discount_amount > 0) ? $items->discount_percentage : 0;
                    $items->discount_percentage = ($total_discount_amount > 0) ? getDiscountPercentage($items->mrp, $strike_price) : 0;
                } else {
                    $strike_price = $items->strike_price;
                }
                $category               = $items->productCategory;
                if ($type != 'remove' && isset($citems->coupon_id)) {
                    // $price=$items->strike_price /(1+$tax_data);
                    $price_with_tax         = $strike_price;
                    $citems->sub_total = round($price_with_tax * $citems->quantity);
                    // log::info($citems->sub_total . 'citems sub total if');
                    $citems->update();
                } else {
                    //   $price=$items->mrp /(1+$tax_data);
                    $price_with_tax         = $items->mrp;
                    $citems->sub_total = round($price_with_tax * $citems->quantity);
                    // log::info($citems->sub_total . 'citems sub total else');
                    $citems->update();
                }
                if (isset($category->parent->tax_id) && !empty($category->parent->tax_id)) {
                    $tax_info = Tax::find($category->parent->tax_id);
                } else if (isset($category->tax_id) && !empty($category->tax_id)) {
                    $tax_info = Tax::find($category->tax_id);
                }
                // dump( $citems );
                if (isset($tax_info) && !empty($tax_info)) {
                    $tax = getAmountExclusiveTax($price_with_tax, $product_info->productCategory->tax->pecentage ?? 12);
                    $tax_total =  $tax_total + ($tax['gstAmount'] * $citems->quantity) ?? 0;
                    $product_tax_exclusive_total = $product_tax_exclusive_total + ($tax['basePrice'] * $citems->quantity);
                    // print_r( $product_tax_exclusive_total );
                    $tax_percentage         = $tax['tax_percentage'] ?? 0;
                } else {
                    $product_tax_exclusive_total = $product_tax_exclusive_total + $citems->sub_total;
                }

                /**
                 * addon amount calculated here
                 */
                $addonItems = CartProductAddon::where(['cart_id' => $citems->id, 'product_id' => $items->id])->get();

                $addon_total = 0;
                if (isset($addonItems) && !empty($addonItems)) {
                    foreach ($addonItems as $addItems) {

                        $addons = [];
                        $addons['addon_id'] = $addItems->addonItem->addon->id;
                        $addons['title'] = $addItems->addonItem->addon->title;
                        $addons['description'] = $addItems->addonItem->addon->description;

                        if (!Storage::exists($addItems->addonItem->addon->icon)) {
                            $path               = asset('assets/logo/no_Image.jpg');
                        } else {
                            $url                = Storage::url($addItems->addonItem->addon->icon);
                            $path               = asset($url);
                        }
                        $addons['addon_item_id'] = $addItems->addonItem->id;
                        $addons['icon'] = $path;
                        $addons['addon_item_label'] = $addItems->addonItem->label;
                        $addons['amount'] = $addItems->addonItem->amount;
                        $addon_total += $addItems->addonItem->amount;
                        $used_addons[] = $addons;
                    }
                }

                $total_addon_amount += $addon_total;

                $pro                    = [];
                $pro['id']              = $items->id;
                $pro['tax']             = $tax;
                $pro['tax_percentage']  = $tax_percentage;
                $pro['hsn_no']          = $items->hsn_code ?? null;
                $pro['product_name']    = $items->product_name;
                $pro['category_name']   = $category->name ?? '';
                $pro['brand_name']      = $items->productBrand->brand_name ?? '';
                $pro['hsn_code']        = $items->hsn_code;
                $pro['product_url']     = $items->product_url;
                $pro['sku']             = $items->sku;
                $pro['stock_status']    = $items->stock_status;
                $pro['is_featured']     = $items->is_featured;
                $pro['is_best_selling'] = $items->is_best_selling;
                $pro['price']           = $items->mrp;
                $pro['strike_price']    = $strike_price;
                $pro['save_price']      = $strike_price - $items->mrp;
                $pro['discount_percentage'] = abs($items->discount_percentage);
                $pro['image']           = $items->base_image;
                $pro['max_quantity']    = $items->quantity;
                $imagePath              = $items->base_image;

                $brand_array[] = $items->brand_id;

                if (!Storage::exists($imagePath)) {
                    $path               = asset('assets/logo/product-noimg.jpg');
                } else {
                    $url                = Storage::url($imagePath);
                    $path               = asset($url);
                }

                $pro['image']           = $path;
                $pro['customer_id']     = $customer_id;
                $pro['guest_token']     = $citems->guest_token;
                $pro['cart_id']         = $citems->id;
                $pro['price']           = $citems->price;
                $pro['quantity']        = $citems->quantity;
                $pro['sub_total']       = $citems->sub_total;
                $pro['is_cod']       = $citems->is_cod;
                $pro['cod_amount']       = $citems->cod_amount;

                $grand_total            += $citems->sub_total;
                $grand_total            += $addon_total;
                $cartTemp[] = $pro;
            }

            $tmp['carts'] = $cartTemp;
            $tmp['cart_count'] = count($cartTemp);
            // if (isset($shippingfee_info) && !empty($shippingfee_info)) {
            //     $tmp['selected_shipping_fees'] = array(
            //         'id' => $shippingfee_info->id,
            //         'charges' => $shippingfee_info->charges,
            //         'shipping_title' => $shippingfee_info->shipping_title
            //     );

            //     $grand_total                = $grand_total + ($shippingfee_info->charges ?? 0);
            // }

            $shipping_amount = 0;
            $shippingTypes = [];
            $shipping_name = '';
            if (isset($selected_shipping) && (!empty($selected_shipping))) {
                $subquery = DB::table('cart_shipments')
                    ->join('carts', function ($join) {
                        $join->on('cart_shipments.cart_id', '=', 'carts.id')
                            ->whereColumn('cart_shipments.brand_id', '=', 'carts.brand_id');
                    })
                    ->where('carts.customer_id', $customer_id)
                    ->select(
                        'cart_shipments.brand_id',
                        'cart_shipments.shipping_type',
                        DB::raw('MAX(gbs_cart_shipments.shipping_amount) as max_shipping_amount')
                    )
                    ->groupBy('cart_shipments.brand_id', 'cart_shipments.shipping_type');

                // Main query to sum the shipping amounts for each unique brand_id
                $results = DB::table(DB::raw("({$subquery->toSql()}) as gbs_sub"))
                    ->mergeBindings($subquery)
                    ->select(
                        DB::raw('SUM(gbs_sub.max_shipping_amount) as total_shipment_amount'),
                        'sub.brand_id',
                        'sub.shipping_type'
                    )
                    ->groupBy('sub.brand_id', 'sub.shipping_type')
                    ->get();

                Log::info($results);

                foreach ($results as $result) {
                    $max_shipping_amount = floatval($result->total_shipment_amount);
                    $shipping_amount += $max_shipping_amount;
                    $shippingTypes[] = $result->shipping_type;
                }

                // Determine the final shipping type based on the rules provided
                $shipping_name = $this->determineFinalShippingType($shippingTypes);

                // Logging the total shipment amount and final shipping type
                Log::info("Total Amount for carts with more than one unique brand: " . $grand_total);
                Log::info("Final Shipping Type: " . $shipping_name);
                if (isset($shipping_amount) && !empty($shipping_amount) && ($shipping_amount > 0)) {
                    $grand_total                = (float)$grand_total + (float)$shipping_amount;
                }
            }


            if (isset($coupon_amount) && !empty($coupon_amount)) {
                $grand_total = (float)$grand_total - (float)$coupon_amount ?? 0;
            }

            // if($type==null){
            //     $grand_total=$grand_total-round($tax_total);
            // }
            if (count(array_unique($brand_array)) > 1) {
                $has_pickup_store = false;
            }

            $amount         = filter_var($grand_total, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $charges        = ShippingCharge::select('id', 'shipping_title', 'minimum_order_amount', 'charges', 'is_free')->where('status', 'published')->where('minimum_order_amount', '<', $amount)->get();
            $is_cod = 0;
            $cod_amount = 0;
            if (isset($checkCart[0]) && $checkCart[0]->is_cod == 1) {

                $grand_total = $grand_total + $checkCart[0]->cod_amount;
                $is_cod = $checkCart[0]->is_cod;
                $cod_amount = ($checkCart[0]->is_cod == 1) ? $checkCart[0]->cod_amount : 0;
            }
            $coupon_data = Cart::where('customer_id', $customer_id)->whereNotNull('coupon_id')->first();
            $is_coupon = 0;
            $coupon_code = '';
            $coupon_percentage = '';
            $coupon_type = '';
            if (isset($coupon_data) && $coupon_data->coupon_id != null) {
                $is_coupon = 1;
                $coupon_code = $coupon_data->coupon_code;
                $coupon_percentage = $coupon_data->coupon_percentage;
                $coupon_type = $coupon_data->coupon_type;
            }
            $tmp['shipping_charges']    = $charges;
            $tmp['cart_total']          = array(
                'total' => number_format(round($grand_total), 2),
                'product_tax_exclusive_total' => number_format(round($product_tax_exclusive_total), 2),
                'product_tax_exclusive_total_without_format' => round($product_tax_exclusive_total),
                'tax_total' => number_format($tax_total, 2),
                'tax_percentage' => number_format(round($tax_percentage), 2),
                'shipping_name' => ucwords(str_replace('_', '', $shipping_name)),
                'shipping_charge' => number_format($shipping_amount, 2),
                'coupon_amount' => $coupon_amount ?? 0,
                'addon_amount' => $total_addon_amount,
                'has_pickup_store' => $has_pickup_store,
                'is_cod' => $is_cod,
                'cod_amount' => $cod_amount,
                'is_coupon' => $is_coupon,
                'coupon_code' => $coupon_code,
                'coupon_percentage' => $coupon_percentage,
                'coupon_type' => $coupon_type
            );
        }

        return $tmp;
    }

    public function getVariationAmount($product_id, $selected_variation_ids)
    {
        $product = Product::find($product_id);
    }

    public function setShippingCharges(Request $request)
    {

        $customer_id = $request->customer_id;
        $shipping_fee_id = $request->shipping_fee_id;
        $coupon_amount = $request->coupon_amount ?? 0;

        $fee_info = ShippingCharge::select('id', 'shipping_title', 'minimum_order_amount', 'charges', 'is_free')->find($shipping_fee_id);
        if ($fee_info) {
            $update_data = [
                'shipping_fee_id' => $shipping_fee_id,
                'shipping_fee' => $fee_info->charges
            ];
            DB::table('carts')->where('customer_id', $customer_id)->update($update_data);
            $data = $this->getCartListAll($customer_id, null, null, null, $shipping_fee_id, $coupon_amount);
            $response['data'] = $data;
            $response['error'] = '0';
            $response['message'] = 'Shipping Fee Applied';
        } else {

            $response['data'] = [];
            $response['error'] = '1';
            $response['message'] = 'There is no products on the cart';
        }

        return $response;
    }

    public function determineFinalShippingType($shippingTypes)
    {
        // Determine the final shipping type based on the rules provided
        if (count(array_unique($shippingTypes)) === 1) {
            // All carts have the same shipping type
            return $shippingTypes[0];
        } else {
            // Different shipping types exist, apply the specified rules
            if (in_array('free_shipping', $shippingTypes) && in_array('standard_shipping', $shippingTypes)) {
                return 'standard_shipping';
            } elseif (in_array('free_shipping', $shippingTypes) && in_array('flat_shipping', $shippingTypes)) {
                return 'flat_shipping';
            } elseif (in_array('flat_shipping', $shippingTypes) && in_array('standard_shipping', $shippingTypes)) {
                return 'standard_shipping';
            }
            // Default fallback (optional based on your needs)
            return null;
        }
    }
}
