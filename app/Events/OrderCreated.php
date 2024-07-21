<?php
namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated
{
    use Dispatchable, SerializesModels;

    public $brandIds;
    public $orderId;

    public function __construct(array $brandIds, int $orderId)
    {
        $this->brandIds = $brandIds;
        $this->orderId = $orderId;
    }
}
