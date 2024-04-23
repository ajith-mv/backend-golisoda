<?php

namespace App\Exports;


use App\Models\Master\Brands;
use App\Models\HomePageSetting\HomepageSettingField;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class HomepageSettingFieldExport implements FromView
{
    public function view(): View
    {
        $list = HomepageSettingField::select('homepage_setting_fields.*','users.name as users_name')
        ->join('users', 'users.id', '=', 'homepage_setting_fields.added_by')->get();
        return view('platform.exports.homepage_setting_fields.excel', compact('list'));
    }
}
