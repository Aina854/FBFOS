<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Specify the table name if it doesn't follow Laravel's naming convention
    protected $table = 'payments';

    // Define the primary key
    protected $primaryKey = 'paymentId';

    // Allow mass assignment for these fields
    protected $fillable = [
        'id', // Foreign key or user id
        'orderId', // Foreign key to the orders table
        'paymentAmount',
        'paymentDate',
        'receiptImage',
        'paymentStatus',
        'responseDate',
        'comments',
        'paymentMethod',
        'staffFirstName',
        'staffLastName',
        'stripe_session_id',
        'attempts',
        'last_attempt_at',
    ];

    // Set the timestamps to true to enable created_at and updated_at
    public $timestamps = true;

    // Optionally define relationships with other models
    public function order()
    {
        return $this->belongsTo(Order::class, 'orderId', 'orderId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
}
