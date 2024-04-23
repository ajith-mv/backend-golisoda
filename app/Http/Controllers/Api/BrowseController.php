<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomePageSetting\HomepageSetting;
use App\Models\HomePageSetting\HomepageSettingItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrowseController extends Controller
{
    public function index()
    {
      
        $browse                   = [];
        $browse_filed_data = HomepageSetting::where('status', 'published')->orderBy('order_by', 'asc')->get();
        foreach ($browse_filed_data as $key => $data) {
            $parent = [];
            $parent['id']             = $data->id;
            $parent['title']          = $data->title;
            $parent['color']          = $data->color;
            $parent['type']           = $data->fields->slug == 'price' ? 'prices' : 'screen-size';
            $items_field = HomepageSettingItems::where('homepage_settings_id', $data->id)->get();
            $items = [];
            foreach ($items_field as $key => $data_field) {
                $tmp = [];
                $tmp['start_size'] = $data_field->start_size;
                $tmp['end_size'] = $data_field->end_size;
                if( $parent['type'] == 'screen-size' ) {
                    $tmp['slug'] = $data_field->start_size.'-Inch_'.$data_field->end_size.'-Inch';
                } else {
                    $tmp['slug'] = $data_field->start_size.'-'.$data_field->end_size;
                }
                $image           = $data_field->setting_image_name;
                $mobUrl          = Storage::url($image);
                $pathbrowse      = asset($mobUrl);
                $tmp['path'] = $pathbrowse;

                $items[] = $tmp;
            }
            $parent['children'] = $items;

            $browse[] = $parent;
        }
        
        return response()->json(['data' => $browse]);

    }

   
}
