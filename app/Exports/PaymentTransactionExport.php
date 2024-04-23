<?php

namespace App\Exports;

use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PaymentTransactionExport implements FromView
{
    public function view(): View
    {

        $filter_search_data = request()->filter_product_status;
        $date_range = request()->date_range;
        $filter_product_status = request()->filter_product_status;
        $start_date = $end_date = '';
       
        if( isset( $date_range ) && !empty( $date_range ) ) {
                
            $dates = explode('-', $date_range);
            $start_date = date('Y-m-d', strtotime( trim(str_replace('/', '-',$dates[0]))));
            $end_date = date('Y-m-d', strtotime( trim( str_replace('/', '-', $dates[1]))));
            
        }
       // $list = Payment::all();
       $list =Payment::selectRaw('gbs_orders.order_no,gbs_orders.created_at as order_date,gbs_orders.amount as order_amount
       ,gbs_orders.status as order_status_dd,gbs_payments.status as payment_status,gbs_payments.*, sum(gbs_order_products.quantity) as order_quantity')
                ->join('orders', 'orders.id', '=', 'payments.order_id')
                ->join('order_products', 'order_products.order_id', '=', 'orders.id')               
               ->when( $start_date != '', function($query) use($start_date, $end_date){
                $query->where( function($q) use ($start_date, $end_date){
                    $q->whereDate('orders.created_at', '>=', $start_date);
                    $q->whereDate('orders.created_at', '<=', $end_date);
                });
            })
            ->when($filter_search_data != '', function($q) use($filter_search_data){
                $q->where('payments.status', $filter_search_data);
            })
            ->when($filter_product_status != '', function($q) use($filter_search_data){
                $q->where('order.status', $filter_search_data);
            })->groupBy('orders.id')->orderBy('orders.id', 'desc')->get();
        return view('platform.reports.payment_transaction._excel', compact('list'));
    }
}
