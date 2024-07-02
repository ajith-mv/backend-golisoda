<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category\SubCategory;
use App\Models\HomePageSetting\HomepageSetting;
use App\Models\HomePageSetting\HomepageSettingItems;
use App\Models\Product\Product;
use App\Models\Product\ProductAttributeSet;
use App\Models\Product\ProductCategory;
use App\Models\Product\ProductCollection;
use App\Models\Product\ProductWithAttributeSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Master\Brands;
class FilterController extends Controller
{

    public function getProductListCategory(Request $request)
    {

        $category_slug = $request->category_slug ?? '';
        $brand_slug = $request->brand_slug ?? '';
        /**
         * top menu
         */
        $response = [];
        if ($category_slug) {
            $category = ProductCategory::select('id', 'name', 'parent_id', 'slug', 'image','banner_image')->with('childCategory')->where('slug', $category_slug)->first();
            $top_slide_menu = [];
            if ($category) {
                $top_slide_menu['id'] = $category->id;
                $top_slide_menu['name'] = $category->name;
                $top_slide_menu['parent_id'] = $category->parent_id;
                $top_slide_menu['slug'] = $category->slug;
                $top_slide_menu['image'] = $category->image;
                $path=substr($category->banner_image, 0, -1);
                $imagePath=storage_path($path);
                if (file_exists($imagePath)) {
                     $top_slide_menu['banner_image']     =  NULL;
                 }else{
                    $top_slide_menu['banner_image']= asset(Storage::url($path));
                 }
                $tmp_cat = [];
                if (isset($category->childCategory) && !empty($category->childCategory)) {
                    foreach ($category->childCategory as $sub_item) {
                        if (count($sub_item->products) > 0) {
                            $tmp_cat[] = array('id' => $sub_item->id, 'name' => $sub_item->name, 'slug' => $sub_item->slug);
                        }
                    }
                }
                $top_slide_menu['child_category'] = $tmp_cat;
            }

            if (isset($category) && $category->parent_id != 0) {
                $top_category = ProductCategory::select('id', 'name', 'parent_id', 'slug', 'image','banner_image')->with('childCategory')->where('status', 'published')
                    ->where('parent_id', 0)
                    ->where('id', $category->parent_id)
                    ->first();

                $top_slide_menu = [];
                if ($top_category) {
                    $top_slide_menu['id'] = $top_category->id;
                    $top_slide_menu['name'] = $top_category->name;
                    $top_slide_menu['parent_id'] = $top_category->parent_id;
                    $top_slide_menu['slug'] = $top_category->slug;
                    $top_slide_menu['image'] = $top_category->image;
                    $path=substr($top_category->banner_image, 0, -1);
                    $imagePath=storage_path($path);
                    if (file_exists($imagePath)) {
                     $top_slide_menu['banner_image']     =  NULL;
                     }else{
                     $top_slide_menu['banner_image']= asset(Storage::url($path));
                     }
                    $tmp_cat = [];
                    if (isset($top_category->childCategory) && !empty($top_category->childCategory)) {
                        foreach ($top_category->childCategory as $sub_item) {
                            if (count($sub_item->products) > 0) {
                                $tmp_cat[] = array('id' => $sub_item->id, 'name' => $sub_item->name, 'slug' => $sub_item->slug);
                            }
                        }
                    }
                    $top_slide_menu['child_category'] = $tmp_cat;
                }
            } 
        }else if (isset($brand_slug) && !empty($brand_slug)) {
        $array = explode('_', $brand_slug);
        $brands = Brands::select('brands.id', 'brands.brand_name as name', 'brands.slug')->whereIn('slug',$array)->get();
        if(count($brands)>0){

        foreach($brands as $brand){
           $products=Product::where('brand_id',$brand->id)                 
            ->join('product_categories', 'product_categories.id', '=', 'products.category_id')
           ->where('product_categories.parent_id', '!=', 0)
            ->groupBy('products.category_id')
            ->distinct()
            ->get();
        foreach($products as $product){
            if($product->productCategory){
                $child_data=$product->productCategory;
            $tmp_cat[] = array('id' => $child_data->id, 'name' => $child_data->name, 'slug' => $child_data->slug);
            } 
           }
          }
$serializedObjects = array_map('serialize', $tmp_cat);
$uniqueSerializedObjects = array_unique($serializedObjects);
$uniqueObjects = array_map('unserialize', $uniqueSerializedObjects);
$top_slide_menu['child_category']= array_values($uniqueObjects);
          }
        } else {

            $top_category = ProductCategory::select('id', 'name', 'parent_id', 'slug', 'image','banner_image')
                ->where('status', 'published')
                ->where('parent_id', 0)
                ->where('slug', 'laptop')->first();

            $top_slide_menu = [];

            if ($top_category) {

                $top_slide_menu['id'] = $top_category->id;
                $top_slide_menu['name'] = $top_category->name;
                $top_slide_menu['parent_id'] = $top_category->parent_id;
                $top_slide_menu['slug'] = $top_category->slug;
                $top_slide_menu['image'] = $top_category->image;
                $top_slide_menu['banner_image'] = $top_category->banner_image;
                $top_slide_menu['child_category'] = [];

            }
        }
       
        return $top_slide_menu;
    }

