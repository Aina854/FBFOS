<div class="col-md-3">
    <div class="sidebar bg-light p-3">
        <h4 class="text-center">Orders</h4>
        <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <a href="{{ route('staff.orders.incoming') }}">Incoming Orders</a>
                <span class="badge bg-warning">{{ $sidebarData['incomingOrderCount'] }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <a href="{{ route('staff.orders.details') }}">In Progress Order</a>
                <span class="badge bg-info">{{ $sidebarData['orderDetailCount'] }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <a href="{{ route('staff.orders.completed') }}">Completed Orders</a>
                <span class="badge bg-success">{{ $sidebarData['completedOrderCount'] }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <a href="{{ route('staff.orders.rejected') }}">Rejected Orders</a>
                <span class="badge bg-danger">{{ $sidebarData['rejectedOrderCount'] }}</span>
            </li>
        </ul>
    </div>
</div>


