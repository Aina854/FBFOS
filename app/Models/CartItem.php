<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    // The table associated with the model.
    protected $table = 'cartitems';

    // The primary key associated with the table.
    protected $primaryKey = 'cartItemId';

    // The attributes that are mass assignable.
    protected $fillable = [
        'menuId',
        'quantity',
        'price',
        'totalPrice',
        'createdAt',
        'cartId',
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'createdAt' => 'date',
        'price' => 'decimal:2',
        'totalPrice' => 'decimal:2',
    ];

    /**
     * Get the cart that owns the cart item.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cartId', 'cartId');
    }

    /**
     * Get the menu associated with the cart item.
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menuId', 'menuId');
    }

    
}
