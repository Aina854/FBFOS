<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;
use Illuminate\Support\Facades\Log as LogFacade; // Alias the Log facade
use App\Models\Log;
use App\Models\Order;
use App\Models\Payment;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set the Stripe API key
        Stripe::setApiKey('sk_test_51Q50H5HFO4bNfGB64aMg9YlREfOJzP68qTMOw3g4EFzZhVlT8VeVTYzkc3OvMMoDXV10KpVwOYhCYfRMGYuch01k00JFpNmXqx');
        
    }

    public function proceedToPayment(Request $request, $cartId)
{
    $order = null;
    $totalAmount = 0;
    $payment = null;
    $lineItems = []; // Define lineItems here

    

    try{
    DB::transaction(function () use ($request, $cartId, &$order, &$totalAmount, &$payment, &$lineItems) {
        // Create a new order
        $order = Order::create([
            'id' => Auth::id(), // Assuming this is the user ID
            'OrderStatus' => 'Pending',
            'OrderDate' => Carbon::now(),
        ]);
        

        // Retrieve items from the cart and add them to the order
        $cartItems = CartItem::where('cartId', $cartId)->get();
        if ($cartItems->isEmpty()) {
            Log::error('Cart is empty', ['cartId' => $cartId]);
            throw new \Exception('Cart is empty!');
        }
        

        foreach ($cartItems as $item) {
            // Retrieve menu item details
            $menuItem = Menu::findOrFail($item->menuId); // Ensure you have the correct model for your items
            

            // Check stock availability
        if ($item->quantity > $menuItem->quantityStock) {
            // Stop the transaction and throw an exception
            throw new \Exception("You cannot purchase more than what is in stock for {$menuItem->menuName}. Please adjust your order.");
        }
        // Retrieve the remarks for this specific cart item
        $remarksForItem = $request->input('remarks.' . $item->cartItemId);

            // Create OrderItem
            OrderItem::create([
                'orderId' => $order->orderId,
                'menuId' => $item->menuId,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'totalPrice' => $item->quantity * $item->price,
                'remarks' => $remarksForItem, // Store the remarks here
            ]);
            

            // Deduct stock from the menu item
            $menuItem->quantityStock -= $item->quantity;
            $menuItem->save(); // Save the updated stock

            /// Use the Ngrok URL for the image
            $ngrokUrl = 'https://399c-210-186-147-9.ngrok-free.app'; // Update to the new Ngrok URL

            $imageUrl = !empty($menuItem->menuImage) ? $ngrokUrl . Storage::url($menuItem->menuImage) : null;


            // Add item details to line items for Stripe
            $lineItems[] = [ // Use array push to append items
                'price_data' => [
                    'currency' => 'myr',
                    'product_data' => [
                        'name' => $menuItem->menuName, // Use the actual menu item's name
                        'description' => $menuItem->description, // Use the actual menu item's description
                        'images' => [$imageUrl], // Use the generated image URL
                    ],
                    'unit_amount' => $item->price * 100, // Ensure this is in cents
                ],
                'quantity' => $item->quantity, // Use the actual quantity
            ];
        }

        // Clear the cart after transferring items
        CartItem::where('cartId', $cartId)->delete();
        

        // Calculate total amount
        $totalAmount = OrderItem::where('orderId', $order->orderId)->sum('totalPrice');
        

        // Create payment record with a temporary null for stripe_session_id
        $payment = Payment::create([
            'id' => Auth::id(), // Assuming this is the user ID
            'orderId' => $order->orderId,
            'paymentAmount' => $totalAmount,
            'paymentDate' => Carbon::now(),
            'paymentStatus' => 'Pending',
            'paymentMethod' => null,
            'stripe_session_id' => null, // Temporary null
        ]);
        

         // Increment the payment attempts and update the last_attempt_at field
        $payment->increment('attempts');
        $payment->update(['last_attempt_at' => Carbon::now()]);
        
    });

} catch (\Exception $e) {
    // Redirect back with error message
    return redirect()->back()->with('error', $e->getMessage());
}

    
    
    // Stripe Checkout Session
    try {
        $checkoutSession = StripeSession::create([
            'payment_method_types' => ['card', 'fpx'],
            'line_items' => $lineItems, // Use the detailed line items
            'mode' => 'payment',
            'success_url' => route('payment.success', ['orderId' => $order->orderId]),
            'cancel_url' => route('payment.cancel', ['orderId' => $order->orderId] ),
            'metadata' => [
                'orderId' => $order->orderId,
            ],
        ]);

        // Update the payment record with the Stripe session ID
        $payment->update([
            'stripe_session_id' => $checkoutSession->id,
        ]);
        

        return redirect($checkoutSession->url);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        Log::error('Stripe API error', ['error' => $e->getMessage()]);
        return response()->json(['error' => $e->getMessage()], 500);
    } catch (\Exception $e) {
        Log::error('General error', ['error' => $e->getMessage()]);
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

public function paymentSuccess($orderId)
{
    // Retrieve payment and order details
    $payment = Payment::where('orderId', $orderId)->first();
    $order = Order::findOrFail($orderId);
    $paymentAmount = number_format($payment->paymentAmount, 2); // Format the payment amount

    

    // Check if stripe_session_id exists
    if (!$payment || !$payment->stripe_session_id) {
        Log::error('Stripe session not found', ['orderId' => $orderId]);
        return response()->json(['error' => 'Stripe session not found for this order'], 404);
    }

    // Fetch the Stripe session using the stored session ID
    $checkoutSession = StripeSession::retrieve($payment->stripe_session_id);

    try {
        // Fetch the associated PaymentIntent to determine the payment method
        $paymentIntent = \Stripe\PaymentIntent::retrieve($checkoutSession->payment_intent);
        $paymentMethodUsed = $paymentIntent->payment_method_types[0];

        // Check the PaymentIntent status
        if ($paymentIntent->status === 'succeeded') {
            
            // Check if the payment has already been processed
            if ($payment->paymentStatus === 'Successful') {
                // Payment already processed; return the success view without modifying stock
                return view('payment.success', [
                    'order' => $order,
                    'payment' => $payment,
                    'orderItems' => OrderItem::where('orderId', $orderId)->get(),
                    'orderId' => $orderId,
                ]);
            }

            // Save the Stripe receipt URL and update the payment status
            $payment->update([
                'paymentStatus' => 'Successful',
                'paymentMethod' => ucfirst($paymentMethodUsed),
                'receiptUrl' => $checkoutSession->receipt_url,
            ]);


            // Log the successful payment and order creation
            Log::create([
                'user_id' => Auth::id(), // ID of the user who made the order
                'action' => 'Create Payment',
                'details' => 'Payment of RM ' . $paymentAmount . ' received from ' . Auth::user()->name . ' (customer)',
                'created_at' => now(), // Current timestamp
            ]);

            // Log the successful payment and order creation
            Log::create([
                'user_id' => Auth::id(), // ID of the user who made the order
                'action' => 'Create Order',
                'details' => 'New order #' . $orderId .' placed by ' . Auth::user()->name . ' (customer)',
                'created_at' => now(), // Current timestamp
            ]);

            // Retrieve the order items associated with the order
            $orderItems = OrderItem::where('orderId', $orderId)->get();

            // Reduce the quantity in the menus table for each purchased item
            //foreach ($orderItems as $orderItem) {
            //    $menu = Menu::find($orderItem->menuId);
            //    if ($menu) {
            //        // Reduce the stock quantity
            //        $menu->quantityStock -= $orderItem->quantity; // Assuming you have quantity in OrderItem
            //        $menu->save(); // Save the updated menu item
            //    }
            //}

            // Prepare the alert message
            $alertMessage = "Payment Successful!\n" .
                            "Order ID: {$order->orderId}\n" .
                            "Payment Method: {$payment->paymentMethod}\n" .
                            "Total Amount: MYR " . number_format($payment->paymentAmount, 2);

            // Flash the alert message to the session
            session()->flash('success', $alertMessage);

            // Redirect to the success page with the order details
            return view('payment.success', [
                'order' => $order,
                'payment' => $payment,
                'orderItems' => $orderItems,
                'orderId' => $orderId,
            ]);
        } else {
            

            // Handle accordingly, you could redirect or show a failed message
            session()->flash('alert', 'Payment failed or incomplete. Please try again.');

            return view('payment.cancel');
        }
    } catch (\Stripe\Exception\ApiErrorException $e) {
        Log::error('Stripe API error', ['error' => $e->getMessage()]);
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


public function paymentCancel(Request $request)
{
    

    // Retrieve the order ID from the request
    $orderId = $request->input('orderId');
    
    
    // Fetch the payment associated with the order
    $payment = Payment::where('orderId', $orderId)->first();
    
    if ($payment) {
        // Check attempts and the last attempt timestamp
        if ($payment->attempts >= 3 && $payment->last_attempt_at && Carbon::parse($payment->last_attempt_at)->diffInHours(now()) < 24) {
            // Update both payment and order status to 'Failed'
            $payment->update(['paymentStatus' => 'Failed']);
            $order = Order::find($orderId);
            if ($order) {
                // Revert quantity stocks if payment has failed after 3 attempts
                $this->revertQuantityStocks($orderId);
                $order->update(['OrderStatus' => 'Failed']); // Update order status to Failed
                
            } else {
                Log::error('Order not found for updating status to Failed', ['orderId' => $orderId]);
            }
            // Redirect to homepage if the user has exceeded payment attempts within 24 hours
            return redirect()->route('homepageCustomer')->with('alert', 'You have exceeded the maximum number of payment attempts within 24 hours. Please consider making a new purchase when you are ready. Thank you for your understanding!');
        }
        
        // Update the payment status to 'Pending'
        $payment->update(['paymentStatus' => 'Pending']);
        

        // Check if a Stripe session ID is provided in the request
        if ($request->has('session_id')) {
            $payment->update(['stripe_session_id' => $request->input('session_id')]);
            
        }
    } else {
        Log::error('Payment not found for cancellation', ['orderId' => $orderId]);
        return redirect()->back()->with('alert', 'Payment not found.');
    }

    // Fetch the order by ID
    $order = Order::find($orderId);
    
    if ($order) {
        // Update the order status to 'Pending'
        $order->update(['OrderStatus' => 'Pending']);
        
    } else {
        Log::error('Order not found for cancellation', ['orderId' => $orderId]);
        return redirect()->back()->with('alert', 'Order not found.');
    }

    // Flash a session message
    session()->flash('alert', 'Payment was cancelled. Please try again.');

    // Pass both the order and payment data to the view
    return view('payment.cancel', [
        'order' => $order,
        'payment' => $payment, // Include payment data
        'orderId' => $orderId, // Pass orderId to the view
    ]);
}

protected function revertQuantityStocks($orderId)
{
    // Fetch the order items associated with the order
    $orderItems = OrderItem::where('orderId', $orderId)->get();
    
    foreach ($orderItems as $item) {
        // Assuming you have a stock column in your menu items
        $menuItem = Menu::find($item->menuId);
        if ($menuItem) {
            $menuItem->increment('quantityStock', $item->quantity); // Revert the stock quantity
            
        } else {
            Log::error('Menu item not found for stock revert', ['menuId' => $item->menuId]);
        }
    }
}


public function payAgain($orderId)
{
    // Find the existing payment record by order ID
    $payment = Payment::where('orderId', $orderId)->first();

    // Check if the payment exists
    if (!$payment) {
        return redirect()->back()->with('alert', 'Payment record not found.');
    }

    // Check if the payment is already completed
    if ($payment->paymentStatus === 'Completed') {
        return redirect()->route('order.show', ['orderId' => $orderId])->with('alert', 'Payment has already been completed.');
    }

    // Check if this is the first attempt and set last_attempt_at if not already set
    if (!$payment->last_attempt_at) {
        $payment->last_attempt_at = now(); // Set the first attempt timestamp
        $payment->save(); // Save changes to update last_attempt_at
    }

    //Check if more than 24 hours have passed since the last attempt
    if (Carbon::parse($payment->last_attempt_at)->diffInHours(now()) >= 24) {
       // Update both payment and order status to 'Failed'
       $payment->update(['paymentStatus' => 'Failed']);
       $order = Order::find($orderId);
       if ($order) {
           // Revert quantity stocks if payment has failed after 3 attempts
           $this->revertQuantityStocks($orderId);
           $order->update(['OrderStatus' => 'Failed']); // Update order status to Failed
           
       }
       return redirect()->route('homepageCustomer')->with('alert', 'You have exceeded the maximum number of payment attempts within 24 hours. Please consider making a new purchase when you are ready.');
    }
    

    // Check if more than 24 hours have passed since the last attempt
   //if ($payment->attempts >= 3 && $payment->last_attempt_at && Carbon::parse($payment->last_attempt_at)->diffInHours(now()) < 24) {
   //    // Update both payment and order status to 'Failed'
   //    $payment->update(['paymentStatus' => 'Failed']);
   //    $order = Order::find($orderId);
   //    if ($order) {
   //        // Revert quantity stocks if payment has failed after 3 attempts
   //        $this->revertQuantityStocks($orderId);
   //        $order->update(['OrderStatus' => 'Failed']); // Update order status to Failed
  
   //    }
   //    return redirect()->back()->with('alert', 'You have exceeded the maximum number of payment attempts within 24 hours. Please consider making a new purchase when you are ready.');
   //}

    // Check if the user has reached the maximum attempts
    if ($payment->attempts >= 3) {
        return redirect()->back()->with('alert', 'You have exceeded the maximum number of payment attempts. Please try again later.');
    }


    // If payment attempts are less than 3, proceed
    $payment->increment('attempts'); // Increment the attempts count
    $payment->save(); // Save the changes

    $lineItems = []; // Define lineItems here
    $totalAmount = $payment->paymentAmount; // Use the existing payment amount

    // Log for debugging
    
    /// Use the Ngrok URL for the image
    $ngrokUrl = 'https://399c-210-186-147-9.ngrok-free.app'; // Update to the new Ngrok URL

    
    // Retrieve order items based on the existing payment
    $orderItems = OrderItem::where('orderId', $orderId)->get();
    foreach ($orderItems as $item) {

        $imageUrl = !empty($item->menu->menuImage) ? $ngrokUrl . Storage::url($item->menu->menuImage) : null;

        $lineItems[] = [
            'price_data' => [
                'currency' => 'myr',
                'product_data' => [
                    'name' => $item->menu->menuName,
                    'description' => $item->menu->description,
                    'images' => [$imageUrl], // Use the generated image URL
                ],
                'unit_amount' => $item->price * 100, // Ensure this is in cents
            ],
            'quantity' => $item->quantity,
        ];
    }

    // Stripe Checkout Session
    try {
        $checkoutSession = StripeSession::create([
            'payment_method_types' => ['card', 'fpx'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('payment.success', ['orderId' => $orderId]),
            'cancel_url' => route('payment.cancel', ['orderId' => $orderId]),
            'metadata' => [
                'orderId' => $orderId,
            ],
        ]);

        // Update the existing payment record with the new Stripe session ID and status
        $payment->update([
            'stripe_session_id' => $checkoutSession->id,
            'paymentStatus' => 'Pending', // Update to Pending since it's a retry
        ]);

        

        return redirect($checkoutSession->url);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        Log::error('Stripe API error', ['error' => $e->getMessage()]);
        return response()->json(['error' => $e->getMessage()], 500);
    } catch (\Exception $e) {
        Log::error('General error', ['error' => $e->getMessage()]);
        return response()->json(['error' => $e->getMessage()], 500);
    }
}




public function handleWebhook(Request $request)
{
    $payload = $request->getContent();
    $sig_header = $request->header('Stripe-Signature');
    $endpoint_secret = config('stripe.webhook_secret');

    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret
        );

        // Handle the event based on its type
        if ($event->type === 'payment_intent.payment_failed') {
            $paymentIntent = $event->data->object; // Contains the failed payment intent data
            
            // Retrieve order using the payment intent ID and update order status
            $order = Order::where('payment_intent_id', $paymentIntent->id)->first();
            if ($order) {
                $order->update(['OrderStatus' => 'failed']);
                

                // Optionally, notify the user
                session()->flash('alert', 'Payment failed. Please try again.');
            }
        }

        return response('Webhook handled', 200);
    } catch (\UnexpectedValueException $e) {
        // Invalid payload
        return response('Invalid payload', 400);
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        // Invalid signature
        return response('Invalid signature', 400);
    }
}


}
