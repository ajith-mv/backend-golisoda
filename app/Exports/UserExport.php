<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class UserExport implements FromView
{
    public function view(): View
    {
        $list = User::select('users.name','users.added_by','email','mobile_no','address','users.created_at','roles.name as role_name', DB::raw(" IF(gbs_users.status = 2, 'Inactive', 'Active') as user_status"))->join('roles', 'roles.id', '=', 'users.role_id')->where('users.is_super_admin','!=',1)->get();

        return view('platform.exports.users.excel', compact('list'));
    }
}
