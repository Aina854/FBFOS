<!-- resources/views/menus/edit.blade.php -->
@extends('layouts.staff')

@section('content')
    <div class="container">
        <h1>Edit Menu Item</h1>
        <form action="{{ route('menus.update', $menu->menuId) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="menuImage">Menu Image</label>
                @if($menu->menuImage)
                    <img src="{{ asset('storage/' . $menu->menuImage) }}" alt="{{ $menu->menuName }}" width="100">
                @endif
                <input type="file" name="menuImage" id="menuImage" class="form-control">
            </div>
            <div class="form-group">
                <label for="menuName">Menu Name</label>
                <input type="text" name="menuName" id="menuName" class="form-control" value="{{ $menu->menuName }}" required>
            </div>
            <div class="form-group">
                <label for="menuCategory">Menu Category</label>
                <select class="form-control" id="menuCategory" name="menuCategory" required>
                    <option value="" disabled>Select Menu Category</option>
                    <option value="Main Course" {{ $menu->menuCategory == 'Main Course' ? 'selected' : '' }}>Main Course</option>
                    <option value="Western" {{ $menu->menuCategory == 'Western' ? 'selected' : '' }}>Western</option>
                    <option value="Drinks" {{ $menu->menuCategory == 'Drinks' ? 'selected' : '' }}>Drinks</option>
                    <option value="Side Order" {{ $menu->menuCategory == 'Side Order' ? 'selected' : '' }}>Side Order</option>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" name="price" id="price" class="form-control" step="0.01" value="{{ $menu->price }}" required>
            </div>
            <div class="form-group">
                <label for="quantityStock">Quantity in Stock</label>
                <input type="number" name="quantityStock" id="quantityStock" class="form-control" min="0" value="{{ $menu->quantityStock }}" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control">{{ $menu->description }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection
