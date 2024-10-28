<?php

namespace App\Http\Controllers;
use App\Events\OrderStatusUpdated; // Import the event at the top of your controller
use Illuminate\Http\Request;
use App\Models\Order; // Include your Order model
use App\Models\Payment; // Include your Order model
use App\Models\OrderItem; // Include your OrderItem model
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;
use Livewire\Component;




class OrderController extends Controller
{
    public function index(Request $request)
{
    // Get the authenticated user
    $user = auth()->user();

    // Determine which tab to display
    $tab = $request->input('tab', 'current'); // Default to 'pending' if no tab specified

    // Initialize order variables
    $currentOrders = collect(); // For current orders
    $pendingOrders = collect(); // For pending orders
    $pastOrders = collect(); // For past orders

    // Fetch current orders (Preparing and Ready for Pickup) with successful payment status
    if ($tab == 'current' || $tab == 'pending' || $tab == 'history') {
        $currentOrders = Order::with(['orderItems.menu', 'payment'])
            ->where('id', $user->id) // Use user_id for filtering
            ->whereIn('OrderStatus', ['Preparing', 'Ready for Pickup', 'Completed']) // Only current order statuses
            ->whereHas('payment', function($query) {
                $query->where('paymentStatus', 'Successful'); // Ensure successful payment status
            })
            ->orderBy('created_at', 'desc') // Sort orders by created_at in descending order
            ->get();
    }

    // Fetch pending orders where payment status is pending
    if ($tab == 'pending' || $tab == 'current' || $tab == 'history') {
        $pendingOrders = Order::with(['orderItems.menu', 'payment'])
            ->where('id', $user->id) // Use user_id for filtering
            ->where('OrderStatus', 'Pending')
            ->whereHas('payment', function($query) {
                $query->where('paymentStatus', 'Pending'); // Ensure pending payment status
            })
            ->orderBy('created_at', 'desc') // Sort orders by created_at in descending order
            ->get();
    }

    // Fetch past orders for the user
    if ($tab == 'history' ||$tab == 'current' || $tab == 'pending') {
        $pastOrders = Order::with(['orderItems.menu', 'payment'])
            ->where('id', $user->id) // Use user_id for filtering
            ->whereIn('OrderStatus', ['Completed', 'Failed']) // Assuming you want only completed past orders
            ->orderBy('created_at', 'desc') // Sort orders by created_at in descending order
            ->get();
    }

    // Return the view with all orders and the selected tab
    return view('orders.index', compact('currentOrders', 'pendingOrders', 'pastOrders', 'tab'));
}


    public function show($orderId)
{
    // Find the order by ID
    $order = Order::with('orderItems.menu')->find($orderId);

    // Check if the order exists
    if (!$order) {
        return redirect()->back()->with('alert', 'Order not found.');
    }

    // Return the view with the order details
    return view('orders.show', compact('order'));
}


public function getSidebarData()
{
    // Count orders with 'Pending' OrderStatus and 'Successful' PaymentStatus
    $incomingOrderCount = Order::join('payments', 'orders.orderId', '=', 'payments.orderId')
        ->where('orders.OrderStatus', 'Pending')
        ->where('payments.PaymentStatus', 'Successful')
        ->count();

    // Count orders with 'Preparing' or 'Ready for Pickup' OrderStatus
    $orderDetailCount = Order::whereIn('OrderStatus', ['Preparing', 'Ready for Pickup'])->count();

    // Count completed orders
    $completedOrderCount = Order::where('OrderStatus', 'Completed')->count();

    // Count failed orders
    $rejectedOrderCount = Order::where('OrderStatus', 'Failed')->count();

    return compact('incomingOrderCount', 'orderDetailCount', 'completedOrderCount', 'rejectedOrderCount');
}


public function incomingOrders()
{
    // Fetch all incoming orders with successful payments
    $orders = Order::with(['payment', 'orderItems.menu'])
        ->whereIn('OrderStatus', ['Pending', 'Preparing', 'Ready for Pickup'])
        ->whereHas('payment', function ($query) {
            $query->where('PaymentStatus', 'Successful'); // Only orders with successful payments
        })
        //->orderBy('created_at', 'desc') // Sort orders by created_at in descending order
        ->get();

    // Get sidebar data
    $sidebarData = $this->getSidebarData();

    // Fetch incoming orders that are still pending, with successful payments
    $incomingOrders = Order::with(['orderItems.menu', 'payment'])
        ->whereIn('OrderStatus', ['Pending'])
        ->whereHas('payment', function ($query) {
            $query->where('PaymentStatus', 'Successful'); // Filter by successful payment
        })
        //->orderBy('created_at', 'desc') // Sort orders by created_at in descending order
        ->get()
        ->map(function ($order) {
            // Calculate order status percentage
            switch ($order->OrderStatus) {
                case 'Pending':
                    $order->orderStatusPercentage = 0;
                    break;
                case 'Preparing':
                    $order->orderStatusPercentage = 50; // Assuming 50% for Preparing
                    break;
                case 'Ready for Pickup':
                    $order->orderStatusPercentage = 75; // Assuming 75% for Ready for Pickup
                    break;
                default:
                    $order->orderStatusPercentage = 0; // Default case
            }
            return $order;
        });

    return view('orders.incoming', compact('incomingOrders', 'orders', 'sidebarData'));
}


    // Method to accept an order
    public function accept($orderId)
{
    Log::info("Accepting order with ID: $orderId");

    $order = Order::find($orderId);
    if ($order) {
        Log::info("Order found: ", ['orderId' => $order->id, 'previousStatus' => $order->OrderStatus]);

        $order->OrderStatus = 'Preparing';
        $order->save();

        // Broadcast the event with progress update
        
        event(new OrderStatusUpdated($order->id, $order->OrderStatus, 50)); // Assuming 50% for 'Preparing'
        Log::info("Order status updated: ", ['orderId' => $order->id, 'newStatus' => $order->OrderStatus]);

        if (request()->ajax()) {
            return response()->json(['success' => "Order #{$order->orderId} accepted.", 'orderId' => $order->orderId]);
        }

        return redirect()->route('staff.orders.incoming')->with('success', "Order #{$order->orderId} accepted.");
    }

    Log::warning("Order not found: ", ['orderId' => $orderId]);

    if (request()->ajax()) {
        return response()->json(['error' => 'Order not found.']);
    }

    return redirect()->route('staff.orders.incoming')->with('error', 'Order not found.');
}



    // Method to reject an order
    public function reject($orderId)
{
    Log::info("Rejecting order with ID: $orderId");

    $order = Order::findOrFail($orderId); // This will throw an exception if not found
    if ($order) {
        Log::info("Order found: ", ['orderId' => $order->id, 'previousStatus' => $order->OrderStatus]);

        $order->OrderStatus = 'Failed';
        $order->save();

        // Broadcast the event with progress update
        event(new OrderStatusUpdated($order->id, $order->OrderStatus, 0)); // Assuming 0% for 'Failed'
        Log::info("Order status updated: ", ['orderId' => $order->id, 'newStatus' => $order->OrderStatus]);

        return redirect()->route('staff.orders.rejected')->with('success', "Order #{$order->orderId} has been rejected.");
    }

    Log::warning("Order not found for rejection: ", ['orderId' => $orderId]);
    return redirect()->route('staff.orders.rejected')->with('error', 'Order not found.');
}




    public function details()
{

    // Get sidebar data
    $sidebarData = $this->getSidebarData(); 

    // Fetch orders for "Preparing" and "Ready for Pickup"
    $preparingOrders = Order::where('OrderStatus', 'Preparing')->get();
    $readyForPickupOrders = Order::where('OrderStatus', 'Ready for Pickup')->get();

    // Retrieve only orders with status Preparing or Ready for Pickup
    $orders = Order::with(['payment', 'orderItems.menu'])
        ->whereIn('OrderStatus', ['Preparing', 'Ready for Pickup']) // Filter for specific statuses
        //->orderBy('created_at', 'desc') // Sort orders by created_at in descending order
        ->get()
        ->map(function ($order) {
            // Calculate order status percentage
            switch ($order->OrderStatus) {
                case 'Pending':
                    $order->progress = 0;
                    break;
                case 'Preparing':
                    $order->progress = 50; // Assuming 50% for Preparing
                    break;
                case 'Ready for Pickup':
                    $order->progress = 75; // Assuming 75% for Ready for Pickup
                    break;
                case 'Completed':
                    $order->progress = 100; // Assuming 100% for Completed
                    break;
                default:
                    $order->progress = 0; // Default case for unexpected statuses
            }
            return $order;
        });

    return view('orders.order-details', compact('orders', 'sidebarData', 'preparingOrders', 'readyForPickupOrders'));
}

public function updateStatus(Request $request, $orderId)
{
    Log::info("Updating status for order ID: $orderId");

    $request->validate([
        'OrderStatus' => 'required|string|in:Ready for Pickup,Completed',
    ]);

    $order = Order::findOrFail($orderId);
    Log::info("Order found: ", ['orderId' => $order->id, 'previousStatus' => $order->OrderStatus]);

    // Update the order status
    $order->OrderStatus = $request->OrderStatus;
    $order->save();

    // Calculate progress based on status
    //$progress = ($order->OrderStatus == 'Ready for Pickup') ? 75 : 100; // 75% for 'Ready for Pickup', 100% for 'Completed'
//
    //// Store status update in session
    //session()->flash('orderStatusUpdated', [
    //    'id' => $order->id,
    //    'status' => $order->OrderStatus,
    //    'progress' => $progress,
    //]);

    //Log::info("Order status updated: ", [
    //    'orderId' => $order->id,
    //    'newStatus' => $order->OrderStatus,
    //    'progress' => $progress,
    //]);

    return redirect()->back()->with('success', 'Order status updated successfully!');
}



    public function completedOrders()
    {
        // Fetch completed orders
        $completedOrders = Order::where('OrderStatus', 'Completed')->with('payment', 'orderItems.menu')->get();

        // Get sidebar data
        $sidebarData = $this->getSidebarData(); 

        // Return the completed orders view
        return view('orders.completed-orders', compact('completedOrders', 'sidebarData'));
    }

    public function rejectedOrders()
{
    // Fetch the rejected orders
    $rejectedOrders = Order::where('OrderStatus', 'Failed')->with(['payment', 'orderItems'])->get();

    // Get sidebar data
    $sidebarData = $this->getSidebarData(); 

    // Pass both rejected orders and sidebar data to the view
    return view('orders.rejected-orders', compact('rejectedOrders', 'sidebarData'));
}





}
