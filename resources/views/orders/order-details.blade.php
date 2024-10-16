@extends('layouts.staff')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('partials.sidebarorder')

        <!-- Main Content -->
        <div class="col-md-9">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

            <h1 class="mb-4">Order Details</h1>

            <!-- Preparing Orders Section -->
            <div class="mb-4">
                <h2>Preparing Orders</h2>

                @if($preparingOrders->isEmpty())
                <div class="alert alert-info">
                        No preparing orders found.
                    </div>
                @else
                    @foreach($orders as $order)
                        @if($order->OrderStatus === 'Preparing') <!-- Adjust the status as needed -->
                            <div class="card mb-3 order-card" data-order-id="{{ $order->orderId }}">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Order ID: #{{ $order->orderId }}</h5>
                                    <span class="badge bg-info">{{ $order->OrderStatus }}</span>
                                </div>
                                <div class="card-body">
                                    <h6>Total Price: RM{{ number_format($order->payment->paymentAmount, 2) }}</h6>
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
                                    <h6>Number of Items: {{ $order->orderItems->count() }}</h6>

                                    <!-- Expandable Order Items Section -->
                                    <div class="mt-3">
                                        <h6>
                                            <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#order-items-{{ $order->orderId }}" aria-expanded="false" aria-controls="order-items-{{ $order->orderId }}">
                                                Order Items
                                            </button>
                                        </h6>
                                        <div class="collapse" id="order-items-{{ $order->orderId }}">
                                            <ul class="list-group mt-2">
                                                @foreach($order->orderItems as $item)
                                                    <li class="list-group-item">
                                                        {{ $item->menu->menuName }} - Quantity: x{{ $item->quantity }} - RM{{ number_format($item->price, 2) }} each
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Progress Bar and Action Dropdown -->
                                    <div class="mt-3">
                                        <h6>Progress:</h6>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-flow" role="progressbar" style="width: {{ $order->progress }}%;" aria-valuenow="{{ $order->progress }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $order->progress }}%
                                            </div>
                                        </div>

                                        <h6>Change Order Status:</h6>
                                        <form action="{{ route('staff.orders.updateStatus', $order->orderId) }}" method="POST">
                                            @csrf
                                            <select name="OrderStatus" class="form-select">
                                                <option value="Ready for Pickup">Ready for Pickup</option>
                                                <option value="Completed">Completed</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary mt-2">Update Status</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>

            <!-- Ready for Pickup Orders Section -->
            <div class="mb-4">
                <h2>Ready for Pickup Orders</h2>
                @if($readyForPickupOrders->isEmpty())                   
                    <div class="alert alert-info">
                        No ready for pickup orders found.
                    </div>
                @else
                    @foreach($orders as $order)
                        @if($order->OrderStatus === 'Ready for Pickup') <!-- Adjust the status as needed -->
                            <div class="card mb-3 order-card" data-order-id="{{ $order->orderId }}">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Order ID: #{{ $order->orderId }}</h5>
                                    <span class="badge bg-success">{{ $order->OrderStatus }}</span>
                                </div>
                                <div class="card-body">
                                    <h6>Total Price: RM{{ number_format($order->payment->paymentAmount, 2) }}</h6>
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
                                    <h6>Number of Items: {{ $order->orderItems->count() }}</h6>

                                    <!-- Expandable Order Items Section -->
                                    <div class="mt-3">
                                        <h6>
                                            <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#order-items-{{ $order->orderId }}" aria-expanded="false" aria-controls="order-items-{{ $order->orderId }}">
                                                Order Items
                                            </button>
                                        </h6>
                                        <div class="collapse" id="order-items-{{ $order->orderId }}">
                                            <ul class="list-group mt-2">
                                                @foreach($order->orderItems as $item)
                                                    <li class="list-group-item">
                                                        {{ $item->menu->menuName }} - Quantity: x{{ $item->quantity }} - RM{{ number_format($item->price, 2) }} each
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>

                                    <h6>Progress:</h6>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-flow" role="progressbar" style="width: {{ $order->progress }}%;" aria-valuenow="{{ $order->progress }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ $order->progress }}%
                                        </div>
                                    </div>

                                    <h6>Change Order Status:</h6>
                                        <form action="{{ route('staff.orders.updateStatus', $order->orderId) }}" method="POST">
                                            @csrf
                                            <select name="OrderStatus" class="form-select">
                                            <option value="Completed">Completed</option>
                                            <option value="Ready for Pickup">Ready for Pickup</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary mt-2">Update Status</button>
                                        </form>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
