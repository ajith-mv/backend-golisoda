<?php

namespace App\Http\Controllers;

use App\Models\Master\Brands;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;

class VendorWiseSaleReportController extends Controller
{
    public function index(Request $request)
    {
        $title = "Vendor Wise Sale Report";
        $breadCrum = array('Reports', 'Vendor Wise Sale Report');

        if ($request->ajax()) {

            $date_range = $request->get('date_range');
            $start_date = $end_date = '';
            if (isset($date_range) && !empty($date_range)) {
                $dates = explode('-', $date_range);
                $start_date = date('Y-m-d', strtotime(trim(str_replace('/', '-', $dates[0]))));
                $end_date = date('Y-m-d', strtotime(trim(str_replace('/', '-', $dates[1]))));
            }

            $where = "";
            if (!empty($start_date) && !empty($end_date)) {
                $where = "WHERE DATE(gbs_brand_orders.created_at) >= '$start_date' AND DATE(gbs_brand_orders.created_at) <= '$end_date'";
            }

            $data = DB::table(DB::raw("(SELECT gbs_brands.id as id, gbs_brands.brand_name as brand_name,
            sum(qty*price) as sale_amount,
            CASE
        WHEN gbs_brand_orders.commission_type = 'percentage' THEN ROUND(SUM(qty*price * gbs_brand_orders.commission_value / 100),2)
        ELSE 0
    END AS com_percentage,
    CASE
        WHEN gbs_brand_orders.commission_type = 'fixed' THEN ROUND(SUM(qty * gbs_brand_orders.commission_value),2)
        ELSE 0
    END AS com_amount
            FROM gbs_brand_orders
            JOIN gbs_brands ON gbs_brands.id = gbs_brand_orders.brand_id $where
            GROUP BY gbs_brand_orders.brand_id, gbs_brand_orders.commission_type) as a"))
                ->select(
                    'id',
                    'brand_name',
                    DB::raw('SUM(sale_amount) as sale_amount'),
                    DB::raw('SUM(com_percentage) as com_percentage'),
                    DB::raw('SUM(com_amount) as com_amount')
                )
                ->groupBy('id');


            $keywords = $request->get('search')['value'];
            $filter_search_data = $request->get('filter_search_data');

            $datatables = DataTables::of($data)
                ->filter(function ($query) use ($keywords, $start_date, $end_date, $filter_search_data) {
                    if ($filter_search_data) {
                        $query->where('id', $filter_search_data);
                    }

                    if ($keywords) {
                        $query->Where('brand_name', 'like', "%{$keywords}%");
                    }
                });

            return $datatables->make(true);
        }


        $params = array(
            'title' => $title,
            'breadCrum' => $breadCrum,
            'vendors' => Brands::select('id', 'brand_name')->where('brand_name', '!=', '')
                ->orderBy('brand_name')->get()
        );

        return view('platform.reports.vendor_wise_sale.list', $params);
    }

}
