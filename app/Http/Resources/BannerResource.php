<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BannerResource extends JsonResource
{
    
    public function toArray($request)
    {

        $bannerImagePath        = 'banner/'.$this->id.'/main_banner/'.$this->banner_image;
        $url                    = Storage::url($bannerImagePath);
        $path                   = asset($url);

        $mobileBanner           = 'banner/'.$this->id.'/mobile_banner/'.$this->mobile_banner;
        $mobUrl                 = Storage::url($mobileBanner);
        $pathBanner             = asset($mobUrl);

        $tmp[ 'id' ]            = $this->id;
        $tmp[ 'title' ]         = $this->title;
        $tmp[ 'image' ]         = $path;
        $tmp[ 'mobile_banner' ] = $pathBanner;
        $tmp[ 'links' ] = $this->links;
        $tmp[ 'description' ]   = $this->description;
        $tmp[ 'tag_line' ]      = $this->tag_line;

        return $tmp;
    }

}
