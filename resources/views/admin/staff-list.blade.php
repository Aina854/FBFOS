@extends('layouts.admin')

@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4">Staff Members</h1>

        <!-- Alert for Success Message -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- New Staff Button -->
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('addStaff') }}" class="btn btn-success">+ Add New Staff</a>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffMembers as $staff)
                            <tr>
                                <td>{{ $staff->name }}</td>
                                <td>{{ $staff->email }}</td>
                                <td>{{ $staff->phoneNo }}</td>
                                <input type="hidden" name="id" value="{{ $staff->id }}">
                                <td>
                                    <a href="{{ route('admin.editStaff', $staff->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                      <!-- Delete Button as Form to Support DELETE Method -->
                                      <form action="{{ route('deleteStaff', $staff->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this staff member?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
