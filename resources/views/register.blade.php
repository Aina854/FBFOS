@extends('layouts.register')

@section('title', 'Login & Register')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg p-4" id="auth-card">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12 text-center mb-4">
                <h2 class="auth-heading">Welcome</h2>
                <p class="auth-subheading">Please login or register to continue</p>
            </div>
            <!-- Tabs for switching between forms -->
            <div class="col-12">
                <ul class="nav nav-tabs justify-content-center" id="authTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ session('activeTab') === 'login' ? 'active' : '' }}" id="login-tab" data-bs-toggle="tab" href="#login" role="tab" onclick="setActiveTab('login')">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ session('activeTab') === 'register' ? 'active' : '' }}" id="register-tab" data-bs-toggle="tab" href="#register" role="tab" onclick="setActiveTab('register')">Register</a>
                    </li>
                </ul>
            </div>

            <div class="tab-content col-12 mt-4" id="authContent">
                <!-- Login Form -->
                <div class="tab-pane fade {{ session('activeTab') === 'login' ? 'show active' : '' }}" id="login" role="tabpanel">
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
                <div class="tab-pane fade {{ session('activeTab') === 'register' ? 'show active' : '' }}" id="register" role="tabpanel">
                    <form action="{{ route('register.submit') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="register-name">Name</label>
                            <input type="text" id="register-name" name="name" placeholder="eg. AinaOmar123" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="register-email">Email</label>
                            <input type="email" id="register-email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="register-password">Password</label>
                            <input type="password" id="register-password" name="password" class="form-control" required>
                        </div>

                        <!-- Hidden input to set category as 'customer' -->
                        <input type="hidden" name="category" value="Customer">
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Set active tab in session storage
    function setActiveTab(tab) {
        // Save the active tab in session storage
        sessionStorage.setItem('activeTab', tab);
    }

    // Get the active tab from session storage
    const activeTab = sessionStorage.getItem('activeTab');
    if (activeTab) {
        // If an active tab is found, activate it
        const tabLink = document.querySelector(`a[href="#${activeTab}"]`);
        if (tabLink) {
            const tabTrigger = new bootstrap.Tab(tabLink);
            tabTrigger.show();
        }
    }
</script>

@endsection
