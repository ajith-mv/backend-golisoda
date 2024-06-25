<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\DynamicMail;
use App\Models\GlobalSettings;
use App\Models\Master\EmailTemplate;
use App\Models\Master\OrderStatus;
use App\Models\Order;
use App\Models\OrderHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mail;

class OrderController extends Controller
{
    public function getOrders(Request $request)
    {
        $customer_id   = $request->customer_id ?? 1;
        $ifOrderStatus = !empty($request->status) && isset($request->status);
        $orderAll      = Order::where('customer_id', $customer_id)
                                ->when($ifOrderStatus, function ($q) use ($request) {
                                    $q->where("status", $request->status);
                                })
                                ->orderBy('id', 'desc')
                                ->get();
        $orders = [];
        if (isset($orderAll) && !empty($orderAll)) {
            foreach ($orderAll as $item) {

                $tmp = [];
                $tmp['id'] = $item->id;
                $tmp['order_no'] = $item->order_no;
                $tmp['shipping_type'] = $item->shipping_type;
                $tmp['amount'] = $item->amount;
                $tmp['tax_percentage'] = $item->tax_percentage;
                $tmp['tax_amount'] = $item->tax_amount;
                $tmp['shipping_amount'] = $item->shipping_amount;
                $tmp['discount_amount'] = $item->discount_amount;
                $tmp['coupon_amount'] = $item->coupon_amount;
                $tmp['coupon_code'] = $item->coupon_code;
                $tmp['sub_total'] = $item->sub_total;
                $tmp['billing_name'] = $item->billing_name;
                $tmp['billing_email'] = $item->billing_email;
                $tmp['billing_mobile_no'] = $item->billing_mobile_no;
                $tmp['billing_address_line1'] = $item->billing_address_line1;
                $tmp['billing_address_line2'] = $item->billing_address_line2;
                $tmp['billing_landmark'] = $item->billing_landmark;
                $tmp['billing_country'] = $item->billing_country;
                $tmp['billing_post_code'] = $item->billing_post_code;
                $tmp['billing_state'] = $item->billing_state;
                $tmp['billing_city'] = $item->billing_city;
                $tmp['status'] = $item->status;
                $tmp['invoice_file'] = asset('storage/invoice_order/' . $item->order_no . '.pdf');

                $delivery_document              = $item->delivery_document;

                if (!Storage::exists($delivery_document)) {
                    $tmp['delivery_document'] = '';
                } else {
                    $url                = Storage::url($delivery_document);
                    $tmp['delivery_document'] = asset($url);
                }

                $tmp['order_date'] = date('d M Y H:i A', strtotime($item->created_at));
                $itemArray = [];
                if (isset($item->orderItems) && !empty($item->orderItems)) {
                    foreach ($item->orderItems as $pro) {

                        $tmp1 = [];
                        $tmp1['product_name'] = $pro->product_name;
                        $tmp1['hsn_code'] = $pro->hsn_code;
                        $tmp1['sku'] = $pro->sku;
                        $tmp1['quantity'] = $pro->quantity;
                        $tmp1['price'] = $pro->price;
                        $tmp1['strice_price'] = $pro->strice_price;
                        $tmp1['save_price'] = $pro->save_price;
                        $tmp1['base_price'] = $pro->base_price;
                        $tmp1['tax_amount'] = $pro->tax_amount;
                        $tmp1['tax_percentage'] = $pro->tax_percentage;
                        $tmp1['quantity'] = $pro->quantity;
                        $tmp1['sub_total'] = $pro->sub_total;

                        $imagePath              = $pro->products->base_image;

                        if (!Storage::exists($imagePath)) {
                            $path               = asset('assets/logo/no_Image.jpg');
                        } else {
                            $url                = Storage::url($imagePath);
                            $path               = asset($url);
                        }

                        $tmp1['image']                   = $path;

                        $itemArray[] = $tmp1;
                    }
                }
                $tmp['items'] = $itemArray;
                #customers
                $tmp['customer'] = $item->customer;
                $tmp['tracking'] = $item->tracking;

                $orders[] = $tmp;
            }
        }

        return $orders;
    }

    public function getOrderStatus($customer_id)
    {
        return response()->json([
            "status" => true,
            "data"   => Order::where('customer_id', $customer_id)->groupBy("status")->pluck("status")
        ]);
    }

