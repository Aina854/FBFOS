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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;




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


public function cardsales()
{
    // Calculate today's sales and change from yesterday
    $todaySales = DB::table('orders')
        ->join('payments', 'orders.orderId', '=', 'payments.orderId')
        ->whereDate('orders.created_at', now()->format('Y-m-d'))
        ->where('orders.orderStatus', '=', 'Completed') // Filter for completed orders
        ->sum('payments.paymentAmount');
    
    $yesterdaySales = DB::table('orders')
        ->join('payments', 'orders.orderId', '=', 'payments.orderId')
        ->whereDate('orders.created_at', now()->subDays(1)->format('Y-m-d'))
        ->where('orders.orderStatus', '=', 'Completed') // Filter for completed orders
        ->sum('payments.paymentAmount');
    
    $todayChange = $yesterdaySales > 0 ? (($todaySales - $yesterdaySales) / $yesterdaySales) * 100 : ($todaySales > 0 ? 100 : 0);
    $todayChange = number_format(min($todayChange, 100), 2);

    // Calculate weekly sales and change from last week
    $weeklySales = DB::table('orders')
    ->join('payments', 'orders.orderId', '=', 'payments.orderId')
    ->where('orders.created_at', '>=', now()->startOfWeek(Carbon::SUNDAY)) // Start of current week (Sunday)
    ->where('orders.orderStatus', '=', 'Completed') // Filter for completed orders
    ->sum('payments.paymentAmount');

    $lastWeekSales = DB::table('orders')
    ->join('payments', 'orders.orderId', '=', 'payments.orderId')
    ->whereBetween('orders.created_at', [
        now()->subWeek()->startOfWeek(Carbon::SUNDAY), // Start of last week (Sunday)
        now()->startOfWeek(Carbon::SUNDAY) // Start of current week (Sunday)
    ])
    ->where('orders.orderStatus', '=', 'Completed') // Filter for completed orders
    ->sum('payments.paymentAmount');

    $weeklyChange = $lastWeekSales > 0 ? (($weeklySales - $lastWeekSales) / $lastWeekSales) * 100 : ($weeklySales > 0 ? 100 : 0);
    $weeklyChange = number_format(min($weeklyChange, 100), 2);


    // Calculate monthly sales and change from last month
    $monthlySales = DB::table('orders')
        ->join('payments', 'orders.orderId', '=', 'payments.orderId')
        ->whereBetween('orders.created_at', [now()->startOfMonth(), now()->endOfMonth()])
        ->where('orders.orderStatus', '=', 'Completed') // Filter for completed orders
        ->sum('payments.paymentAmount');
    
    $lastMonthSales = DB::table('orders')
        ->join('payments', 'orders.orderId', '=', 'payments.orderId')
        ->whereBetween('orders.created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
        ->where('orders.orderStatus', '=', 'Completed') // Filter for completed orders
        ->sum('payments.paymentAmount');
    
    $monthlyChange = $lastMonthSales > 0 ? (($monthlySales - $lastMonthSales) / $lastMonthSales) * 100 : ($monthlySales > 0 ? 100 : 0);
    $monthlyChange = number_format(min($monthlyChange, 100), 2);

    // Calculate yearly sales and change from last year
    $yearlySales = DB::table('orders')
        ->join('payments', 'orders.orderId', '=', 'payments.orderId')
        ->whereBetween('orders.created_at', [now()->startOfYear(), now()->endOfYear()])
        ->where('orders.orderStatus', '=', 'Completed') // Filter for completed orders
        ->sum('payments.paymentAmount');
    
    $lastYearSales = DB::table('orders')
        ->join('payments', 'orders.orderId', '=', 'payments.orderId')
        ->whereBetween('orders.created_at', [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()])
        ->where('orders.orderStatus', '=', 'Completed') // Filter for completed orders
        ->sum('payments.paymentAmount');
    
    $yearlyChange = $lastYearSales > 0 ? (($yearlySales - $lastYearSales) / $lastYearSales) * 100 : ($yearlySales > 0 ? 100 : 0);
    $yearlyChange = number_format(min($yearlyChange, 100), 2);

    // Return calculated data
    return [
        'todaySales' => $todaySales,
        'todayChange' => $todayChange,
        'weeklySales' => $weeklySales,
        'weeklyChange' => $weeklyChange,
        'monthlySales' => $monthlySales,
        'monthlyChange' => $monthlyChange,
        'yearlySales' => $yearlySales,
        'yearlyChange' => $yearlyChange,
    ];
}

public function dailySalesData()
{
    // Call the cardsales method to get card data
    $cardData = $this->cardsales();

    // Fetch daily sales data for the last 7 days
    $dailySales = DB::table('orders')
        ->join('payments', 'orders.orderId', '=', 'payments.orderId')
        ->select(
            DB::raw('DATE(orders.created_at) as order_date'),
            DB::raw('SUM(payments.paymentAmount) as daily_total')
        )
        ->where('orders.created_at', '>=', now()->subDays(6))
        ->where('orders.orderStatus', '=', 'Completed') // Filter for completed orders
        ->groupBy('order_date')
        ->orderBy('order_date', 'asc')
        ->get();

    $dates = [];
    $salesData = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i)->format('Y-m-d');
        $dates[] = $date;
        $dailyTotal = $dailySales->firstWhere('order_date', $date);
        $salesData[] = $dailyTotal ? (float) $dailyTotal->daily_total : 0.00;
    }

    // Call getDailySalesSummary to get today's menu item sales data
    $dailySalesSummaryData = $this->getDailySalesSummary();
    $dailySalesSummary = $dailySalesSummaryData['dailySalesSummary'];
    $totalRevenueSum = $dailySalesSummaryData['totalRevenueSum'];

    // Merge all data and pass it to the view
    return view('admin.dailysales', array_merge([
        'orderDates' => $dates,
        'dailyTotals' => $salesData,
        'dailySalesSummary' => $dailySalesSummary,
        'totalRevenueSum' => $totalRevenueSum
    ], $cardData));
}


