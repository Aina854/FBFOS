<!-- resources/views/admin-dashboard.blade.php -->
@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div class="welcome-section">
    <div class="hero-background" style="position: relative; height: 400px; overflow: hidden;">
        <!-- Background Image -->
        <img src="{{ asset('images/adminbg.png') }}" alt="Admin Background" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1;">
        
        <!-- Text Content -->
        <div class="hero-text" style="position: relative; color: white; z-index: 1; text-align: center;">
            <br><br><br><br><br><br><br><br><br><br>
            @php
                    $user = Auth::user(); // Get the currently authenticated user
            @endphp
            <h2>Welcome Back, {{ $user->firstName }}!</h2>
            <p>Manage and monitor the system with the tools provided below.</p>
        </div>
    </div>
</div>




    <!-- Key Statistics Section -->
<div class="container mt-4">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-light text-dark text-center" style="background-color: #f8f9fa; border: 1px solid #28a745;">
                <div class="card-body">
                    <i class="fas fa-dollar-sign fa-2x mb-2" style="color: #28a745;"></i>
                    <h5 class="card-title">Total Payments</h5>
                    <p class="card-text">RM{{ number_format($totalPayments, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-light text-dark text-center" style="background-color: #f8f9fa; border: 1px solid #17a2b8;">
                <div class="card-body">
                    <i class="fas fa-shopping-cart fa-2x mb-2" style="color: #17a2b8;"></i>
                    <h5 class="card-title">Total Orders</h5>
                    <p class="card-text">{{ $totalOrders }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-light text-dark text-center" style="background-color: #f8f9fa; border: 1px solid #ffc107;">
                <div class="card-body">
                    <i class="fas fa-users fa-2x mb-2" style="color: #ffc107;"></i>
                    <h5 class="card-title">Total Customers</h5>
                    <p class="card-text">{{ $totalCustomers }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-light text-dark text-center" style="background-color: #f8f9fa; border: 1px solid #dc3545;">
                <div class="card-body">
                    <i class="fas fa-user-tie fa-2x mb-2" style="color: #dc3545;"></i>
                    <h5 class="card-title">Total Staff</h5>
                    <p class="card-text">{{ $totalStaff }}</p>
                </div>
            </div>
        </div>
    </div>
</div>


    <!-- Recent Activity Section -->
    <div class="container mt-4">
        @livewire('recent-activity')
    </div>

 <!-- Top Selling Food Section -->
<div class="container mt-4">
    <h4 class="text-center" style="font-size: 1.8em; font-weight: bold; color: #333; margin-bottom: 30px;">Top Selling Food <i class="fas fa-thumbs-up"></i></h4>
    <div class="top-selling-container">
    <div class="left-section">
    <div class="food-items">
        @php
            $highestSelling = collect($topFoodsDetails)->max('total_sales');
        @endphp
        @foreach($topFoodsDetails as $food)
            <div class="food-item" >
                <div class="sales-bar" style="--bar-width: {{ $food['total_sales'] * 100 }}px;"> <!-- Adjust multiplier as needed -->
                    <!--<span class="sales-value" >{{ $food['total_sales'] }}</span> -->
                    
                    <span class="sales-count" style="color:black;">{{ $food['total_sales'] }} solds</span> <!-- Moved here -->   
                    <span class="food-name" style="font-size: 0.7em; color: #555; display: block; margin-top: 4px;">{{ $food['name'] }}</span>
                </div>
                <div class="food-info">

                <!-- Show crown if this item has the highest sales -->
                    @if ($food['total_sales'] === $highestSelling)
                    <i class="fas fa-crown crown-icon" style="display: block; color: gold; font-size: 26px;"></i>
                    @endif
                    <img src="{{ asset('storage/' . $food['menuImage']) }}" alt="{{ $food['name'] }} image" class="food-image">
                </div>
                
            </div>
            <br>
        @endforeach
    </div>
</div>

<div class="right-section">
    <div class="top-selling-food">
        <h2 style="text-align: center; font-size: 24px; color: #333;">Very Popular <i class="fas fa-fire" style="color: orange; font-size: 30px; animation: shine 1.5s infinite alternate;"></i>
        </h2>
        @if(count($topFoodsDetails) > 0)
            <div class="food-image-container" style="text-align: center; margin-top: 20px;">
                <img src="{{ asset('storage/' . $topFoodsDetails[0]['menuImage']) }}" alt="{{ $topFoodsDetails[0]['name'] }} image" style="width: 80%; max-width: 600px; border-radius: 10px;">
            </div>
            <div class="food-info" style="text-align: center; margin-top: 10px;">
                <h3 style="font-size: 22px; color: #333;">{{ $topFoodsDetails[0]['name'] }}</h3>
                <p style="font-size: 16px; color: #666;">{{ $topFoodsDetails[0]['total_sales'] }} solds!</p>
            </div>
        @else
            <p style="text-align: center; color: #666;">No top-selling foods found.</p>
        @endif
    </div>
</div>


    </div>
</div>

<!-- Rating Food Section -->
<div class="container mt-4">
    <h4 class="text-center" style="font-size: 1.8em; font-weight: bold; color: #333; margin-bottom: 30px;">Food Ratings <i class="fas fa-heart"></i></h4>

    <div class="row">
        <!-- Highest Rating Food Section -->
        <div class="col-md-6 mb-3">
            <div class="card" style="border: 2px solid #28a745; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                <div class="card-body text-center">
                    <h5 class="card-title" style="color: #28a745; font-size: 1.4em; font-weight: bold; letter-spacing: 1px;">Highest Rated Food <i class="fas fa-thumbs-up"></i></h5>
                    <img src="{{ asset('storage/' . $highestRatedFood->menuImage) }}" alt="Highest Rated Food" class="img-fluid mb-3" style="width: 160px; height: 160px; object-fit: cover; border-radius: 10px;">
                    <p class="card-text" style="font-weight: bold; font-size: 1.2em; color: #333;">{{ $highestRatedFood->menuName }}</p>
                    
                    <!-- Star Rating -->
                    <p class="card-text" style="color: #ffc107; font-size: 1.3em;">
                        @php
                            $rating = round($highestRatedFood->average_rating); // Round the rating to an integer value
                        @endphp
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $rating)
                                <span class="fa fa-star" style="color: #ffc107;"></span> <!-- Filled star -->
                            @else
                                <span class="fa fa-star" style="color: #e4e5e9;"></span> <!-- Empty star -->
                            @endif
                        @endfor
                    </p>
                    <p class="card-text" style="color: #666; font-size: 1.1em; margin-top: 10px;">{{ $highestRatedFood->total_ratings }} rating(s)</p>
                </div>
            </div>
        </div>

        <!-- Lowest Rating Food Section -->
        <div class="col-md-6 mb-3">
            <div class="card" style="border: 2px solid #dc3545; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                <div class="card-body text-center">
                    <h5 class="card-title" style="color: #dc3545; font-size: 1.4em; font-weight: bold; letter-spacing: 1px;">Lowest Rated Food <i class="fas fa-thumbs-down"></i></h5>
                    <img src="{{ asset('storage/' . $lowestRatedFood->menuImage) }}" alt="Lowest Rated Food" class="img-fluid mb-3" style="width: 160px; height: 160px; object-fit: cover; border-radius: 10px;">
                    <p class="card-text" style="font-weight: bold; font-size: 1.2em; color: #333;">{{ $lowestRatedFood->menuName }}</p>
                    
                    <!-- Star Rating -->
                    <p class="card-text" style="color: #ffc107; font-size: 1.3em;">
                        @php
                            $rating = round($lowestRatedFood->average_rating); // Round the rating to an integer value
                        @endphp
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $rating)
                                <span class="fa fa-star" style="color: #ffc107;"></span> <!-- Filled star -->
                            @else
                                <span class="fa fa-star" style="color: #e4e5e9;"></span> <!-- Empty star -->
                            @endif
                        @endfor
                    </p>
                    <p class="card-text" style="color: #666; font-size: 1.1em; margin-top: 10px;">{{ $lowestRatedFood->total_ratings }} rating(s)</p>
                </div>
            </div>
        </div>
    </div>
</div>


<br><br><br>

@endsection
