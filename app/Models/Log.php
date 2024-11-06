<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    // Specify the table name (optional if it follows naming conventions)
    // Specify the table name if it's not the plural form of the model name
    protected $table = 'logs';

    // Specify the primary key if it's not 'id'
    protected $primaryKey = 'logId';

    // If your primary key is not auto-incrementing, set this to false
    public $incrementing = false; // or true depending on your schema

    // Define any mass-assignable attributes
    protected $fillable = ['user_id', 'action', 'details'];

    /**
     * Relationship to the User model, if each log belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
