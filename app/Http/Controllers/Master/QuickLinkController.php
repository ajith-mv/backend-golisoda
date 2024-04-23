<?php

namespace App\Http\Controllers\Master;
use App\Exports\QuickLinkExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuickLink;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Excel;
class QuickLinkController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data       = QuickLink::select('quick_links.*');
            $status     = $request->get('status');
            $keywords   = $request->get('search')['value'];

            $datatables =  DataTables::of($data)
                ->filter(function ($query) use ($keywords,$status) {
                    if ($status) {
                        return $query->where('quick_links.status', '=', "$status");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('name', 'like', "%{$keywords}%")->orWhere('url', 'like', "%{$keywords}%")->orWhereDate("created_at", $date);
                    }
                })
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ',\''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'quick-link\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })
    
                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'quick-link\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'quick-link\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';
              
                    return $edit_btn.$del_btn;
                })
                ->rawColumns(['action', 'status', 'created_at']);
            return $datatables->make(true);
        }
        $title                  = "Quick Link";
        $breadCrum              = array('Master','Quick Link');
        return view('platform.quick_link.index', compact('title', 'breadCrum'));
    }
    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $from               = $request->from;
        $info               = '';
        $modal_title        = 'Add Quick Link';
        if (isset($id) && !empty($id)) {
            $info           = QuickLink::find($id);
            $modal_title    = 'Update Quick Link';
        }
        return view('platform.quick_link.add_edit_modal', compact('info', 'modal_title', 'from'));
    }
    public function saveForm(Request $request,$id = null)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'name' => 'required|string|unique:quick_links,name,' . $id . ',id,deleted_at,NULL',
                                'url' => 'required|url',
                                'order_by' => 'unique:quick_links,order_by,'.$id.',id,deleted_at,NULL'
                            ]);
        $banner_id      = '';

        if ($validator->passes()) {
        
 
            $ins['name']               = $request->name;
            $ins['url']         = $request->url;
            $ins['order_by']            = $request->order_by ?? 0;
            $ins['added_by']            = auth()->user()->id;

            if($request->status == "1")
            {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            
            $error                      = 0;
            $info                       = QuickLink::updateOrCreate(['id' => $id], $ins);
        
            $message                    = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } else {
            $error                      = 1;
            $message                    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);
    }
    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = QuickLink::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the Banner status!",'status'=>1]);

    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = QuickLink::find($id);
        $info->delete();
        return response()->json(['message'=>"Successfully deleted Banner!",'status'=>1]);
    }

    public function export()
    {

        return Excel::download(new QuickLinkExport, 'banner.xlsx');
    }
}
