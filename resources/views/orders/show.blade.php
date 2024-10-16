<!-- resources/views/orders/show.blade.php -->

@extends('layouts.customer')

@section('content')
<div class="container">
    <h1>Order Details</h1>
    
    <h2>Order Status: {{ $order->OrderStatus }}</h2>
    <p>Order Date: {{ $order->OrderDate }}</p>
    <p>Remarks: {{ $order->remarks }}</p>
    <p>Staff Name: {{ $order->staffFirstName }} {{ $order->staffLastName }}</p>
    <p>Feedback Submitted: {{ $order->feedbackSubmitted ? 'Yes' : 'No' }}</p>

    <h3>Order Items</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Menu Item</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $item)
            <tr>
                <td>{{ $item->menu->menuName }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->totalPrice }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('orders.index') }}" class="btn btn-primary">Back to Orders</a>
</div>
@endsection
