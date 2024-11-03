<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    // The table associated with the model.
    protected $table = 'menus';

    // The primary key associated with the table.
    protected $primaryKey = 'menuId';

    // Indicates if the IDs are auto-incrementing.
    public $incrementing = true; // Set to true for auto-incrementing integers

    // The type of the primary key ID.
    protected $keyType = 'int'; // Use 'int' for auto-incrementing integers

    // The attributes that are mass assignable.
    protected $fillable = [
        'menuImage',
        'menuName',
        'menuCategory',
        'price',
        'quantityStock', // Add quantityStock here
        'description',
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'menuId');
    }

    public function badFeedbacks()
{
    return $this->hasManyThrough(Feedback::class, OrderItem::class, 'menuId', 'orderitemId')
                ->where('rating', '<=', 2); // Filter for ratings 1-2
}

}
