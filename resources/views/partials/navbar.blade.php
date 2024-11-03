<!-- resources/views/partials/navbar.blade.php -->
@if (Session::get('role') === 'admin')
    <!-- Navigation Bar for Admin -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <a class="navbar-brand" href="{{ route('homepageAdmin') }}">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.staffList') ? 'active' : '' }}" href="{{ route('admin.staffList') }}">
                        <i class="fas fa-address-book"></i> Staff 
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.customerList') ? 'active' : '' }}" href="{{ route('admin.customerList') }}">
                        <i class="fas fa-user-friends"></i> Customer 
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('listMenu') ? 'active' : '' }}" href="{{ route('listMenu') }}">
                        <i class="fas fa-chart-line"></i> Sales
                    </a>
                </li>
                <!-- Only Profile link for Admin -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user"></i> Profile
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    @if(Auth::check())
                        <a class="dropdown-item" href="{{ route('editStaff', ['id' => Auth::user()->id]) }}">
                            <i class="fas fa-pencil-alt"></i> Edit Profile
                        </a>
                    @else
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-pencil-alt"></i> Edit Profile
                        </a>
                    @endif

                    <div class="dropdown-divider"></div>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
@elseif (Session::get('role') === 'staff')
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg  fixed-top">
        <a class="navbar-brand" href="{{ route('homepageStaff') }}">Staff Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item {{ request()->routeIs('staff.orders.incoming') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('staff.orders.incoming') }}">
                        <i class="fas fa-list-alt"></i> Orders
                        @php
                            // Count all orders with specified orderStatus and successful paymentStatus
                            $pendingCount = \App\Models\Order::whereIn('orderStatus', ['pending', 'preparing', 'ready for pickup'])
                                ->whereHas('payment', function ($query) {
                                    $query->where('paymentStatus', 'Successful');
                                })
                                ->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="badge badge-warning">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li>


                <li class="nav-item {{ request()->routeIs('listMenu') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('listMenu') }}">
                        <i class="fas fa-pizza-slice"></i> Menu
                        @php
                            // Count all menu items with quantityStock less than 10
                            $lowStockCount = \App\Models\Menu::where('quantityStock', '<', 10)->count();
                        @endphp
                        @if($lowStockCount > 0)
                            <span class="badge badge-danger">{{ $lowStockCount }}</span>
                        @endif
                    </a>
                </li>


                <li class="nav-item {{ request()->routeIs('feedback.staff') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('feedback.staff') }}">
                        <i class="fas fa-comments"></i> Feedback
                        @php
                            // Count all feedback items with a non-null responseTimestamp
                            $feedbackCount = \App\Models\Feedback::whereNull('responseTimestamp')->count();
                        @endphp
                        @if($feedbackCount > 0)
                            <span class="badge badge-info">{{ $feedbackCount }}</span>
                        @endif
                    </a>
                </li>


                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user"></i> Profile
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    @if(Auth::check())
                        <a class="dropdown-item" href="{{ route('editStaff', ['id' => Auth::user()->id]) }}">
                            <i class="fas fa-pencil-alt"></i> Edit Profile
                        </a>
                    @else
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-pencil-alt"></i> Edit Profile
                        </a>
                    @endif
                    

                    <div class="dropdown-divider"></div>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>

                    </div>
                </li>
            </ul>
        </div>
    </nav>
@else
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <a class="navbar-brand" href="{{ route('homepageStaff') }}">Staff Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                </li>
            </ul>
        </div>
    </nav>
@endif