    public function getOrderByOrderNo(Request $request)
    {
        $customer_id        = $request->customer_id;
        $order_no           = $request->order_no;

        $info               = Order::where('order_no', $order_no)->where('customer_id', $customer_id)->first();

        $orders = [];
        if (isset($info) && !empty($info)) {

            $tmp['id'] = $info->id;
            $tmp['order_no'] = $info->order_no;
            $tmp['shipping_type'] = $info->shipping_type;
            $tmp['amount'] = $info->amount;
            $tmp['tax_percentage'] = $info->tax_percentage;
            $tmp['tax_amount'] = $info->tax_amount;
            $tmp['shipping_amount'] = $info->shipping_amount;
            $tmp['discount_amount'] = $info->discount_amount;
            $tmp['coupon_amount'] = $info->coupon_amount;
            $tmp['coupon_code'] = $info->coupon_code;
            $tmp['sub_total'] = $info->sub_total;

            $bill_tmp['name'] = $info->billing_name;
            $bill_tmp['email'] = $info->billing_email;
            $bill_tmp['mobile_no'] = $info->billing_mobile_no;
            $bill_tmp['address'] = $info->billing_address_line1 . ' ' . $info->billing_address_line2 . ' ' . $info->billing_landmark . ' ' . $info->billing_city . ' ' . $info->billing_state . ' ' . $info->billing_country . ' ' . $info->billing_post_code;
            $tmp['billing'] = $bill_tmp;

            $ship_tmp['name'] = $info->shipping_name;
            $ship_tmp['email'] = $info->shipping_email;
            $ship_tmp['mobile_no'] = $info->shipping_mobile_no;
            $ship_tmp['address'] = $info->shipping_address_line1 . ' ' . $info->shipping_address_line2 . ' ' . $info->shipping_landmark . ' ' . $info->shipping_city . ' ' . $info->shipping_state . ' ' . $info->shipping_country . ' ' . $info->shipping_post_code;
            $tmp['shipping'] = $ship_tmp;

            $tmp['status'] = $info->status;
            $tmp['invoice_file'] = asset('storage/invoice_order/' . $info->order_no . '.pdf');
            $tmp['order_date'] = date('d M Y H:i A', strtotime($info->created_at));
            $delivery_document = $info->delivery_document;

            if (!Storage::exists($delivery_document)) {
                $tmp['delivery_document'] = '';
            } else {
                $url                = Storage::url($delivery_document);
                $tmp['delivery_document'] = asset($url);
            }

            $itemArray = [];
            if (isset($info->orderItems) && !empty($info->orderItems)) {
                foreach ($info->orderItems as $pro) {

                    $tmp1 = [];
                    $tmp1['product_name'] = $pro->product_name;
                    $tmp1['hsn_code'] = $pro->hsn_code;
                    $tmp1['sku'] = $pro->sku;
                    $tmp1['quantity'] = $pro->quantity;
                    $tmp1['price'] = $pro->price;
                    $tmp1['strice_price'] = $pro->strice_price;
                    $tmp1['save_price'] = $pro->save_price;
                    $tmp1['base_price'] = $pro->base_price;
                    $tmp1['tax_amount'] = $pro->tax_amount;
                    $tmp1['tax_percentage'] = $pro->tax_percentage;
                    $tmp1['quantity'] = $pro->quantity;
                    $tmp1['sub_total'] = $pro->sub_total;
                    $tmp1['tracking_id'] = $pro->vendorTracking->tracking_id;
                    $tmp1['estimated_arrival_date'] = $pro->vendorTracking->estimated_arrival_date;

                    $imagePath              = $pro->products->base_image;

                    if (!Storage::exists($imagePath)) {
                        $path               = asset('assets/logo/no_Image.jpg');
                    } else {
                        $url                = Storage::url($imagePath);
                        $path               = asset($url);
                    }

                    $tmp1['image']                   = $path;

                    $itemArray[] = $tmp1;
                }
            }
            $tmp['items'] = $itemArray;
            #customers
            $tmp['customer'] = $info->customer;
            $tracking_start_data = [];
            $tracking_info = [];
            if (isset($info->tracking) && !empty($info->tracking)) {
                foreach ($info->tracking as $track) {
                    $tracking = [];
                    $track_data = [];
                    $tracking['id'] = $track->id;
                    $tracking['status_name'] = $track->action;

                    $track_data['id'] = $track->id;
                    $track_data['action'] = $track->action;
                    $track_data['description'] = $track->description;
                    $track_data['order_id'] = $track->order_id;
                    $track_data['created_at'] = date('H:i A - d M Y', strtotime($track->created_at));

                    $tracking['tracking_info'] = $track_data;
                    $tracking['has_tracking'] = true;

                    $tracking_start_data[] = $tracking;
                }
            }
            $tmp['tracking'] = $tracking_start_data;

            $orderTracking  = OrderStatus::select('id', 'status_name')
                ->where('order', '!=', 6)
                ->where('order', '!=', 3)
                ->get();
            $tracking_info = [];
            if ($info->status == 'cancel_requested') {
                if (isset($info->tracking) && !empty($info->tracking)) {
                    foreach ($info->tracking as $track) {
                        $tra = [];
                        $track_data = [];
                        $tra['id'] = $track->id;
                        $tra['status_name'] = $track->action;

                        $track_data['id'] = $track->id;
                        $track_data['action'] = $track->action;
                        $track_data['description'] = $track->description;
                        $track_data['order_id'] = $track->order_id;
                        $track_data['created_at'] = date('H:i A - d M Y', strtotime($track->created_at));

                        $tra['tracking_info'] = $track_data;
                        $tra['has_tracking'] = true;

                        $tracking_info[] = $tra;
                    }
                }
            } else {

                if (isset($orderTracking) && !empty($orderTracking)) {
                    foreach ($orderTracking as $oritem) {

                        $tmp_order = [];
                        $tmp_order['id'] = $oritem->id;
                        $tmp_order['status_name'] = $oritem->status_name;

                        $has_key =  array_search($oritem->status_name, array_column($tracking_start_data, 'status_name'));
                        if (is_int($has_key)) {
                            $tmp_order['tracking_info'] = $tracking_start_data[$has_key];
                        }
                        $tmp_order['has_tracking'] = isset($tmp_order['tracking_info']) ? true : false;
                        $tracking_info[] = $tmp_order;
                    }
                }
            }

            $tmp['orderTracking'] = $tracking_info;
            $orders = $tmp;
        }

        return $orders;
    }

