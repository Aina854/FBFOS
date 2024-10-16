@extends('layouts.customer') <!-- Extending the base layout -->

@section('title', 'Customer Home') <!-- Defining the title -->

@section('content')

    <!-- Alert Message Section -->
    @if(session('alert'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('alert') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success" id="cart-alert">
        {{ session('success') }}
    </div>
    @endif


    <!-- Image Slider Section -->
    <div id="imageSlider" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('images/stafflogin.jpg') }}" class="d-block w-100" alt="Staff Login">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('images/adminlogin.jpg') }}" class="d-block w-100" alt="Admin Login">
            </div>
            <!-- Add more slides as needed -->
        </div>
    </div>

    
    <div class="container"> <!-- Use custom-container for full-width -->
        <div class="welcome-section">
            <div class="welcome-content">
                <h2 class="display-4">Welcome {{ auth()->user()->firstName }}!</h2><br><br>
                <p>We’re thrilled to see you at Fariz's Bistro. Browse our menu and let us serve you a great meal today!</p>
            </div>
        </div>

    <!-- Menu Section -->
    <div class="menu-section mt-5">
        <h2>Menu</h2>
        <!-- Search and Filter Section -->
        <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="categoryFilter">Category</label>
                            </div>
                            <select class="custom-select" id="categoryFilter" onchange="applyFilters()">
                                <option value="all">All Categories</option>
                                <option value="Main Course">Main Course</option>
                                <option value="Western">Western</option>
                                <option value="Drinks">Drinks</option>
                                <option value="Side Order">Side Order</option>
                                <!-- Add more categories as needed -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3"> <!-- Move mb-3 here for consistent spacing -->
                            <input type="text" id="search" class="form-control" placeholder="Search by Menu Name" onkeyup="filterMenu()">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="applyFilters()">Search</button> <!-- Changed to btn -->
                            </div>
                        </div>
                    </div>

                </div>


        <div class="row">
            @foreach($menus as $menu)
                <div class="col-md-4">
                    <div class="card" data-category="{{ strtolower($menu->menuCategory) }}">
                        <img src="{{ asset('storage/' . $menu->menuImage) }}" class="card-img-top" alt="{{ $menu->menuName }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $menu->menuName }}</h5>
                            <!--<p class="card-text" >{{ $menu->description }}</p>-->
                            <p class="card-price">RM{{ number_format($menu->price, 2) }}</p>
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="cartId" value="{{ session('cartId') }}">
                                <input type="hidden" name="menuId" value="{{ $menu->menuId }}">
                                <input type="number" name="quantity" min="1" value="1" required>
                                <button type="submit" class="btn btn-primary">Add to Cart</button>
                            </form>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // JavaScript for the image slider
    $('#imageSlider').carousel({
        interval: 2000
    });

    $(document).ready(function() {
    $('.btn-primary').on('click', function(e) {
        e.preventDefault(); // Prevent immediate form submission
        
        let button = $(this);
        let cartIcon = $('#cart-icon');
        let imgToDrag = button.closest('.card').find("img").eq(0);

        if (imgToDrag.length) {
            let imgClone = imgToDrag.clone()
                .offset({
                    top: imgToDrag.offset().top,
                    left: imgToDrag.offset().left
                })
                .css({
                    'opacity': '0.8',
                    'position': 'absolute',
                    'height': '150px',
                    'width': '150px',
                    'z-index': '100'
                })
                .appendTo($('body'))
                .animate({
                    'top': cartIcon.offset().top + 10,
                    'left': cartIcon.offset().left + 10,
                    'width': 50,
                    'height': 50
                }, 1000, function() {
                    imgClone.remove(); // Remove the clone after animation completes
                    // Submit the form after the animation finishes
                    button.closest('form').submit(); 
                });

            imgClone.animate({
                'width': 0,
                'height': 0
            });
        }
    });
});

////////////////////////
function applyFilters() {
    const searchValue = document.getElementById('search').value.toLowerCase();
    const categoryFilterValue = document.getElementById('categoryFilter').value.toLowerCase();
    const menuCards = document.querySelectorAll('.card');

    // Get the parent containers for the cards
    const containers = Array.from(document.querySelectorAll('.col-md-4'));

    containers.forEach(container => {
        const card = container.querySelector('.card'); // Get the card within the container
        const menuName = card.querySelector('.card-title').innerText.toLowerCase();
        const menuCategory = card.getAttribute('data-category').toLowerCase();

        // Determine if the card should be shown based on the filters
        const isCategoryMatch = categoryFilterValue === 'all' || menuCategory === categoryFilterValue;
        const isSearchMatch = menuName.includes(searchValue);

        // Show or hide the card container based on the filters
        if (isCategoryMatch && isSearchMatch) {
            container.style.display = ''; // Show container
        } else {
            container.style.display = 'none'; // Hide container
        }
    });

    // Optional: Handle the case when no items are found
    const visibleItems = Array.from(document.querySelectorAll('.col-md-4:not([style*="display: none"])'));
    const menuSection = document.querySelector('.menu-section');

    // Show a message if no items are found
    if (visibleItems.length === 0) {
        menuSection.innerHTML += '<p>No menu items found</p>'; // Adjust this as necessary
    } else {
        const message = menuSection.querySelector('p');
        if (message) {
            message.remove(); // Remove message if items are found
        }
    }
}



</script>


@endsection
