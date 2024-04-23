<?php

namespace App\Http\Controllers;

use App\Exports\PaymentGatewayExport;
use App\Models\Category\MainCategory;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use PDF;
use Excel;
use Illuminate\Support\Facades\DB;

class PaymentGatewayController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data       = PaymentGateway::select('payment_gateways.*','users.name as users_name', 'sub_categories.name as gateway')
                                            ->join('users', 'users.id', '=', 'payment_gateways.added_by')
                                            ->join('sub_categories', 'sub_categories.id', '=', 'payment_gateways.gateway_id');
            $status     = $request->get('status');
            $keywords   = $request->get('search')['value'];
            $datatables =  DataTables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('payment_gateways.mode', 'like', "%{$status}%");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('payment_gateways.title', 'like', "%{$keywords}%")->orWhere('users.name', 'like', "%{$keywords}%")->orWhereDate("walk_throughs.created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->addColumn('mode', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->mode == 'live') ? 'success': 'danger').'" tooltip="Click to '.(($row->mode == 'live') ? 'Test' : 'Live').'" onclick="return commonChangeStatus(' . $row->id . ', \''.(($row->mode == 'live') ? 'test': 'live').'\', \'payment-gateway\')">'.ucfirst($row->mode).'</a>';
                    return $status;
                })

                ->addColumn('is_primary', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->is_primary == '1') ? 'success': 'danger').'" tooltip="Click to '.(($row->is_primary == '1') ? 'Yes' : 'No').'" ">'.ucfirst((($row->is_primary == '1') ? 'Yes' : 'No')).'</a>';
                    return $status;
                })
                
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'payment-gateway\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'payment-gateway\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'mode', 'is_primary']);
            return $datatables->make(true);
        }
    }

    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Payment Gateway';
        $gateways           = MainCategory::where(['slug' => 'payment-gateway', 'status' => 'published'])->first();
        if (isset($id) && !empty($id)) {
            $info           = PaymentGateway::find($id);
            $modal_title    = 'Update Payment Gateway';
        }
        
        return view('platform.payment_gateway.add_edit_modal', compact('info', 'modal_title', 'gateways'));
    }

    public function saveForm(Request $request,$id = null)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'gateway_id' => 'required|unique:payment_gateways,gateway_id,' . $id. ',id,deleted_at,NULL',
                            ]);

        if ($validator->passes()) {

            if( isset($request->is_primary) && $request->is_primary == 1 ) {
                DB::table('payment_gateways')->update(['is_primary' => '0' ]);
            }

            $ins['gateway_id']          = $request->gateway_id;
            $ins['access_key']          = $request->access_key ?? null;
            $ins['secret_key']          = $request->secret_key ?? null;
            $ins['merchant_id']         = $request->merchant_id ?? null;
            $ins['working_key']         = $request->working_key ?? null;
            $ins['is_primary']          = $request->is_primary ?? '0';
            $ins['added_by']            = auth()->user()->id;

            if($request->status == "1")
            {
                $ins['mode']            = 'live';
            } else {
                $ins['mode']            = 'test';
            }

            $error                      = 0;
            PaymentGateway::updateOrCreate(['id' => $id], $ins);
            $message                    = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';

        } else {
            $error                      = 1;
            $message                    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);

    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = PaymentGateway::find($id);
        $info->delete();
        return response()->json(['message'=>"Successfully deleted Payment Gateway!",'status'=>1]);
    }

    public function changeStatus(Request $request)
    {
        
        $id             = $request->id;
        $status         = $request->status;
        $info           = PaymentGateway::find($id);
        $info->mode     = $status;
        $info->update();
        return response()->json(['message'=>"You changed the Payment Gateway status!",'status'=>1]);

    }

    public function export()
    {
        return Excel::download(new PaymentGatewayExport, 'payment_gateways.xlsx');
    }

    public function exportPdf()
    {
        $list       = PaymentGateway::get();
        $pdf        = PDF::loadView('platform.exports.global.payment_gateway', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('payment_gateways.pdf');
    }


    
}
