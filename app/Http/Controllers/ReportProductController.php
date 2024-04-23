<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Category\MainCategory;
use App\Models\Master\Brands;
use App\Models\Order;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Maatwebsite\Excel\Facades\Excel;

class ReportProductController extends Controller
{
    public function index(Request $request)
    {
        $title                  = "Standard Sale Report";
        $breadCrum              = array('Reports', 'Standard Sale Report');
        
        if ($request->ajax()) {
            
            $data = Order::selectRaw('gbs_payments.order_id,gbs_payments.payment_no,gbs_orders.status as order_status,gbs_payments.status as payment_status,gbs_orders.*,sum(gbs_order_products.quantity) as order_quantity')
                            ->join('order_products', 'order_products.order_id', '=', 'orders.id')
                            ->join('payments', 'payments.order_id', '=', 'orders.id')
                            ->where('orders.status', '!=', 'pending')
                            ->groupBy('orders.id')->orderBy('orders.id', 'desc');
            
            $keywords = $request->get('search')['value'];
            $filter_search_data = $request->get('filter_search_data');
            $date_range = $request->get('date_range');
            $filter_product_status = $request->get('filter_product_status');
            $start_date = $end_date = '';
            if( isset( $date_range ) && !empty( $date_range ) ) {
                
                $dates = explode('-', $date_range);
                $start_date = date('Y-m-d', strtotime( trim(str_replace('/', '-',$dates[0]))));
                $end_date = date('Y-m-d', strtotime( trim( str_replace('/', '-', $dates[1]))));
                
            }
            
            $datatables =  DataTables::of($data)
                ->filter(function ($query) use ($keywords,$start_date, $end_date, $filter_search_data, $filter_product_status) {
                    
                    if( $filter_product_status ) {
                        $query->where('orders.status', $filter_product_status);
                    }
                    if( $filter_search_data ) {
                        $query->where('orders.order_no', $filter_search_data);
                    }
                    if( !empty( $start_date ) && !empty( $end_date ) ) {
                        $query->where( function($q) use ($start_date, $end_date){
                            $q->whereDate('orders.created_at', '>=', $start_date);
                            $q->whereDate('orders.created_at', '<=', $end_date);
                        });
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        $query->where('orders.billing_name','like',"%{$keywords}%")
                                ->orWhere('orders.billing_email', 'like', "%{$keywords}%")
                                ->orWhere('orders.billing_mobile_no', 'like', "%{$keywords}%")
                                ->orWhere('orders.billing_address_line1', 'like', "%{$keywords}%")
                                ->orWhere('orders.billing_state', 'like', "%{$keywords}%")
                                ->orWhere('orders.status', 'like', "%{$keywords}%")
                                ->orWhereDate("orders.created_at", $date);
                    }
                })
                ->addIndexColumn()
                ->editColumn('billing_info', function ($row) {
                    $billing_info = '';
                    $billing_info .= '<div class="font-weight-bold">'.$row['billing_name'].'</div>';
                    $billing_info .= '<div class="">'.$row['billing_email'].','.$row['billing_mobile_no'].'</div>';
                    $billing_info .= '<div class="">'.$row['billing_address_line1'].'</div>';

                    return $billing_info;
                })
               
                ->editColumn('payment_status', function ($row) {
                    return ucwords($row->payment_status);
                })
                ->editColumn('order_status', function ($row) {
                    return ucwords($row->status);
                })
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })
             
               
                ->rawColumns(['billing_info', 'payment_status', 'order_status', 'created_at']);
            return $datatables->make(true);
        }

        $addHref = route('products.add.edit');
        $routeValue = 'products';
        $productCategory        = ProductCategory::where('status', 'published')->get();
        $brands                 = Brands::where('status', 'published')->get();
        $productLabels          = MainCategory::where(['slug' => 'product-labels', 'status' => 'published'])->first();        
        $productTags            = MainCategory::where(['slug' => 'product-tags', 'status' => 'published'])->first();

        $params                 = array(
                                    'title' => $title,
                                    'breadCrum' => $breadCrum,
                                    'addHref' => $addHref,
                                    'routeValue' => $routeValue,
                                    'productCategory' => $productCategory,
                                    'brands' => $brands,
                                    'productLabels' => $productLabels,
                                    'productTags' => $productTags,
                                );

        return view('platform.reports.products.list', $params);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new ReportExport, 'products.xlsx');
    }
}
