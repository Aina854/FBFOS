<!-- resources/views/partials/navbar.blade.php -->
@if (Session::get('role') === 'staff')
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
                    <a class="nav-link" href="{{ route('staff.orders.incoming') }}">Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('listMenu') }}">Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('feedback.staff') }}">Feedback</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user"></i> Profile
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    @if(Auth::check())
                        <a class="dropdown-item" href="{{ route('editProfile', ['id' => Auth::user()->id]) }}">
                            <i class="fas fa-pencil-alt"></i> Edit Profile
                        </a>
                    @else
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-pencil-alt"></i> Edit Profile
                        </a>
                    @endif


                    

                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="return confirm('Are you sure you want to logout?');">
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
