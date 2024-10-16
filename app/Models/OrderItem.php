<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'orderitems';

    // Primary key for the table
    protected $primaryKey = 'orderItemId';

    // Disable auto-increment if not using Laravel's default 'id' primary key
    public $incrementing = true;

    // Specify the data type of the primary key
    protected $keyType = 'int';

    // Define the fillable fields
    protected $fillable = [
        'orderId', // Foreign key to orders table
        'menuId',  // Foreign key to menu table
        'quantity',
        'price',
        'totalPrice',
    ];

    // Define the relationship with the order (belongsTo relationship)
    public function order()
    {
        return $this->belongsTo(Order::class, 'orderId', 'orderId');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'orderItemId');
    }


    public function feedbacks()
    {
        return $this->hasMany(Feedback::class); // This establishes the one-to-many relationship
    }
    
    // Define the relationship with the menu (belongsTo relationship)
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menuId', 'menuId');
    }

    public static function getOrderItemsWithoutFeedback($orderId)
    {
        return self::where('orderId', $orderId)
            ->leftJoin('feedbacks', 'orderitems.orderItemId', '=', 'feedbacks.orderItemId')
            ->select('orderitems.*') // Select the fields you need
            ->whereNull('feedbacks.orderItemId') // Ensure no feedback exists
            ->get();
    }
}
