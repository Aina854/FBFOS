@extends('layouts.register')

@section('title', 'Login & Register')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg p-4" id="auth-card">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h2 class="auth-heading">Welcome</h2>
                <p class="auth-subheading">Please login or register to continue</p>
            </div>
            <!-- Tabs for switching between forms -->
            <div class="col-12">
                <ul class="nav nav-tabs justify-content-center" id="authTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="login-tab" data-bs-toggle="tab" href="#login" role="tab">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="register-tab" data-bs-toggle="tab" href="#register" role="tab">Register</a>
                    </li>
                </ul>
            </div>

            <div class="tab-content col-12 mt-4" id="authContent">
                <!-- Login Form -->
                <div class="tab-pane fade show active" id="login" role="tabpanel">
                    <form action="{{ route('login.submit') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="login-email">Email</label>
                            <input type="email" id="login-email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="login-password">Password</label>
                            <input type="password" id="login-password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>

                <!-- Register Form -->
                <div class="tab-pane fade" id="register" role="tabpanel">
                    <form action="{{ route('register.submit') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="register-name">Name</label>
                            <input type="text" id="register-name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="register-email">Email</label>
                            <input type="email" id="register-email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="register-password">Password</label>
                            <input type="password" id="register-password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Enable Bootstrap tabs switching with smooth animation
    var triggerTabList = [].slice.call(document.querySelectorAll('#authTabs a'));
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);

        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });
</script>

@endsection
