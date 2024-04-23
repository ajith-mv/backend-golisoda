<?php

namespace App\Http\Controllers;

use App\Exports\SmsExport;
use App\Models\SmsTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use PDF;
use Excel;

class SmsTemplateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data       = SmsTemplate::select('sms_templates.*','users.name as users_name')->join('users', 'users.id', '=', 'sms_templates.added_by');
            $status     = $request->get('status');
            $keywords   = $request->get('search')['value'];
            $datatables =  DataTables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('sms_templates.status', 'like', "%{$status}%");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('sms_templates.title', 'like', "%{$keywords}%")->orWhere('users.name', 'like', "%{$keywords}%")->orWhereDate("walk_throughs.created_at", $date);
                    }
                })
                ->addIndexColumn()
               
                ->addColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ', \''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'sms-template\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'sms-template\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'sms-template\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status']);
            return $datatables->make(true);
        }
        
    }

    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Sms Template';
        if (isset($id) && !empty($id)) {
            $info           = SmsTemplate::find($id);
            $modal_title    = 'Update Sms Template';
        }
        
        return view('platform.sms_template.add_edit_modal', compact('info', 'modal_title'));
    }

    public function saveForm(Request $request,$id = null)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'company_name' => 'required',
                                'peid_no' => 'required',
                                'tdlt_no' => 'required',
                                'header' => 'required',
                                'template_name' => 'required',
                                'sms_type' => 'required',
                                'template_content' => 'required',
                            ]);

        if ($validator->passes()) {

            $ins['company_name']        = $request->company_name;
            $ins['peid_no']             = $request->peid_no;
            $ins['tdlt_no']             = $request->tdlt_no;
            $ins['header']              = $request->header;
            $ins['template_name']       = $request->template_name;
            $ins['sms_type']            = $request->sms_type;
            $ins['template_content']    = $request->template_content;
            $ins['added_by']            = auth()->user()->id;

            if($request->status == "1")
            {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }

            $error                      = 0;
            $info                       = SmsTemplate::updateOrCreate(['id' => $id], $ins);
            $message                    = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } else {
            $error                      = 1;
            $message                    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = SmsTemplate::find($id);
        $info->delete();
        return response()->json(['message'=>"Successfully deleted Sms Template!",'status'=>1]);
    }

    public function changeStatus(Request $request)
    {
        
        $id             = $request->id;
        $status         = $request->status;
        $info           = SmsTemplate::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the Sms Template status!",'status'=>1]);

    }

    public function export()
    {
        return Excel::download(new SmsExport, 'sms_templates.xlsx');
    }

    public function exportPdf()
    {
        $list       = SmsTemplate::select('sms_templates.*','users.name as users_name')->join('users', 'users.id', '=', 'sms_templates.added_by')->get();
        $pdf        = PDF::loadView('platform.exports.global.sms_template', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('sms_templates.pdf');
    }

}
