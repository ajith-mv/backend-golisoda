<?php

namespace App\Http\Controllers;

use App\Exports\OrderExport;
use App\Mail\DynamicMail;
use App\Mail\OrderMail;
use App\Models\BrandOrder;
use App\Models\GlobalSettings;
use App\Models\Master\Brands;
use App\Models\Master\BrandVendorLocation;
use App\Models\Master\EmailTemplate;
use App\Models\Master\OrderStatus;
use App\Models\Order;
use App\Models\OrderHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Exception;
use App\Models\Master\Variation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Image;
use PDF;
use App\Models\Master\Pincode;
use App\Services\WatiService;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $watiService;

    public function __construct(WatiService $watiService)
    {
        $this->watiService = $watiService;
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $order_by = $request->order[0]['column'] ?? 'orders.id';
            $order_type = $request->order[0]['dir'] ?? 'desc';
            if (isset($request->order[0]['column'])) {
                if ($request->order[0]['column'] == 0) {
                    $order_by = 'orders.created_at';
                } else if ($request->order[0]['column'] == 1) {
                    $order_by = 'orders.order_no';
                } else if ($request->order[0]['column'] == 2) {
                    $order_by = 'orders.billing_address_line1';
                } else if ($request->order[0]['column'] == 3) {
                    $order_by = 'orders.amount';
                } else if ($request->order[0]['column'] == 4) {
                    $order_by = 'order_quantity';
                } else if ($request->order[0]['column'] == 5) {
                    $order_by = 'payment_type';
                } else if ($request->order[0]['column'] == 6) {
                    $order_by = 'payment_status';
                } else {
                    $order_by = 'orders.id';
                }
            }
            $data = Order::selectRaw('gbs_payments.order_id,gbs_payments.payment_no,gbs_payments.status as payment_status,gbs_payments.payment_type,gbs_orders.status as order_status,gbs_orders.billing_address_line1 as billing_info,gbs_orders.*,sum(gbs_order_products.quantity) as order_quantity')
                ->join('order_products', 'order_products.order_id', '=', 'orders.id')
                ->leftJoin('payments', 'payments.order_id', '=', 'orders.id')
                ->groupBy('orders.id')->orderBy($order_by, $order_type);
            $filter_subCategory   = '';
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  DataTables::of($data)
                ->filter(function ($query) use ($keywords, $status, $filter_subCategory) {
                    if ($status) {
                        return $query->where('orders.status', 'like', $status);
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('orders.billing_name', 'like', "%{$keywords}%")
                            ->orWhere('orders.billing_email', 'like', "%{$keywords}%")
                            ->orWhere('orders.billing_mobile_no', 'like', "%{$keywords}%")
                            ->orWhere('orders.billing_address_line1', 'like', "%{$keywords}%")
                            ->orWhere('orders.billing_state', 'like', "%{$keywords}%")
                            ->orWhere('orders.status', 'like', "%{$keywords}%")
                            ->orWhere('orders.order_no', 'like', "%{$keywords}%")
                            ->orWhereDate("orders.created_at", $date);
                    }
                })
                ->addIndexColumn()
                ->editColumn('billing_info', function ($row) {
                    $pincode = Pincode::find($row['billing_post_code']);
                    $billing_info = '';
                    $billing_info .= '<div class="font-weight-bold">' . $row['billing_name'] . '</div>';
                    $billing_info .= '<div class="">' . $row['billing_email'] . ',' . $row['billing_mobile_no'] . '</div>';
                    $billing_info .= '<div class="">' . $row['billing_address_line1'] . '</div>';
                    $billing_info .= '<div class="">' . $row['billing_city'] . ','  . $row['billing_state'] . ','  . $row['billing_country'] . '-'  . $pincode ? $pincode->pincode ?? '' : '' .  '</div>';

                    return $billing_info;
                })

                ->editColumn('payment_status', function ($row) {
                    return ucwords($row->payment_status);
                })->editColumn('payment_type', function ($row) {
                    return strtoupper($row->payment_type ?? '');
                })
                ->editColumn('order_status', function ($row) {
                    return ucwords(str_replace('_', ' ', $row->status));
                })
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $view_btn = '<a href="javascript:void(0)" onclick="return viewOrder(' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-eye"></i>
                </a>';

                    $view_btn .= '<a href="javascript:void(0)" onclick="return openOrderStatusModal(' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                                <i class="fa fa-edit"></i>
                            </a>';

                    $view_btn .= '<a target="_blank" href="' . asset('storage/invoice_order/' . $row->order_no . '.pdf') . '" tooltip="Download Invoice"  class="btn btn-icon btn-active-success btn-light-success mx-1 w-30px h-30px" > 
                                    <i class="fa fa-download"></i>
                                </a>';

                    return $view_btn;
                })
                ->rawColumns(['action', 'status', 'billing_info', 'payment_status', 'order_status', 'created_at']);
            return $datatables->make(true);
        }
        $breadCrum = array('Order');
        $title      = 'Order';
        return view('platform.order.index', compact('title', 'breadCrum'));
    }

    public function orderView(Request $request)
    {
        $order_id = $request->id;
        $order_info = Order::find($order_id);
        $pickup_details = [];
        $modal_title        = 'View Order';
        $globalInfo = GlobalSettings::first();
        $view_order = view('platform.invoice.view_invoice', compact('order_info', 'globalInfo', 'pickup_details'));
        return view('platform.order.view_modal', compact('view_order', 'modal_title'));
    }

    public function openOrderStatusModal(Request $request)
    {

        $order_id = $request->id;
        $order_status_id = $request->order_status_id;
        $modal_title        = 'Update Order Status';

        $info = Order::find($order_id);

        $brandOrders = BrandOrder::select('brand_id', 'tracking_id', 'estimated_arrival_date')
            ->where('order_id', $order_id)
            ->groupBy('brand_id') // Ensure unique brand_ids
            ->get();

        // Get brand names for each brand_id
        foreach ($brandOrders as $brandOrder) {
            $brand = Brands::find($brandOrder->brand_id);
            if (isset($brand))
                $brandOrder->brand_name = $brand->brand_name;
        }

        $order_status_info = OrderStatus::where('status', 'published')->get();

        return view('platform.order.order_status_modal', compact('info', 'order_status_info', 'brandOrders'));
    }

    public function changeOrderStatus(Request $request)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
            'order_status_id' => 'required|string',
            'description' => 'required|string',
        ]);
        if ($validator->passes()) {


            $trackingIds = $request->input('tracking_id');
            $arrivalDates = $request->input('estimated_arrival_date');
            if (isset($trackingIds) || isset($arrivalDates)) {

                foreach ($trackingIds as $brandId => $trackingId) {
                    $brandOrders = BrandOrder::where([['order_id', $id], ['brand_id', $brandId]])->get();
                    if (isset($brandOrders) && !empty($brandOrders)) {
                        foreach ($brandOrders as $brandOrder) {
                            $brandOrder->tracking_id = $trackingId;
                            $brandOrder->estimated_arrival_date = $arrivalDates[$brandId];
                            $brandOrder->save();
                        }
                    }
                }
            }


            if ($request->order_status_id == 6) {
                return response()->json(['error' => 1, 'message' => 'Admin cannot raise a cancel request as this will be raised by the website customers.']);
            }
            $info = Order::find($id);
            $info->notification_status = 'yes';
            $info->order_status_id = $request->order_status_id;
            $info->description = $request->description;

            switch ($request->order_status_id) {
                case '1':
                    $action = 'Order Initiated';
                    $info->status = 'pending';
                    break;

                case '2':
                    $action = 'Order Placed';
                    $info->status = 'placed';
                    break;

                case '3':
                    $action = 'Order Cancelled';
                    $info->status = 'cancelled';
                    break;

                case '4':
                    $action = 'Order Shipped';
                    $otp = generateOtp();

                    /****
                     * 1.send email for order placed
                     * 2.send sms for notification
                     */
                    #generate invoice
                    $globalInfo = GlobalSettings::first();

                    #send mail
                    $emailTemplate = EmailTemplate::select('email_templates.*')
                        ->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id')
                        ->where('sub_categories.slug', 'order-shipped')->first();

                    $globalInfo = GlobalSettings::first();

                    $extract = array(
                        'name' => $info->billing_name,
                        'regards' => $globalInfo->site_name,
                        'company_website' => '',
                        'company_mobile_no' => $globalInfo->site_mobile_no,
                        'company_address' => $globalInfo->address,
                        'customer_login_url' => env('WEBSITE_LOGIN_URL'),
                        'order_no' => $info->order_no,
                        'description' => $request->description ?? ''
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
                    $bccEmails = explode(',', env('BCC_EMAILS'));
                    $bccRecipients = array_merge($bccEmails, [$info->billing_email]);
                    Mail::to($info->billing_email)->bcc($bccRecipients)->send($send_mail);

                    #send sms for notification
                    $sms_params = array(
                        'name' => $info->billing_name,
                        'order_no' => $info->order_no,
                        'otp' => $otp,
                        'mobile_no' => [$info->billing_mobile_no]
                    );

                    sendGBSSms('order_shipping', $sms_params);

                    $info->status = 'shipped';
                    $info->delivery_otp = $otp;
                    log::info(config('wati.order_placed'));
                    if (!empty(config('wati.order_placed'))) {
                        $whatsapp_params = [
                            ['name' => 'name', 'value' => $info->billing_name],
                            ['name' => 'order_number', 'value' => $info->order_no],
                        ];
                        $mobile_number = formatPhoneNumber($info->billing_mobile_no);
                        $this->watiService->sendMessage($mobile_number, 'order_placed_message', 'order_placed_message',  $whatsapp_params);
                    }
                    break;

                case '5':
                    $otp = $request->otp;
                    if ($otp) {
                        if ($info->delivery_otp != $otp) {

                            $message = ['OTP is not matched'];
                            return response()->json(['error' => '1', 'message' => $message]);
                        }
                    }
                    $action = 'Order Delivered';
                    $info->status = 'delivered';
                    /**
                     * upload image
                     */
                    if ($request->hasFile('delivery_document')) {

                        $imagName               = time() . '_' . $request->delivery_document->getClientOriginalName();
                        $directory              = 'orderDocument/' . $info->order_no . '/document';
                        Storage::deleteDirectory('public/' . $directory);

                        if (!is_dir(storage_path("app/public/orderDocument/" . $info->order_no . "/document"))) {
                            mkdir(storage_path("app/public/orderDocument/" . $info->order_no . "/document"), 0775, true);
                        }

                        $thumbnailPath          = 'public/orderDocument/' . $info->order_no . '/document/' . $imagName;

                        $path = Storage::put($thumbnailPath, file_get_contents($request->delivery_document));

                        $info->delivery_document = $thumbnailPath;
                    }
                    $info->otp_verified_by = auth()->user()->id;
                    $info->otp_verified_at = date('Y-m-d H:i:s');

                    #send mail
                    $emailTemplate = EmailTemplate::select('email_templates.*')
                        ->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id')
                        ->where('sub_categories.slug', 'order-delivered')->first();

                    $globalInfo = GlobalSettings::first();

                    $extract = array(
                        'name' => $info->billing_name,
                        'regards' => $globalInfo->site_name,
                        'company_website' => '',
                        'company_mobile_no' => $globalInfo->site_mobile_no,
                        'company_address' => $globalInfo->address,
                        'dynamic_content' => '',
                        'customer_login_url' => env('WEBSITE_LOGIN_URL'),
                        'order_id' => $info->order_no,
                        'description' => $request->description ?? ''
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

                    // $filePath = 'storage/orderDocument/' . $info->order_no . '/document/' . $imagName;

                    // $filePath = "";
                    // $send_mail = new OrderMail($templateMessage, $title, $filePath);
                    //  $send_mail = new OrderMail($templateMessage, $title);
                    $filePath = 'storage/invoice_order/' . $info->order_no . '.pdf';
                    if (!(file_exists(public_path($filePath)))) {
                        $this->generateInvoice($order_id);
                    }
                    $send_mail = new OrderMail($templateMessage, $title, $filePath);

                    // return $send_mail->render();
                    $bccEmails = explode(',', env('BCC_EMAILS'));
                    $bccRecipients = array_merge($bccEmails, [$info->billing_email]);
                    Mail::to($info->billing_email)->bcc($bccRecipients)->send($send_mail);

                    #send sms for notification
                    $sms_params = array(
                        'name' => $info->billing_name,
                        'order_no' => $info->order_no,
                        'tracking_url' => env('WEBSITE_LOGIN_URL'),
                        'mobile_no' => [$info->billing_mobile_no]
                    );
                    // sendGBSSms('delivery_sms', $sms_params);
                    if (!empty(config('wati.order_placed'))) {
                        $brandOrders = BrandOrder::select('brand_id', 'tracking_id', 'estimated_arrival_date')
                            ->where('order_id', $id)
                            ->groupBy('brand_id') // Ensure unique brand_ids
                            ->get();
                        if (isset($brandOrders) && !empty($brandOrders)) {
                            foreach ($brandOrders as $brandOrder) {
                                $whatsapp_params = [
                                    ['name' => 'name', 'value' => $info->billing_name],
                                    ['name' => 'order_number', 'value' => $info->order_no],
                                    ['name' => 'tracking_url', 'value' => $brandOrder->tracking_id],
                                ];
                                $mobile_number = formatPhoneNumber($info->billing_mobile_no);
                                $this->watiService->sendMessage($mobile_number, config('wati.order_placed'), config('wati.order_placed'),  $whatsapp_params);
                            }
                        }
                    }



                    break;

                default:
                    # code...
                    $orderStatus = OrderStatus::find($request->order_status_id);
                    if (isset($orderStatus)) {
                        $action = $orderStatus->status_name;
                        $info->status = strtolower(str_replace(' ', '_', $action));
                    } else {
                        $action = '';
                    }
                    break;
            }


            $info->update();

            $ins['order_id']     = $request->id;
            $ins['action']       = $action;
            $ins['description']  = $request->description;

            OrderHistory::create($ins);
            $message    = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
            $error = 0;
        } else {
            $error      = 1;
            $message    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function export()
    {
        return Excel::download(new OrderExport, 'orders.xlsx');
    }
    public function orderCountGolbal()
    {
        $data = Order::selectRaw('gbs_payments.order_id,gbs_payments.payment_no,gbs_payments.status as payment_status,gbs_orders.*,sum(gbs_order_products.quantity) as order_quantity')
            ->join('order_products', 'order_products.order_id', '=', 'orders.id')
            ->join('payments', 'payments.order_id', '=', 'orders.id')->where('orders.notification_status', 'no')
            ->groupBy('orders.id')->get();
        $order_count = count($data);
        return $order_count;
    }

    public function generateInvoice($order_id)
    {
        $order_info = Order::find($order_id);
        if ($order_info) {
            $globalInfo = GlobalSettings::first();

            $pdf = PDF::loadView('platform.invoice.index', compact('order_info', 'globalInfo'));
            Storage::put('public/invoice_order/' . $order_info->order_no . '.pdf', $pdf->output());
            return true;
        }
        return false;
    }

    public function downloadVendorInvoice(Request $request)
    {
        $order_id = $request->order_id;
        $singleBrandId = $request->brand_id;
        $order_no = $request->order_no;
        $globalInfo = GlobalSettings::first();

        $order_info = Order::find($order_id);
        $variations = $this->getVariations($order_info);
        $brand_address = BrandVendorLocation::where([['brand_id', $singleBrandId], ['is_default', 1]])
            ->join('brands', 'brand_vendor_locations.brand_id', '=', 'brands.id')
            ->select('brand_vendor_locations.*', 'brands.brand_name')
            ->first();
        if (isset($brand_address) && (!empty($brand_address))) {
            $pdf = PDF::loadView('platform.vendor_invoice.index', compact('brand_address', 'order_info', 'globalInfo', 'variations', 'singleBrandId'));
            Storage::put('public/invoice_order/' . $order_id . '/' . $singleBrandId . '/' . $order_no . '.pdf', $pdf->output());
            return $pdf->download($order_no . '.pdf');
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
}
