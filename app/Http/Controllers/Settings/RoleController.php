<?php

namespace App\Http\Controllers\Settings;

use App\Exports\RoleExport;
use App\Http\Controllers\Controller;
use App\Models\Settings\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Auth;
use Excel;
use Illuminate\Support\Facades\Route;
use PDF;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $title = "Roles";
        if ($request->ajax()) {
            $data = Role::select('*', DB::raw(" IF(status = 2, 'Inactive', 'Active') as role_status"));
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('status', 'like', "%{$status}%");
                    }
                    if ($keywords) {
                        return $query->where('name', 'like', "%{$keywords}%");
                    }
                })
                ->addIndexColumn()
                ->editColumn('status', function($row){
                    if( $row->status == 1){
                        $status = '<a href="javascript:void(0);" class="badge badge-light-success" tooltip="Click to Inactive" onclick="return commonChangeStatus('.$row->id.', 2, \'roles\')">Active</a>';
                    } else {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-danger" tooltip="Click to Active" onclick="return commonChangeStatus('.$row->id.', 1, \'roles\')">Inactive</a>';
                    }
                    return $status;
                })
                ->addColumn('action', function($row){
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'roles\','.$row->id.')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                        <i class="fa fa-edit"></i>
                    </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete('.$row->id.', \'roles\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                    <i class="fa fa-trash"></i></a>';
                    // $edit_btn = '<a href="javascript:void(0);" class="action-icon" onclick="return add_modal('.$row->id.')"> <i class="mdi mdi-square-edit-outline"></i></a>';
                    // $del_btn = '<a href="javascript:void(0);" class="action-icon" onclick="return delete_institute('.$row->id.')"> <i class="mdi mdi-delete"></i></a>';
                    return $edit_btn.$del_btn;
                })
               
                ->rawColumns(['action', 'status']);
                
             
                return $datatables->make(true);
        }

        return view('platform.settings.roles.index', compact('title'));
    }

    public function list(Request $request)
    {
        $data = Role::all();
        return view('platform.settings.roles.list', compact('data'));
    }

    public function modalAddEdit(Request $request)    {
        $id = $request->id;
        $info = '';
        $modal_title = 'Add Role and Permission';
        if( isset( $id ) && !empty( $id ) ) {
            $info = Role::find( $id );
            $modal_title = 'Update Role and Permission';

        }
        return view('platform.settings.roles.add_edit_modal', compact('info', 'modal_title'));
    }

    public function saveForm(Request $request)
    {
        $id = $request->id;
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|unique:roles,name,'.$id,
        ]);

        if ($validator->passes()) {

            $perm = [];
            if( config('constant.permission') ) {
                foreach (config('constant.permission') as $item) {
                    $perm[$item] = array(
                        $item.'_visible' => $_POST[$item.'_visible'] ?? 'off',
                        $item.'_editable' => $_POST[$item.'_editable'] ?? 'off',
                        $item.'_delete' => $_POST[$item.'_delete'] ?? 'off',
                        $item.'_status' => $_POST[$item.'_status'] ?? 'off',
                        $item.'_export' => $_POST[$item.'_export'] ?? 'off',
                    );
                }
            }
            
            $ins['name'] = $request->role_name;
            $ins['description'] = $request->description;
            $ins['permissions'] = serialize($perm);
            $ins['added_by'] = Auth::id();
            $ins['status'] = 1;
            $error = 0;
            $info = Role::updateOrCreate(['id' => $id],$ins);
            $message = (isset($id) && !empty($id)) ? 'Updated Successfully' :'Added successfully';
        } else {
            $error = 1;
            $message = $validator->errors()->all();
        }
        return response()->json(['error'=> $error, 'message' => $message]);
    }

    public function delete(Request $request) {
        $id = $request->id;
        $info = Role::find($id);
        $info->delete();
        return response()->json(['message'=>"Successfully deleted role!",'status'=>1]);

    }

    public function changeStatus(Request $request) {
        $id = $request->id;
        $status = $request->status;
        $info = Role::find($id);
        $info->status = $status;
        $info->update();
        return response()->json(['message'=>"You changed the role status!",'status'=>1]);

        
    }

    public function export() 
    {
        return Excel::download(new RoleExport, 'roles.xlsx');
    }

    public function exportPdf() {
        $list = Role::select('*', DB::raw("IF(status = 2, 'Inactive', 'Active') as role_status"))->get();
        
        $pdf = PDF::loadView('platform.exports.roles.excel', array('list' => $list, 'from' => 'pdf') )->setPaper('a4', 'landscape');;
        return $pdf->download('roles.pdf');
    }

}
