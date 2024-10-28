<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class CurrentOrders extends Component
{
    public $currentOrders;
    public $progress = []; // To store progress for each order
    public $previousStatuses = []; // To track previous statuses
    public $orders;
    public $order;

    public function mount()
    {
        // Load initial orders
        $this->loadOrders();
    }

    public function loadOrders()
{
    \Log::info('Polling orders...');

    // Get the authenticated user
    $user = auth()->user();

    // Load all orders for the current user with statuses: Pending, Preparing, Ready for Pickup, and Completed
    $this->orders = Order::with('payment', 'orderItems.menu')
        ->where('id', $user->id) // Filter by the user ID
        ->whereIn('OrderStatus', ['Pending', 'Preparing', 'Ready for Pickup', 'Completed'])
        ->whereHas('payment', function ($query) {
            $query->where('paymentStatus', 'Successful');
        })
        ->orderBy('created_at', 'desc') // Sort orders by created_at in descending order
        ->get();

    // Filter only the current (not completed) orders to display in the current tab
    $this->currentOrders = $this->orders->filter(function ($order) {
        return in_array($order->OrderStatus, ['Pending', 'Preparing', 'Ready for Pickup']);
    });

    \Log::info('Current Orders:', $this->currentOrders->toArray());

    // Check for status changes, including orders moving to "Completed"
    $this->detectStatusChange();
}



public function detectStatusChange()
    {
        Log::info("detectStatusChange method called.");

        foreach ($this->orders as $order) {
            $orderId = $order->orderId; // Assuming orderId is the correct attribute name
            $currentStatus = $order->OrderStatus;
            $previousStatus = $this->previousStatuses[$orderId] ?? null;

            Log::info("Checking order #{$orderId}: previousStatus={$previousStatus}, currentStatus={$currentStatus}");

            if ($previousStatus !== $currentStatus) {
                // Update the previous status
                $this->previousStatuses[$orderId] = $currentStatus;

                // Handle order completion if status changed to "Completed"
                if ($currentStatus === 'Completed') {
                    $this->handleOrderCompleted($orderId);
                }
            }
        }
    }
    //public function checkForCompletedOrders()
    //{
    //    // Check for completed orders and dispatch event if any
    //    foreach ($this->currentOrders as $order) {
    //        if ($order->OrderStatus === 'Completed') {
    //            $this->handleOrderCompleted();
    //            break; // Exit the loop after finding the first completed order
    //        }
    //    }
    //}

    public function handleOrderCompleted($orderId)
    {
        Log::info("handleOrderCompleted method called for order #{$orderId}.");
        Log::info("Dispatching browser event for order #{$orderId}.");
        $this->dispatch('order-completed',$orderId);
    }
    

    public function calculateProgress($status)
    {
        switch ($status) {
            case 'Pending':
                return 0;
            case 'Preparing':
                return 33;
            case 'Ready for Pickup':
                return 66;
            case 'Completed':
                return 100;
            default:
                return 0; // In case of an unknown status
        }
    }

    public function getProgress($orderStatus)
    {
        // Logic to calculate the progress percentage based on order status
        return $this->calculateProgress($orderStatus);
    }

    public function getIconColor($orderStatus, $iconStatus)
    {
        if ($orderStatus === $iconStatus) {
            return match ($orderStatus) {
                'Pending' => 'orange',
                'Preparing' => 'orange',
                'Ready for Pickup' => 'blue',
                'Completed' => 'green',
                default => 'gray',
            };
        }

        return 'gray';
    }

    public function render()
    {
        // Automatically load orders every time the component re-renders due to polling
        $this->loadOrders();
        
        return view('livewire.current-orders');
    }
}
