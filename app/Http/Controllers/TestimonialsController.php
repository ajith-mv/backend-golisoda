<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\TestimonialsExport;
use App\Models\Testimonials;
use Illuminate\Support\Facades\DB;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Auth;
use Excel;
use PDF;

class TestimonialsController extends Controller
{
    public function index(Request $request)
    {
        $title = "Testimonials";
        if ($request->ajax()) {
            $data               = Testimonials::select('testimonials.*','users.name as users_name')->join('users', 'users.id', '=', 'testimonials.added_by');
            $status             = $request->get('status');
            $keywords           = $request->get('search')['value'];
            $datatables         =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('testimonials.status', '=', "$status");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('testimonials.title', 'like', "%{$keywords}%")->orWhere('users.name', 'like', "%{$keywords}%")->orWhere('testimonials.short_description', 'like', "%{$keywords}%")->orWhere('testimonials.long_description', 'like', "%{$keywords}%")->orWhere('testimonials.status', 'like', "%{$keywords}%")->orWhere("testimonials.order_by",'like', "%{$keywords}%")->orWhereDate("testimonials.created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ', \''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'testimonials\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->editColumn('image', function ($row) {
                    if ($row->image) {

                        $path = asset($row->image);
                        $image = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    } else {
                        $path = asset('userImage/no_Image.jpg');
                        $image = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    }
                    return $image;
                })
                
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'testimonials\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'testimonials\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'image']);
            return $datatables->make(true);
        }
        $breadCrum = array('Testimonials');
        $title      = 'Testimonials';
        return view('platform.testimonials.index', compact('breadCrum', 'title'));
    }
    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Testimonials';
        if (isset($id) && !empty($id)) {
            $info           = Testimonials::find($id);
            $modal_title    = 'Update Testimonials';
        }
        
        return view('platform.testimonials.add_edit_modal', compact('info', 'modal_title'));
    }
    public function saveForm(Request $request,$id = null)
    {
        $id                         = $request->id;
        $validator                  = Validator::make($request->all(), [
                                        'title' => 'required|string|unique:testimonials,title,' . $id . ',id,deleted_at,NULL',
                                        'avatar' => 'mimes:jpeg,png,jpg',
                                        'short_description' => 'max:250',
                                        'order_by' => 'required|unique:testimonials,order_by,' . $id . ',id,deleted_at,NULL'
                                    ]);

        if ($validator->passes()) {
            
            if ($request->file('avatar')) {

                $filename           = time() . '_' . $request->avatar->getClientOriginalName();
                $folder_name        = 'testimonial/' . str_replace(' ', '', $request->title) . '/';
                $existID            = '';
                if($id)
                {
                    $existID        = Testimonials::find($id);
                    $deleted_file   = $existID->image;
                    if(File::exists($deleted_file)) {
                        File::delete($deleted_file);
                    }
                }
               
                $path               = $folder_name . $filename;
                $request->avatar->move(public_path($folder_name), $filename);
                $ins['image']       = $path;
            }
            
            if ($request->image_remove_logo == "yes") {
                $ins['image']           = '';
            }

            $ins['title']               = $request->title;
            $ins['short_description']   = $request->short_description;
            $ins['long_description']    = $request->long_description;
            $ins['order_by']            = $request->order_by;
            $ins['added_by']            = Auth::id();
            if($request->status == "1")
            {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            $error                  = 0;

            $info                   = Testimonials::updateOrCreate(['id' => $id], $ins);
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
        $info       = Testimonials::find($id);
        $info->delete();
        return response()->json(['message'=>"Successfully deleted testimonials!",'status'=>1]);
    }
    public function changeStatus(Request $request)
    {
        
        $id             = $request->id;
        $status         = $request->status;
        $info           = Testimonials::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the testimonials status!",'status'=>1]);

    }
    public function export()
    {
        return Excel::download(new TestimonialsExport, 'testimonials.xlsx');
    }
    public function exportPdf()
    {
        $list       = Testimonials::select('testimonials.*','users.name as users_name',DB::raw(" IF(gbs_testimonials.status = 2, 'Inactive', 'Active') as user_status"))->join('users', 'users.id', '=', 'testimonials.added_by')->get();
        $pdf        = PDF::loadView('platform.exports.testimonials.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('testimonial.pdf');
    }
}
