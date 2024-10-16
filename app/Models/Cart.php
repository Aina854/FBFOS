<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'carts';

    // Specify the fillable attributes
    protected $fillable = [
        'user_id',
    ];

    // Specify the primary key column
    protected $primaryKey = 'cartId';

    // Define the primary key type
    protected $keyType = 'int'; // Change this if your primary key is not an integer

    // Disable auto-increment if it's not auto-incremented
    public $incrementing = true;

    // Define the relationship with the User model
    public function user()
{
    return $this->belongsTo(User::class, 'user_id'); // Adjust if 'user_id' is not the correct foreign key column
}


    public function items()
{
    return $this->hasMany(CartItem::class, 'cartId', 'cartId');
}

public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'cartId');
    }

}