    public function getFilterStaticSideMenu(Request $request)
    {
        $category_slug = $request->category_slug ?? '';
        $brand_slug = $request->brand_slug ?? '';

        $categories_data = Product::selectRaw('parent.name,parent.slug,parent.id,parent.parent_id')
            ->join('product_categories', 'product_categories.id', '=', 'products.category_id')
            ->join(DB::raw('gbs_product_categories parent'), 'product_categories.parent_id', '=', DB::raw('parent.id'))
            ->where('products.status', 'published')
            ->where('products.stock_status', 'in_stock')
            ->whereNotNull(DB::raw('parent.id'))
            ->groupBy(DB::raw('parent.id'))
            ->get();

        $all_category = ProductCategory::where('status', 'published')->where('parent_id', 0)->get();

        $category = [];
        $categories = [];
        $all_category = ProductCategory::where('status', 'published')->where('parent_id', 0)->get();

        $category = [];
        if (isset($all_category) && !empty($all_category)) {
            foreach ($all_category as $cat_item) {

                $category[$cat_item->id] = array('id' => $cat_item->id, 'name' => $cat_item->name, 'slug' => $cat_item->slug);
                $category[$cat_item->id]['child'] = [];
                // dump( $cat_item->childCategory );
                if( isset( $cat_item->childCategory ) && !empty( $cat_item->childCategory ) ) {
                    foreach ($cat_item->childCategory as $sub_item) {
                                             
                        if( count($sub_item->products ) > 0 ) {                            
                            $category[$cat_item->id]['child'][] = array('id' => $sub_item->id, 'name' => $sub_item->name, 'slug' => $sub_item->slug);
                        }

                    }
                }
            }
        }
        if (!empty($category)) {
            foreach ($category as $key => $value) {

                $categories[] = $value;
            }
        }


        $get_max_discounts = Product::selectRaw('max(abs(gbs_products.discount_percentage)) as discount')
            ->where('status', 'published')->where('stock_status', 'in_stock')->first();
        $discounts = [];
        if ($get_max_discounts->discount) {

            $discount_range = $get_max_discounts->discount / 3;
            $second_range = (round($discount_range) +  round($discount_range));
            $discounts = array(
                array('name' => 'below ' . round($discount_range) . '%', 'slug' => '0-' . round($discount_range)),
                array('name' => round($discount_range) . '% To ' . $second_range . '%', 'slug' =>  round($discount_range) . '-' . $second_range),
                array('name' => $second_range . '% To ' . round($get_max_discounts->discount) . '%', 'slug' => $second_range . '-' . round($get_max_discounts->discount)),
            );
        }
        /**
         * over all attributes
         */

        /** 
         * size filter actions
         */
        $size_data = ProductWithAttributeSet::select('product_with_attribute_sets.attribute_values')
            ->join('products', 'products.id', '=', 'product_with_attribute_sets.product_id')
            ->where('product_with_attribute_sets.title', 'Size')->groupByRaw("SUBSTRING_INDEX(gbs_product_with_attribute_sets.attribute_values,' ', 1)")->get();
        $sizes = [];
        if (isset($size_data) && !empty($size_data)) {
            foreach ($size_data as $size_item) {
                $int_var = explode(' ', $size_item->attribute_values);
                $tmp = [];
                $tmp = array('name' => $size_item->attribute_values, 'slug' => current($int_var));
                $sizes[] = $tmp;
            }
        }

        $sort_by                = array(
            // array('id' => null, 'name' => 'Featured', 'slug' => 'is-featured'),
            array('id' => null, 'name' => 'Price: High to Low', 'slug' => 'prices-high-to-low'),
            array('id' => null, 'name' => 'Price: Low to High', 'slug' => 'prices-low-to-high'),
        );

        $collection             = ProductCollection::select('product_collections.id', 'product_collections.collection_name as name', 'product_collections.slug')
            ->join('product_collections_products', 'product_collections_products.product_collection_id', '=', 'product_collections.id')
            ->join('products', 'products.id', '=', 'product_collections_products.product_id')
            ->where('products.stock_status', 'in_stock')
            ->where('products.status', 'published')
            ->where('product_collections.can_map_discount', 'no')
            ->where('product_collections.show_home_page', 'yes')
            ->where('product_collections.is_handpicked_collection', 'no')
            ->where('product_collections.status', 'published')
            ->orderBy('product_collections.order_by', 'asc')
            ->groupBy('product_collections.id')
            ->get()->toArray();

        $handpicked = ProductCollection::select('product_collections.id', 'product_collections.collection_name as name', 'product_collections.slug')
            ->join('product_collections_products', 'product_collections_products.product_collection_id', '=', 'product_collections.id')
            ->join('products', 'products.id', '=', 'product_collections_products.product_id')
            ->where('products.stock_status', 'in_stock')
            ->where('products.status', 'published')
            ->where('product_collections.is_handpicked_collection', 'yes')
            ->where('product_collections.show_home_page', 'yes')
            ->where('product_collections.status', 'published')
            ->orderBy('product_collections.order_by', 'asc')
            ->groupBy('product_collections.id')
            ->get()->toArray();

        $browse                     = [];
        $browse_filed_data          = HomepageSetting::where('title', 'Browse By Price')->where('status', 'published')->orderBy('order_by', 'asc')->first();

        if ($browse_filed_data) {

            $parent = [];

            $items_field = HomepageSettingItems::where('homepage_settings_id', $browse_filed_data->id)->get();

            foreach ($items_field as $key => $data_field) {
                $tmp = [];
                $tmp['name'] = ($data_field->start_size == 0 ? 'Below ' : $data_field->start_size) . '-' . ($data_field->end_size == 0 ? 'Above' : $data_field->end_size);
                $tmp['slug'] = $data_field->start_size . '-' . $data_field->end_size;
                $parent[] = $tmp;
            }
        }

        $browse = $parent;

        $attr_response = $this->getAttributeFilter($category_slug,$brand_slug);

        $response['exclusive'] =  [array('id' => null, 'name' => 'Goli Soda', 'slug' => 'goli-soda')];
        $response['categories'] =  $categories;
        if (!empty($attr_response['brands'])) {
            $response['brands'] =  $attr_response['brands'];
        }
        $response['discounts'] = $discounts;
        if (!empty($prices)) {
            $response['prices'] = $browse;
        }
        if (!empty($sizes)) {
            $response['sizes'] = $sizes;
        }
        $new_array = array_merge($response, $attr_response['attributes']);
        $new_array['collection'] = $collection;
        $new_array['handpicked'] = $handpicked;

        // dd( $attr_response['attributes'] );
        // dd( $new_array );
        // $response['sort_by'] =  $sort_by;          

        return $new_array;
    }

