<?php

namespace App\Exports;


use App\Models\Master\City;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class CityExport implements FromView
{
    public function view(): View
    {
        $list = City::select('cities.*', 'countries.name as country_name','states.state_name as state_name','pincodes.pincode as pincode','users.name as users_name',DB::raw(" IF(gbs_cities.status = 2, 'Inactive', 'Active') as user_status"))->join('pincodes', 'pincodes.id', '=', 'cities.pincode_id')->join('states', 'states.id', '=', 'cities.state_id')->join('countries', 'countries.id', '=', 'cities.country_id')->join('users', 'users.id', '=', 'cities.added_by')->get();
        // dd($list);
        return view('platform.exports.city.excel', compact('list'));
    }
}