    public function requestCancelOrder(Request $request)
    {
        $customer_id        = $request->customer_id;
        $order_id           = $request->order_id;
        $cancel_reason      = $request->cancelReason;

        $orderInfo = Order::find($order_id);

        if (isset($orderInfo) && !empty($orderInfo)) {
            if ($orderInfo->status == 'cancel_requested') {
                $error = 1;
                $message = 'Cancel Request has been sent already, You will receive mail about your cancel orders';
            } else {
                $error = 0;
                $message = 'Cancel Request has been sent successfully, You will receive mail about your cancel orders';

                $order_status    = OrderStatus::where('status', 'published')->where('order', 6)->first();

                $orderInfo->status = 'cancel_requested';
                $orderInfo->description = $cancel_reason;
                $orderInfo->order_status_id = $order_status->id;
                $orderInfo->save();

                /**** order history */
                $his['order_id'] = $orderInfo->id;
                $his['action'] = 'Order Cancel Requested';
                $his['description'] = $cancel_reason;
                OrderHistory::create($his);

                /****
                 * 1.send email for order placed
                 */
                #generate invoice
                $globalInfo = GlobalSettings::first();

                #send mail
                $emailTemplate = EmailTemplate::select('email_templates.*')
                    ->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id')
                    ->where('sub_categories.slug', 'order-cancel-requested')->first();

                $globalInfo = GlobalSettings::first();

                $dynamic_content = 'Order no : ' . $orderInfo->order_no . ', Order Date:' . date('d M Y H:i A', strtotime($orderInfo->created_at));

                $extract = array(
                    'name' => '',
                    'dynamic_content' => $dynamic_content,
                    'regards' => $globalInfo->site_name,
                    'company_website' => '',
                    'company_mobile_no' => $globalInfo->site_mobile_no,
                    'company_address' => $globalInfo->address,
                    'customer_login_url' => env('WEBSITE_LOGIN_URL'),
                    'cancel_reason' => $cancel_reason
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

                $send_mail = new DynamicMail($templateMessage, $title);
                // return $send_mail->render();
                Mail::to($orderInfo->billing_email)->send($send_mail);
            }
        } else {
            $error = 1;
            $message = 'Order not found, Please contact admin';
        }
        return array('error' => $error, 'message' => $message);
    }
}