public function getDailySalesSummary()
{
    // Get today's date
    $today = now()->format('Y-m-d');

    // Retrieve daily sales summary for each menu item
    $dailySalesSummary = DB::table('orders')
        ->join('orderitems', 'orders.orderId', '=', 'orderitems.orderId')
        ->join('menus', 'orderitems.menuId', '=', 'menus.menuId')
        ->select(
            'menus.menuId',
            'menus.menuName',
            'menus.price as pricePerUnit',
            DB::raw('SUM(orderitems.quantity) as quantitySold'),
            DB::raw('SUM(orderitems.quantity * menus.price) as totalRevenue')
        )
        ->whereDate('orders.created_at', $today) // Filter for today's orders
        ->where('orders.orderStatus', '=', 'Completed') // Filter for completed orders
        ->groupBy('menus.menuId', 'menus.menuName', 'menus.price')
        ->orderBy('menus.menuId')
        ->get();

    // Calculate the total revenue sum
    $totalRevenueSum = $dailySalesSummary->sum('totalRevenue');

    // Return both the summary and the total revenue sum as an array
    return [
        'dailySalesSummary' => $dailySalesSummary,
        'totalRevenueSum' => $totalRevenueSum
    ];
}
public function weeklySalesData()
{
    // Call the cardsales method to get card data
    $cardData = $this->cardsales();

    // Fetch sales data for the last 7 weeks
    $salesData = DB::table('orders')
        ->join('payments', 'orders.orderId', '=', 'payments.orderId')
        ->select(
            'orders.created_at',
            DB::raw('SUM(payments.paymentAmount) as paymentAmount')
        )
        ->where('orders.created_at', '>=', now()->subWeeks(7))
        ->where('orders.orderStatus', '=', 'Completed') // Filter for completed orders
        ->groupBy('orders.created_at')
        ->orderBy('orders.created_at', 'asc')
        ->get();

    // Log the fetched sales data for debugging
    \Log::info('Fetched Sales Data:', $salesData->toArray());

    // Prepare data structure for 8-week sales totals (7 past + 1 current)
    $weeklySales = [];

    // Add the last 7 weeks first
    for ($i = 0; $i < 7; $i++) {
        // Get Sunday of each week for the last 7 weeks
        $weekStartDate = now()->subWeeks($i)->next(Carbon::SUNDAY);
        $weekLabel = "Week of " . $weekStartDate->format('M d, Y');

        // Initialize week with a 0 total if no data is present
        $weeklySales[$weekLabel] = 0.00;
    }

    // Then add the current week
    $currentWeekStartDate = now()->startOfWeek(Carbon::SUNDAY);
    $currentWeekLabel = "Week of " . $currentWeekStartDate->format('M d, Y');
    $weeklySales[$currentWeekLabel] = 0.00; // Initialize current week

    // Accumulate sales data by week
    foreach ($salesData as $sale) {
        // Get the Sunday start date of the week for this order
        $orderDate = Carbon::parse($sale->created_at);
        $weekStartDate = $orderDate->copy()->startOfWeek(Carbon::SUNDAY);
        
        // Custom label for the week of the order date
        $weekLabel = "Week of " . $weekStartDate->format('M d, Y');

        // Add to weekly sales total
        if (isset($weeklySales[$weekLabel])) {
            $weeklySales[$weekLabel] += $sale->paymentAmount;
        }
    }

    // Prepare final data for view
    $dates = array_keys($weeklySales);
    $salesTotals = array_values($weeklySales);

    // Reverse arrays to show from oldest week to current
    $dates = array_reverse($dates);
    $salesTotals = array_reverse($salesTotals);

    // Log the formatted dates and sales data for debugging
    \Log::info('Formatted Dates:', $dates);
    \Log::info('Sales Data:', $salesTotals);

    // Call getDailySalesSummary to get today's menu item sales data
    $weeklySalesSummaryData = $this->getweeklySalesSummary();
    $weeklySalesSummary = $weeklySalesSummaryData['weeklySalesSummary'];
    $totalRevenueSum = $weeklySalesSummaryData['totalRevenueSum'];
    $dailyTotals = $weeklySalesSummaryData['dailyTotals'];

    // Merge all data and pass it to the view
    return view('admin.weeklysales', array_merge([
        'orderDates' => $dates,
        'weeklyTotals' => $salesTotals,
        'weeklySalesSummary' => $weeklySalesSummary,
        'totalRevenueSum' => $totalRevenueSum,
        'dailyTotals' => $dailyTotals
    ], $cardData));
}


