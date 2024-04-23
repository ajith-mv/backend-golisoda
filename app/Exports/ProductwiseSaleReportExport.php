<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductwiseSaleReportExport implements FromView
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

     
        $list = Order::selectRaw('gbs_payments.order_id,gbs_payments.payment_no,gbs_payments.status 
        as payment_status,gbs_orders.*,sum(gbs_order_products.quantity) as order_quantity
        ,sum(gbs_order_products.price) as prod_amount,gbs_order_products.product_name,gbs_product_categories.name as category_name')
        ->join('order_products', 'order_products.order_id', '=', 'orders.id')
        ->join('products', 'products.id', '=', 'order_products.product_id')
        ->join('product_categories', 'product_categories.id', '=', 'products.category_id')
        ->join('payments', 'payments.order_id', '=', 'orders.id')
        ->where('orders.status', '!=', 'pending')
                            ->when( $start_date != '', function($query) use($start_date, $end_date){
                                $query->where( function($q) use ($start_date, $end_date){
                                    $q->whereDate('orders.created_at', '>=', $start_date);
                                    $q->whereDate('orders.created_at', '<=', $end_date);
                                });
                            })
                            ->when($filter_search_data != '', function($q) use($filter_search_data){
                                $q->where('order_products.product_name', $filter_search_data);
                            })
                            ->when($filter_product_status != '', function($q) use($filter_product_status){
                                $q->where('orders.status', $filter_product_status);
                            })
                            ->groupBy('order_products.product_id')
                            ->get();
                            
        return view('platform.reports.productwise_sale._excel', compact('list'));
    }
}
