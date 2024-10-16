<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    // Specify the table associated with the model (optional if following Laravel's naming convention)
    protected $table = 'feedbacks';

    // Specify the primary key if it's not 'id'
    protected $primaryKey = 'feedbackId';

    // Disable incrementing if your primary key is not an auto-incrementing integer
    public $incrementing = true;

    // Specify the fields that can be mass assigned
    protected $fillable = [
        'orderItemId',
        'rating',
        'comments',
        'id', // Assuming this is the user ID, which is a foreign key
        'commentsTime',
        'staffResponse',
        'responseTimestamp',
        'anonymous',
    ];

    // Define relationships if needed
    public function orderItems()
    {
        return $this->belongsTo(OrderItem::class, 'orderItemId', 'orderItemId');
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'orderItemId', 'orderItemId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }


}