    public function getProducts(Request $request)
    {
        $page                   = $request->page ?? 0;
        $take                   = $request->take ?? 12;
        $filter_category        = $request->categories;
        $filter_sub_category    = $request->scategory;
        $filter_availability    = $request->availability;
        $filter_brand           = $request->brands;
        $filter_discount        = $request->discounts;
        $filter_discount_collection = $request->discount_collection;
        $filter_collection      = $request->collection;
        $fitler_handpicked      = $request->handpicked;
        // $filter_attribute       = $request->attribute_category ?? '';
        $sort                   = $request->sort_by;
        $price                  = $request->prices;
        $size                   = $request->sizes;
        $exclusive              = $request->exclusive ?? '';
        $search                 = $request->search ?? '';

        $not_in_attributes = array('page','search' ,'take', 'categories', 'scategory', 'brands', 'discounts', 'sort_by', 'prices', 'sizes', 'size', 'customer_id', 'collection', 'handpicked', 'discount_collection', 'exclusive');
        $from_request = $request->all();

        $filter_attribute = [];
        if ($from_request) {
            foreach ($from_request as $key => $value) {

                if (in_array($key, $not_in_attributes)) {
                } else {
                    $filter_attribute[$key] = $value;
                }
            }
        }

        $filter_availability_array = [];
        $filter_attribute_array = [];
        $filter_brand_array = [];
        $filter_discount_array = [];
        $filter_price_array = [];
        $filter_size_array = [];
        $filter_collection_array = [];
        $filter_handpicked_array = [];
        $tmp_price = [];

        $price_start = 0;
        $price_end = 0;

        if (isset($price) && !empty($price)) {
            $filter_price_array = explode("_", $price);
        }

        if (isset($filter_collection) && !empty($filter_collection)) {
            $filter_collection_array = explode("_", $filter_collection);
        }

        if (isset($fitler_handpicked) && !empty($fitler_handpicked)) {
            $filter_handpicked_array = explode("_", $fitler_handpicked);
        }

        // dd( $filter_collection_array);

        if (isset($filter_availability) && !empty($filter_availability)) {
            $filter_availability_array = explode("-", $filter_availability);
        }
        if (isset($filter_brand) && !empty($filter_brand)) {
            $filter_brand_array     = explode("_", $filter_brand);
        }
        if (isset($size) && !empty($size)) {
            $filter_size_array     = explode("_", $size);
        }

        $discount_start_value = '';
        $discount_end_value = '';

        if (isset($filter_discount) && !empty($filter_discount)) {
            $filter_discount_array     = explode("_", $filter_discount);
            if (!empty($filter_discount_array)) {
                $dis_array = [];
                foreach ($filter_discount_array as $dis_arr) {
                    $dis_array = array_merge(explode('-', $dis_arr), $dis_array);
                }
            }
            if (!empty($dis_array)) {
                $dis_array = array_unique($dis_array);
                sort($dis_array);

                $discount_start_value = current($dis_array);
                $discount_end_value = end($dis_array);
            }
        }


        $productAttrNames = [];
        if (isset($filter_attribute_array) && !empty($filter_attribute_array)) {
            $productWithData = ProductWithAttributeSet::whereIn('id', $filter_attribute_array)->get();
            if (isset($productWithData) && !empty($productWithData)) {
                foreach ($productWithData as $attr) {
                    $productAttrNames[] = $attr->title;
                }
            }
        }

        $take_limit = $take ?? 12;
        $total_data = Product::select('products.*')->where('products.status', 'published')
            ->join('product_categories', 'product_categories.id', '=', 'products.category_id')
            ->leftJoin('product_categories as parent', 'parent.id', '=', 'product_categories.parent_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->leftJoin('product_variation_options', function ($join) {
                $join->on('product_variation_options.product_id', '=', 'products.id')
                     ->where('product_variation_options.is_default', 1); // Consider only default variations
            })
            ->selectRaw('MIN(gbs_products.mrp + COALESCE(gbs_product_variation_options.amount - gbs_product_variation_options.discount_amount, 0)) AS min_value, MAX(gbs_products.mrp + COALESCE(gbs_product_variation_options.amount - gbs_product_variation_options.discount_amount, 0)) AS max_value')
            ->when($filter_category != '', function ($q) use ($filter_category) {
                $q->where(function ($query) use ($filter_category) {
                    return $query->where('product_categories.slug', $filter_category)->orWhere('parent.slug', $filter_category);
                });
            })
            ->when($exclusive == 'gbs', function ($q) {
                $q->join('sub_categories', 'sub_categories.id', '=', 'products.label_id');
                $q->where('sub_categories.slug', 'gbs');
            })
            ->when($filter_sub_category != '', function ($q) use ($filter_sub_category) {
                return $q->where('product_categories.slug', $filter_sub_category);
            })
            ->when($filter_availability != '', function ($q) use ($filter_availability_array) {
                return $q->whereIn('products.stock_status', $filter_availability_array);
            })
            ->when($filter_brand != '', function ($q) use ($filter_brand_array) {
                return $q->whereIn('brands.slug', $filter_brand_array);
            })

