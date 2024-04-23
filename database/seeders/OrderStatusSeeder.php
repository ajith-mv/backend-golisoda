<?php

namespace Database\Seeders;

use App\Models\Master\OrderStatus;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ins['status_name'] = 'Order Initiate';
        $ins['description'] = 'Order init without payment';
        $ins['order'] = '1';
        $ins['added_by'] = '1';
        $ins['status'] = 'published';
        OrderStatus::updateOrCreate(['status_name' => 'Order Initiate'], $ins);

        $ins1['status_name'] = 'Order Placed';
        $ins1['description'] = 'payment was successful';
        $ins1['order'] = '2';
        $ins1['added_by'] = '1';
        $ins1['status'] = 'published';
        OrderStatus::updateOrCreate(['status_name' => 'Order Placed'], $ins1);

        $ins2['status_name'] = 'Order Cancelled';
        $ins2['description'] = 'Cancelled by user and payment failed';
        $ins2['order'] = '3';
        $ins2['added_by'] = '1';
        $ins2['status'] = 'published';
        OrderStatus::updateOrCreate(['status_name' => 'Order Cancelled'], $ins2);

        $ins3['status_name'] = 'Order Shipped';
        $ins3['description'] = 'Order has been shipped';
        $ins3['order'] = '4';
        $ins3['added_by'] = '1';
        $ins3['status'] = 'published';
        OrderStatus::updateOrCreate(['status_name' => 'Order Shipped'], $ins3);

        $ins4['status_name'] = 'Order Delivered';
        $ins4['description'] = 'Order has been delivered';
        $ins4['order'] = '5';
        $ins4['added_by'] = '1';
        $ins4['status'] = 'published';
        OrderStatus::updateOrCreate(['status_name' => 'Order Delivered'], $ins4);

        $ins5['status_name'] = 'Order Cancel Requested';
        $ins5['description'] = 'Order Cancel Request has been received';
        $ins5['order'] = '6';
        $ins5['added_by'] = '1';
        $ins5['status'] = 'published';
        OrderStatus::updateOrCreate(['status_name' => 'Order Cancel Requested'], $ins5);
    }
}
