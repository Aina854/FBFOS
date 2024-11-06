<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FeedbackController;

// Home Route
Route::get('/', function () {
    return view('welcome');
})->name('home');

//Route::view('/(path)','welcome');

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('login.submit');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [UserController::class, 'insertUser'])->name('register.submit');
});
Route::post('/profile/{id}/change-password', [UserController::class, 'changePassword'])->name('profile.changePassword');
Route::post('/profile/{id}/change-password-staff', [UserController::class, 'changePasswordStaff'])->name('profile.changePasswordStaff');

// User Management (Admin) Routes
Route::middleware('auth')->group(function () {
    Route::prefix('users')->group(function () {
        Route::post('/', [UserController::class, 'insertUser'])->name('user.insert');
        Route::post('/{id}', [UserController::class, 'updateUser'])->name('user.update');
        Route::delete('/{id}', [UserController::class, 'deleteUser'])->name('user.delete');
        Route::get('/profile/edit/{id}', [UserController::class, 'editProfile'])->name('editProfile');
        Route::get('/staff/edit/{id}', [UserController::class, 'editStaff'])->name('editStaff');
        Route::post('/profile/update/{id}', [UserController::class, 'updateProfile'])->name('profile.update');
        Route::post('/staff/update/{id}', [UserController::class, 'updateProfileStaff'])->name('profile.update.staff');

    });
});

// Dashboard Routes
Route::middleware('auth')->group(function () {
    Route::get('/homepage-staff', [UserController::class, 'showStaffDashboard'])->name('homepageStaff');
    Route::get('/homepage-customer', [UserController::class, 'showCustomerDashboard'])->name('homepageCustomer');
    Route::get('/homepage-admin', [UserController::class, 'showAdminDashboard'])->name('homepageAdmin');
});


//staff
// Menu Management Routes
Route::middleware('auth')->group(function () {
    Route::get('/list-menu', [MenuController::class, 'index'])->name('listMenu');
    Route::resource('menus', MenuController::class);
    Route::get('/staff/orders/incoming', [OrderController::class, 'incomingOrders'])->name('staff.orders.incoming');
    Route::get('/staff/orders/details', [OrderController::class, 'details'])->name('staff.orders.details');
    Route::post('/staff/orders/updateStatus/{orderId}', [OrderController::class, 'updateStatus'])->name('staff.orders.updateStatus');

    Route::get('/staff/orders/rejected', [OrderController::class, 'rejectedOrders'])->name('staff.orders.rejected');
    Route::get('/staff/orders/completed', [OrderController::class, 'completedOrders'])->name('staff.orders.completed');
    Route::post('/staff/orders/accept/{orderId}', [OrderController::class, 'accept'])->name('staff.orders.accept');
    Route::post('/staff/orders/reject/{orderId}', [OrderController::class, 'reject'])->name('staff.orders.reject');
});

// Cart Routes
Route::middleware(['web', 'auth'])->prefix('cart')->group(function () {
    Route::post('/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::get('/{cartId}', [CartController::class, 'show'])->name('showCart'); // Use {cartId}
    Route::delete('/delete-item/{cartItemId}', [CartController::class, 'deleteItem'])->name('cart.deleteItem'); 
    // Route for adding side order
    Route::post('/{cartId}/add-side-order', [CartController::class, 'addSideOrder'])->name('cart.addSideOrder'); // Use {cartId}
    // Route for showing order summary
    Route::get('/{cartId}/order-summary', [CartController::class, 'showOrderSummary'])->name('cart.showOrderSummary'); // Use {cartId}
});



// Order Management Routes
Route::middleware('auth')->prefix('orders')->group(function () {
    Route::get('/view-orders', [OrderController::class, 'viewOrders'])->name('viewOrders');
    //Route::get('/{id}', [OrderController::class, 'select'])->name('selectOrder');
});
Route::get('/order/{orderId}', [OrderController::class, 'show'])->name('order.show');

Route::get('/orders/{orderId}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');



// Payment Routes
Route::middleware('auth')->prefix('payments')->group(function () {
    Route::get('/view-payments', [PaymentController::class, 'viewPayments'])->name('viewPayments');
});
Route::post('/payment/proceed/{cartId}', [PaymentController::class, 'proceedToPayment'])
     ->name('cart.proceedToPayment');
     Route::get('/payment/success/{orderId}', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
     Route::get('/payment/cancel', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');  
     Route::get('payment/pay-again/{orderId}', [PaymentController::class, 'payAgain'])->name('payment.again');


// Feedback Routes
Route::middleware('auth')->prefix('feedback')->group(function () {
    Route::get('/view-feedback', [FeedbackController::class, 'viewFeedback'])->name('viewFeedback');
});
// If you need resource routes and the custom create route:

Route::get('/feedback/create/{orderId?}', [FeedbackController::class, 'create'])->name('feedback.create');
Route::get('/feedback/{orderId}', [FeedbackController::class, 'index'])->name('feedback.index');

Route::post('/feedback/store', [FeedbackController::class, 'store'])->name('feedback.store');

Route::get('/feedback/show', [FeedbackController::class, 'show'])->name('feedback.show');


Route::get('/staff/feedback', [FeedbackController::class, 'feedbackForStaff'])->name('feedback.staff');

Route::get('/staff/feedback/{id}/response', [FeedbackController::class, 'showResponseForm'])->name('feedback.response');


Route::post('/feedback/submit/{orderItemId}', [FeedbackController::class, 'submitResponse'])->name('feedback.submit');

Route::get('/staff/feedback/past', [FeedbackController::class, 'feedbackPast'])->name('feedback.past');



// Admin
// Route to display the list of staff for the admin
Route::get('/admin/staff-list', [UserController::class, 'getStaffList'])->name('admin.staffList')->middleware('auth');

Route::get('/staff/add', [UserController::class, 'createstaff'])->name('addStaff');
Route::post('/staff/store', [UserController::class, 'storestaff'])->name('storeStaff');
Route::get('/admin/staff/{id}', [UserController::class, 'admineditStaff'])->name('admin.editStaff');
Route::put('/admin/staff/{id}', [UserController::class, 'updateStaff'])->name('updateStaff');
Route::delete('/admin/staff/{id}', [UserController::class, 'deleteStaff'])->name('deleteStaff');

// Route to display the list of customers
Route::get('/admin/customers', [UserController::class, 'customerList'])->name('admin.customerList');

// Route to delete a specific customer
Route::delete('/admin/customers/{id}', [UserController::class, 'deleteCustomer'])->name('deleteCustomer');


// In routes/web.php


Route::get('/admin/daily-sales', [OrderController::class, 'dailySalesData'])->name('dailySalesData');
Route::get('/admin/weekly-sales', [OrderController::class, 'weeklySalesData'])->name('weeklySalesData');
Route::get('/admin/monthly-sales', [OrderController::class, 'monthlySalesData'])->name('monthlySalesData');
Route::get('/admin/yearly-sales', [OrderController::class, 'yearlySalesData'])->name('yearlySalesData');

