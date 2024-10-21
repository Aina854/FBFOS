@extends('layouts.customer')

@if (session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
@endif

@section('title', 'My Cart')
@section('content')
    <!-- Cart Section -->
    <div class="container">
        <div class="cart-section">
            <h2>My Cart</h2>

            @if($cartItems->isEmpty())
                <div class="empty-cart-message">
                    <i class="fas fa-shopping-cart fa-5x"></i>
                    <p>Your cart is empty. <a href="{{ route('homepageCustomer') }}">Continue Shopping</a></p>
                </div>
            @else
                <!-- Available Items Section -->
                <div class="available-items">
                    <h3>Available Items</h3>
                    @foreach($cartItems as $cartItem)
                        @if($cartItem->menu->quantityStock > 0)
                            <div class="cart-item">
                                <div class="item-details">
                                    <div class="item-description">
                                        <p class="item-name" style="font-size: 1.1em;">{{ $cartItem->menu->menuName }}</p>
                                        <p class="item-price">Price: RM{{ number_format($cartItem->price, 2) }}</p>
                                        <livewire:cart-quantity :cartItem="$cartItem" :key="$cartItem->cartItemId" />
                                        <span style="font-size: 0.8em; color: darkgrey;">Available Stock: {{ $cartItem->menu->quantityStock }}</span>
                                        <!--<p class="item-createdAt">Created: {{ $cartItem->createdAt->format('Y-m-d') }}</p>-->
                                    </div>
                                    
                                    <form action="{{ route('cart.deleteItem', ['cartItemId' => $cartItem->cartItemId]) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-btn" title="Remove from cart">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>

                                <img src="{{ asset('storage/' . $cartItem->menu->menuImage) }}" 
                                     class="cart-item-image" 
                                     alt="Image of {{ $cartItem->menu->menuName }}">

                                    
                            </div>
                            <!-- Remarks for each item -->
    <div class="form-group">
        <label for="remarks_{{ $cartItem->cartItemId }}">Remarks for {{ $cartItem->menu->menuName }}:</label>
        <textarea class="form-control" id="remarks_{{ $cartItem->cartItemId }}" name="remarks[{{ $cartItem->cartItemId }}]" rows="2"></textarea>
    </div> 
                        @endif
                    @endforeach
                </div>

                <!-- Unavailable Items Section -->
                <div class="unavailable-items mt-4">
                    <h3>Unavailable Items</h3>
                    @foreach($cartItems as $cartItem)
                        @if($cartItem->menu->quantityStock <= 0)
                        <div class="cart-item unavailable" style="display: flex; align-items: center;">
                                <div class="item-details">
                                    <div class="item-description" style="width: 300px; padding: 10px; margin: 0 10px;">
                                        <p class="item-name" style="font-size: 1.1em;">{{ $cartItem->menu->menuName }}</p>
                                        <p class="item-price">Price: RM{{ number_format($cartItem->price, 2) }}</p>
                                        <!--<p class="item-createdAt">Created: {{ $cartItem->createdAt->format('Y-m-d') }}</p>-->
                                        <p class="item-unavailable">This item is not available at the moment.</p>
                                    </div>

                                    <form action="{{ route('cart.deleteItem', ['cartItemId' => $cartItem->cartItemId]) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-btn" title="Remove from cart">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>

                                <img src="{{ asset('storage/' . $cartItem->menu->menuImage) }}" 
                                    class="cart-item-image" 
                                    alt="Image of {{ $cartItem->menu->menuName }}"
                                    style="max-width: 280px; max-height: 300px; object-fit: cover; margin-left: 450px;">
                            </div>

                        @endif
                    @endforeach
                </div>

                <!-- Confirmation for deletion -->
                <script>
                    function confirmDelete() {
                        return confirm('Are you sure you want to delete this item?');
                    }
                </script>

               <!-- Side Menu Section -->
<div class="side-menu-container">
    <p style="font-size: 20px; font-weight: bold;">Side Order</p>
    <div class="side-menu-wrapper">
        <div class="side-menu-scroll">
            @foreach($menu as $menuItem)
                @if($menuItem->menuCategory == 'Side Order' && $menuItem->quantityStock > 0) <!-- Added condition to check stock -->
                    <div class="side-menu-card">
                        <img src="{{ asset('storage/' . $menuItem->menuImage) }}" class="side-menu-image" alt="Image of {{ $menuItem->menuName }}">
                        <div class="side-menu-details">
                            <h5 class="side-menu-title">{{ $menuItem->menuName }}</h5>
                            <p class="side-menu-price">Price: RM{{ number_format($menuItem->price, 2) }}</p>
                            <p class="card-quantity-stock" 
                                style="color: 
                                    <?php 
                                        if($menuItem->quantityStock == 0) {
                                            echo 'red'; 
                                        } elseif($menuItem->quantityStock <= 10) {
                                            echo 'orange'; 
                                        } else {
                                            echo 'green'; 
                                        }
                                    ?>
                                ">
                                Available Quantity: {{ $menuItem->quantityStock }}
                            </p>

                            <form action="{{ route('cart.addSideOrder', ['cartId' => $cart->cartId]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="menuId" value="{{ $menuItem->menuId }}">
                                <input type="hidden" name="price" value="{{ $menuItem->price }}">
                                <input type="hidden" name="id" value="{{ $customer->id }}">
                                <input type="number" name="quantity" class="side-menu-quantity-input" value="1" min="1" max="{{ $menuItem->quantityStock }}"  required>
                                <button type="submit" class="side-menu-add-to-cart-btn">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>



                <!-- Total Price and Checkout Button Container -->
                <div class="total-price-container mt-4">
                <livewire:order-total :cartItems="$cartItems" :key="$cart->cartId" />

                    <div style="margin-bottom: 20px; margin-left: 20px; display: flex; justify-content: space-between;">
                        <div class="mt-3">
                            <a href="{{ route('homepageCustomer') }}" class="btn-back">Continue Shopping</a>
                            <button onclick="checkoutWithRemarks()" class="checkout-btn">Checkout</button>
                        </div>
                    </div>
                </div>




            @endif

            <!-- JavaScript for total price and checkout -->
            <script>
                function checkoutWithRemarks() {
    var unavailableItems = document.querySelectorAll('.cart-item.unavailable');
    if (unavailableItems.length > 0) {
        alert('Please remove unavailable items from your cart before proceeding to payment.');
        return;
    }

    var totalPrice = parseFloat(document.getElementById('totalPrice').innerText.replace('RM', ''));
    if (totalPrice < 2) {
        alert('The total amount must be at least RM2.00. Please add more items to your cart.');
        return;
    }

    // Collect individual remarks for each item
    var remarks = {};
    document.querySelectorAll('textarea[id^="remarks_"]').forEach(function(textarea) {
        var cartItemId = textarea.id.split('_')[1];
        remarks[cartItemId] = textarea.value.trim() === '' ? 'No remarks' : textarea.value;
    });

    var cartId = "{{ $cart->cartId }}";
    var url = `{{ route('cart.showOrderSummary', ['cartId' => '__CART_ID__']) }}`.replace('__CART_ID__', cartId) + `?remarks=${encodeURIComponent(JSON.stringify(remarks))}`;
    window.location.href = url;
}



                window.addEventListener('livewire:load', function () {
                Livewire.on('quantityUpdated', function (data) {
                    document.getElementById('totalPrice').innerText = 'RM' + data.overallTotal.toFixed(2);
                });
            });

            </script>

                    </div>
                </div>
@endsection
