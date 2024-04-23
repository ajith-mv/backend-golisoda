<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Exports\BannerExport;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Carbon\Carbon;
use Excel;
use Illuminate\Support\Facades\Storage;
use PDF;
use Image;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $title = "Banner";
        if ($request->ajax()) {
            $data = Banner::select('banners.*','users.name as users_name')->join('users', 'users.id', '=', 'banners.added_by');
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('banners.status', '=', "$status");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('banners.title', 'like', "%{$keywords}%")
                        ->orWhere('banners.status', 'like', "%{$keywords}%")->orWhere('users.name', 'like', "%{$keywords}%")
                        ->orWhere('banners.description', 'like', "%{$keywords}%")->orWhere('banners.tag_line', 'like', "%{$keywords}%")
                        ->orWhereDate("banners.created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ',\''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'banner\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->editColumn('image', function ($row) {
                    if ($row->banner_image) {
                        
                        $bannerImagePath = 'banner/'.$row->id.'/main_banner/'.$row->banner_image;
                        $url = Storage::url($bannerImagePath);
                        $path = asset($url);
                        $banner_image = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    } else {
                        $path = asset('userImage/no_Image.jpg');
                        $banner_image = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    }
                    return $banner_image;
                })
               
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'banner\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'banner\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'image']);
            return $datatables->make(true);
        }
        $breadCrum  = array( 'Banner');
        $title      = 'Banner';
        return view('platform.banner.index', compact('breadCrum', 'title'));
    }

    public function modalAddEdit(Request $request)
    {

        $id                 = $request->id;
        $from               = $request->from;
        $info               = '';
        $modal_title        = 'Add Banner';
        if (isset($id) && !empty($id)) {
            $info           = Banner::find($id);
            $modal_title    = 'Update Banner';
        }
        
        return view('platform.banner.add_edit_modal', compact('info', 'modal_title', 'from'));
        
    }

    public function saveForm(Request $request,$id = null)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'title' => 'required|string|unique:banners,title,' . $id . ',id,deleted_at,NULL',
                                'avatar' => 'mimes:jpeg,png,jpg|max:1024',
                                'banner' => 'max:1024',
                                // 'avatar' => 'mimes:jpeg,png,jpg|dimensions:min_width=1600,min_height=420',
                                'order_by' => 'required|unique:banners,order_by,'.$id.',id,deleted_at,NULL'
                            ]);
        $banner_id      = '';

        if ($validator->passes()) {
            
            // if ($request->image_remove_image == "yes") {
            //     $ins['banner_image'] = '';
            // }
 
            $ins['title']               = $request->title;
            $ins['description']         = $request->description;
            $ins['tag_line']            = $request->tag_line;
            $ins['links']               = $request->links;
            $ins['order_by']            = $request->order_by ?? 0;
            $ins['added_by']            = auth()->user()->id;

            if($request->status == "1")
            {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            
            $error                      = 0;
            $info                       = Banner::updateOrCreate(['id' => $id], $ins);
            $banner_id                  = $info->id;

            // if ($request->image_remove_image == "yes") {
            //     $directory = 'banner/'.$banner_id;
            //     Storage::deleteDirectory('public/'.$directory); 
            // }
            
            if ($request->hasFile('avatar')) {
                
                $directory = 'banner/'.$banner_id."/main_banner";
                Storage::deleteDirectory('public/'.$directory);

                $file                   = $request->file('avatar');
                $imageName              = uniqid().$file->getClientOriginalName();
                if (!is_dir(storage_path("app/public/banner/".$banner_id."/main_banner"))) {
                    mkdir(storage_path("app/public/banner/".$banner_id."/main_banner"), 0775, true);
                }
               
                $mainBanner            = 'public/banner/'.$banner_id .'/main_banner/' .$imageName;
                Image::make($file)->save(storage_path('app/' . $mainBanner));

                $info->banner_image     = $imageName;
                $info->update();
            }

            if ($request->hasFile('banner')) {
                
                $directory = 'banner/'.$banner_id."/mobile_banner";
                Storage::deleteDirectory('public/'.$directory);

                $file1                   = $request->file('banner');
                $imageName1              = uniqid().$file1->getClientOriginalName();
               
                if (!is_dir(storage_path("app/public/banner/".$banner_id."/mobile_banner"))) {
                    mkdir(storage_path("app/public/banner/".$banner_id."/mobile_banner"), 0775, true);
                }
               
                $mobileBanner            = 'public/banner/'.$banner_id ."/mobile_banner/". $imageName1;
                Image::make($file1)->save(storage_path('app/' . $mobileBanner));

                $info->mobile_banner       = $imageName1;
                $info->update();
            }
            $message                    = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } else {
            $error                      = 1;
            $message                    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message, 'banner_id' => $banner_id]);
    }

    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = Banner::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the Banner status!",'status'=>1]);

    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = Banner::find($id);
        $info->delete();
        $directory = 'banner/'.$id;
        Storage::deleteDirectory('public/'.$directory); 
        return response()->json(['message'=>"Successfully deleted Banner!",'status'=>1]);
    }

    public function export()
    {
        return Excel::download(new BannerExport, 'banner.xlsx');
    }

    public function exportPdf()
    {
        $list       = Banner::select('banners.*','users.name as users_name')->join('users', 'users.id', '=', 'banners.added_by')->get();
        $pdf        = PDF::loadView('platform.exports.banner.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');
        return $pdf->download('banner.pdf');
    }
}
