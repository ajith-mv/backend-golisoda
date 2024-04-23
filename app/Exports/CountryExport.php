<?php

namespace App\Exports;


use App\Models\Master\Country;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class CountryExport implements FromView
{
    public function view(): View
    {
        $list = Country::select('countries.*', DB::raw(" IF(status = 2, 'Inactive', 'Active') as user_status"))->get();
        return view('platform.exports.country.excel', compact('list'));
    }
}