public function getWeeklySalesSummary()
{
    // Get the date range for the last 7 days
    $startOfWeek = now()->subDays(6)->startOfDay();
    $endOfWeek = now()->endOfDay();

    // Retrieve weekly sales summary for each menu item grouped by day
    $weeklySalesSummary = DB::table('orders')
        ->join('orderitems', 'orders.orderId', '=', 'orderitems.orderId')
        ->join('menus', 'orderitems.menuId', '=', 'menus.menuId')
        ->select(
            DB::raw('DATE(orders.created_at) as orderDate'),
            'menus.menuId',
            'menus.menuName',
            'menus.price as pricePerUnit',
            DB::raw('SUM(orderitems.quantity) as quantitySold'),
            DB::raw('SUM(orderitems.quantity * menus.price) as totalRevenue')
        )
        ->whereBetween('orders.created_at', [$startOfWeek, $endOfWeek])
        ->where('orders.orderStatus', '=', 'Completed') // Filter for completed orders
        ->groupBy('orderDate', 'menus.menuId', 'menus.menuName', 'menus.price', 'orders.created_at')
        ->orderBy('orderDate', 'asc')
        ->get();

    // Calculate the total revenue for each day
    $dailyTotals = $weeklySalesSummary->groupBy('orderDate')->map(function ($items) {
        return $items->sum('totalRevenue');
    });

    // Calculate the total revenue sum for the week
    $totalRevenueSum = $weeklySalesSummary->sum('totalRevenue');

    // Return both the summary and the total revenue sum as an array
    return [
        'weeklySalesSummary' => $weeklySalesSummary,
        'dailyTotals' => $dailyTotals,
        'totalRevenueSum' => $totalRevenueSum
    ];
}

