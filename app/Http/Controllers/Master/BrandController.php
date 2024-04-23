<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\BrandsExport;
use App\Models\Master\Brands;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Auth;
use Excel;
use Illuminate\Support\Facades\Storage;
use PDF;
use Image;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $title = "Brand";
        
        if ($request->ajax()) {
            $data =Brands::select('brands.*','users.name as users_name')->leftJoin('users', 'users.id', '=', 'brands.added_by');
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('brands.status', '=', "$status");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('brands.brand_name', 'like', "%{$keywords}%")->orWhere('users.name', 'like', "%{$keywords}%")->orWhere('brands.short_description', 'like', "%{$keywords}%")->orWhere('brands.notes', 'like', "%{$keywords}%")->orWhere("brands.order_by",'like', "%{$keywords}%")->orWhereDate("brands.created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ',\''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'brands\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->editColumn('brand_logo', function ($row) {
                    if ($row->brand_logo) {
                        
                        // print_r( $url );
                        $brandLogoPath = 'brands/'.$row->id.'/option1/'.$row->brand_logo;
                        $url = Storage::url($brandLogoPath);
                        $path = asset($url);
                        $brand_logo = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    } else {
                        $path = asset('userImage/no_Image.jpg');
                        $brand_logo = '<div class="symbol symbol-45px me-5"><img src="' . $path . '" alt="" /><div>';
                    }
                    return $brand_logo;
                })
               
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'brands\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'brands\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'brand_logo']);
            return $datatables->make(true);
        }
        $breadCrum  = array('Masters', 'Brands');
        $title      = 'Brands';
        return view('platform.master.brand.index', compact('breadCrum', 'title'));
    }

    public function modalAddEdit(Request $request)
    {

        $id                 = $request->id;
        $from               = $request->from;
        $info               = '';
        $modal_title        = 'Add Brand';
        if (isset($id) && !empty($id)) {
            $info           = Brands::find($id);
            $modal_title    = 'Update Brand';
        }
        
        return view('platform.master.brand.add_edit_modal', compact('info', 'modal_title', 'from'));
        
    }

    public function saveForm(Request $request,$id = null)
    {
        
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'brand_name' => 'required|string|unique:brands,brand_name,' . $id . ',id,deleted_at,NULL',
                                'avatar_logo' => 'mimes:jpeg,png,jpg|max:150',
                                'avatar_banner' => 'mimes:jpeg,png,jpg|max:150',
                                'order_by' => 'required|unique:brands,order_by,' . $id . ',id,deleted_at,NULL',

                            ]);
        $brand_id       = '';

        if ($validator->passes()) {
                                 
            if ($request->image_remove_logo == "yes") {
                $ins['brand_logo'] = '';
            }
 
            $ins['brand_name']          = $request->brand_name;
            $ins['short_description']   = $request->short_description;
            $ins['notes']               = $request->notes;
            $ins['order_by']            = $request->order_by ?? 0;
            $ins['added_by']            = Auth::id();
            $ins['slug']                = Str::slug($request->brand_name);
            if($request->status == "1")
            {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            if($request->top_brand == "1")
            {
                $ins['is_top_brand']          = 'yes';
            }
            else
            {
                $ins['is_top_brand']          = 'no';
            }
            $error                  = 0;
            $info                   = Brands::updateOrCreate(['id' => $id], $ins);
            $brand_id               = $info->id;

        
            if ($request->hasFile('brand_logo')) {
                
                $directory = 'brands/'.$brand_id;
                Storage::deleteDirectory('public/'.$directory);

                $file                   = $request->file('brand_logo');
                $imageName              = uniqid().$file->getClientOriginalName();
                if (!is_dir(storage_path("app/public/brands/".$brand_id."/option1"))) {
                    mkdir(storage_path("app/public/brands/".$brand_id."/option1"), 0775, true);
                }
                if (!is_dir(storage_path("app/public/brands/".$brand_id."/option2"))) {
                    mkdir(storage_path("app/public/brands/".$brand_id."/option2"), 0775, true);
                }
                if (!is_dir(storage_path("app/public/brands/".$brand_id."/option3"))) {
                    mkdir(storage_path("app/public/brands/".$brand_id."/option3"), 0775, true);
                }
                if (!is_dir(storage_path("app/public/brands/".$brand_id."/option4"))) {
                    mkdir(storage_path("app/public/brands/".$brand_id."/option4"), 0775, true);
                }

                if (!is_dir(storage_path("app/public/brands/".$brand_id."/thumb"))) {
                    mkdir(storage_path("app/public/brands/".$brand_id."/thumb"), 0775, true);
                }

                if (!is_dir(storage_path("app/public/brands/".$brand_id."/default"))) {
                    mkdir(storage_path("app/public/brands/".$brand_id."/default"), 0775, true);
                }

                $option1Path            = 'public/brands/'.$brand_id.'/option1/' . $imageName;
                Image::make($file)->resize(350,690)->save(storage_path('app/' . $option1Path));
                
                $option2Path            = 'public/brands/'.$brand_id.'/option2/' . $imageName;
                Image::make($file)->resize(350,336)->save(storage_path('app/' . $option2Path));

                $option3Path            = 'public/brands/'.$brand_id.'/option3/' . $imageName;
                Image::make($file)->resize(350,336)->save(storage_path('app/' . $option3Path));

                $option4Path            = 'public/brands/'.$brand_id.'/option4/' . $imageName;
                Image::make($file)->resize(350,336)->save(storage_path('app/' . $option4Path)); 

                $option5Path            = 'public/brands/'.$brand_id.'/thumb/' . $imageName;
                Image::make($file)->resize(285,30)->save(storage_path('app/' . $option5Path)); 

                $option6Path            = 'public/brands/'.$brand_id.'/default/' . $imageName;
                Image::make($file)->save(storage_path('app/' . $option6Path)); 

                $info->brand_logo       = $imageName;
                $info->update();

            }

            $message                    = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';

        } else {

            $error                      = 1;
            $message                    = $validator->errors()->all();

        }
        return response()->json(['error' => $error, 'message' => $message, 'brand_id' => $brand_id]);
    }
    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = Brands::find($id);
        $info->delete();
        return response()->json(['message'=>"Successfully deleted brand!",'status'=>1]);
    }
    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = Brands::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the Brand status!",'status'=>1]);

    }
    public function export()
    {
        return Excel::download(new BrandsExport, 'brand.xlsx');
    }

    public function exportPdf()
    {
        $list       = Brands::select('brands.*','users.name as users_name')->join('users', 'users.id', '=', 'brands.added_by')->get();
        $pdf        = PDF::loadView('platform.exports.brand.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('brand.pdf');
    }
}
