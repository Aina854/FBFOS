@extends('layouts.customer')

@section('title', 'Order Summary')

@section('content')
    <!-- Order Summary Section -->
    <div class="container mt-4">
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
                                        <h5 class="item-name">{{ $cartItem->menuName }}</h5>
                                    </div>
                                </td>
                                <td>RM{{ number_format($cartItem->price, 2) }}</td>
                                <td>{{ $cartItem->quantity }}</td>
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
                <form action="{{ route('cart.proceedToPayment', ['cartId' => $cartId]) }}" method="POST" class="d-inline">
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
    <!-- Add any specific scripts for this page here -->
@endsection
