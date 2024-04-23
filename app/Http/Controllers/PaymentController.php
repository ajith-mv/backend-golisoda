<?php

namespace App\Http\Controllers;

use App\Exports\PaymentExport;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Payment::selectRaw('gbs_orders.order_no, gbs_payments.status as payment_status, gbs_payments.*, sum(gbs_order_products.quantity) as order_quantity')
                            ->join('orders', 'orders.id', '=', 'payments.order_id')
                            ->join('order_products', 'order_products.order_id', '=', 'orders.id')
                            ->groupBy('orders.id')->orderBy('orders.id', 'desc');

            $filter_subCategory   = '';
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables = DataTables::of($data)
                ->filter(function ($query) use ($keywords, $status, $filter_subCategory) {
                    if ($status) {
                        return $query->where('payments.status', 'like', $status);
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('orders.billing_name','like',"%{$keywords}%")
                                ->orWhere('payments.payment_no', 'like', "%{$keywords}%")
                                ->orWhere('payments.amount', 'like', "%{$keywords}%")
                                ->orWhere('payments.payment_mode', 'like', "%{$keywords}%")
                                ->orWhere('payments.status', 'like', "%{$keywords}%")
                                ->orWhereDate("payments.created_at", $date);
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
                ->addColumn('action', function ($row) {
                    $view_btn = '<a href="javascript:void(0)" onclick="return viewPayments('.$row->id.')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                                    <i class="fa fa-eye"></i>
                                </a>';
                    return $view_btn;
                })
                ->rawColumns(['action', 'status', 'billing_info', 'payment_status', 'order_status', 'created_at']);
            return $datatables->make(true);
        }
        $breadCrum = array('Payments');
        $title      = 'Payment';
        return view('platform.payment.list.index',compact('title','breadCrum'));
    }

    public function paymentView(Request $request)
    {

        $payment_id     = $request->id;        
        $payment_info   = Payment::find($payment_id);
        $modal_title    = 'View Payment List';
        
        return view('platform.payment.list.view', compact('payment_info', 'modal_title'));

    }

    public function export()
    {
        return Excel::download(new PaymentExport, 'payments.xlsx');
    }

}
