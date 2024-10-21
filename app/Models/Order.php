<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Specify the table name (if it is not the plural of the model name)
    protected $table = 'orders';

    // Primary key for the table
    protected $primaryKey = 'orderId';

    // Disable auto-increment if not using Laravel's default 'id' primary key
    public $incrementing = true;

    // Specify the data type of the primary key
    protected $keyType = 'int';

    // Define the fillable fields
    protected $fillable = [
        'id', // Foreign key to users table
        'OrderStatus',
        'OrderDate',
        'staffFirstName',
        'staffLastName',
        'feedbackSubmitted',
    ];

    // Define the relationship with the user (belongsTo relationship)
    public function user()
    {
        return $this->belongsTo(User::class, 'id'); // 'id' is the foreign key in the orders table
    }

    // Define the relationship with order items (hasMany relationship)
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'orderId', 'orderId');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'orderId'); // Adjust the foreign key as necessary
    }

    // In Order.php model
    public function payment()
    {
        return $this->hasOne(Payment::class, 'orderId'); // Use the correct foreign key if it's different
    }

}
