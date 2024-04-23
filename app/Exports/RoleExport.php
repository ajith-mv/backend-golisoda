<?php

namespace App\Exports;

use App\Models\Settings\Role;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class RoleExport implements FromView
{
    public function view(): View {
        $list = Role::select('*', DB::raw("IF(status = 2, 'Inactive', 'Active') as role_status"))->get();
        return view('platform.exports.roles.excel', compact('list'));
    }
}
