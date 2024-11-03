@extends('layouts.staff')

@section('title', 'Staff Dashboard')

@section('content')
    <div class="welcome-section" style="background-image: url('{{ asset('images/staffbg.png') }}');">
        <div class="hero-background">
            <div class="hero-text text-center">
                @php
                    $user = Auth::user(); // Get the currently authenticated user
                @endphp
                <br><br><br><br><br><br><br><br><br><br><br><br>
                
                <h2>Welcome Back, {{ $user->firstName }}!</h2>
                <h6>We’re excited to have you here. Use the tools below to manage food stock and stay updated!</h6>
            </div>
        </div>
    </div>

   <!-- Stock Alert Section -->
<div class="container mt-4" style="background-color: #FFF3CD">
    <h4 class="text-center mb-4">Stock Alerts <i class="fas fa-exclamation-triangle text-warning"></i></h4>
    <div class="row justify-content-center"> <!-- Center the cards -->
        <!-- Low Stock Card -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow border-warning">
                <div class="card-body">
                    <h2 class="card-title text-warning">Low Stock ({{ $lowStockItems->count() }})</h2>
                    <p><strong>{{ $lowStockItems->count() }}</strong> items low on stock</p>
                    <button class="btn btn-link" data-toggle="collapse" data-target="#lowStockDetails" aria-expanded="false" aria-controls="lowStockDetails">
                        See More
                    </button>
                    <div class="collapse" id="lowStockDetails">
                        <ul class="list-unstyled mt-3">
                            @foreach($lowStockItems as $item)
                                <li class="mb-2">
                                    <h6 class="card-subtitle mb-1 text-muted">
                                        <strong>{{ $item->menuName }}</strong> - <span class="text-warning">{{ $item->quantityStock }}</span> left
                                    </h6>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Out of Stock Card -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow border-danger">
                <div class="card-body">
                    <h2 class="card-title text-danger">Out of Stock ({{ $outOfStockItems->count() }})</h2>
                    <p><strong>{{ $outOfStockItems->count() }}</strong> items out of stock</p>
                    <button class="btn btn-link" data-toggle="collapse" data-target="#outOfStockDetails" aria-expanded="false" aria-controls="outOfStockDetails">
                        See More
                    </button>
                    <div class="collapse" id="outOfStockDetails">
                        <ul class="list-unstyled mt-3">
                            @foreach($outOfStockItems as $item)
                                <li class="mb-2">
                                    <h6 class="card-subtitle mb-1 text-muted">
                                        <strong>{{ $item->menuName }}</strong>- <span class="text-warning">{{ $item->quantityStock }}</span> left
                                    </h6>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>






<!-- Most Bad Rated Food Section -->
<div class="container mt-4" style="background-color: #F8D7DA">
    <h4 class="text-center">Most Bad Rated Food <i class="fas fa-thumbs-down text-danger"></i></h4>
    <div class="row">
        @foreach ($badRatedItems as $item)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow border-danger">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <!-- Left Section (Text Content) -->
                        <div class="text-left">
                            <h5 class="card-title text-danger">{{ $item->menuName }}</h5>
                            <p class="card-text">
                                Ratings: <br>
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $item->avg_rating)
                                        <i class="fas fa-star text-warning"></i> <!-- Full star -->
                                    @elseif ($i - $item->avg_rating < 1)
                                        <i class="fas fa-star-half-alt text-warning"></i> <!-- Half star -->
                                    @else
                                        <i class="far fa-star text-warning"></i> <!-- Empty star -->
                                    @endif
                                @endfor
                            </p>
                            <!-- Expandable Section for Bad Reviews -->
                            <a data-bs-toggle="collapse" href="#collapseFeedback{{ $item->menuId }}" role="button" aria-expanded="false" aria-controls="collapseFeedback{{ $item->menuId }}">
                                Show Bad Reviews
                            </a>
                            <div class="collapse mt-2" id="collapseFeedback{{ $item->menuId }}">
                                <ul class="list-group list-group-flush">
                                @foreach ($item->badFeedbacks as $feedback)
                                    <li class="list-group-item">
                                        <strong>Rating:</strong> <br>
                                        <!-- Small stars display -->
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $feedback->rating)
                                                <i class="fas fa-star text-warning" style="font-size: 0.75rem;"></i> <!-- Full star -->
                                            @elseif ($i - $feedback->rating < 1)
                                                <i class="fas fa-star-half-alt text-warning" style="font-size: 0.75rem;"></i> <!-- Half star -->
                                            @else
                                                <i class="far fa-star text-warning" style="font-size: 0.75rem;"></i> <!-- Empty star -->
                                            @endif
                                        @endfor
                                        <br>
                                        <strong>Comment:</strong> {{ $feedback->comments }}
                                    </li>
                                @endforeach
                                </ul>
                            </div>
                        </div>
                        <!-- Right Section (Image) -->
                        <div class="image-right">
                            <img src="{{ asset('storage/' . $item->menuImage) }}" alt="Food Image" style="height: 100px; width: 100px; object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>


<!-- Frequently Ordered Items Section -->
<div class="container mt-4" style="background-color: #D1ECF1">
    <h4 class="text-center">Frequently Ordered Items <i class="fas fa-bell text-primary"></i></h4>
    <div class="row">
        @foreach ($frequentlyOrderedItems as $item)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow border-primary">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="text-left">
                            <h5 class="card-title text-primary">{{ $item->menuName }}</h5>
                            <p class="card-text">Times Ordered: {{ $item->order_count }}</p>
                            <p class="card-text">Price: RM{{ number_format($item->price, 2) }}</p> <!-- Make sure to get price from the item if you have that in your model -->
                        
                        </div>
                        <div class="image-right">
                            <img src="{{ asset('storage/' . $item->menuImage) }}" alt="Food Image" style="height: 100px; width: 100px; object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<br>



@endsection
