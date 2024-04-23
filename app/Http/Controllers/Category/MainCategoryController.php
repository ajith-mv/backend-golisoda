<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\MainCategoryExport;
use App\Models\Category\MainCategory;
use Illuminate\Support\Facades\DB;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Psy\Util\Str;
use Auth;
use Excel;
use PDF;

class MainCategoryController extends Controller
{
    public function index(Request $request)
    { 
        
        if ($request->ajax()) {
            $data = MainCategory::select('main_categories.*','users.name as users_name')->join('users', 'users.id', '=', 'main_categories.added_by');
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('main_categories.status', '=', "$status");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('main_categories.category_name', 'like', "%{$keywords}%")->orWhere('users.name', 'like', "%{$keywords}%")->orWhere('main_categories.description', 'like', "%{$keywords}%")->orWhere('main_categories.slug', 'like', "%{$keywords}%")->orWhereDate("main_categories.created_at", $date);
                    }
                })
                ->addIndexColumn()
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
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ',\''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'main_category\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })

                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'main_category\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'main_category\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'image']);
            return $datatables->make(true);
        }
        $breadCrum  = array('Masters', 'Dynamic Main Category');
        $title      = 'Main Category';
        return view('platform.category.main_category.index', compact('breadCrum', 'title'));

    }
    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Main Category';
        if (isset($id) && !empty($id)) {
            $info           = MainCategory::find($id);
            $modal_title    = 'Update Main Category';
        }
        return view('platform.category.main_category.add_edit_modal', compact('info', 'modal_title'));
    }
    public function saveForm(Request $request,$id = null)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'category_name' => 'required|string|unique:main_categories,category_name,' . $id . ',id,deleted_at,NULL',
                                'avatar' => 'mimes:jpeg,png,jpg',
                            ]);

        if ($validator->passes()) {
            
            if ($request->file('avatar')) {
                $filename       = time() . '_' . $request->avatar->getClientOriginalName();
                $folder_name    = 'categories/main_category/' . str_replace(' ', '', $request->category_name) .'/';
                $existID        = '';
                $filename       = str_replace(' ', '', $filename);
                
                if($id)
                {
                    $existID = MainCategory::find($id);
                    $deleted_file = $existID->image;
                    if(File::exists($deleted_file)) {
                        File::delete($deleted_file);
                    }
                }
                $path           = $folder_name . $filename;
                $request->avatar->move(public_path($folder_name), $filename);
                $ins['image']   = $path;
            }
           
            if ($request->image_remove_image == "yes") {
                $ins['image'] = '';
            }
           
            $ins['category_name']   = $request->category_name;
            $ins['slug']            = \Str::slug($request->category_name);
            $ins['description']     = $request->description;
            $ins['tagline']         = $request->tagline;
            $ins['order_by']        = $request->order_by ?? 0;
            $ins['added_by']        = Auth::id();

            if($request->status == "1")
            {
                $ins['status']      = 'published';
            } else {
                $ins['status']      = 'unpublished';
            }
            $error                  = 0;
            $info                   = MainCategory::updateOrCreate(['id' => $id], $ins);
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
        $info       = MainCategory::find($id);
        $info->delete();
        return response()->json(['message'=>"Successfully deleted Main Category!",'status'=>1]);
    }
    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = MainCategory::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the Main Category status!",'status'=>1]);

    }
    public function export()
    {
        return Excel::download(new MainCategoryExport, 'category.xlsx');
    }

    public function exportPdf()
    {
        $list       = MainCategory::select('main_categories.*','users.name as users_name')->join('users', 'users.id', '=', 'main_categories.added_by')->get();
        $pdf        = PDF::loadView('platform.exports.category.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('category.pdf');
    }
}
