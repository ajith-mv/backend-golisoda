<?php

namespace App\Http\Resources;

use App\Models\Category\MainCategory;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use App\Models\Product\ProductCategory;
use Illuminate\Support\Facades\Storage;
class SiteResource extends JsonResource
{
    
    public function toArray($request)
    {
        
        $childTmp = [];
        $tmp[ 'site_name' ]         = $this->site_name;
        $tmp[ 'site_email' ]        = $this->site_email;
        $tmp[ 'site_mobile_no' ]    = $this->site_mobile_no;
        $tmp[ 'favicon' ]           = asset('/') . $this->favicon;
        $tmp[ 'logo' ]              = asset('/') . $this->logo;
        $tmp[ 'address' ]           = $this->address;
        $tmp[ 'copyrights' ]        = $this->copyrights;
        $tmp[ 'is_tax_inclusive' ]  = $this->is_tax_inclusive;
        $tmp[ 'payment_mode' ]      = $this->payment_mode;
        $tmp[ 'is_pickup_from_store' ]      = $this->is_pickup_from_store;
        
        $tmp[ 'is_cod' ]      = $this->is_cod;
        $tmp[ 'cod_amount' ]      = $this->cod_amount;

        if( isset( $this->links ) && !empty( $this->links ) ) {
            foreach ($this->links as $child ) {
                $innerTmp['link_name']      = $child->link_name;
                $innerTmp['link_icon']      = $child->link_icon;
                $innerTmp['link_url']       = $child->link_url;
                $childTmp[]         = $innerTmp;
            }
        }
        $tmp['links']       = $childTmp;
         $categories=ProductCategory::where('parent_id',0)->where('is_home_page',1)->get();
        if(count($categories)>0 ) {
        foreach ($categories as $category ) {
                $innerTmp1['id']               = $category->id;
                $innerTmp1['name']             = $category->name;
                $innerTmp1['parent_id']        = $category->parent_id;
                $innerTmp1['slug']             = $category->slug;
                $innerTmp1['description']      = $category->description;
                $innerTmp1['is_home_page']        = $category->is_home_page;
                $innerTmp1['banner_image']        = $category->banner_image;
                $path=substr($category->image, 0, -1);
                $imagePath=storage_path($path);
                if (file_exists($imagePath)) {
                     $innerTmp1['image']     =  NULL;
                 }else{
                    $innerTmp1['image']= asset(Storage::url($path));
                 }
                $childTmp1[]         = $innerTmp1;
 
            }
            
        }
        $tmp['home_page']       =$childTmp1;
        /**
         *  get address types
         */
        $address_type       = MainCategory::where('slug', 'address-type')->first();
        $tmp['address_type'] = $address_type->subCategory ?? [];

        return $tmp;
    }
}
