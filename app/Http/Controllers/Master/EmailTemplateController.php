<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Category\MainCategory;
use Illuminate\Http\Request;
use App\Models\Master\EmailTemplate;
use App\Models\Category\SubCategory;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        $title = "Email Template";
        $subCategory    = SubCategory::where('status','!=',0)->get();
        if ($request->ajax()) {
            $data = EmailTemplate::select('email_templates.*','sub_categories.name')->join('sub_categories', 'sub_categories.id', '=', 'email_templates.type_id');
            $filter_subCategory   = '';
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $filter_subCategory   = $request->get('filter_subCategory');
            $datatables =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status,$filter_subCategory) {
                    if ($status) {
                        return $query->where('email_templates.status', 'like', $status);
                    }
                    if($filter_subCategory)
                    {
                        return $query->where('sub_categories.name','like',"%{$filter_subCategory}%")->orWhere('email_templates.status', 'like', "%{$status}%");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('sub_categories.name','like',"%{$keywords}%")->orWhere('email_templates.title', 'like', "%{$keywords}%")->orWhereDate("email_templates.created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ', \''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'email-template\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'email-template\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'email-template\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'image']);
            return $datatables->make(true);
        }
        $breadCrum = array('Masters', 'Email Template');
        $title      = 'Email Template';
        return view('platform.master.email-template.index', compact('subCategory','breadCrum', 'title'));
    }

    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Email Template';
        $subCat             = MainCategory::where('slug', 'email-template')->where('status',"published")->first();
        $subCat             = $subCat->subCategory;
        
        if (isset($id) && !empty($id)) {
            $info           = EmailTemplate::find($id);
            if($info['params']){
                $info['params'] = explode(",",$info['params']);
            }
            $modal_title    = 'Update Email Template';
        }
        return view('platform.master.email-template.add_edit_modal', compact('info', 'modal_title','subCat'));
    }

    public function saveForm(Request $request)
    {
        $id                         = $request->id;
        $validator                  = Validator::make($request->all(), [
                                        'type_id' => 'required|unique:email_templates,type_id,'.$id .',id,deleted_at,NULL',
                                        'title' => 'required|string|unique:email_templates,title,' . $id . ',id,deleted_at,NULL',
                                        'message_description' => 'required',
                                    ]);

        if ($validator->passes()) {

            $params = NULL;
            $ins['type_id']                 = $request->type_id;
            $ins['title']                   = $request->title;
            $ins['message']                 = $request->message_description;
            
            if( isset( $request->kt_ecommerce_add_product_options ))
            {
                $params = implode(',',array_column($request->kt_ecommerce_add_product_options, 'params'));
            }
           
            $ins['params']                 = $params;
           

            if($request->status == "1")
            {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            $error                  = 0;
            $info       = EmailTemplate::updateOrCreate(['id' => $id], $ins);
            $message    = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } else {
            $error      = 1;
            $message    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function changeStatus(Request $request)
    {
        
        $id             = $request->id;
        $status         = $request->status;
        $info           = EmailTemplate::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the status!",'status'=>1]);

    }
    
    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = EmailTemplate::find($id);
        $info->delete();
        return response()->json(['message'=>"Successfully deleted state!",'status'=>1]);
    }
}
