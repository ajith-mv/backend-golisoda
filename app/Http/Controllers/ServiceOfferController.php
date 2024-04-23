<?php

namespace App\Http\Controllers;

use App\Exports\ServiceCenterOfferExport;
use App\Models\ServiceCenter;
use App\Models\ServiceCenterOffer;
use App\Models\ServiceOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DataTables;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Excel;
use Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceOfferController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data       = ServiceCenterOffer::select(DB::raw("group_concat(gbs_service_center_offers.title) AS title, gbs_service_centers.title as service_center, gbs_service_center_offers.service_center_id, gbs_service_center_offers.status, gbs_service_center_offers.id"))
            ->leftJoin('service_centers','service_centers.id','=','service_center_offers.service_center_id')->groupBy('service_center_id');
           
            $status     = $request->get('status');
            $keywords   = $request->get('search')['value'];

            $datatables =  DataTables::of($data)
                ->filter(function ($query) use ($keywords,$status) {
                    if ($status) {
                        return $query->where('service_center_offers.status', '=', "$status");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('service_center_offers.title', 'like', "%{$keywords}%")
                        ->orWhere('service_centers.title', 'like', "%{$keywords}%");
                        
                    }
                })
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ',\''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'service-offer\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
    
                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'service-offer\',' . $row->service_center_id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'service-offer\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';
              
                    return $edit_btn.$del_btn;
                })
                ->rawColumns(['action', 'status']);
            return $datatables->make(true);
        }
        $title                  = "Service Center";
        $breadCrum              = array('Service Offer');
        return view('platform.service_offer.index', compact('title', 'breadCrum'));

    }

    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $from               = $request->from;
        $info               = '';
        $data = [];
        $modal_title        = 'Add Service Offer';
        $serviceCenter = ServiceCenter::where('status','published')->get();
        if (isset($id) && !empty($id)) {
            $info           = ServiceCenterOffer::where('service_center_id',$id)->get();
            $modal_title    = 'Update Service Offer';
        }

        return view('platform.service_offer.add_edit_modal', compact('info', 'modal_title', 'from','serviceCenter'));
    }

    public function saveForm(Request $request,$id = null)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'service_center_id' => 'required',
                            ]);
        if ($validator->passes()) {
          
           
            $title = array_filter($request->title);
            $image  = $request->image;
            $offer_id  = $request->offer_id;
            $service_center_id = $request->service_center_id;
            if( isset($offer_id) && count($offer_id) > 0 ){
                ServiceCenterOffer::where('service_center_id', $service_center_id)->whereNotIn('id', $offer_id)->delete();
            }
            
            if( isset( $title ) && !empty($title)) {
                
                for($i = 0 ; $i < count($title) ; $i++)
                {
                    $id = $offer_id[$i] ?? '';
                    $ins['service_center_id']        = $service_center_id;
    
                    $ins['status']                  = 'published';
                    $ins['title']                   = $title[$i] ?? '';
                    $ins['added_by']                = Auth::id();
                  
                    
                    if ( isset( $image[$i] ) && !empty( $image[$i] ) ) {
    
                        $imageName                  = uniqid().$image[$i]->getClientOriginalName();
                        
                        if (!is_dir(storage_path("app/public/serviceCenter/".$service_center_id."/offer"))) {
                            mkdir(storage_path("app/public/serviceCenter/".$service_center_id."/offer"), 0775, true);
                        }
                        
                        $fileNameThumb              = 'public/serviceCenter/'.$service_center_id.'/offer/' . time() . '-' . $imageName;
                        Image::make($image[$i])->save(storage_path('app/' . $fileNameThumb));
    
                        $ins['image']    = $fileNameThumb;
                        
                    }

                    ServiceCenterOffer::updateOrCreate(['service_center_id'=>$request->service_center_id,'id'=> $id],$ins);
                   
                }
            }
            $error                      = 0;
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
        $info           = ServiceCenterOffer::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the Service Center Offer status!",'status'=>1]);

    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = ServiceCenterOffer::find($id);
        $info->delete();
        
        return response()->json(['message'=>"Successfully deleted Service Center Offer!",'status'=>1]);
    }
    public function export()
    {
        return Excel::download(new ServiceCenterOfferExport, 'ServiceCenterOffer.xlsx');
    }
}
