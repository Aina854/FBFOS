<div wire:poll.5000ms>
<div>
        @if($currentOrders->isEmpty())
            <p>No processing orders found.</p>
        @else
            @foreach($currentOrders as $order)
                <div class="card mb-4 current-order-card" id="order-{{ $order->orderId }}" style="display: flex;" wire:key="order-{{ $order->orderId }}">
                    <div class="card-body" style="flex: 1;">
                        <div class="card-header">
                            <h5 class="mb-0">Order ID: #{{ $order->orderId }}</h5>
                            <span class="badge 
                                @if($order->OrderStatus == 'Pending') bg-warning
                                @elseif($order->OrderStatus == 'Preparing') bg-info
                                @elseif($order->OrderStatus == 'Ready for Pickup') bg-primary
                                @elseif($order->OrderStatus == 'Completed') bg-success
                                @elseif($order->OrderStatus == 'Failed') bg-danger
                                @else bg-secondary
                                @endif
                            ">{{ $order->OrderStatus }}</span>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="flex: 1;">
                                <h6>Total Price: RM{{ number_format($order->payment->paymentAmount, 2) }}</h6>
                                <h6>
                                    Payment Status: 
                                    <span class="badge 
                                        @if($order->payment->paymentStatus == 'Pending') bg-warning
                                        @elseif($order->payment->paymentStatus == 'Successful') bg-success
                                        @elseif($order->payment->paymentStatus == 'Failed') bg-danger
                                        @else bg-secondary
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
                            </div>

                            <div style="flex: 0 0 100px; display: flex; justify-content: center; align-items: center;">
                                @php
                                    $menu = $order->orderItems->first()->menu ?? null;
                                    $imageUrl = $menu && $menu->menuImage ? asset('storage/' . $menu->menuImage) : asset('storage/default-image.jpg');
                                @endphp
                                <img src="{{ $imageUrl }}" alt="{{ $menu->menuName ?? 'Default Image' }}" style="width: 200px; height: 130px; object-fit: cover; border-radius: 8px;"/>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5>Order Status Progress:</h5>
                            <h6>Debug: {{($order->OrderStatus) }}</h6>
                            <div class="d-flex justify-content-between">
                                @php
                                    // Set the icon colors based on the order status
                                    $iconColorPending = ($order->OrderStatus == 'Pending') ? 'orange' : 'gray';
                                    $iconColorPreparing = ($order->OrderStatus == 'Preparing') ? 'orange' : 'gray';
                                    $iconColorReady = ($order->OrderStatus == 'Ready for Pickup') ? 'blue' : 'gray';
                                    $iconColorCompleted = ($order->OrderStatus == 'Completed') ? 'green' : 'gray';
                                @endphp

                                <span wire:key="icon-{{ $order->OrderStatus }}-Pending" title="Order is Pending">
                                    <i class="fas fa-clock" style="color: {{ $iconColorPending }};"></i> Pending
                                </span>
                                <span wire:key="icon-{{ $order->OrderStatus }}-Preparing" title="Order is Preparing">
                                    <i class="fas fa-blender" style="color: {{ $iconColorPreparing }};"></i> Preparing
                                </span>
                                <span wire:key="icon-{{ $order->OrderStatus }}-ReadyForPickup" title="Order is Ready for Pickup">
                                    <i class="fas fa-cutlery" style="color: {{ $iconColorReady }};"></i> Ready for Pickup
                                </span>
                                <span wire:key="icon-{{ $order->OrderStatus }}-Completed" title="Order is Completed">
                                    <i class="fas fa-check-circle" style="color: {{ $iconColorCompleted }};"></i> Completed
                                </span>
                            </div>
                            <div class="progress mt-2">
                                @php
                                    // Initialize progress value
                                    $progressValue = 0;

                                    // Determine progress value based on order status
                                    switch ($order->OrderStatus) {
                                        case 'Pending':
                                            $progressValue = 0; // 0% for Pending
                                            break;
                                        case 'Preparing':
                                            $progressValue = 33; // 33% for Preparing
                                            break;
                                        case 'Ready for Pickup':
                                            $progressValue = 66; // 66% for Ready for Pickup
                                            break;
                                        case 'Completed':
                                            $progressValue = 100; // 100% for Completed
                                            break;
                                        default:
                                            $progressValue = 0; // Default to 0% if status is unknown
                                            break;
                                    }

                                    // Set background class based on progress
                                    $bgClass = $progressValue < 100 ? 'bg-warning' : 'bg-success';

                                    // Generate a unique wire:key based on orderId and OrderStatus
                                    $wireKey = "progress-{$order->orderId}-{$order->OrderStatus}";
                                @endphp
                                <div class="progress-bar {{ $bgClass }} progress-bar-flow-custom" 
                                    role="progressbar"
                                    wire:key="{{ $wireKey }}" 
                                    style="width: {{ $progressValue }}%;"
                                    aria-valuenow="{{ $progressValue }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    {{ $order->OrderStatus }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
   

</div>
<script>
$(document).ready(function() {
    Livewire.on('order-completed', (orderId) => {
        console.log("Event 'order-completed' triggered with orderId: ", orderId);
        
        Swal.fire({
            title: 'Order Completed!',
            text: 'Order #' + orderId + ' has been completed!',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to orders history tab only when OK is clicked
                window.location.href = '/orders?tab=history';
            }
        });
    });
});
</script>




