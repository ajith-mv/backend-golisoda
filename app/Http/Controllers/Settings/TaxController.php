<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\TaxExport;
use App\Models\Settings\Tax;
use DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Auth;
use Excel;
use Illuminate\Support\Facades\Storage;
use PDF;
class TaxController extends Controller
{
    public function index(Request $request)
    {
        $title                  = "Tax";
        $breadCrum              = array('Tax', 'Tax');
        if ($request->ajax()) {
            $data               = Tax::select('taxes.*');
            $status             = $request->get('status');
            $keywords           = $request->get('search')['value'];
            $datatables         =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('taxes.status',$status);
                    }
                    if ($keywords) {
                        
                        if( !strpos($keywords, '.')) {
                            $date = date('Y-m-d', strtotime($keywords));
                        } 
                        $query->where('taxes.title', 'like', "%{$keywords}%")->orWhere('taxes.pecentage', 'like', "%{$keywords}%");
                        if( isset( $date )) {
                            $query->orWhereDate("taxes.created_at", $date);
                        }
                        
                        return $query;
                    }
                })
                ->addIndexColumn()
               
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ', \''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'tax\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                
                
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'tax\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'tax\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status']);
            return $datatables->make(true);
        }
        return view('platform.tax.index',compact('title','breadCrum'));
    }
    public function modalAddEdit(Request $request)
    {
        $title              = "Add Tax";
        $breadCrum          = array('Products', 'Add Tax');

        $id                 = $request->id;
        $from               = $request->from;
        $info               = '';
        $modal_title        = 'Add Tax';
        if (isset($id) && !empty($id)) {
            $info           = Tax::find($id);
            $modal_title    = 'Update Tax';
        }
        return view('platform.tax.add_edit_modal', compact('modal_title', 'breadCrum', 'info', 'from'));
    }
   
    public function saveForm(Request $request,$id = null)
    {
        $id                         = $request->id;
        $validator                  = Validator::make($request->all(), [
                                        'title' => 'required|string|unique:taxes,title,' . $id . ',id,deleted_at,NULL',
                                        'pecentage' => 'required',
                                        
                                    ]);

        if ($validator->passes()) {
            
            $ins['title']           = $request->title;
            $ins['pecentage']       = $request->pecentage;
            $ins['order_by']        = $request->order_by ?? 1;
            if($request->status == "1")
            {
                $ins['status']      = 'published';
            } else {
                $ins['status']      = 'unpublished';
            }
            $error                  = 0;

            $info                   = Tax::updateOrCreate(['id' => $id], $ins);
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
        $info       = Tax::find($id);
        $info->delete();
        return response()->json(['message'=>"Successfully deleted state!",'status'=>1]);
    }
    
    public function changeStatus(Request $request)
    {
        
        $id             = $request->id;
        $status         = $request->status;
        $info           = Tax::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the status!",'status'=>1]);

    }
    public function export()
    {
        return Excel::download(new TaxExport, 'tax.xlsx');
    }
    public function exportPdf()
    {
        $list       = Tax::select('taxes.*')->get();
        $pdf        = PDF::loadView('platform.exports.tax.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('tax.pdf');
    }
}
