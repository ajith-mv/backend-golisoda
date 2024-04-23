<?php

namespace App\Exports;

use App\Models\StoreLocator;
use App\Models\StoreLocatorOffer;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StoreLocatorOfferExport implements FromView
{
    public function view(): View
    {
        $list = StoreLocatorOffer::select('store_locator_offers.*','users.name as users_name','store_locators.title as store_locator')
        ->join('users', 'users.id', '=', 'store_locator_offers.added_by')
        ->leftJoin('store_locators','store_locators.id','=','store_locator_offers.store_locator_id')
        ->get();
        return view('platform.exports.store_locator_offer.excel', compact('list'));
    }
}