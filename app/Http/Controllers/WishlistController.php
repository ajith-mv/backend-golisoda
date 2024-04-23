<?php

namespace App\Http\Controllers;

use App\Exports\WishlistExport;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;


class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $title = "Wishlist";
        $breadCrum              = array('Wishlist', 'Wishlist');
        if ($request->ajax()) {
            $data               = Wishlist::select('wishlists.*','customers.first_name','products.product_name','products.price')
            ->join('customers','customers.id','=','wishlists.customer_id')
            ->leftJoin('products','products.id','=','wishlists.product_id');
            $status             = $request->get('status');
            $keywords           = $request->get('search')['value'];
            $datatables         =  Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($keywords) {
                            return $query->where(function($query) use ($keywords){
                                $query->orWhere('customers.first_name','like',"%{$keywords}%");
                                $query->orWhere('products.price','like',"%{$keywords}%");
                                $query->orWhere('products.product_name','like',"%{$keywords}%");
                            }); 
                    }
                })
                ->addIndexColumn()
            
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })

                ->addColumn('action', function ($row) {
           
        $view_btn = '<a href="javascript:void(0);"   onclick="return  openForm(\'wishlist\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
        <i class="fa fa-eye"></i>
    </a>';

                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'wishlist\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $view_btn . $del_btn;
                })
                ->rawColumns(['action']);
            return $datatables->make(true);
        }
        return view('platform.wishlist.index',compact('title','breadCrum'));
    }
    public function modalAddEdit(Request $request)
    {
        $breadCrum          = array('Products', 'Add Wishlist');
        $id                 = $request->id;
        $from               = $request->from;
        if (isset($id) && !empty($id)) {
            $info           = Wishlist::where('id',$id)->first();
            $modal_title    = 'Wishlist Details';
        }
        return view('platform.wishlist.view', compact('modal_title', 'breadCrum', 'info', 'from'));
    }
    
   
    public function export()
    {
        return Excel::download(new WishlistExport, 'wishlist.xlsx');

    }

}
