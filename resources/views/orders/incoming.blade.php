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

            <h1 class="mb-4">Incoming Orders</h1>

            <!-- Notification Message -->
            <div class="alert alert-info">
                This page will auto-refresh every 30 minutes. You can click the button below to refresh orders manually.
            </div>

            <!-- Refresh Button -->
            <button id="refresh-button" class="btn btn-secondary mb-3">Refresh Orders</button>

            <!-- Search Bar -->
            <div class="mb-3">
                <input type="text" id="search" class="form-control" placeholder="Search by Order ID or Customer Name" onkeyup="searchOrders()">
            </div>

            @if($incomingOrders->isEmpty())
                <div class="alert alert-info">
                    No incoming orders found.
                </div>
            @else
                @foreach($incomingOrders as $order)
                    <div class="card mb-4 order-card" data-order-id="{{ $order->orderId }}">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Order ID: #{{ $order->orderId }}</h5>
                            <span class="badge bg-warning">{{ $order->OrderStatus }}</span>
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
                            <div class="order-items mt-3">
                                <h6>
                                    <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#order-items-{{ $order->orderId }}" aria-expanded="false" aria-controls="order-items-{{ $order->orderId }}">
                                        View Items
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

                            <div class="mt-3">
                                <form action="{{ route('staff.orders.accept', $order->orderId) }}" method="POST" class="accept-form" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Accept</button>
                                </form>
                                <form action="{{ route('staff.orders.reject', $order->orderId) }}" method="POST" style="display: inline;" onsubmit="return confirmReject()">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Reject</button>
                                </form>
                                <!-- Add Print Button -->
                                <button type="button" class="btn btn-primary" onclick="printOrder({{ $order->orderId }})">Print</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Tips Section -->
<div class="col-md-3 mt-4">
    <div class="card tips-card">
        <div class="card-header">
            <h4>Tips for Staff</h4>
        </div>
        <div class="card-body">
            <ul>
                <li>1. Print order</li>
                <li>2. Then click accept</li>
            </ul>
            <div class="alert alert-warning mt-2">
                Note: Once you accept the order, you will not be able to print it anymore.
            </div>
        </div>
    </div>
</div>

</div>

<script>
    function searchOrders() {
        const input = document.getElementById('search').value.toLowerCase();
        const orderCards = document.querySelectorAll('.order-card');

        orderCards.forEach(card => {
            const orderId = card.getAttribute('data-order-id').toLowerCase();
            const customerName = card.querySelector('.customer-name') ? card.querySelector('.customer-name').textContent.toLowerCase() : '';

            if (orderId.includes(input) || customerName.includes(input)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function confirmReject() {
        return confirm("Are you sure you want to reject this order?");
    }

    // Handle form submission for accepting orders
    document.querySelectorAll('.accept-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission
            
            // Show alert about printing order before accepting
            const confirmation = confirm("Please make sure to print the order before accepting it. Do you want to continue?");
            if (!confirmation) {
                return; // If the user cancels, do not proceed with form submission
            }

            const orderId = this.action.split('/').pop(); // Extract order ID from form action
            const formData = new FormData(this);

            // Send the form data via fetch
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest', // To indicate an AJAX request
                    'X-CSRF-TOKEN': formData.get('_token') // Include CSRF token
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.success); // Show success message
                    // Redirect to incoming page after a short delay
                    setTimeout(() => {
                        window.location.href = '{{ route("staff.orders.incoming") }}'; // Redirect to incoming orders
                    }, 1000); // Adjust the delay as necessary (2000 ms = 2 seconds)
                } else if (data.error) {
                    alert(data.error); // Show error message
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    function printOrder(orderId) {
        const orderCard = document.querySelector(`.order-card[data-order-id="${orderId}"]`);
        const orderItems = Array.from(orderCard.querySelectorAll('.list-group-item')).map(item => {
            const itemText = item.textContent.split(' - '); // Split item details
            return `${itemText[0]} - Quantity: ${itemText[1].split(': ')[1]}`; // Format for print
        });

        const printContents = `
            <h2>Order ID: #${orderId}</h2>
            <h3>Order Items:</h3>
            <ul>
                ${orderItems.map(item => `<li>${item}</li>`).join('')}
            </ul>
        `;

        const win = window.open('', '', 'height=400,width=600');
        win.document.write('<html><head><title>Print Order</title>');
        win.document.write('<style>body { font-family: Arial, sans-serif; }</style>'); // Optional styling for better readability
        win.document.write('</head><body>');
        win.document.write(printContents);
        win.document.write('</body></html>');
        win.document.close();
        win.print();
    }

    // Auto-refresh every 30 minutes (1800000 ms)
    setInterval(() => {
        location.reload();
    }, 1800000); // 30 minutes in milliseconds

    // Refresh button functionality
    document.getElementById('refresh-button').addEventListener('click', function() {
        location.reload(); // Reload the page when button is clicked
    });
</script>

@endsection
