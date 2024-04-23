<?php

namespace App\Http\Controllers;

use App\Exports\CustomerExport;
use App\Models\Category\MainCategory;
use App\Models\Master\City;
use App\Models\Master\Country;
use App\Models\Master\Customer;
use App\Models\Master\CustomerAddress;
use App\Models\Master\Pincode;
use App\Models\Master\State;
use App\Models\Order;
use App\Models\Wishlist;
use PDF;
use Illuminate\Http\Request;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::select('customers.*');
            $filter_subCategory   = '';
            $status = $request->get('status');
            $keywords = $request->get('search')['value'];
            $datatables =  DataTables::of($data)
                ->filter(function ($query) use ($keywords, $status,$filter_subCategory) {
                    if ($status) {
                        return $query->where('customers.status', 'like', $status);
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('customers.first_name','like',"%{$keywords}%")
                                ->orWhere('customers.last_name', 'like', "%{$keywords}%")
                                ->orWhere('customers.email', 'like', "%{$keywords}%")
                                ->orWhere('customers.mobile_no', 'like', "%{$keywords}%")
                                ->orWhere('customers.customer_no', 'like', "%{$keywords}%")
                                ->orWhere('customers.status', 'like', "%{$keywords}%")
                                ->orWhereDate("customers.created_at", $date);
                    }
                })
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ', \''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'customer\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })
             
                ->addColumn('action', function ($row) {
                    $view_btn = '<a href="'.route('customer.view',$row->id).'"  class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-eye"></i>
                </a>';
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'customer\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'customer\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $view_btn . $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status']);
            return $datatables->make(true);
        }
        $breadCrum = array('Customer');
        $title      = 'Customer';
        return view('platform.customer.index',compact('title','breadCrum'));
    }

    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $info               = '';
        $modal_title        = 'Add Customer';
        if (isset($id) && !empty($id)) {
            $info           = Customer::find($id);
            $modal_title    = 'Update Customer';
        }
        
        return view('platform.customer.add_edit_modal', compact('info', 'modal_title'));
    }
    
   
    public function saveForm(Request $request,$id = null)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'first_name' => 'required|string',
                                'email' => 'required|email|unique:customers,email,' . $id . ',id',
                                'mobile_no' => 'required|numeric|digits:10|unique:customers,mobile_no,'. $id . ',id',
                                'avatar' => 'max:150'
                            ]);
        if ($validator->passes()) {

            
            $ins['first_name']      = $request->first_name;
            $ins['last_name']       = $request->last_name;
            $ins['email']           = $request->email;
            $ins['password']        = Hash::make($request->password);
            $ins['mobile_no']       = $request->mobile_no;
            $ins['address']         = $request->address;
            $ins['dob']         = $request->dob;
            if (empty($id)) {
             $ins['customer_no'] = getCustomerNo();
            }
            if($request->status)
            {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            $error                  = 0;
            $error                      = 0;
            $customerInfo = Customer::updateOrCreate(['id' => $id], $ins);
            $customerId = $customerInfo->id;
            if ($request->image_remove == "yes") {
                $customerInfo->profile_image = '';
            }
            if ($request->hasFile('avatar')) {
               
                $filename       = time() . '_' . $request->avatar->getClientOriginalName();
                $directory      = 'customer/'.$customerId;
                $filename       = $directory.'/'.$filename;
                Storage::deleteDirectory('public/'.$directory);
                Storage::disk('public')->put($filename, File::get($request->avatar));
                
                $customerInfo->profile_image = $filename;
                $customerInfo->save();
            }
            
            $message    = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';

        } else {
            $error      = 1;
            $message    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function customerDelete(Request $request)
    {
        $id         = $request->id;
        $info       = Customer::find($id);
        
        $info->delete();
        return response()->json(['message'=>"Successfully deleted customer!",'status'=>1]);
    }

    public function changeStatus(Request $request)
    {
        
        $id             = $request->id;
        $status         = $request->status;
        $info           = Customer::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the status!",'status'=>1]);

    }

    public function export()
    {
        return Excel::download(new CustomerExport, 'customers.xlsx');
    }

    public function exportPdf()
    {
        $list       = Customer::select('customers.*')->get();
        $pdf        = PDF::loadView('platform.exports.customer.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('customer.pdf');
    }

    public function view($id)
    {
        $breadCrum          = array('Customer','Customer View');
        $title              = 'Customer View';
        $info               = Customer::where('id',$id)->first();
        $customerAddress    = CustomerAddress::get();
        return view('platform.customer.view', compact('title', 'breadCrum','info','customerAddress'));

    }
    public function addAddress(Request $request)
    {
        $customerId             = $request->customerId;
        $addressId              = $request->addressId;
        $modal_title            = 'Add Address';
      
        $addressType            = MainCategory::where('slug', 'address')->first();
        $country                = Country::where('status',1)->get();
        $state                  = State::where('status',1)->get();
        $city                   = City::where('status',1)->get();
        $pinCode                = Pincode::where('status',1)->get();
        $customerInfo           = Customer::find( $customerId );
        $info                   = '';
        if(isset($addressId) && !empty($addressId))
        {
            $info = CustomerAddress::find( $addressId );
        }

        return view('platform.customer.form.address.form_address',compact('modal_title','customerInfo','addressType','country','city','state','pinCode','info'));

    }
    public function customerAddress(Request $request)
    {
        
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'first_name' => 'required|string',
                                'email' => 'required|email',
                                'mobile_no' => 'required|numeric|digits:10'
                            ]);
        if ($validator->passes()) {
            $ins['customer_id']             = $request->customer_id;
            $ins['address_type_id']         = (int)$request->address_type_id;
           
            $ins['name']                    = $request->first_name;
            $ins['email']                   = $request->email;
            $ins['mobile_no']               = $request->mobile_no;
            $ins['address_line1']           = $request->address_line1;
            $ins['address_line2']           = $request->address_line2;
            $ins['landmark']                = $request->landmark;
            $ins['city_id']                    = $request->city;
            $ins['state_id']                   = $request->state;
            $ins['country_id']                 = $request->country	;
            $ins['post_code_id']               = $request->post_code;
           
            if($request->is_default)
            {
                $ins['is_default']          = "1";
                CustomerAddress::where('customer_id', $request->customer_id)->update(['is_default' => '0']);
            } else {
                $ins['is_default']          = "0";
            }
            $error                          = 0;
            $addressInfo = CustomerAddress::updateOrCreate(['id' => $id], $ins);
            $message                        = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } else {
            $error      = 1;
            $message    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message, 'customer_id' => $request->customer_id]);
    }

    public function addressList(Request $request)
    {
        $customerId         = $request->customerId;
        $customerAddress    = CustomerAddress::where('customer_id', $customerId)->get();
        $info               = Customer::find( $customerId);
// dd($customerAddress);
        return view('platform.customer.form.address._list', compact('customerAddress', 'info'));
    }
    public function addressDelete(Request $request)
    {
        // dd($request->addressId);
        $customerAddress    = CustomerAddress::where('id', $request->addressId)->delete();
        $message                        = 'Deleted Successfully';
        return response()->json(['message' => $message]);
    }
    public function wishlist(Request $request)
    {
        if($request->ajax())
        {
            $customerId = $request->customer_id;
            $data = Wishlist::select('wishlists.*','products.product_name','products.price')->where('customer_id',$customerId)
            ->leftJoin('products','products.id','=','wishlists.product_id');
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
            ->filter(function ($query) use ($keywords) {
                if ($keywords) {
                   
                    return $query->where(function($query) use ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                                    $query->orWhere('products.price', 'like', "%{$keywords}%");
                                    $query->orWhere('products.product_name', 'like', "%{$keywords}%");
                                    $query->orWhereDate("wishlists.created_at", $date);
                    });
                }
            })
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                return $created_at;
            });
            return $datatables->make(true);
        }
    }

    public function orderList(Request $request)
    {
        if($request->ajax())
        {
            $customerId = $request->customer_id;
            $data = Order::where('customer_id',$customerId);
            
            $keywords = $request->get('search')['value'];
            $datatables =  Datatables::of($data)
            ->filter(function ($query) use ($keywords) {
                if ($keywords) {
                   
                    return $query->where(function($query) use ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                                    $query->orWhere('orders.order_no', 'like', "%{$keywords}%");
                                    $query->orWhere('orders.amount', 'like', "%{$keywords}%");
                                    $query->orWhereDate("orders.created_at", $date);
                    });
                }
            })
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                return $created_at;
            })
            ->addColumn('shipping_info', function ($row) {
                
                $shipping_info = '<div><div><b>'.$row->shipping_name.'</b></div>';
                $shipping_info .= '<div>'.$row->shipping_address_line1.'</div>';
                $shipping_info .= '<div>'.$row->shipping_city.' '. $row->shipping_city.' '.$row->shipping_post_code.'  '.$row->shipping_country.'</div>';
                $shipping_info .= '<div>'.$row->shipping_email.', '.$row->shipping_mobile_no.'</div></div>';
                return $shipping_info;
                
            })
            ->addColumn('billing_info', function ($row) {
                $billing_info = '<div><b>'.$row->billing_name.'</b></div>';
                $billing_info .= '<div>'.$row->billing_address_line1.'</div>';
                $billing_info .= '<div>'.$row->billing_city.' '. $row->billing_city.' '.$row->billing_post_code.'  '.$row->billing_country.'</div>';
                $billing_info .= '<div>'.$row->billing_email.', '.$row->billing_mobile_no.'</div>';
                return $billing_info;
            })
            ->rawColumns(['shipping_info', 'billing_info']);
            return $datatables->make(true);
        }
    }
}
