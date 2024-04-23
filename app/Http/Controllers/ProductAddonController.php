<?php

namespace App\Http\Controllers;

use App\Exports\ProductAddonExport;
use App\Models\Product\Product;
use App\Models\Product\ProductAddonProduct;
use Illuminate\Http\Request;
use App\Models\ProductAddon;
use App\Models\ProductAddonItem;
use Illuminate\Support\Facades\Storage;
use DataTables;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Excel;
use Image;
use App\Models\Product\ProductCategory;

class ProductAddonController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data       = ProductAddon::select('product_addons.*','product_addon_products.type')
            ->leftJoin('product_addon_products','product_addon_products.product_addon_id','=','product_addons.id')->groupBy('product_addon_products.product_addon_id');
            $status     = $request->get('status');
            $keywords   = $request->get('search')['value'];

            $datatables =  DataTables::of($data)
                ->filter(function ($query) use ($keywords,$status) {
                    if ($status) {
                        return $query->where('product_addons.status', '=', "$status");
                    }
                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        return $query->where('product_addons.title', 'like', "%{$keywords}%")
                        ->orWhere('product_addon_products.type', 'like', "%{$keywords}%")
                        ->orWhereDate("product_addons.created_at", $date);
                    }
                })
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ',\''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'product-addon\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })
    
                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'product-addon\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                    <i class="fa fa-edit"></i>
                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'product-addon\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';
              
                    return $edit_btn.$del_btn;
                })
                ->rawColumns(['action', 'status', 'created_at']);
            return $datatables->make(true);
        }
        $title                  = "Addons";
        $breadCrum              = array('Addons');
        return view('platform.product_addon.index', compact('title', 'breadCrum'));
    }

    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $from               = $request->from;
        $info               = '';
        $modal_title        = 'Add Product Addon';
        $product            = Product::select('id','product_name')->where('status','published')->get();
        $category           = ProductCategory::where('status','published')->get();
        $usedProduct = [];
        $usedCategory =[];
        $info_items='';
        if (isset($id) && !empty($id)) {
           
            $info           = ProductAddon::find($id);
            $info_items     = ProductAddonProduct::where('product_addon_id',$id)->first();
          
            $usedProduct    = array_column($info->addonProducts->toArray(), 'product_id');
            $usedCategory   = array_column($info->addonCategory->toArray(), 'product_id');
            
            $modal_title    = 'Update Product Addon';
        }

        return view('platform.product_addon.add_edit_modal', compact('info', 'modal_title', 'info_items','category' ,'from','product', 'usedProduct','usedCategory'));
    }

    public function saveForm(Request $request,$id = null)
    {
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                                'title' => 'required|string|unique:product_addons,title,' . $id . ',id,deleted_at,NULL',
                                'product_id' => 'required_if:add_on_type,==,product',
                                'category_id' => 'required_if:add_on_type,==,category',
                                'icon' => 'max:150'
                            ]);
        $banner_id      = '';
        if ($validator->passes()) {
            
            $ins['title']               = $request->title;
            $ins['description']         = $request->description;
            $ins['order_by']            = $request->order_by ?? 0;
            $ins['added_by']            = auth()->user()->id;

            if($request->status == "1")
            {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            
            $error                      = 0;
            $info                       = ProductAddon::updateOrCreate(['id' => $id], $ins);
            if ($request->hasFile('icon')) {
               
                $imageName               = time() . '_' . $request->icon->getClientOriginalName();
                
                $directory              = 'addons/'.$info->id;
                Storage::deleteDirectory('public/'.$directory);
                
                if (!is_dir(storage_path("app/public/addons/".$info->id))) {
                    mkdir(storage_path("app/public/addons/".$info->id), 0775, true);
                }

                $fileNameThumb              = 'public/addons/'.$info->id.'/' . time() . '-' . $imageName;
                Image::make($request->icon)->save(storage_path('app/' . $fileNameThumb));
                
                $info->icon = $fileNameThumb;
                $info->save();
            }
            
            if(!empty($request->label))
            {
                $dataItem = ProductAddonItem::where('product_addon_id',$id)->get();
                foreach($dataItem as $key=>$val)
                {
                    $val['status'] = 'unpublished';
                    $val->save();
                }
                $label  = $request->label;
                $amount = $request->amount;
                for($i = 0 ; $i < count($label) ; $i++)
                {
                    $ins['product_addon_id']        = $info->id;
                    $ins['label']                   = $label[$i] ?? '' ;
                    $ins['amount']                  = $amount[$i] ?? '' ;
                    $ins['status']                  = 'published';
                    $data = ProductAddonItem::updateOrCreate(['product_addon_id' => $id,'label' => $label[$i],'amount' => $amount[$i]], $ins);
                }
            }
            /**
             * store products
             */
            $add_on_type_id=[];
            if( $request->product_id)
            {
              
                if($request->product_id[0]=='all'){
                  $prod=  Product::select('id','product_name')->where('status','published')->get();
                    foreach($prod as $products_all)
                    {
                        $add_on_type_id[]=$products_all->id;
                    }
                }
                else
                {                
                    $add_on_type_id=$request->product_id;
                }
            }
            else if($request->category_id )
            {
                $add_on_type_id=$request->category_id;
            }
            if( $add_on_type_id  ) {               
                ProductAddonProduct::where('product_addon_id', $info->id )->delete();
                foreach ( $add_on_type_id as $item ) {
                    $pro = [];
                    $pro['product_addon_id'] = $info->id;
                    $pro['product_id'] = $item;
                    $pro['type']=$request->add_on_type;

                    ProductAddonProduct::create($pro);
                }
            }
        
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
        $info           = ProductAddon::find($id);
        $info->status   = $status;
        $info->update();
        return response()->json(['message'=>"You changed the Product Addon status!",'status'=>1]);

    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = ProductAddon::find($id);
        $info->delete();
        $infoItem      = ProductAddonItem::where('product_addon_id',$id)->delete();

        return response()->json(['message'=>"Successfully deleted Product Addon!",'status'=>1]);
    }

    public function export()
    {

        return Excel::download(new ProductAddonExport, 'ProductAddon.xlsx');
    }
}
