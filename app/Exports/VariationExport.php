<?php

namespace App\Exports;


use App\Models\Master\Variation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class VariationExport implements FromView
{
    public function view(): View
    {
        $list = Variation::select('variations.*', DB::raw(" IF(status = 2, 'Inactive', 'Active') as user_status"))->get();
        return view('platform.exports.variation.excel', compact('list'));
    }
}
