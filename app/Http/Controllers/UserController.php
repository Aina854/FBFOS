<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Menu;
use App\Models\Cart;
use App\Models\Payment;
use App\Models\Log;
use App\Models\Order; // Make sure to include your Order model
use App\Models\OrderItem; // Make sure to include your Order model
use Illuminate\Support\Facades\Log as LogFacade; // Alias the Log facade
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    /**
     * Display the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('register');
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

    // Check if the email exists in the database
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return redirect()->route('login')->withErrors(['email' => 'Your email is not registered. Please register first.']);
    }

    // Attempt to authenticate the user
    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        Session::put('role', strtolower($user->category));

        

        // Only create cart if user is a customer and no existing cart is found
        if (strtolower($user->category) === 'customer') {
            $existingCart = Cart::where('user_id', $user->id)->first();
            if (!$existingCart) {
                
                app(CartController::class)->createCartForUser($user->id);
            } else {
                
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
        return redirect()->route('login')->withErrors(['password' => 'Invalid credentials.']);
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
        'email' => 'required|email',
        'name' => 'required|string|unique:users,name',
        'password' => 'required|string',
        'category' => 'required|string'
    ]);

    // Check if the email already exists in the database
    if (User::where('email', $request->email)->exists()) {
        return redirect()->back()->withErrors(['email' => 'Your email already has an account. Please log in.']);
    }

    // Create a new user
    $user = new User([
        'email' => $request->email,
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

    // Log the registration activity for the user
    Log::create([
        'user_id' => $user->id,
        'action' => 'User Registered',
        'details' => 'Customer registered with email: ' . $user->email,
    ]);

    // Log the cart ID after creation (optional)
    $cartId = Session::get('cartId');
    

    return redirect()->route('login')->with('success', 'Successfully registered!');
}

public function changePassword(Request $request, $id)
    {
        

        $user = User::findOrFail($id);

        // Validate passwords
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();
        // Log success message
        

        // Set a success message
        session()->flash('success', 'Password updated successfully.');

        return redirect()->route('editProfile', $id);
    }

    public function changePasswordStaff(Request $request, $id)
    {
        
        $user = User::findOrFail($id);

        // Validate passwords
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();
        

        // Set a success message
        session()->flash('success', 'Password updated successfully.');

        return redirect()->route('editStaff', $id);
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

        

        return redirect()->route('editProfile', $user->id)->with('success', 'Profile updated successfully!');
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
    
        // Fetch items based on stock status
        $lowStockItems = Menu::where('quantityStock', '<', 10)
            ->where('quantityStock', '>', 0)
            ->get();
        $outOfStockItems = Menu::where('quantityStock', 0)->get();
        $restockNeededItems = Menu::whereBetween('quantityStock', [10, 30])->get();
    
        // Fetch most poorly rated food items with their bad feedbacks
        $badRatedItems = Menu::select('menus.menuId', 'menus.menuName', 'menus.menuImage')
            ->join('orderitems', 'menus.menuId', '=', 'orderitems.menuId')
            ->join('feedbacks', 'orderitems.orderitemId', '=', 'feedbacks.orderitemId')
            ->selectRaw('menus.menuId, menus.menuName, menus.menuImage, AVG(feedbacks.rating) as avg_rating')
            ->groupBy('menus.menuId', 'menus.menuName', 'menus.menuImage')
            ->havingRaw('avg_rating BETWEEN 1 AND 2')
            ->orderBy('avg_rating', 'asc')
            ->with(['badFeedbacks']) // Load bad feedbacks
            ->get();

            // Fetch frequently ordered items
            $frequentlyOrderedItems = Menu::select('menus.menuId', 'menus.menuName', 'menus.menuImage')
            ->join('orderitems', 'menus.menuId', '=', 'orderitems.menuId')
            ->selectRaw('menus.menuId, menus.menuName, menus.menuImage, menus.price, COUNT(orderitems.orderitemId) as order_count')
            ->groupBy('menus.menuId', 'menus.menuName', 'menus.menuImage', 'menus.price')
            ->orderBy('order_count', 'desc')
            ->take(3) // Get top 3 items
            ->get();
    
        // Return the staff dashboard view with stock alert and bad rated items data
        return view('staff/staff-home', [
            'lowStockItems' => $lowStockItems,
            'outOfStockItems' => $outOfStockItems,
            'restockNeededItems' => $restockNeededItems,
            'badRatedItems' => $badRatedItems,
            'frequentlyOrderedItems' => $frequentlyOrderedItems,
        ]);
    }
    
    public function showAdminDashboard()
{
    // Check if the user is logged in and has the admin role
    if (Session::get('role') !== 'admin') {
        return redirect()->route('login')->withErrors(['You do not have access to this page']);
    }

    // Get total payment amount
    $totalPayments = Payment::sum('paymentAmount');

    // Get total number of orders
    $totalOrders = Order::count();

    // Get total number of customers
    $totalCustomers = User::where('category', 'Customer')->count(); 

    // Get total number of staff
    $totalStaff = User::where('category', 'Staff')->count();

    // Retrieve the latest 7 log entries
    $recentActivities = Log::orderBy('created_at', 'desc')->take(7)->get();

    // Get the top three selling food items
    $topSellingFoods = DB::table('orderitems')
        ->select('menuId', DB::raw('COUNT(*) as total_sales'))
        ->groupBy('menuId')
        ->orderBy('total_sales', 'desc')
        ->take(5)
        ->get();

    // Get details for each top-selling food item including menuImage
    $topFoodsDetails = [];
    foreach ($topSellingFoods as $food) {
        $menuItem = Menu::find($food->menuId);
        if ($menuItem) {
            $topFoodsDetails[] = [
                'name' => $menuItem->menuName,
                'description' => $menuItem->description ?? 'Popular choice',
                'price' => $menuItem->price,
                'menuImage' => $menuItem->menuImage, // Include the image
                'total_sales' => $food->total_sales, // Include the total sales count
            ];
        }
    }


    // Get highest and lowest rated foods
    $highestRatedFood = DB::table('feedbacks')
        ->join('orderitems', 'feedbacks.orderitemId', '=', 'orderitems.orderitemId')
        ->join('menus', 'orderitems.menuId', '=', 'menus.menuId')
        ->select('menus.menuName', 'menus.menuImage', DB::raw('AVG(feedbacks.rating) as average_rating'), DB::raw('COUNT(feedbacks.feedbackId) as total_ratings'))
        ->groupBy('menus.menuId','menus.menuName', 'menus.menuImage')
        ->orderByDesc('average_rating')
        ->first();

    $lowestRatedFood = DB::table('feedbacks')
        ->join('orderitems', 'feedbacks.orderitemId', '=', 'orderitems.orderitemId')
        ->join('menus', 'orderitems.menuId', '=', 'menus.menuId')
        ->select('menus.menuName', 'menus.menuImage', DB::raw('AVG(feedbacks.rating) as average_rating'), DB::raw('COUNT(feedbacks.feedbackId) as total_ratings'))
        ->groupBy('menus.menuId', 'menus.menuName', 'menus.menuImage')
        ->orderBy('average_rating')
        ->first();


    // Return the admin dashboard view with the necessary data
    return view('admin.admin-home', compact('totalPayments', 'totalOrders', 'totalCustomers', 'totalStaff', 'recentActivities', 'topFoodsDetails', 'highestRatedFood', 'lowestRatedFood'));
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
        //Log::info('Feedbacks for Menu ID ' . $menu->menuId . ':', ['feedbacks' => $feedbacks]);

        // Calculate the average rating and reviews count
        $averageRating = $feedbacks->avg('rating'); // Calculate average rating
        $reviewsCount = $feedbacks->count(); // Count reviews

        // Log the calculated average rating and reviews count
        //Log::info('Average Rating and Review Count for Menu ID ' . $menu->menuId . ':', [
        //    'averageRating' => $averageRating,
        //   'reviewsCount' => $reviewsCount,
        //]);

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

public function editStaff()
{
    $user = Auth::user(); // Get the authenticated user
    return view('editStaff', compact('user')); // Pass user data to the view
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

    // Insert a log entry
    Log::create([
        'user_id' => $user->id,
        'action' => 'Profile updated', // Describe the activity
        'details' => $user->name . ' (customer) updated their profile.', // Specific details
    ]);

    // Redirect to the customer homepage with a success message
    return redirect()->route('editProfile', $user->id)->with('success', 'Profile updated successfully!');
}

public function updateProfileStaff(Request $request, $id)
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
    
    // Insert a log entry
    Log::create([
        'user_id' => $user->id,
        'action' => 'Profile updated', // Describe the activity
        'details' => $user->name . ' (staff) updated their profile.', // Specific details
    ]);

    // Redirect to the customer homepage with a success message
    return redirect()->route('editStaff', $user->id)->with('success', 'Profile updated successfully!');
}




public function getStaffList()
{
    // Retrieve all users with the category of "staff"
    $staffMembers = User::where('category', 'staff')->get();

    // Pass the list of staff members to the admin view
    return view('admin.staff-list', ['staffMembers' => $staffMembers]);
}


public function createstaff()
{
    return view('admin.create'); // Make sure this view exists
}

public function storestaff(Request $request)
{
    // Validate form data
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phoneNo' => 'required|string|max:15',
        'password' => 'required|string|min:8|confirmed', // Password confirmation field
    ]);

    // Create the new staff
    $staff = new User();
    $staff->name = $request->name;
    $staff->email = $request->email;
    $staff->phoneNo = $request->phoneNo;
    $staff->password = bcrypt($request->password);
    $staff->category = 'Staff'; // Assuming 'category' is used to assign roles

    $staff->save();

    Log::create([
        'user_id' => Auth::id(), // The ID of the admin performing the action
        'action' => 'Add Staff',
        'details' => Auth::user()->name . ' (admin) added new staff: ' . $staff->name,
        'created_at' => now(), // Current timestamp
    ]);
    
    return redirect()->route('admin.staffList')->with('success', 'New staff member added successfully');
}

public function admineditStaff($id)
{
    $staff = User::findOrFail($id); // Retrieve the staff member by their ID
    return view('admin.editStaff', compact('staff')); // Pass the staff data to the view
}

public function updateStaff(Request $request, $id)
{
    // Validate the input data
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'phoneNo' => 'required|string|max:15',
        // Add validation for other fields if necessary
    ]);

    // Find the staff member by ID
    $staff = User::findOrFail($id);

    // Update the staff member's information
    $staff->name = $request->input('name');
    $staff->email = $request->input('email');
    $staff->phoneNo = $request->input('phoneNo');
    // Add more fields to update if needed

    // Save the updated staff member data
    $staff->save();

    Log::create([
        'user_id' => Auth::id(), // The ID of the admin performing the action
        'action' => 'Edit Staff',
        'details' => Auth::user()->name . ' (admin) edit profile for staff: ' . $staff->name,
        'created_at' => now(), // Current timestamp
    ]);

    // Redirect back to the staff list with a success message
    return redirect()->route('admin.staffList')->with('success', 'Staff member updated successfully.');
}

public function deleteStaff($id)
{
    // Find the staff member by ID
    $staff = User::findOrFail($id);

    // Delete the staff member
    $staff->delete();

    Log::create([
        'user_id' => Auth::id(), // The ID of the admin performing the action
        'action' => 'Delete Staff',
        'details' => Auth::user()->name . ' (admin) deleted staff: ' . $staff->name,
        'created_at' => now(), // Current timestamp
    ]);

    // Redirect back to the staff list with a success message
    return redirect()->route('admin.staffList')->with('success', 'Staff member deleted successfully.');
}


public function customerList()
{
    // Retrieve all users with the category of "staff"
    $customers = User::where('category', 'customer')->get();

    // Pass the list of staff members to the admin view
    return view('admin.customerList', compact('customers'));
    
}

public function deleteCustomer($id)
{
    $customer = User::findOrFail($id);
    $customer->delete();

    Log::create([
        'user_id' => Auth::id(), // The ID of the admin performing the action
        'action' => 'Delete Customer',
        'details' => Auth::user()->name . ' (admin) deleted customer: ' . $customer->name,
        'created_at' => now(), // Current timestamp
    ]);

    // Set a success message in the session
    return redirect()->route('admin.customerList')->with('success', 'Customer deleted successfully.');
}



}
