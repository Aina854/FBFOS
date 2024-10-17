@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
    <div class="container">
        <div class="edit-form">
            <h1 class="center-heading">Edit Profile</h1>
            <form action="{{ route('profile.update', $user->id) }}" method="POST">
                @csrf

                <div class="form-row">
                    <div class="form-column">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <td><label for="name">Name:</label></td>
                                    <td><input type="text" id="name" name="name" class="form-control" value="{{ $user->name }}" required></td>
                                </tr>

                                <tr>
                                    <td><label for="firstName">First Name:</label></td>
                                    <td><input type="text" id="firstName" name="firstName" class="form-control" value="{{ $user->firstName }}"></td>
                                </tr>

                                <tr>
                                    <td><label for="lastName">Last Name:</label></td>
                                    <td><input type="text" id="lastName" name="lastName" class="form-control" value="{{ $user->lastName }}"></td>
                                </tr>

                                <tr>
                                    <td><label for="email">Email:</label></td>
                                    <td><input type="email" id="email" name="email" class="form-control" value="{{ $user->email }}" required></td>
                                </tr>

                                <tr>
                                    <td><label for="age">Age:</label></td>
                                    <td><input type="number" id="age" name="age" class="form-control" value="{{ $user->age }}"></td>
                                </tr>

                                <tr>
                                    <td><label for="gender">Gender:</label></td>
                                    <td>
                                        <select id="gender" name="gender" class="form-control">
                                            <option value="">Select Gender</option>
                                            <option value="Male" {{ $user->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ $user->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                            <option value="Other" {{ $user->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td><label for="phoneNo">Phone Number:</label></td>
                                    <td><input type="text" id="phoneNo" name="phoneNo" class="form-control" value="{{ $user->phoneNo }}"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-column">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <td><label for="address1">Address Line 1:</label></td>
                                    <td><input type="text" id="address1" name="address1" class="form-control" value="{{ $user->address1 }}"></td>
                                </tr>

                                <tr>
                                    <td><label for="address2">Address Line 2:</label></td>
                                    <td><input type="text" id="address2" name="address2" class="form-control" value="{{ $user->address2 }}"></td>
                                </tr>

                                <tr>
                                    <td><label for="postcode">Postcode:</label></td>
                                    <td><input type="text" id="postcode" name="postcode" class="form-control" value="{{ $user->postcode }}"></td>
                                </tr>

                                <tr>
                                    <td><label for="city">City:</label></td>
                                    <td><input type="text" id="city" name="city" class="form-control" value="{{ $user->city }}"></td>
                                </tr>

                                <tr>
                                    <td><label for="state">State:</label></td>
                                    <td><input type="text" id="state" name="state" class="form-control" value="{{ $user->state }}"></td>
                                </tr>

                                <tr>
                                    <td><label for="category">Category:</label></td>
                                    <td>
                                        <select id="category" name="category" class="form-control" required>
                                            <option value="Customer" {{ $user->category == 'Customer' ? 'selected' : '' }}>Customer</option>
                                            <option value="Staff" {{ $user->category == 'Staff' ? 'selected' : '' }}>Staff</option>
                                            <option value="Admin" {{ $user->category == 'Admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td><label for="password">Password:</label></td>
                                    <td class="password-wrapper">
                                        <input type="password" id="password" name="password" class="form-control">
                                        <!--<i class="fa fa-eye"></i>
                                        <i class="fa fa-eye-slash"></i>-->
                                    </td>
                                </tr>

                                <tr>
                                    <td><label for="password_confirmation">Confirm Password:</label></td>
                                    <td><input type="password" id="password_confirmation" name="password_confirmation" class="form-control"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-buttons">
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
