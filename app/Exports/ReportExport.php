<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportExport implements FromView
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
        $list = Order::selectRaw('gbs_payments.order_id,gbs_payments.payment_no,gbs_payments.status as payment_status,gbs_orders.*,sum(gbs_order_products.quantity) as order_quantity')
                            ->join('order_products', 'order_products.order_id', '=', 'orders.id')
                            ->join('payments', 'payments.order_id', '=', 'orders.id')
                            ->where('orders.status', '!=', 'pending')
                            ->when( $start_date != '', function($query) use($start_date, $end_date){
                                $query->where( function($q) use ($start_date, $end_date){
                                    $q->whereDate('orders.created_at', '>=', $start_date);
                                    $q->whereDate('orders.created_at', '<=', $end_date);
                                });
                            })
                            ->when($filter_search_data != '', function($q) use($filter_search_data){
                                $q->where('orders.order_no', $filter_search_data);
                            })
                            ->when($filter_product_status != '', function($q) use($filter_product_status){
                                $q->where('orders.status', $filter_product_status);
                            })
                            ->groupBy('orders.id')->orderBy('orders.id', 'desc')
                            ->get();
                            
        return view('platform.order._excel', compact('list'));
    }
}
