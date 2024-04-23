<?php

namespace App\Exports;


use App\Models\Master\State;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class StateExport implements FromView
{
    public function view(): View
    {
        $list = State::select('states.*', 'countries.name as country_name','users.name as users_name', DB::raw(" IF(gbs_states.status = 2, 'Inactive', 'Active') as user_status"))->join('countries', 'countries.id', '=', 'states.country_id')->join('users', 'users.id', '=', 'states.added_by')->get();
        return view('platform.exports.state.excel', compact('list'));
    }
}
