<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CartItem;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;


class CartQuantity extends Component
{
    public $cartItems; // Cart items
    public $cartItem;
    public $quantities = []; // Quantities for each cart item
    public $totalPrices = []; // Total prices for each cart item

    public function mount($cartItem)
    {
        $this->cartItem = $cartItem; // Store the single cart item
        $this->quantities[$cartItem->cartItemId] = $cartItem->quantity; // Initialize quantities
        $this->totalPrices[$cartItem->cartItemId] = $cartItem->totalPrice; // Initialize total prices
        \Log::info('Initial quantities: ', $this->quantities); // Log the quantities
    }

    public function increaseQuantity($cartItemId)
    {
        $this->quantities[$cartItemId]++;
        $this->updatedQuantities($cartItemId); // Call the update method
        \Log::info("Increased quantity for cart item {$cartItemId} to {$this->quantities[$cartItemId]}");
    }

    public function decreaseQuantity($cartItemId)
    {
        if ($this->quantities[$cartItemId] > 1) {
            $this->quantities[$cartItemId]--;
            $this->updatedQuantities($cartItemId); // Call the update method
            \Log::info("Decreased quantity for cart item {$cartItemId} to {$this->quantities[$cartItemId]}");
        }
    }

    public function updatedQuantities($cartItemId)
{
    // Validate quantities
    $this->validate([
        'quantities.' . $cartItemId => 'required|integer|min:1',
    ]);

    // Update the quantity in the database
    $cartItem = CartItem::find($cartItemId);
    if ($cartItem) {
        try {
            // Update the quantity and total price for this item
            $cartItem->update(['quantity' => $this->quantities[$cartItemId]]);
            $cartItem->totalPrice = $cartItem->price * $this->quantities[$cartItemId];
            $cartItem->save();

            // Update the total price for the item
            $this->totalPrices[$cartItemId] = $cartItem->totalPrice;
            
            // Fetch all the user's cart items
            $cart = Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                // Sum the total prices of all items in the cart
                $totalCartPrice = CartItem::where('cartId', $cart->cartId)->sum('totalPrice');
                
                // Dispatch the updated total price for the whole cart to the `OrderTotal` component
                $this->dispatch('quantityUpdated', $totalCartPrice); 
            }
        } catch (\Exception $e) {
            \Log::error("Failed to update cart item {$cartItemId}: {$e->getMessage()}");
            session()->flash('error', 'Failed to update the cart item.');
        }
    }
}


    public function render()
    {
        return view('livewire.cart-quantity', ['totalPrice' => $this->totalPrices[$this->cartItem->cartItemId]]);
    }
}
