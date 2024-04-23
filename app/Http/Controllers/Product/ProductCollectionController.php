<?php

namespace App\Http\Controllers\Product;

use App\Exports\ProductCollectionExport;
use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Product\ProductCollection;
use App\Models\Product\ProductCollectionProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use DataTables;
use Excel;
use PDF;
use Image;
use Illuminate\Support\Str;

class ProductCollectionController extends Controller
{
    public function index(Request $request)
    {
        $title                  = "Product Collectons";
        $breadCrum              = array('Products', 'Product Collections');

        if ($request->ajax()) {
            $data               = ProductCollection::select('product_collections.*');
            $status             = $request->get('status');
            $keywords           = $request->get('search')['value'];
            $datatables         = Datatables::of($data)
            ->filter(function ($query) use ($keywords, $status) {
                if ($status) {
                    return $query->where('product_collections.status',$status);
                }
                if ($keywords) {
                    
                    if( !strpos($keywords, '.')) {
                        $date = date('Y-m-d', strtotime($keywords));
                    } 
                    $query->where('product_collections.collection_name', 'like', "%{$keywords}%");
                    if( isset( $date )) {
                        $query->orWhereDate("product_collections.created_at", $date);
                    }
                    
                    return $query;
                }
            })
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ', \''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'product-collection\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                ->addColumn('no_of_products', function ($row) {
                    return count($row->collectionProducts);
                })
                ->addColumn('can_map_discount', function ($row) {

                    $discount_name = $row->can_map_discount ?? 'No';
                    return ucfirst($discount_name);
                })
                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'product-collection\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                                    <i class="fa fa-edit"></i>
                                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'product-collection\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                                <i class="fa fa-trash"></i></a>';
                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'no_of_products', 'can_map_discount']);
            return $datatables->make(true);
        }
        return view('platform.product_collection.index', compact('title','breadCrum'));
    }

    public function modalAddEdit(Request $request)
    {
        
        $title              = "Add Product Collection";
        $breadCrum          = array('Products', 'Add Product Collection');

        $id                 = $request->id;
        $from               = $request->from;
        $info               = '';
        $modal_title        = 'Add Product Collection';
        $products           = Product::where('status', 'published')
                                ->when($id != '', function($q) use($id){
                                    $q->whereRaw('id not IN(SELECT product_id FROM `gbs_product_collections_products` where product_collection_id  != '.$id.')');
                                } )
                                ->when($id == '', function($q){
                                    $q->whereRaw('id not IN(SELECT product_id FROM `gbs_product_collections_products`)');
                                } )
                                ->where('stock_status', 'in_stock')
                                ->get();
        
        $productCategory    = ProductCollection::where('status', 'published')->get();

        if (isset($id) && !empty($id)) {
            $info           = ProductCollection::find($id);
            
            $modal_title    = 'Update Product Collection';
        }
        
        return view('platform.product_collection.add_edit_modal', compact('modal_title', 'breadCrum', 'info', 'products'));
    }

    public function saveForm(Request $request,$id = null)
    {
        
        $id             = $request->id;
        $validator      = Validator::make($request->all(), [
                            'collection_name' => 'required|string|unique:product_collections,collection_name,' . $id,
                            'collection_product' => 'required|array|min:5',
                            'image' => 'max:150'
                        ]);

        $categoryId         = '';
        if ($validator->passes()) {
            
            $ins['collection_name']     = $request->collection_name;
            $ins['order_by']            = $request->order_by;
            $ins['tag_line']            = $request->tag_line;
            $ins['show_home_page']      = $request->show_home_page ?? 'no';
            $ins['can_map_discount']    = $request->can_map_discount ?? 'no';
            $ins['is_handpicked_collection'] = $request->is_handpicked_collection ?? 'no';
            $ins['slug']                = Str::slug($request->collection_name);

            if($request->status)
            {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            $error                      = 0;
            $collectionInfo             = ProductCollection::updateOrCreate(['id' => $id], $ins);
            $collection_id              = $collectionInfo->id;
            if ($request->hasFile('image')) {

                $directory = 'productCollection/'.$collection_id;
                Storage::deleteDirectory('public/'.$directory);

                $file                   = $request->file('image');
                $imageName              = uniqid().str_replace(' ', '', $file->getClientOriginalName());
                if (!is_dir(storage_path("app/public/productCollection/".$collection_id))) {
                    mkdir(storage_path("app/public/productCollection/".$collection_id), 0775, true);
                }
                $collectionImage           = 'public/productCollection/'.$collection_id .'/' .$imageName;
                Image::make($file)->save(storage_path('app/' . $collectionImage));

                $collectionInfo->image     = $collectionImage;
                $collectionInfo->save();
            }
            
            if( isset($request->collection_product) && !empty($request->collection_product) ) {
                ProductCollectionProduct::where('product_collection_id', $collection_id)->delete();
                $iteration              = 1;
                foreach ( $request->collection_product as $proItem ) {
                    $insRelated['product_collection_id'] = $collection_id;
                    $insRelated['product_id']   = $proItem;
                    $insRelated['order_by']     = $iteration;
                    ProductCollectionProduct::create($insRelated);
                    $iteration++;
                }
            }
       
            $message                    = (isset($id) && !empty($id)) ? 'Updated Successfully' : 'Added successfully';
        } else {
            $error      = 1;
            $message    = $validator->errors()->all();
        }
        return response()->json(['error' => $error, 'message' => $message, 'categoryId' => $categoryId, 'from' => $request->from ?? '']);
    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = ProductCollection::find($id);
        $info->forceDelete();    
        return response()->json(['message'=>"Successfully deleted state!",'status'=>1]);
    }

    public function changeStatus(Request $request)
    {
        
        $id             = $request->id;
        $status         = $request->status;
        $info           = ProductCollection::find($id);
        $info->status   = $status;
        $info->update();
        
        return response()->json(['message'=>"You changed the status!",'status'=>1]);

    }

    public function export()
    {
        return Excel::download(new ProductCollectionExport, 'prodcutCollections.xlsx');
    }
    
    public function exportPdf()
    {
        $list       = ProductCollection::all();
        $pdf        = PDF::loadView('platform.exports.product.product_collection_excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('productCollections.pdf');
    }

}
