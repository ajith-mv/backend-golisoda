<?php

namespace App\Http\Controllers;

use App\Exports\CustomerwiseSaleReportExport;
use App\Models\Category\MainCategory;
use App\Models\Master\Brands;
use App\Models\Order;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;


class CustomerwiseSaleReportController extends Controller
{
    public function index(Request $request)
    {
        $title                  = "Customerwise Sales Report";
        $breadCrum              = array('Reports', 'Customerwise Sale Report');
        
        if ($request->ajax()) {

             $data = Order::selectRaw('gbs_customers.first_name,gbs_customers.last_name,count(DISTINCT(gbs_orders.id)) as total_order,
             count(gbs_order_products.id) as total_products,sum(gbs_orders.coupon_amount) as cus_coupon_amount,
             sum(gbs_order_products.save_price) as discount_amount,sum(gbs_order_products.price) as order_amount')
             ->join('order_products', 'order_products.order_id', '=', 'orders.id')
             ->join('customers', 'customers.id', '=', 'orders.customer_id')
             ->where('orders.status', '!=', 'pending')
             ->groupBy('orders.customer_id');

            
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
          //  dd(  $start_date.'end'.$end_date);
            
            $datatables =  DataTables::of($data)
                ->filter(function ($query) use ($keywords,$start_date, $end_date, $filter_search_data, $filter_product_status) {
                    
                    if( $filter_search_data ) {
                        $query->where('customers.first_name', $filter_search_data);
                    }
                    if( !empty( $start_date ) && !empty( $end_date ) ) {
                        $query->where( function($q) use ($start_date, $end_date){
                            $q->whereDate('orders.created_at', '>=', $start_date);
                            $q->whereDate('orders.created_at', '<=', $end_date);
                        });
                    }
                    //dd($keywords);
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        $query->Where('customers.first_name','like',"%{$keywords}%")                              
                                ->orWhereDate("orders.created_at", $date);      
                               
                    }
                });
                
            return $datatables->make(true);
        }

       
        $params                 = array(
                                    'title' => $title,
                                    'breadCrum' => $breadCrum
                                    
                                );

        return view('platform.reports.customerwise_sales.list', $params);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new CustomerwiseSaleReportExport, 'customerwise_sale.xlsx');
    }
}