            ->when(!empty($filter_attribute)  || !empty($filter_size_array), function ($q) {
                $q->join('product_with_attribute_sets', 'product_with_attribute_sets.product_id', '=', 'products.id');
            })
            ->when($discount_start_value != '' && $discount_end_value != '', function ($q) use ($discount_start_value, $discount_end_value) {

                $q->where(function ($query) use ($discount_start_value, $discount_end_value) {
                    return $query->whereRaw('ABS(gbs_products.discount_percentage) >= ' . $discount_start_value)
                        ->whereRaw('ABS(gbs_products.discount_percentage) <= ' . $discount_end_value);
                });
            })
            ->when(!empty($filter_size_array) || !empty($filter_attribute), function ($q) use ($filter_size_array, $filter_attribute) {

                $q->where(function ($query) use ($filter_size_array, $filter_attribute) {
                    if (count($filter_size_array) > 0) {
                        $query->where(function ($query1) use ($filter_size_array, $filter_attribute) {

                            $query1->where('product_with_attribute_sets.title', 'size');
                            $query1->where(function ($query2) use ($filter_size_array) {

                                $i = 1;
                                foreach ($filter_size_array as $size_arr) {
                                    if ($i == 1) {
                                        $query2->where('product_with_attribute_sets.attribute_values', 'like', "%{$size_arr}%");
                                    } else {
                                        $query2->orWhere('product_with_attribute_sets.attribute_values', 'like', "%{$size_arr}%");
                                    }
                                    $i++;
                                }
                            });
                        });
                    }

                    if (count($filter_attribute) > 0 && count($filter_size_array) > 0) {
                        $query->orWhere(function ($query3) use ($filter_size_array, $filter_attribute) {
                            $loop_count = 4;
                            foreach ($filter_attribute as $tkey => $tvalue) {
                                $attr_title = str_replace(['-', '_'], ' ', $tkey);
                                $attribute_values_sub = str_replace('-', ' ', explode('_', $tvalue));
                                $whereloop = '$query' . $loop_count;
                                $query3->orWhere(function ($whereloop) use ($attr_title, $attribute_values_sub) {
                                    $whereloop->where('product_with_attribute_sets.title', $attr_title);
                                    $whereloop->whereIn('product_with_attribute_sets.attribute_values', $attribute_values_sub);
                                });
                                $loop_count++;
                            }
                        });
                    } else {
                        $query->where(function ($query3) use ($filter_size_array, $filter_attribute) {
                            $loop_count = 4;
                            foreach ($filter_attribute as $tkey => $tvalue) {
                                $attr_title = str_replace(['-', '_'], ' ', $tkey);
                                $attribute_values_sub = str_replace('-', ' ', explode('_', $tvalue));
                                $whereloop = '$query' . $loop_count;
                                $query3->orWhere(function ($whereloop) use ($attr_title, $attribute_values_sub) {
                                    $whereloop->where('product_with_attribute_sets.title', $attr_title);
                                    $whereloop->whereIn('product_with_attribute_sets.attribute_values', $attribute_values_sub);
                                });
                                $loop_count++;
                            }
                        });
                    }
                });
            })

