<?php

namespace App\Http\Controllers;

use App\Exports\StoreLocatorOfferExport;
use App\Models\StoreLocator;
use App\Models\StoreLocatorOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use DataTables;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Excel;
use Illuminate\Support\Facades\DB;
use Image;

class StoreLocatorOfferController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data       = StoreLocatorOffer::select(DB::raw("group_concat(gbs_store_locator_offers.title) AS title, gbs_store_locators.title as store_locator, gbs_store_locator_offers.store_locator_id, gbs_store_locator_offers.status, gbs_store_locator_offers.id"))
            ->leftJoin('store_locators','store_locators.id','=','store_locator_offers.store_locator_id')->groupBy('store_locator_id');
           
            $status     = $request->get('status');
            $keywords   = $request->get('search')['value'];

            $datatables =  DataTables::of($data)
                ->filter(function ($query) use ($keywords,$status) {
                    if ($status) {
                        return $query->where('store_locator_offers.status', '=', "$status");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('store_locator_offers.title', 'like', "%{$keywords}%")
                        ->orWhere('store_locators.title', 'like', "%{$keywords}%")
                        ->orWhereDate("store_locator_offers.created_at", $date);
                    }
                })
                ->addIndexColumn()
    
                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'store-offer\',' . $row->store_locator_id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'store-offer\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';
              
                    return $edit_btn.$del_btn;
                })
                ->rawColumns(['action']);
            return $datatables->make(true);
        }
        $title                  = "Store Locators";
        $breadCrum              = array('Store Locator Offer');
        return view('platform.store_locator_offer.index', compact('title', 'breadCrum'));
    }
    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $from               = $request->from;
        $info               = '';
        $data = [];
        $modal_title        = 'Add Store Locator Offer';
        $storeLocator = StoreLocator::where('status','published')->get();
        if (isset($id) && !empty($id)) {
            $info           = StoreLocatorOffer::where('store_locator_id',$id)->get();
            $modal_title    = 'Update Store Locator Offer';
        }
        return view('platform.store_locator_offer.add_edit_modal', compact('info', 'modal_title', 'from','storeLocator'));
    }

    public function saveForm(Request $request,$id = null)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'store_locator_id' => 'required',
                            ]);

        if ($validator->passes()) {
            
            $title = array_filter($request->title);
            $image  = $request->image;
            $offer_id  = $request->offer_id;
            $store_locator_id = $request->store_locator_id;

            if( isset($offer_id) && count($offer_id) > 0 ) {
                StoreLocatorOffer::where('store_locator_id', $store_locator_id)->whereNotIn('id', $offer_id)->delete();
            }
            
            if( isset( $title ) && !empty($title)) {
                
                for($i = 0 ; $i < count($title) ; $i++)
                {
                    $id = $offer_id[$i] ?? '';
                    $ins['store_locator_id']        = $store_locator_id;
    
                    $ins['status']                  = 'published';
                    $ins['title']                   = $title[$i] ?? '';
                    $ins['added_by']                = Auth::id();
                    
                    if ( isset( $image[$i] ) && !empty( $image[$i] ) ) {
    
                        $imageName                  = uniqid().$image[$i]->getClientOriginalName();
                        
                        if (!is_dir(storage_path("app/public/store/".$store_locator_id."/offer"))) {
                            mkdir(storage_path("app/public/store/".$store_locator_id."/offer"), 0775, true);
                        }
                        
                        $fileNameThumb              = 'public/store/'.$store_locator_id.'/offer/' . time() . '-' . $imageName;
                        Image::make($image[$i])->save(storage_path('app/' . $fileNameThumb));
    
                        $ins['image']    = $fileNameThumb;
                        
                    }

                    StoreLocatorOffer::updateOrCreate(['store_locator_id'=>$request->store_locator_id,'id'=> $id],$ins);
                   
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
        $info           = StoreLocatorOffer::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the Store Locator Offer status!",'status'=>1]);

    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = StoreLocatorOffer::find($id);
        $info->delete();
        
        return response()->json(['message'=>"Successfully deleted Service Center Offer!",'status'=>1]);
    }
    public function export()
    {
        return Excel::download(new StoreLocatorOfferExport, 'StoreLocatorOffer.xlsx');
    }
}
