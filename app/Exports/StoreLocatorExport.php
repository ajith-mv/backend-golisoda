<?php

namespace App\Exports;

use App\Models\StoreLocator;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StoreLocatorExport implements FromView
{
    public function view(): View
    {
        $list = StoreLocator::select('store_locators.*','users.name as users_name','brands.brand_name as brand_name')
        ->join('users', 'users.id', '=', 'store_locators.added_by')
        ->leftJoin('brands', 'brands.id', '=', 'store_locators.brand_id')
        ->get();
        return view('platform.exports.store_locator.excel', compact('list'));
    }
}