            ->when($filter_price_array != '', function ($q) use ($filter_price_array) {
                // dd( $filter_price_array );
                if (count($filter_price_array) > 0) {
                    $j = 1;
                    foreach ($filter_price_array as $price_var) {
                        $test_price = explode('-', $price_var);
                        if ($j == 1) {

                            $q->where(function ($query) use ($test_price) {
                                return $query->where('products.mrp', '>=', current($test_price))
                                    ->where('products.mrp', '<=', end($test_price));
                            });
                        } else {
                            $q->orWhere(function ($query) use ($test_price) {
                                return $query->where('products.mrp', '>=', current($test_price))
                                    ->where('products.mrp', '<=', end($test_price));
                            });
                        }
                        $j++;
                    }
                }
            })
            ->when((!empty($filter_collection_array) || !empty($filter_handpicked_array) || !empty($filter_discount_collection)), function ($q) {
                $q->leftJoin('product_collections_products', 'product_collections_products.product_id', '=', 'products.id');
                $q->leftJoin('product_collections', 'product_collections.id', '=', 'product_collections_products.product_collection_id');
            })
            ->when(!empty($filter_collection_array), function ($q) use ($filter_collection_array) {
                $q->whereIn('product_collections.slug', $filter_collection_array);
            })
            ->when(!empty($filter_handpicked_array), function ($q) use ($filter_handpicked_array) {
                $q->whereIn('product_collections.slug', $filter_handpicked_array);
            })
            ->when(!empty($filter_discount_collection), function ($q) use ($filter_discount_collection) {
                $q->where('product_collections.slug', $filter_discount_collection);
            })
            ->when($sort == 'price-high-to-low', function ($q) {
                $q->orderBy('products.mrp', 'desc');
            })
            ->when($sort == 'price-low-to-high', function ($q) {
                $q->orderBy('products.mrp', 'asc');
            })
            ->when($sort == 'is_featured', function ($q) {
                $q->orderBy('products.is_featured', 'desc');
            })
            ->when($sort == 'a-to-z', function ($q) {
                $q->orderBy('products.product_name', 'asc');
            })
            ->when($sort == 'z-to-a', function ($q) {
                $q->orderBy('products.product_name', 'asc');
            })
            ->when(!empty($search), function($q) use($search){
                $q->where(function($query) use($search) {
                    $query->where('products.product_name', 'like', "%{$search}%")
                    ->orWhere('products.sku', 'like', "%{$search}%")
                    ->orWhere('products.hsn_code', 'like', "%{$search}%")
                    ->orWhere('products.price', 'like', "%{$search}%")
                    ->orWhere('product_categories.name', 'like', "%{$search}%")
                    ->orWhere('brands.brand_name', 'like', "%{$search}%");
                });
            } )
            ->where('products.stock_status', 'in_stock')
            ->groupBy('products.id')
            // ->selectRaw('MIN(mrp) AS min_value, MAX(mrp) AS max_value')
            ->get();

        $total = count($total_data);

