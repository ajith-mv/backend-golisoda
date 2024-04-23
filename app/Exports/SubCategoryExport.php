<?php

namespace App\Exports;


use App\Models\Category\SubCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class SubCategoryExport implements FromView
{
    protected $pageName;

    public function __construct($pageName)
    {
        $this->pageName     = $pageName;
    }

    public function view(): View
    {
        $page_type = $this->pageName;
        $list = SubCategory::select('sub_categories.*','main_categories.category_name as category_name', 'users.name as users_name')
                    ->join('main_categories', 'sub_categories.parent_id', '=', 'main_categories.id')
                    ->join('users', 'users.id', '=', 'sub_categories.added_by')
                    ->when( $page_type != 'sub_category',  function($q) use($page_type) {
                        return $q->where('main_categories.slug', $page_type);
                    })
                    ->get();
        return view('platform.exports.sub_category.excel', compact('list'));
    }
}
