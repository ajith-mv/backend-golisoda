<?php

namespace App\Exports;


use App\Models\Master\VariationGroup;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class VariationGroupExport implements FromView
{
    public function view(): View
    {
        $list = VariationGroup::select('variation_groups.*', DB::raw(" IF(status = 2, 'Inactive', 'Active') as user_status"))->get();
        return view('platform.exports.variation-group.excel', compact('list'));
    }
}
