@extends('layouts.customer')

@section('title', 'Order Summary')

@section('content')
    <!-- Order Summary Section -->
    <div class="container mt-4">
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

        <div class="order-summary">
            <h2 class="text-center">Order Summary</h2>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 150px;">Item</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $cartItem)
                        <tr>
                            <td>
                                <div class="item-details">
                                    <img src="{{ asset('storage/' . $cartItem->menu->menuImage) }}" 
                                        class="menu-image" 
                                        alt="Image of {{ $cartItem->menu->menuName }}">    
                                </div>
                                <p class="item-name" style="font-size: 1.0em;">{{ $cartItem->menu->menuName }}</p>
                                <span style="font-size: 0.8em; color: darkgrey;">Available Stock: {{ $cartItem->menu->quantityStock }}</span>
                            </td>
                            <td>RM{{ number_format($cartItem->price, 2) }}</td>
                            <td>
                                <span class="cart-quantity">{{ $cartItem->quantity }}</span>
                                <input type="hidden" class="quantity-stocks" value="{{ $cartItem->menu->quantityStock }}">
                            </td>
                            <td>RM{{ number_format($cartItem->totalPrice, 2) }}</td>
                        </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Remarks -->
            <div class="remarks">
                <strong>Remarks:</strong> {{ $remarks }}
            </div>

            <!-- Total Price -->
            <div class="total-price">Order Total: RM{{ number_format($total, 2) }}</div>

            <!-- Buttons -->
            <div class="mt-3">
                <a href="{{ route('showCart', ['cartId' => $cartId]) }}" class="btn btn-backtc">Back to Cart</a>
                
                <!-- Proceed to Payment Button as a Form -->
                <form id="proceedToPaymentForm" action="{{ route('cart.proceedToPayment', ['cartId' => $cartId]) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="total" value="{{ $total }}">
                    <input type="hidden" name="remarks" value="{{ $remarks }}">
                    <button type="submit" class="btn btn-proceedtp">Proceed to Payment</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('proceedToPaymentForm').addEventListener('submit', function(event) {
            const rows = document.querySelectorAll('tbody tr');
            let isValid = true; // Flag to track if quantities are valid

            rows.forEach(row => {
                const quantity = parseInt(row.querySelector('.cart-quantity').innerText);
                const quantityStocks = parseInt(row.querySelector('.quantity-stocks').value);

                if (quantity > quantityStocks) {
                    isValid = false; // Set flag to false if any quantity exceeds stock
                    alert(`The quantity for ${row.querySelector('.item-name').innerText} exceeds the available stock (${quantityStocks}). Please adjust your order.`);
                }
            });

            if (!isValid) {
                event.preventDefault(); // Prevent form submission if invalid
            }
        });
    </script>
@endsection
