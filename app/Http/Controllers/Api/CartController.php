<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartAddress;
use App\Models\CartShipment;
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
use App\Models\CartShiprocketResponse;
use App\Models\Master\Brands;
use App\Models\Master\Variation;
use App\Models\Offers\Coupons;
use App\Models\Product\ProductVariationOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Master\CustomerAddress;
use App\Services\ShipRocketService;
use Illuminate\Support\Str;

class CartController extends Controller
{
    protected $rocketService;

    public function __construct(ShipRocketService $rocketService)
    {
        $this->rocketService = $rocketService;
    }

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

        if (isset($variation_option_ids) && !empty($variation_option_ids)) {
            if (isset($checkCart) && !empty($checkCart)) {
                if ($type == 'delete') {
                    $checkCart->delete();
                } else {
                    $error = 0;
                    $message = 'Cart added successful';
                    $allCart = Cart::when($customer_id != '', function ($q) use ($customer_id) {
                        $q->where('customer_id', $customer_id);
                    })->when($customer_id == '' && $guest_token != '', function ($q) use ($guest_token) {
                        $q->where('guest_token', $guest_token);
                    })->where('product_id', $product_id)->get();
                    if (isset($allCart)) {
                        $cart_ids = [];
                        foreach ($allCart as $singleCart) {
                            $cart_ids[] = $singleCart->id;
                        }
                    }

                    $check_cart_variation_option = CartProductVariationOption::select('product_id')->whereIn('cart_id', $cart_ids)->whereIn('variation_option_id', $variation_option_ids)->where('product_id', $product_id)->groupBy('product_id')->havingRaw('COUNT(DISTINCT variation_option_id)  = ?', [count($variation_option_ids)])->exists();
                    if (!$check_cart_variation_option) {
                        log::info('products with same variation does not exists in the cart');
                        $customer_info = Customer::find($request->customer_id);
                        $total_variation_amount = 0;
                        $total_discount_amount = 0;
                        if (isset($customer_info) && !empty($customer_info) || !empty($request->guest_token)) {

                            if ($product_info->quantity <= $quantity) {
                                $quantity = $product_info->quantity;
                            }
                            if (isset($variation_option_ids) && !empty($variation_option_ids)) {
                                $variation_option_data = ProductVariationOption::whereIn('id', $variation_option_ids)
                                    ->where('product_id', $product_id)
                                    ->selectRaw("SUM(amount) AS total_amount, SUM(discount_amount) as total_discount_amount")
                                    ->groupBy('product_id')
                                    ->first();
                                if (isset($variation_option_data)) {
                                    $total_variation_amount = $variation_option_data->total_amount;
                                    $total_discount_amount = $variation_option_data->total_discount_amount;
                                    $product_info->mrp = ($product_info->strike_price + $total_variation_amount) - $total_discount_amount;
                                    $product_info->strike_price = $product_info->strike_price + $total_variation_amount;
                                }
                            }
                            $ins['customer_id']     = $request->customer_id;
                            $ins['product_id']      = $product_id;
                            $ins['brand_id']      = $product_info->brand_id;
                            $ins['guest_token']     = $request->guest_token ?? null;
                            $ins['quantity']        = $quantity ?? 1;
                            $ins['price']           = (float)$product_info->mrp;
                            $ins['sub_total']       = $ins['price'] * $quantity ?? 1;
                            $ins['cart_order_no']   = 'ORD' . date('ymdhis');

                            $cart_id = Cart::create($ins)->id;
                            if (isset($variation_option_ids) && !empty($variation_option_ids)) {
                                foreach ($variation_option_ids as $variation_option_id) {
                                    $product_variation_option = ProductVariationOption::find($variation_option_id);
                                    if (isset($product_variation_option) && (!empty($product_variation_option))) {
                                        $cart_product_variation_ins['cart_id'] = $cart_id;
                                        $cart_product_variation_ins['product_id'] = $product_variation_option->product_id;
                                        $cart_product_variation_ins['variation_id'] = $product_variation_option->variation_id;
                                        $cart_product_variation_ins['variation_option_id'] = $product_variation_option->id;
                                        $cart_product_variation_ins['value'] = $product_variation_option->value;
                                        $cart_product_variation_ins['amount'] = $product_variation_option->amount;
                                        $cart_product_variation_ins['discount_amount'] = $product_variation_option->discount_amount;
                                        CartProductVariationOption::create($cart_product_variation_ins);
                                    }
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
                        log::info('products with same variation exists in the cart');

                        if (isset($variation_option_ids) && !empty($variation_option_ids)) {
                            $allCart = Cart::when($customer_id != '', function ($q) use ($customer_id) {
                                $q->where('customer_id', $customer_id);
                            })->when($customer_id == '' && $guest_token != '', function ($q) use ($guest_token) {
                                $q->where('guest_token', $guest_token);
                            })->where('product_id', $product_id)->get();
                            if (isset($allCart)) {
                                $cart_ids = [];
                                foreach ($allCart as $singleCart) {
                                    $cart_ids[] = $singleCart->id;
                                }
                            }
                            log::info('variation option is set to this product');
                            $check_cart_variation_option = CartProductVariationOption::whereIn('variation_option_id', $variation_option_ids)->where('product_id', $product_id)->whereIn('cart_id', $cart_ids)->groupBy('cart_id')->havingRaw('COUNT(DISTINCT variation_option_id)  = ?', [count($variation_option_ids)])->get('cart_id');
                            log::info('find the cart id in which quantity needs to be added');
                            $checkCart = Cart::find($check_cart_variation_option[0]->cart_id);
                            $product_quantity = $checkCart->quantity + $quantity;
                            if ($product_info->quantity <= $product_quantity) {
                                $product_quantity = $product_info->quantity;
                            }

                            $checkCart->quantity  = $product_quantity;
                            $checkCart->sub_total = $product_quantity * $checkCart->price;
                            $checkCart->update();
                        } else {
                            $product_quantity = $checkCart->quantity + $quantity;
                            if ($product_info->quantity <= $product_quantity) {
                                $product_quantity = $product_info->quantity;
                            }

                            $checkCart->quantity  = $product_quantity;
                            $checkCart->sub_total = $product_quantity * $checkCart->price;
                            $checkCart->update();
                        }
                    }



                    $data = $this->getCartListAll($customer_id, $guest_token);
                }
            } else {
                $customer_info = Customer::find($request->customer_id);
                $total_variation_amount = 0;
                $total_discount_amount = 0;
                if (isset($customer_info) && !empty($customer_info) || !empty($request->guest_token)) {

                    if ($product_info->quantity <= $quantity) {
                        $quantity = $product_info->quantity;
                    }
                    if (isset($variation_option_ids) && !empty($variation_option_ids)) {
                        $variation_option_data = ProductVariationOption::whereIn('id', $variation_option_ids)
                            ->where('product_id', $product_id)
                            ->selectRaw("SUM(amount) AS total_amount, SUM(discount_amount) as total_discount_amount")
                            ->groupBy('product_id')
                            ->first();
                        if (isset($variation_option_data)) {
                            $total_variation_amount = $variation_option_data->total_amount;
                            $total_discount_amount = $variation_option_data->total_discount_amount;
                            $product_info->mrp = ($product_info->strike_price + $total_variation_amount) - $total_discount_amount;
                            $product_info->strike_price = $product_info->strike_price + $total_variation_amount;
                        }
                    }
                    $ins['customer_id']     = $request->customer_id;
                    $ins['product_id']      = $product_id;
                    $ins['brand_id']      = $product_info->brand_id;
                    $ins['guest_token']     = $request->guest_token ?? null;
                    $ins['quantity']        = $quantity ?? 1;
                    $ins['price']           = (float)$product_info->mrp;
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
                            $cart_product_variation_ins['discount_amount'] = $product_variation_option->discount_amount;
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
        } else {
            if (isset($checkCart) && !empty($checkCart)) {
                if ($type == 'delete') {
                    $checkCart->delete();
                } else {
                    $error = 0;
                    $message = 'Cart added successful';
                    $product_quantity = $checkCart->quantity + $quantity;
                    if ($product_info->quantity <= $product_quantity) {
                        $product_quantity = $product_info->quantity;
                    }

                    $checkCart->quantity  = $product_quantity;
                    $checkCart->sub_total = $product_quantity * $checkCart->price;
                    $checkCart->update();

                    $data = $this->getCartListAll($customer_id, $guest_token);
                }
            } else {
                $customer_info = Customer::find($request->customer_id);

                if (isset($customer_info) && !empty($customer_info) || !empty($request->guest_token)) {

                    if ($product_info->quantity <= $quantity) {
                        $quantity = $product_info->quantity;
                    }
                    $ins['customer_id']     = $request->customer_id;
                    $ins['product_id']      = $product_id;
                    $ins['brand_id']      = $product_info->brand_id;
                    $ins['guest_token']     = $request->guest_token ?? null;
                    $ins['quantity']        = $quantity ?? 1;
                    $ins['price']           = (float)$product_info->mrp;
                    $ins['sub_total']       = $product_info->mrp * $quantity ?? 1;
                    $ins['cart_order_no']   = 'ORD' . date('ymdhis');

                    $cart_id = Cart::create($ins)->id;
                    $error = 0;
                    $message = 'Cart added successful';
                    $data = $this->getCartListAll($customer_id, $guest_token);
                } else {
                    $error = 1;
                    $message = 'Customer Data not available';
                    $data = [];
                }
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

                                    return $response['coupon_amount'];
                                }
                            } else {
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

                                    return $response['coupon_amount'];
                                }
                            } else {
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
        $selected_shipping = $request->selected_shipping ?? '';
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
            $data = $this->getCartListAll($checkCart->customer_id, $checkCart->guest_token, null, null, $selected_shipping, null, null);
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
         $shiprocket_order_ids = [];
         $shiprocketOrderId = null;
        if ($checkCart) {
            $checkCart->addons()->delete();
            $checkCart->variationOptions()->delete();
            $shipments = $checkCart->shipments();
            $shiprocketOrder = CartShipment::where('cart_id', $checkCart->id)->first();
            if(isset($shiprocketOrder)){
                   $shiprocketOrderId =  $shiprocketOrder->shiprocket_order_id;

            }

            if ($shiprocketOrderId) {
                log::info($shiprocketOrderId. 'shiprocket order id');
                // Count how many carts are associated with this shiprocket_order_id
                 $count = CartShipment::where('shiprocket_order_id', $shiprocketOrderId)->count();
                 log::info('count of data'. $count);
                 if ($count <= 1) {
                    $shiprocket_order_ids[] = $shiprocketOrderId;
                    // If only one cart is associated, cancel the Shiprocket order
                    $this->rocketService->cancelShiprocketOrder($shiprocket_order_ids);
                   
                 }
            }
            $checkCart->rocketResponse()->delete();
            $checkCart->shipments()->delete();
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
        $cart_ids = [];
        if ($customer_id || $guest_token) {
            $data = Cart::when($customer_id != '', function ($q) use ($customer_id) {
                $q->where('customer_id', $customer_id);
            })->when($customer_id == '' && $guest_token != '', function ($q) use ($guest_token) {
                $q->where('guest_token', $guest_token);
            })->get();

            if (isset($data) && count($data) > 0) {
                foreach ($data as $item) {
                    $cart_ids[] = $item->id;
                    $item->addons()->delete();
                    $item->variationOptions()->delete();
                    $item->rocketResponse()->delete();
                }
            }


            Cart::when($customer_id != '', function ($q) use ($customer_id) {
                $q->where('customer_id', $customer_id);
            })->when($customer_id == '' && $guest_token != '', function ($q) use ($guest_token) {
                $q->where('guest_token', $guest_token);
            })->delete();
            $shipment_order_ids = $this->getShipmentOrderIds($cart_ids);
            $this->rocketService->cancelShiprocketOrder($shipment_order_ids);
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
        $address    = $request->address;
        $selected_shipping = $request->selected_shipping ?? '';
        if (empty($customer_id) && empty($guest_token)) {
            $tmp                = [];
            // if ($guest_token == null) {
            $tmp['carts'] = [];
            $tmp['cart_count'] = 0;
            $tmp['cod_amount'] = 0;
            $tmp['is_code'] = 0;
            $tmp['is_pickup_from_store'] = 0;
            $tmp['shipping_charges']    = [];
            $tmp['cart_total']          = array(
                'total' => 0.00,
                'product_tax_exclusive_total' =>  0.00,
                'product_tax_exclusive_total_without_format' => 0,
                'tax_total' =>  0.00,
                'tax_percentage' =>  0.00,
                'shipping_charge' =>  0.00,
                'addon_amount' => 0,
                'coupon_amount' => 0,
                'has_pickup_store' => 0,
                'brand_id' => "",
                'is_cod' => 0,
                'cod_amount' => 0,
                'is_coupon' => 0,
                'coupon_code' => 0,
                'coupon_percentage' => 0,
                'coupon_type' => 0
            );
            return $tmp;
            // }
        }
        $cart_list = $this->getCartListAll($customer_id, $guest_token, null, null, $selected_shipping, null, $address);
        if ($cart_list['cart_count'] > 15) {
            log::info('---------------------Cart count Debug Begin----------------------');
            log::debug($request->all());
            log::debug($cart_list);
            log::info('---------------------Cart count Debug End----------------------');
        }
        return $cart_list;
    }

    function getCartListAll($customer_id = null, $guest_token = null,  $shipping_info = null, $shipping_type = null, $selected_shipping = null, $coupon_data = null, $address = null)
    {

        // dd( $coupon_data );
        // $checkCart          = Cart::with(['products', 'products.productCategory', 'variationOptions'])->when($customer_id != '', function ($q) use ($customer_id) {
        //     $q->where('customer_id', $customer_id);
        // })->when($customer_id == '' && $guest_token != '', function ($q) use ($guest_token) {
        //     $q->where('guest_token', $guest_token);
        // })->get();
        // foreach ($checkCart as $cartItem) {

        // }
        $checkCart = Cart::with(['products', 'products.productCategory', 'variationOptions']);
        if (!empty($customer_id)) {
            $checkCart = $checkCart->where('customer_id', $customer_id);
        }
        if (!empty($guest_token) && empty($customer_id)) {
            $checkCart = $checkCart->where('guest_token', $guest_token);
        }
        $checkCart = $checkCart->get();

        if (empty($customer_id) && empty($guest_token)) {
            $checkCart = 0;
        }

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
                    $total_discount_amount = 0;
                    if (isset($citems->variationOptions) && !empty($citems->variationOptions)) {
                        foreach ($citems->variationOptions as $variationids) {
                            $variation_option_id[] = $variationids->variation_option_id;
                        }
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
                                $discount_amount = $value->discount_amount;
                                $total_variation_amount = $total_variation_amount + $amount;
                                $total_discount_amount = $total_discount_amount + $discount_amount;
                            }
                        }
                    }
                    if (isset($selected_value) && !empty($selected_value)) {
                        $items->mrp = ($items->strike_price + $total_variation_amount) - $total_discount_amount;
                        $strike_price = $items->strike_price + $total_variation_amount;
                        $items->discount_percentage = ($total_discount_amount > 0) ? getDiscountPercentage($items->mrp, $strike_price) : 0;
                    } else {
                        $strike_price = $items->strike_price;
                    }

                    $category               = $items->productCategory;
                    if (isset($citems->coupon_id)) {
                        // $price=$items->strike_price /(1+$tax_data);
                        $price_with_tax         = $strike_price;
                        $citems->sub_total = round($price_with_tax * $citems->quantity);
                        $citems->save();
                    } else {
                        // $price=$items->mrp /(1+$tax_data);
                        $price_with_tax         = $items->mrp;
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
                    $pro['price']           = $items->mrp;
                    $pro['total_variation_amount'] = $total_variation_amount;
                    $pro['total_discount_amount'] = $total_discount_amount;
                    $pro['strike_price']    = number_format($strike_price, 2);
                    $pro['save_price']      = $strike_price - $items->mrp;
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

            // if (isset($address) && (!empty($address))) {
            //     $shipping_charges = $this->getShippingChargesFromShiprocket($address, $customer_id);
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

            // if (isset($coupon_data) && !empty($coupon_data)) {
            //     $grand_total = (float)$grand_total - $coupon_data['discount_amount'] ?? 0;
            // }

            if (count(array_unique($brand_array)) > 1) {
                $has_pickup_store = false;
            }

            // $amount         = filter_var($grand_total, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            // $charges        = ShippingCharge::select('id', 'shipping_title', 'minimum_order_amount', 'charges', 'is_free')->where('status', 'published')->where('minimum_order_amount', '<', $amount)->get();

            // $tmp['shipping_charges']    = $charges;

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
                if ($coupon_amount < 1) {
                    $is_coupon = 0;
                }
            }
            $tmp['cart_total']          = array(
                'total' => number_format(round($grand_total), 2),
                'product_tax_exclusive_total' => number_format(round($product_tax_exclusive_total), 2),
                'product_tax_exclusive_total_without_format' => round($product_tax_exclusive_total),
                'tax_total' => number_format(round($tax_total), 2),
                'tax_percentage' => number_format(round($tax_percentage), 2),
                'shipping_name' => ucwords(str_replace('_', '', $shipping_name)),
                'shipping_charge' => number_format($shipping_amount, 2),
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

    public function getShippingRocketCharges(Request $request, ShipRocketService $service)
    {

        $from_type = $request->from_type;
        $address = $request->address;
        if (!isset($address) && (empty($address))) {
            return response()->json(array('error' => 1, 'status_code' => 400, 'message' => 'Address not set', 'status' => 'failure', 'data' => []), 200);
        }
        $shippingAddress = CustomerAddress::find($address);
        log::debug($shippingAddress);
        log::debug('address id is' . $address);
        $customer_id = $request->customer_id;

        $cart_info = Cart::where('customer_id', $customer_id)->first(); //get from token
        /**
         * get volume metric value for kg
         */
        $all_cart = Cart::where('customer_id', $customer_id)->get();
        $is_free = [];
        $flat_charges = 0;
        $overall_flat_charges = 0;
        // dd( $all_cart );
        if (isset($all_cart) && !empty($all_cart)) {
            foreach ($all_cart as $item) {
                $pro = $item->products;
                $brandId = $pro->brand_id;
                // CartShiprocketResponse::where('cart_token', $item->cart_order_no)->delete();
                $brand_data = Brands::find($brandId);
                if (isset($brand_data)) {
                    $is_free[] = $brand_data->is_free_shipping;

                    if ($brand_data->is_free_shipping == 1) {
                        $item->shipping_fee_id = 1;
                        $item->update();
                    } else {
                        $item->shipping_fee_id = NULL;
                        $item->update();
                    }
                }

                // Fetch or create base unique ID for the customer
                $base_unique_id = Cart::where('customer_id', $customer_id)
                    ->where('brand_id', $brandId)
                    ->value('base_unique_id');

                if (!$base_unique_id) {
                    // Create a new base unique ID if it doesn't exist
                    $base_unique_id = $customer_id.'-'.date('YmdHis'); // Generate a new unique ID
                    $item->base_unique_id = $base_unique_id;
                    $item->update();
                }

                // Determine suffix based on brand_id and ensure uniqueness
                $existingCarts = Cart::where('customer_id', $customer_id)
                    ->where('base_unique_id', $base_unique_id)
                    ->get();

                if ($existingCarts->isNotEmpty()) {
                    $suffix = $existingCarts->where('brand_id', $brandId)->first()->suffix ?? null;

                    if (!$suffix) {
                        // Generate a new suffix if it does not exist for the brand
                        $maxSuffix = $existingCarts->max('suffix');
                        $suffix = str_pad($maxSuffix + 1, 2, '0', STR_PAD_LEFT); // Increment suffix and ensure it's two digits

                        // Store the new suffix in the database
                        $item->suffix = $suffix;
                        $item->update();
                    }
                } else {
                    // If no existing carts, start suffix from '01'
                    $suffix = '01';
                    $item->suffix = $suffix;
                    $item->update();
                }

                // Generate the unique number
                $unique_number = $suffix ? $base_unique_id . '-' . $suffix : $base_unique_id;

                // Ensure unique_number is unique across different brands
                while (Cart::where('shiprocket_order_number', $unique_number)
                    ->where('brand_id', '!=', $item->brand_id)
                    ->exists()
                ) {
                    $suffix = str_pad(++$suffix, 2, '0', STR_PAD_LEFT); // Increment suffix
                    $unique_number = $base_unique_id . '-' . $suffix;
                }

                // Update item with the new unique number
                $item->shiprocket_order_number = $unique_number;
                $item->update();


                log::info($item->products->productMeasurement);
                $flat_charges = $flat_charges + getVolumeMetricCalculation($item->products->productMeasurement->length ?? 0, $item->products->productMeasurement->width ?? 0, $item->products->productMeasurement->hight ?? 0);
            }
        }
        // $uniqueIsFree = array_unique($is_free);
        // if (count($uniqueIsFree) === 1 && reset($uniqueIsFree) == 1) {
        //     $chargeData = ['shipping_title' => "Free Shipping1", 'is_free' => 1, 'charges' => 0];

        //     return response()->json(array('error' => 0, 'status_code' => 200, 'message' => 'Data loaded successfully', 'status' => 'success', 'data' => $chargeData), 200);
        // }
        if (!empty($flat_charges)) {

            $overall_flat_charges = $flat_charges * 50 ?? 0;
        }

        /**
         *  End Metric value calculation
         */
        if (isset($from_type) && !empty($from_type)) {

            CartAddress::where('customer_id', $customer_id)
                ->where('address_type', $from_type)->delete();
            $ins_cart = [];
            $ins_cart['cart_token'] = $cart_info->cart_order_no;
            $ins_cart['customer_id'] = $customer_id;
            $ins_cart['address_type'] = $from_type;
            $ins_cart['name'] = isset($shippingAddress->name) ? $shippingAddress->name : 'No name';
            $ins_cart['email'] = $shippingAddress->email;
            $ins_cart['mobile_no'] = $shippingAddress->mobile_no;
            $ins_cart['address_line1'] = $shippingAddress->address_line1;
            $ins_cart['country'] = 'india';
            $ins_cart['post_code'] = $shippingAddress->PostCode->pincode;
            $ins_cart['state'] = $shippingAddress->states->state_name;
            $ins_cart['city'] = $shippingAddress->city;

            $cart_address = CartAddress::create($ins_cart);
            $data = $service->getShippingRocketOrderDimensions($customer_id, $cart_info->cart_order_no ?? null, $cart_address->id);
        }
        if (isset($data)) {
            $chargeData = $data;
            // Log::debug("got the response from api for cart id " . $shipping_charge);
        } else {
            $chargeData = ['shipping_title' => "Flat Rate", 'is_free' => 0, 'charges' => round($overall_flat_charges)];
            Log::debug("did not get the response from api for cart id, calculated shipping charge based on volumetric calculation - " . $cart_info->id);
            Log::debug("overall flat charge" . $overall_flat_charges);
        }
        // $chargeData =  array('shiprocket_charges' => $data, 'flat_charge' => $shipping_charge);

        return response()->json(array('error' => 0, 'status_code' => 200, 'message' => 'Data loaded successfully', 'status' => 'success', 'data' => $chargeData), 200);
    }

    public function getShippingChargesFromShiprocket($address, $customer_id)
    {
        $from_type = "shipping";
        // $address = $request->address;
        $shippingAddress = CustomerAddress::find($address);
        // $customer_id = $request->customer_id;

        $cart_info = Cart::where('customer_id', $customer_id)->first(); //get from token
        /**
         * get volume metric value for kg
         */
        $all_cart = Cart::where('customer_id', $customer_id)->get();
        $is_free = [];
        $all_flat_charges = [];
        $overall_flat_charges = 0;
        $flat_charges = 0;
        // dd( $all_cart );
        if (isset($all_cart) && !empty($all_cart)) {
            log::info('works here');
            foreach ($all_cart as $item) {
                log::info('works here 1');
                $pro = $item->products;
                $brandId = $pro->brand_id;
                $brand_data = Brands::find($brandId);
                if (isset($brand_data)) {
                    $is_free[] = $brand_data->is_free_shipping;

                    if ($brand_data->is_free_shipping == 1) {
                        $item->shipping_fee_id = 1;
                        $item->update();
                    } else {
                        $item->shipping_fee_id = NULL;
                        $item->update();
                    }
                }
                log::debug($item->products->productMeasurement);
                $all_flat_charges[] = getVolumeMetricCalculation($item->products->productMeasurement->length ?? 0, $item->products->productMeasurement->width ?? 0, $item->products->productMeasurement->hight ?? 0);

                // $flat_charges = $flat_charges + getVolumeMetricCalculation($item->products->productMeasurement->length ?? 0, $item->products->productMeasurement->width ?? 0, $item->products->productMeasurement->hight ?? 0);
            }
            if (!empty($flat_charges)) {

                $volume_metric_weight = max($all_flat_charges);

                $flat_charges = $volume_metric_weight * 50 ?? 0;
            }
        }
        $uniqueIsFree = array_unique($is_free);
        log::debug($is_free);
        log::debug($uniqueIsFree);
        if (count($uniqueIsFree) === 1 && reset($uniqueIsFree) == 1) {
            $chargeData = ['shipping_title' => "Free Shipping", 'is_free' => 1, 'charges' => 0];

            return response()->json(array('error' => 0, 'status_code' => 200, 'message' => 'Data loaded successfully', 'status' => 'success', 'data' => $chargeData), 200);
        }
        if (!empty($flat_charges)) {

            $overall_flat_charges = $flat_charges * 50 ?? 0;
        }

        /**
         *  End Metric value calculation
         */
        if (isset($from_type) && !empty($from_type)) {

            CartAddress::where('customer_id', $customer_id)
                ->where('address_type', $from_type)->delete();
            $ins_cart = [];
            $ins_cart['cart_token'] = $cart_info->guest_token;
            $ins_cart['customer_id'] = $customer_id;
            $ins_cart['address_type'] = $from_type;
            $ins_cart['name'] = isset($shippingAddress->name) ? $shippingAddress->name : 'No name';
            $ins_cart['email'] = $shippingAddress->email;
            $ins_cart['mobile_no'] = $shippingAddress->mobile_no;
            $ins_cart['address_line1'] = $shippingAddress->address_line1;
            $ins_cart['country'] = 'india';
            $ins_cart['post_code'] = $shippingAddress->PostCode->pincode;
            $ins_cart['state'] = $shippingAddress->states->state_name;
            $ins_cart['city'] = $shippingAddress->city;
            $cart_address = CartAddress::create($ins_cart);
            log::info('works here');
            $data = $this->rocketService->getShippingRocketOrderDimensions($customer_id, $cart_info->guest_token ?? null, $cart_address->id);
            log::info('works here 1');
        }
        if (isset($data) && ($data['charges'] != 0 && ($data['is_free'] == 0))) {
            $chargeData = $data;
            // Log::debug("got the response from api for cart id " . $shipping_charge);
        } else {
            $chargeData = ['shipping_title' => "Flat Charge", 'is_free' => 0, 'charges' => round($overall_flat_charges)];
        }
        // $chargeData =  array('shiprocket_charges' => $data, 'flat_charge' => $shipping_charge);
        log::debug('got the value after adding to cart api');
        return $chargeData;
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

    public function getShipmentOrderIds($cart_ids)
    {
        if (!empty($cart_ids)) {
            $uniqueShiprocketOrderIds = CartShipment::whereIn('cart_id', $cart_ids)
                ->pluck('shiprocket_order_id')
                ->unique()
                ->values()
                ->toArray();
        }
        return $uniqueShiprocketOrderIds;
    }
}
