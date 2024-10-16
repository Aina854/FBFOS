<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <a class="navbar-brand" href="{{ url('/') }}"><i class="fas fa-store"></i></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            @if(Auth::check() && Auth::user()->category == 'Customer')
                <!-- For logged-in customers -->
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('homepageCustomer') }}"><i class="fas fa-home"></i> Home</a>
                </li>
                <li class="nav-item">
    <a class="nav-link" href="{{ route('showCart', ['cartId' => Auth::user()->cart->cartId]) }}">
        <i id="cart-icon" class="fas fa-shopping-cart"></i>
        <span id="cart-count" class="badge badge-pill badge-danger">
            {{ Auth::user()->cart->cartItems()->sum('quantity') }}
        </span>
        Cart
    </a>
</li>


                
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('orders.index') }}">
                        <i class="fas fa-list-alt"></i> Orders
                    </a>
                </li>
                

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user"></i> Profile
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('editProfile', ['id' => Auth::user()->id]) }}"><i class="fas fa-pencil-alt"></i> Edit Profile</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            @else
                <!-- For guests -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user"></i> Login/Signup
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a class="dropdown-item" href="{{ route('register') }}"><i class="fas fa-user-plus"></i> Signup</a>
                    </div>
                </li>
            @endif
        </ul>
    </div>
</nav>
