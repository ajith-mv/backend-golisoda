<?php

namespace App\Exports;


use App\Models\WalkThrough;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class WalkThroughExport implements FromView
{
    public function view(): View
    {
        $list = WalkThrough::select('walk_throughs.*','users.name as users_name')->join('users', 'users.id', '=', 'walk_throughs.added_by')->get();
        return view('platform.exports.walk_throughs.excel', compact('list'));
    }
}