<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class MenuResource extends JsonResource
{
    
    public function toArray($request)
    {
        
        $childTmp = [];
        $tmp[ 'id' ]        = $this->id;
        $tmp[ 'name' ]      = $this->name;
        $tmp[ 'slug' ]      = $this->slug;

        if( isset( $this->childTopMenuCategory ) && !empty( $this->childTopMenuCategory ) ) {
            foreach ($this->childTopMenuCategory as $child ) {
                $innerTmp['id']     = $child->id;
                $innerTmp['name']   = $child->name;
                $innerTmp['slug']   = $child->slug;
                $childTmp[]         = $innerTmp;
            }
        }
        $tmp['child']       = $childTmp;

        return $tmp;
    }

}
