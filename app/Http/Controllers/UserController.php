<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Menu;
use App\Models\Cart;
use App\Models\Order; // Make sure to include your Order model
use App\Models\OrderItem; // Make sure to include your Order model
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /**
     * Display the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Handle the login request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */


     public function login(Request $request)
     {
         $request->validate([
             'email' => 'required|email',
             'password' => 'required',
         ]);
     
         if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
             $user = Auth::user();
             Session::put('role', strtolower($user->category));
     
             Log::info('User logged in with ID: ' . $user->id . ' and category: ' . $user->category);
     
             // Only create cart if user is a customer and no existing cart is found
             if (strtolower($user->category) === 'customer') {
                 $existingCart = Cart::where('user_id', $user->id)->first();
                 if (!$existingCart) {
                     Log::info('Creating cart for user ID: ' . $user->id);
                     $response = app(CartController::class)->createCartForUser($user->id);
                     // Optionally handle the response if needed
                 } else {
                     Log::info('Cart already exists for user ID: ' . $user->id, ['cartId' => $existingCart->cartId]);
                     Session::put('cartId', $existingCart->cartId); // Store existing cart ID in session
                 }
             }
     
             switch (strtolower($user->category)) {
                 case 'customer':
                     return redirect()->route('homepageCustomer');
                 case 'staff':
                     return redirect()->route('homepageStaff');
                 case 'admin':
                     return redirect()->route('homepageAdmin');
                 default:
                     return redirect()->route('login')->withErrors(['Invalid category']);
             }
         } else {
             return redirect()->route('login')->withErrors(['Invalid credentials']);
         }
     }
     

    /**
     * Handle the logout request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
{
    Auth::logout(); // Log out the user
    Session::flush(); // Clear all session data
    return redirect()->route('login');
}


    /**
     * Display the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('register');
    }

    /**
     * Handle the user registration request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function insertUser(Request $request)
{
    // Validate registration form data
    $request->validate([
        'firstName' => 'required|string',
        'lastName' => 'required|string',
        'age' => 'required|integer',
        'gender' => 'required|string',
        'email' => 'required|email',
        'phoneNo' => 'required|string',
        'address1' => 'required|string',
        'address2' => 'nullable|string',
        'postcode' => 'required|string',
        'city' => 'required|string',
        'state' => 'required|string',
        'name' => 'required|string|unique:users,name',
        'password' => 'required|string|confirmed',
        'category' => 'required|string'
    ]);

    // Create a new user
    $user = new User([
        'firstName' => $request->firstName,
        'lastName' => $request->lastName,
        'age' => $request->age,
        'gender' => $request->gender,
        'email' => $request->email,
        'phoneNo' => $request->phoneNo,
        'address1' => $request->address1,
        'address2' => $request->address2,
        'postcode' => $request->postcode,
        'city' => $request->city,
        'state' => $request->state,
        'name' => $request->name,
        'password' => Hash::make($request->password),
        'category' => $request->category,
    ]);

    // Save the user to the database
    $user->save();

    // Create cart only for customers
    if (strtolower($user->category) === 'customer') {
        // Call the CartController's method to create a cart
        app(CartController::class)->createCartForUser($user->id);
        
    }
    // Check if cartId is set
    $cartId = Session::get('cartId');
    Log::info('Cart ID after creation: ', ['cartId' => $cartId]);


    return redirect()->route('login')->with('success', 'Successfully registered!');
}

    
    
    /**
     * Handle the user update request.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUser(Request $request, $id)
    {
        // Validate update form data
        $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'age' => 'required|integer',
            'gender' => 'required|string',
            'email' => 'required|email',
            'phoneNo' => 'required|string',
            'address1' => 'required|string',
            'address2' => 'nullable|string',
            'postcode' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'name' => 'required|string|unique:users,name,' . $id,
            'password' => 'nullable|string|confirmed',
            'category' => 'required|string'
        ]);

        // Find user by ID
        $user = User::findOrFail($id);

        // Update user details
        $user->update([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'age' => $request->age,
            'gender' => $request->gender,
            'email' => $request->email,
            'phoneNo' => $request->phoneNo,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'postcode' => $request->postcode,
            'city' => $request->city,
            'state' => $request->state,
            'name' => $request->name,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'category' => $request->category,
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Handle the user deletion request.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUser($id)
    {
        // Find user by ID
        $user = User::findOrFail($id);

        // Optionally delete the user's cart
        // $user->cart()->delete();

        // Delete the user
        $user->delete();

        return redirect()->route('listUsers')->with('success', 'User deleted successfully!');
    }

    /**
     * Handle staff login request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function staffLogin(Request $request)
    {
        // Validate staff login form data
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string'
        ]);

        // Find staff user by name
        $user = User::where('name', $request->name)->where('category', 'Staff')->first();

        // Check if staff user exists and password matches
        if ($user && Hash::check($request->password, $user->password)) {
            // Login successful
            Session::put('user_id', $user->id);
            Session::put('role', 'staff');
            return redirect()->route('homepageStaff');
        } else {
            return redirect()->route('staff.login')->withErrors(['Invalid username or password']);
        }
    }


public function showStaffDashboard()
{
    // Check if the user is logged in and has the staff role
    if (Session::get('role') !== 'staff') {
        return redirect()->route('login')->withErrors(['You do not have access to this page']);
    }

    // Return the staff dashboard view
    return view('staff/staff-home');
}

public function showCustomerDashboard()
{
    // Check if the user is logged in and has the customer role
    if (Session::get('role') !== 'customer') {
        return redirect()->route('login')->withErrors(['You do not have access to this page']);
    }

    // Fetch the menus with orderItems, orders, and feedbacks
    $menus = Menu::with(['orderItems.order', 'orderItems.feedback'])->get();

    foreach ($menus as $menu) {
        // Extract feedback for this menu from its orderItems
        $feedbacks = $menu->orderItems->flatMap(function ($orderItem) {
            // Ensure the feedback is fetched only for valid orders (linked through order)
            return $orderItem->order ? $orderItem->feedback : collect();
        });

        // Log the feedbacks for debugging
        Log::info('Feedbacks for Menu ID ' . $menu->menuId . ':', ['feedbacks' => $feedbacks]);

        // Calculate the average rating and reviews count
        $averageRating = $feedbacks->avg('rating'); // Calculate average rating
        $reviewsCount = $feedbacks->count(); // Count reviews

        // Log the calculated average rating and reviews count
        Log::info('Average Rating and Review Count for Menu ID ' . $menu->menuId . ':', [
            'averageRating' => $averageRating,
            'reviewsCount' => $reviewsCount,
        ]);

        // Assign the calculated values to the menu item
        $menu->averageRating = $averageRating ? round($averageRating, 1) : 'No rating';
        $menu->reviewsCount = $reviewsCount;
    }

    // Fetch the cart for the logged-in user
    $user = Auth::user();
    $cart = $user->cart;

    // Fetch the latest order ID or set to null if no orders exist
    $order = $user->orders()->latest()->first();
    $orderId = $order ? $order->id : null;

    // Pass menus, cart, and orderId to the view
    return view('customer.customer-home', compact('menus','cart', 'orderId'));
}

/**
 * Show the form for editing the authenticated user's profile.
 *
 * @return \Illuminate\View\View
 */
