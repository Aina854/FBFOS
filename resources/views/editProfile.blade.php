@extends('layouts.customer')

@section('title', 'Edit Profile')

@section('content')
    <div class="container">
        <div class="edit-form mt-4">
            <h1 class="center-heading text-center">Edit Profile</h1>
            <form action="{{ route('profile.update', $user->id) }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="user-info">
                            
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" id="name" name="name" class="form-control" value="{{ $user->name }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="firstName">First Name:</label>
                                <input type="text" id="firstName" name="firstName" class="form-control" value="{{ $user->firstName }}">
                            </div>
                            <div class="form-group">
                                <label for="lastName">Last Name:</label>
                                <input type="text" id="lastName" name="lastName" class="form-control" value="{{ $user->lastName }}">
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" class="form-control" value="{{ $user->email }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="age">Age:</label>
                                <input type="number" id="age" name="age" class="form-control" value="{{ $user->age }}">
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender:</label>
                                <select id="gender" name="gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ $user->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ $user->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ $user->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="phoneNo">Phone Number:</label>
                                <input type="text" id="phoneNo" name="phoneNo" class="form-control" value="{{ $user->phoneNo }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="address-info">
                            
                            <div class="form-group">
                                <label for="address1">Address Line 1:</label>
                                <input type="text" id="address1" name="address1" class="form-control" value="{{ $user->address1 }}">
                            </div>
                            <div class="form-group">
                                <label for="address2">Address Line 2:</label>
                                <input type="text" id="address2" name="address2" class="form-control" value="{{ $user->address2 }}">
                            </div>
                            <div class="form-group">
                                <label for="postcode">Postcode:</label>
                                <input type="text" id="postcode" name="postcode" class="form-control" value="{{ $user->postcode }}">
                            </div>
                            <div class="form-group">
                                <label for="city">City:</label>
                                <input type="text" id="city" name="city" class="form-control" value="{{ $user->city }}">
                            </div>
                            <div class="form-group">
                                <label for="state">State:</label>
                                <input type="text" id="state" name="state" class="form-control" value="{{ $user->state }}">
                            </div>
                            <!-- Hidden input to set category as 'customer' -->
                            <input type="hidden" name="category" value="Customer">
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" id="password" name="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password:</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-buttons text-center mt-4">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.querySelector('.fa-eye');
            const eyeSlashIcon = document.querySelector('.fa-eye-slash');

            eyeIcon.addEventListener('click', function () {
                passwordField.type = 'text';
                eyeIcon.classList.add('hide');
                eyeSlashIcon.classList.remove('hide');
            });

            eyeSlashIcon.addEventListener('click', function () {
                passwordField.type = 'password';
                eyeSlashIcon.classList.add('hide');
                eyeIcon.classList.remove('hide');
            });
        });
    </script>
@endsection
