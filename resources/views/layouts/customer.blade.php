<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>



    <!-- CSS Links -->
    <link rel="stylesheet" href="{{ asset('css/cust/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cust/carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cust/card.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cust/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cust/feedback.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cust/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cust/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cust/order.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cust/payment.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cust/invoice.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cust/feedback.css') }}">
    <link rel="stylesheet" href="{{ asset('css/edit-profile.css') }}">

    <!-- Include SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    {{-- Include Bootstrap JS and jQuery --}}
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

    <!-- Include SweetAlert2 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Georama:ital,wght@0,100..900;1,100..900&family=Jua&family=Lexend+Exa:wght@100..900&display=swap" rel="stylesheet">


    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Font Links -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    @include('partials.navbar-cust')

    <div class="custom-container">
        <main>
            @yield('content') <!-- Content from child templates -->
        </main>
    </div>

    @include('partials.footer')

    <!-- Custom JS -->
    <script src="{{ asset('js/homepagecustomer.js') }}"></script>
    
    

    <!-- Additional Scripts from Child Templates -->
    @yield('scripts')

    <!-- Livewire Scripts -->
    @livewireScripts
</body>

</html>
