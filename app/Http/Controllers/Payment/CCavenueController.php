<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Mail\OrderMail;
use App\Models\Cart;
use App\Models\CartProductAddon;
use App\Models\GlobalSettings;
use App\Models\Master\CustomerAddress;
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
use App\Models\Settings\Tax;
use App\Models\ShippingCharge;
use App\Models\StoreLocator;
use App\Models\Warranty;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Softon\Indipay\Facades\Indipay;
use PDF;
use Mail;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CCavenueController extends Controller
{
    public function index(Request $request)
    {
        $parameters = [
            'tid' => date('ymdhis'),
            'order_id' => '123654789',
            'amount' => '1.00',
            'billing_name' => 'Jon Doe',
            'billing_address' => 'annanagar, chennai',
            'billing_city' => 'chennai',
            'billing_state' => 'Tamil Nadu',
            'billing_zip' => '600032',
            'billing_country' => 'India',
            'billing_tel' => '1234567890',
            'billing_email' => 'pixel@gmail.com',
            'delivery_name' => 'Chaplin',
            'delivery_address' => 'room no.701 near bus stand',
            'delivery_city' => 'Hyderabad',
            'delivery_state' => 'Tamilnadu',
            'delivery_zip' => '600049',
            'delivery_country' => 'India',
            'delivery_tel' => '1234567890'

        ];

        $order = Indipay::prepare($parameters);
        return Indipay::process($order);

        return view('payment.ccavenue');
    }

    public function ccavRequestHandler(Request $request)
    {

        return view('payment.request_handler');
    }

    public function ccavResponseHandler(Request $request)
    {

        // For default Gateway
        $response = Indipay::response($request);
        // dd( $response );
        // For Otherthan Default Gateway
        $response = Indipay::gateway('CCAvenue')->response($request);

        $encrypted_order_no = '';
        if ($response) {
            $paid_amount = $response['amount'];
            $order_no = $response['order_id'];
            $order_status = $response['order_status'];
            $order_info = Order::where('order_no', $order_no)->first();

            if ($order_info) {
                #generate invoice
                $globalInfo = GlobalSettings::first();
                $pickup_details = [];
                if (isset($order_info->pickup_store_id) && !empty($order_info->pickup_store_id) && !empty($order_info->pickup_store_details)) {
                    $pickup = unserialize($order_info->pickup_store_details);
                    $pickup_details = $pickup;
                }
                $pdf = PDF::loadView('platform.invoice.index', compact('order_info', 'globalInfo', 'pickup_details'));
                Storage::put('public/invoice_order/' . $order_info->order_no . '.pdf', $pdf->output());
            }

            $encrypted_order_no = base64_encode($order_info->order_no);
            if (strtolower($order_status) == 'success' && $order_info->amount == $paid_amount) {
                /*
                Success Payment
                */
                $cart_data = Cart::where('customer_id', $order_info->customer_id)->get();
                if (isset($cart_data) && !empty($cart_data)) {
                    foreach ($cart_data as $item) {
                        $item->addons()->delete();
                        $item->delete();
                    }
                }
                /** 
                 *  1. do quantity update in product
                 *  2. update order status and payment response
                 *  3. insert in payment entry 
                 */
                if ($order_info) {
                    $order_status    = OrderStatus::where('status', 'published')->where('order', 2)->first();

                    $order_info->status = 'placed';
                    $order_info->order_status_id = $order_status->id;
                    $order_info->save();

                    $order_items = OrderProduct::where('order_id', $order_info->id)->get();

                    if(!is_null($order_info->coupon_code)) {
                        $AppliedCoupon =  Coupons::where('coupon_code', $order_info->coupon_code)->first();
                        if(isset($AppliedCoupon) && $AppliedCoupon !=null ){
                        $AppliedCoupon->quantity = $AppliedCoupon->quantity - 1;
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
                            }
                            $product_info->save();
                        }
                    }

                    $pay_ins['order_id'] = $order_info->id;
                    $pay_ins['payment_no'] = $response['tracking_id'] ?? '';
                    $pay_ins['amount'] = $order_info->amount;
                    $pay_ins['paid_amount'] = $order_info->amount;
                    $pay_ins['payment_type'] = 'ccavenue';
                    $pay_ins['payment_mode'] = $response['payment_mode'] ?? 'online';
                    $pay_ins['response'] = serialize($response);
                    $pay_ins['status'] = 'pending';

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
                    extract($extract);
                    
                    $templateMessage = $emailTemplate->message;
                    $templateMessage = str_replace("{", "", addslashes($templateMessage));
                    $templateMessage = str_replace("}", "", $templateMessage);
                    eval("\$templateMessage = \"$templateMessage\";");

                    $title = $emailTemplate->title;
                    $title = str_replace("{", "", addslashes($title));
                    $title = str_replace("}", "", $title);
                    eval("\$title = \"$title\";");

                    $filePath = 'storage/invoice_order/' . $order_info->order_no . '.pdf';
                    $send_mail = new OrderMail($templateMessage, $title, $filePath);
                    // return $send_mail->render();
                    try {
                         $bccEmails = explode(',', env('BCC_EMAILS'));
                     $bccRecipients = array_merge($bccEmails, [$order_info->billing_email]);
                        Mail::to($order_info->billing_email)->bcc($bccRecipients)->send($send_mail);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }

                    #send sms for notification
                    $sms_params = array(
                        'company_name' => env('APP_NAME'),
                        'order_no' => $order_info->order_no,
                        'reference_no' => '',
                        'mobile_no' => [$order_info->billing_mobile_no]
                    );
                    sendGBSSms('confirm_order', $sms_params);

                    $success = true;
                    $error_message = "Payment Success";

                    $order_info->response_amount = $paid_amount;
                    $order_info->save();
                }
            } else {
                /*
                Failed Payment
                */
                $success = false;
                $error_message = 'Payment Failed';

                if ($order_info) {

                    $order_status    = OrderStatus::where('status', 'published')->where('order', 3)->first();

                    $order_info->status = 'payment_pending';
                    $order_info->order_status_id = $order_status->id;

                    $order_info->save();

                    $order_items = OrderProduct::where('order_id', $order_info->id)->get();

                    if(!is_null($order_info->coupon_code)) {
                        $AppliedCoupon =  Coupons::where('coupon_code', $order_info->coupon_code)->first();
                        $AppliedCoupon->quantity = $AppliedCoupon->quantity - 1;
                        $AppliedCoupon->save();
                    }

                    if (isset($order_items) && !empty($order_items)) {
                        foreach ($order_items as $product) {
                            $product_info = Product::find($product->id);
                            $pquantity = $product_info->quantity - $product->quantity;
                            $product_info->quantity = $pquantity;
                            if ($pquantity == 0) {
                                $product_info->stock_status = 'out_of_stock';
                            }
                            $product_info->save();
                        }
                    }

                    $pay_ins['order_id'] = $order_info->id;
                    $pay_ins['payment_no'] = $response['tracking_id'] ?? '';
                    $pay_ins['amount'] = $order_info->amount;
                    $pay_ins['paid_amount'] = $order_info->amount;
                    $pay_ins['payment_type'] = 'ccavenue';
                    $pay_ins['payment_mode'] = $response['payment_mode'] ?? 'online';
                    $pay_ins['description'] = null;
                    $pay_ins['response'] = serialize($response);
                    $pay_ins['status'] = 'failed';

                    Payment::create($pay_ins);
                }
            }
        } else {
            $success = false;
            $error_message = 'Payment Failed';
        }

        return redirect()->away('https://www.gbssystems.com/verify-payment/' . $encrypted_order_no);

        return  array('success' => $success, 'message' => $error_message);
    }

    public function proceedCheckout(Request $request)
    {

        $validate_array     = [
            'billing_address_id' => 'required',
            'shipping_method' => 'required'
        ];
        $validator      = Validator::make($request->all(), $validate_array);
        if ($validator->passes()) {

            $customer_id            = $request->customer_id;
            $billing_address_id     = $request->billing_address_id;
            $shipping_method        = $request->shipping_method;

            $coupon_amount = 0;
            $shippping_fee_amount = 0;

            $cart_info = Cart::selectRaw('sum(sub_total) as total, coupon_id, coupon_amount, shipping_fee_id, shipping_fee')->where('customer_id', $customer_id)->first();

            $cart_addon_info = CartProductAddon::selectRaw('sum(gbs_cart_product_addons.amount) as addon_total')
                ->join('carts', 'carts.id', '=', 'cart_product_addons.cart_id')->where('customer_id', $customer_id)->first();

            $cart_items = Cart::where('customer_id', $customer_id)->get();

            $total_order_value = 0;

            if ($cart_info) {
                $coupon_amount = $cart_info->coupon_amount ?? 0;
                if ($cart_info->coupon_id ?? '') {

                    $coupon_code = Coupons::find($cart_info->coupon_id);
                }
                $shippping_fee_amount = ($cart_info->shipping_fee ?? 0);
                $total_order_value = ($cart_info->total + $cart_addon_info->addon_total ?? 0) - ($cart_info->coupon_amount ?? 0) + ($cart_info->shipping_fee ?? 0);
            }

            $order_status           = OrderStatus::where('status', 'published')->where('order', 1)->first();

            $shipping_method_name   = $shipping_method['type'];

            // $cart_items             = $checkout_infomation->cart_items;
            $billing_address        = CustomerAddress::find($billing_address_id);

            if ($shipping_method_name == 'STANDARD_SHIPPING') {
                $shipping_address       = CustomerAddress::find($shipping_method['address_id']);
                $shipping_type_info     = ShippingCharge::find($shipping_method['charge_id']);
                $order_ins['shipping_options'] = $shipping_method['charge_id'] ?? 0;
                if ($shipping_type_info) {
                    $order_ins['shipping_type'] = $shipping_type_info->shipping_title ?? 'Free';
                }
            } else {
                $pickup_store_address   = StoreLocator::find($shipping_method['address_id']);

                $order_ins['pickup_store_id'] = $shipping_method['address_id'];
            }

            $pickup_address_details = '';
            if (isset($pickup_store_address) && !empty($pickup_store_address)) {
                $pickup_address_details = serialize($pickup_store_address);
            }

            #check product is out of stock
            $errors                 = [];
            $tax_total          = 0;
            $product_tax_exclusive_total = 0;
            $tax_percentage = 0;
            if (isset($cart_items) && !empty($cart_items)) {
                foreach ($cart_items as $citem) {

                    $tax = [];
                    $tax_percentage = 0;

                    $product_id = $citem->product_id;
                    $product_info = Product::find($product_id);

                    if ($product_info->quantity < $citem->quantity) {
                        $errors[]     = $citem->product_name . ' is out of stock, Product will be removed from cart.Please choose another';
                        $response['error'] = $errors;
                    }
                    /** 
                     * do tax calculation here
                     */
                    $items = $citem->products;
                    $category               = $items->productCategory;
                    $price_with_tax         = $items->mrp;
                    if (isset($category->parent->tax_id) && !empty($category->parent->tax_id)) {
                        $tax_info = Tax::find($category->parent->tax_id);
                    } else if (isset($category->tax_id) && !empty($category->tax_id)) {
                        $tax_info = Tax::find($category->tax_id);
                    }
                    // dump( $citems );
                    if (isset($tax_info) && !empty($tax_info)) {
                        $tax = getAmountExclusiveTax($price_with_tax, $tax_info->pecentage);
                        $tax_total =  $tax_total + ($tax['gstAmount'] * $citem->quantity) ?? 0;
                        $product_tax_exclusive_total = $product_tax_exclusive_total + ($tax['basePrice'] * $citem->quantity);
                        // print_r( $product_tax_exclusive_total );
                        $tax_percentage         = $tax['tax_percentage'] ?? 0;
                    } else {
                        $product_tax_exclusive_total = $product_tax_exclusive_total + $citem->sub_total;
                    }
                }
            }

            if (!$shipping_method) {
                $message = 'Shipping Method not selected';
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

            $order_ins['customer_id'] = $customer_id;
            $order_ins['order_no'] = getOrderNo();

            $order_ins['amount'] = $total_order_value;
            $order_ins['tax_amount'] = $tax_total ?? 0;
            $order_ins['tax_percentage'] = $tax_percentage ?? 0;
            $order_ins['shipping_amount'] = $shippping_fee_amount;
            $order_ins['coupon_amount'] = $coupon_amount ?? 0;
            $order_ins['coupon_code'] = $coupon_code->coupon_code ?? '';
            $order_ins['coupon_percentage'] = isset($coupon_code->calculate_type) && $coupon_code->calculate_type == 'percentage' ? $coupon_code->calculate_value : '';
            $order_ins['sub_total'] = $product_tax_exclusive_total ?? 0;
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
            $order_ins['billing_country'] = $billing_address->country ?? null;
            $order_ins['billing_post_code'] = $billing_address->post_code ?? null;
            $order_ins['billing_state'] = $billing_address->state ?? null;
            $order_ins['billing_city'] = $billing_address->city ?? null;

            $order_ins['shipping_name'] = $pickup_store_address->title ?? $shipping_address->name ?? $billing_address->name;
            $order_ins['shipping_email'] = $pickup_store_address->email ??$shipping_address->email ?? $billing_address->email;
            $order_ins['shipping_mobile_no'] = $pickup_store_address->whatsapp_no ?? $shipping_address->mobile_no ?? $billing_address->mobile_no;
            $order_ins['shipping_address_line1'] = $pickup_store_address->address ?? $shipping_address->address_line1 ?? $billing_address->address_line1;
            $order_ins['shipping_address_line2'] = (isset($pickup_store_address) && !empty($pickup_store_address)) ? null : ($shipping_address->address_line2 ?? $billing_address->address_line2 ?? null);
            $order_ins['shipping_landmark'] = $shipping_address->landmark ?? $billing_address->landmark ?? null;
            $order_ins['shipping_country'] = $shipping_address->country ?? $billing_address->country ?? null;
            $order_ins['shipping_post_code'] = (isset($pickup_store_address) && !empty($pickup_store_address)) ? null : ($shipping_address->post_code ?? $billing_address->post_code);
            $order_ins['shipping_state'] = (isset($pickup_store_address) && !empty($pickup_store_address)) ? null : ($shipping_address->state ?? $billing_address->state ?? null);
            $order_ins['shipping_city'] = (isset($pickup_store_address) && !empty($pickup_store_address)) ? null : ($shipping_address->city ?? $billing_address->city ?? null);

            // dump($order_ins);

            $order_info = Order::create($order_ins);
            $order_id = $order_info->id;

            if (isset($cart_items) && !empty($cart_items)) {
                foreach ($cart_items as $item) {

                    $product_info = Product::find($item->product_id);
                    /**
                     * tax calculation
                     */
                    $category               = $product_info->productCategory;
                    $price_with_tax         = $product_info->mrp;
                    if (isset($category->parent->tax_id) && !empty($category->parent->tax_id)) {
                        $tax_info = Tax::find($category->parent->tax_id);
                    } else if (isset($category->tax_id) && !empty($category->tax_id)) {
                        $tax_info = Tax::find($category->tax_id);
                    }
                    // dump( $citems );
                    if (isset($tax_info) && !empty($tax_info)) {
                        $tax = getAmountExclusiveTax($price_with_tax, $tax_info->pecentage);
                        $tax_percentage         = $tax['tax_percentage'] ?? 0;
                    }
                    // dd( $tax );
                    $items_ins['order_id'] = $order_id;
                    $items_ins['product_id'] = $product_info->id;
                    $items_ins['product_name'] = $product_info->product_name;
                    $items_ins['image'] = $product_info->image;
                    $items_ins['hsn_code'] = $product_info->hsn_no;
                    $items_ins['sku'] = $product_info->sku;
                    $items_ins['quantity'] = $item->quantity;
                    $items_ins['price'] = $item->price;
                    $items_ins['strice_price'] = $product_info->strike_price;
                    $items_ins['save_price'] = $product_info->save_price;
                    $items_ins['base_price'] = $product_info->tax->basePrice ?? 0;
                    $items_ins['tax_amount'] = ($tax['gstAmount'] ?? 0) * $item->quantity;
                    $items_ins['tax_percentage'] = $tax['tax_percentage'] ?? $tax_percentage ?? 0;
                    $items_ins['sub_total'] = $item->sub_total;
                    

                    $order_product_info = OrderProduct::create($items_ins);
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
                            $add_ins['product_id'] = $product_info->id;
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
            $his['description'] = 'Order has been Initiated successfully & '.route('ccav.payment.start', ['customer_id' => base64_encode($customer_id), 'order_id' => base64_encode($order_info->id)]);
            OrderHistory::create($his);

            $error = 0;
            $response['error'] = $error;
            $response['message'] = 'Payment Initiated Successfully';
            $response['redirect_url'] = route('ccav.payment.start', ['customer_id' => base64_encode($customer_id), 'order_id' => base64_encode($order_info->id)]);
        } else {

            $error = 1;
            $response['error'] = $error;
            $response['message'] = errorArrays($validator->errors()->all());
            $response['redirect_url'] = '';
        }
        return $response;
    }

    public function startPayment(Request $request,)
    {

        if ($request->customer_id && $request->order_id) {

            $customer_id = base64_decode($request->customer_id);
            $order_id    = base64_decode($request->order_id);

            $order_info = Order::find($order_id);
            if ($order_info) {

                $parameters = [
                    'tid' => date('ymdhis'),
                    'order_id' => $order_info->order_no,
                    'amount' => $order_info->amount,
                    'billing_name' => $order_info->billing_name,
                    'billing_address' => $order_info->billing_address_line1,
                    'billing_city' => $order_info->billing_city,
                    'billing_state' => $order_info->billing_state,
                    'billing_zip' => $order_info->billing_post_code,
                    'billing_country' => $order_info->billing_country,
                    'billing_tel' => $order_info->billing_mobile_no,
                    'billing_email' => $order_info->billing_email,
                    'delivery_name' => $order_info->shipping_name,
                    'delivery_address' => $order_info->shipping_address_line1,
                    'delivery_city' => $order_info->shipping_city,
                    'delivery_state' => $order_info->shipping_state,
                    'delivery_zip' => $order_info->shipping_post_code,
                    'delivery_country' => $order_info->shipping_country,
                    'delivery_tel' => $order_info->shipping_mobile_no

                ];

                $order = Indipay::prepare($parameters);
                return Indipay::process($order);
            } else {
                $error = 1;
                $response['error'] = $error;
                $response['message'] = 'UnAuthorized Access';
            }
        } else {
            $error = 1;
            $response['error'] = $error;
            $response['message'] = 'Payment link expired';
        }

        return $response;
    }

    public function verifyCCavenueTransaction(Request $request)
    {

        $token = $request->token;
        $orders = [];
        if ($token) {
            $order_no = base64_decode($token);

            $order_info = Order::where('order_no', $order_no)->first();

            $pay_response = unserialize($order_info->payments->response);

            if ($order_info) {
                $orders = array(
                    'order_no' => $order_info->order_no,
                    'amount' => $order_info->amount
                );
                /** 
                 *  check status api
                 */
                $merchant_json_data = array(
                    'order_no' => $order_info->order_no,
                    'reference_no' => $pay_response->tracking_id ?? ''
                );
                $access_code = 'AVRD71KE07CJ75DRJC';
                $working_key = 'B00B81683DCD0816F8F32551E2C2910B';
                $merchant_data = json_encode($merchant_json_data);
                $encrypted_data = $this->statusEncrypt($merchant_data, $working_key);
                $payment_info = Payment::where('order_id', $order_info->id)->orderBy('id', 'desc')->first();
                $payment_info->enc_request = $encrypted_data;
                $payment_info->save();
                
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.ccavenue.com/apis/servlet/DoWebTrans?enc_request=' . $encrypted_data . '&access_code=AVRD71KE07CJ75DRJC&request_type=JSON&response_type=JSON&command=orderStatusTracker&version=1.2',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                ));

                $response = curl_exec($curl);
                curl_close($curl);

                $status_response = '';
                $payment_status = '';
                if ($response) {

                    $payment_info = Payment::where('order_id', $order_info->id)->orderBy('id', 'desc')->first();
                    $payment_info->enc_response = $response;
                    // $payment_info->status = 'paid';
                    $payment_status = 'paid';
                    $orders['payment_no'] = $payment_info->payment_no;
                    /** 
                     * insert in payment enc_response
                     */
                    $information = explode('&', $response);

                    $dataSize = sizeof($information);
                    for ($i = 0; $i < $dataSize; $i++) {
                        $info_value = explode('=', $information[$i]);
                        if ($info_value[0] == 'enc_response') {
                            $status_response = $this->statusDecrypt(trim($info_value[1]), $working_key);
                            break;
                        }
                    }

                    $payment_info->enc_response_decrypted = $status_response;
                    $obj = json_decode($status_response);
                    $payment_info->save();

                }

                // 'enc_request=' . $encrypted_data . '&access_code=' . $access_code . '&command=orderStatusTracker&request_type=JSON&response_type=JSON';

                if (strtolower($payment_status) == 'paid' && $order_info->amount == $order_info->response_amount) {
                    $error = 0;
                    $response_api['error'] = $error;
                    $response_api['message'] = 'PAYMENT_SUCCESS';
                    $orders['status'] = 'paid';

                    $payment_info = Payment::where('order_id', $order_info->id)->orderBy('id', 'desc')->first();
                    // $payment_info->enc_request = $encrypted_data;
                    // $payment_info->enc_response = $response;
                    $payment_info->status = 'paid';
                    $payment_info->save();
                } else {
                    $error = 0;
                    $orders['status'] = 'failed';

                    $response_api['error'] = $error;
                    $response_api['message'] = 'PAYMENT_FAILD';
                }
            } else {
                $error = 1;
                $response_api['error'] = $error;
                $response_api['message'] = 'PAYMENT_TOKEN_INVALID';
            }
        } else {
            $error = 1;
            $response_api['error'] = $error;
            $response_api['message'] = 'PAYMENT_TOKEN_INVALID';
        }
        $response_api['order'] = $orders;

        return $response_api;
    }

    public function statusTracker($final_data)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://apitest.ccavenue.com/apis/servlet/DoWebTrans");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $final_data);
        // Get server response ...
        $result = curl_exec($ch);
        curl_close($ch);
        // dd($result);
    }


    function statusEncrypt($plainText, $key)
    {
        $key = $this->statusHextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        $encryptedText = bin2hex($openMode);
        return $encryptedText;
    }

    function statusDecrypt($encryptedText, $key)
    {
        $key = $this->statusHextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = $this->statusHextobin($encryptedText);
        $decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        return $decryptedText;
    }
    //*********** Padding Function *********************

    function status_pkcs5_pad($plainText, $blockSize)
    {
        $pad = $blockSize - (strlen($plainText) % $blockSize);
        return $plainText . str_repeat(chr($pad), $pad);
    }

    //********** Hexadecimal to Binary function for php 4.0 version ********

    function statusHextobin($hexString)
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

    function getCartListAll($customer_id = null, $guest_token = null,  $shipping_info = null, $shipping_type = null, $selected_shipping = null, $coupon_data = null)
    {
        // dd( $coupon_data );
        $checkCart          = Cart::with(['products', 'products.productCategory'])->when($customer_id != '', function ($q) use ($customer_id) {
            $q->where('customer_id', $customer_id);
        })->get();

        $tmp                = [];
        $grand_total        = 0;
        $tax_total          = 0;
        $product_tax_exclusive_total = 0;
        $tax_percentage = 0;
        $cartTemp = [];
        $used_addons = [];
        $total_addon_amount = 0;
        $has_pickup_store = true;
        $brand_array = [];
        if (isset($checkCart) && !empty($checkCart)) {
            foreach ($checkCart as $citems) {

                $items = $citems->products;
                $tax = [];
                $tax_percentage = 0;

                $category               = $items->productCategory;
                $price_with_tax         = $items->mrp;
                if (isset($category->parent->tax_id) && !empty($category->parent->tax_id)) {
                    $tax_info = Tax::find($category->parent->tax_id);
                } else if (isset($category->tax_id) && !empty($category->tax_id)) {
                    $tax_info = Tax::find($category->tax_id);
                }
                // dump( $citems );
                if (isset($tax_info) && !empty($tax_info)) {
                    $tax = getAmountExclusiveTax($price_with_tax, $tax_info->pecentage);
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
                $pro['strike_price']    = $items->strike_price;
                $pro['save_price']      = $items->strike_price - $items->mrp;
                $pro['discount_percentage'] = abs($items->discount_percentage);
                $pro['image']           = $items->base_image;
                $pro['max_quantity']    = $items->quantity;
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
                $grand_total            += $citems->sub_total;
                $grand_total            += $addon_total;
                $cartTemp[] = $pro;
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
            if (isset($coupon_data) && !empty($coupon_data)) {
                $grand_total = $grand_total - $coupon_data['discount_amount'] ?? 0;
            }

            if (count(array_unique($brand_array)) > 1) {
                $has_pickup_store = false;
            }

            $amount         = filter_var($grand_total, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $charges        = ShippingCharge::select('id', 'shipping_title', 'minimum_order_amount', 'charges', 'is_free')->where('status', 'published')->where('minimum_order_amount', '<', $amount)->get();

            $tmp['shipping_charges']    = $charges;
            $tmp['cart_total']          = array(
                'total' => number_format(round($grand_total), 2),
                'product_tax_exclusive_total' => number_format(round($product_tax_exclusive_total), 2),
                'product_tax_exclusive_total_without_format' => round($product_tax_exclusive_total),
                'tax_total' => number_format(round($tax_total), 2),
                'tax_percentage' => number_format(round($tax_percentage), 2),
                'shipping_charge' => $shipping_info->charges ?? 0,
                'addon_amount' => $total_addon_amount,
                'has_pickup_store' => $has_pickup_store
            );
        }

        return $tmp;
    }
}
