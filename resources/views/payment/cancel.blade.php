@extends('layouts.customer')

@section('content')
<div class="container mt-4">
    <div class="alert alert-warning text-center">
        <h2>Payment Cancelled</h2>
        <p>Your payment has been cancelled. Please review your order or contact support if you need assistance.</p>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5>Order Summary</h5>
        </div>
        <div class="card-body">
            <p><strong>Order ID:</strong> {{ $order->orderId ?? 'N/A' }}</p>
            <p><strong>Payment Status:</strong> Pending</p>
            <p><strong>Total Amount:</strong> RM {{ $payment->paymentAmount ?? 'N/A' }}</p> <!-- Adjust according to your order attributes -->
            <p><strong>Last Payment Attempt: </strong>{{ $payment->last_attempt_at ?? 'N/A' }}</p> 
            <p><strong>Payment Attempts Made:</strong> {{ $payment->attempts ?? 'N/A' }}</p> <!-- Display attempts made -->
            <p><strong>Note:</strong> Please complete your payment within 24 hours to avoid any issues. You have a maximum of 5 attempts to complete the payment.</p> <!-- Updated note -->

            <h6>Items:</h6>
            <ul>
                @foreach($order->items as $item) <!-- Adjust the relationship name -->
                    <li>{{ $item->menu->menuName }} x {{ $item->quantity }} - Price: RM {{ $item->price }} for each</li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="text-center">
    <a href="{{ route('orders.index', ['orderId' => $order->orderId ?? null, 'tab' => 'pending']) }}" class="btn btn-primary me-2">View Order</a>
        <a href="{{ route('payment.again', ['orderId' => $order->orderId ?? null]) }}" class="btn btn-success">Pay Again</a>
    </div>

    @if(session('alert'))
        <script>
            alert("{{ session('alert') }}");
        </script>
    @endif
</div>
@endsection
