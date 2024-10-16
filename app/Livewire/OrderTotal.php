<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CartItem;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class OrderTotal extends Component
{
    public $totalPrice = 0.00;
    public $cartItems = [];

    public function mount()
    {
        // Fetch the current user's cart
        $cart = Cart::where('user_id', Auth::id())->first();

        // If a cart exists, fetch its items
        if ($cart) {
            $this->cartItems = CartItem::where('cartId', $cart->cartId)->get();
        } else {
            $this->cartItems = collect();
        }

        $this->calculateTotal(); // Calculate the total price at the start
    }

    protected $listeners = ['quantityUpdated' => 'updateTotalPrice']; // Listen for updates

    public function updateTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice; // Update the total price
    }

    public function calculateTotal()
    {
        $this->totalPrice = $this->cartItems->sum(function ($item) {
            return $item->price * $item->quantity; // Sum prices of all items in the cart
        });
    }

    public function render()
    {
        return view('livewire.order-total');
    }
}
