<!-- resources/views/staff-dashboard.blade.php -->
@extends('layouts.staff')

@section('title', 'Staff Dashboard')

@section('content')
    <div id="announcementCarousel" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
            <img class="d-block w-100" src="{{ asset('images/farizsbistro.jpg') }}" alt="Meet Our Team">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Meet Our Team</h5>
                    <p>Get to know the amazing staff that makes everything possible!</p>
                </div>
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="{{ asset('image/stafffarizs.jpg') }}" alt="Our Staff at Work">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Our Staff at Work</h5>
                    <p>See our team in action as they work hard to provide great service.</p>
                </div>
            </div>
            <!-- Add more items as needed -->
        </div>
        <a class="carousel-control-prev" href="#announcementCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#announcementCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <div class="welcome-section">
        <div class="hero-background">
            <div class="hero-text">
                <h2>Welcome Back, {{ Session::get('staff_name') }}!</h2>
                <p>We're excited to have you here. Explore the features below to manage tasks and stay updated!</p>
            </div>
        </div>
    </div>

    <!-- Regular Tasks Section -->
    <div class="container mt-4">
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading">Regular Tasks</h4>
            <ul>
                <li>Check and update inventory levels.</li>
                <li>Prepare for the upcoming meal services.</li>
                <li>Ensure all equipment and workstations are clean and ready.</li>
                <li>Review and restock supplies as needed.</li>
                <li>Maintain high standards of customer service.</li>
            </ul>
        </div>
    </div>

    <!-- Quick Links Section -->
    <div class="container mt-4">
        <h3>Quick Links</h3>
        <div class="row">
            <div class="col-md-4 mb-4">
                <a href="{{ route('viewOrders') }}" class="btn btn-primary btn-block">View Orders</a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="{{ route('viewPayments') }}" class="btn btn-success btn-block">View Payments</a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="{{ route('listMenu') }}" class="btn btn-warning btn-block">Update Menu</a>
            </div>
        </div>
    </div>
@endsection
