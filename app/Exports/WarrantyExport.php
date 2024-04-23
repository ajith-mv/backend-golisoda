<?php

namespace App\Exports;


use App\Models\Warranty;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class WarrantyExport implements FromView
{
    public function view(): View
    {
        $list = Warranty::select('warranties.*','users.name as users_name')->join('users', 'users.id', '=', 'warranties.added_by')->get();
        return view('platform.exports.warranty.excel', compact('list'));
    }
}