public function monthlySalesData()
{
    // Call the cardsales method to get card data
    $cardData = $this->cardsales();

    // Fetch sales data for the last 7 months
    $salesData = DB::table('orders')
        ->join('payments', 'orders.orderId', '=', 'payments.orderId')
        ->select(
            DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m") as month'), // Format the date to "YYYY-MM"
            DB::raw('SUM(payments.paymentAmount) as paymentAmount')
        )
        ->where('orders.created_at', '>=', now()->subMonths(7)) // Get data for the last 7 months
        ->where('orders.orderStatus', '=', 'Completed') // Filter for completed orders
        ->groupBy('month') // Group by formatted month
        ->orderBy('month', 'asc') // Order by month
        ->get();

    // Log the fetched sales data for debugging
    \Log::info('Fetched Sales Data:', $salesData->toArray());

    // Prepare data structure for 7-month sales totals
    $monthlySales = [];

    // Initialize the last 7 months with zero totals
    for ($i = 0; $i < 7; $i++) {
        // Get the first day of each month for the last 7 months
        $monthStartDate = now()->subMonths($i)->startOfMonth();
        $monthLabel = $monthStartDate->format('F Y'); // Get month name and year

        // Initialize month with a 0 total if no data is present
        $monthlySales[$monthLabel] = 0.00;
    }

    // Accumulate sales data by month
    foreach ($salesData as $sale) {
        // Custom label for the month of the sale
        $monthLabel = Carbon::parse($sale->month)->format('F Y');

        // Add to monthly sales total
        if (isset($monthlySales[$monthLabel])) {
            $monthlySales[$monthLabel] += $sale->paymentAmount;
        }
    }

    // Prepare final data for view
    $dates = array_keys($monthlySales);
    $salesTotals = array_values($monthlySales);

    // Reverse the arrays to start with the oldest month
    $dates = array_reverse($dates);
    $salesTotals = array_reverse($salesTotals);

    // Log the formatted dates and sales data for debugging
    \Log::info('Formatted Dates:', $dates);
    \Log::info('Sales Data:', $salesTotals);

    // Call getDailySalesSummary to get today's menu item sales data
    $monthlySalesSummaryData = $this->getmonthlySalesSummary();
    $monthlySalesSummary = $monthlySalesSummaryData['monthlySalesSummary'];
    $totalRevenueSum = $monthlySalesSummaryData['totalRevenueSum'];
   
    // Merge all data and pass it to the view
    return view('admin.monthlysales', array_merge([
        'orderDates' => $dates,
        'monthlyTotals' => $salesTotals,
        'monthlySalesSummary' => $monthlySalesSummary,
        'totalRevenueSum' => $totalRevenueSum
    ], $cardData));
}


public function getMonthlySalesSummary()
{
    // Get the start and end of the current month
    $startOfMonth = now()->startOfMonth();
    $endOfMonth = now()->endOfMonth();

    // Retrieve monthly sales summary for each menu item
    $monthlySalesSummary = DB::table('orders')
        ->join('orderitems', 'orders.orderId', '=', 'orderitems.orderId')
        ->join('menus', 'orderitems.menuId', '=', 'menus.menuId')
        ->select(
            'menus.menuId',
            'menus.menuName',
            'menus.price as pricePerUnit',
            DB::raw('SUM(orderitems.quantity) as quantitySold'),
            DB::raw('SUM(orderitems.quantity * menus.price) as totalRevenue')
        )
        ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
        ->where('orders.orderStatus', '=', 'Completed') // Filter for completed orders
        ->groupBy('menus.menuId', 'menus.menuName', 'menus.price')
        ->orderBy('menus.menuName', 'asc')
        ->get();

    // Calculate total revenue sum for the month
    $totalRevenueSum = $monthlySalesSummary->sum('totalRevenue');

    // Return the summary and total revenue sum
    return [
        'monthlySalesSummary' => $monthlySalesSummary,
        'totalRevenueSum' => $totalRevenueSum,
    ];
}

