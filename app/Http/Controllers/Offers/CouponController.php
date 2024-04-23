<?php

namespace App\Http\Controllers\Offers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\CouponsExport;
use App\Models\Offers\Coupons;
use App\Models\Offers\CouponProduct;
use App\Models\Offers\CouponCustomer;
use App\Models\Offers\CouponCategory;
use App\Models\Offers\CouponBrand;
use Illuminate\Support\Facades\DB;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Auth;
use Excel;
use PDF;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $title = "Coupons";
        $breadCrum = array('Coupons');
        if ($request->ajax()) {
            $data               = Coupons::select('coupons.*')
                ->where(function ($query) {
                    $query->where('is_discount_on', 'no');
                });
            $status             = $request->get('status');
            $coupon_type        = $request->get('coupon_type');
            $keywords           = $request->get('search')['value'];
            $datatables         =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status, $coupon_type) {
                    if ($status) {
                        return $query->where('coupons.status', '=', "$status");
                    }
                    if ($coupon_type ?? null) {
                        return $query->where('coupons.coupon_type', '=', "$coupon_type")
                            ->orWhere('coupons.status', '=', "$status");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        $query->where(function ($que) use ($keywords, $date) {
                            $que->where('coupons.coupon_name', 'like', "%{$keywords}%")->orWhere('coupons.coupon_code', 'like', "%{$keywords}%")->orWhere('coupons.start_date', 'like', "%{$keywords}%")->orWhere('coupons.end_date', 'like', "%{$keywords}%")->orWhere('coupons.status', 'like', "%{$keywords}%")->orWhereDate("coupons.created_at", $date);
                        });
                        return $query;
                    }
                })
                ->editColumn('coupon_type', function ($row) {
                    if ($row->coupon_type == '1' || $row->coupon_type == '4') {
                        $coupon_type = "Product";
                    }
                    if ($row->coupon_type == '2') {
                        $coupon_type = "Customer";
                    }
                    if ($row->coupon_type == '3') {
                        $coupon_type = "Category";
                    }
                     if ($row->coupon_type == '5') {
                        $coupon_type = "Brand";
                    }
                    return $coupon_type;
                })
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-' . (($row->status == 'published') ? 'success' : 'danger') . '" tooltip="Click to ' . (($row->status == 'published') ? 'Unpublish' : 'Publish') . '" onclick="return commonChangeStatus(' . $row->id . ', \'' . (($row->status == 'published') ? 'unpublished' : 'published') . '\', \'coupon\')">' . ucfirst($row->status) . '</a>';
                    return $status;
                })
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'coupon\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'coupon\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'coupon_type']);
            return $datatables->make(true);
        }
        return view('platform.offers.coupon.index', compact('breadCrum', 'title'));
    }

    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Coupon';
        $couponTypeAttributes = '';
        if (isset($id) && !empty($id)) {
            $info           = Coupons::find($id);
            if ($info->coupon_type == 1 || $info->coupon_type == 4) {
                $couponTypeAttributes       = DB::table('products')->select('id', 'product_name')->where('status', 'published')->get();
            } else if ($info->coupon_type == 2) {
                $couponTypeAttributes = DB::table('customers')->select('id', 'first_name')->where('status', 'published')->get();
            } else if ($info->coupon_type == 3) {
                $couponTypeAttributes = DB::table('product_categories')->select('id', 'name')->where('status', 'published')->get();
            }else if ($info->coupon_type == 5) {
                $couponTypeAttributes = DB::table('brands')->select('id','brand_name as name')->where('status', 'published')->get();
            }
            $modal_title    = 'Update Coupon';
        }
        return view('platform.offers.coupon.add_edit_modal', compact('info', 'modal_title', 'couponTypeAttributes'));
    }

    public function couponType(Request $request)
    {
        $name           = $request->name;
        $value[]        = "<option value='all'>All</option>";

        if ($name == '1' || $name == '4') {
            $data       = DB::table('products')->select('id', 'product_name')->where('status', 'published')->get();
            $title      = "Product";
            foreach ($data as $key => $val) {
                if($name == '4') {
                    $selected =  $key == 0 ? 'selected' : '';
                    $value[] = "<option $selected value=" . $val->id . ">" . $val->product_name . "</option>";
                } else {
                    $value[] = "<option value=" . $val->id . ">" . $val->product_name . "</option>";
                }
            }
            return response()->json(["data" => $value, "title" => $title]);
        }
        if ($name == '2') {
            $data = DB::table('customers')->select('id', 'first_name')->where('status', 'published')->get();
            $title = "Customer";

            foreach ($data as $key => $val) {
                $value[] = "<option value=" . $val->id . ">" . $val->first_name . "</option>";
            }
            return response()->json(["data" => $value, "title" => $title]);
        }
        if ($name == '3') {
            $data = DB::table('product_categories')->select('id', 'name')->where('status', 'published')->get();
            $title = "Categories";
            foreach ($data as $key => $val) {
                $value[] = "<option value=" . $val->id . ">" . $val->name . "</option>";
            }
            return response()->json(["data" => $value, "title" => $title]);
        }
         if ($name == '5') {
            $data = DB::table('brands')->select('id', 'brand_name as name')->where('status', 'published')->get();
            $title = "Brands";
            foreach ($data as $key => $val) {
                $value[] = "<option value=" . $val->id . ">" . $val->name . "</option>";
            }
            return response()->json(["data" => $value, "title" => $title]);
        }
    }

    public function saveForm(Request $request, $id = null)
    {

        $id                         = $request->id;
        $validator                  = Validator::make($request->all(), [
            'calculate_type' => 'required',
            'calculate_value' => 'required',
            'coupon_type' => 'required',
            'coupon_name' => 'required|string|unique:coupons,coupon_name,' . $id . ',id,deleted_at,NULL',
            'coupon_code' => 'required|string|unique:coupons,coupon_code,' . $id . ',id,deleted_at,NULL',
            'start_date' => 'required',
            'end_date' => 'required',
            'repeated_coupon' => 'required_if:coupon_type,==,2',
            'quantity' => 'numeric|gt:0',
            'minimum_order_value' => 'numeric|gt:0',
        ]);

        if ($validator->passes()) {
            $arrlProduct            = $request->product_id;
            $isAll                  = false;
            if (isset($arrlProduct) && !empty($arrlProduct)) {
                $allKey = array_search('all', $arrlProduct);
                if (isset($arrlProduct[$allKey]) && $arrlProduct[$allKey] == 'all') {
                    $isAll          = true;
                }
            }

            $ins['is_applied_all']              = $isAll ? 'yes' : 'no';
            $ins['coupon_name']                 = $request->coupon_name;
            $ins['coupon_code']                 = $request->coupon_code;
            $ins['coupon_sku']                  = \Str::slug($request->coupon_name);;
            $ins['start_date']                  = $request->start_date;
            $ins['end_date']                    = $request->end_date;
            $ins['calculate_type']              = $request->calculate_type;
            $ins['calculate_value']             = $request->calculate_value;
            $ins['coupon_type']                 = $request->coupon_type;
            $ins['minimum_order_value']         = $request->minimum_order_value;
            $ins['is_discount_on']              = "no";
            $ins['quantity']                    = $request->quantity;
            $ins['repeated_use_count']          = $request->repeated_coupon ?? 0;
            $ins['order_by']                    = $request->order_by ?? 0;
            $ins['added_by']            = Auth::id();

            if ($request->status == "1") {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            $error                  = 0;

            $info                   = Coupons::updateOrCreate(['id' => $id], $ins);

            if ($request->coupon_type == "1" || $request->coupon_type == "4") {

                CouponProduct::where('coupon_id', $info->id)->forceDelete();
                if ($isAll) {
                    $couponUseable       = DB::table('products')->select('id', 'product_name')->where('status', 'published')->get();
                    if (isset($couponUseable) && !empty($couponUseable)) {
                        foreach ($couponUseable as $citem) {
                            $newCdata['coupon_id']          = $info->id;
                            $newCdata['product_id']        = $citem->id;
                            $newCdata['quantity']           = 0;
                            CouponProduct::Create($newCdata);
                        }
                    }
                } else {

                    foreach ($request->product_id as $key => $val) {
                        $data['coupon_id']          = $info->id;
                        $data['product_id']         = $val;
                        $data['quantity']           = 0;

                        CouponProduct::Create($data);
                    }
                }
            } else if ($request->coupon_type == "2") {

                CouponCustomer::where('coupon_id', $info->id)->forceDelete();
                if ($isAll) {
                    $couponUseable = DB::table('customers')->select('id', 'first_name')->where('status', 'published')->get();
                    if (isset($couponUseable) && !empty($couponUseable)) {
                        foreach ($couponUseable as $citem) {
                            $newCdata['coupon_id']          = $info->id;
                            $newCdata['customer_id']        = $citem->id;
                            $newCdata['quantity']           = 0;
                            CouponCustomer::Create($newCdata);
                        }
                    }
                } else {
                    foreach ($request->product_id as $cusItem) {
                        $data['coupon_id']          = $info->id;
                        $data['customer_id']        = $cusItem;
                        $data['quantity']           = 0;
                        CouponCustomer::Create($data);
                    }
                }
            } else if ($request->coupon_type == "3") {

                CouponCategory::where('coupon_id', $info->id)->forceDelete();
                if ($isAll) {
                    $couponUseable = DB::table('product_categories')->select('id', 'name')->where('status', 'published')->get();
                    if (isset($couponUseable) && !empty($couponUseable)) {
                        foreach ($couponUseable as $citem) {
                            $newCdata['coupon_id']          = $info->id;
                            $newCdata['category_id']        = $citem->id;
                            $newCdata['quantity']           = 0;
                            CouponCategory::Create($newCdata);
                        }
                    }
                } else {
                    foreach ($request->product_id as $catItem) {
                        $data['coupon_id']          = $info->id;
                        $data['category_id']        = $catItem;
                        $data['quantity']           = 0;
                        CouponCategory::Create($data);
                    }
                }
            }else if ($request->coupon_type == "5") {

                CouponBrand::where('coupon_id', $info->id)->forceDelete();
                if ($isAll) {
                    $couponUseable = DB::table('brands')->select('id', 'brand_name')->where('status', 'published')->get();
                    if (isset($couponUseable) && !empty($couponUseable)) {
                        foreach ($couponUseable as $citem) {
                            $newCdata['coupon_id']          = $info->id;
                            $newCdata['brand_id']        = $citem->id;
                            $newCdata['quantity']           = 0;
                            CouponBrand::Create($newCdata);
                        }
                    }
                } else {
                    foreach ($request->product_id as $catItem) {
                        $data['coupon_id']          = $info->id;
                        $data['brand_id']        = $catItem;
                        $data['quantity']           = 0;
                        CouponBrand::Create($data);
                    }
                }
            }

            $message   = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } else {
            $error      = 1;
            $message    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);
    }
    public function couponGendrate(Request $request)
    {
        $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $val =  substr(str_shuffle($permitted_chars), 0, 6);
        return $val;
    }
    public function changeStatus(Request $request)
    {

        $id             = $request->id;
        $status         = $request->status;
        $info           = Coupons::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message' => "You changed the Coupon status!", 'status' => 1]);
    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = Coupons::find($id);
        $info->couponProducts()->delete();
        $info->couponCustomers()->delete();
        $info->couponCategory()->delete();
        $info->couponBrandData()->delete();
        $info->forceDelete();
        return response()->json(['message' => "Successfully deleted Coupon!", 'status' => 1]);
    }


    public function export()
    {
        return Excel::download(new CouponsExport, 'coupon.xlsx');
    }

    public function exportPdf()
    {
        $list       = Coupons::select('coupons.*')->where('is_discount_on', 'no')->get();
        $pdf        = PDF::loadView('platform.exports.coupon.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('coupon.pdf');
    }
}
