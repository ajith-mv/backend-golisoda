<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Master\Brands;
use App\Models\ServiceCenter;
use App\Models\StoreLocator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreLocatorController extends Controller
{

    public function getStoreLocator(Request $request)
    {

        $brand_id = $request->brand_id ?? '';
        $post_code = $request->post_code ?? '';
        $center_id = $request->center_id ?? '';

        $data = StoreLocator::select('store_locators.*')
            ->join('store_locator_brands', 'store_locator_brands.store_locator_id', '=', 'store_locators.id')
            ->join('store_locator_pincodes', 'store_locator_pincodes.store_locator_id', '=', 'store_locators.id')
            ->join('brands', 'brands.id', '=', 'store_locator_brands.brand_id')
            ->where('store_locators.status', 'published')
            ->where('store_locator_brands.status', 'active')
            // ->where('store_locators.parent_id', 0)
            ->when($brand_id != '', function ($query) use ($brand_id) {
                $query->where('brands.id', $brand_id);
            })
            ->when($post_code != '', function ($query) use ($post_code) {
                $query->where('store_locator_pincodes.pincode', $post_code);
                $query->orWhere('store_locators.pincode', $post_code);
            })
            ->when($center_id != '', function ($query) use ($center_id) {
                $query->where('store_locators.id', $center_id);
            })
            ->groupBy('store_locators.id')
            ->get();

        $params = [];

        if (isset($data) && !empty($data)) {
            foreach ($data as $item) {
                $params[] = $this->getList($item);
            }
        }

        return response()->json(['data' => $params]);
    }

    public function getStoreLocatorDetail(Request $request)
    {

        $slug = $request->slug;
        $data = StoreLocator::select('store_locators.*')
            ->join('store_locator_brands', 'store_locator_brands.store_locator_id', '=', 'store_locators.id')
            ->join('store_locator_pincodes', 'store_locator_pincodes.store_locator_id', '=', 'store_locators.id')
            ->join('brands', 'brands.id', '=', 'store_locator_brands.brand_id')
            ->where('store_locators.status', 'published')
            // ->where('store_locators.parent_id', 0)
            ->when($slug != '', function ($query) use ($slug) {
                $query->where('store_locators.slug', $slug);
            })
            ->groupBy('store_locators.id')
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
        if (isset($data->offers) && !empty($data->offers)) {
            foreach ($data->offers as $item) {
                $path = '';
                $tmp = [];
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

        if (isset($data->contacts) && !empty($data->contacts)) {

            $d_contact = array_column($data->contacts->toArray(), 'contact');

            if (isset($d_contact) && !empty($d_contact)) {
                $contacts = implode(",", $d_contact);
            }
        }

        $temp['group_contacts'] = $contacts;
        $emails = '';
        if (isset($data->emails) && !empty($data->emails)) {

            $d_contact = array_column($data->emails->toArray(), 'email');

            if (isset($d_contact) && !empty($d_contact)) {
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
        $temp['store_image']        = $data->banner_mb;
        $temp['status']             = $data->status;
        $temp['meta_title']         = $data->meta->meta_title ?? "";
        $temp['meta_keyword']       = $data->meta->meta_keyword ?? "";
        $temp['meta_description']   = strip_tags($data->meta->meta_description ?? "");
        $temp['meta'] = array(
            'title' => $data->meta->meta_title ?? "",
            'description' => strip_tags($data->meta->meta_description ?? ""),
            'keywords' => $data->meta->meta_keyword ?? "",
        );
        return $temp;
    }

    public function getStoreOrService(Request $request)
    {

        $slug = $request->slug;
        $data = StoreLocator::select('store_locators.*')
            ->join('store_locator_brands', 'store_locator_brands.store_locator_id', '=', 'store_locators.id')
            ->leftJoin('store_locator_pincodes', 'store_locator_pincodes.store_locator_id', '=', 'store_locators.id')
            ->join('brands', 'brands.id', '=', 'store_locator_brands.brand_id')
            ->where('store_locators.status', 'published')
            // ->where('store_locators.parent_id', 0)
            ->when($slug != '', function ($query) use ($slug) {
                $query->where('store_locators.slug', $slug);
            })
            ->orderBy('store_locators.order_by', 'asc')
            ->groupBy('store_locators.id')
            ->first();

        $response = [];
        if (isset($data) && !empty($data)) {
            $response['data'] = $this->getList($data);
            $response['type'] = 'store_locator';
            return $response;
        } else {
            $data = ServiceCenter::select('service_centers.*')
                ->join('service_center_brands', 'service_center_brands.service_center_id', '=', 'service_centers.id')
                ->leftJoin('service_center_pincodes', 'service_center_pincodes.service_center_id', '=', 'service_centers.id')
                ->join('brands', 'brands.id', '=', 'service_center_brands.brand_id')
                ->where('service_centers.status', 'published')
                // ->where('service_centers.parent_id', 0)
                ->when($slug != '', function ($query) use ($slug) {
                    $query->where('service_centers.slug', $slug);
                })
                ->orderBy('service_centers.order_by', 'asc')
                ->groupBy('service_centers.id')
                ->first();

            $response = [];
            if (isset($data) && !empty($data)) {
                $response['data'] = $this->getServiceList($data);
                $response['type'] = 'service_center';
            }
            return $response;
        }
        
    }

    public function getServiceList($data)
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
        if (isset($data->offers) && !empty($data->offers)) {
            foreach ($data->offers as $item) {
                $path = '';
                $tmp = [];
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

        if (isset($data->contacts) && !empty($data->contacts)) {

            $d_contact = array_column($data->contacts->toArray(), 'contact');

            if (isset($d_contact) && !empty($d_contact)) {
                $contacts = implode(",", $d_contact);
            }
        }

        $temp['group_contacts'] = $contacts;
        $emails = '';
        if (isset($data->emails) && !empty($data->emails)) {

            $d_contact = array_column($data->emails->toArray(), 'email');

            if (isset($d_contact) && !empty($d_contact)) {
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
        $temp['service_center_image'] = $data->banner_mb;
        $temp['status']             = $data->status;
        $temp['meta_title']         = $data->meta->meta_title ?? "";
        $temp['meta_keyword']       = $data->meta->meta_keyword ?? "";
        $temp['meta_description']   = strip_tags($data->meta->meta_description ?? "");
        $temp['meta'] = array(
            'title' => $data->meta->meta_title ?? "",
            'description' => strip_tags($data->meta->meta_description ?? ""),
            'keywords' => $data->meta->meta_keyword ?? "",
        );

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
