<?php

namespace App\Exports;

use App\Models\ServiceCenter;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ServiceCenterExport implements FromView
{
    public function view(): View
    {
        $list = ServiceCenter::select('service_centers.*','users.name as users_name')->join('users', 'users.id', '=', 'service_centers.added_by')->get();
        return view('platform.exports.service_center.excel', compact('list'));
    }
}