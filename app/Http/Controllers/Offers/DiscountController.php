<?php

namespace App\Http\Controllers\Offers;

use App\Exports\DiscountExport;
use App\Http\Controllers\Controller;
use App\Models\CouponProductCollection;
use App\Models\Offers\CouponCategory;
use App\Models\Offers\CouponCustomer;
use App\Models\Offers\CouponProduct;
use App\Models\Offers\Coupons;
use App\Models\Product\ProductCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Carbon\Carbon;
use Excel;
use Illuminate\Support\Facades\DB;
use PDF; 

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $title = "Discount";
        $breadCrum = array('Discount');
        if ($request->ajax()) {
            $data               = Coupons::select('coupons.calculate_type', 'coupons.calculate_value', 
            'coupons.coupon_name as discount_name', 'coupons.status', 'coupons.created_at', 'coupons.id', 'coupons.start_date',
             'coupons.end_date')
                                    ->where(function($query){
                                        $query->where('is_discount_on', 'yes');
                                    });
            $status             = $request->get('status');
            $keywords           = $request->get('search')['value'];
            $datatables         =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('coupons.status', '=', "$status");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        $query->where(function($que) use($keywords, $date){
                            $que->where('coupons.coupon_name', 'like', "%{$keywords}%")->orWhere('coupons.calculate_value', 'like', "%{$keywords}%")->orWhere('coupons.start_date', 'like', "%{$keywords}%")->orWhere('coupons.end_date', 'like', "%{$keywords}%")->orWhere('coupons.status', 'like', "%{$keywords}%")->orWhereDate("coupons.created_at", $date);
                        });
                        return $query;
                    }
                })
                ->addIndexColumn()
                ->editColumn('calculate_type', function ($row) {
                    if( $row->calculate_type == 'percentage' ) {
                        $calculate_type = $row->calculate_value.' %';
                    } else {
                        $calculate_type = 'INR '.$row->calculate_value;
                    }
                    return $calculate_type;
                })
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ', \''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'discount\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'discount\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'discount\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action','status', 'calculate_type']);
            return $datatables->make(true);
        }
        return view('platform.offers.discount.index', compact('breadCrum', 'title'));

    }

    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Discount';
        $couponTypeAttributes = '';
        if (isset($id) && !empty($id)) {
            $info           = Coupons::find($id);
            if( $info->coupon_type == 1 ) {
                $couponTypeAttributes       = DB::table('products')->select('id','product_name')->where('status', 'published')->get();
            } else if( $info->coupon_type == 2 ) {
                $couponTypeAttributes = DB::table('customers')->select('id','first_name')->where('status', 'published')->get();
            } else if( $info->coupon_type == 3 ) {
                $couponTypeAttributes = DB::table('product_categories')->select('id','name')->where('status', 'published')->get();
            }
            else if($info->coupon_type == 4 ){
                $couponTypeAttributes = ProductCollection::where('status', 'published')->where('can_map_discount', 'yes')->get();
              
            }
           
            $modal_title    = 'Update Discount';
        }
        
        return view('platform.offers.discount.add_edit_modal', compact('info', 'modal_title', 'couponTypeAttributes'));
    }

    public function getDiscountTypeData(Request $request)
    {
        $name           = $request->name;
        $value[]        = "<option value='all'>All</option>";

        if($name == '1')
        {
            $data       = DB::table('products')->select('id','product_name')->where('status', 'published')->get();
            $title      = "Product"; 
            foreach($data as $key=>$val)
            {
                $value[] = "<option value=".$val->id.">".$val->product_name."</option>";
            }
            return response()->json(["data"=>$value,"title"=>$title]);
        }
        if($name == '2')
        {
            $data = DB::table('customers')->select('id','first_name')->where('status', 'published')->get();
            $title = "Customer"; 
            
            foreach($data as $key=>$val)
            {
                $value[] = "<option value=".$val->id.">".$val->first_name."</option>";
            }
            return response()->json(["data"=>$value,"title"=>$title]);
        }
        if($name == '3')
        {
            $data = DB::table('product_categories')
                            ->select('id','name')
                            ->whereRaw('id not IN(select category_id from gbs_coupon_categories)')
                            ->where('status', 'published')->get();
            $title = "Categories"; 
            foreach($data as $key=>$val)
            {
                $value[] = "<option value=".$val->id.">".$val->name."</option>";
            }
            return response()->json(["data"=>$value,"title"=>$title]);
        }
        if($name == '4')
        {
            $data = DB::table('product_collections')
                        ->select('id','collection_name')
                        ->where('status', 'published')
                        ->where('can_map_discount', 'yes')
                        ->whereRaw('id not in (select product_collection_id from gbs_coupon_product_collection)')
                        ->get();
            $title = "Product Collection"; 
            foreach($data as $key=>$val)
            {
                $value[] = "<option value=".$val->id.">".$val->collection_name."</option>";
            }
            return response()->json(["data"=>$value,"title"=>$title]);
        }

    }

    public function saveForm(Request $request,$id = null)
    {
        $id                         = $request->id;
        $validator                  = Validator::make($request->all(), [
                                        'calculate_type' => 'required',
                                        'calculate_value' => 'required',
                                        'discount_type' => 'required',
                                        'discount_name' => 'required|string|unique:coupons,coupon_name,' . $id . ',id,deleted_at,NULL',
                                        'start_date' => 'required',
                                        'end_date' => 'required',
                                        'repeated_coupon'=> 'required_if:coupon_type,==,2',
                                        'minimum_order_value'=>'numeric|gt:0',
                                        
                                    ]);

        if ($validator->passes()) {
            $arrlProduct            = $request->product_id;
            $isAll                  = false;
            if( isset( $arrlProduct ) && !empty( $arrlProduct ) ) {
                $allKey = array_search('all', $arrlProduct);
                if( isset($arrlProduct[$allKey]) && $arrlProduct[$allKey] == 'all' ) {
                    $isAll          = true;
                }
            }
            /**
             * 
             *  discounttype => [ 3 = category, 4 = product_collection]
             */
            $discount_type = array(3=>'category', 4=>'product_collection');
            

            $ins['is_applied_all']              = $isAll ? 'yes' :'no';
            $ins['coupon_name']                 = $request->discount_name;
            $ins['coupon_sku']                  = \Str::slug($request->discount_name);;
            $ins['start_date']                  = $request->start_date;
            $ins['end_date']                    = $request->end_date;
            $ins['calculate_type']              = $request->calculate_type;
            $ins['calculate_value']             = $request->calculate_value;
            $ins['coupon_type']                 = $request->discount_type;
            $ins['from_coupon']                 = $discount_type[$request->discount_type];
            $ins['minimum_order_value']         = $request->minimum_order_value;
            $ins['is_discount_on']              = "yes";
            $ins['quantity']                    = 100;
            $ins['repeated_use_count']          = $request->repeated_coupon ?? 0;
            $ins['order_by']                    = $request->order_by ?? 0;
            $ins['added_by']                    = auth()->user()->id;
            
            if($request->status == "1")
            {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            $error                  = 0;
           
            $info                   = Coupons::updateOrCreate(['id' => $id], $ins);
            
            if($request->discount_type == "1" ) {

                CouponProduct::where('coupon_id',$info->id)->forceDelete();
                if( $isAll ) {
                    $couponUseable       = DB::table('products')->select('id','product_name')->where('status', 'published')->get();
                    if( isset( $couponUseable ) && !empty( $couponUseable ) ) {
                        foreach ($couponUseable as $citem) {
                            $newCdata['coupon_id']          = $info->id;
                            $newCdata['product_id']        = $citem->id;
                            $newCdata['quantity']           = 0;
                            CouponProduct::Create($newCdata);
                        }
                    }
                } else {

                    foreach($request->product_id as $key=>$val)
                    {
                        $data['coupon_id']          = $info->id;
                        $data['product_id']         = $val;
                        $data['quantity']           = 0;

                        CouponProduct::Create($data);
                    }
                }

            } else if($request->discount_type == "2" ) {

                CouponCustomer::where('coupon_id',$info->id)->forceDelete();
                if( $isAll ) {
                    $couponUseable = DB::table('customers')->select('id','first_name')->where('status', 'published')->get();
                    if( isset( $couponUseable ) && !empty( $couponUseable ) ) {
                        foreach ($couponUseable as $citem) {
                            $newCdata['coupon_id']          = $info->id;
                            $newCdata['customer_id']        = $citem->id;
                            $newCdata['quantity']           = 0;
                            CouponCustomer::Create($newCdata);
                        }
                    }
                } else {
                    foreach($request->product_id as $cusItem)
                    {
                        $data['coupon_id']          = $info->id;
                        $data['customer_id']        = $cusItem;
                        $data['quantity']           = 0;
                        CouponCustomer::Create($data);
                    }
                }
                
            } else if($request->discount_type == "3" ) {

                CouponCategory::where('coupon_id',$info->id)->forceDelete();
                if( $isAll ) {
                    $couponUseable = DB::table('product_categories')->select('id','name')->where('status', 'published')->get();
                    if( isset( $couponUseable ) && !empty( $couponUseable ) ) {
                        foreach ($couponUseable as $citem) {
                            $newCdata['coupon_id']          = $info->id;
                            $newCdata['category_id']        = $citem->id;
                            $newCdata['quantity']           = 0;
                            CouponCategory::Create($newCdata);
                        }
                    }
                } else {
                    foreach($request->product_id as $catItem)
                    {
                        $data['coupon_id']          = $info->id;
                        $data['category_id']        = $catItem;
                        $data['quantity']           = 0;
                        CouponCategory::Create($data);
                    }
                }
               
            }
            else if($request->discount_type == "4" )
            {
                CouponProductCollection::where('coupon_id',$info->id)->forceDelete();
                
                if(isset($request->product_id) && !empty($request->product_id))
                {
                    foreach($request->product_id  as $val){
                        $productCollect['coupon_id']                  = $info->id;
                        $productCollect['product_collection_id']      = $val;
                        $productCollect['quantity']                   = 100;
                        CouponProductCollection::Create($productCollect);
                    }
                }
            }
            
            $message   = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } 
        else {
            $error      = 1;
            $message    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function changeStatus(Request $request)
    {
        
        $id             = $request->id;
        $status         = $request->status;
        $info           = Coupons::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the Discount status!",'status'=>1]);

    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = Coupons::find($id);
        $info->couponProducts()->delete();
        $info->couponCustomers()->delete();
        $info->couponCategory()->delete();
        $info->couponProductCollection()->delete();
        $info->forceDelete();
        return response()->json(['message'=>"Successfully deleted Discount!",'status'=>1]);
    }

    public function export()
    {
        return Excel::download(new DiscountExport, 'discount.xlsx');
    }

    public function exportPdf()
    {
        $list       = Coupons::select('coupons.*')->where('is_discount_on', 'yes')->get();
        $pdf        = PDF::loadView('platform.exports.coupon.discount_excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('discount.pdf');
    }
    
}
