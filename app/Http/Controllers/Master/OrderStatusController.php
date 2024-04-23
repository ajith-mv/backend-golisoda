<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\OrderStatus;
use App\Exports\OrderStatusExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Carbon\Carbon;
use Auth;
use Excel;
use PDF;

class OrderStatusController extends Controller
{
    public function index(Request $request)
    { 
        $title = "Order Status";
        if ($request->ajax()) {
            $data = OrderStatus::select('order_statuses.*','users.name as users_name')->join('users', 'users.id', '=', 'order_statuses.added_by');
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
    
    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Order Status';
        if (isset($id) && !empty($id)) {
            $info           = OrderStatus::find($id);
            $modal_title    = 'Update User';
        }
        return view('platform.master.order-status.add_edit_modal', compact('info', 'modal_title'));
    }

    public function saveForm(Request $request,$id = null)
    {
        // dd($request->all());
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'status_name' => 'required|string|unique:order_statuses,status_name,' . $id . ',id,deleted_at,NULL',
                                'order' => 'required|unique:order_statuses,order,' . $id . ',id,deleted_at,NULL',
                                'tracking_link' => 'nullable|url',
                            ]);

        if ($validator->passes()) {
           
            $ins['status_name']         = $request->status_name;
            $ins['description']         = $request->description;
            $ins['tracking_link']       = $request->tracking_link;
            $ins['tracking_number']     = $request->tracking_number;
            $ins['shipping_medium']     = $request->shipping_medium;
            $ins['order']               = $request->order ?? '';
            $ins['added_by']            = Auth::id();
           
            if($request->status == "1")
            {
                $ins['status']      = 'published';
            } else {
                $ins['status']      = 'unpublished';
            }
            $error                  = 0;

            $info                   = OrderStatus::updateOrCreate(['id' => $id], $ins);
            $message                = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } 
        else {
            $error                  = 1;
            $message                = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = OrderStatus::find($id);
        $info->delete();
        // echo 1;
        return response()->json(['message'=>"Successfully deleted order status!",'status'=>1]);
    }

    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = OrderStatus::find($id);
        $info->status   = $status;
        $info->update();
        // echo 1;
        return response()->json(['message'=>"You changed the order status!",'status'=>1]);

    }

    public function export()
    {
        return Excel::download(new OrderStatusExport, 'order_status.xlsx');
    }

    public function exportPdf()
    {
        $list       = OrderStatus::select('order_statuses.*','users.name as users_name')->join('users', 'users.id', '=', 'order_statuses.added_by')->get();
        $pdf        = PDF::loadView('platform.exports.order_status.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('order_status.pdf');
    }
    
}
