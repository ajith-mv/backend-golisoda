<?php

namespace App\Exports;


use App\Models\Master\Brands;
use App\Models\QuickLink;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class QuickLinkExport implements FromView
{
    public function view(): View
    {
        $list = QuickLink::select('quick_links.*','users.name as users_name')->join('users', 'users.id', '=', 'quick_links.added_by')->get();
        return view('platform.exports.quick_link.excel', compact('list'));
    }
}
