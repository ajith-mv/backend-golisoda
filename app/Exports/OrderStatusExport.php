<?php

namespace App\Exports;


use App\Models\Master\OrderStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class OrderStatusExport implements FromView
{
    public function view(): View
    {
        $list = OrderStatus::select('order_statuses.*','users.name as users_name')->join('users', 'users.id', '=', 'order_statuses.added_by')->get();
        
        return view('platform.exports.order_status.excel', compact('list'));
    }
}
