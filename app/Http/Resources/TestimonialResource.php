<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    
    public function toArray($request)
    {

        $tmp[ 'id' ]                    = $this->id;
        $tmp[ 'title' ]                 = $this->title;
        $tmp[ 'image' ]                 = asset($this->image);
        $tmp[ 'short_description' ]     = substr($this->short_description, 0, 200);
        $tmp[ 'long_description' ]      = $this->long_description;

        return $tmp;
        
    }

}
