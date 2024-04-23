<?php

namespace App\Http\Controllers;

use App\Exports\ProductwiseSaleReportExport;
use App\Models\Category\MainCategory;
use App\Models\Master\Brands;
use App\Models\Order;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Maatwebsite\Excel\Facades\Excel;

class ProductwiseSaleReportController extends Controller
{
    public function index(Request $request)
    {
        $title                  = "Productwise Sale Report";
        $breadCrum              = array('Reports', 'Productwise Sale Report');
        
        if ($request->ajax()) {
            
            $data = Order::selectRaw('gbs_payments.order_id,gbs_payments.payment_no,gbs_payments.status 
            as payment_status,gbs_orders.*,sum(gbs_order_products.quantity) as order_quantity
            ,sum(gbs_order_products.price) as prod_amount,gbs_order_products.product_name,gbs_product_categories.name as category_name')
            ->join('order_products', 'order_products.order_id', '=', 'orders.id')
            ->join('products', 'products.id', '=', 'order_products.product_id')
            ->join('product_categories', 'product_categories.id', '=', 'products.category_id')
            ->join('payments', 'payments.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'pending')
            ->groupBy('order_products.product_id');

            
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
                    
                    if( $filter_product_status ) {
                        $query->where('orders.status', $filter_product_status);
                    }
                    if( $filter_search_data ) {
                        $query->where('order_products.product_name', $filter_search_data);
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
                        $query->Where('order_products.product_name','like',"%{$keywords}%")
                                ->orWhere('product_categories.name', 'like', "%{$keywords}%")
                                ->orWhereDate("orders.created_at", $date);      
                               
                    }
                })->editColumn('status', function($row){
                    
                    return ucwords( str_replace("_", " ", $row->status) );
                });
                
            return $datatables->make(true);
        }

       
        $params                 = array(
                                    'title' => $title,
                                    'breadCrum' => $breadCrum
                                    
                                );

        return view('platform.reports.productwise_sale.list', $params);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new ProductwiseSaleReportExport, 'productswise_sale.xlsx');
    }
}
