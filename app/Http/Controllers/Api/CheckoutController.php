<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OrderMail;
use App\Models\BrandOrder;
use App\Models\Cart;
use App\Models\GlobalSettings;
use App\Models\Master\Brands;
use App\Models\Master\BrandVendorLocation;
use App\Models\Master\EmailTemplate;
use App\Models\Master\OrderStatus;
use App\Models\Offers\Coupons;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderProduct;
use App\Models\OrderProductWarranty;
use App\Models\Payment;
use App\Models\Product\OrderProductAddon;
use App\Models\Product\Product;
use App\Models\CartShipment;
use App\Models\Master\CustomerAddress;
use App\Models\Master\Variation;
use App\Models\OrderProductVariationOption;
use App\Models\Product\ProductVariationOption;
use App\Models\Warranty;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Razorpay\Api\Api;
use PDF;
use Mail;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function proceedCod(Request $request)
    {

        // $checkout_infomation = json_decode($request->checkout_infomation);
        // dd( $checkout_infomation );
        // dump( $checkout_infomation->checkout_data );
        // dd( $checkout_infomation->cart_items );
        /***
         * Check order product is out of stock before proceed, if yes remove from cart and notify user
         * 1.insert in order table with status init
         * 2.INSERT IN Order Products
         * 
         */


        $billing_id = $request->billing_address_id ?? $request->customer_id;
        $shipping_id = $request->shipping_method['address_id'] ?? $request->customer_id;
        $billing_address = CustomerAddress::find($billing_id);
        $shipping_address = CustomerAddress::find($shipping_id);
        $customer_id            = $request->customer_id;
        $order_status           = OrderStatus::where('status', 'published')->where('order', 1)->first();
        $shipping_method        = $request->shipping_method;
        $checkout_data          = $request->cart_total;
        $cart_items             = $request->cart_items;
        $shipping_address       = $shipping_address;
        $billing_address        = $billing_address;
        $coupon_data            = $request->coupon_info ?? '';
        $pickup_store_address   = $request->pickup_store_address ?? '';

        $coupon_details = '';
        $coupon_code = '';
        $coupon_amount = 0;
        $coupon_percentage = '';
        $coupon_type = '';
        $coupon_id = 0;
        if (isset($checkout_data) && $checkout_data['is_coupon'] == 1) {
            $coupon_code = $checkout_data['coupon_code'];
            $coupon_amount = $checkout_data['coupon_amount'];
            $coupon_percentage = $checkout_data['coupon_percentage'];
            $coupon_type = $checkout_data['coupon_type'];
            $coupon_details = $coupon_data['coupon_type'] ?? NULL;
        }

        $pickup_address_details = '';
        if (isset($pickup_store_address) && !empty($pickup_store_address)) {
            $pickup_address_details = serialize($pickup_store_address);
        }

        #check product is out of stock
        $errors                 = [];
        if (isset($cart_items) && !empty($cart_items)) {
            foreach ($cart_items as $citem) {

                $product_id = $citem['id'];
                $product_info = Product::find($product_id);
                if ($product_info->quantity < $citem['quantity'] || $product_info->stock_status == 'out_of_stock' || $product_info->status == 'unpublished') {
                    $errors[]     = 'Due to Out of Stock, Kindly remove the product ' . $citem['product_name'];
                    $response['error'] = $errors;
                }
            }
        }
        if (!$shipping_method) {
            $message     = 'Shipping Method not selected';
            $error = 1;
            $response['error'] = $error;
            $response['message'] = $message;
        }
        if (!empty($errors)) {

            $error = 1;
            $response['error'] = $error;
            $response['message'] = implode(',', $errors);

            return $response;
        }

        $checkout_total = str_replace(',', '', $checkout_data['total']);
        $pay_amount  = filter_var($checkout_total, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        $shipping_amount = 0;
        $shippingTypes = [];
        $shipping_name = '';
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
                    $shipping_amount += $result->total_shipment_amount;
                    $shippingTypes[] = $result->shipping_type;
                }

                // Determine the final shipping type based on the rules provided
                $shipping_name = $this->determineFinalShippingType($shippingTypes);

                // Logging the total shipment amount and final shipping type
                Log::info("Total Shipment Amount for carts with more than one unique brand: " . $shipping_amount);
                Log::info("Final Shipping Type: " . $shipping_name);



        $order_ins['customer_id'] = $customer_id;
        $order_ins['customer_id'] = $customer_id;
        $order_ins['order_no'] = getOrderNo();


        $order_ins['amount'] = $pay_amount;
        $order_ins['tax_amount'] = $checkout_data['tax_total'] ? str_replace(',', '', $checkout_data['tax_total']) : 0;
        $order_ins['tax_percentage'] = $checkout_data['tax_percentage'] ?? 0;
        $order_ins['shipping_amount'] = $shipping_amount;
        $order_ins['coupon_amount'] = $coupon_amount ?? 0;
        $order_ins['coupon_code'] = $coupon_code ?? '';
        $order_ins['coupon_details'] = $coupon_details ?? '';
        $order_ins['coupon_percentage'] = $coupon_percentage ?? '';
        $order_ins['coupon_type'] = $coupon_type ?? '';
        $order_ins['sub_total'] = $checkout_data['product_tax_exclusive_total_without_format'];
        $order_ins['description'] = '';
        $order_ins['order_status_id'] = $order_status->id;
        $order_ins['status'] = 'pending';
        $order_ins['pickup_store_details'] = $pickup_address_details;

        $order_ins['billing_name'] = $billing_address->name;
        $order_ins['billing_email'] = $billing_address->email;
        $order_ins['billing_mobile_no'] = $billing_address->mobile_no;
        $order_ins['billing_address_line1'] = $billing_address->address_line1;
        $order_ins['billing_address_line2'] = $billing_address->address_line2 ?? null;
        $order_ins['billing_landmark'] = $billing_address->landmark ?? null;
        $order_ins['billing_country'] = $billing_address->countries->name ?? null;
        $order_ins['billing_post_code'] = $billing_address->post_code ?? null;
        $order_ins['billing_state'] = $billing_address->states->state_name ?? null;
        $order_ins['billing_city'] = $billing_address->city ?? null;

        $order_ins['shipping_name'] = $shipping_address->name ?? $billing_address->name;
        $order_ins['shipping_email'] = $shipping_address->email ?? $billing_address->email;
        $order_ins['shipping_mobile_no'] = $shipping_address->mobile_no ?? $billing_address->mobile_no;
        $order_ins['shipping_address_line1'] = $shipping_address->address_line1 ?? $billing_address->address_line1;
        $order_ins['shipping_address_line2'] = $shipping_address->address_line2 ?? $billing_address->address_line2 ?? null;
        $order_ins['shipping_landmark'] = $shipping_address->landmark ?? $billing_address->landmark ?? null;
        $order_ins['shipping_country'] = $shipping_address->countries->name ?? $billing_address->countries->name ?? null;
        $order_ins['shipping_post_code'] = $shipping_address->post_code ?? $billing_address->post_code;
        $order_ins['shipping_state'] = $shipping_address->states->state_name ?? $billing_address->states->state_name ?? null;
        $order_ins['shipping_city'] = $shipping_address->city ?? $billing_address->city ?? null;
        $order_ins['is_cod'] = $checkout_data['is_cod'] ?? 0;
        $order_ins['cod_amount'] = $checkout_data['cod_amount'] ?? NULL;
        $order_ins['shipping_type'] = $shipping_name;


        // if (isset($shipping_method) && $shipping_method != 'PICKUP_FROM_STORE' && isset($shipping_address) && !empty($shipping_address)) {

        //     $shipping_type_info = ShippingCharge::find($shipping_method['charge_id']);

        //     $order_ins['shipping_options'] = $shipping_method['charge_id'] ?? 0;
        //     if ($shipping_type_info) {
        //         $order_ins['shipping_type'] = $shipping_type_info->shipping_title ?? 'Free';
        //     }
        // } else {

        //     $order_ins['pickup_store_id'] = $request->pickup_store_id;
        // }

        $order_info = Order::create($order_ins);
        $order_id = $order_info->id;

        if (isset($cart_items) && !empty($cart_items)) {
            foreach ($cart_items as $item) {
                $product_info = Product::find($item['id']);
                $items_ins['order_id'] = $order_id;
                $items_ins['product_id'] = $item['id'];
                $items_ins['product_name'] = $item['product_name'];
                $items_ins['image'] = $item['image'];
                $items_ins['hsn_code'] = $item['hsn_no'];
                $items_ins['sku'] = $item['sku'];
                $items_ins['quantity'] = $item['quantity'];
                $items_ins['price'] = $item['price'];
                $items_ins['strice_price'] = $item['strike_price'];
                $items_ins['save_price'] = $item['save_price'];
                $items_ins['base_price'] = $item['tax']['basePrice'];
                $items_ins['tax_amount'] = ($item['tax']['gstAmount'] ?? 0) * $item['quantity'];
                $items_ins['tax_percentage'] = $item['tax']['tax_percentage'] ?? 0;
                $items_ins['sub_total'] = $item['sub_total'];
                $cart_data_coupon = Cart::find($item['cart_id']);
                $items_ins['coupon_id'] = isset($cart_data_coupon) ? $cart_data_coupon->coupon_id : NULL;

                $order_product_info = OrderProduct::create($items_ins);

                $brandIds[] = $product_info->brand_id;
                $ins['order_id'] = $order_info->id;
                $ins['brand_id'] = $product_info->brand_id;
                $ins['order_product_id'] = $order_product_info->id;
                $ins['qty'] = $order_product_info->quantity;
                $ins['price'] = $order_product_info->price;
                $ins['sub_total'] = $order_product_info->sub_total;
                $ins['total_excluding_tax'] = $order_product_info->sub_total - $order_product_info->tax_amount;
                $ins['order_status_id'] = $order_status->id;
                $brand_data = Brands::find($ins['brand_id']);
                $ins['commission_type'] = $brand_data->commission_type;
                $ins['commission_value'] = $brand_data->commission_value;
                $cart_shipment = CartShipment::where('cart_id', $item['cart_id'])->first();
                $ins['shipping_amount'] = isset($cart_shipment) ? $cart_shipment->shipping_amount : NULL;
                $ins['shiprocket_amount'] = isset($cart_shipment) ? $cart_shipment->shiprocket_amount : NULL;
                $ins['shipping_id'] = isset($cart_shipment) ? $cart_shipment->shiprocket_amount : NULL;
                $ins['shipping_type'] = isset($cart_shipment) ? $cart_shipment->shiprocket_amount : NULL;
                $brand_order = BrandOrder::create($ins);

                //insert variations
                if (isset($item['chosen_variation_option_ids']) && !empty($item['chosen_variation_option_ids'])) {
                    foreach ($item['chosen_variation_option_ids'] as $variation_option_id) {
                        $product_variation_option = ProductVariationOption::find($variation_option_id);
                        if (isset($product_variation_option) && (!empty($product_variation_option))) {
                            $cart_product_variation_ins['order_id'] = $order_id;
                            $cart_product_variation_ins['order_product_id'] = $order_product_info->id;
                            $cart_product_variation_ins['variation_id'] = $product_variation_option->variation_id;
                            $cart_product_variation_ins['variation_option_id'] = $product_variation_option->id;
                            $cart_product_variation_ins['value'] = $product_variation_option->value;
                            $cart_product_variation_ins['amount'] = $product_variation_option->amount;
                            $cart_product_variation_ins['discount_amount'] = $product_variation_option->discount_amount;
                            OrderProductVariationOption::create($cart_product_variation_ins);
                        }
                    }
                }

                if (isset($product_info->warranty_id) && !empty($product_info->warranty_id)) {
                    $warranty_info = Warranty::find($product_info->warranty_id);
                    if ($warranty_info) {
                        $war = [];
                        $war['order_product_id'] = $order_product_info->id;
                        $war['product_id'] = $order_product_info->product_id;
                        $war['warranty_id'] = $warranty_info->id;
                        $war['warranty_name'] = $warranty_info->name;
                        $war['description'] = $warranty_info->description;
                        $war['warranty_period'] = $warranty_info->warranty_period;
                        $war['warranty_period_type'] = $warranty_info->warranty_period_type;
                        $war['warranty_start_date'] = date('Y-m-d');
                        $war['warranty_end_date'] = getEndWarrantyDate($warranty_info->warranty_period, $warranty_info->warranty_period_type);
                        $war['status'] = 'active';
                        OrderProductWarranty::create($war);
                    }
                }

                /**
                 * insert addons data
                 */
                if (isset($item->addons) && !empty($item->addons)) {
                    foreach ($item->addons as $aitems) {
                        $add_ins = [];
                        $add_ins['order_id'] = $order_id;
                        $add_ins['product_id'] = $item->id;
                        $add_ins['addon_id'] = $aitems->addon_id;
                        $add_ins['addon_item_id'] = $aitems->addon_item_id;
                        $add_ins['title'] = $aitems->title;
                        $add_ins['addon_item_label'] = $aitems->addon_item_label;
                        $add_ins['amount'] = $aitems->amount;
                        $add_ins['icon'] = $aitems->icon;
                        $add_ins['description'] = $aitems->description;

                        OrderProductAddon::create($add_ins);
                    }
                }
            }
        }

        /**** order history */
        $his['order_id'] = $order_info->id;
        $his['action'] = 'Order Placed';
        $his['description'] = 'Order has been Placed successfully';
        OrderHistory::create($his);
        $delete_cart = Cart::where('customer_id', $customer_id)->get();
        foreach($delete_cart as $delete_data){
            $delete_data->variationOptions()->delete();
            $delete_data->rocketResponse()->delete();
            $delete_data->shipments()->delete();
            $delete_data->delete();
        }
        if ($order_info) {
            $order_status    = OrderStatus::where('status', 'published')->where('order', 2)->first();

            $order_info->status = 'placed';
            $order_info->order_status_id = $order_status->id;
            $order_info->save();

            $order_items = OrderProduct::where('order_id', $order_info->id)->get();

            if (!is_null($order_info->coupon_code)) {
                $AppliedCoupon =  Coupons::where('coupon_code', $order_info->coupon_code)->first();
                if ($AppliedCoupon) {
                    $AppliedCoupon->quantity = $AppliedCoupon->quantity ?? 0 - 1;
                    $AppliedCoupon->save();
                }
            }

            if (isset($order_items) && !empty($order_items)) {
                foreach ($order_items as $product) {
                    $product_info = Product::find($product->product_id);
                    $pquantity = $product_info->quantity - $product->quantity;
                    $product_info->quantity = $pquantity;
                    if ($pquantity == 0) {
                        $product_info->stock_status = 'out_of_stock';
                        $product_info->status = 'unpublished';
                    }
                    $product_info->save();
                }
            }
            $pay_ins['order_id'] = $order_info->id;
            $pay_ins['payment_no'] = Null;
            $pay_ins['amount'] = $order_info->amount;
            $pay_ins['paid_amount'] = $order_info->amount;
            $pay_ins['payment_type'] = 'cod';
            $pay_ins['payment_mode'] = 'cod';
            $pay_ins['response'] = Null;
            $pay_ins['status'] = 'pending';

            Payment::create($pay_ins);

            /**** order history */
            $his['order_id'] = $order_info->id;
            $his['action'] = 'Order Placed';
            $his['description'] = 'Order has been placed successfully';
            OrderHistory::create($his);
            #generate invoice
            $globalInfo = GlobalSettings::first();
            $pickup_details = [];
            if (isset($order_info->pickup_store_id) && !empty($order_info->pickup_store_id) && !empty($order_info->pickup_store_details)) {
                $pickup = unserialize($order_info->pickup_store_details);

                $pickup_details = $pickup;
            }
            $variation_id = [];
            $variations = [];
            if (isset($order_info->Variation) && !empty($order_info->Variation)) {
                $data = $order_info->Variation;
                foreach ($data as $value) {
                    $variation_id[] = $value->variation_id;
                }
                $variations = Variation::whereIn('id', $variation_id)->get();
            }

            $pdf = PDF::loadView('platform.invoice.index', compact('order_info', 'globalInfo', 'pickup_details', 'variations'));
            Storage::put('public/invoice_order/' . $order_info->order_no . '.pdf', $pdf->output());
            #send mail
            $emailTemplate = EmailTemplate::select('email_templates.*')
                ->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id')
                ->where('sub_categories.slug', 'new-order')->first();

            $globalInfo = GlobalSettings::first();

            $extract = array(
                'name' => $order_info->billing_name,
                'regards' => $globalInfo->site_name,
                'company_website' => '',
                'company_mobile_no' => $globalInfo->site_mobile_no,
                'company_address' => $globalInfo->address,
                'dynamic_content' => '',
                'order_id' => $order_info->order_no
            );
            $templateMessage = $emailTemplate->message;
            $templateMessage = str_replace("{", "", addslashes($templateMessage));
            $templateMessage = str_replace("}", "", $templateMessage);
            extract($extract);
            eval("\$templateMessage = \"$templateMessage\";");

            $title = $emailTemplate->title;
            $title = str_replace("{", "", addslashes($title));
            $title = str_replace("}", "", $title);
            eval("\$title = \"$title\";");

            $filePath = 'storage/invoice_order/' . $order_info->order_no . '.pdf';
            $send_mail = new OrderMail($templateMessage, $title, $filePath);
            // return $send_mail->render();
            try {
                $bccEmails = explode(',', env('ORDER_EMAILS'));
                Mail::to($order_info->billing_email)->bcc($bccEmails)->send($send_mail);
            } catch (\Throwable $th) {
                Log::info($th->getMessage());
            }
            $this->sendBrandVendorEmail($brandIds, $order_info->id); //email to brand vendor
            #send sms for notification
            $sms_params = array(
                'company_name' => env('APP_NAME'),
                'order_no' => $order_info->order_no,
                'reference_no' => '',
                'mobile_no' => [$order_info->billing_mobile_no]
            );
            sendGBSSms('confirm_order', $sms_params);
        }
        return  array('success' => true, 'message' => 'Order has been placed successfully');
    }
    public function proceedCheckout(Request $request)
    {

        $keyId = env('RAZORPAY_KEY');
        $keySecret = env('RAZORPAY_SECRET');

        // $checkout_infomation = json_decode($request->checkout_infomation);
        // dd( $checkout_infomation );
        // dump( $checkout_infomation->checkout_data );
        // dd( $checkout_infomation->cart_items );
        /***
         * Check order product is out of stock before proceed, if yes remove from cart and notify user
         * 1.insert in order table with status init
         * 2.INSERT IN Order Products
         * 
         */
        $billing_id = $request->billing_address_id ?? $request->customer_id;
        $shipping_id = $request->shipping_method['address_id'] ?? $request->customer_id;
        $billing_address = CustomerAddress::find($billing_id);
        $shipping_address = CustomerAddress::find($shipping_id);
        $shipment_amount = $request->shipment_amount;
        $customer_id            = $request->customer_id;
        $order_status           = OrderStatus::where('status', 'published')->where('order', 1)->first();
        $shipping_method        = $request->shipping_method;

        $checkout_data          = $request->cart_total;
        $cart_items             = $request->cart_items;
        $shipping_address       = $shipping_address;
        $billing_address        = $billing_address;
        $coupon_data            = $request->coupon_info ?? '';
        $pickup_store_address   = $request->pickup_store_address ?? '';

        $coupon_details = '';
        $coupon_code = '';
        $coupon_amount = 0;
        $coupon_percentage = '';
        $coupon_type = '';
        $coupon_id = 0;
        if (isset($checkout_data) && $checkout_data['is_coupon'] == 1) {
            $coupon_code = $checkout_data['coupon_code'];
            $coupon_amount = $checkout_data['coupon_amount'];
            $coupon_percentage = $checkout_data['coupon_percentage'];
            $coupon_type = $checkout_data['coupon_type'];
            $coupon_details = $coupon_data['coupon_type'] ?? NULL;
        }

        $pickup_address_details = '';
        if (isset($pickup_store_address) && !empty($pickup_store_address)) {
            $pickup_address_details = serialize($pickup_store_address);
        }

        #check product is out of stock
        $errors                 = [];
        if (isset($cart_items) && !empty($cart_items)) {
            foreach ($cart_items as $citem) {

                $product_id = $citem['id'];
                $product_info = Product::find($product_id);
                if ($product_info->quantity < $citem['quantity'] || $product_info->stock_status == 'out_of_stock' || $product_info->status == 'unpublished') {
                    $errors[]     = 'Due to Out of Stock, Kindly remove the product ' . $citem['product_name'];
                    $response['error'] = $errors;
                }
            }
        }
        if (!$shipping_method) {
            $message     = 'Shipping Method not selected';
            $error = 1;
            $response['error'] = $error;
            $response['message'] = $message;
        }
        if (!empty($errors)) {

            $error = 1;
            $response['error'] = $error;
            $response['message'] = implode(',', $errors);

            return $response;
        }

        $checkout_total = str_replace(',', '', $checkout_data['total']);
        $pay_amount  = filter_var($checkout_total, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $shipping_amount = 0;
        $shippingTypes = [];
        $shipping_name = '';
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
                    $shipping_amount += $result->total_shipment_amount;
                    $shippingTypes[] = $result->shipping_type;
                }

                // Determine the final shipping type based on the rules provided
                $shipping_name = $this->determineFinalShippingType($shippingTypes);

                // Logging the total shipment amount and final shipping type
                Log::info("Total Shipment Amount for carts with more than one unique brand: " . $shipping_amount);
                Log::info("Final Shipping Type: " . $shipping_name);

        $order_ins['customer_id'] = $customer_id;
        $order_ins['customer_id'] = $customer_id;
        $order_ins['order_no'] = getOrderNo();


        $order_ins['amount'] = $pay_amount;
        $order_ins['tax_amount'] = $checkout_data['tax_total'] ? str_replace(',', '', $checkout_data['tax_total']) : 0;
        $order_ins['tax_percentage'] = $checkout_data['tax_percentage'] ?? 0;
        $order_ins['shipping_amount'] = $shipping_amount;
        $order_ins['coupon_amount'] = $coupon_amount ?? 0;
        $order_ins['coupon_code'] = $coupon_code ?? '';
        $order_ins['coupon_details'] = $coupon_details ?? '';
        $order_ins['coupon_percentage'] = $coupon_percentage ?? '';
        $order_ins['coupon_type'] = $coupon_type ?? '';
        $order_ins['sub_total'] = $checkout_data['product_tax_exclusive_total_without_format'];
        $order_ins['description'] = '';
        $order_ins['order_status_id'] = $order_status->id;
        $order_ins['status'] = 'pending';
        $order_ins['pickup_store_details'] = $pickup_address_details;

        $order_ins['billing_name'] = $billing_address->name;
        $order_ins['billing_email'] = $billing_address->email;
        $order_ins['billing_mobile_no'] = $billing_address->mobile_no;
        $order_ins['billing_address_line1'] = $billing_address->address_line1;
        $order_ins['billing_address_line2'] = $billing_address->address_line2 ?? null;
        $order_ins['billing_landmark'] = $billing_address->landmark ?? null;
        $order_ins['billing_country'] = $billing_address->countries->name ?? null;
        $order_ins['billing_post_code'] = $billing_address->post_code ?? null;
        $order_ins['billing_state'] = $billing_address->states->state_name ?? null;
        $order_ins['billing_city'] = $billing_address->city ?? null;

        $order_ins['shipping_name'] = $shipping_address->name ?? $billing_address->name;
        $order_ins['shipping_email'] = $shipping_address->email ?? $billing_address->email;
        $order_ins['shipping_mobile_no'] = $shipping_address->mobile_no ?? $billing_address->mobile_no;
        $order_ins['shipping_address_line1'] = $shipping_address->address_line1 ?? $billing_address->address_line1;
        $order_ins['shipping_address_line2'] = $shipping_address->address_line2 ?? $billing_address->address_line2 ?? null;
        $order_ins['shipping_landmark'] = $shipping_address->landmark ?? $billing_address->landmark ?? null;
        $order_ins['shipping_country'] = $shipping_address->countries->name ?? $billing_address->countries->name ?? null;
        $order_ins['shipping_post_code'] = $shipping_address->post_code ?? $billing_address->post_code;
        $order_ins['shipping_state'] = $shipping_address->states->state_name ?? $billing_address->states->state_name ?? null;
        $order_ins['shipping_city'] = $shipping_address->city ?? $billing_address->city ?? null;
        $order_ins['shipping_type'] = $shipping_name;
        // if (isset($shipping_method) && $shipping_method != 'PICKUP_FROM_STORE' && isset($shipping_address) && !empty($shipping_address)) {

        //     $shipping_type_info = ShippingCharge::find($shipping_method['charge_id']);

        //     $order_ins['shipping_options'] = $shipping_method['charge_id'] ?? 0;
        //     if ($shipping_type_info) {
        //         $order_ins['shipping_type'] = $shipping_type_info->shipping_title ?? 'Free';
        //     }
        // } else {

        //     $order_ins['pickup_store_id'] = $request->pickup_store_id;
        // }

        $order_info = Order::create($order_ins);
        $order_id = $order_info->id;

        if (isset($cart_items) && !empty($cart_items)) {
            foreach ($cart_items as $item) {
                $product_info = Product::find($item['id']);
                $items_ins['order_id'] = $order_id;
                $items_ins['product_id'] = $item['id'];
                $items_ins['product_name'] = $item['product_name'];
                $items_ins['image'] = $item['image'];
                $items_ins['hsn_code'] = $item['hsn_no'];
                $items_ins['sku'] = $item['sku'];
                $items_ins['quantity'] = $item['quantity'];
                $items_ins['price'] = $item['price'];
                $items_ins['strice_price'] = $item['strike_price'];
                $items_ins['save_price'] = $item['save_price'];
                $items_ins['base_price'] = $item['tax']['basePrice'];
                $items_ins['tax_amount'] = ($item['tax']['gstAmount'] ?? 0) * $item['quantity'];
                $items_ins['tax_percentage'] = $item['tax']['tax_percentage'] ?? 0;
                $items_ins['sub_total'] = $item['sub_total'];
                $cart_data_coupon = Cart::find($item['cart_id']);
                $items_ins['coupon_id'] = isset($cart_data_coupon) ? $cart_data_coupon->coupon_id : NULL;

                $order_product_info = OrderProduct::create($items_ins);
                $brandIds[] = $product_info->brand_id;
                $ins['order_id'] = $order_info->id;
                $ins['brand_id'] = $product_info->brand_id;
                $ins['order_product_id'] = $order_product_info->id;
                $ins['qty'] = $order_product_info->quantity;
                $ins['price'] = $order_product_info->price;
                $ins['sub_total'] = $order_product_info->sub_total; //total amount including tax
                $ins['total_excluding_tax'] = $order_product_info->sub_total - $order_product_info->tax_amount;
                $ins['order_status_id'] = $order_status->id;
                $brand_data = Brands::find($ins['brand_id']);
                $ins['commission_type'] = $brand_data->commission_type;
                $ins['commission_value'] = $brand_data->commission_value;
                $cart_shipment = CartShipment::where('cart_id', $item['cart_id'])->first();
                $ins['shipping_amount'] = isset($cart_shipment) ? $cart_shipment->shipping_amount : NULL;
                $ins['shiprocket_amount'] = isset($cart_shipment) ? $cart_shipment->shiprocket_amount : NULL;
                $ins['shipping_id'] = isset($cart_shipment) ? $cart_shipment->shiprocket_amount : NULL;
                $ins['shipping_type'] = isset($cart_shipment) ? $cart_shipment->shiprocket_amount : NULL;
                $brand_order = BrandOrder::create($ins);

                if (isset($product_info->warranty_id) && !empty($product_info->warranty_id)) {
                    $warranty_info = Warranty::find($product_info->warranty_id);
                    if ($warranty_info) {
                        $war = [];
                        $war['order_product_id'] = $order_product_info->id;
                        $war['product_id'] = $order_product_info->product_id;
                        $war['warranty_id'] = $warranty_info->id;
                        $war['warranty_name'] = $warranty_info->name;
                        $war['description'] = $warranty_info->description;
                        $war['warranty_period'] = $warranty_info->warranty_period;
                        $war['warranty_period_type'] = $warranty_info->warranty_period_type;
                        $war['warranty_start_date'] = date('Y-m-d');
                        $war['warranty_end_date'] = getEndWarrantyDate($warranty_info->warranty_period, $warranty_info->warranty_period_type);
                        $war['status'] = 'active';
                        OrderProductWarranty::create($war);
                    }
                }
                //insert variations
                if (isset($item['chosen_variation_option_ids']) && !empty($item['chosen_variation_option_ids'])) {
                    foreach ($item['chosen_variation_option_ids'] as $variation_option_id) {
                        $product_variation_option = ProductVariationOption::find($variation_option_id);
                        $cart_product_variation_ins['order_id'] = $order_id;
                        $cart_product_variation_ins['order_product_id'] = $order_product_info->id;
                        $cart_product_variation_ins['variation_id'] = $product_variation_option->variation_id;
                        $cart_product_variation_ins['variation_option_id'] = $product_variation_option->id;
                        $cart_product_variation_ins['value'] = $product_variation_option->value;
                        $cart_product_variation_ins['amount'] = $product_variation_option->amount;
                        $cart_product_variation_ins['discount_amount'] = $product_variation_option->discount_amount;
                        OrderProductVariationOption::create($cart_product_variation_ins);
                    }
                }
                /**
                 * insert addons data
                 */
                if (isset($item->addons) && !empty($item->addons)) {
                    foreach ($item->addons as $aitems) {
                        $add_ins = [];
                        $add_ins['order_id'] = $order_id;
                        $add_ins['product_id'] = $item->id;
                        $add_ins['addon_id'] = $aitems->addon_id;
                        $add_ins['addon_item_id'] = $aitems->addon_item_id;
                        $add_ins['title'] = $aitems->title;
                        $add_ins['addon_item_label'] = $aitems->addon_item_label;
                        $add_ins['amount'] = $aitems->amount;
                        $add_ins['icon'] = $aitems->icon;
                        $add_ins['description'] = $aitems->description;

                        OrderProductAddon::create($add_ins);
                    }
                }
            }
        }

        /**** order history */
        $his['order_id'] = $order_info->id;
        $his['action'] = 'Order Initiate';
        $his['description'] = 'Order has been Initiated successfully';
        OrderHistory::create($his);

        try {

            $api = new Api($keyId, $keySecret);
            $orderData = [
                'receipt'         => $order_info->order_no,
                'amount'          => $pay_amount * 100,
                'currency'        => "INR",
                'payment_capture' => 1 // auto capture
            ];

            $razorpayOrder = $api->order->create($orderData);
            $razorpayOrderId = $razorpayOrder['id'];

            session()->put('razorpay_order_id', $razorpayOrderId);

            $amount = $orderData['amount'];
            $displayCurrency        = "INR";
            $data = [
                "key"               => $keyId,
                "amount"            => round($amount),
                "currency"          => "INR",
                "name"              => 'GOLI SODA Sustainable Solutions Private Limited',
                "image"             => asset(gSetting('logo')),
                "description"       => "Secure Payment",
                "prefill"           => [
                    "name"              => $billing_address->name,
                    "email"             => $billing_address->email,
                    "contact"           => $billing_address->mobile_no,
                ],
                "notes"             => [
                    "address"           => "",
                    "merchant_order_id" => $order_info->order_no,
                ],
                "theme"             => [
                    "color"             => "#F37254"
                ],
                "order_id"          => $razorpayOrderId,
            ];

            $order_info = Order::find($order_id);
            $order_info->payment_response_id = $razorpayOrderId;
            $order_info->save();

            return $data;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function verifySignature(Request $request)
    {

        $keyId = env('RAZORPAY_KEY');
        $keySecret = env('RAZORPAY_SECRET');

        $customer_id = $request->customer_id;

        $razor_response = $request->razor_response;
        $status = $request->status;

        $success = true;
        $error_message = "Payment Success";

        if (isset($razor_response['razorpay_payment_id']) && empty($razor_response['razorpay_payment_id']) === false) {
            $razorpay_order_id = $razor_response['razorpay_order_id'];
            $razorpay_signature = $razor_response['razorpay_signature'];
            // $razorpay_order_id = session()->get('razorpay_order_id');

            $api = new Api($keyId, $keySecret);
            $finalorder = $api->order->fetch($razorpay_order_id);

            try {
                $attributes = array(
                    'razorpay_order_id' => $razorpay_order_id,
                    'razorpay_payment_id' => $razor_response['razorpay_payment_id'],
                    'razorpay_signature' => $razor_response['razorpay_signature']
                );

                $api->utility->verifyPaymentSignature($attributes);
            } catch (SignatureVerificationError $e) {
                $success = false;
                $error_message = 'Razorpay Error : ' . $e->getMessage();
            }

            if ($success) {

                $delete_cart = Cart::where('customer_id', $customer_id)->get();
                foreach($delete_cart as $delete_data){
                    $delete_data->variationOptions()->delete();
                    $delete_data->rocketResponse()->delete();
                    $delete_data->shipments()->delete();
                    $delete_data->delete();
                }
                /** 
                 *  1. do quantity update in product
                 *  2. update order status and payment response
                 *  3. insert in payment entry 
                 */
                $order_info = Order::where('payment_response_id', $razorpay_order_id)->first();
                if ($order_info) {
                    $order_status    = OrderStatus::where('status', 'published')->where('order', 2)->first();

                    $order_info->status = 'placed';
                    $order_info->order_status_id = $order_status->id;
                    $order_info->save();

                    $order_items = OrderProduct::where('order_id', $order_info->id)->get();

                    $brand_orders = BrandOrder::where('order_id', $order_info->id)->get();
                    if (isset($brand_orders) && !empty($brand_orders)) {
                        foreach ($brand_orders as $brand_order) {
                            $brand_order->order_status_id =  $order_status->id;
                            $brand_order->save();
                        }
                    }

                    if (!is_null($order_info->coupon_code)) {
                        $AppliedCoupon =  Coupons::where('coupon_code', $order_info->coupon_code)->first();
                        if ($AppliedCoupon) {
                            $AppliedCoupon->quantity = $AppliedCoupon->quantity ?? 0 - 1;
                            $AppliedCoupon->save();
                        }
                    }

                    if (isset($order_items) && !empty($order_items)) {
                        foreach ($order_items as $product) {
                            $product_info = Product::find($product->product_id);
                            $pquantity = $product_info->quantity - $product->quantity;
                            $product_info->quantity = $pquantity;
                            $brandIds[] = $product_info->brand_id;
                            if ($pquantity == 0) {
                                $product_info->stock_status = 'out_of_stock';
                                $product_info->status = 'unpublished';
                            }
                            $product_info->save();
                        }
                    }

                    $pay_ins['order_id'] = $order_info->id;
                    $pay_ins['payment_no'] = $razor_response['razorpay_payment_id'];
                    $pay_ins['amount'] = $order_info->amount;
                    $pay_ins['paid_amount'] = $order_info->amount;
                    $pay_ins['payment_type'] = 'razorpay';
                    $pay_ins['payment_mode'] = 'online';
                    $pay_ins['response'] = serialize($finalorder);
                    $pay_ins['status'] = $finalorder['status'];

                    Payment::create($pay_ins);

                    /**** order history */
                    $his['order_id'] = $order_info->id;
                    $his['action'] = 'Order Placed';
                    $his['description'] = 'Order has been placed successfully';
                    OrderHistory::create($his);

                    /****
                     * 1.send email for order placed
                     * 2.send sms for notification
                     */
                    #generate invoice
                    $globalInfo = GlobalSettings::first();
                    $pickup_details = [];
                    if (isset($order_info->pickup_store_id) && !empty($order_info->pickup_store_id) && !empty($order_info->pickup_store_details)) {
                        $pickup = unserialize($order_info->pickup_store_details);

                        $pickup_details = $pickup;
                    }
                    $pdf = PDF::loadView('platform.invoice.index', compact('order_info', 'globalInfo', 'pickup_details'));
                    Storage::put('public/invoice_order/' . $order_info->order_no . '.pdf', $pdf->output());
                    #send mail
                    $emailTemplate = EmailTemplate::select('email_templates.*')
                        ->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id')
                        ->where('sub_categories.slug', 'new-order')->first();

                    $globalInfo = GlobalSettings::first();

                    $extract = array(
                        'name' => $order_info->billing_name,
                        'regards' => $globalInfo->site_name,
                        'company_website' => '',
                        'company_mobile_no' => $globalInfo->site_mobile_no,
                        'company_address' => $globalInfo->address,
                        'dynamic_content' => '',
                        'order_id' => $order_info->order_no
                    );
                    $templateMessage = $emailTemplate->message;
                    $templateMessage = str_replace("{", "", addslashes($templateMessage));
                    $templateMessage = str_replace("}", "", $templateMessage);
                    extract($extract);
                    eval("\$templateMessage = \"$templateMessage\";");

                    $title = $emailTemplate->title;
                    $title = str_replace("{", "", addslashes($title));
                    $title = str_replace("}", "", $title);
                    eval("\$title = \"$title\";");

                    $filePath = 'storage/invoice_order/' . $order_info->order_no . '.pdf';
                    $send_mail = new OrderMail($templateMessage, $title, $filePath);
                    // return $send_mail->render();
                    try {
                        $bccEmails = explode(',', env('ORDER_EMAILS'));
                        Mail::to($order_info->billing_email)->bcc($bccEmails)->send($send_mail);
                    } catch (\Throwable $th) {
                        Log::info($th->getMessage());
                    }
                    $this->sendBrandVendorEmail($brandIds, $order_info->id); //email to brand vendor
                    #send sms for notification
                    $sms_params = array(
                        'company_name' => env('APP_NAME'),
                        'order_no' => $order_info->order_no,
                        'reference_no' => '',
                        'mobile_no' => [$order_info->billing_mobile_no]
                    );
                    sendGBSSms('confirm_order', $sms_params);
                }
            }
        } else {
            $success = false;
            $error_message = 'Payment Failed';

            if (isset($request->razor_response['error']) && !empty($request->razor_response['error'])) {

                $orderdata = $request->razor_response['error']['metadata'];
                $razorpay_payment_id = $orderdata['payment_id'];
                $razorpay_order_id = $orderdata['order_id'];

                $api = new Api($keyId, $keySecret);

                $finalorder = $api->order->fetch($orderdata['order_id']);

                $order_info = Order::where('payment_response_id', $razorpay_order_id)->first();

                if ($order_info) {

                    $order_status    = OrderStatus::where('status', 'published')->where('order', 3)->first();

                    $order_info->status = 'payment_pending';
                    $order_info->order_status_id = $order_status->id;

                    $order_info->save();

                    $order_items = OrderProduct::where('order_id', $order_info->id)->get();


                    if (!is_null($order_info->coupon_code)) {
                        $AppliedCoupon =  Coupons::where('coupon_code', $order_info->coupon_code)->first();
                        if ($AppliedCoupon) {
                            $AppliedCoupon->quantity = $AppliedCoupon->quantity ?? 0 - 1;
                            $AppliedCoupon->save();
                        }
                    }

                    if (isset($order_items) && !empty($order_items)) {
                        foreach ($order_items as $product) {
                            $product_info = Product::find($product->id);
                            if ($product_info) {
                                $pquantity = $product_info->quantity ?? 0 - $product->quantity ?? 0; // reduce product Qty
                                $product_info->quantity = $pquantity ?? 0;
                                if ($pquantity == 0) {
                                    $product_info->stock_status = 'out_of_stock';
                                    $product_info->status = 'unpublished';
                                }
                                $product_info->save();
                            }
                        }
                    }

                    $pay_ins['order_id'] = $order_info->id;
                    $pay_ins['payment_no'] = $razorpay_payment_id;
                    $pay_ins['amount'] = $order_info->amount;
                    $pay_ins['paid_amount'] = $order_info->amount;
                    $pay_ins['payment_type'] = 'razorpay';
                    $pay_ins['payment_mode'] = 'online';
                    $pay_ins['description'] = $request->razor_response['error']['description'];
                    $pay_ins['response'] = serialize($finalorder);
                    $pay_ins['status'] = 'failed';

                    $error_message = $request->razor_response['error']['description'];

                    Payment::create($pay_ins);
                }
            }
        }

        return  array('success' => $success, 'message' => $error_message);
    }

    /**
     * Method generateInvoice
     *
     * @param Request $request [Generate invoice for customer who we enter data manually]
     *
     * @return void
     */
    public function generateInvoice(Request $request)
    {
        $order_no = $request->order_no;
        $order_info = Order::where('order_no', $order_no)->first();
        if ($order_info) {
            $globalInfo = GlobalSettings::first();

            $pdf = PDF::loadView('platform.invoice.index', compact('order_info', 'globalInfo'));
            Storage::put('public/invoice_order/' . $order_info->order_no . '.pdf', $pdf->output());
            return response()->json(array('error' => 0, 'status_code' => 200, 'message' => 'Invoice generated successfully', 'status' => 'success', 'success' => []), 200);
        }
        return response()->json(array('error' => 1, 'status_code' => 200, 'message' => 'Something went wrong', 'status' => 'failure', 'success' => []), 200);
    }

    /**
     * Method sendBrandVendorEmail
     *
     * @param $brandIds array
     *
     * @return void
     */
    public function sendBrandVendorEmail($brandIds, $order_id)
    {
        $uniqueBrandIds =  array_unique($brandIds);
        $globalInfo = GlobalSettings::first();
        if (!empty($uniqueBrandIds)) {
            foreach ($uniqueBrandIds as $singleBrandId) {
                $brandOrderData = BrandOrder::join('orders', 'orders.id', '=', 'brand_orders.order_id')
                    ->where([['brand_id', $singleBrandId], ['order_id', $order_id]])
                    ->get();
                if ($brandOrderData) {
                    $order_info = $brandOrderData[0]->order;
                    $variations = $this->getVariations($order_info);
                    $brand_address = BrandVendorLocation::where([['brand_id', $singleBrandId], ['is_default', 1]])->first();
                    if (isset($brand_address) && (!empty($brand_address))) {
                        $pdf = PDF::loadView('platform.vendor_invoice.index', compact('brand_address', 'order_info', 'globalInfo', 'variations', 'singleBrandId'));
                        Storage::put('public/invoice_order/' . $brandOrderData[0]->order_id . '/' . $singleBrandId . '/' . $brandOrderData[0]->order->order_no . '.pdf', $pdf->output());
                        $email_slug = 'new-order-vendor';
                        $to_email_address = $brand_address->email_id;
                        $globalInfo = GlobalSettings::first();
                        $filePath = 'storage/invoice_order/' . $brandOrderData[0]->order_id . '/' . $singleBrandId . '/' . $brandOrderData[0]->order->order_no . '.pdf';
                        $extract = array(
                            'name' => $brand_address->branch_name,
                            'regards' => $globalInfo->site_name,
                            'company_website' => '',
                            'company_mobile_no' => $globalInfo->site_mobile_no,
                            'company_address' => $globalInfo->address,
                            'dynamic_content' => '',
                            'order_id' => $order_info->order_no
                        );

                        $this->sendEmailNotificationByArray($email_slug, $extract, $to_email_address, $filePath);
                    }
                }
            }
        }
    }

    /**
     * Method sendEmailNotificationByArray
     *
     * @param $email_slug string
     * @param $extract array
     * @param $to_email_address string
     * @param $filePath string
     *
     * @return void
     */
    public function sendEmailNotificationByArray($email_slug, $extract, $to_email_address, $filePath)
    {
        $emailTemplate = EmailTemplate::select('email_templates.*')
            ->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id')
            ->where('sub_categories.slug', $email_slug)->first();

        $templateMessage = $emailTemplate->message;
        $templateMessage = str_replace("{", "", addslashes($templateMessage));
        $templateMessage = str_replace("}", "", $templateMessage);
        extract($extract);
        eval("\$templateMessage = \"$templateMessage\";");

        $title = $emailTemplate->title;
        $title = str_replace("{", "", addslashes($title));
        $title = str_replace("}", "", $title);
        eval("\$title = \"$title\";");

        // $filePath = 'storage/invoice_order/' . $order_info->order_no . '.pdf';
        $send_mail = new OrderMail($templateMessage, $title, $filePath);
        // return $send_mail->render();
        try {
            $bccEmails = explode(',', env('ORDER_EMAILS'));
            Mail::to($to_email_address)->bcc($bccEmails)->send($send_mail);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    /**
     * Method getVariations
     *
     * @param $order_info object
     *
     * @return array
     */
    public function getVariations($order_info)
    {
        $variation_id = [];
        $variations = [];
        if (isset($order_info->Variation) && !empty($order_info->Variation)) {
            $data = $order_info->Variation;
            foreach ($data as $value) {
                $variation_id[] = $value->variation_id;
            }
            $variations = Variation::whereIn('id', $variation_id)->get();
        }
        return $variations;
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
