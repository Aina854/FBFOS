<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;

class OrderStatusUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderId;
    public $orderStatus;
    public $progress;

    public function __construct($orderId, $orderStatus, $progress)
    {
        $this->orderId = $orderId;
        $this->orderStatus = $orderStatus;
        $this->progress = $progress;
    }

    public function broadcastOn()
    {
        // Adjust this channel if needed
        return new Channel('orders');
    }

    public function broadcastAs()
    {
        return 'order.status.updated';
    }
}
