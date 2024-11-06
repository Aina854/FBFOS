@extends('layouts.customer')

@section('content')
<div class="container">
    <h1>Your Orders</h1>

    <!-- Tabs Navigation -->
<ul class="nav nav-tabs" id="orderTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $tab == 'current' ? 'active' : '' }}" id="current-orders-tab" data-bs-toggle="tab" href="#currentOrders" role="tab" aria-controls="currentOrders" aria-selected="{{ $tab == 'current' ? 'true' : 'false' }}">Processing Orders</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $tab == 'pending' ? 'active' : '' }}" id="pending-orders-tab" data-bs-toggle="tab" href="#pendingOrders" role="tab" aria-controls="pendingOrders" aria-selected="{{ $tab == 'pending' ? 'true' : 'false' }}">Awaiting Payment</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $tab == 'history' ? 'active' : '' }}" id="past-orders-tab" data-bs-toggle="tab" href="#pastOrders" role="tab" aria-controls="pastOrders" aria-selected="{{ $tab == 'history' ? 'true' : 'false' }}">Order History</a>
    </li>
</ul>


    <!-- Tab Content -->
<div class="tab-content mt-3" id="orderTabsContent">

<!-- Current Orders Tab -->
<div class="tab-pane fade {{ $tab == 'current' ? 'show active' : '' }}" id="currentOrders" role="tabpanel" aria-labelledby="current-orders-tab">
    @livewire('current-orders', ['data' => $currentOrders])
</div>

