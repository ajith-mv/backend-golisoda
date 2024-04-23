<?php

namespace App\Exports;


use App\Models\Master\Brands;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BrandsExport implements FromView
{
    public function view(): View
    {
        $list = Brands::select('brands.*','users.name as users_name')->join('users', 'users.id', '=', 'brands.added_by')->get();
        // dd($list);
        return view('platform.exports.brand.excel', compact('list'));
    }
}
