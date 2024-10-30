<!-- resources/views/admin-dashboard.blade.php -->
@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="hero-background">
            <div class="hero-text">
                <h2>Welcome Back, {{ Session::get('admin_name') }}!</h2>
                <p>Manage and monitor the system with the tools provided below.</p>
            </div>
        </div>
    </div>

    <!-- Key Statistics Section -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card bg-light text-dark text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Sales</h5>
                        <p class="card-text">$XX,XXX</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card bg-light text-dark text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Customers</h5>
                        <p class="card-text">totalCustomers</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card bg-light text-dark text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Staff</h5>
                        <p class="card-text">totalStaff</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Tasks Section -->
    <div class="container mt-4">
        <h3>Admin Tasks</h3>
        <div class="row">
            <div class="col-md-4 mb-4">
                <a href="{{ route('monitorSales') }}" class="btn btn-primary btn-block">Monitor Sales</a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="{{ route('admin.customerList') }}" class="btn btn-info btn-block">View & Delete Customers</a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="{{ route('admin.staffList') }}" class="btn btn-warning btn-block">Manage Staff</a>
            </div>
        </div>
    </div>

    <!-- Reports Section -->
    <div class="container mt-4">
        <h3>Reports</h3>
        <div class="row">
            <div class="col-md-6 mb-4">
                <a href="{{ route('salesReport') }}" class="btn btn-secondary btn-block">View Sales Report</a>
            </div>
            <div class="col-md-6 mb-4">
                <a href="{{ route('customerReport') }}" class="btn btn-secondary btn-block">View Customer Report</a>
            </div>
        </div>
    </div>
@endsection
