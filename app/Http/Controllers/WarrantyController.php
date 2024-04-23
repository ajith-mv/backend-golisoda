<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DataTables;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Excel;
use App\Exports\WarrantyExport;

class WarrantyController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data       = Warranty::select('warranties.*');
           
            $status     = $request->get('status');
            $keywords   = $request->get('search')['value'];

            $datatables =  DataTables::of($data)
                ->filter(function ($query) use ($keywords,$status) {
                    if ($status) {
                        return $query->where('warranties.status', '=', "$status");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('warranties.name', 'like', "%{$keywords}%")
                        ->orWhere("warranties.warranty_period", 'like', "%{$keywords}%")
                        ->orWhere("warranties.warranty_period_type", 'like', "%{$keywords}%")
                        ->orWhereDate("warranties.created_at", $date);
                    }
                })
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ',\''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'warranty\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })
    
                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'warranty\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'warranty\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';
              
                    return $edit_btn.$del_btn;
                })
                ->rawColumns(['action', 'status', 'created_at']);
            return $datatables->make(true);
        }
        $title                  = "Warranty";
        $breadCrum              = array('Warranty');
        return view('platform.warranty.index', compact('title', 'breadCrum'));
    }
    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $from               = $request->from;
        $info               = '';
        $modal_title        = 'Add Warranty';

        if (isset($id) && !empty($id)) {
            $info           = Warranty::find($id);
           
            $modal_title    = 'Update Warranty';
        }

        return view('platform.warranty.add_edit_modal', compact('info', 'modal_title', 'from'));
    }
    public function saveForm(Request $request,$id = null)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'name' => 'required|string|unique:warranties,name,' . $id . ',id,deleted_at,NULL',
                                'warranty_period_type' => 'required',
                                'warranty_period' => 'required',
                            ]);
        if ($validator->passes()) {
        
 
            $ins['name']                           = $request->name;
            $ins['warranty_period']                 = $request->warranty_period;
            $ins['warranty_period_type']            = $request->warranty_period_type;
            $ins['description']                     = $request->description;
            $ins['order_by']                        = $request->order_by ?? 0;
            $ins['added_by']                        = auth()->user()->id;

            if($request->status == "1")
            {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            
            $error                      = 0;
            $info                       = Warranty::updateOrCreate(['id' => $id], $ins);
        
            $message                    = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
            
        } else {
            $error                      = 1;
            $message                    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message, 'id' => $id ?? '']);
    }
    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = Warranty::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the Warranty status!",'status'=>1]);

    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = Warranty::find($id);
        $info->delete();
        
        return response()->json(['message'=>"Successfully deleted Warranty!",'status'=>1]);
    }
    public function export()
    {
        return Excel::download(new WarrantyExport, 'Warranty.xlsx');
    }
}
