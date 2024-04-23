<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Exports\CountryExport;
use App\Models\Master\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Auth;
use Excel;
use PDF;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $title = "Country";
        if ($request->ajax()) {
            $data = Country::select('countries.*', DB::raw(" IF(status = 2, 'Inactive', 'Active') as user_status"));
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('status', 'like', "%{$status}%");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('name', 'like', "%{$keywords}%")->orWhere('nice_name', 'like', "%{$keywords}%")->orWhere('iso', 'like', "%{$keywords}%")->orWhere('iso3', 'like', "%{$keywords}%")->orWhere('phone_code', 'like', "%{$keywords}%")->orWhere('num_code', 'like', "%{$keywords}%")->orWhere('phone_code', 'like', "%{$keywords}%")->orWhereDate("created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->editColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-success" tooltip="Click to Inactive" onclick="return commonChangeStatus(' . $row->id . ', 2, \'country\')">Active</a>';
                    } else {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-danger" tooltip="Click to Active" onclick="return commonChangeStatus(' . $row->id . ', 1, \'country\')">Inactive</a>';
                    }
                    return $status;
                })

                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'country\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'country\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'image']);
            return $datatables->make(true);
        }
        $breadCrum = array('Masters', 'Countries');
        $title      = 'Countries';
        return view('platform.master.country.index', compact('breadCrum', 'title'));
    }
    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Country';
        if (isset($id) && !empty($id)) {
            $info           = Country::find($id);
            $modal_title    = 'Update Country';
        }
        return view('platform.master.country.add_edit_modal', compact('info', 'modal_title'));
    }
    public function saveForm(Request $request,$id = null)
    {
        // dd($request->all());
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'name' => 'required|string|unique:countries,name,' . $id . ',id,deleted_at,NULL',
                                'iso' => 'required|max:3|unique:countries,iso,' . $id . ',id,deleted_at,NULL',
                                'phone_code' => 'required|digits:2|unique:countries,phone_code,' . $id . ',id,deleted_at,NULL',
                                // 'iso3' => 'unique:countries,iso3,' . $id . ',id,deleted_at,NULL',
                                // 'num_code' => 'unique:countries,num_code,' . $id . ',id,deleted_at,NULL',
                            ]);

        if ($validator->passes()) {
           
            $ins['name']                        = $request->name;
            $ins['nice_name']                   = strtolower($request->name);
            $ins['iso']                         = $request->iso;
            $ins['iso3']                        = $request->iso3;
            $ins['num_code']                    = $request->num_code;
            $ins['phone_code']                  = $request->phone_code;
            $ins['added_by']        = Auth::id();
            if($request->status == "1")
            {
                $ins['status']          = 1;
            }
            else{
                $ins['status']          = 2;
            }
            $error                  = 0;

            $info                   = Country::updateOrCreate(['id' => $id], $ins);
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
        $info       = Country::find($id);
        $info->delete();
        // echo 1;
        return response()->json(['message'=>"Successfully deleted Country!",'status'=>1]);
    }
    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = Country::find($id);
        $info->status   = $status;
        $info->update();
        // echo 1;
        return response()->json(['message'=>"You changed the Country status!",'status'=>1]);

    }
    public function export()
    {
        return Excel::download(new CountryExport, 'country.xlsx');
    }

    public function exportPdf()
    {
        // $list       = OrderStatus::select('status_name', 'added_by', 'description', 'order', DB::raw(" IF(status = 2, 'Inactive', 'Active') as user_status"))->get();
        $list       = Country::select('countries.*', DB::raw(" IF(status = 2, 'Inactive', 'Active') as user_status"))->get();
        $pdf        = PDF::loadView('platform.exports.country.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('country.pdf');
    }
}
