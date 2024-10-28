@extends('layouts.staff')

@section('title', 'Edit Profile')

@section('content')
<div class="container">
    <div class="edit-form mt-4">
        <h1 class="center-heading text-center">Edit Profile</h1>

        <!-- Display Flash Message -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="true">User Info</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="security-tab" data-toggle="tab" href="#security" role="tab" aria-controls="security" aria-selected="false">Security</a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content mt-3" id="profileTabContent">
            <!-- User Info Tab -->
            <div class="tab-pane fade" id="info" role="tabpanel" aria-labelledby="info-tab">
                <form id="edit-profile-form" action="{{ route('profile.update.staff', $user->id) }}" method="POST">
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
                                    <input type="text" id="firstName" name="firstName" class="form-control" value="{{ $user->firstName }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last Name:</label>
                                    <input type="text" id="lastName" name="lastName" class="form-control" value="{{ $user->lastName }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" id="email" name="email" class="form-control" value="{{ $user->email }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="age">Age:</label>
                                    <input type="number" id="age" name="age" class="form-control" value="{{ $user->age }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="gender">Gender:</label>
                                    <select id="gender" name="gender" class="form-control" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male" {{ $user->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ $user->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>                  
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="address-info">
                            <div class="form-group">
                                    <label for="phoneNo">Phone Number:</label>
                                    <input type="text" id="phoneNo" name="phoneNo" class="form-control" value="{{ $user->phoneNo }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="address1">Address Line 1:</label>
                                    <input type="text" id="address1" name="address1" class="form-control" value="{{ $user->address1 }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="address2">Address Line 2:</label>
                                    <input type="text" id="address2" name="address2" class="form-control" value="{{ $user->address2 }}">
                                </div>
                                <div class="form-group">
                                    <label for="postcode">Postcode:</label>
                                    <input type="text" id="postcode" name="postcode" class="form-control" value="{{ $user->postcode }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="city">City:</label>
                                    <input type="text" id="city" name="city" class="form-control" value="{{ $user->city }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="state">State:</label>
                                    <select id="state" name="state" class="form-control" required>
                                        <option value="">Select State</option>
                                        <option value="Johor" {{ $user->state == 'Johor' ? 'selected' : '' }}>Johor</option>
                                        <option value="Kedah" {{ $user->state == 'Kedah' ? 'selected' : '' }}>Kedah</option>
                                        <option value="Kelantan" {{ $user->state == 'Kelantan' ? 'selected' : '' }}>Kelantan</option>
                                        <option value="Melaka" {{ $user->state == 'Melaka' ? 'selected' : '' }}>Melaka</option>
                                        <option value="Negeri Sembilan" {{ $user->state == 'Negeri Sembilan' ? 'selected' : '' }}>Negeri Sembilan</option>
                                        <option value="Pahang" {{ $user->state == 'Pahang' ? 'selected' : '' }}>Pahang</option>
                                        <option value="Pulau Pinang" {{ $user->state == 'Pulau Pinang' ? 'selected' : '' }}>Pulau Pinang</option>
                                        <option value="Perak" {{ $user->state == 'Perak' ? 'selected' : '' }}>Perak</option>
                                        <option value="Perlis" {{ $user->state == 'Perlis' ? 'selected' : '' }}>Perlis</option>
                                        <option value="Selangor" {{ $user->state == 'Selangor' ? 'selected' : '' }}>Selangor</option>
                                        <option value="Terengganu" {{ $user->state == 'Terengganu' ? 'selected' : '' }}>Terengganu</option>
                                        <option value="Sabah" {{ $user->state == 'Sabah' ? 'selected' : '' }}>Sabah</option>
                                        <option value="Sarawak" {{ $user->state == 'Sarawak' ? 'selected' : '' }}>Sarawak</option>
                                        <option value="Kuala Lumpur" {{ $user->state == 'Kuala Lumpur' ? 'selected' : '' }}>Kuala Lumpur</option>
                                        <option value="Putrajaya" {{ $user->state == 'Putrajaya' ? 'selected' : '' }}>Putrajaya</option>
                                        <option value="Labuan" {{ $user->state == 'Labuan' ? 'selected' : '' }}>Labuan</option>
                                    </select>
                                </div>

                                <!-- Hidden input to set category as 'Staff' -->
                                <input type="hidden" name="category" value="Staff">
                                
                            </div>
                        </div>
                    </div>

                    <div class="form-buttons text-center mt-4">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>

            <!-- Security Tab -->
            <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                <form id="change-password-form" action="{{ route('profile.changePasswordStaff', $user->id) }}#security" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="password">New Password:</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password" class="form-control" onkeyup="validatePassword()">
                            <div class="input-group-append">
                                <span class="input-group-text" id="toggle-password" style="cursor: pointer;">
                                    <i class="fa fa-eye" id="password-icon"></i>
                                </span>
                            </div>
                        </div>
                        <small id="password-validation-message" class="text-danger" style="display: none;"></small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password:</label>
                        <div class="input-group">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                            <div class="input-group-append">
                                <span class="input-group-text" id="toggle-confirm-password" style="cursor: pointer;">
                                    <i class="fa fa-eye" id="confirm-password-icon"></i>
                                </span>
                            </div>
                        </div>
                        <small id="password-error" class="text-danger" style="display: none;">Passwords do not match</small>
                    </div>
                    <div class="form-buttons text-center mt-4">
                        <button type="submit" class="btn btn-warning">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hash = window.location.hash || '#info'; // Default to the info tab if no hash
        const infoTab = document.getElementById('info-tab');
        const securityTab = document.getElementById('security-tab');

        // Function to activate the selected tab based on the hash
        function activateTab(selectedTab, selectedPane) {
            infoTab.classList.remove('active');
            securityTab.classList.remove('active');
            document.getElementById('info').classList.remove('show', 'active');
            document.getElementById('security').classList.remove('show', 'active');

            selectedTab.classList.add('active');
            selectedPane.classList.add('show', 'active');
        }

        // Check the hash to activate the respective tab
        if (hash === '#security') {
            activateTab(securityTab, document.getElementById('security'));
        } else {
            activateTab(infoTab, document.getElementById('info'));
        }

        // Add event listeners to update the URL hash when a tab is clicked
        infoTab.addEventListener('click', () => {
            history.replaceState(null, null, '#info');
            activateTab(infoTab, document.getElementById('info'));
        });
        securityTab.addEventListener('click', () => {
            history.replaceState(null, null, '#security');
            activateTab(securityTab, document.getElementById('security'));
        });

        // Function to validate the password
        window.validatePassword = function() {
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('password_confirmation');
            const validationMessage = document.getElementById('password-validation-message');
            const errorMessage = document.getElementById('password-error');
            const password = passwordField.value;
            const confirmPassword = confirmPasswordField.value;

            // Regular expression for password validation
            const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;

            // Reset messages
            validationMessage.style.display = 'none';
            errorMessage.style.display = 'none';

            // Validate password pattern
            if (!passwordPattern.test(password)) {
                validationMessage.textContent = 'Password must be at least 8 characters long, include at least one letter, one number, and one special character.';
                validationMessage.style.display = 'block';
                return false; // Invalid password
            }

            // Check if passwords match
            if (password !== confirmPassword) {
                errorMessage.style.display = 'block'; // Show error message for mismatch
                return false; // Passwords do not match
            }

            // Hide error messages when both checks pass
            errorMessage.style.display = 'none'; 
            return true; // All validations passed
        }

        // Validate password on keyup for real-time feedback
        document.getElementById('password').addEventListener('keyup', window.validatePassword);
        document.getElementById('password_confirmation').addEventListener('keyup', window.validatePassword);

        // Add event listener to the form for validation on submit
        document.getElementById('change-password-form').addEventListener('submit', function(event) {
            if (!window.validatePassword()) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });

        // Toggle password visibility
        const togglePasswordVisibility = (inputId, iconId) => {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            const isPasswordVisible = input.type === 'text';
            input.type = isPasswordVisible ? 'password' : 'text';
            icon.classList.toggle('fa-eye', isPasswordVisible);
            icon.classList.toggle('fa-eye-slash', !isPasswordVisible);
        };

        // Event listeners for password visibility toggle
        document.getElementById('toggle-password').addEventListener('click', () => {
            togglePasswordVisibility('password', 'password-icon');
        });

        document.getElementById('toggle-confirm-password').addEventListener('click', () => {
            togglePasswordVisibility('password_confirmation', 'confirm-password-icon');
        });
    });


</script>


@endsection
