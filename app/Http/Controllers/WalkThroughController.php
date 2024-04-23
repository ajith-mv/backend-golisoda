<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\WalkThroughExport;
use App\Models\WalkThrough;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Auth;
use Excel;
use PDF;

class WalkThroughController extends Controller
{
    public function index(Request $request)
    {
        $title = "Walk Through";
        if ($request->ajax()) {
            $data =WalkThrough::select('walk_throughs.*','users.name as users_name')->join('users', 'users.id', '=', 'walk_throughs.added_by');
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('walk_throughs.status', '=', "$status");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('walk_throughs.title', 'like', "%{$keywords}%")->orWhere('users.name', 'like', "%{$keywords}%")->orWhere('walk_throughs.description', 'like', "%{$keywords}%")->orWhereDate("walk_throughs.created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ', \''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'walkthroughs\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'walkthroughs\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'walkthroughs\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'image']);
            return $datatables->make(true);
        }
        $breadCrum = array('History Videos');
        $title      = 'History Video';
        return view('platform.walk_throughs.index', compact('breadCrum', 'title'));
    }

    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Walk Throughs';
        if (isset($id) && !empty($id)) {
            $info           = WalkThrough::find($id);
            $modal_title    = 'Update Walk Throughs';
        }
        return view('platform.walk_throughs.add_edit_modal', compact('info', 'modal_title'));
    }

    public function saveForm(Request $request,$id = null)
    {
        $id                         = $request->id;
        $validator                  = Validator::make($request->all(), [
                                            'title' => 'required|string|unique:walk_throughs,title,' . $id . ',id,deleted_at,NULL',
                                            'order_by' => 'required|unique:walk_throughs,order_by,' . $id . ',id,deleted_at,NULL'
                                        ]);

        if ($validator->passes()) {

            $ins['title']           = $request->title;
            $ins['video_url']       = $request->video_url;
            $ins['type']            = "video";
            $ins['description']     = $request->description;
            $ins['order_by']        = $request->order_by ?? 1;
            $ins['added_by']        = Auth::id();

            if($request->status == "1")
            {
                $ins['status']      = 'published';
            } else {
                $ins['status']      = 'unpublished';
            }
            
            $error                  = 0;

            $info                   = WalkThrough::updateOrCreate(['id' => $id], $ins);
            $message                = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } 
        else {
            $error                  = 1;
            $message                = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = WalkThrough::find($id);
        $info->delete();
        return response()->json(['message'=>"Successfully deleted state!",'status'=>1]);
    }

    public function changeStatus(Request $request)
    {
        
        $id             = $request->id;
        $status         = $request->status;
        $info           = WalkThrough::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the status!",'status'=>1]);

    }

    public function export()
    {
        return Excel::download(new WalkThroughExport, 'WalkThroughs.xlsx');
    }

    public function exportPdf()
    {
        $list       = WalkThrough::select('walk_throughs.*','users.name as users_name')->join('users', 'users.id', '=', 'walk_throughs.added_by')->get();
        $pdf        = PDF::loadView('platform.exports.walk_throughs.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('WalkThroughs.pdf');
    }
}
