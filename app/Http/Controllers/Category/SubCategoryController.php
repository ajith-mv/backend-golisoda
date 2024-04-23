<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\SubCategoryExport;
use App\Models\Category\MainCategory;
use App\Models\Category\SubCategory;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Psy\Util\Str;
use Auth;
use Excel;
use PDF;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    { 
        $routeName = Route::currentRouteName();
        
        if( $routeName == 'product-tags') {

        } else if( $routeName == 'product-labels') {

        }

        $title = ucwords( str_replace(["-","_"]," ", $routeName));
        $category    = MainCategory::where('status','published')->get();

        if ($request->ajax()) {
            $page_type   = $request->get('page_type') ?? '';
            $data       = SubCategory::select('sub_categories.*','main_categories.category_name as category_name','users.name as users_name')
                                ->join('main_categories', 'sub_categories.parent_id', '=', 'main_categories.id')
                                ->join('users', 'users.id', '=', 'sub_categories.added_by')
                                ->when($page_type != 'sub_category', function ($q) use ($page_type) {
                                    return $q->where('main_categories.slug', $page_type);
                                });
            
            $filter_category  ='';
            $status     = $request->get('status');
            $keywords   = $request->get('search')['value'];
            $filter_category   = $request->get('filter_category');
            
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status, $filter_category, $page_type) {
                   
                    if ($status) {
                        return $query->where('sub_categories.status', '=', "$status");
                    }
                    if ($filter_category) {
                        return $query->where('main_categories.category_name', '=', "$filter_category");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('sub_categories.name', 'like', "%{$keywords}%")->orWhere('users.name', 'like', "%{$keywords}%")->orWhere('main_categories.category_name', 'like', "%{$keywords}%")->orWhere('sub_categories.slug', 'like', "%{$keywords}%")->orWhereDate("sub_categories.created_at", $date);
                    }
                  
                })
                ->addIndexColumn()

                ->editColumn('image', function ($row) {
                    if ($row->image) {

                        $path   = asset($row->image);
                        $image  = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    } else {
                        $path   = asset('userImage/no_Image.jpg');
                        $image  = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    }
                    return $image;
                })
                ->editColumn('status', function ($row) use($page_type) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ',\''.(($row->status == 'published') ? 'unpublished': 'published').'\', \''.$page_type.'\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                

                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) use($page_type) {
                    $edit_btn   = '<a href="javascript:void(0);" onclick="return  openForm(\''.$page_type.'\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn    = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \''.$page_type.'\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'image']);
            return $datatables->make(true);
        }
        $breadCrum  = array('Masters', $title);
        $showFilterCategory         = false;
        if( $routeName == 'sub_category' ) {
            $showFilterCategory     = true;
        }
        
        return view('platform.category.sub_category.index',compact('category', 'breadCrum', 'title', 'routeName', 'showFilterCategory'));

    }
    public function modalAddEdit(Request $request)
    {
        $routeName          = Route::currentRouteName();
        $routeSegment       = explode('.', $routeName);
        $pageName           = current($routeSegment);
        
        $title              = ucwords( str_replace(["-","_"]," ", $pageName));
        
        $id                 = $request->id;
        $from               = $request->from;
        $dynamicModel       = $request->dynamicModel;
        $info               = '';
        $sub_title          = '';
        $modal_title        = 'Add '.$title;
       

        if( isset( $dynamicModel )&& !empty( $dynamicModel )) {
            $sub_title      = ucwords( str_replace('-', ' ', $dynamicModel));
            $category       = MainCategory::where('status', 'published')->where('slug', $dynamicModel)->first();
            if( isset( $category ) && !empty( $category)) {

            } else {
                //insert new entry in maincategory
                $ins['category_name']   = $sub_title;
                $ins['slug']            = $dynamicModel;
                $ins['order_by']        = 0;
                $ins['added_by']        = Auth::id();
                $ins['status']          = 'published';
                $category               = MainCategory::create($ins);
            }
            $modal_title        = 'Add '.$sub_title;
            
        } else {
            if( $pageName != 'sub_category' ) {

                $sub_title      = $title;
                $category       = MainCategory::where('status', 'published')->where('slug', $pageName)->first();
                if( isset( $category ) && !empty( $category)) {

                } else {
                    //insert new entry in maincategory
                    $ins['category_name']   = $title;
                    $ins['slug']            = $pageName;
                    $ins['order_by']        = 0;
                    $ins['added_by']        = Auth::id();
                    $ins['status']          = 'published';
                    $category               = MainCategory::create($ins);
                }

            } else {

                $category       = MainCategory::where('status', 'published')->get();

            }
            
        }
        if (isset($id) && !empty($id)) {
            $info           = SubCategory::find($id);
            $modal_title    = 'Update '.$info->main->category_name ?? 'Sub Category';
        }
        $params = array(
                    'info' => $info,
                    'modal_title' => $modal_title,
                    'category' => $category,
                    'dynamicModel' => $dynamicModel,
                    'from' => $from,
                    'sub_title' => $sub_title,
                );
        
        return view('platform.category.sub_category.add_edit_modal', $params);
    }

    public function saveForm(Request $request,$id = null)
    {

        $id                     = $request->id;
        $validator              = Validator::make($request->all(), [
                                        'name' => 'required|string|unique:sub_categories,name,' . $id . ',id,deleted_at,NULL',
                                        'category_name' => 'required',
                                        'avatar' => 'mimes:jpeg,png,jpg|max:150',
                                    ]);
        $sub_id                 = '';                    
        if ($validator->passes()) {
            
            if ($request->file('avatar')) {
                $filename       = time() . '_' . $request->avatar->getClientOriginalName();
                $folder_name    = 'categories/sub_category/' . str_replace(' ', '', $request->name) .'/';
                $existID        = '';
                $filename       = str_replace(' ', '', $filename);

                if($id)
                {
                    $existID        = SubCategory::find($id);
                    $deleted_file   = $existID->image;
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
           
            $ins['parent_id']                   = $request->category_name;
            $ins['name']                        = $request->name;
            $ins['slug']                        = \Str::slug($request->name);
            $ins['description']                 = $request->description;
            $ins['tagline']                     = $request->tagline;
            $ins['order_by']                    = $request->order_by ?? 0;
            $ins['added_by']                    = Auth::id();
            if($request->status == "1")
            {
                $ins['status']                  = 'published';
            } else {
                $ins['status']                  = 'unpublished';
            }
            $error                              = 0;
            $info                               = SubCategory::updateOrCreate(['id' => $id], $ins);
            $sub_id                             = $info->id;
            $message                            = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } 
        else {
            $error      = 1;
            $message    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message, 'id' => $sub_id]);
    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = SubCategory::find($id);
        $info->delete();
        return response()->json(['message'=>"Successfully deleted Sub Category!",'status'=>1]);
    }

    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = SubCategory::find($id);
        $info->status   = $status;
        $info->update();
        
        return response()->json(['message'=>"You changed the Sub Category status!",'status'=>1]);

    }

    public function export(Request $request)
    {

        $routeName          = Route::currentRouteName();
        $routeSegment       = explode('.', $routeName);
        $pageName           = current($routeSegment);
        
        return Excel::download(new SubCategoryExport($pageName), $pageName.'.xlsx');

    }

    public function exportPdf()
    {
        
        $routeName          = Route::currentRouteName();
        $routeSegment       = explode('.', $routeName);
        $page_type           = current($routeSegment);

        $list               = SubCategory::select('sub_categories.*','main_categories.category_name as category_name','users.name as users_name')
                                ->join('main_categories', 'sub_categories.parent_id', '=', 'main_categories.id')
                                ->join('users', 'users.id', '=', 'sub_categories.added_by')
                                ->when( $page_type != 'sub_category',  function($q) use($page_type) {
                                    return $q->where('main_categories.slug', $page_type);
                                })
                                ->get();

        $pdf                = PDF::loadView('platform.exports.sub_category.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download($page_type.'.pdf');

    }
}
