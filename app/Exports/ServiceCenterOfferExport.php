<?php

namespace App\Exports;

use App\Models\ServiceCenter;
use App\Models\ServiceCenterOffer;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ServiceCenterOfferExport implements FromView
{
    public function view(): View
    {
        $list = ServiceCenterOffer::select('service_center_offers.*','users.name as users_name','service_centers.title as service_center')
        ->join('users', 'users.id', '=', 'service_center_offers.added_by')
        ->leftJoin('service_centers','service_centers.id','=','service_center_offers.service_center_id')
        ->get();
        return view('platform.exports.service_center_offer.excel', compact('list'));
    }
}