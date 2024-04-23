<?php

namespace App\Exports;

use App\Models\Banner;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BannerExport implements FromView
{
    public function view(): View
    {
        $list = Banner::select('banners.*','users.name as users_name')->join('users', 'users.id', '=', 'banners.added_by')->get();
        return view('platform.exports.banner.excel', compact('list'));
    }
}
