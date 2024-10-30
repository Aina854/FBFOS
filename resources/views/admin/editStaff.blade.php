@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Edit Staff Member</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('updateStaff', $staff->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $staff->name }}" placeholder="Enter full name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $staff->email }}" placeholder="Enter email address" required>
                </div>

                <div class="form-group">
                    <label for="phoneNo">Phone Number</label>
                    <input type="text" class="form-control" id="phoneNo" name="phoneNo" value="{{ $staff->phoneNo }}" placeholder="Enter phone number" required>
                </div>

                <!-- Add more fields as needed -->
                
                <button type="submit" class="btn btn-success mt-3">Update Staff</button>
                <a href="{{ route('admin.staffList') }}" class="btn btn-secondary mt-3">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
