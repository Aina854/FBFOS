<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * These attributes can be assigned via mass assignment.
     * @var array<int, string>
     */
    protected $fillable = [
        'firstName',    // User's first name
        'lastName',     // User's last name
        'age',          // User's age
        'gender',       // User's gender
        'email',        // User's email address
        'phoneNo',      // User's phone number
        'address1',     // User's primary address
        'address2',     // User's secondary address (optional)
        'postcode',     // User's postal code
        'city',         // User's city
        'state',        // User's state
        'name',         // Username for login
        'password',     // User's hashed password
        'category',     // User's role category (e.g., Customer, Staff, Admin)
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * These attributes will be hidden when the model is converted to an array or JSON.
     * @var array<int, string>
     */
    protected $hidden = [
        'password',       // User's password (hidden for security reasons)
        'remember_token', // Token used for "remember me" functionality
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * Casting attributes allows for easier handling of certain data types.
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Cast to datetime for email verification timestamp
            'password' => 'hashed',           // Ensure the password is hashed
        ];
    }

    // User.php
    public function cart()
    {
        return $this->hasOne(Cart::class, 'user_id');
    }

    // User.php
    public function orders()
    {   
    return $this->hasMany(Order::class, 'id'); // Ensure 'id' matches the foreign key in the orders table
    }

// app/Models/User.php
public function feedbacks() {
    return $this->hasMany(Feedback::class);
}

    // In User.php model

}
