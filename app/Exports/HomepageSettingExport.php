<?php

namespace App\Exports;


use App\Models\Master\Brands;
use App\Models\HomePageSetting\HomepageSetting;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class HomepageSettingExport implements FromView
{
    public function view(): View
    {
        $list = HomepageSetting::select('homepage_settings.*','users.name as users_name','homepage_setting_fields.title as field_title')
        ->leftJoin('homepage_setting_fields','homepage_setting_fields.id','=','homepage_settings.homepage_setting_field_id')
        ->leftJoin('users', 'users.id', '=', 'homepage_setting_fields.added_by')
        ->get();
        return view('platform.exports.homepage_setting.excel', compact('list'));
    }
}
