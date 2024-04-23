<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Master\Brands;
use App\Models\ServiceCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{

    public function getServiceCenter(Request $request)
    {
        $brand_id = $request->brand_id ?? '';
        $post_code = $request->post_code ?? '';
        $center_id = $request->center_id ?? '';

        $data = ServiceCenter::select('service_centers.*')
            ->join('service_center_brands', 'service_center_brands.service_center_id', '=', 'service_centers.id')
            ->join('service_center_pincodes', 'service_center_pincodes.service_center_id', '=', 'service_centers.id')
            ->join('brands', 'brands.id', '=', 'service_center_brands.brand_id')
            ->where('service_centers.status', 'published')
            // ->where('service_centers.parent_id', 0)
            ->when($brand_id != '', function ($query) use ($brand_id) {
                $query->where('brands.id', $brand_id);
            })
            ->when($post_code != '', function ($query) use ($post_code) {
                $query->where('service_center_pincodes.pincode', $post_code);
                $query->orWhere('service_centers.pincode', $post_code);
            })
            ->when($center_id != '', function ($query) use ($center_id) {
                $query->where('service_centers.id', $center_id);
            })
            ->groupBy('service_centers.id')
            ->get();

        $params = [];

        if( isset( $data ) && !empty( $data ) ) {
            foreach ($data as $item) {
                $params[] = $this->getList($item);
            }
        }

        return response()->json(['data' => $params]);
    }


    public function getServiceCenterDetail(Request $request)
    {
        
        $slug = $request->slug;
        $data = ServiceCenter::select('service_centers.*')
            ->join('service_center_brands', 'service_center_brands.service_center_id', '=', 'service_centers.id')
            ->join('service_center_pincodes', 'service_center_pincodes.service_center_id', '=', 'service_centers.id')
            ->join('brands', 'brands.id', '=', 'service_center_brands.brand_id')
            ->where('service_centers.status', 'published')
            // ->where('service_centers.parent_id', 0)
            ->when($slug != '', function ($query) use ($slug) {
                $query->where('service_centers.slug', $slug);
            })
            ->groupBy('service_centers.id')
            ->first();
            
        $response = [];
        if (isset($data) && !empty($data)) {
            $response = $this->getList($data);
        }
        return $response;

    }

    public function getList($data)
    {

        $temp['id']             = $data->id;
        $temp['title']          = $data->title;
        $temp['slug']           = $data->slug;
        $temp['parent_id']      = $data->parent_id;
        $temp['description']    = $data->description;
        $temp['pincode']        = $data->pincode;
        $temp['whatsapp_no']    = $data->whatsapp_no;
        $temp['address']        = $data->address;
        $temp['map_link']       = $data->map_link;
        $temp['image_360_link'] = $data->image_360_link;
        $offers = [];
        if( isset( $data->offers ) && !empty($data->offers ) ) {
            foreach ($data->offers as $item) {
                $path = '';
                $tmp =[];
                $tmp['id'] = $item->id;
                $tmp['title'] = $item->title;
                $offerImagePath = $item->image;
                if (!Storage::exists($offerImagePath) || $offerImagePath === null) {
                    $path               = asset('assets/logo/no_Image.jpg');
                } else {
                    $url                    = Storage::url($offerImagePath);
                    $path                   = asset($url);
                }

                $tmp['image'] = $path;
                $offers[] = $tmp;
            }
        }
        $temp['offers'] = $offers;
        $usedBrands = [];
        $brandarr = [];
        $near_pincodes = [];
        if (isset($data->nearPincodes) && !empty($data->nearPincodes)) {
            foreach ($data->nearPincodes as $items) {
                $near_pincodes[] = $items->pincode;
            }
        }

        $temp['near_pincodes'] = $near_pincodes;
        $contacts = '';
        
        if( isset( $data->contacts ) && !empty($data->contacts ) ) {

            $d_contact = array_column($data->contacts->toArray(), 'contact' );

            if( isset( $d_contact ) && !empty( $d_contact ) ) {
                $contacts = implode(",", $d_contact);
            }
          
        }

        $temp['group_contacts'] = $contacts; 
        $emails = '';
        if( isset( $data->emails ) && !empty($data->emails ) ) {

            $d_contact = array_column($data->emails->toArray(), 'email' );

            if( isset( $d_contact ) && !empty( $d_contact ) ) {
                $emails = implode(",", $d_contact);
            }
          
        }
        $temp['group_emails'] = $emails; 
        
        if (isset($data->brands) && !empty($data->brands)) {

            $usedBrands = array_column($data->brands->toArray(), 'brand_id');
            $brandall = Brands::whereIn('id', $usedBrands)->get();

            if (isset($brandall) && !empty($brandall)) {
                foreach ($brandall as $item) {
                    $parent = [];
                    $parent['id'] = $item->id;
                    $parent['name'] = $item->brand_name;
                    $parent['slug'] = $item->slug;

                    $brandLogoPath          = 'public/brands/' . $item->id . '/default/' . $item->brand_logo;

                    if (!Storage::exists($brandLogoPath) || $item->brand_logo === null) {
                        $path               = asset('assets/logo/no_Image.jpg');
                    } else {
                        $url                    = Storage::url($brandLogoPath);
                        $path                   = asset($url);
                    }

                    $parent['image'] = $path;
                    $brandarr[] = $parent;
                }
            }
        }

        $temp['brands'] = $brandarr;

        if (isset($data->email) && !empty($data->email)) {
            $arrEmail = json_decode($data->email);
            $data->email = implode(',', $arrEmail);
            $temp['email']          = $data->email;
        } else {
            $temp['email']       = '';
        }

        if (isset($data->contact_no) && !empty($data->contact_no)) {
            $arrContact             = json_decode($data->contact_no);
            $data->contact_no      = implode(',', $arrContact);
            $temp['contact_no']     = $data->contact_no;
        } else {
            $temp['contact_no']       = '';
        }

        if ($data->banner) {
            $url = Storage::url($data->banner);
            $data->banner = asset($url);
        } else {
            $data->banner = asset('userImage/no_Image.jpg');
        }
        $temp['banner'] = $data->banner;

        if ($data->banner_mb) {
            $url = Storage::url($data->banner_mb);
            $data->banner_mb = asset($url);
        } else {
            $data->banner_mb = asset('userImage/no_Image.jpg');
        }
        $temp['service_center_image']= $data->banner_mb;
        $temp['status']             = $data->status;
        $temp['meta_title']         = $data->meta->meta_title ?? "";
        $temp['meta_keyword']       = $data->meta->meta_keyword ?? "";
        $temp['meta_description']   = $data->meta->meta_description ?? "";

        // if (isset($data->child) && !empty($data->child) && count($data->child) > 0) {
        //     foreach ($data->child as $childData) {
        //         $temp1['id']            = $childData->id;
        //         $temp1['title']         = $childData->title;
        //         $temp1['slug']          =    $childData->slug;
        //         $temp1['parent_id']     = $data->parent_id;
        //         $temp1['description']   = $data->description;
        //         $temp1['pincode']       = $data->pincode;
        //         $temp1['address']       = $data->address;
        //         $temp1['latitude']      = $data->latitude;
        //         $temp1['langitude']     = $data->langitude;
        //         $temp1['email']         = $data->email;
        //         $temp1['contact_no']    = $data->contact_no;

        //         if ($childData->banner) {
        //             $url = Storage::url($childData->banner);
        //             $childData->banner = asset($url);
        //         } else {
        //             $childData->banner = asset('userImage/no_Image.jpg');
        //         }
        //         $temp1['banner'] = $childData->banner;

        //         if ($childData->banner_mb) {
        //             $url = Storage::url($childData->banner_mb);
        //             $childData->banner_mb = asset($url);
        //         } else {
        //             $childData->banner_mb = asset('userImage/no_Image.jpg');
        //         }
        //         $temp1['banner_mb'] = $childData->banner_mb;
        //         $temp1['status']    = $childData->status;
        //         $temp1['meta_title']         = $childData->meta->meta_title ?? "";
        //         $temp1['meta_keyword']       = $childData->meta->meta_keyword ?? "";
        //         $temp1['meta_description']   = $childData->meta->meta_description ?? "";

        //         $temp['child'][] = $temp1;
        //     }
        // }
        return $temp;
    }
}
