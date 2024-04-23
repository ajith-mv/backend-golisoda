<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Master\Country;
use App\Models\Settings\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Hash;
use Auth;
use Excel;
use PDF;
class UserController extends Controller
{
    public function index(Request $request)
    {
        $title = "Users";
        if ($request->ajax()) {
            $data = User::select('users.*', 'roles.name as role_name', DB::raw(" IF(gbs_users.status = 2, 'Inactive', 'Active') as user_status"))->join('roles', 'roles.id', '=', 'users.role_id');
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('users.status', 'like', "%{$status}%");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('users.name', 'like', "%{$keywords}%")->orWhere('users.email', 'like', "%{$keywords}%")->orWhere('users.mobile_no', 'like', "%{$keywords}%")->orWhere('roles.name', 'like', "%{$keywords}%")->orWhereDate("users.created_at", $date);
                    }
                })
                ->addIndexColumn()
                ->editColumn('image', function ($row) {
                    if ($row->image) {

                        $path = asset($row->image);
                        $image = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    } else {
                        $path = asset('userImage/dummy.jpeg');
                        $image = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    }
                    return $image;
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-success" tooltip="Click to Inactive" onclick="return commonChangeStatus(' . $row->id . ', 2, \'users\')">Active</a>';
                    } else {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-danger" tooltip="Click to Active" onclick="return commonChangeStatus(' . $row->id . ', 1, \'users\')">Inactive</a>';
                    }
                    return $status;
                })

                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'users\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'users\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'image']);
            return $datatables->make(true);
        }
        $breadCrum = array('User Management', 'Users');
        $title      = 'Users';
        return view('platform.settings.user.index', compact('breadCrum', 'title'));
    }

    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add User';
        $role               = Role::select('id', 'name')->get();
        $country_code = Country::select('phone_code')->where('status',1)->get();
        if (isset($id) && !empty($id)) {
            $info           = User::find($id);
            $modal_title    = 'Update User';
        }
        return view('platform.settings.user.add_edit_modal', compact('info', 'modal_title', 'role','country_code'));
    }

    public function saveForm(Request $request,$id = null)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'user_name' => 'required|string',
                                'user_email' => 'required|email|unique:users,email,' . $id . ',id',
                                'user_role' => 'required',
                                'mobile_no' => 'required|numeric|digits:10|unique:users,mobile_no,'. $id . ',id'
                            ]);

        if ($validator->passes()) {

            if ($request->hasFile('avatar')) {
                $filename       = time() . '_' . $request->avatar->getClientOriginalName();
                $folder_name    = 'user/' . $request->user_email . '/profile/';
                if( isset( $id ) && !empty( $id ) ) {
                    $existID = User::find($id);
                    $deleted_file = $existID->image;
                    if(File::exists($deleted_file)) {
                        File::delete($deleted_file);
                    }
                } 
                $path           = $folder_name . $filename;
                $request->avatar->move(public_path($folder_name), $filename);
                $ins['image']   = $path;
            }
            if ($request->image_remove == "yes") {
                $ins['image'] = '';
            }
            $ins['name']            = $request->user_name;
            $ins['email']           = $request->user_email;
            $ins['password']        = Hash::make($request->password);
            $ins['mobile_no']       = $request->mobile_no;
            $ins['country_code']    = $request->country_code;
            $ins['address']         = $request->address;
            $ins['role_id']         = $request->user_role;
            $ins['added_by']        = Auth::id();
            $ins['status']          = 1;
            $error                  = 0;

            $info                   = User::updateOrCreate(['id' => $id], $ins);
            $message                = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } else {
            $error      = 1;
            $message    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = User::find($id);
        $info->delete();

        return response()->json(['message'=>"Successfully deleted user!",'status'=>1]);

    }
    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = User::find($id);
        $info->status   = $status;
        $info->update();

        return response()->json(['message'=>"You changed the user status!",'status'=>1]);

    }
    
    public function export()
    {
        return Excel::download(new UserExport, 'users.xlsx');
    }

    public function exportPdf()
    {
        $list       = User::select('users.name', 'users.added_by', 'email', 'mobile_no', 'address', 'users.created_at', 'roles.name as role_name', DB::raw(" IF(gbs_users.status = 2, 'Inactive', 'Active') as user_status"))->join('roles', 'roles.id', '=', 'users.role_id')->where('users.is_super_admin', '!=', 1)->get();
        $pdf        = PDF::loadView('platform.exports.users.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('users.pdf');
    }
}
