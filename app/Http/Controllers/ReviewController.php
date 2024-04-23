<?php

namespace App\Http\Controllers;

use App\Exports\ReviewExport;
use App\Models\Product\Review;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Excel;
use Illuminate\Support\Facades\Validator;
class ReviewController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data       = Review::select('reviews.*','customers.first_name as customer_name','products.product_name as product_name')
            ->leftJoin('customers','customers.id','=','reviews.customer_id')
            ->leftJoin('products','products.id','=','reviews.product_id');
           
            $status     = $request->get('status');
            $keywords   = $request->get('search')['value'];

            $datatables =  DataTables::of($data)
                ->filter(function ($query) use ($keywords,$status) {
                    if ($status) {
                        return $query->where('reviews.status', '=', "$status");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('reviews.star', 'like', "%{$keywords}%")
                        ->orWhere('customers.first_name', 'like', "%{$keywords}%")
                        ->orWhere('reviews.comments', 'like', "%{$keywords}%")
                        ->orWhere('products.product_name', 'like', "%{$keywords}%")
                        ->orWhereDate("reviews.created_at", $date);
                    }
                })
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'approved') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'approved') ? 'Pending' : 'Approved').'" onclick="return commonChangeStatus(' . $row->id . ',\''.(($row->status == 'approved') ? 'pending': 'approved').'\', \'review\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })
    
                ->addColumn('action', function ($row) {
                 
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'review\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';
              
                    return $del_btn;
                })
                ->rawColumns(['action', 'status', 'created_at']);
            return $datatables->make(true);
        }
        $title                  = "Review";
        $breadCrum              = array('Customer Review');
        return view('platform.customer_review.index', compact('title', 'breadCrum'));
    }
    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = Review::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the Review status!",'status'=>1]);

    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = Review::find($id);
        $info->delete();
        
        return response()->json(['message'=>"Successfully deleted Review!",'status'=>1]);
    }
    public function export()
    {
        return Excel::download(new ReviewExport, 'Review.xlsx');
    }
}
