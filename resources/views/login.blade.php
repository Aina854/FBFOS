@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="container">
        <div class="login-form">
            <h1>Login</h1>

            <!-- Display success message if it exists -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Display error message if it exists -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                <table class="form-table">
                    <tr>
                        <td><label for="email">Email:</label></td>
                        <td><input type="email" id="email" name="email" class="form-control" required></td>
                    </tr>
                    <tr>
                        <td><label for="password">Password:</label></td>
                        <td>
                            <div class="password-wrapper">
                                <input type="password" id="password" name="password" class="form-control" required>
                                <i class="fa fa-eye"></i>
                                <i class="fa fa-eye-slash hide"></i>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>

    @push('scripts')
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
    @endpush
@endsection