        $details = Product::select('products.*')->where('products.status', 'published')
            ->join('product_categories', 'product_categories.id', '=', 'products.category_id')
            ->leftJoin('product_categories as parent', 'parent.id', '=', 'product_categories.parent_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->when($filter_category != '', function ($q) use ($filter_category) {
                $q->where(function ($query) use ($filter_category) {
                    return $query->where('product_categories.slug', $filter_category)->orWhere('parent.slug', $filter_category);
                });
            })
            ->when($exclusive == 'gbs', function ($q) {
                $q->join('sub_categories', 'sub_categories.id', '=', 'products.label_id');
                $q->where('sub_categories.slug', 'gbs');
            })
            ->when($filter_sub_category != '', function ($q) use ($filter_sub_category) {
                return $q->where('product_categories.slug', $filter_sub_category);
            })
            ->when($filter_availability != '', function ($q) use ($filter_availability_array) {
                return $q->whereIn('products.stock_status', $filter_availability_array);
            })
            ->when($filter_brand != '', function ($q) use ($filter_brand_array) {
                return $q->whereIn('brands.slug', $filter_brand_array);
            })

            ->when(!empty($filter_attribute)  || !empty($filter_size_array), function ($q) {
                $q->join('product_with_attribute_sets', 'product_with_attribute_sets.product_id', '=', 'products.id');
            })
            ->when($discount_start_value != '' && $discount_end_value != '', function ($q) use ($discount_start_value, $discount_end_value) {

                $q->where(function ($query) use ($discount_start_value, $discount_end_value) {
                    return $query->whereRaw('ABS(gbs_products.discount_percentage) >= ' . $discount_start_value)
                        ->whereRaw('ABS(gbs_products.discount_percentage) <= ' . $discount_end_value);
                });
            })
            ->when(!empty($filter_size_array) || !empty($filter_attribute), function ($q) use ($filter_size_array, $filter_attribute) {

                $q->where(function ($query) use ($filter_size_array, $filter_attribute) {
                    if (count($filter_size_array) > 0) {
                        $query->where(function ($query1) use ($filter_size_array, $filter_attribute) {

                            $query1->where('product_with_attribute_sets.title', 'size');
                            $query1->where(function ($query2) use ($filter_size_array) {

                                $i = 1;
                                foreach ($filter_size_array as $size_arr) {
                                    if ($i == 1) {
                                        $query2->where('product_with_attribute_sets.attribute_values', 'like', "%{$size_arr}%");
                                    } else {
                                        $query2->orWhere('product_with_attribute_sets.attribute_values', 'like', "%{$size_arr}%");
                                    }
                                    $i++;
                                }
                            });
                        });
                    }

                    if (count($filter_attribute) > 0 && count($filter_size_array) > 0) {
                        $query->orWhere(function ($query3) use ($filter_size_array, $filter_attribute) {
                            $loop_count = 4;
                            foreach ($filter_attribute as $tkey => $tvalue) {
                                $attr_title = str_replace(['-', '_'], ' ', $tkey);
                                $attribute_values_sub = str_replace('-', ' ', explode('_', $tvalue));
                                $whereloop = '$query' . $loop_count;
                                $query3->orWhere(function ($whereloop) use ($attr_title, $attribute_values_sub) {
                                    $whereloop->where('product_with_attribute_sets.title', $attr_title);
                                    $whereloop->whereIn('product_with_attribute_sets.attribute_values', $attribute_values_sub);
                                });
                                $loop_count++;
                            }
                        });
                    } else {
                        $query->where(function ($query3) use ($filter_size_array, $filter_attribute) {
                            $loop_count = 4;
                            foreach ($filter_attribute as $tkey => $tvalue) {
                                $attr_title = str_replace(['-', '_'], ' ', $tkey);
                                $attribute_values_sub = str_replace('-', ' ', explode('_', $tvalue));
                                $whereloop = '$query' . $loop_count;
                                $query3->orWhere(function ($whereloop) use ($attr_title, $attribute_values_sub) {
                                    $whereloop->where('product_with_attribute_sets.title', $attr_title);
                                    $whereloop->whereIn('product_with_attribute_sets.attribute_values', $attribute_values_sub);
                                });
                                $loop_count++;
                            }
                        });
                    }
                });
            })

            ->when($filter_price_array != '', function ($q) use ($filter_price_array) {
                // dd( $filter_price_array );
                if (count($filter_price_array) > 0) {
                    $j = 1;
                    foreach ($filter_price_array as $price_var) {
                        $test_price = explode('-', $price_var);
                        if ($j == 1) {

                            $q->where(function ($query) use ($test_price) {
                                return $query->where('products.mrp', '>=', current($test_price))
                                    ->where('products.mrp', '<=', end($test_price));
                            });
                        } else {
                            $q->orWhere(function ($query) use ($test_price) {
                                return $query->where('products.mrp', '>=', current($test_price))
                                    ->where('products.mrp', '<=', end($test_price));
                            });
                        }
                        $j++;
                    }
                }
            })
            ->when((!empty($filter_collection_array) || !empty($filter_handpicked_array) || !empty($filter_discount_collection)), function ($q) {
                $q->leftJoin('product_collections_products', 'product_collections_products.product_id', '=', 'products.id');
                $q->leftJoin('product_collections', 'product_collections.id', '=', 'product_collections_products.product_collection_id');
            })
            ->when(!empty($filter_collection_array), function ($q) use ($filter_collection_array) {
                $q->whereIn('product_collections.slug', $filter_collection_array);
            })
            ->when(!empty($filter_handpicked_array), function ($q) use ($filter_handpicked_array) {
                $q->whereIn('product_collections.slug', $filter_handpicked_array);
            })
            ->when(!empty($filter_discount_collection), function ($q) use ($filter_discount_collection) {
                $q->where('product_collections.slug', $filter_discount_collection);
            })
            ->when($sort == 'price-high-to-low', function ($q) {
                $q->orderBy('products.mrp', 'desc');
            })
            ->when($sort == 'price-low-to-high', function ($q) {
                $q->orderBy('products.mrp', 'asc');
            })
            ->when($sort == 'a-to-z', function ($q) {
                $q->orderBy('products.product_name', 'asc');
            })
            ->when($sort == 'z-to-a', function ($q) {
                $q->orderBy('products.product_name', 'desc');
            })
            ->when($sort == 'is_featured', function ($q) {
                $q->orderBy('products.is_featured', 'desc');
            })
            ->when(!empty($search), function($q) use($search){
                $q->where(function($query) use($search) {
                    $query->where('products.product_name', 'like', "%{$search}%")
                    ->orWhere('products.sku', 'like', "%{$search}%")
                    ->orWhere('products.hsn_code', 'like', "%{$search}%")
                    ->orWhere('products.price', 'like', "%{$search}%")
                    ->orWhere('product_categories.name', 'like', "%{$search}%")
                    ->orWhere('brands.brand_name', 'like', "%{$search}%");
                });
            } )
            ->where('products.stock_status', 'in_stock')
            ->groupBy('products.id')
            ->skip(0)->take($take_limit)->selectRaw('MIN(mrp) AS min_value, MAX(mrp) AS max_value')
            ->get();
      
            $tmp = [];
        if (isset($details) && !empty($details)) {
            foreach ($details as $items) {
                $tmp[] = getProductApiData($items);
            }
        }

        // if ($total < $limit) {
        //     $to = $total;
        // }
        $to = count($details);

         $minValue = $total_data->min('min_value');
         $maxValue = $total_data->max('max_value');
         return array('products' => $tmp, 'total_count' => $total, 'from' => ($total == 0 ? '0' : '1'), 'to' => $to,'min_value'=>$minValue??0,'max_value'=>$maxValue??0);
    }

    public function getProductBySlug(Request $request)
    {
        $product_url = $request->product_url;
        $customer_id = $request->customer_id;
        $variation_option_ids = $request->variation_option_ids;
        $items = Product::where('product_url', $product_url)->first();
        if ($items) {
            $return = getProductApiData($items, $customer_id, $variation_option_ids);
        }
        return $return ?? [];
    }

    public function globalSearch(Request $request)
    {
        $search_type = $request->search_type;
        $query = $request->search_field;
        $take = $request->take ?? 10;

        $searchData = [];
        $error = 0;
        if (!empty($query)) {

            $productInfo = Product::where(function ($qr) use ($query) {
                $qr->where('product_name', 'like', "%{$query}%")
                    ->orWhere('hsn_code', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })->where('status', 'published')->where('products.stock_status', 'in_stock')
                ->skip(0)->take($take)->get();
            if (count($productInfo) == 0) {
                $productInfo = Product::where(function ($qr) use ($query) {
                    $qr->whereRaw("MATCH (gbs_products.product_name) AGAINST ('" . $query . "' IN BOOLEAN MODE)")
                        ->orWhere('sku', 'like', "%{$query}%");
                })->where('status', 'published')
                    ->where('products.stock_status', 'in_stock')->skip(0)->take($take)->get();
            }

            if (isset($productInfo) && !empty($productInfo) && count($productInfo) > 0) {
                $error = 1;
                foreach ($productInfo as $items) {
                    $searchData[] = getProductApiData($items);
                }
            } else {
                $pro = [];
                $pro['has_data']        = 'no';
                $pro['message']         = 'No record found';

                $searchData[] = $pro;
            }
        }

        return array('products' => $searchData, 'status' => $error);
    }

    public function getOtherCategories(Request $request)
    {

        $category       = $request->category;

        $otherCategory   = ProductCategory::select('id', 'name', 'slug')
            ->when($category != '', function ($q) use ($category) {
                $q->where('slug', '!=', $category);
            })
            ->where(['status' => 'published', 'parent_id' => 0])
            ->orderBy('order_by', 'asc')
            ->get();
        $data = [];
        if (isset($otherCategory) && !empty($otherCategory)) {
            foreach ($otherCategory as $item) {

                $tmp = [];
                $tmp['id'] = $item->id;
                $tmp['name'] = $item->name;
                $tmp['slug'] = $item->slug;
                $tmp['description'] = $item->description;

                $imagePath              = $item->image;

                if (!Storage::exists($imagePath)) {
                    $path               = asset('assets/logo/no_Image.jpg');
                } else {
                    $url                = Storage::url($imagePath);
                    $path               = asset($url);
                }

                $tmp['image'] = $path;

                $data[] = $tmp;
            }
        }
        return $data;
    }

    public function getAttributeFilter($category_slug = '',$brand_slug = '')
    {

        if ($category_slug) {
            $productCategory = ProductCategory::where('slug', $category_slug)->first();
        }

        $cat_id = $productCategory->id ?? '';
        if(isset($brand_slug) && !empty($brand_slug)){
        $array = explode('_', $brand_slug);
        $brands = Brands::select('brands.id', 'brands.brand_name as name', 'brands.slug')->whereIn('slug',$array)->get();
        }else{
        $brands = Product::select('brands.id', 'brands.brand_name as name', 'brands.slug')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->join('product_categories', function ($join) {
                $join->on('product_categories.id', '=', 'products.category_id');
                $join->orOn('product_categories.parent_id', '=', 'products.category_id');
            })
            ->when($cat_id != '', function ($query) use ($cat_id) {
                $query->where(function ($query) use ($cat_id) {
                    return $query->where('product_categories.id', $cat_id)->orWhere('product_categories.parent_id', $cat_id);
                });
            })
            ->where('products.stock_status', 'in_stock')
            ->whereNull('products.deleted_at')
            ->where('products.status', 'published')->groupBy('products.brand_id')
            ->get()->toArray();
        }

        $productCategory = ProductCategory::where('slug', $category_slug)->first();
        // dump($category_slug);
        // dd( $productCategory );
        $attribute_header = ProductWithAttributeSet::select('product_with_attribute_sets.*')
            ->join('product_attribute_sets', 'product_attribute_sets.id', '=', 'product_with_attribute_sets.product_attribute_set_id')
            ->join('products', 'products.id', '=', 'product_with_attribute_sets.product_id')
            ->where(['products.status' => 'published', 'products.stock_status' => 'in_stock'])
            ->where('product_with_attribute_sets.title', '!=', 'size')
            ->whereNull('products.deleted_at')
            ->where('product_attribute_sets.is_searchable', '1')
            ->groupBy('product_with_attribute_sets.title')
            ->get();

        $attributes = [];
        if (isset($attribute_header) && !empty($attribute_header)) {
            foreach ($attribute_header as $att_value) {

                /**
                 * get group by values
                 */
                $sub_values = ProductWithAttributeSet::select('product_with_attribute_sets.*')
                    ->join('products', 'products.id', '=', 'product_with_attribute_sets.product_id')
                    ->where(['products.status' => 'published', 'products.stock_status' => 'in_stock'])
                    ->where('product_with_attribute_sets.title', $att_value->title)
                    ->groupBy('product_with_attribute_sets.attribute_values')
                    ->whereNull('products.deleted_at')
                    ->get();
                if (isset($sub_values) && !empty($sub_values)) {
                    $sub_array = [];
                    foreach ($sub_values as $items_sub) {
                        $temp_val = [];
                        $temp_val['name'] = trim($items_sub->attribute_values);
                        
                        if( Str::slug(strtolower($att_value->title)) == 'screen-size' ) {
                            $temp_val['slug'] = str_replace(' ', '-',trim( ucwords($items_sub->attribute_values)));
                        } else {
                            $temp_val['slug'] = Str::slug(trim($items_sub->attribute_values));
                        }
                        $sub_array[] = $temp_val;
                    }
                    $attributes[Str::slug(strtolower($att_value->title))] = $sub_array;
                }
            }
        }
        // dd( $attributes);

        return array('attributes' => $attributes, 'brands' => $brands ?? []);
    }

    public function exclusiveProduct()
    {
        $product_data = Product::join('sub_categories', 'sub_categories.id', '=', 'products.label_id')
            ->join('main_categories', 'main_categories.id', '=', 'sub_categories.parent_id')
            ->where('main_categories.slug', 'product-labels')
            ->where('products.stock_status', 'in_stock')
            ->where('products.status', 'published')->get();
        $data = [];
        if (isset($product_data) && !empty($product_data)) {
            foreach ($product_data as $item) {
                $data[] = getProductApiData($item);
            }
        }
        return array('products' => $data, 'error' => 1);
    }
}
