<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderProcessed
{
    use Dispatchable, SerializesModels;

    public $order_info;
    public $pickup_details;
    public $variations;

    public function __construct(Order $order_info)
    {
        $this->order_info = $order_info;
    }
}
