<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CustomerwiseSaleReportExport implements FromView
{
    public function view(): View
    {
        $filter_search_data = request()->filter_search_data;
        $date_range = request()->date_range;
        $filter_product_status = request()->filter_product_status;
        $start_date = $end_date = '';
       
        if( isset( $date_range ) && !empty( $date_range ) ) {
                
            $dates = explode('-', $date_range);
            $start_date = date('Y-m-d', strtotime( trim(str_replace('/', '-',$dates[0]))));
            $end_date = date('Y-m-d', strtotime( trim( str_replace('/', '-', $dates[1]))));
            
        }

     
        $list = Order::selectRaw('gbs_customers.first_name,gbs_customers.last_name,count(DISTINCT(gbs_orders.id)) as total_order,
        count(gbs_order_products.id) as total_products,sum(gbs_orders.coupon_amount) as cus_coupon_amount,
        sum(gbs_order_products.save_price) as discount_amount,sum(gbs_order_products.price) as order_amount')
        ->join('order_products', 'order_products.order_id', '=', 'orders.id')
        ->join('customers', 'customers.id', '=', 'orders.customer_id')
        ->where('orders.status', '!=', 'pending')
                ->when( $start_date != '', function($query) use($start_date, $end_date){
                    $query->where( function($q) use ($start_date, $end_date){
                        $q->whereDate('orders.created_at', '>=', $start_date);
                        $q->whereDate('orders.created_at', '<=', $end_date);
                    });
                })
                ->when($filter_search_data != '', function($q) use($filter_search_data){
                    $q->where('customers.first_name', $filter_search_data);
                })                           
                ->groupBy('orders.customer_id')
                ->get();
                            
        return view('platform.reports.customerwise_sales._excel', compact('list'));
    }
}
