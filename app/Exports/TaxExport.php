<?php

namespace App\Exports;


use App\Models\Settings\Tax;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class TaxExport implements FromView
{
    public function view(): View
    {
        $list = Tax::select('taxes.*')->get();
        return view('platform.exports.tax.excel', compact('list'));
    }
}
