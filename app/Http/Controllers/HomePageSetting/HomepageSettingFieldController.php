<?php

namespace App\Http\Controllers\HomePageSetting;

use App\Exports\HomepageSettingFieldExport;
use App\Http\Controllers\Controller;
use App\Models\HomePageSetting\HomepageSettingField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DataTables;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Excel;
use Illuminate\Support\Str;

class HomepageSettingFieldController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data       = HomepageSettingField::select('homepage_setting_fields.*');
            $status     = $request->get('status');
            $keywords   = $request->get('search')['value'];

            $datatables =  DataTables::of($data)
                ->filter(function ($query) use ($keywords,$status) {
                    if ($status) {
                        return $query->where('homepage_setting_fields.status', '=', "$status");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('homepage_setting_fields.title', 'like', "%{$keywords}%")
                        ->orWhereDate("homepage_setting_fields.created_at", $date);
                    }
                })
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ',\''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'homepage-setting-field\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })
    
                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'homepage-setting-field\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'homepage-setting-field\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';
              
                    return $edit_btn.$del_btn;
                })
                ->rawColumns(['action', 'status', 'created_at']);
            return $datatables->make(true);
        }
        $title                  = "Homepage Setting";
        $breadCrum              = array('Homepage Setting Field');
        return view('platform.homepage_setting.homepage_setting_field.index', compact('title', 'breadCrum'));
    }

    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $from               = $request->from;
        $info               = '';
        $modal_title        = 'Add Homepage Setting Field';

        if (isset($id) && !empty($id)) {
            $info           = HomepageSettingField::find($id);
           
            $modal_title    = 'Update Homepage Setting Field';
        }

        return view('platform.homepage_setting.homepage_setting_field.add_edit_modal', compact('info', 'modal_title', 'from'));
    }

    public function saveForm(Request $request,$id = null)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'title' => 'required|string|unique:homepage_setting_fields,title,' . $id . ',id,deleted_at,NULL',
                            ]);
        $banner_id      = '';
        if ($validator->passes()) {
        
            $ins['title']               = $request->title;
            $ins['slug']                = Str::slug($request->title);
            $ins['product_id']          = $request->product_id;
            $ins['description']         = $request->description;
            $ins['order_by']            = $request->order_by ?? 0;
            $ins['added_by']            = auth()->user()->id;

            if($request->status == "1")
            {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            
            $error                      = 0;
            $info                       = HomepageSettingField::updateOrCreate(['id' => $id], $ins);

            if ($request->hasFile('icon')) {
               
                $filename       = time() . '_' . $request->icon->getClientOriginalName();
                $directory      = 'product_addon/'.$info->id;
                $filename       = $directory.'/'.$filename;
                Storage::deleteDirectory('public/'.$directory);
                Storage::disk('public')->put($filename, File::get($request->icon));
                
                $info->icon = $filename;
                $info->save();
            }
        
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
        $info           = HomepageSettingField::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the Homepage Setting Field status!",'status'=>1]);

    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = HomepageSettingField::find($id);
        $info->delete();

        return response()->json(['message'=>"Successfully deleted Homepage Setting Field!",'status'=>1]);
    }

    public function export()
    {

        return Excel::download(new HomepageSettingFieldExport, 'HomepageSettingField.xlsx');
    }
}
