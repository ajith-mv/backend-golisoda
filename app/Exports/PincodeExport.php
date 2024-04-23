<?php

namespace App\Exports;


use App\Models\Master\Pincode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class PincodeExport implements FromView
{
    public function view(): View
    {
        $list = Pincode::select('pincodes.*', 'users.name as users_name',DB::raw(" IF(gbs_pincodes.status = 2, 'Inactive', 'Active') as user_status"))->join('users', 'users.id', '=', 'pincodes.added_by')->get();
        return view('platform.exports.pincode.excel', compact('list'));
    }
}
