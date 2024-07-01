<?php

namespace App\Http\Controllers\Product;

use App\Exports\ProductExport;
use App\Http\Controllers\Controller;
use App\Imports\MultiSheetRelatedProductImport;
use App\Imports\MultiSheetProductImport;
use App\Imports\StockUpdateImport;
use App\Imports\TestImport;
use App\Imports\UploadAttributes;
use Illuminate\Http\Request;
use App\Models\Category\MainCategory;
use App\Models\Master\Brands;
use App\Models\Product\Product;
use App\Models\Product\ProductAttributeSet;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductDescription;
use App\Models\Product\ProductCrossSaleRelation;
use App\Models\Product\ProductImage;
use App\Models\Product\ProductMeasurement;
use App\Models\Product\ProductLink;
use App\Models\Product\ProductMapAttribute;
use App\Models\Product\ProductMetaTag;
use App\Models\Product\ProductRelatedRelation;
use App\Models\Product\ProductWithAttributeSet;
use App\Exports\ProductAttributeSetBulkExport;
use App\Exports\RelatedProductAttributeSetExport;
use App\Models\Warranty;
use App\Repositories\ProductRepository;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Image;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Models\Product\ProductUrl;
use App\Models\Product\ProductDiscount;
use App\Models\Product\ProductVariationOption;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository    = $productRepository;
    }

    public function index(Request $request)
    {
        $title                  = "Product";
        $breadCrum              = array('Products', 'Product');

        if ($request->ajax()) {

            $f_product_category = $request->get('filter_product_category');
            $f_brand = $request->get('filter_brand');
            $f_label = $request->get('filter_label');
            $f_tags = $request->get('filter_tags');
            $f_stock_status = $request->get('filter_stock_status');
            $f_product_name = $request->get('filter_product_name');
            $f_product_status = $request->get('filter_product_status');

            $data = Product::leftJoin('brands', 'brands.id', '=', 'products.brand_id')->leftJoin('product_categories', 'product_categories.id', '=', 'products.category_id')
                ->select('products.*', 'brands.brand_logo', 'brands.brand_name', 'product_categories.name as category')->when($f_product_category, function ($q) use ($f_product_category) {
                    return $q->where('category_id', $f_product_category);
                })
                ->when($f_brand, function ($q) use ($f_brand) {
                    return $q->where('brands.id', $f_brand);
                })
                ->when($f_tags, function ($q) use ($f_tags) {
                    return $q->where('tag_id', $f_tags);
                })
                ->when($f_stock_status, function ($q) use ($f_stock_status) {
                    return $q->where('stock_status', $f_stock_status);
                })
                ->when($f_product_status, function ($q) use ($f_product_status) {
                    return $q->where('products.status', $f_product_status);
                })

                ->when($f_product_name, function ($q) use ($f_product_name) {
                    return $q->where(function ($qr) use ($f_product_name) {
                        $qr->where('product_name', 'like', "%{$f_product_name}%")
                            ->orWhere('sku', 'like', "%{$f_product_name}%")
                            ->orWhere('price', 'like', "%{$f_product_name}%");
                    });
                })
                ->when($f_label, function ($q) use ($f_label) {
                    return $q->where('label_id', $f_label);
                });

            $keywords = $request->get('search')['value'];

            $datatables = Datatables::of($data)
                ->filter(function ($query) use ($keywords) {

                    if ($keywords) {
                        $date = date('Y-m-d', strtotime($keywords));
                        $query->where(function ($que) use ($keywords, $date) {
                            $que->where('products.status', 'like', "%{$keywords}%")
                                ->orWhere('products.stock_status', 'like', "%{$keywords}%")
                                ->orWhere('brands.brand_name', 'like', "%{$keywords}%")
                                ->orWhere('product_categories.name', 'like', "%{$keywords}%")
                                ->orWhere('products.product_name', 'like', "%{$keywords}%")
                                ->orWhere('products.sku', 'like', "%{$keywords}%")
                                ->orWhere('products.price', 'like', "%{$keywords}%")
                                ->orWhereDate("products.created_at", $date);
                        });
                        return $query;
                    }
                })
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $status = '<a href="javascript:void(0);" class="badge badge-light-' . (($row->status == 'published') ? 'success' : 'danger') . '" tooltip="Click to ' . (($row->status == 'published') ? 'Unpublish' : 'Publish') . '" onclick="return commonChangeStatus(' . $row->id . ', \'' . (($row->status == 'published') ? 'unpublished' : 'published') . '\', \'products\')">' . ucfirst($row->status) . '</a>';
                    return $status;
                })
                ->editColumn('stock_status', function ($row) {
                    return ucwords(str_replace("_", " ", $row->stock_status));
                })
                ->editColumn('product_image', function ($row) {
                    $path = str_replace("\t", '', $row->base_image);
                    if (Storage::exists($path)) {
                        $image = '<img src="' .  asset(Storage::url($path)) . '" alt="Image" class="img-thumbnail" width="100">';
                        return $image;
                    } else {

                        $image = '<img src="' . asset('assets/logo/no_Image.jpg') . '" alt="Image" class="img-thumbnail" width="100">';
                        return $image;
                    }
                })
                // ->editColumn('brand', function($row){
                //     return $row->productBrand->brand_name ?? '';
                // })
                ->addColumn('action', function ($row) {
                    $edit_btn = '<a href="' . route('products.add.edit', ['id' => $row->id]) . '" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px" > 
                                    <i class="fa fa-edit"></i>
                                </a>';
                    $del_btn = '<a href="javascript:void(0);" onclick="return commonDelete(' . $row->id . ', \'products\')" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" > 
                <i class="fa fa-trash"></i></a>';

                    return $edit_btn . $del_btn;
                })

                ->rawColumns(['action', 'status', 'category', 'product_image']);


            return $datatables->make(true);
        }

        $addHref = route('products.add.edit');
        $uploadHref = route('products.upload');
        $routeValue = 'products';
        $productCategory        = ProductCategory::where('status', 'published')->get();
        $brands                 = Brands::where('status', 'published')->get();
        $productLabels          = MainCategory::where(['slug' => 'product-labels', 'status' => 'published'])->first();
        $productTags            = MainCategory::where(['slug' => 'product-tags', 'status' => 'published'])->first();

        $params                 = array(
            'title' => $title,
            'breadCrum' => $breadCrum,
            'addHref' => $addHref,
            'uploadHref' => $uploadHref,
            'routeValue' => $routeValue,
            'productCategory' => $productCategory,
            'brands' => $brands,
            'productLabels' => $productLabels,
            'productTags' => $productTags,
        );

        return view('platform.product.index', $params);
    }

    public function addEditPage(Request $request, $id = null)
    {

        $title                  = "Add Product";
        $breadCrum              = array('Products', 'Add Product');
        if ($id) {
            $title              = 'Update Product';
            $breadCrum          = array('Products', 'Update Product');
            $info               = Product::find($id);
        }
        $otherProducts          = Product::where('status', 'published')
            ->when($id, function ($q) use ($id) {
                return $q->where('id', '!=', $id);
            })->get();
        $productCategory        = ProductCategory::where('status', 'published')->get();
        $attributes             = ProductAttributeSet::where('status', 'published')->orderBy('order_by', 'ASC')->get();
        $warranties             = Warranty::where('status', 'published')->get();
        $productLabels          = MainCategory::where(['slug' => 'product-labels', 'status' => 'published'])->first();

        $productTags            = MainCategory::where(['slug' => 'product-tags', 'status' => 'published'])->first();
        $brands                 = Brands::where('status', 'published')->get();

        $images                 = $this->productRepository->getImageInfoJson($id);

        $brochures              = $this->productRepository->getBrochureJson($id);

        $params                 = array(

            'title' => $title,
            'breadCrum' => $breadCrum,
            'productCategory' => $productCategory,
            'productLabels' => $productLabels,
            'productTags' => $productTags,
            'brands' => $brands,
            'info'  => $info ?? '',
            'images' => $images,
            'brochures' => $brochures,
            'attributes' => $attributes,
            'otherProducts' => $otherProducts,
            'warranties' => $warranties

        );

        return view('platform.product.form.add_edit_form', $params);
    }

    public function urlDelete(Request $request)
    {
        $id         = $request->id;
        $info       = ProductUrl::find($id);
        $info->delete();
        return response()->json(['message' => "Successfully deleted Product!", 'status' => 1]);
    }

    public function saveForm(Request $request)
    {

        $id                 = $request->id;
        $product_page_type  = $request->product_page_type;
        $isUpdate           = false;
        $validate_array     = [
            'product_page_type' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'status' => 'required',
            //'sorting_order' => 'required',                                  
            // 'desc_image' => 'nullable|array', 
            //'desc_image' => 'max:150', 
            //'avatar' => 'nullable|array', 
            //'avatar' => 'max:150',

            'title' => 'nullable|array',
            'title.*' => 'nullable|required_with:title',
            'sorting_order' => 'nullable|required_with:title|array',
            'sorting_order.*' => 'nullable|required_with:title.*',
            'desc' => 'nullable|required_with:title|array',
            'desc.*' => 'nullable|required_with:title.*',
            'thumbnail_url' => 'nullable|array',
            'thumbnail_url.*' => 'url',
            'video_url' => 'nullable|array',
            'video_url.*' => 'url',
            'related_product' => 'min:5',
            'stock_status' => 'required',
            'product_name' => 'required_if:product_page_type,==,general',
            'base_price' => 'required_if:product_page_type,==,general',
            'mrp' => 'required_if:product_page_type,==,general',
            // 'strike_price' => 'required_if:product_page_type,==,general',
            'sku' => 'required_if:product_page_type,==,general|unique:products,sku,' . $id . ',id,deleted_at,NULL',
            'sale_price' => 'required_if:discount_option,==,percentage',
            'sale_price' => 'required_if:discount_option,==,fixed_amount',
            // 'sale_start_date' => 'required_if:sale_price,!=,0',
            // 'sale_end_date' => 'required_if:sale_price,==,0',
            'discount_percentage' => 'required_if:discount_option,==,fixed_amount',
            // 'filter_variation' => 'nullable|array',
            // 'filter_variation.*' => 'nullable|required_with:filter_variation',
            // 'filter_variation_value' => 'nullable|required_with:filter_variation|array',
            // 'filter_variation_value.*' => 'nullable|required_with:filter_variation.*',                                          
        ];

        if (isset($request->url) && !empty($request->url) && !is_null($request->url[0])) {
            // $validate_array['url'] = 'nullable|url|array';
            // $validate_array['url.*'] = 'nullable|url|required_with:url';
            // $validate_array['url_type'] = 'nullable|required_with:url|array';
            // $validate_array['url_type.*'] = 'nullable|required_with:url.*';

            $validate_array['url.*'] = 'required|url';
            $validate_array['url_type.*'] = 'required';
        }
        $validator      = Validator::make($request->all(), $validate_array);

        if ($validator->passes()) {

            // dd( $request->all() );

            if (isset($request->avatar_remove) && !empty($request->avatar_remove)) {
                $ins['base_image']          = null;
            }

            $ins['product_name']          = $request->product_name;
            $ins['hsn_code']              = $request->hsn_code;
            $ins['product_url']           = Str::slug($request->product_name);
            $ins['sku']                   = $request->sku;
            $ins['price']                 = $request->base_price;
            if ($request->mrp == 0) {
                $ins['mrp'] = round($request->strike_price ?? 0);
            } else {
                $ins['mrp'] = round($request->mrp ?? 0);
            }
            $ins['strike_price']          = $request->strike_price;
            $ins['status']                = $request->status;
            $ins['brand_id']              = $request->brand_id;
            $ins['category_id']           = $request->category_id;
            $ins['tag_id']                = $request->tag_id;
            $ins['label_id']              = $request->label_id;
            $ins['is_featured']           = $request->is_featured ?? 0;
            $ins['quantity']              = $request->qty;
            $ins['warranty_id']             = $request->warranty_id ?? null;
            $ins['stock_status']          = $request->stock_status;
            $ins['discount_percentage']     = getDiscountPercentage(round($request->mrp), round($request->strike_price));
            $ins['sale_price']            = $request->sale_price ?? 0;
            $ins['sale_start_date']       = $request->sale_start_date ?? null;
            $ins['sale_end_date']         = $request->sale_end_date ?? null;
            $ins['description']           = $request->product_description ?? null;
            $ins['technical_information'] = $request->product_technical_information ?? null;
            $ins['feature_information']   = $request->product_feature_information ?? null;
            $ins['specification']         = $request->product_specification ?? null;
            $ins['added_by']              = auth()->user()->id;
            $ins['no_of_items'] = $request->no_of_items ?? NULL;
            $ins['material_ingredients'] = $request->material_ingredients ?? NULL;
            $ins['features'] = $request->features ?? NULL;
            $ins['benefits'] = $request->benefits ?? NULL;

            $productInfo                    = Product::updateOrCreate(['id' => $id], $ins);
            $product_id = $productInfo->id;

            $desc_id = $request->desc_id ?? '';
            ProductDescription::where('product_id', $product_id)->delete();
            if (isset($request->title) && !empty($request->title)) {

                for ($i = 0; $i < count($request->title); $i++) {
                    $ins_desc_array = [];
                    $pro_desc_id = $desc_id[$i] ?? '';
                    if (isset($desc_id) && !empty($desc_id)) {
                        //  ProductDescription::where('product_id', $product_id)->whereNotIn('id', $desc_id)->delete();

                    }
                    if (isset($request->home_image[$i]) && !empty($request->home_image[$i])) {

                        $imageName                  = uniqid() . $request->home_image[$i]->getClientOriginalName();
                        $imageName = str_replace([' ', '  '], "_", $imageName);


                        $directory                  = 'products/' . $product_id . '/description';
                        //Storage::deleteDirectory('public/'.$directory);

                        if (!is_dir(storage_path("app/public/products/" . $product_id . "/description"))) {
                            mkdir(storage_path("app/public/products/" . $product_id . "/description"), 0775, true);
                        }

                        $fileNameThumb              = 'public/products/' . $product_id . '/description/' . time() . '-' . $imageName;
                        Image::make($request->home_image[$i])->save(storage_path('app/' . $fileNameThumb));
                        $ins_desc_array['desc_image'] = $fileNameThumb;
                    } else {
                        $ins_desc_array['desc_image'] = $request->old_image_name[$i];
                    }

                    $ins_desc_array['product_id'] = $product_id;
                    $ins_desc_array['title'] = $request->title[$i];
                    $ins_desc_array['description'] = $request->desc[$i];
                    $ins_desc_array['order_by'] = $request->sorting_order[$i];

                    //  ProductDescription::updateOrCreate(['id' => $pro_desc_id], $ins_desc_array);
                    ProductDescription::Create($ins_desc_array);
                }
            }

            if (!empty($id)) {
                $message                    = "Thank you! You've updated Products";
                $isUpdate                   = true;
            } else {
                $message                    = "Thank you! You've add Products";
            }
            $product_id                     = $productInfo->id;
            if ($request->hasFile('avatar')) {

                $imageName                  = uniqid() . $request->avatar->getClientOriginalName();
                $imageName = str_replace([' ', '  '], "_", $imageName);
                $directory                  = 'products/' . $product_id . '/default';
                Storage::deleteDirectory('public/' . $directory);

                if (!is_dir(storage_path("app/public/products/" . $product_id . "/default"))) {
                    mkdir(storage_path("app/public/products/" . $product_id . "/default"), 0775, true);
                }


                $fileNameThumb              = 'public/products/' . $product_id . '/default/' . time() . '-' . $imageName;
                Image::make($request->avatar)->save(storage_path('app/' . $fileNameThumb));

                $productInfo->base_image    = $fileNameThumb;
                $productInfo->update();
            }
            /**
             *
             * product gallery
             * 
             * 
             * */
            if ($request->hasFile('gallery') && isset($product_id)) {
                $image_order = ProductImage::where('product_id', $product_id)->orderBy('order_by', 'desc')->first();
                $files = $request->file('gallery');
                $imageIns = [];
                $iteration = 1;
                $order_no = ($image_order->order_by ?? 0) + 1;
                foreach ($files as $file) {

                    $imageName = uniqid() . $file->getClientOriginalName();
                    $imageName = str_replace([' ', '  '], "_", $imageName);

                    if (!is_dir(storage_path("app/public/products/" . $product_id . "/gallery"))) {
                        mkdir(storage_path("app/public/products/" . $product_id . "/gallery"), 0775, true);
                    }

                    $fileName =  'public/products/' . $product_id . '/gallery/' . time() . '-' . $imageName;
                    Image::make($file)->save(storage_path('app/' . $fileName));

                    $fileSize = $file->getSize();
                    $imageIns[] = array(
                        'gallery_path'  => $fileName,
                        'product_id'    => $product_id,
                        'file_size'     => $fileSize,
                        'is_default'    => ($iteration == 1) ? 1 : "0",
                        'order_by'      => $order_no,
                        'status'        => 'published'
                    );
                    $order_no++;
                    $iteration++;
                }
                if (!empty($imageIns)) {
                    ProductImage::insert($imageIns);
                }
            }

            $request->session()->put('image_product_id', $product_id);
            if (isset($request->filter_variation) && !empty($request->filter_variation)) {
                ProductMapAttribute::where('product_id', $product_id)->delete();

                $filter_variation = $request->filter_variation;
                $filter_variation_value = $request->filter_variation_value;
                $filter_variation_title = $request->filter_variation_title;
                $filter_variation_order = $request->filter_variation_order;
                ProductWithAttributeSet::where('product_id', $product_id)->delete();

                for ($i = 0; $i < count($request->filter_variation); $i++) {
                    $atIns = [];
                    $check = ProductMapAttribute::where('product_id', $product_id)->where('attribute_id', $filter_variation[$i])->first();
                    if (isset($check) && !empty($check)) {
                        $map_id = $check->id;
                    } else {

                        $atIns['product_id'] = $product_id;
                        $atIns['attribute_id'] = $filter_variation[$i];
                        $map_id = ProductMapAttribute::create($atIns)->id;
                    }

                    $insAttr = [];
                    $insAttr['product_attribute_set_id']    = $filter_variation[$i];
                    $insAttr['attribute_values']            = trim($filter_variation_value[$i]);
                    $insAttr['title']                       = trim($filter_variation_title[$i]);
                    $insAttr['order_by']                    = $filter_variation_order[$i] ?? null;
                    if (isset($_POST['is_overview_' . $i + 1]) && !empty($_POST['is_overview_' . $i + 1]) && $_POST['is_overview_' . $i + 1] == "1") {
                        $insAttr['is_overview']      = 'yes';
                    } else {
                        $insAttr['is_overview']      = 'no';
                    }

                    $insAttr['product_id']                  = $product_id;

                    ProductWithAttributeSet::create($insAttr);
                }
            }

            if (isset($request->kt_docs_repeater_nested_outer) && !empty($request->kt_docs_repeater_nested_outer)) {
                $kt_docs_repeater_nested_outer = $request->kt_docs_repeater_nested_outer;
                $product_id = $request->id;

                // Retrieve all existing options for the product and variation IDs
                $existingOptions = ProductVariationOption::where('product_id', $product_id)
                    ->whereIn('variation_id', collect($kt_docs_repeater_nested_outer)->pluck('variation_id')->filter())
                    ->get();

                // Loop through existing options and mark them as not found initially
                $optionsNotFound = $existingOptions->keyBy('id')->map(function ($option) {
                    $option->found = false;
                    return $option;
                });


                foreach ($kt_docs_repeater_nested_outer as $outer) {
                    if (!empty($outer['variation_id'])) {
                        $variation_id = $outer['variation_id'];
                        $kt_docs_repeater_nested_inner = $outer['kt_docs_repeater_nested_inner'];

                        foreach ($kt_docs_repeater_nested_inner as $inner) {
                            $value = $inner['variation_value'];
                            $amount = $inner['amount'];
                            $discount_amount = $inner['discount_amount'];
                            $is_default = $inner['is_default'][0] ?? 0;

                            $existingOption = ProductVariationOption::where('product_id', $product_id)
                                ->where('variation_id', $variation_id)
                                ->where('value', $value)
                                ->first();

                            if ($existingOption) {
                                // Update existing record
                                $existingOption->amount = $amount;
                                $existingOption->discount_amount = $discount_amount;
                                $existingOption->is_default = $is_default;
                                $existingOption->save();
                            } else {
                                // Insert new record
                                ProductVariationOption::create([
                                    'product_id' => $product_id,
                                    'variation_id' => $variation_id,
                                    'value' => $value,
                                    'amount' => $amount,
                                    'discount_amount' => $discount_amount,
                                    'is_default' => $is_default
                                ]);
                            }
                        }
                    }
                }
                // Delete options that were not found (not updated or inserted)
                $optionsNotFound->each(function ($option) {
                    if (!$option->found) {
                        $option->delete();
                    }
                });
            }

            $meta_ins['meta_title']         = $request->meta_title ?? '';
            $meta_ins['meta_description']   = $request->meta_description ?? '';
            $meta_ins['meta_keyword']       = $request->meta_keywords ?? '';
            $meta_ins['product_id']         = $product_id;
            ProductMetaTag::updateOrCreate(['product_id' => $product_id], $meta_ins);
            ProductRelatedRelation::where('from_product_id', $product_id)->delete();
            if (isset($request->related_product) && !empty($request->related_product)) {

                foreach ($request->related_product as $proItem) {
                    $insRelated['from_product_id'] = $product_id;
                    $insRelated['to_product_id'] = $proItem;
                    ProductRelatedRelation::create($insRelated);
                }
            }
            ProductCrossSaleRelation::where('from_product_id', $product_id)->delete();
            if (isset($request->cross_selling_product) && !empty($request->cross_selling_product)) {

                foreach ($request->cross_selling_product as $proItem) {
                    $insCrossRelated['from_product_id'] = $product_id;
                    $insCrossRelated['to_product_id'] = $proItem;
                    ProductCrossSaleRelation::create($insCrossRelated);
                }
            }
            ProductMeasurement::where('product_id', $product_id)->delete();
            if (isset($request->isShipping)) {

                $measure['product_id']  = $product_id;
                $measure['weight']    = $request->weight ?? 0;
                $measure['width']     = $request->width ?? 0;
                $measure['hight']     = $request->height ?? 0;
                $measure['length']    = $request->length ?? 0;
                ProductMeasurement::create($measure);
            }

            if (isset($request->url) && !empty($request->url) && !is_null($request->url[0])) {

                $url = $request->url;
                $url_type = $request->url_type;
                // $linkArr                        = array_combine($url_type, $url);
                if (isset($url) && !empty($url)) {

                    ProductLink::where('product_id', $product_id)->delete();
                    for ($i = 0; $i < count($url); $i++) {
                        $insAttr = [];
                        $insAttr['url']         = $url[$i];
                        $insAttr['url_type']    = $url_type[$i];
                        $insAttr['product_id']  = $product_id;

                        ProductLink::create($insAttr);
                    }
                }
            }
            if ((isset($request->thumbnail_url) && !empty($request->thumbnail_url)) ||  (isset($request->video_url) && !empty($request->video_url))) {
                $thumbnail_url = $request->thumbnail_url;
                $video_url = $request->video_url;
                $url_desc = $request->url_desc;
                $url_order_by = $request->url_order_by;
                if (isset($thumbnail_url) && !empty($thumbnail_url)) {
                    $check_url = ProductUrl::where('product_id', $product_id)->first();
                    if ($check_url) {
                        ProductUrl::where('product_id', $product_id)->delete();
                    }

                    for ($i = 0; $i < count($video_url); $i++) {
                        $insUrl = [];
                        $insUrl['product_id']         = $product_id;
                        $insUrl['thumbnail_url']      = $thumbnail_url[$i];
                        $insUrl['video_url']          = $video_url[$i];
                        $insUrl['description']        = $url_desc[$i];
                        $insUrl['order_by']           = $url_order_by[$i];

                        ProductUrl::create($insUrl);
                    }
                }
            }
            ProductDiscount::where('product_id', $product_id)->delete();
            if (isset($request->discount_option) && $request->discount_option != 1) {
                $disIns['product_id'] = $product_id;
                $disIns['discount_type'] = $request->discount_option;
                $disIns['discount_value'] = $request->discount_percentage ?? 0; //this is for percentage 
                $disIns['amount'] = $request->dicsounted_price ?? 0; //this only for fixed amount
                ProductDiscount::create($disIns);
            }

            $error                          = 0;
        } else {

            $error                          = 1;
            $message                        = errorArrays($validator->errors()->all());

            $product_id                     = '';
        }
        return response()->json(['error' => $error, 'isUpdate' => $isUpdate, 'message' => $message, 'product_id' => $product_id]);
    }

    public function uploadGallery(Request $request)
    {

        $product_id = $request->session()->pull('image_product_id');
        // dd( $request->all() );
        if ($request->hasFile('file') && isset($product_id)) {

            $files = $request->file('file');
            $imageIns = [];
            $iteration = 1;
            foreach ($files as $file) {

                $imageName = uniqid() . $file->getClientOriginalName();
                $imageName = str_replace([' ', '  '], "_", $imageName);

                if (!is_dir(storage_path("app/public/products/" . $product_id . "/gallery"))) {
                    mkdir(storage_path("app/public/products/" . $product_id . "/gallery"), 0775, true);
                }

                $fileName =  'public/products/' . $product_id . '/gallery/' . time() . '-' . $imageName;
                Image::make($file)->save(storage_path('app/' . $fileName));

                $fileSize = $file->getSize();
                $imageIns[] = array(
                    'gallery_path'  => $fileName,
                    'product_id'    => $product_id,
                    'file_size'     => $fileSize,
                    'is_default'    => ($iteration == 1) ? 1 : "0",
                    'order_by'      => $iteration,
                    'status'        => 'published'
                );

                $iteration++;
            }
            if (!empty($imageIns)) {

                ProductImage::insert($imageIns);
                echo 'Uploaded';
            }

            $request->session()->forget('image_product_id');
        } else {
            echo 'upload error';
        }
    }

    public function removeImage(Request $request)
    {

        $id             = $request->id;
        $info           = ProductImage::find($id);
        if (isset($info->gallery_path) && !empty($info->gallery_path)) {

            $directory      = 'products/' . $info->product_id . '/gallery/' . $info->gallery_path;
            Storage::delete('public/' . $directory);

            $info->delete();
        }
        echo 1;
        return true;
    }

    public function changeStatus(Request $request)
    {
        $id             = $request->id;
        $status         = $request->status;
        $info           = Product::find($id);
        $info->status   = $status;
        $info->update();

        return response()->json(['message' => "You changed the Product status!", 'status' => 1]);
    }

    public function delete(Request $request)
    {
        $id         = $request->id;
        $info       = Product::find($id);
        $info->delete();

        return response()->json(['message' => "Successfully deleted Product!", 'status' => 1]);
    }

    public function export()
    {
        return Excel::download(new ProductExport, 'product.xlsx');
    }

    public function exportPdf()
    {
        $list       = Product::all();
        $pdf        = PDF::loadView('platform.exports.product.products_excel', array('list' => $list, 'from' => 'pdf'))->setPaper('a2', 'landscape');;
        return $pdf->download('product_masters.pdf');
    }

    public function bulkUpload(Request $request)
    {

        $addHref        = route('products.add.edit');
        $uploadHref     = route('products.upload');
        $title          = "Product Bulk Upload";
        $breadCrum      = array('Products', 'Product Bulk Upload');

        $params         = array(
            'addHref' => $addHref,
            'uploadHref' => $uploadHref,
            'title' => $title,
            'breadCrum' => $breadCrum,
        );

        return view('platform.product.bulk_upload', $params);
    }

    public function doBulkUpload()
    {
        Excel::import(new MultiSheetProductImport, request()->file('file'));
        return response()->json(['error' => 0, 'message' => 'Imported successfully']);
    }
    public function doRelatedBulkUpload()
    {
        Excel::import(new MultiSheetRelatedProductImport, request()->file('file'));
        return response()->json(['error' => 0, 'message' => 'Imported successfully']);
    }

    public function doStockUpdate()
    {
        Excel::import(new StockUpdateImport, request()->file('file'));
        return response()->json(['error' => 0, 'message' => 'Imported successfully']);
    }

    public function doAttributesBulkUpload(Request $request)
    {
        Excel::import(new UploadAttributes, request()->file('file'));
        return response()->json(['error' => 0, 'message' => 'Imported successfully']);
    }

    public function getBaseMrpPrice(Request $request)
    {
        $category_id = $request->category_id;
        $price = $request->price;
        $inputField = $request->inputField;
        $tax = ProductCategory::find($category_id);

        if (isset($tax->tax) && !empty($tax->tax)) {

            $percentage = $tax->tax->pecentage;
            if ($inputField == 'mrp') {
                $price_info = getAmountExclusiveTax($price, $percentage);
            } else {
                $price_info = getAmountInclusiveTax($price, $percentage);
            }

            $message = 'Success';
            $error = 0;
        } else {
            $error = 1;
            $message = 'Please set Tax to Product Category';
        }

        return response()->json(['error' => $error, 'message' => $message, 'price_info' => $price_info ?? '']);
    }

    public function exportAttriuteSet()
    {
        return Excel::download(new ProductAttributeSetBulkExport, 'product_arrttiute_set.xlsx');
    }
    public function exportAttriuteSetRelatedProduct()
    {
        return Excel::download(new RelatedProductAttributeSetExport, 'related_product_arrttiute_set.xlsx');
    }

    public function changeImageOrder(Request $request)
    {

        $id = $request->id;
        $order_no = $request->order_no;
        if ($order_no) {
            $info           = ProductImage::find($id);
            $info->order_by = $request->order_no;
            $info->save();
            return array('error' => 1, 'message' => 'Image Order Changed successfully');
        }
    }
}
