<?php

use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('orders', function ($user) {
    return true; // Adjust this logic based on your requirements
});