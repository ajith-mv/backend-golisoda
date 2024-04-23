<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\StateExport;
use App\Models\Master\Country;
use App\Models\Master\State;
use Illuminate\Support\Facades\DB;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Auth;
use Excel;
use PDF;
use PHPUnit\Framework\Constraint\Count;

class StateController extends Controller
{
    public function index(Request $request)
    {
        $title = "State";
        if ($request->ajax()) {
            $data =State::select('states.*', 'countries.name as country_name','users.name as users_name',DB::raw(" IF(gbs_states.status = 2, 'Inactive', 'Active') as user_status"))->join('countries', 'countries.id', '=', 'states.country_id')->join('users', 'users.id', '=', 'states.added_by');
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('states.status', 'like', "%{$status}%");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('states.state_name', 'like', "%{$keywords}%")->orWhere('states.state_code', 'like', "%{$keywords}%")->orWhere('users.name', 'like', "%{$keywords}%")->orWhere('countries.name', 'like', "%{$keywords}%")->orWhereDate("states.created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->editColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-success" tooltip="Click to Inactive" onclick="return commonChangeStatus(' . $row->id . ', 2, \'state\')">Active</a>';
                    } else {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-danger" tooltip="Click to Active" onclick="return commonChangeStatus(' . $row->id . ', 1, \'state\')">Inactive</a>';
                    }
                    return $status;
                })

                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'state\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'state\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'image']);
            return $datatables->make(true);
        }
        $breadCrum = array('Masters', 'States');
        $title      = 'States';
        return view('platform.master.state.index', compact('breadCrum', 'title'));
    }
    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add State';
       $country = Country::where('status',1)->get();
        if (isset($id) && !empty($id)) {
            $info           = State::find($id);
            $modal_title    = 'Update State';
        }
        
        return view('platform.master.state.add_edit_modal', compact('info', 'modal_title','country'));
    }
    public function saveForm(Request $request,$id = null)
    {
        // dd($request->all());
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'state_name' => 'required|string|unique:states,state_name,' . $id . ',id,deleted_at,NULL',
                                'state_code' => 'required|unique:states,state_code,' . $id . ',id,deleted_at,NULL',
                                'country_id' => 'required',
                                // 'iso3' => 'unique:countries,iso3,' . $id . ',id,deleted_at,NULL',
                                // 'num_code' => 'unique:countries,num_code,' . $id . ',id,deleted_at,NULL',
                            ]);

        if ($validator->passes()) {
           
            $ins['state_name']                        = $request->state_name;
            $ins['state_code']                   = $request->state_code;
            $ins['country_id']                         = $request->country_id;
            $ins['added_by']        = Auth::id();
            if($request->status == "1")
            {
                $ins['status']          = 1;
            }
            else{
                $ins['status']          = 2;
            }
            $error                  = 0;

            $info                   = State::updateOrCreate(['id' => $id], $ins);
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
        $info       = State::find($id);
        $info->delete();
        // echo 1;
        return response()->json(['message'=>"Successfully deleted state!",'status'=>1]);
    }
    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = State::find($id);
        $info->status   = $status;
        $info->update();
        // echo 1;
        return response()->json(['message'=>"You changed the status!",'status'=>1]);

    }
    public function export()
    {
        return Excel::download(new StateExport, 'state.xlsx');
    }

    public function exportPdf()
    {
        // $list       = OrderStatus::select('status_name', 'added_by', 'description', 'order', DB::raw(" IF(status = 2, 'Inactive', 'Active') as user_status"))->get();
        $list       = State::select('states.*',  'countries.name as country_name','users.name as users_name',DB::raw(" IF(gbs_states.status = 2, 'Inactive', 'Active') as user_status"))->join('countries', 'countries.id', '=', 'states.country_id')->join('users', 'users.id', '=', 'states.added_by')->get();
        $pdf        = PDF::loadView('platform.exports.state.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('state.pdf');
    }
}
