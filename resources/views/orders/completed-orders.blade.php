@extends('layouts.staff')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('partials.sidebarorder')

        <!-- Main Content -->
        <div class="col-md-9">
            <h1 class="mb-4">Completed Orders</h1>

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

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-center">Order ID</th>
                        <th class="text-center">Total Price</th>
                        <th class="text-center">Payment Status</th>
                        <th class="text-center">Order Date</th>
                        <th class="text-center">Number of Items</th>
                        <th class="text-center">Order Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($completedOrders as $order)
                        <tr>
                            <td class="text-center">#{{ $order->orderId }}</td>
                            <td class="text-center">RM{{ number_format($order->payment->paymentAmount, 2) }}</td>
                            <td class="text-center">
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
                            </td>
                            <td class="text-center">{{ $order->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <h6>
                                    <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#order-items-{{ $order->orderId }}" aria-expanded="false" aria-controls="order-items-{{ $order->orderId }}">
                                        {{ $order->orderItems->count() }}
                                    </button>
                                </h6>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $order->OrderStatus }}</span>
                            </td>
                        </tr>
                        <tr class="collapse" id="order-items-{{ $order->orderId }}">
                            <td colspan="6">
                                <ul class="list-group">
                                    @foreach($order->orderItems as $item)
                                        <li class="list-group-item">
                                            {{ $item->menu->menuName }} - RM{{ number_format($item->price, 2) }} (Quantity x{{ $item->quantity }})
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($completedOrders->isEmpty())
                <div class="alert alert-info">
                    No completed orders found.
                </div>
            @endif
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Optionally, you can add custom scripts here if needed
</script>
@endsection
@endsection