<!-- Pending Orders Tab -->
<div class="tab-pane fade {{ $tab == 'pending' ? 'show active' : '' }}" id="pendingOrders" role="tabpanel" aria-labelledby="pending-orders-tab">
    @if($pendingOrders->isEmpty())
        <p>No awaiting payment orders found.</p>
    @else
        @foreach($pendingOrders as $order)
            <div class="card mb-4 pending-order-card" style="display: flex;">
                <div class="card-body" style="flex: 1;">
                    <div class="card-header">
                        <h5 class="mb-0">Order ID: #{{ $order->orderId }}</h5>
                        <span class="badge bg-warning">{{ $order->OrderStatus }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="flex: 1;">
                            <h6>Total Price: RM{{ number_format($order->payment->paymentAmount, 2) }}</h6>
                            <h6>Payment Status: 
                                <span class="badge 
                                    @if($order->payment->paymentStatus == 'Pending')
                                        bg-warning
                                    @elseif($order->payment->paymentStatus == 'Successful')
                                        bg-success
                                    @elseif($order->payment->paymentStatus == 'Failed')
                                        bg-danger
                                    @else
                                        bg-secondary
                                    @endif
                                ">{{ $order->payment->paymentStatus }}</span>
                            </h6>
                            <h6>Payment Method: {{ $order->payment->paymentMethod }}</h6>
                            <h6>Order Date: {{ $order->created_at->format('d/m/Y') }}</h6>

                            <button class="btn btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#orderItems{{ $order->orderId }}" aria-expanded="false" aria-controls="orderItems{{ $order->orderId }}">
                                View Order Items
                            </button>

                            <div class="collapse mt-3" id="orderItems{{ $order->orderId }}">
                                <h6>Order Items:</h6>
                                <ul>
                                    @foreach($order->orderItems as $item)
                                        <li>{{ $item->menu->menuName }} x{{ $item->quantity }} - RM{{ number_format($item->price, 2) }} for each ({{ $item->remarks }})</li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Pay Again Button -->
                            <a href="{{ route('payment.again', ['orderId' => $order->orderId ?? null]) }}" class="btn btn-success">Pay Now</a>
                        </div>

                        <div style="flex: 0 0 100px; display: flex; justify-content: center; align-items: center;">
                            @if($order->orderItems->isNotEmpty())
                                @php
                                    $menu = $order->orderItems[0]->menu; // Get the first menu item
                                    $imageUrl = $menu->menuImage ? asset('storage/' . $menu->menuImage) : asset('storage/default-image.jpg'); // Fallback to default image
                                @endphp
                                <img src="{{ $imageUrl }}" alt="{{ $menu->menuName }}" style="width: 200px; height: 130px; object-fit: cover; border-radius: 8px;"/>
                            @else
                                <img src="{{ asset('storage/default-image.jpg') }}" alt="Default Image" style="width: 200px; height: 130px; object-fit: cover; border-radius: 8px;"/>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<!-- Past Orders Tab -->
<div class="tab-pane fade {{ $tab == 'history' ? 'show active' : '' }}" id="pastOrders" role="tabpanel" aria-labelledby="past-orders-tab">
    @if($pastOrders->isEmpty())
        <p>No order history found.</p>
    @else
        <div id="pastOrdersContainer">
            @foreach($pastOrders as $order)
                <div class="card mb-4 past-order-card" style="display: flex;">
                    <div class="card-body" style="flex: 1;">
                        <div class="card-header">
                            <h5 class="mb-0">Order ID: #{{ $order->orderId }}</h5>
                            <span class="badge 
                                @if($order->OrderStatus == 'Pending')
                                    bg-warning
                                @elseif($order->OrderStatus == 'Preparing')
                                    bg-info
                                @elseif($order->OrderStatus == 'Ready for Pickup')
                                    bg-primary
                                @elseif($order->OrderStatus == 'Completed')
                                    bg-success
                                @elseif($order->OrderStatus == 'Failed')
                                    bg-danger
                                @else
                                    bg-secondary
                                @endif
                            ">{{ $order->OrderStatus }}</span>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="flex: 1;">
                                <h6 style="margin: 1;">Total Price: RM{{ number_format($order->payment->paymentAmount, 2) }}</h6>
                                <h6>
                                    Payment Status: 
                                    <span class="badge 
                                        @if($order->payment->paymentStatus == 'Pending')
                                            bg-warning
                                        @elseif($order->payment->paymentStatus == 'Successful')
                                            bg-success
                                        @elseif($order->payment->paymentStatus == 'Failed')
                                            bg-danger
                                        @else
                                            bg-secondary
                                        @endif
                                    ">{{ $order->payment->paymentStatus }}</span>
                                </h6>

                                <h6>Order Date: {{ $order->created_at->format('d/m/Y') }}</h6>

                                <button class="btn btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#orderItems{{ $order->orderId }}" aria-expanded="false" aria-controls="orderItems{{ $order->orderId }}">
                                    View Order Items
                                </button>

                                <div class="collapse mt-3" id="orderItems{{ $order->orderId }}">
                                    <h6>Order Items:</h6>
                                    <ul>
                                        @foreach($order->orderItems as $item)
                                            <li>{{ $item->menu->menuName }} x{{ $item->quantity }} - RM{{ number_format($item->price, 2) }} for each ({{ $item->remarks }})</li>
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- Check if the order status is 'Failed' -->
                                @if ($order->OrderStatus == 'Failed')
                                    <!-- Show Contact Support button for failed orders -->
                                    <a href="https://wa.me/60199687438?text=I%20need%20help%20with%20my%20order%20#{{ $order->orderId }}%20(Order%20Failed)." class="btn btn-danger" target="_blank">
                                        Contact Support
                                    </a>

                                @elseif ($order->feedbackSubmitted)
                                    <!-- Feedback has been submitted, show the view feedback button -->
                                    <a href="{{ route('feedback.index', ['orderId' => $order->orderId]) }}" class="btn btn-secondary">View Feedback</a>
                                @else
                                    <!-- Feedback has not been submitted, show the leave feedback button -->
                                    <a href="{{ route('feedback.create', ['orderId' => $order->orderId]) }}" class="btn btn-primary">Leave Feedback</a>
                                @endif

                            </div>

                            <div style="flex: 0 0 100px; display: flex; justify-content: center; align-items: center;">
                                @if($order->orderItems->isNotEmpty())
                                    @php
                                        $menu = $order->orderItems[0]->menu; // Get the first menu item
                                        $imageUrl = $menu->menuImage ? asset('storage/' . $menu->menuImage) : asset('storage/default-image.jpg'); // Fallback to default image
                                    @endphp
                                    <img src="{{ $imageUrl }}" alt="{{ $menu->menuName }}" style="width: 200px; height: 130px; object-fit: cover; border-radius: 8px;"/>
                                @else
                                    <img src="{{ asset('storage/default-image.jpg') }}" alt="Default Image" style="width: 200px; height: 130px; object-fit: cover; border-radius: 8px;"/>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>


</div>

</div>

@endsection
