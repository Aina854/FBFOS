<div>
    <div class="cart-item-controls">
        <!-- Decrease Button -->
        <button wire:click="decreaseQuantity({{ $cartItem->cartItemId }})" 
                class="btn-decrease" 
                type="button">-</button>

        <!-- Display the Quantity -->
        <input type="text" 
               wire:model="quantities.{{ $cartItem->cartItemId }}" 
               min="1" 
               class="quantity-input" 
               readonly />

        <!-- Increase Button -->
        <button wire:click="increaseQuantity({{ $cartItem->cartItemId }})" 
                class="btn-increase" 
                type="button">+</button>
    </div>
    <br>
    <!-- Display the Total Price -->
    <p class="item-total-price">Total: RM{{ number_format($totalPrice, 2) }}</p>
</div>
