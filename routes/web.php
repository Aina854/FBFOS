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

// User Management (Admin) Routes
Route::middleware('auth')->group(function () {
    Route::prefix('users')->group(function () {
        Route::post('/', [UserController::class, 'insertUser'])->name('user.insert');
        Route::post('/{id}', [UserController::class, 'updateUser'])->name('user.update');
        Route::delete('/{id}', [UserController::class, 'deleteUser'])->name('user.delete');
        Route::get('/profile/edit/{id}', [UserController::class, 'editProfile'])->name('editProfile');
    });
});

// Dashboard Routes
Route::middleware('auth')->group(function () {
    Route::get('/homepage-staff', [UserController::class, 'showStaffDashboard'])->name('homepageStaff');
    Route::get('/homepage-customer', [UserController::class, 'showCustomerDashboard'])->name('homepageCustomer');
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