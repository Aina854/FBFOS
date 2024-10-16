<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class FeedbackController extends Controller
{
    // Display a listing of the feedback
    public function index($orderId)
{
    // Fetch feedback for the specific order using the orderId
    $feedbacks = Feedback::with(['orderItem.menu', 'user'])
        ->whereHas('orderItem', function ($query) use ($orderId) {
            $query->where('orderId', $orderId); // Filter by orderId from OrderItems
        })
        ->get();

        if (!$feedbacks) {
            return redirect()->back()->with('error', 'Feedback not found.');
        }
    // Pass the feedbacks to the view
    return view('feedback.index', compact('feedbacks', 'orderId'));
}

    

    // Show the form for creating a new feedback
    public function create($orderId) // Accepting $orderId to fetch all order items for the order
    {
        \Log::info('Creating feedback for Order ID:', ['orderId' => $orderId]);
    
        // Fetch the order
        $order = Order::find($orderId);
    
        // Check if the order exists
        if (!$order) {
            return redirect()->route('orders.index')->with('error', 'Order not found.');
        }
    
        // Fetch order items without feedback
        $orderItems = OrderItem::getOrderItemsWithoutFeedback($orderId);
    
        // Log the order and order items
        \Log::info('Order:', $order->toArray());
        \Log::info('Order Items:', $orderItems->toArray());
    
        return view('feedback.create', compact('order', 'orderItems'));
    }
    
    public function areAllItemsFeedbackSubmitted($orderId)
    {
        $count = DB::table('orderitems as oi')
            ->leftJoin('feedbacks as f', 'oi.orderItemId', '=', 'f.orderItemId')
            ->where('oi.orderId', $orderId)
            ->whereNull('f.orderItemId')
            ->count();
    
        return $count === 0; // Return true if all items have feedback
    }
    
    public function markFeedbackSubmitted($orderId)
    {
        DB::table('orders')
            ->where('orderId', $orderId)
            ->update(['feedbackSubmitted' => true]);
    }
    

    // Store a newly created feedback in storage
public function store(Request $request)
{
    // Log the incoming request data for debugging
    \Log::info('Feedback form submitted:', $request->all());

    // Validate the request
    $validatedData = $request->validate([
        'orderItemId' => 'required|integer',
        'rating' => 'required|integer|min:1|max:5',
        'comments' => 'required|string|max:1000',
        'orderId' => 'required|integer', // Ensure orderId is also validated
        'anonymous' => 'nullable|string|in:yes,no',
    ]);

    // Log the validated data
    \Log::info('Validated feedback data:', $validatedData);

    // Extract validated data
    $orderItemId = $validatedData['orderItemId'];
    $rating = $validatedData['rating'];
    $comments = $validatedData['comments'];
    $anonymous = $validatedData['anonymous'] ?? 'no'; // Default to 'no' if not provided
    $customerId = $request->session()->get('customer'); // Get customer ID from session

    // Create feedback in the database
    Feedback::create([
        'id' => $request->input('id'), // You might want to remove this if the ID is auto-incremented
        'orderItemId' => $orderItemId,
        'comments' => $comments,
        'rating' => $rating,
        'anonymous' => $anonymous,
        'commentsTime' => now(), // Set the current timestamp
    ]);

    // Check if all items in the order have received feedback
    if ($this->areAllItemsFeedbackSubmitted($validatedData['orderId'])) {
        $this->markFeedbackSubmitted($validatedData['orderId']);
    }

    return redirect()->route('feedback.index', ['orderId' => $validatedData['orderId']])->with('success', 'Feedback submitted successfully!');

}

public function feedbackForStaff()
{
    // Retrieve all feedbacks for staff (you can adjust the query based on your feedback structure)
    $feedbacks = Feedback::whereNull('staffResponse') // Use whereNull for checking null values
                         ->orderBy('created_at', 'desc')   // Order by latest feedback
                         ->get();

    // Return a view with feedback data
    return view('feedback.feedbackstaff', compact('feedbacks'));
}


public function feedbackPast() 
{
    // Retrieve all feedbacks that have a staff response
    $feedbacks = Feedback::whereNotNull('staffResponse') // Select feedbacks with a response
                         ->orderBy('created_at', 'desc') // Order by the latest feedback
                         ->get();

    // Return a view with feedback data
    return view('feedback.feedbackpast', compact('feedbacks'));
}

public function submitResponse(Request $request, $orderItemId)
{
    // Validate the request
    $request->validate([
        'staffResponse' => 'required|string|max:255',
    ]);

    // Find the feedback by orderItemId
    $feedback = Feedback::where('orderItemId', $orderItemId)->firstOrFail();

    // Check if a staff response already exists
    if ($feedback->staffResponse) {
        return redirect()->back()->with('error', 'A response has already been submitted for this feedback.');
    }

    // Update the feedback with the staff's response and response timestamp
    $feedback->staffResponse = $request->input('staffResponse');
    $feedback->responseTimestamp = now(); // Store the current timestamp
    $feedback->save();

    // Redirect back with a success message
    return redirect()->back()->with('success', 'Your response has been submitted successfully.');
}


    // Show the specified feedback
    public function show($id)
    {
        $feedback = Feedback::with(['orderItem', 'user'])->findOrFail($id);
        return view('feedback.show', compact('feedback'));
    }

    // Show the form for editing the specified feedback
    public function edit($id)
    {
        $feedback = Feedback::findOrFail($id);
        return view('feedback.edit', compact('feedback'));
    }

    // Update the specified feedback in storage
    public function update(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'required|string|max:1000',
            'staffResponse' => 'nullable|string|max:1000',
        ]);

        $feedback = Feedback::findOrFail($id);
        $feedback->update($request->all());

        return redirect()->route('feedback.index')->with('success', 'Feedback updated successfully!');
    }

    // Remove the specified feedback from storage
    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();

        return redirect()->route('feedback.index')->with('success', 'Feedback deleted successfully!');
    }
}
