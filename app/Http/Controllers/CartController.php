<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User; // Import the User model
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session; // Import Session facade

class CartController extends Controller
{
    /**
     * Create a new cart for a user.
     *
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    // Example of CartController method
    public function createCartForUser($userId)
    {
        // Check if a cart already exists for the user
        $existingCart = Cart::where('user_id', $userId)->first();
        
        // Log existing cart check
        Log::info('Checking for existing cart for user ID: ' . $userId, ['existingCart' => $existingCart]);
    
        if ($existingCart) {
            // Log that a cart already exists
            Log::info('Cart already exists for user ID: ' . $userId);
            return response()->json(['message' => 'Cart already exists.'], 400); // Early return
        }
    
        // Create a new cart for the user
        $cart = new Cart();
        $cart->user_id = $userId;
    
        // Attempt to save the cart
        if (!$cart->save()) {
            // Log the errors if saving fails
            Log::error('Failed to create cart for user: ' . json_encode($cart->getErrors()));
            return response()->json(['message' => 'Failed to create cart.'], 500);
        }
    
        // Log the cartId for debugging after saving
        Log::info('Cart created with ID: ' . $cart->cartId);
    
        // Store cartId in session
        Session::put('cartId', $cart->cartId);
        
        // Log the cartId set in the session
        Log::info('Cart ID set in session: ', ['cartId' => $cart->cartId]);
    
        // Return success message with cartId
        return response()->json(['message' => 'Cart created successfully.', 'cartId' => $cart->cartId]);
    }
    

    /**
     * Add an item to the cart.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    // CartController.php
    public function addToCart(Request $request)
{
    // Log the entire session data for debugging
    Log::info('Full Session Data Before Retrieval: ', session()->all());

    // Retrieve cartId from the session
    $cartId = Session::get('cartId');
    Log::info('Cart ID from session: ', ['cartId' => $cartId]);

    // Check if cartId exists in the session
    if (!$cartId) {
        Log::error('Cart ID not found in session. Redirecting to homepage.');
        return redirect()->route('homepageCustomer')->withErrors(['Cart not found']);
    }

    // Validate the incoming request data
    $request->validate([
        'menuId' => 'required|exists:menus,menuId',
        'quantity' => 'required|integer|min:1',
    ]);

    // Find the cart and menu
    $cart = Cart::find($cartId);
    $menu = Menu::find($request->menuId);

    // Check if the cart and menu were found
    if (!$cart) {
        Log::error('Cart not found with ID: ' . $cartId);
        return redirect()->route('homepageCustomer')->withErrors(['Cart not found']);
    }

    if (!$menu) {
        Log::error('Menu item not found with ID: ' . $request->menuId);
        return redirect()->route('homepageCustomer')->withErrors(['Menu item not found']);
    }

    // Check if the cart item already exists
    $cartItem = CartItem::where('cartId', $cartId)
                         ->where('menuId', $request->menuId)
                         ->first();

    if ($cartItem) {
        // Update existing cart item
        $cartItem->quantity += $request->quantity;
        $cartItem->totalPrice = $cartItem->price * $cartItem->quantity;

        if (!$cartItem->save()) {
            Log::error('Failed to update cart item: ' . json_encode($cartItem->getErrors()));
            return redirect()->route('homepageCustomer')->withErrors(['Failed to update item in cart']);
        }
    } else {
        // Create a new cart item
        $price = $menu->price;
        $totalPrice = $price * $request->quantity;

        $cartItem = new CartItem();
        $cartItem->cartId = $cart->cartId;
        $cartItem->menuId = $request->menuId;
        $cartItem->quantity = $request->quantity;
        $cartItem->price = $price;
        $cartItem->totalPrice = $totalPrice;
        $cartItem->createdAt = now();

        if (!$cartItem->save()) {
            Log::error('Failed to add item to cart: ' . json_encode($cartItem->getErrors()));
            return redirect()->route('homepageCustomer')->withErrors(['Failed to add item to cart']);
        }
    }

    return redirect()->route('homepageCustomer')->with('success', 'Item added to cart successfully!');
}



    
public function show($cartId)
{
    // Fetch the cart with its items
    $cart = Cart::with('items')->find($cartId);

    // Check if the cart exists
    if (!$cart) {
        return redirect()->route('homepageCustomer')->withErrors(['Cart not found.']);
    }

    // Pass the cart items to the view
    $cartItems = $cart->items; // Assuming 'items' is the relationship name

    // Fetch menu items
    $menu = Menu::all(); // Adjust this if you need specific menu items

    // Fetch the customer based on the cart's user_id
    $customer = User::find($cart->user_id); // Make sure this is correct

    // Check if the customer exists
    if (!$customer) {
        return redirect()->route('homepageCustomer')->withErrors(['Customer not found.']);
    }
     // Calculate total prices
     $totalPrices = $cartItems->pluck('totalPrice')->toArray();


    // Optionally fetch an order ID if needed
    // If you need to check for an orderId associated with the customer
    $orderId = $customer->orders()->first()->id ?? null; // Adjust based on your Order relationship

    return view('carts.cart', [
        'cart' => $cart,
        'totalPrices' => $totalPrices,
        'cartItems' => $cartItems, // Pass the cart items
        'menu' => $menu, // Pass the menu items
        'customer' => $customer, // Pass the customer information
        'orderId' => $orderId, // Pass the order ID if applicable
    ]);
}

    
    public function deleteItem($cartItemId)
    {
        // Find the cart item by its ID
        $cartItem = CartItem::find($cartItemId);
    
        if (!$cartItem) {
            return redirect()->route('showCart', ['cartId' => $cartItem->cartId])->withErrors(['Cart item not found.']);
        }
    
        // Delete the cart item
        $cartItem->delete();
    
        return redirect()->route('showCart', ['cartId' => $cartItem->cartId])->with('success', 'Item removed from cart.');
    }
        
    public function addSideOrder(Request $request, $cartId)
{
    // Log the incoming request data
    \Log::info('Add Side Order Request Data: ', $request->all());

    // Validate the request
    $validatedData = $request->validate([
        'menuId' => 'required|exists:menus,menuId',
        'quantity' => 'required|integer|min:1',
        'price' => 'required|numeric|min:0',
        'id' => 'required|exists:users,id',
    ]);

    // Check if the cart exists
    $cart = Cart::find($cartId);
    if (!$cart) {
        \Log::error('Cart not found: ' . $cartId);
        return redirect()->back()->withErrors(['Cart not found.']);
    }

    // Find the menu item using menuId as primary key
    $menu = Menu::find($validatedData['menuId']);
    if (!$menu) {
        \Log::error('Menu not found: ' . $validatedData['menuId']);
        return redirect()->back()->withErrors(['Menu item not found.']);
    }

    // Log details before adding/updating the cart item
    \Log::info('Adding/Updating Cart Item', [
        'cartId' => $cartId,
        'menuId' => $menu->menuId,
        'quantity' => $validatedData['quantity'],
        'price' => $menu->price,
    ]);

    // Check if the menu item already exists in the cart
    $cartItem = CartItem::where('cartId', $cartId)
        ->where('menuId', $validatedData['menuId'])
        ->first();

    try {
        if ($cartItem) {
            // Update existing cart item
            $cartItem->quantity += $validatedData['quantity'];
            $cartItem->totalPrice = $cartItem->quantity * $cartItem->price;

            if ($cartItem->save()) {
                \Log::info('Cart Item updated successfully', ['cartItemId' => $cartItem->cartItemId]);
            } else {
                \Log::error('Failed to update Cart Item', ['cartItem' => $cartItem]);
            }
        } else {
            // Create a new cart item
            $cartItem = new CartItem();
            $cartItem->cartId = $cartId;
            $cartItem->menuId = $menu->menuId;
            $cartItem->quantity = $validatedData['quantity'];
            $cartItem->price = $menu->price;
            $cartItem->totalPrice = $menu->price * $validatedData['quantity'];

            // Set createdAt to the current timestamp
            $cartItem->createdAt = now(); // Use Laravel's now() function

            if ($cartItem->save()) {
                \Log::info('Cart Item saved successfully', ['cartItemId' => $cartItem->cartItemId]);
            } else {
                \Log::error('Failed to save Cart Item', ['cartItem' => $cartItem]);
            }
        }
    } catch (\Exception $e) {
        \Log::error('Error occurred while adding side order', [
            'message' => $e->getMessage(),
            'cartId' => $cartId,
            'menuId' => $validatedData['menuId'],
            'quantity' => $validatedData['quantity'],
        ]);
        return redirect()->back()->withErrors(['Error occurred while adding side order.']);
    }

    return redirect()->back()->with('success', 'Side menu item added to cart successfully!');
}



public function showOrderSummary(Request $request, $cartId)
{
    // Log the incoming request data
    \Log::info('Show Order Summary Request Data: ', $request->all());

    // Find the cart using the cartId
    $cart = Cart::find($cartId);
    if (!$cart) {
        \Log::error('Cart not found: ' . $cartId);
        return redirect()->route('homepageCustomer')->withErrors(['Cart not found.']);
    }

    // Fetch cart items
    $cartItems = CartItem::where('cartId', $cartId)->get();

    // Calculate the total price
    $total = $cartItems->sum(function ($cartItem) {
        return $cartItem->price * $cartItem->quantity;
    });

    // Get remarks from the request
    $remarks = $request->query('remarks', 'No remarks provided.');

    // You can initialize orderId as null if it's not retrieved
    $orderId = null; // or retrieve it from your logic if necessary

    return view('orders.summary', compact('cart', 'cartItems', 'cartId', 'remarks', 'total', 'orderId'));
}



}