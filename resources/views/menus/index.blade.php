@extends('layouts.staff')

@section('title', 'Menu Management - Farizs Bistro')

@section('content')
<div class="container">
    <h2 class="mb-4">Menu Management</h2>
    
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
                    <th>Availability</th>
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
        <td>{{ $menu->availability }}</td>
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
