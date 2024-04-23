<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HistoryVideoResource extends JsonResource
{
    public function toArray($request)
    {
        $tmp[ 'id' ]            = $this->id;
        $tmp[ 'title' ]         = $this->title;
        $tmp[ 'file_path' ]     = asset($this->file_path);
        $tmp[ 'video_url' ]     = $this->video_url;
        $tmp[ 'description' ]   = $this->description;

        return $tmp;
    }
    
}
