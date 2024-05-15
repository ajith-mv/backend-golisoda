<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartAddress;
use App\Models\CartProductAddon;
use App\Models\Master\Customer;
use App\Models\Product\Product;
use App\Models\ProductAddonItem;
use App\Models\Settings\Tax;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use App\Models\GlobalSettings;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use App\Models\CartProductVariationOption;
use App\Models\Master\Variation;
use App\Models\Offers\CouponCategory;
use App\Models\Offers\Coupons;
use App\Models\Product\ProductVariationOption;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {

        $customer_id = $request->customer_id;
        $guest_token = $request->guest_token;
        $addon_id = $request->addon_id;
        $product_id = $request->product_id;
        $variation_option_ids =  $request->variation_option_ids;
        $quantity = $request->quantity ?? 1;
        $type = $request->type;

        /**
         * 1. check customer id and product exist if not insert
         * 2. if exist update quantiy
         */


        $product_info = Product::find($product_id);
        $checkCart = Cart::when($customer_id != '', function ($q) use ($customer_id) {
            $q->where('customer_id', $customer_id);
        })->when($customer_id == '' && $guest_token != '', function ($q) use ($guest_token) {
            $q->where('guest_token', $guest_token);
        })->where('product_id', $product_id)->first();

        $getCartToken = Cart::when($customer_id != '', function ($q) use ($customer_id) {
            $q->where('customer_id', $customer_id);
        })->when($customer_id == '' && $guest_token != '', function ($q) use ($guest_token) {
            $q->where('guest_token', $guest_token);
        })->first();


        if (isset($checkCart) && !empty($checkCart)) {
            if ($type == 'delete') {
                $checkCart->delete();
            } else {
                $error = 0;
                $message = 'Cart added successful';
                $check_cart_variation_option = CartProductVariationOption::where('cart_id', $checkCart->id)->whereNotIn('variation_option_id', $variation_option_ids)->exists();
                if ($check_cart_variation_option) {
                    $customer_info = Customer::find($request->customer_id);
                    $total_variation_amount = 0;
                    if (isset($customer_info) && !empty($customer_info) || !empty($request->guest_token)) {

                        if ($product_info->quantity <= $quantity) {
                            $quantity = $product_info->quantity;
                        }
                        if (isset($variation_option_ids) && !empty($variation_option_ids)) {
                            $variation_option_data = ProductVariationOption::whereIn('id', $variation_option_ids)
                                ->where('product_id', $product_id)
                                ->selectRaw("SUM(amount) AS total_amount")
                                ->groupBy('product_id')
                                ->get();
                            if (isset($variation_option_data)) {
                                $total_variation_amount = $variation_option_data[0]->total_amount;
                            }
                        }
                        $ins['customer_id']     = $request->customer_id;
                        $ins['product_id']      = $product_id;
                        $ins['guest_token']     = $request->guest_token ?? null;
                        $ins['quantity']        = $quantity ?? 1;
                        $ins['price']           = (float)$product_info->mrp + $total_variation_amount;
                        $ins['sub_total']       = $ins['price'] * $quantity ?? 1;
                        $ins['cart_order_no']   = 'ORD' . date('ymdhis');

                        $cart_id = Cart::create($ins)->id;
                        if (isset($variation_option_ids) && !empty($variation_option_ids)) {
                            foreach ($variation_option_ids as $variation_option_id) {
                                $product_variation_option = ProductVariationOption::find($variation_option_id);
                                $cart_product_variation_ins['cart_id'] = $cart_id;
                                $cart_product_variation_ins['product_id'] = $product_variation_option->product_id;
                                $cart_product_variation_ins['variation_id'] = $product_variation_option->variation_id;
                                $cart_product_variation_ins['variation_option_id'] = $product_variation_option->id;
                                $cart_product_variation_ins['value'] = $product_variation_option->value;
                                $cart_product_variation_ins['amount'] = $product_variation_option->amount;
                                CartProductVariationOption::create($cart_product_variation_ins);
                            }
                        }

                        $error = 0;
                        $message = 'Cart added successful';
                        $data = $this->getCartListAll($customer_id, $guest_token);
                    } else {
                        $error = 1;
                        $message = 'Customer Data not available';
                        $data = [];
                    }
                } else {
                    $product_quantity = $checkCart->quantity + $quantity;
                    if ($product_info->quantity <= $product_quantity) {
                        $product_quantity = $product_info->quantity;
                    }

                    $checkCart->quantity  = $product_quantity;
                    $checkCart->sub_total = $product_quantity * $checkCart->price;
                    $checkCart->update();
                }



                $data = $this->getCartListAll($customer_id, $guest_token);
            }
        } else {
            $customer_info = Customer::find($request->customer_id);
            $total_variation_amount = 0;
            if (isset($customer_info) && !empty($customer_info) || !empty($request->guest_token)) {

                if ($product_info->quantity <= $quantity) {
                    $quantity = $product_info->quantity;
                }
                if (isset($variation_option_ids) && !empty($variation_option_ids)) {
                    $variation_option_data = ProductVariationOption::whereIn('id', $variation_option_ids)
                        ->where('product_id', $product_id)
                        ->selectRaw("SUM(amount) AS total_amount")
                        ->groupBy('product_id')
                        ->get();
                    if (isset($variation_option_data)) {
                        $total_variation_amount = $variation_option_data[0]->total_amount;
                    }
                }
                $ins['customer_id']     = $request->customer_id;
                $ins['product_id']      = $product_id;
                $ins['guest_token']     = $request->guest_token ?? null;
                $ins['quantity']        = $quantity ?? 1;
                $ins['price']           = (float)$product_info->mrp + $total_variation_amount;
                $ins['sub_total']       = $ins['price'] * $quantity ?? 1;
                $ins['cart_order_no']   = 'ORD' . date('ymdhis');

                $cart_id = Cart::create($ins)->id;
                if (isset($variation_option_ids) && !empty($variation_option_ids)) {
                    foreach ($variation_option_ids as $variation_option_id) {
                        $product_variation_option = ProductVariationOption::find($variation_option_id);
                        $cart_product_variation_ins['cart_id'] = $cart_id;
                        $cart_product_variation_ins['product_id'] = $product_variation_option->product_id;
                        $cart_product_variation_ins['variation_id'] = $product_variation_option->variation_id;
                        $cart_product_variation_ins['variation_option_id'] = $product_variation_option->id;
                        $cart_product_variation_ins['value'] = $product_variation_option->value;
                        $cart_product_variation_ins['amount'] = $product_variation_option->amount;
                        CartProductVariationOption::create($cart_product_variation_ins);
                    }
                }

                $error = 0;
                $message = 'Cart added successful';
                $data = $this->getCartListAll($customer_id, $guest_token);
            } else {
                $error = 1;
                $message = 'Customer Data not available';
                $data = [];
            }
        }




        return array('error' => $error, 'message' => $message, 'data' => $data);
    }

    public function applyCoupon($coupon_code, $customer_id, $shipping_fee_id = '')
    {
        $coupon_code = $coupon_code;
        $customer_id = $customer_id;
        $shipping_fee_id = $shipping_fee_id ?? '';
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
                                    $cartCount = Cart::where('customer_id', $customer_id)->where('product_id', $items->product_id)->first();
                                    if (isset($cartCount) && is_null($cartCount->id)) {
                                        $response['status'] = 'error';
                                        $response['message'] = 'Coupon not applicable';
                                        return $response ?? '';
                                    }
                                    $product_info = Product::find($items->product_id);
                                    $cartCount->sub_total = round($product_info->strike_price * $cartCount->quantity);
                                    $cartCount->update();
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
                                            return $response['coupon_amount'];
                                        }
                                    } else {
                                        $has_product_error++;
                                    }
                                }
                                if ($has_product == 0 && $has_product_error > 0) {
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
                            # category ...
                            $checkCartData = Cart::selectRaw('gbs_carts.*,gbs_products.product_name, SUM(gbs_products.strike_price * gbs_carts.quantity) as category_total')
                                ->join('products', 'products.id', '=', 'carts.product_id')
                                ->where('carts.customer_id', $customer_id)
                                // ->groupBy('carts.product_id')
                                ->first();
                            if (isset($checkCartData) && is_null($checkCartData->id)) {
                                $response['status'] = 'error';
                                $response['message'] = 'Coupon not applicable';
                                return $response ?? '';
                            }
                            $product_info = Product::find($checkCartData->product_id);
                            $checkCartData->sub_total = round($product_info->strike_price * $checkCartData->quantity);
                            $checkCartData->update();
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

                                    return $response['coupon_amount'];
                                }
                            } else {
                                $response['status'] = 'error';
                                $response['message'] = 'Cart order does not meet coupon minimum order amount';
                            }
                            break;
                        case '3':
                            # category ...
                            $checkCartData = Cart::selectRaw('gbs_carts.*,gbs_products.product_name,gbs_product_categories.name,gbs_coupon_categories.id as catcoupon_id, SUM(gbs_products.strike_price * gbs_carts.quantity) as category_total')
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
                            if (isset($checkCartData) && is_null($checkCartData->id)) {
                                $response['status'] = 'error';
                                $response['message'] = 'Coupon not applicable';
                                return $response ?? '';
                            }
                            $product_info = Product::find($checkCartData->product_id);
                            $checkCartData->sub_total = round($product_info->strike_price * $checkCartData->quantity);
                            $checkCartData->update();
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

                                    return $response['coupon_amount'];
                                }
                            } else {
                                $response['status'] = 'error';
                                $response['message'] = 'Cart order does not meet coupon minimum order amount';
                            }
                            break;
                        case '5':

                            # brands ...
                            $checkCartData = Cart::selectRaw('gbs_carts.*,gbs_products.product_name,gbs_brands.brand_name,gbs_coupon_brands.id as catcoupon_id, SUM(gbs_products.strike_price * gbs_carts.quantity) as category_total')
                                ->join('products', 'products.id', '=', 'carts.product_id')
                                ->join('brands', 'brands.id', '=', 'products.brand_id')
                                ->join('coupon_brands', function ($join) {
                                    $join->on('coupon_brands.brand_id', '=', 'brands.id');
                                })
                                ->where('coupon_brands.coupon_id', $coupon->id)
                                ->where('carts.customer_id', $customer_id)
                                //->groupBy('carts.product_id')
                                ->first();
                            if (isset($checkCartData) && is_null($checkCartData->id)) {
                                $response['status'] = 'error';
                                $response['message'] = 'Coupon not applicable';
                                return $response ?? '';
                            }
                            $product_info = Product::find($checkCartData->product_id);
                            $checkCartData->sub_total = round($product_info->strike_price * $checkCartData->quantity);
                            $checkCartData->update();
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
                                    return $response['coupon_amount'];
                                } else {
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
    public function updateCart(Request $request)
    {

        $cart_id        = $request->cart_id;
        $guest_token    = $request->guest_token;
        $customer_id    = $request->customer_id;
        $quantity       = $request->quantity ?? 1;
        $addon_id   = $request->addon_id;
        $addon_item_id   = $request->addon_item_id;

        $addon_items_info = ProductAddonItem::find($addon_item_id);

        $checkCart      = Cart::where('id', $cart_id)->first();

        if ($checkCart) {

            if (isset($addon_items_info) && !empty($addon_items_info)) {
                CartProductAddon::where('cart_id', $cart_id)
                    ->where(['product_id' => $checkCart->product_id, 'addon_id' => $addon_id])->delete();
                $addon = [];
                $addon['cart_id'] = $cart_id;
                $addon['product_id'] = $checkCart->product_id;
                $addon['addon_id'] = $addon_id;
                $addon['addon_item_id'] = $addon_item_id;
                $addon['title'] = $addon_items_info->label;
                $addon['amount'] = $addon_items_info->amount;

                CartProductAddon::create($addon);
            } else {

                $checkCart->quantity = $quantity;
                $checkCart->sub_total = $checkCart->price * $quantity;
                $checkCart->update();
            }

            $error = 0;
            $message = 'Cart updated successful';
            $data = $this->getCartListAll($checkCart->customer_id, $checkCart->guest_token);
        } else {

            $error = 1;
            $message = 'You need to login first';
            $data = [];
        }

        return array('error' => $error, 'message' => $message, 'data' => $data);
    }
    public function calculationCod(Request $request)
    {
        $customer_id    = $request->customer_id;
        $guest_token    = $request->guest_token;
        $checkCarts      = Cart::where('customer_id', $customer_id)->get();


        if (count($checkCarts) > 0) {


            foreach ($checkCarts as $checkCart) {
                $cart_data = Cart::find($checkCart->id);
                $cart_data->is_cod = $request->is_cod;
                $cart_data->cod_amount = ($request->is_cod == 1) ? $request->cod_amount : NULL;
                $cart_data->update();
            }
        }
        $error = 0;
        $message = 'Cod updated successful';
        $data = $this->getCartListAll($request->customer_id, $request->guest_token);
        return array('error' => $error, 'message' => $message, 'data' => $data);
    }
    public function deleteCart(Request $request)
    {

        $cart_id        = $request->cart_id;
        $customer_id = $request->customer_id;
        $guest_token = $request->guest_token;
        $product_id = $request->product_id;

        $checkCart = Cart::when($customer_id != '', function ($q) use ($customer_id) {
            $q->where('customer_id', $customer_id);
        })->when($customer_id == '' && $guest_token != '', function ($q) use ($guest_token) {
            $q->where('guest_token', $guest_token);
        })->where('product_id', $product_id)->first();
        if (isset($cart_id)) {
            $checkCart      = Cart::find($cart_id);
        }
        if ($checkCart) {
            $checkCart->addons()->delete();
            $checkCart->variationOptions()->delete();
            $customer_id    = $checkCart->customer_id;
            $guest_token    = $checkCart->guest_token;
            $checkCart->delete();

            $error = 0;
            $message = 'Cart Item deleted successful';

            $data = $this->getCartListAll($customer_id, $guest_token);
        } else {
            $error = 1;
            $message = 'Cart Data not available';
            $data = [];
        }
        return array('error' => $error, 'message' => $message, 'data' => $data);
    }

    public function clearCart(Request $request)
    {

        $customer_id        = $request->customer_id;
        $guest_token        = $request->guest_token;

        if ($customer_id || $guest_token) {
            $data = Cart::when($customer_id != '', function ($q) use ($customer_id) {
                $q->where('customer_id', $customer_id);
            })->when($customer_id == '' && $guest_token != '', function ($q) use ($guest_token) {
                $q->where('guest_token', $guest_token);
            })->get();

            if (isset($data) && count($data) > 0) {
                foreach ($data as $item) {
                    $item->addons()->delete();
                    $item->variationOptions()->delete();
                }
            }


            Cart::when($customer_id != '', function ($q) use ($customer_id) {
                $q->where('customer_id', $customer_id);
            })->when($customer_id == '' && $guest_token != '', function ($q) use ($guest_token) {
                $q->where('guest_token', $guest_token);
            })->delete();

            $data = $this->getCartListAll($customer_id, $guest_token);
            $error = 0;
            $message = 'Cart Cleared successful';
        } else {

            $error = 1;
            $message = 'Customer Data not available';
            $data = [];
        }

        return array('error' => $error, 'message' => $message, 'data' => $data);
    }


    public function getCarts(Request $request)
    {
        $guest_token = $request->guest_token;
        $customer_id    = $request->customer_id;
        $selected_shipping = $request->selected_shipping ?? '';
        return $this->getCartListAll($customer_id, $guest_token, null, null, $selected_shipping, null);
    }

    function getCartListAll($customer_id = null, $guest_token = null,  $shipping_info = null, $shipping_type = null, $selected_shipping = null, $coupon_data = null)
    {

        // dd( $coupon_data );
        $checkCart          = Cart::with(['products', 'products.productCategory', 'variationOptions'])->when($customer_id != '', function ($q) use ($customer_id) {
            $q->where('customer_id', $customer_id);
        })->when($customer_id == '' && $guest_token != '', function ($q) use ($guest_token) {
            $q->where('guest_token', $guest_token);
        })->get();
        // foreach ($checkCart as $cartItem) {

        // }
        $globel = GlobalSettings::find(1);
        $tmp                = [];
        $grand_total        = 0;
        $tax_total          = 0;
        $product_tax_exclusive_total = 0;
        $tax_percentage = 0;
        $cartTemp = [];

        $total_addon_amount = 0;
        $has_pickup_store = true;
        $brand_array = [];
        if (isset($checkCart) && !empty($checkCart) && count($checkCart) > 0) {
            foreach ($checkCart as $citems) {
                $used_addons = [];
                $selected_value = [];
                $items = $citems->products;
                $tax = [];
                $tax_data = 0;
                $tax_percentage = 0;
                try {
                    $category               = $items->productCategory;
                    $product_info = Product::find($citems->product_id);

                    // if($citems){
                    // $product_info=Product::find($citems->product_id);
                    //  if(isset($product_info->productCategory->tax)){
                    //       $tax_data=($product_info->productCategory->tax->pecentage / 100);  
                    //     }else if(isset($product_info->tax)){
                    //       $tax_data=($product_info->tax->pecentage / 100);  
                    //     }else{
                    //         $tax_data=(0 / 100);
                    //     }
                    // }
                    $pro                    = [];
                    $variation_option_id = [];

                    $total_variation_amount = 0;
                    foreach ($citems->variationOptions as $variationids) {
                        $variation_option_id[] = $variationids->variation_option_id;
                    }
                    if (isset($variation_option_id) && !empty($variation_option_id)) {
                        $variation_option_data = ProductVariationOption::whereIn('id', $variation_option_id)
                            ->where('product_id', $product_info->id)
                            // ->selectRaw("SUM(amount) AS total_amount")
                            // ->groupBy('product_id')
                            ->get();
                        // if (isset($variation_option_data)) {
                        //     $total_variation_amount = $variation_option_data[0]->total_amount;
                        // }

                        foreach ($variation_option_data as $value) {

                            $variation = Variation::where('id', $value->variation_id)->first();
                            if ($variation) {
                                $title = $variation->title ?? '';
                                // $id = $value->id ?? '';
                                $selected_value[$title] = $value->value ?? '';
                                $amount = $value->amount;
                                $total_variation_amount = $total_variation_amount + $amount;
                            }
                        }
                    }
                    $category               = $items->productCategory;
                    if (isset($citems->coupon_id)) {
                        // $price=$items->strike_price /(1+$tax_data);
                        $price_with_tax         = $items->strike_price + $total_variation_amount;
                        $citems->sub_total = round($price_with_tax * $citems->quantity);
                        $citems->save();
                    } else {
                        // $price=$items->mrp /(1+$tax_data);
                        $price_with_tax         = $items->mrp + $total_variation_amount;
                        $citems->sub_total = round($price_with_tax * $citems->quantity);
                        $citems->save();
                    }

                    if (isset($category->parent->tax_id) && !empty($category->parent->tax_id)) {
                        $tax_info = Tax::find($category->parent->tax_id);
                    } else if (isset($category->tax_id) && !empty($category->tax_id)) {
                        $tax_info = Tax::find($category->tax_id);
                    }
                    if (isset($tax_info) && !empty($tax_info)) {
                        $tax = getAmountExclusiveTax($price_with_tax, $product_info->productCategory->tax->pecentage ?? 12);
                        $tax_total =  $tax_total + ($tax['gstAmount'] * $citems->quantity) ?? 0;
                        $product_tax_exclusive_total = $product_tax_exclusive_total + ($tax['basePrice'] * $citems->quantity);
                        // print_r( $product_tax_exclusive_total );
                        $tax_percentage         = $tax['tax_percentage'] ?? 0;
                    } else {
                        $product_tax_exclusive_total = $product_tax_exclusive_total + $citems->sub_total;
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                }

                /**
                 * addon amount calculated here
                 */
                try {
                    $addonItems = CartProductAddon::where(['cart_id' => $citems->id, 'product_id' => $items->id])->get();

                    $addon_total = 0;
                    if (isset($addonItems) && !empty($addonItems) && count($addonItems) > 0) {
                        foreach ($addonItems as $addItems) {
                            if (isset($addItems->addonItem->addon) && !empty($addItems->addonItem->addon)) {

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
                    $pro['price']           = $items->mrp + $total_variation_amount;
                    $pro['total_variation_amount'] = $total_variation_amount;
                    $pro['strike_price']    = number_format(($items->strike_price + $total_variation_amount), 2);
                    $pro['save_price']      = ($items->strike_price + $total_variation_amount) - ($items->mrp + $total_variation_amount);
                    $pro['discount_percentage'] = abs($items->discount_percentage);
                    $pro['image']           = $items->base_image;
                    $pro['max_quantity']    = $items->quantity;
                    $pro['chosen_variation_option_ids'] = $variation_option_id;
                    $pro['chosen_variation'] = $selected_value;
                    $imagePath              = $items->base_image;

                    $brand_array[] = $items->brand_id;

                    if (!Storage::exists($imagePath)) {
                        $path               = asset('assets/logo/no_Image.jpg');
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
                    $pro['addons']          = $used_addons;
                    $pro['is_cod']       = $citems->is_cod;
                    $pro['cod_amount']       = $citems->cod_amount;
                    $grand_total            += $citems->sub_total;
                    $grand_total            += $addon_total;
                    $cartTemp[] = $pro;
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }

            $tmp['carts'] = $cartTemp;
            $tmp['cart_count'] = count($cartTemp);
            if (isset($shipping_info) && !empty($shipping_info) || (isset($selected_shipping) && !empty($selected_shipping))) {
                $tmp['selected_shipping_fees'] = array(
                    'shipping_id' => $shipping_info->id ?? $selected_shipping['shipping_id'],
                    'shipping_charge_order' => $shipping_info->charges ?? $selected_shipping['shipping_charge_order'],
                    'shipping_type' => $shipping_type ?? $selected_shipping['shipping_type'] ?? 'fees'
                );

                $grand_total                = $grand_total + ($shipping_info->charges ?? $selected_shipping['shipping_charge_order'] ?? 0);
            }
            // if (isset($coupon_data) && !empty($coupon_data)) {
            //     $grand_total = $grand_total - $coupon_data['discount_amount'] ?? 0;
            // }

            if (count(array_unique($brand_array)) > 1) {
                $has_pickup_store = false;
            }

            $amount         = filter_var($grand_total, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $charges        = ShippingCharge::select('id', 'shipping_title', 'minimum_order_amount', 'charges', 'is_free')->where('status', 'published')->where('minimum_order_amount', '<', $amount)->get();

            $tmp['shipping_charges']    = $charges;

            $is_cod = 0;
            $cod_amount = 0;
            $coupon_data = Cart::where('customer_id', $customer_id)->whereNotNull('coupon_id')->first();
            if (isset($coupon_data) && isset($coupon_data->coupon_id)) {
                $new_coupon_amount = $this->applyCoupon($coupon_data->coupon_code, $customer_id, $selected_shipping);
                if (filter_var($new_coupon_amount, FILTER_VALIDATE_INT) !== false || filter_var($new_coupon_amount, FILTER_VALIDATE_FLOAT) !== false) {
                    $grand_total = (float)$grand_total - (float)$new_coupon_amount;
                }

                //  $grand_total=$grand_total+round($tax_total);
            }
            if (isset($checkCart[0]) && $checkCart[0]->is_cod == 1) {
                $grand_total = $grand_total + $checkCart[0]->cod_amount;
                $is_cod = $checkCart[0]->is_cod;
                $cod_amount = ($checkCart[0]->is_cod == 1) ? $checkCart[0]->cod_amount : 0;
            }
            $is_coupon = 0;
            $coupon_code = '';
            $coupon_percentage = '';
            $coupon_type = '';
            $coupon_amount = 0;
            if (isset($coupon_data) && isset($coupon_data->coupon_id)) {
                $is_coupon = 1;
                $coupon_code = $coupon_data->coupon_code;
                $coupon_percentage = $coupon_data->coupon_percentage;
                $coupon_type = $coupon_data->coupon_type;
                if (filter_var($new_coupon_amount, FILTER_VALIDATE_INT) !== false || filter_var($new_coupon_amount, FILTER_VALIDATE_FLOAT) !== false) {
                    $coupon_amount = $new_coupon_amount;
                } else {
                    $coupon_amount = $coupon_data->coupon_amount;
                }
            }
            $tmp['cart_total']          = array(
                'total' => number_format(round($grand_total), 2),
                'product_tax_exclusive_total' => number_format(round($product_tax_exclusive_total), 2),
                'product_tax_exclusive_total_without_format' => round($product_tax_exclusive_total),
                'tax_total' => number_format(round($tax_total), 2),
                'tax_percentage' => number_format(round($tax_percentage), 2),
                'shipping_charge' => $shipping_info->charges ?? 0,
                'addon_amount' => $total_addon_amount,
                'coupon_amount' => $coupon_amount ?? 0,
                'total_variation_amount' => $total_variation_amount,
                'has_pickup_store' => $has_pickup_store,
                'brand_id' => $brand_array[0] ?? '',
                'is_cod' => $is_cod,
                'cod_amount' => $cod_amount,
                'is_coupon' => $is_coupon,
                'coupon_code' => $coupon_code,
                'coupon_percentage' => $coupon_percentage,
                'coupon_type' => $coupon_type

            );
            $tmp['is_pickup_from_store'] = $globel->is_pickup_from_store ?? 0;
            $tmp['is_cod'] = $globel->is_cod ?? 0;
            $tmp['cod_amount'] = $globel->cod_amount ?? 0;
            return $tmp;
        } else {
            $tmp['cart_total']          = array(
                'total' => 0,
                'product_tax_exclusive_total' => 0,
                'product_tax_exclusive_total_without_format' => 0,
                'tax_total' => 0,
                'tax_percentage' => 0,
                'shipping_charge' => 0,
                'addon_amount' => 0,
                'coupon_amount' => 0,
                'has_pickup_store' => 0,
                'brand_id' => '',
                'is_cod' => 0,
                'cod_amount' => 0,
                'is_coupon' => 0,
                'coupon_code' => 0,
                'coupon_percentage' => 0,
                'coupon_type' => 0

            );
            $tmp['is_pickup_from_store'] = $globel->is_pickup_from_store ?? 0;
            $tmp['is_cod'] = $globel->is_cod ?? 0;
            $tmp['cod_amount'] = $globel->cod_amount ?? 0;
            $tmp['cart_count'] = 0;
            $tmp['carts'] = 0;
            return $tmp;
        }


        return $tmp;
    }

    public function getShippingCharges(Request $request)
    {

        $amount         = filter_var($request->amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $charges        = ShippingCharge::select('id', 'shipping_title', 'minimum_order_amount', 'charges', 'is_free')->where('status', 'published')->where('minimum_order_amount', '<=', $amount)->get();
        return $charges;
    }

    public function deleteAddonItems(Request $request)
    {

        $addon_id   = $request->addon_id;
        $cart_id    = $request->cart_id;
        $product_id = $request->product_id;

        CartProductAddon::where(['addon_id' => $addon_id, 'cart_id' => $cart_id, 'product_id' => $product_id])->delete();

        $cart_info = Cart::find($cart_id);
        if ($cart_info) {
            $error = 0;
            $message = 'Addon Deleted Successfully';
            $data = $this->getCartListAll($cart_info->customer_id, $cart_info->guest_token);
        } else {
            $error = 1;
            $message = 'Cart data not found';
        }

        return array('error' => $error, 'message' => $message, 'data' => $data ?? []);
    }
}
