<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Exports\VariationGroupExport;
use App\Models\Master\VariationGroup;
use App\Models\Master\Variation;
use App\Models\Product\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Support\Facades\File;
use Auth;
use Excel;
use PDF;
use Illuminate\Support\Facades\Validator;

class VariationGroupController extends Controller
{
    public function index(Request $request)
    {
        $title = "Variation Group";
        if ($request->ajax()) {
            $data = VariationGroup::select('variation_groups.*', DB::raw(" IF(status = 2, 'Inactive', 'Active') as user_status"));
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('status', 'like', "%{$status}%");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('title', 'like', "%{$keywords}%");
                    }
                })
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-success" tooltip="Click to Inactive" onclick="return commonChangeStatus(' . $row->id . ', 2, \'variation-group\')">Active</a>';
                    } else {
                        $status = '<a href="javascript:void(0);" class="badge badge-light-danger" tooltip="Click to Active" onclick="return commonChangeStatus(' . $row->id . ', 1, \'variation-group\')">Inactive</a>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'variation-group\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'variation-group\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'image']);
            return $datatables->make(true);
        }
        $breadCrum = array('Masters', 'Variation Groups');
        $title      = 'Variation Groups';
        return view('platform.master.variation-group.index', compact('breadCrum', 'title'));
    }
    public function modalAddEdit(Request $request)
    {
        $id = $request->id;
        $info = '';
        $modal_title = 'Add Variation Group';
        $variations = Variation::where('status', '1')->get();
        $categories = ProductCategory::where('status', 'published')->get();
        $selectedVariationIds = [];
        $selectedCategoryIds = [];
    
        if (isset($id) && !empty($id)) {
            $info = VariationGroup::find($id);
            $modal_title = 'Update Variation Group';
            $variations = Variation::where('status', '1')->get();
            if (!empty($info->variation_id)) {
                $selectedVariationIds = json_decode($info->variation_id, true);
                $selectedCategoryIds = json_decode($info->category_id, true);
              
            }
        }
    
        return view('platform.master.variation-group.add_edit_modal', compact('info', 'modal_title', 'variations','categories', 'selectedVariationIds','selectedCategoryIds'));
    }
    
    public function saveForm(Request $request, $id = null)
    {
        $id= $request->id;
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|unique:variation_groups,title,' . $id . ',id,deleted_at,NULL',
            'collection_variation' => 'required|array',
            'sort' => 'required',
            'collection_category' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => 1, 'message' => $validator->errors()], 422);
        }
 
        $ins['title'] = $request->title;
        $ins['variation_id'] = json_encode($request->collection_variation);
        $ins['sort'] = $request->sort;
        $ins['category_id'] = json_encode($request->collection_category);
        $ins['added_by'] = Auth::id();
        $ins['status'] = $request->status == "1" ? 1 : 2;
    
        try {
            $info = VariationGroup::updateOrCreate(['id' => $id], $ins);
            $message = isset($id) && !empty($id) ? 'Updated Successfully' : 'Added successfully';
            return response()->json(['error' => 0, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['error' => 1, 'message' => $e->getMessage()], 500);

        }
    }
    public function delete(Request $request)
    {
        $id= $request->id;
        $info= VariationGroup::find($id);
        $info->delete();
        return response()->json(['message'=>"Successfully deleted Variation Group!",'status'=>1]);
    }
    public function changeStatus(Request $request)
    {
        $id= $request->id;
        $status= $request->status;
        $info= VariationGroup::find($id);
        $info->status= $status;
        $info->update();
        return response()->json(['message'=>"You changed the Variation Group status!",'status'=>1]);
    }
    public function export()
    {
        return Excel::download(new VariationGroupExport, 'variation-group.xlsx');
    }
}
