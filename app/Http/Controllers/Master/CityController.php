<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\CityExport;
use App\Models\Master\Country;
use App\Models\Master\Pincode;
use App\Models\Master\State;
use App\Models\Master\City;
use Illuminate\Support\Facades\DB;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Auth;
use Excel;
use PDF;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $title = "City";
        if ($request->ajax()) {
           $pincode = Pincode :: where('status',1)->get();
            $data = City::select('cities.*', 'countries.name as country_name','states.state_name as state_name','pincodes.pincode as pincode','users.name as users_name',DB::raw(" IF(gbs_cities.status = 2, 'Inactive', 'Active') as user_status"))->join('pincodes', 'pincodes.id', '=', 'cities.pincode_id')->join('states', 'states.id', '=', 'cities.state_id')->join('countries', 'countries.id', '=', 'cities.country_id')->join('users', 'users.id', '=', 'cities.added_by');
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('cities.status', '=', "$status");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('countries.name', 'like', "%{$keywords}%")->orWhere('states.state_name', 'like', "%{$keywords}%")
                        ->orWhere('cities.description', 'like', "%{$keywords}%")->orWhere('cities.city', 'like', "%{$keywords}%")
                        ->orWhere('pincodes.pincode', 'like', "%{$keywords}%")
                        ->orWhere('users.name', 'like', "%{$keywords}%")->orWhereDate("cities.created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->editColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-success" tooltip="Click to Inactive" onclick="return commonChangeStatus(' . $row->id . ', 2, \'city\')">Active</a>';
                    } else {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-danger" tooltip="Click to Active" onclick="return commonChangeStatus(' . $row->id . ', 1, \'city\')">Inactive</a>';
                    }
                    return $status;
                })

                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'city\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'city\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'image']);
            return $datatables->make(true);
        }
        $breadCrum  = array('Masters', 'Cities');
        $title      = 'Cities';
        return view('platform.master.city.index', compact('breadCrum', 'title'));
    }

    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add City';
        $country = Country::where('status',1)->get();
        $state = State::where('status',1)->get();
        $pincode = Pincode::where('status',1)->get();
        if (isset($id) && !empty($id)) {
            $info           = City::find($id);
            $modal_title    = 'Update City';
        }
        return view('platform.master.city.add_edit_modal', compact('info', 'modal_title','country','state','pincode'));
    }
    public function saveForm(Request $request,$id = null)
    {
        // dd($request->all());
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'city' => 'required|string|unique:cities,city,' . $id . ',id,deleted_at,NULL',
                                'country_id' => 'required',
                                'state_id' => 'required',
                                'pincode_id' => 'required',
                            ]);

        if ($validator->passes()) {
           
            $ins['city']                        = $request->city;
            $ins['country_id']                  = $request->country_id;
            $ins['state_id']                    = $request->state_id;
            $ins['pincode_id']                  = $request->pincode_id;
            $ins['description']                 = $request->description;
          
            $ins['added_by']        = Auth::id();
            if($request->status == "1")
            {
                $ins['status']          = 1;
            }
            else{
                $ins['status']          = 2;
            }
            $error                  = 0;

            $info                   = City::updateOrCreate(['id' => $id], $ins);
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
        $info       = City::find($id);
        $info->delete();
        // echo 1;
        return response()->json(['message'=>"Successfully deleted city!",'status'=>1]);
    }
    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = City::find($id);
        $info->status   = $status;
        $info->update();
        // echo 1;
        return response()->json(['message'=>"You changed the city status!",'status'=>1]);

    }
    public function export()
    {
        return Excel::download(new CityExport, 'city.xlsx');
    }

    public function exportPdf()
    {
        // $list       = OrderStatus::select('status_name', 'added_by', 'description', 'order', DB::raw(" IF(status = 2, 'Inactive', 'Active') as user_status"))->get();
        $list       = City::select('cities.*', 'countries.name as country_name','states.state_name as state_name','pincodes.pincode as pincode','users.name as users_name',DB::raw(" IF(gbs_cities.status = 2, 'Inactive', 'Active') as user_status"))->join('pincodes', 'pincodes.id', '=', 'cities.pincode_id')->join('states', 'states.id', '=', 'cities.state_id')->join('countries', 'countries.id', '=', 'cities.country_id')->join('users', 'users.id', '=', 'cities.added_by')->get();
        $pdf        = PDF::loadView('platform.exports.city.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');
        return $pdf->download('city.pdf');
    }
}
