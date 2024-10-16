@extends('layouts.customer')

@section('content')
<div class="order-info" style="text-align: center; font-size: 1.2em; margin-bottom: 20px;">
    <p>Here’s your receipt. You can view your order status <a href="{{ route('orders.index', ['tab' => 'current']) }}" style="color: #007bff; text-decoration: underline;">here</a>.</p>
</div>


<div class="receipt-container">
    <div class="receipt-header">
        <h1>Receipt</h1>
        <p><strong>Fariz's Bistro</strong></p>
        <p>
            Universiti Malaysia Terengganu,<br>
            21300 Kuala Terengganu,<br>
            Terengganu<br>
            Phone: +6012-3456789
        </p>
        <p>Date: {{ date('Y-m-d H:i:s') }}</p>
    </div>

    <h5>Order ID: {{ $order->orderId }}</h5>

    <!-- Display Payment Method -->
    <h5>Payment Method: {{ $payment->paymentMethod }}</h5>

    <h5>Ordered Items</h5>
    <table class="receipt-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orderItems as $item)
            <tr>
                <td>{{ $item->menu->menuName }}</td>
                <td>{{ $item->quantity }}</td>
                <td>RM {{ number_format($item->price, 2) }}</td>
                <td>RM {{ number_format($item->totalPrice, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-amount">
        <h4>Total: RM {{ number_format($payment->paymentAmount, 2) }}</h4>
    </div>

    <p class="thank-you">Thank you for your order!</p>

    <!-- Check and display alert -->
    @if(session('alert'))
        <script>
            alert(@json(session('alert')));
        </script>
    @endif
</div>
@endsection