public function editProfile()
{
    $user = Auth::user(); // Get the authenticated user
    return view('editProfile', compact('user')); // Pass user data to the view
}

/**
 * Handle the profile update request.
 *
 * @param \Illuminate\Http\Request $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function updateProfile(Request $request, $id)
{
    // Fetch the user by ID
    $user = User::findOrFail($id); // Ensure the user exists

    // Validate update form data
    $request->validate([
        'firstName' => 'required|string',
        'lastName' => 'required|string',
        'age' => 'required|integer',
        'gender' => 'required|string',
        'email' => 'required|email',
        'phoneNo' => 'required|string',
        'address1' => 'required|string',
        'address2' => 'nullable|string',
        'postcode' => 'required|string',
        'city' => 'required|string',
        'state' => 'required|string',
        'name' => 'required|string|unique:users,name,' . $user->id,
        'password' => 'nullable|string|confirmed',
    ]);

    // Update user details
    $user->update([
        'firstName' => $request->firstName,
        'lastName' => $request->lastName,
        'age' => $request->age,
        'gender' => $request->gender,
        'email' => $request->email,
        'phoneNo' => $request->phoneNo,
        'address1' => $request->address1,
        'address2' => $request->address2,
        'postcode' => $request->postcode,
        'city' => $request->city,
        'state' => $request->state,
        'name' => $request->name,
        'password' => $request->password ? Hash::make($request->password) : $user->password,
    ]);

    // Redirect to the customer homepage with a success message
    return redirect()->route('homepageCustomer')->with('success', 'Profile updated successfully!');
}


}
