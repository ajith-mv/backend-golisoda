<?php

namespace App\Http\Controllers;

use App\Exports\PaymentTransactionExport;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Maatwebsite\Excel\Facades\Excel;

class PaymentReportController extends Controller
{
    public function index(Request $request)
    {
        $title                  = "Payment Transaction Report";
        $breadCrum              = array('Reports', 'Payment Transaction Report');
        if ($request->ajax()) {
            $data = Payment::selectRaw('gbs_orders.order_no,gbs_orders.created_at as order_date,gbs_orders.amount as order_amount
            ,gbs_orders.status as order_status_dd,
                                 gbs_payments.status as payment_status,gbs_payments.*, sum(gbs_order_products.quantity) as order_quantity')
                            ->join('orders', 'orders.id', '=', 'payments.order_id')
                            ->join('order_products', 'order_products.order_id', '=', 'orders.id')
                            ->groupBy('orders.id')->orderBy('orders.id', 'desc');
          //  dd($request->get('filter_payment_status'));
            $filter_subCategory   = '';
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $filter_search_data = $request->get('filter_payment_status');
            $date_range = $request->get('date_range');
            $filter_product_status = $request->get('filter_product_status');
            $start_date = $end_date = '';
            if( isset( $date_range ) && !empty( $date_range ) ) {
                
                $dates = explode('-', $date_range);
                $start_date = date('Y-m-d', strtotime( trim(str_replace('/', '-',$dates[0]))));
                $end_date = date('Y-m-d', strtotime( trim( str_replace('/', '-', $dates[1]))));
                
            }
            $datatables = DataTables::of($data)
                ->filter(function ($query) use ($keywords,$start_date, $end_date, $filter_search_data, $filter_product_status) {

                    if( $filter_search_data ) {
                        $query->where('payments.status', $filter_search_data);
                    }
                    if( $filter_product_status ) {
                        $query->where('orders.status', $filter_product_status);
                    }
                    if( !empty( $start_date ) && !empty( $end_date ) ) {
                        $query->where( function($q) use ($start_date, $end_date){
                            $q->whereDate('orders.created_at', '>=', $start_date);
                            $q->whereDate('orders.created_at', '<=', $end_date);
                        });
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('orders.order_no','like',"%{$keywords}%")
                                ->orWhere('payments.payment_no', 'like', "%{$keywords}%")
                                ->orWhere('orders.amount', 'like', "%{$keywords}%")
                                ->orWhere('orders.status', 'like', "%{$keywords}%")
                                ->orWhere('payments.status', 'like', "%{$keywords}%")
                                ->orWhereDate("orders.created_at", $date);
                    }
                })
                ->addIndexColumn()
                ->editColumn('order_status_dd', function($row){
                    return ucwords(str_replace('_', " ", $row->order_status_dd) );
                })
                ->addColumn('action', function ($row) {
                    $view_btn = '<a href="javascript:void(0)" onclick="return viewPayments('.$row->id.')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                                    <i class="fa fa-eye"></i>
                                </a>';
                    return $view_btn;
                })
                ->rawColumns(['action', 'order_status_dd']);
               
            return $datatables->make(true);
        }
       
        return view('platform.reports.payment_transaction.index',compact('title','breadCrum'));
    }

    public function paymentView(Request $request)
    {

        $payment_id     = $request->id;        
        $payment_info   = Payment::find($payment_id);
        $modal_title    = 'View Payment List';
        
        return view('platform.reports.payment_transaction.view', compact('payment_info', 'modal_title'));

    }

    public function export()
    {
        return Excel::download(new PaymentTransactionExport, 'paymentsreport.xlsx');
    }

}
