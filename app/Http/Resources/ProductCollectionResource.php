<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductCollectionResource extends JsonResource
{
    public function toArray($request)
    {
        $imagePath              = $this->image;
        $bannerImagePath        = 'productCollection/'.$this->id.'/'.$this->image;
        $url                    = Storage::url($bannerImagePath);
        $path                   = asset($url);
      

        $childTmp                   = [];
        $tmp[ 'id' ]                = $this->id;
        $tmp[ 'collection_name' ]   = $this->collection_name;
        $tmp[ 'collection_slug' ]   = Str::slug($this->collection_name);
        $tmp[ 'image' ]             = $path;
        $tmp[ 'tag_line' ]          = $this->tag_line;
        $tmp[ 'order_by' ]          = $this->order_by;
        $tmp[ 'status' ]            = $this->status;
        $tmp[ 'deleted_at' ]        = $this->deleted_at;
        $tmp[ 'updated_at' ]        = $this->updated_at;
        $tmp[ 'show_home_page' ]    = $this->show_home_page;
        if( isset($this->collectionProducts) && !empty( $this->collectionProducts )) {
            foreach ($this->collectionProducts as $items ) {
                $category = $items->product->productCategory;
                // dd( $category->id );
                // $salePrices             = getProductPrice( $items->product );
                $pro = getProductApiData($items->product);
                $tmp['products'][]      = $pro; 
            }
        }

        return $tmp;
    }
}
