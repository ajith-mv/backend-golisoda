<?php

namespace App\Http\Controllers\Product;

use App\Exports\ProductAttributeSetExport;
use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Product\ProductAttributeSet;
use App\Models\Product\ProductCategory;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Excel;
use PDF;
use Illuminate\Validation\Rule;

class ProductAttributeSetController extends Controller
{

    public function index(Request $request)
    {
        $title                  = "Product Attribute Sets";
        $breadCrum              = array('Products', 'Product Attribute Sets');

        if ($request->ajax()) {
            $data               = ProductAttributeSet::select('product_attribute_sets.*');
            $status             = $request->get('status');
            $keywords           = $request->get('search')['value'];
            $datatables         = Datatables::of($data)
                ->filter(function ($query) use ($keywords, $status) {
                    if ($status) {
                        return $query->where('product_attribute_sets.status', $status);
                    }
                    if ($keywords) {
                    
                        if( !strpos($keywords, '.')) {
                            $date = date('Y-m-d', strtotime($keywords));
                        } 
                        $query->where('product_attribute_sets.title', 'like', "%{$keywords}%")
                        ->orWhere('product_attribute_sets.tag_line', 'like', "%{$keywords}%");
                        if( isset( $date )) {
                            $query->orWhereDate("product_attribute_sets.created_at", $date);
                        }
                        
                        return $query;
                    }
                })
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-'.(($row->status == 'published') ? 'success': 'danger').'" tooltip="Click to '.(($row->status == 'published') ? 'Unpublish' : 'Publish').'" onclick="return commonChangeStatus(' . $row->id . ', \''.(($row->status == 'published') ? 'unpublished': 'published').'\', \'product-attribute\')">'.ucfirst($row->status).'</a>';
                    return $status;
                })
                // ->addColumn('product_list', function($row){
                //     return ( isset( $row->is_use_in_product_listing ) && $row->is_use_in_product_listing == '1' ) ? 'Yes' : 'No';
                // })
                // ->addColumn('compare', function($row){
                //     return ( isset( $row->is_comparable ) && $row->is_comparable == '1' ) ? 'Yes' : 'No';
                // })
                ->addColumn('search', function($row){
                    return ( isset( $row->is_searchable ) && $row->is_searchable == '1' ) ? 'Yes' : 'No';
                })
                ->editColumn('created_at', function ($row) {
                    $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->format('d-m-Y');
                    return $created_at;
                })
                ->editColumn('category', function ($row) {
                    
                    return $row->category->name ?? null;
                })
                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="javascript:void(0);" onclick="return  openForm(\'product-attribute\',' . $row->id . ')" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                                    <i class="fa fa-edit"></i>
                                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'product-attribute\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                                <i class="fa fa-trash"></i></a>';
                    return $edit_btn . $del_btn;
                })
                ->rawColumns(['action', 'status', 'search', 'category']);
            return $datatables->make(true);
        }
        return view('platform.product_attribute_sets.index', compact('title','breadCrum'));
    }

    public function modalAddEdit(Request $request)
    {
        $id                 = $request->id;
        $from               = $request->from ?? '';
        $category_id        = $request->categoryId ?? '';
        $category_info      = ProductCategory::find($category_id);
        $info               = '';
        $modal_title        = 'Add Product Attribute Sets';
        $sub_category       = ProductCategory::where('parent_id', '0')->get();
        if (isset($id) && !empty($id)) {
            $info           = ProductAttributeSet::find($id);
            $modal_title    = 'Update Product Attribute Sets';
        }
        
        return view('platform.product_attribute_sets.add_edit_modal', compact('info', 'modal_title', 'from', 'sub_category', 'category_info'));
    }

    public function saveForm(Request $request,$id = null)
    {
        
        $id             = $request->id;
        $product_category_id    = $request->product_category_id;
        $validator      = Validator::make($request->all(), [
                            'title' => ['required','string',
                                            Rule::unique('product_attribute_sets')->where(function ($query) use($id, $product_category_id) {
                                                return $query->where('product_category_id', $product_category_id)->when($id != '', function($q) use($id){
                                                    return $q->where('id', '!=', $id);
                                                });
                                            }),],
                            
                            'product_category_id' => 'required'
                        ]
                    );

        $categoryId         = '';
        if ($validator->passes()) {
            
            $ins['title'] = $request->title;
            $ins['slug'] = Str::slug($request->title);
            $ins['product_category_id'] = $request->product_category_id;
            $ins['order_by'] = $request->order_by ?? 0;
            $ins['tag_line'] = $request->tag_line;
            $ins['is_searchable'] = $request->is_searchable ?? '0';
            $ins['is_comparable'] = $request->is_comparable ?? '0';
            $ins['is_use_in_product_listing'] = $request->is_use_in_product_listing ?? '0';

            if($request->status)
            {
                $ins['status']          = 'published';
            } else {
                $ins['status']          = 'unpublished';
            }
            $error                      = 0;
            $categeryInfo = ProductAttributeSet::updateOrCreate(['id' => $id], $ins);
            $categoryId = $categeryInfo->id;
            $views                      = '';
       
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
        $info       = ProductAttributeSet::find($id);
        $info->delete();    
                
        return response()->json(['message'=>"Successfully deleted state!",'status'=>1]);
    }

    public function changeStatus(Request $request)
    {
        
        $id             = $request->id;
        $status         = $request->status;
        $info           = ProductAttributeSet::find($id);
        $info->status   = $status;
        $info->update();
        
        return response()->json(['message'=>"You changed the status!",'status'=>1]);

    }

    public function export()
    {
        return Excel::download(new ProductAttributeSetExport, 'productAttributesSets.xlsx');
    }
    
    public function exportPdf()
    {

        $list       = ProductAttributeSet::all();
        $pdf        = PDF::loadView('platform.exports.product.product_attribute_excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a4', 'landscape');;
        return $pdf->download('productAttributesSets.pdf');

    }

    public function getAttributeRow(Request $request)
    {

        $product_id             = $request->product_id;
        $info                   = Product::find($product_id);
        $attributes             = ProductAttributeSet::where('status', 'published')->orderBy('order_by','ASC')->get();
        return view('platform.product.form.filter._items', compact('attributes', 'info'));
        
    }

}
