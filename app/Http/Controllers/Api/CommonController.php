<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\DiscountCollectionResource;
use App\Http\Resources\HistoryVideoResource;
use App\Http\Resources\ProductCollectionResource;
use App\Http\Resources\TestimonialResource;
use App\Mail\EnquiryMail;
use App\Models\Banner;
use App\Models\Enquiry;
use App\Models\Master\Brands;
use App\Models\Master\State;
use App\Models\Offers\Coupons;
use App\Models\Product\Product;
use App\Models\Product\ProductCollection;
use App\Models\Product\ProductWithAttributeSet;
use App\Models\RecentView;
use App\Models\Testimonials;
use App\Models\WalkThrough;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommonController extends Controller
{

    public function getAllTestimonials()
    {
        return TestimonialResource::collection(Testimonials::select('id', 'title', 'image', 'short_description', 'long_description')->where(['status' => 'published'])->orderBy('order_by', 'asc')->get());
    }

    public function getAllHistoryVideo()
    {
        return HistoryVideoResource::collection(WalkThrough::select('id', 'title', 'video_url', 'file_path', 'description')->where(['status' => 'published'])->orderBy('order_by', 'asc')->get());
    }

    public function getAllBanners()
    {
        return BannerResource::collection(Banner::select('id', 'title', 'description', 'banner_image','mobile_banner','links', 'tag_line', 'order_by')->where(['status' => 'published'])->orderBy('order_by', 'asc')->get());
    }

    public function getAllBrands(Request $request)
    {   
        $page                   = $request->page ?? 0;
        $take                   = $request->take ?? 20;
        $all=Brands::where(['status' => 'published'])->get()->count();
        $brands= BrandResource::collection(Brands::select('id', 'brand_name', 'brand_banner', 'brand_logo', 'short_description', 'notes', 'slug')->where(['status' => 'published'])->orderBy('order_by', 'asc')->take($take)->get());
        $to = count($brands);
         return array('brands' => $brands, 'total_count' => $all, 'from' => ($to == 0 ? '0' : '1'), 'to' => $to);
    }

    public function getTopBrands() {

        return Brands::selectRaw("GROUP_CONCAT( slug SEPARATOR '_') AS slug")->where('is_top_brand', 'yes')
                    ->where('status', 'published')->first();

    }

    public function getBrandByAlphabets()
    {
        $alphas = range('A', 'Z');

        $checkArray = [];
        if (isset($alphas) && !empty($alphas)) {
            foreach ($alphas as $items) {


                $data = Brands::where(DB::raw('SUBSTR(brand_name, 1, 1)'), strtolower($items))->get();
                $childTmp = [];
                if (isset($data) && !empty($data)) {
                    foreach ($data as $daitem) {
                        $tmp1                    = [];
                        $brandLogoPath           = 'brands/' . $daitem->id . '/default/' . $daitem->brand_logo;

                        if ($daitem->brand_logo === null) {
                            $path                = asset('assets/logo/no_Image.jpg');
                        } else {
                            $url                 = Storage::url($brandLogoPath);
                            $path                = asset($url);
                        }

                        $tmp1['id']            = $daitem->id;
                        $tmp1['title']         = $daitem->brand_name;
                        $tmp1['slug']          = $daitem->slug;
                        $tmp1['image']         = $path;
                        $tmp1['brand_banner']  = $daitem->brand_banner;
                        $tmp1['description']   = $daitem->short_description;
                        $tmp1['notes']         = $daitem->notes;

                        $childTmp[]     = $tmp1;
                    }
                }
                $tmp[$items]  = $childTmp;
                $checkArray   = $tmp;
            }
        }
        // dd( $checkArray );
        return $checkArray;
    }

    public function getDiscountCollections()
    {

        $details        = ProductCollection::where(['show_home_page' => 'yes', 'status' => 'published','is_handpicked_collection' => 'no', 'can_map_discount' => 'yes'])
            ->orderBy('order_by', 'asc')->limit(4)->get();

        $collection     = [];

        if (isset($details) && !empty($details)) {
            foreach ($details as $item) {

                $tmp                    = [];
                $tmp['id']              = $item->id;
                $tmp['collection_name'] = $item->collection_name;
                $tmp['slug']            = $item->slug;
                $tmp['tag_line']        = $item->tag_line;
                $tmp['order_by']        = $item->order_by;
                $imagePath              = $item->image;

                if (!Storage::exists($imagePath)) {
                    $path               = asset('assets/logo/no_Image.jpg');
                } else {
                    $url                = Storage::url($imagePath);
                    $path               = asset($url);
                }

                $tmp['image']           = $path;             

                $collection[] = $tmp;
            }
        }
        return $collection;
        
    }


    public function getHandPickedCollections()
    {

        $details        = ProductCollection::where(['show_home_page' => 'yes', 'status' => 'published', 'is_handpicked_collection' => 'yes'])
            ->orderBy('order_by', 'desc')->limit(4)->get();

        $collection     = [];

        if (isset($details) && !empty($details)) {
            foreach ($details as $item) {
                $tmp                    = [];
                $tmp['id']              = $item->id;
                $tmp['collection_name'] = $item->collection_name;
                $tmp['slug']            = $item->slug;
                $tmp['tag_line']        = $item->tag_line;
                $tmp['order_by']        = $item->order_by;
                $imagePath              = $item->image;

                if (!Storage::exists($imagePath)) {
                    $path               = asset('assets/logo/no_Image.jpg');
                } else {
                    $url                = Storage::url($imagePath);
                    $path               = asset($url);
                }

                $tmp['image']           = $path;   

                $collection[] = $tmp;
            }
        }
        return $collection;
    }

    public function setRecentView(Request $request)
    {
        $ins['customer_id'] = $request->customer_id;
        $product_url = $request->product_url;
        $product_info = Product::where('product_url', $product_url)->first();
        $ins['product_id'] = $product_info->id;
        RecentView::where('customer_id', $request->customer_id)->where('product_id', $product_info->id)->delete();

        RecentView::create($ins);

        return true;
    }

    public function getSates()
    {
        return State::select('state_name', 'id', 'state_code')->where('status', 1)->get();
    }

    public function getMetaInfo(Request $request)
    {
        $page = $request->page;

        switch ($page) {
            case 'profile':
                # code...
                break;

            default:
                # code...
                break;
        }
    }

    public function getAllHomeDetails()
    {

        $details = ProductCollection::where(['show_home_page' => 'yes', 'status' => 'published', 'can_map_discount' => 'no'])
            ->orderBy('order_by', 'asc')->get();
        $response['collection'] = ProductCollectionResource::collection($details);
        $response['testimonials'] =  TestimonialResource::collection(Testimonials::select('id', 'title', 'image', 'short_description', 'long_description')->where(['status' => 'published'])->orderBy('order_by', 'asc')->get());
        $response['video'] = HistoryVideoResource::collection(WalkThrough::select('id', 'title', 'video_url', 'file_path', 'description')->where(['status' => 'published'])->orderBy('order_by', 'asc')->get());
        $response['banner'] = BannerResource::collection(Banner::select('id', 'title', 'description', 'banner_image', 'tag_line', 'order_by')->where(['status' => 'published'])->orderBy('order_by', 'asc')->get());

        $service_places['chennai'] = 'laptop-service-center-in-chennai';
        $service_places['madurai'] = 'laptop-service-center-in-madurai';
        $service_places['trichy'] = 'laptop-service-center-in-trichy';
        $service_places['coimbatore'] = 'laptop-service-center-in-coimbatore';
        $service_places['bangalore'] = 'laptop-service-center-in-bangalore';
        $response['service_places'] = $service_places;
        return $response;

    }

    public function getHomeServicePlaces() {

        $service_places['chennai'] = 'laptop-service-center-in-chennai';
        $service_places['madurai'] = 'laptop-service-center-in-madurai';
        $service_places['trichy'] = 'laptop-service-center-in-trichy';
        $service_places['coimbatore'] = 'laptop-service-center-in-coimbatore';
        $service_places['bangalore'] = 'laptop-service-center-in-bangalore';
        $response['service_places'] = $service_places;
        return $response;
        
    }

    public function getBrandInfo(Request $request)
    {

        $slug = $request->slug;
        $brand_info = Brands::where('slug', $slug)->first();

        $response['brand_info'] = $brand_info;
        $parent['id'] = $brand_info->id;
        $parent['name'] = $brand_info->name;
        $parent['slug'] = $brand_info->slug;


        $brandLogoPath          = 'public/brands/'.$brand_info->id.'/default/'.$brand_info->brand_logo;
        
        if( !Storage::exists( $brandLogoPath ) || $brand_info->brand_logo === null ) {
            $path               = asset('assets/logo/no_Image.jpg');
        } else {
            $url                    = Storage::url($brandLogoPath);
            $path                   = asset($url);
        }

        $parent['image'] = $path;


        if ($brand_info->category) {
            foreach ($brand_info->category as $items) {
                $tmp = [];
                $tmp['id'] = $items->id;
                $tmp['name'] = $items->name;
                $tmp['slug'] = $items->slug;
                $tmp['image'] = $items->image;
                $parent['category'][] = $tmp;
            }
        }
        return $parent;
    }

    public function getSubCategoryCollections(Request $request)
    {
        $category = Product::select('product_categories.*')->join('product_categories', 'product_categories.id', '=', 'products.category_id')
                               
                                ->groupBy('products.category_id')
                                ->get();
                                // ->where('product_categories.parent_id', '!=', 0)
        $response = [];
        if( isset( $category ) && !empty( $category ) ) {
            foreach ($category as $items) {
                $tmp = [];
                $tmp['id'] = $items->id;
                $tmp['name'] = $items->name;
                $tmp['parent_id'] = $items->parent_id;
                $tmp['slug'] = $items->slug;
                $tmp['description'] = $items->description;
                $tmp['is_home_page'] = $items->is_home_page; 
                $tmp['banner_image'] = $items->banner_image;
                
                $imagePath = 'public/'.$items->image;
                if (!Storage::exists($imagePath) || empty( $items->image )) {
                    $path               = asset('userImage/no_Image.jpg');
                } else {
                    $url                = Storage::url($imagePath);
                    $path               = asset($url);
                }

                $tmp['image'] = $path;
                $response[] = $tmp;
            }
          
        }
        
        
        return $response;
        
    }

    public function submitContactForm(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $mobile_no = $request->mobile_no;
        $message = $request->message;

        $ins['first_name'] = $name;
        $ins['email'] = $email;
        $ins['mobile_no'] = $mobile_no;
        $ins['message'] = $message;

        $to_email = 'info@golisodastore.com';
        $title = 'New Enquiry Has Received';

        $html = '<h3>Dear Support Team,</h3>';
        $html = '<p>New Enquiry was received!</p>';
        $html .= '<table>';
        $html .= '<tr><td> Name </td><td>'.$name.'</td></tr>';
        $html .= '<tr><td> Email </td><td>'.$email.'</td></tr>';
        $html .= '<tr><td> Moblie No </td><td>'.$mobile_no.'</td></tr>';
        $html .= '<tr><td> Message </td><td>'.$message.'</td></tr></table>';

        Enquiry::create($ins);
        $send_mail = new EnquiryMail($html, $title);
         $bccEmails = explode(',', env('EMAILS'));
         $bccRecipients = array_merge($bccEmails, [$to_email]);
        Mail::to($to_email)->bcc($bccRecipients)->send($send_mail);

        return array('status' => 1, 'message' => 'Enquiry submitted successfully');

    }

    public function getProcessorModelData(Request $request)
    {
        /**
        //  * Get Intel Processor data
        //  */
        // $intel_data = ProductWithAttributeSet::where('title', 'processor')
        //                 ->where('attribute_values', 'like', "%intel%")
        //                 ->groupBy('attribute_values')
        //                 ->limit(4)
        //                 ->get();
        // $model = [];
        // $intel_information = [];
        // if( isset( $intel_data ) && !empty( $intel_data ) ) {
        //     $intel_information['title'] = 'Intel® Processors';
        //     $intel_information['banner'] = asset('assets/logo/intelprocessor.jpg');

        //     $tmp = [];
        //     foreach ( $intel_data as $items ) {             
        //         $tmp[] = array('slug' => 'processor='.Str::slug($items->attribute_values ), 'name' => $items->attribute_values );
        //     }
        //     $intel_information['processors'] = $tmp;

        // }
        // $test[] = $intel_information;
        
        // /**
        //  * Get Amd processor data
        //  */
        // $intel_data = ProductWithAttributeSet::where('title', 'processor')
        //                 ->where('attribute_values', 'like', "%ryzen%")
        //                 ->groupBy('attribute_values')
        //                 ->limit(4)
        //                 ->get();
        // $model = [];
        // $intel_information = [];
        // if( isset( $intel_data ) && !empty( $intel_data ) ) {
        //     $intel_information['title'] = 'AMD Ryzen Processors';
        //     $intel_information['banner'] = asset('assets/logo/amdprocessor.jpg');

        //     $tmp = [];
        //     foreach ( $intel_data as $items ) {             
        //         $tmp[] = array('slug' => 'processor='.Str::slug($items->attribute_values), 'name' => $items->attribute_values );
        //     }
        //     $intel_information['processors'] = $tmp;

        // }
        // $test[] = $intel_information;

        // $model = $test;
        return [];
    }
}
