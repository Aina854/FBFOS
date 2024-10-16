@extends('layouts.staff')

@section('content')
    <div class="container">
        <h1>Add New Menu Item</h1>
        <form action="{{ route('menus.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="menuImage">Menu Image</label>
                <input type="file" name="menuImage" id="menuImage" class="form-control">
            </div>
            <div class="form-group">
                <label for="menuName">Menu Name</label>
                <input type="text" name="menuName" id="menuName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="menuCategory">Menu Category</label>
                <select class="form-control" id="menuCategory" name="menuCategory" required>
                    <option value="" disabled selected>Select Menu Category</option>
                    <option value="Main Course">Main Course</option>
                    <option value="Western">Western</option>
                    <option value="Drinks">Drinks</option>
                    <option value="Side Order">Side Order</option>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" name="price" id="price" class="form-control" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="availability">Availability</label>
                <label class="switch">
                    <input type="checkbox" id="availabilityToggle" name="availabilityToggle">
                    <span class="slider round"></span>
                </label>
                <input type="hidden" id="availability" name="availability" value="No">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>


@endsection