public function yearlySalesData()
{
    // Call the cardsales method to get card data
    $cardData = $this->cardsales();

    // Fetch sales data for the last 3 years
    $salesData = DB::table('orders')
        ->join('payments', 'orders.orderId', '=', 'payments.orderId')
        ->select(
            DB::raw('YEAR(orders.created_at) as year'), // Extract the year from the created_at date
            DB::raw('SUM(payments.paymentAmount) as paymentAmount') // Sum the payment amounts
        )
        ->where('orders.created_at', '>=', now()->subYears(3)) // Get data for the last 3 years
        ->where('orders.orderStatus', '=', 'Completed') // Filter for completed orders
        ->groupBy('year') // Group by year
        ->orderBy('year', 'asc') // Order by year
        ->get();

    // Log the fetched sales data for debugging
    \Log::info('Fetched Sales Data:', $salesData->toArray());

    // Prepare data structure for yearly sales totals
    $yearlySales = [];

    // Initialize the last 3 years with zero totals
    for ($i = 0; $i < 3; $i++) {
        // Get the year for the last 3 years (2022, 2023, and current year)
        $year = now()->subYears(2 - $i)->year; // This will give 2022, 2023, 2024
        $yearlySales[$year] = 0.00; // Initialize year with a 0 total if no data is present
    }

    // Accumulate sales data by year
    foreach ($salesData as $sale) {
        // Add to yearly sales total
        if (isset($yearlySales[$sale->year])) {
            $yearlySales[$sale->year] += $sale->paymentAmount;
        }
    }

    // Prepare final data for view
    $years = array_keys($yearlySales);
    $salesTotals = array_values($yearlySales);

    // Log the formatted years and sales data for debugging
    \Log::info('Formatted Years:', $years);
    \Log::info('Sales Data:', $salesTotals);

    // Call getYearlySalesSummary to get yearly summary data
    $yearlySalesSummaryData = $this->getyearlySalesSummary();
    $yearlySalesSummary = $yearlySalesSummaryData['yearlySalesSummary'];
    $totalRevenueSum = $yearlySalesSummaryData['totalRevenueSum'];
    $monthlyTotals = $yearlySalesSummaryData['monthlyTotals'];

    // Merge all data and pass it to the view
    return view('admin.yearlysales', array_merge([
        'orderYears' => $years,
        'yearlyTotals' => $salesTotals,
        'yearlySalesSummary' => $yearlySalesSummary,
        'totalRevenueSum' => $totalRevenueSum,
        'monthlyTotals' => $monthlyTotals
    ], $cardData));
}


public function getYearlySalesSummary()
{
    // Get the date range for the last 12 months
    $startOfYear = now()->subMonths(11)->startOfMonth();
    $endOfYear = now()->endOfMonth();

    // Retrieve yearly sales summary for each menu item grouped by month
    $yearlySalesSummary = DB::table('orders')
        ->join('orderitems', 'orders.orderId', '=', 'orderitems.orderId')
        ->join('menus', 'orderitems.menuId', '=', 'menus.menuId')
        ->select(
            DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m") as orderMonth'), // Format to "YYYY-MM"
            'menus.menuId',
            'menus.menuName',
            'menus.price as pricePerUnit',
            DB::raw('SUM(orderitems.quantity) as quantitySold'),
            DB::raw('SUM(orderitems.quantity * menus.price) as totalRevenue')
        )
        ->whereBetween('orders.created_at', [$startOfYear, $endOfYear]) // Use yearly range
        ->where('orders.orderStatus', '=', 'Completed') // Filter for completed orders
        ->groupBy('orderMonth', 'menus.menuId', 'menus.menuName', 'menus.price')
        ->orderBy('orderMonth', 'asc')
        ->get();

    // Calculate the total revenue for each month
    $monthlyTotals = $yearlySalesSummary->groupBy('orderMonth')->map(function ($items) {
        return $items->sum('totalRevenue');
    });

    // Calculate the total revenue sum for the year
    $totalRevenueSum = $yearlySalesSummary->sum('totalRevenue');

    // Return both the summary and the total revenue sum as an array
    return [
        'yearlySalesSummary' => $yearlySalesSummary,
        'monthlyTotals' => $monthlyTotals,
        'totalRevenueSum' => $totalRevenueSum
    ];
}


}



