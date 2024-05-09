<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Exports\VariationExport;
use App\Models\Master\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Support\Facades\File;
use Auth;
use Excel;
use PDF;
use Illuminate\Support\Facades\Validator;

class VariationController extends Controller
{
    public function index(Request $request)
    {
        $title = "Variation";
        if ($request->ajax()) {
            $data = Variation::select('variations.*', DB::raw(" IF(status = 2, 'Inactive', 'Active') as user_status"));
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('status', 'like', "%{$status}%");
                    }
                    if ($keywords) {
                    return $query->where('title', 'like', "%{$keywords}%")->orWhere('tag_line', 'like', "%{$keywords}%");
                    }
                })
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-success" tooltip="Click to Inactive" onclick="return commonChangeStatus(' . $row->id . ', 2, \'variation\')">Active</a>';
                    } else {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-danger" tooltip="Click to Active" onclick="return commonChangeStatus(' . $row->id . ', 1, \'variation\')">Inactive</a>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'variation\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'variation\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'image']);
            return $datatables->make(true);
        }
        $breadCrum = array('Masters', 'Variations');
        $title      = 'Variations';
        return view('platform.master.variation.index', compact('breadCrum', 'title'));
    }
    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Variation';
        if (isset($id) && !empty($id)) {
            $info           = Variation::find($id);
            $modal_title    = 'Update Variation';
        }
        return view('platform.master.variation.add_edit_modal', compact('info', 'modal_title'));
    }
    public function saveForm(Request $request,$id = null)
    {
        $id= $request->id;
        $validator= Validator::make($request->all(), [
                                'title' => 'required|string|unique:variations,title,' . $id . ',id,deleted_at,NULL',
                                'tag_line' => 'required|string|unique:variations,tag_line,' . $id . ',id,deleted_at,NULL',
                                'sort' => 'required',
                                'kt_docs_repeater_nested_outer.*.value' => 'required',
                            ],[
                                'kt_docs_repeater_nested_outer.*.value.required' => 'Value field is required',
                            ]);

        if ($validator->passes()) {
            $ins['title'] = $request->title;
            $ins['value'] = json_encode($request->kt_docs_repeater_nested_outer);
            $ins['tag_line']                        = $request->tag_line;
            $ins['sort']                    = $request->sort;
            $ins['added_by']        = Auth::id();
            if($request->status == "1")
            {
                $ins['status']= 1;
            }
            else{
                $ins['status'] = 2;
            }
            $error                  = 0;
            $info                   = Variation::updateOrCreate(['id' => $id], $ins);
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
        $id= $request->id;
        $info= Variation::find($id);
        $info->delete();
        return response()->json(['message'=>"Successfully deleted Variation!",'status'=>1]);
    }
    public function changeStatus(Request $request)
    {
        $id= $request->id;
        $status= $request->status;
        $info= Variation::find($id);
        $info->status= $status;
        $info->update();
        return response()->json(['message'=>"You changed the Variation status!",'status'=>1]);
    }
    public function export()
    {
        return Excel::download(new VariationExport, 'variation.xlsx');
    }
}
