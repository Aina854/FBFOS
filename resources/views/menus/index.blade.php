@extends('layouts.staff')

@section('title', 'Menu Management - Farizs Bistro')

@section('content')
<div class="container">
    <h2 class="mb-4">Menu Management</h2>

   <!-- Section for Stock Alerts -->
<div class="alert-container mb-4" style="background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
    <h4 style="margin-bottom: 10px; font-weight: bold; color: #721c24;">Stock Alerts</h4>
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div style="flex: 1;">
            <span style="font-weight: bold; color: #856404;">Low Stock:</span>
            <span class="badge" style="background-color: #ffc107; color: #212529; margin-left: 5px; padding: 5px 10px; border-radius: 5px; font-size: 1.1em;">{{ $lowStockCount }}</span>
        </div>
        <div style="flex: 1; text-align: right;">
            <span style="font-weight: bold; color: #721c24;">Out of Stock:</span>
            <span class="badge" style="background-color: #dc3545; color: #fff; margin-left: 5px; padding: 5px 10px; border-radius: 5px; font-size: 1.1em;">{{ $outOfStockCount }}</span>
        </div>
    </div>
</div>

    
    <!-- Form for Searching Menu -->
<div class="container text-left mb-4">
    <form action="{{ route('menus.index') }}" method="GET" class="d-flex align-items-center">
        <input type="text" class="form-control search-bar mr-2" name="keyword" placeholder="Search by name" style="max-width: 200px;"/>
        <input type="submit" class="btn btn-primary" value="Search"/>
    </form>

    <a href="{{ route('menus.create') }}" class="btn btn-success">Add New Menu</a>
</div>


    <!-- Display alert message if present -->
    @if(session('message'))
        <div class="alert alert-info mt-4" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <!-- Menu Table -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Menu Image</th>
                    <th>Menu Name</th>
                    <th>Menu Category</th>
                    <th>Price</th>
                    <th>Quantity in Stock</th> <!-- Updated Header -->
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($menus as $index => $menu)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>
            @if($menu->menuImage)
                <img src="{{ asset('storage/' . $menu->menuImage) }}" class="card-img-top" alt="Food Image" style="height: 100px; width: 100px;">
            @else
                No Image
            @endif
        </td>
        <td>{{ $menu->menuName }}</td>
        <td>{{ $menu->menuCategory }}</td>
        <td class="item-price">RM{{ number_format($menu->price, 2) }}</td>
        <td>
    @if($menu->quantityStock <= 0)
        <span class="badge" style="background-color: #dc3545; color: #fff; padding: 5px 10px; border-radius: 5px;">Out of Stock</span>
    @elseif($menu->quantityStock <= 10)
        <span class="badge" style="background-color: #ffc107; color: #212529; padding: 5px 10px; border-radius: 5px;">Low Stock ({{ $menu->quantityStock }})</span>
    @else
        <span class="badge" style="background-color: #28a745; color: #fff; padding: 5px 10px; border-radius: 5px;">In Stock ({{ $menu->quantityStock }})</span>
    @endif
</td>

        <td>{{ $menu->description }}</td>
        <td>
            <a href="{{ route('menus.edit', $menu->menuId) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>

            <a href="#" class="btn btn-danger btn-sm"
                onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this menu item?')) { document.getElementById('delete-form-{{ $menu->menuId }}').submit(); }">
                <i class="fas fa-trash-alt"></i> Delete
            </a>
            <form id="delete-form-{{ $menu->menuId }}" action="{{ route('menus.destroy', $menu->menuId) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center">No menus found.</td>
    </tr>
@endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Additional JS if needed
</script>
@endpush
