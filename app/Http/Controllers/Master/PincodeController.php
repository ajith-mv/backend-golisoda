<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\PincodeExport;
use App\Imports\PincodeImport;
use App\Models\Master\Pincode;
use Illuminate\Support\Facades\DB;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Auth;
use Excel;
use PDF;
class PincodeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pincode::select('pincodes.*','users.name as users_name',DB::raw(" IF(gbs_pincodes.status = 2, 'Inactive', 'Active') as user_status"))->join('users', 'users.id', '=', 'pincodes.added_by')->orderBy('pincodes.id', 'desc');
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('pincodes.status', 'like', "%{$status}%");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('pincodes.pincode', 'like', "%{$keywords}%")->orWhere('pincodes.description', 'like', "%{$keywords}%")->orWhereDate("pincodes.created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->editColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-success" tooltip="Click to Inactive" onclick="return commonChangeStatus(' . $row->id . ', 2, \'pincode\')">Active</a>';
                    } else {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-danger" tooltip="Click to Active" onclick="return commonChangeStatus(' . $row->id . ', 1, \'pincode\')">Inactive</a>';
                    }
                    return $status;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'pincode\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                                    <i class="fa fa-edit"></i>
                                </a>';
                                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'pincode\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status']);
            return $datatables->make(true);
        }
        $breadCrum  = array('Deliverable pincode management', 'Pincode');
        $title      = 'Deliverable pincode management';
        return view('platform.master.pincode.index', compact('breadCrum', 'title'));
    }

    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Pincode';
        if (isset($id) && !empty($id)) {
            $info           = Pincode::find($id);
            $modal_title    = 'Update Pincode';
        }
        return view('platform.master.pincode.add_edit_modal', compact('info', 'modal_title'));
    }
    public function saveForm(Request $request,$id = null)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'pincode' => 'required|string|unique:pincodes,pincode,' . $id . ',id,deleted_at,NULL',
                             
                            ]);

        if ($validator->passes()) {
           
            $ins['pincode']                                 = $request->pincode;
            $ins['description']                             = $request->description;
            $ins['shipping_information']                    = $request->shipping_information;
            $ins['added_by']        = Auth::id();
            if($request->status == "1")
            {
                $ins['status']          = 1;
            }
            else{
                $ins['status']          = 2;
            }
            $error                  = 0;

            $info                   = Pincode::updateOrCreate(['id' => $id], $ins);
            $message                = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } 
        else {
            $error      = 1;
            $message    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);
    }
    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = Pincode::find($id);
        $info->delete();
        return response()->json(['message'=>"Successfully deleted pincode!",'status'=>1]);
    }
    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = Pincode::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the Pincode status!",'status'=>1]);

    }
    public function export()
    {
        return Excel::download(new PincodeExport, 'pincode.xlsx');
    }

    public function exportPdf()
    {
        $list       = Pincode::select('pincodes.*', 'users.name as users_name',DB::raw(" IF(gbs_pincodes.status = 2, 'Inactive', 'Active') as user_status"))->join('users', 'users.id', '=', 'pincodes.added_by')->get();
        $pdf        = PDF::loadView('platform.exports.pincode.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('pincode.pdf');
    }
    public function doBulkUpload(Request $request)
    {
       // dd('ddd');
        //Excel::import( new PincodeImport, request()->file('file') );
        $excel_import= Excel::import(new PincodeImport, $request->pincode_file);        
        if($excel_import)
        {
            return back()->with('success','Excel Uploaded successfully!');
        }
        else
        {
            return back()->with('error','Excel Not Uploaded. Please try again later !');
        }    
        //return response()->json(['error'=> 0, 'message' => 'Imported successfully']);
    }
}
