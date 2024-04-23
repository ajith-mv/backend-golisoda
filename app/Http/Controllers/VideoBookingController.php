<?php

namespace App\Http\Controllers;

use App\Exports\VideoBookingExport;
use App\Models\Master\Customer;
use App\Models\Product\Product;
use App\Models\VideoBooking;
use Illuminate\Http\Request;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Excel;
use PDF;
class VideoBookingController extends Controller
{
    public function index(Request $request)
    {

        $title                  = "Video Booking";
        $breadCrum              = array('Video Booking');
        if ($request->ajax()) {

            $data               = VideoBooking::select('video_bookings.*','customers.first_name as first_name','products.product_name as product_name')
                                    ->join('customers','video_bookings.customer_id','=', 'customers.id')
                                    ->leftjoin( 'products','video_bookings.product_id','=', 'products.id');
            $keywords           = $request->get('search')['value'];
            $datatables         =  Datatables::of($data)
                ->filter(function ($query) use ($keywords) {
                    if ($keywords) {
                            $date = date('Y-m-d', strtotime($keywords));
                            return $query->where('video_bookings.contact_name','like',"%{$keywords}%")->orWhere('video_bookings.contact_email', 'like', "%{$keywords}%")
                                    ->orWhere('customers.first_name', 'like', "%{$keywords}%")->orWhere('video_bookings.contact_phone', 'like', "%{$keywords}%")
                                    ->orWhere('products.product_name', 'like', "%{$keywords}%")
                                    ->orWhere('video_bookings.preferred_time', 'like', "%{$keywords}%")->orWhereDate("video_bookings.preferred_date", $date);
                    }
                })
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })
                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'video-booking\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'video-booking\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'product', 'customer']);
            return $datatables->make(true);
        }

        return view('platform.video_booking.index',compact('title','breadCrum'));

    }

    public function modalAddEdit(Request $request)
    {
        $title              = "Add Video Booking";
        $breadCrum          = array('Video Booking', 'Add Video Booking');

        $id                 = $request->id;
        $from               = $request->from;
        $info               = '';
        $modal_title        = 'Add Video Booking';
        $customer           = Customer::get();
        $product            = Product::get();
        if (isset($id) && !empty($id)) {
            $info           = VideoBooking::find($id);
            $modal_title    = 'Update Video Booking';
        }
        return view('platform.video_booking.add_edit_modal', compact('modal_title', 'breadCrum', 'info', 'from','customer','product'));
    }
     
    public function saveForm(Request $request,$id = null)
    {
        
        $id                         = $request->id;
        $validator                  = Validator::make($request->all(), [
                                        'customer_id' => 'required',
                                        'contact_name' => 'required',
                                        'contact_email' => 'required|email',
                                        'contact_phone' => 'required|numeric|digits:10',
                                        'reach_type' => 'required',
                                        'preferred_date' => 'required',
                                        'preferred_time' => 'required',
                                    ]);

        if ($validator->passes()) {
            
            $ins['customer_id']                 = $request->customer_id;
            $ins['contact_name']                = $request->contact_name;
            $ins['contact_email']               = $request->contact_email;
            $ins['contact_phone']               = $request->contact_phone;
            $ins['product_id']                  = $request->product_id;
            $ins['reach_type']                  = $request->reach_type;
            $ins['preferred_date']              = $request->preferred_date;
            $ins['preferred_time']              = $request->preferred_time;
            $error                              = 0;

            $info                               = VideoBooking::updateOrCreate(['id' => $id], $ins);
            $message                            = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } 
        else {
            $error                              = 1;
            $message                            = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message]);
    }
    
    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = VideoBooking::find($id);
        $info->delete();
        
        return response()->json(['message'=>"Successfully deleted state!",'status'=>1]);
    }
    public function export()
    {
        return Excel::download(new VideoBookingExport, 'video_booking.xlsx');
    }

    public function exportPdf()
    {
        $list       = VideoBooking::select('video_bookings.*',  'customers.first_name as first_name')
        ->join('customers', 'customers.id', '=', 'video_bookings.customer_id')->get();
        $pdf        = PDF::loadView('platform.exports.video_booking.excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('video_booking.pdf');
    }
}
