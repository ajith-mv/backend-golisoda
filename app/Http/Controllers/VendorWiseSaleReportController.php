<?php

namespace App\Http\Controllers;

use App\Mail\OrderMail;
use App\Models\GlobalSettings;
use App\Models\Master\Brands;
use App\Models\Master\BrandVendorLocation;
use App\Models\Master\EmailTemplate;
use Illuminate\Http\Request;
use DataTables;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PDF;

class VendorWiseSaleReportController extends Controller
{
    public function index(Request $request)
    {
        $title = "Vendor Wise Sale Report";
        $breadCrum = array('Reports', 'Vendor Wise Sale Report');

        if ($request->ajax()) {

            $date_range = $request->get('date_range');
            $start_date = $end_date = '';
            if (isset($date_range) && !empty($date_range)) {
                $dates = explode('-', $date_range);
                $start_date = date('Y-m-d', strtotime(trim(str_replace('/', '-', $dates[0]))));
                $end_date = date('Y-m-d', strtotime(trim(str_replace('/', '-', $dates[1]))));
            }

            $where = "WHERE gbs_orders.status != 'pending'";
            if (!empty($start_date) && !empty($end_date)) {
                $where = "WHERE DATE(gbs_brand_orders.created_at) >= '$start_date' AND DATE(gbs_brand_orders.created_at) <= '$end_date' AND gbs_orders.status != 'pending'";
            }

            $data = DB::table(DB::raw("(SELECT gbs_brands.id as id, gbs_brands.brand_name as brand_name, gbs_brand_orders.commission_value as com_percentage, gbs_brands.commission_type as commission_type,
            sum(qty*price) as sale_amount,
            CASE
        WHEN gbs_brand_orders.commission_type = 'percentage' THEN ROUND(SUM(qty * price * gbs_brand_orders.commission_value / 100), 2)
        WHEN gbs_brand_orders.commission_type = 'fixed' THEN ROUND(SUM(qty * gbs_brand_orders.commission_value), 2)
        ELSE 0
    END AS com_amount
    
            FROM gbs_brand_orders
            JOIN gbs_orders ON gbs_orders.id = gbs_brand_orders.order_id 
            JOIN gbs_brands ON gbs_brands.id = gbs_brand_orders.brand_id $where
            GROUP BY gbs_brand_orders.brand_id, gbs_brand_orders.commission_type) as a"))
                ->select(
                    'id',
                    'brand_name',
                    'commission_type',
                    DB::raw('SUM(sale_amount) as sale_amount'),
                    DB::raw('SUM(com_percentage) as com_percentage'),
                    DB::raw('com_amount as com_amount')
                )
                ->groupBy('id');


            $keywords = $request->get('search')['value'];
            $filter_search_data = $request->get('filter_search_data');

            $datatables = DataTables::of($data)
                ->filter(function ($query) use ($keywords, $start_date, $end_date, $filter_search_data) {
                    if ($filter_search_data) {
                        $query->where('id', $filter_search_data);
                    }

                    if ($keywords) {
                        $query->Where('brand_name', 'like', "%{$keywords}%");
                    }
                })

                ->addColumn('action', function ($row) use ($start_date, $end_date) {
                    if (empty($start_date) && empty($end_date)) {
                        $start_date = date("Y-m-d");
                        $end_date = date("Y-m-d", strtotime("-1 month"));
                    }
                    $view_btn = '<a href="javascript:void(0)" onclick="return viewInvoice(' . $row->id . ', \'' . $start_date . '\', \'' . $end_date . '\')" class="btn btn-icon btn-active-info btn-light-info mx-1 w-30px h-30px" > 
                    <i class="fa fa-eye"></i>
                </a>';
                    $download_btn = '<a href="' . route('vendor_wise_sale.download', ["id" => $row->id, "start_date" => $start_date, "end_date" => $end_date]) . '" tooltip="Download Invoice" class="btn btn-icon btn-active-success btn-light-success mx-1 w-30px h-30px">
                    <i class="fa fa-download"></i>
                </a>';
                    $send_email_btn = '<a href="javascript:void(0);" onclick="return sendInvoice(' . $row->id . ', \'' . $start_date . '\', \'' . $end_date . '\')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                <i class="fa fa-envelope"></i></a>';
                    //onclick="return downloadInvoice(' . $row->id . ', ' . $start_date . ', ' . $end_date . ')"
                    return $view_btn . ' | ' . $download_btn . ' | ' . $send_email_btn;
                })

                ->rawColumns(['action', 'status', 'category', 'product_image']);;

            return $datatables->make(true);
        }


        $params = array(
            'title' => $title,
            'breadCrum' => $breadCrum,
            'vendors' => Brands::select('id', 'brand_name')->where('brand_name', '!=', '')
                ->orderBy('brand_name')->get()
        );

        return view('platform.reports.vendor_wise_sale.list', $params);
    }

    public function viewInvoice(Request $request)
    {
        $brand_id = $request->id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        try {

            DB::beginTransaction();
            $where = "WHERE brand_id = '$brand_id' AND gbs_orders.status != 'pending'";
            if (!empty($start_date) && !empty($end_date)) {
                $where = "WHERE DATE(gbs_brand_orders.created_at) <= '$start_date' AND DATE(gbs_brand_orders.created_at) >= '$end_date' AND brand_id = '$brand_id'  AND gbs_orders.status != 'pending'";
            }
            $data = DB::table(DB::raw("(SELECT gbs_brands.id as id, 
                                   gbs_brands.brand_name as brand_name, 
                                   gbs_brands.is_shipping_bared_golisoda,
                                   SUM(qty * price) as sale_amount, 
                                   total_excluding_tax, 
                                   gbs_brand_orders.shipping_amount AS shipping_charge, 
                                   gbs_brand_orders.brand_id as shipment_count, 
                                   gbs_brand_orders.commission_type, 
                                   CASE
                                       WHEN gbs_brand_orders.commission_type = 'percentage' THEN gbs_brand_orders.commission_value
                                       WHEN gbs_brand_orders.commission_type = 'fixed' THEN gbs_brand_orders.commission_value
                                       ELSE 0
                                   END AS com_amount,
                                   SUM(tax_amount) as total_tax_amount
                            FROM gbs_brand_orders
                            JOIN gbs_orders ON gbs_orders.id = gbs_brand_orders.order_id 
                            JOIN gbs_brands ON gbs_brands.id = gbs_brand_orders.brand_id $where
                            GROUP BY gbs_brand_orders.brand_id, gbs_brand_orders.commission_type) as a"))
                ->select(
                    'id',
                    'brand_name',
                    'is_shipping_bared_golisoda',
                    DB::raw('SUM(shipping_charge) as shipping_charge'),
                    DB::raw('SUM(sale_amount) as sale_amount'),
                    DB::raw('(SUM(sale_amount) - SUM(total_tax_amount)) as sale_amount_excluding_tax'),
                    DB::raw('
            CASE 
                WHEN commission_type = "fixed" THEN (SUM(sale_amount) - SUM(total_tax_amount)) - com_amount
                WHEN commission_type = "percentage" THEN (SUM(sale_amount) - SUM(total_tax_amount)) * com_amount / 100
                ELSE NULL
            END AS com_amount
        '),
                    DB::raw('COUNT(shipment_count) as total_shipments'),
                    DB::raw('
            CASE 
                WHEN commission_type = "fixed" THEN (0.09 * ((SUM(sale_amount) - SUM(total_tax_amount)) + SUM(shipping_charge) - com_amount))
                WHEN commission_type = "percentage" THEN (0.09 * ((SUM(sale_amount) - SUM(total_tax_amount)) + SUM(shipping_charge) * com_amount / 100))
                ELSE NULL
            END AS cgst_commission
        '),
                    DB::raw('
            CASE 
                WHEN commission_type = "fixed" THEN (0.09 * ((SUM(sale_amount) - SUM(total_tax_amount)) + SUM(shipping_charge) - com_amount))
                WHEN commission_type = "percentage" THEN (0.09 * ((SUM(sale_amount) - SUM(total_tax_amount)) + SUM(shipping_charge) * com_amount / 100))
                ELSE NULL
            END AS sgst_commission
        '),
                    DB::raw('
            CASE 
                WHEN commission_type = "fixed" THEN (0.01 * (SUM(sale_amount) - com_amount))
                WHEN commission_type = "percentage" THEN (0.01 * (SUM(sale_amount) * (com_amount / 100)))
                ELSE NULL
            END AS tds_commission
        ')
                )
                ->groupBy('id')
                ->first();


            // Step 1: Create the subquery with the brand_id filter



            $order_info = DB::table('brand_orders')
                ->join('brands', 'brand_orders.brand_id', '=', 'brands.id')
                ->join('orders', 'brand_orders.order_id', '=', 'orders.id')
                ->where('brand_orders.brand_id', $brand_id)
                ->where('orders.status', '!=', 'pending')
                ->whereRaw('DATE(gbs_brand_orders.created_at) <= ?', [$start_date])
                ->whereRaw('DATE(gbs_brand_orders.created_at) >= ?', [$end_date])
                ->select(
                    'brands.brand_name',
                    'brand_orders.tracking_id',
                    DB::raw('SUM(gbs_brand_orders.sub_total) as vendor_order_amount'),
                    'brand_orders.estimated_arrival_date',
                    'orders.*'
                )
                ->groupBy('brand_orders.brand_id', 'brand_orders.order_id')
                ->get();

            $brand_location = BrandVendorLocation::where([['brand_id', $brand_id], ['is_default', 1]])->first();

            DB::commit();

            $modal_title = 'View Invoice';
            $globalInfo = GlobalSettings::first();

            $view_order = view('platform.vendor_invoice.view_invoice', compact('order_info', 'data', 'globalInfo', 'brand_location', 'start_date', 'end_date'));

            return view('platform.vendor_invoice.view_modal', compact('view_order', 'modal_title'));
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function downloadInvoice(Request $request)
    {
        $brand_id = $request->id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        try {

            DB::beginTransaction();
            $where = "WHERE brand_id = '$brand_id' AND gbs_orders.status != 'pending'";
            if (!empty($start_date) && !empty($end_date)) {
                $where = "WHERE DATE(gbs_brand_orders.created_at) <= '$start_date' AND DATE(gbs_brand_orders.created_at) >= '$end_date' AND brand_id = '$brand_id'  AND gbs_orders.status != 'pending'";
            }
            $data = DB::table(DB::raw("(SELECT gbs_brands.id as id, 
            gbs_brands.brand_name as brand_name, 
            gbs_brands.is_shipping_bared_golisoda,
            SUM(qty * price) as sale_amount, 
            total_excluding_tax, 
            SUM(gbs_brand_orders.shipping_amount) AS shipping_charge, 
            COUNT(gbs_brand_orders.brand_id) as shipment_count, 
            gbs_brand_orders.commission_type, 
            CASE
                WHEN gbs_brand_orders.commission_type = 'percentage' THEN gbs_brand_orders.commission_value
                WHEN gbs_brand_orders.commission_type = 'fixed' THEN gbs_brand_orders.commission_value
                ELSE 0
            END AS com_amount,
            SUM(tax_amount) as total_tax_amount
     FROM gbs_brand_orders
     JOIN gbs_orders ON gbs_orders.id = gbs_brand_orders.order_id 
     JOIN gbs_brands ON gbs_brands.id = gbs_brand_orders.brand_id $where
     GROUP BY gbs_brand_orders.brand_id, gbs_brand_orders.commission_type) as a"))
                ->select(
                    'id',
                    'brand_name',
                    'is_shipping_bared_golisoda',
                    DB::raw('SUM(shipping_charge) as shipping_charge'),
                    DB::raw('SUM(sale_amount) as sale_amount'),
                    DB::raw('(SUM(sale_amount) - SUM(total_tax_amount)) as sale_amount_excluding_tax'),
                    DB::raw('
CASE 
WHEN commission_type = "fixed" THEN (SUM(sale_amount) - SUM(total_tax_amount)) - com_amount
WHEN commission_type = "percentage" THEN (SUM(sale_amount) - SUM(total_tax_amount)) * com_amount / 100
ELSE NULL
END AS com_amount
'),
                    DB::raw('SUM(shipment_count) as total_shipments'),
                    DB::raw('
CASE 
WHEN commission_type = "fixed" THEN (0.09 * ((SUM(sale_amount) - SUM(total_tax_amount)) + SUM(shipping_charge) - com_amount))
WHEN commission_type = "percentage" THEN (0.09 * ((SUM(sale_amount) - SUM(total_tax_amount)) + SUM(shipping_charge) * com_amount / 100))
ELSE NULL
END AS cgst_commission
'),
                    DB::raw('
CASE 
WHEN commission_type = "fixed" THEN (0.09 * ((SUM(sale_amount) - SUM(total_tax_amount)) + SUM(shipping_charge) - com_amount))
WHEN commission_type = "percentage" THEN (0.09 * ((SUM(sale_amount) - SUM(total_tax_amount)) + SUM(shipping_charge) * com_amount / 100))
ELSE NULL
END AS sgst_commission
'),
                    DB::raw('
CASE 
WHEN commission_type = "fixed" THEN (0.01 * (SUM(sale_amount) - com_amount))
WHEN commission_type = "percentage" THEN (0.01 * (SUM(sale_amount) * (com_amount / 100)))
ELSE NULL
END AS tds_commission
')
                )
                ->groupBy('id')
                ->first();



            // Step 1: Create the subquery with the brand_id filter



            $order_info = DB::table('brand_orders')
                ->join('brands', 'brand_orders.brand_id', '=', 'brands.id')
                ->join('orders', 'brand_orders.order_id', '=', 'orders.id')
                ->where('brand_orders.brand_id', $brand_id)
                ->where('orders.status', '!=', 'pending')
                ->whereRaw('DATE(gbs_brand_orders.created_at) <= ?', [$start_date])
                ->whereRaw('DATE(gbs_brand_orders.created_at) >= ?', [$end_date])
                ->select(
                    'brands.brand_name',
                    'brand_orders.tracking_id',
                    DB::raw('SUM(gbs_brand_orders.sub_total) as vendor_order_amount'),
                    'brand_orders.estimated_arrival_date',
                    'orders.*'
                )
                ->groupBy('brand_orders.brand_id', 'brand_orders.order_id')
                ->get();
            $brand_location = BrandVendorLocation::where([['brand_id', $brand_id], ['is_default', 1]])->first();

            DB::commit();
            $modal_title = 'View Invoice';
            $globalInfo = GlobalSettings::first();

            // $view_order = view('platform.vendor_invoice.view_invoice', compact('order_info', 'data', 'globalInfo', 'brand_location'));
            // if ($request->has('download')) {
            $pdf = PDF::loadView('platform.vendor_invoice.view_invoice', compact('order_info', 'data', 'globalInfo', 'brand_location', 'start_date', 'end_date'));

            Storage::put('public/vendor_invoice/' . $brand_id . date('d-m-Y_H_i') . '.pdf', $pdf->output());
            // dd('works here');

            return $pdf->download($brand_id . date('d-m-Y_H_i') . '.pdf');
            // }
            // 

            // return view('platform.vendor_invoice.view_modal', compact('view_order', 'modal_title'));
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function sendInvoice(Request $request)
    {
        $brand_id = $request->id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        try {

            DB::beginTransaction();
            $where = "WHERE brand_id = '$brand_id' AND gbs_orders.status != 'pending'";
            if (!empty($start_date) && !empty($end_date)) {
                $where = "WHERE DATE(gbs_brand_orders.created_at) <= '$start_date' AND DATE(gbs_brand_orders.created_at) >= '$end_date' AND brand_id = '$brand_id'  AND gbs_orders.status != 'pending'";
            }
            $data = DB::table(DB::raw("(SELECT gbs_brands.id as id, 
                                   gbs_brands.brand_name as brand_name, 
                                   gbs_brands.is_shipping_bared_golisoda,
                                   SUM(qty * price) as sale_amount, 
                                   total_excluding_tax, 
                                   SUM(gbs_brand_orders.shipping_amount) AS shipping_charge, 
                                   COUNT(gbs_brand_orders.brand_id) as shipment_count, 
                                   gbs_brand_orders.commission_type, 
                                   CASE
                                       WHEN gbs_brand_orders.commission_type = 'percentage' THEN gbs_brand_orders.commission_value
                                       WHEN gbs_brand_orders.commission_type = 'fixed' THEN gbs_brand_orders.commission_value
                                       ELSE 0
                                   END AS com_amount,
                                   SUM(tax_amount) as total_tax_amount
                            FROM gbs_brand_orders
                            JOIN gbs_orders ON gbs_orders.id = gbs_brand_orders.order_id 
                            JOIN gbs_brands ON gbs_brands.id = gbs_brand_orders.brand_id $where
                            GROUP BY gbs_brand_orders.brand_id, gbs_brand_orders.commission_type) as a"))
                ->select(
                    'id',
                    'brand_name',
                    'is_shipping_bared_golisoda',
                    DB::raw('SUM(shipping_charge) as shipping_charge'),
                    DB::raw('SUM(sale_amount) as sale_amount'),
                    DB::raw('(SUM(sale_amount) - SUM(total_tax_amount)) as sale_amount_excluding_tax'),
                    DB::raw('
            CASE 
                WHEN commission_type = "fixed" THEN (SUM(sale_amount) - SUM(total_tax_amount)) - com_amount
                WHEN commission_type = "percentage" THEN (SUM(sale_amount) - SUM(total_tax_amount)) * com_amount / 100
                ELSE NULL
            END AS com_amount
        '),
                    DB::raw('SUM(shipment_count) as total_shipments'),
                    DB::raw('
            CASE 
                WHEN commission_type = "fixed" THEN (0.09 * ((SUM(sale_amount) - SUM(total_tax_amount)) + SUM(shipping_charge) - com_amount))
                WHEN commission_type = "percentage" THEN (0.09 * ((SUM(sale_amount) - SUM(total_tax_amount)) + SUM(shipping_charge) * com_amount / 100))
                ELSE NULL
            END AS cgst_commission
        '),
                    DB::raw('
            CASE 
                WHEN commission_type = "fixed" THEN (0.09 * ((SUM(sale_amount) - SUM(total_tax_amount)) + SUM(shipping_charge) - com_amount))
                WHEN commission_type = "percentage" THEN (0.09 * ((SUM(sale_amount) - SUM(total_tax_amount)) + SUM(shipping_charge) * com_amount / 100))
                ELSE NULL
            END AS sgst_commission
        '),
                    DB::raw('
            CASE 
                WHEN commission_type = "fixed" THEN (0.01 * (SUM(sale_amount) - com_amount))
                WHEN commission_type = "percentage" THEN (0.01 * (SUM(sale_amount) * (com_amount / 100)))
                ELSE NULL
            END AS tds_commission
        ')
                )
                ->groupBy('id')
                ->first();



            // Step 1: Create the subquery with the brand_id filter



            $order_info = DB::table('brand_orders')
                ->join('brands', 'brand_orders.brand_id', '=', 'brands.id')
                ->join('orders', 'brand_orders.order_id', '=', 'orders.id')
                ->where('brand_orders.brand_id', $brand_id)
                ->where('orders.status', '!=', 'pending')
                ->whereRaw('DATE(gbs_brand_orders.created_at) <= ?', [$start_date])
                ->whereRaw('DATE(gbs_brand_orders.created_at) >= ?', [$end_date])
                ->select(
                    'brands.brand_name',
                    'brand_orders.tracking_id',
                    DB::raw('SUM(gbs_brand_orders.sub_total) as vendor_order_amount'),
                    'brand_orders.estimated_arrival_date',
                    'orders.*'
                )
                ->groupBy('brand_orders.brand_id', 'brand_orders.order_id')
                ->get();
            $brand_location = BrandVendorLocation::where([['brand_id', $brand_id], ['is_default', 1]])->first();

            DB::commit();
            $modal_title = 'View Invoice';
            $globalInfo = GlobalSettings::first();

            $pdf = PDF::loadView('platform.vendor_invoice.view_invoice', compact('order_info', 'data', 'globalInfo', 'brand_location', 'start_date', 'end_date'));

            Storage::put('public/vendor_invoice/' . $brand_id . date('d-m-Y_H_i') . '.pdf', $pdf->output());
            $email_slug = 'vendor-tax-invoice';
            $to_email_address = $brand_location->email_id;
            $globalInfo = GlobalSettings::first();
            $filePath = 'storage/vendor_invoice/'  . $brand_id . date('d-m-Y_H_i') . '.pdf';
            $extract = array(
                'name' => $brand_location->branch_name,
                'regards' => $globalInfo->site_name,
                'company_website' => '',
                'company_mobile_no' => $globalInfo->site_mobile_no,
                'company_address' => $globalInfo->address,
                'dynamic_content' => '',
            );

            $this->sendEmailNotificationByArray($email_slug, $extract, $to_email_address, $filePath);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()]);
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
}
