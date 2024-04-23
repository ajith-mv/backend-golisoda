<?php

namespace App\Exports;


use App\Models\Category\MainCategory;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MainCategoryExport implements FromView
{
    public function view(): View
    {
        $list = MainCategory::select('main_categories.*', 'users.name as users_name')->join('users', 'users.id', '=', 'main_categories.added_by')->get();
        return view('platform.exports.category.excel', compact('list'));
    }
}
