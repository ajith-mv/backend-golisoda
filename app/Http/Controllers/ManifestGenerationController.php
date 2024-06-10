<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Exports\OrderStatusExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Carbon\Carbon;
use Auth;
use Excel;
use PDF;

class ManifestGenerationController extends Controller
{
    public function index(Request $request)
    { 
        $title = "Manifest Generation";
        if ($request->ajax()) {
            $data = Order::selectRaw('gbs_payments.order_id,gbs_payments.payment_no,gbs_payments.status as payment_status,gbs_payments.payment_type,gbs_orders.status as order_status,gbs_orders.billing_address_line1 as billing_info,gbs_orders.*,sum(gbs_order_products.quantity) as order_quantity')
            ->join('order_products', 'order_products.order_id', '=', 'orders.id')
            ->join('brand_orders', 'brand_orders.order_id', 'orders.id')
            ->leftJoin('payments', 'payments.order_id', '=', 'orders.id')
            ->groupBy('orders.id')->orderBy('orders.shipping_post_code', 'desc');
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
            
                ->filter(function ($query) use ($keywords, $status) {
                    
                    if ($status) {
                        return $query->where('order_statuses.status', $status);
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('order_statuses.status_name', 'like', "%{$keywords}%")
                        ->orWhere('order_statuses.description', 'like', "%{$keywords}%")
                        ->orWhereDate("order_statuses.created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->addColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ', \''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'order-status\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'order-status\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'order-status\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn ;
                })
                ->rawColumns(['action', 'status', 'image']);
            return $datatables->make(true);
        }
       
        return view('platform.master.order-status.index');

    }
    
}
