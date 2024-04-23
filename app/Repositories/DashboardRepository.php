<?php
namespace App\Repositories;

use App\Models\Master\Customer;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DashboardRepository
{

    public function getOrderCount($fromDate = '', $toDate = '', $status = '' )
    {
        return Order::when( $fromDate != '', function( $q ) use($fromDate, $toDate){
            $q->whereDate('created_at', '>=', $fromDate);
            $q->whereDate('created_at', '<=', $toDate);
        })->when( $status == '', function($q){
            $q->where('status', '!=', 'pending');
        })->when( $status != '', function($q) use($status){
            $q->where('status', $status);
        })->count();
    }

    public function getProductCount($fromDate = '', $toDate = '' )
    {
        return Product::when( $fromDate != '', function( $q ) use($fromDate, $toDate){
            $q->whereDate('created_at', '>=', $fromDate);
            $q->whereDate('created_at', '<=', $toDate);
        })->where('status', 'published')->count();
    }

    public function getTotalPaymentAmount( $fromDate = '', $toDate = '')
    {
        return Payment::when( $fromDate != '', function( $q ) use($fromDate, $toDate){
            $q->whereDate('created_at', '>=', $fromDate);
            $q->whereDate('created_at', '<=', $toDate);
        })->where('status', 'paid')->sum('amount');
    }

    public function getCustomerCount($fromDate = '', $toDate = '' )
    {
        return Customer::when( $fromDate != '', function( $q ) use($fromDate, $toDate){
            $q->whereDate('created_at', '>=', $fromDate);
            $q->whereDate('created_at', '<=', $toDate);
        })->where('status', 'published')->count();
    }

    public function getNewCustomer()
    {
        return Customer::where('status', 'published')
                        // ->whereDate('created_at', date('Y-m-d'))
                        ->get();
    }

    public function getTopSellingCategory( $fromDate = '', $toDate = '' )
    {
        $details = OrderProduct::select( DB::raw("count(gbs_product_categories.id) as total_count, sum(gbs_order_products.sub_total) as total_amount"), 'product_categories.*' )
                    ->join('orders', 'orders.id', '=', 'order_products.order_id')
                    ->join('products', 'products.id', '=', 'order_products.product_id')
                    ->join('product_categories', 'product_categories.id', '=', 'products.category_id')
                    ->where('orders.status', '!=', 'pending')
                    ->when( $fromDate != '', function( $q ) use($fromDate, $toDate){
                        $q->whereDate('orders.created_at', '>=', $fromDate);
                        $q->whereDate('orders.created_at', '<=', $toDate);
                    })
                    ->groupBy('product_categories.id')
                    ->orderBy('total_amount', 'desc')
                    ->get();
        $amount = [];
        $categories = [];
        if( isset( $details ) && !empty( $details ) ) {
            foreach ( $details as $item ) {
                $amount[] = $item->total_amount;
                $categories[] = $item->name.'('.$item->total_count.')';
            }
        }
        $response = array('amount' => $amount, 'categories' => $categories);
        return $response;
    }

    public function getTopSellingProduct( $fromDate = '', $toDate = '' )
    {
        $details = OrderProduct::select( DB::raw("count(gbs_products.id) as total_count, sum(gbs_order_products.sub_total) as total_amount"), 'products.*' )
                    ->join('orders', 'orders.id', '=', 'order_products.order_id')
                    ->join('products', 'products.id', '=', 'order_products.product_id')
                    ->where('orders.status', '!=', 'pending')
                    ->when( $fromDate != '', function( $q ) use($fromDate, $toDate){
                        $q->whereDate('orders.created_at', '>=', $fromDate);
                        $q->whereDate('orders.created_at', '<=', $toDate);
                    })
                    ->groupBy('products.id')
                    ->orderBy('total_amount', 'desc')
                    ->get();

        $amount = [];
        $categories = [];
        if( isset( $details ) && !empty( $details ) ) {
            foreach ( $details as $item ) {
                $amount[] = $item->total_amount;
                $categories[] = $item->sku.'('.$item->total_count.')';
            }
        }
        $response = array('amount' => $amount, 'categories' => $categories);
        return $response;
    }
    
}