@extends('layouts.customer')

@section('title', 'Customer Home')

@section('content')
    @if(session('alert'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('alert') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success" id="cart-alert">
            {{ session('success') }}
        </div>
    @endif

    <div id="imageSlider" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('images/stafflogin.jpg') }}" class="d-block w-100" alt="Staff Login">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('images/adminlogin.jpg') }}" class="d-block w-100" alt="Admin Login">
            </div>
        </div>
    </div>

    <div class="container">
        <div class="welcome-section">
            <br><br>
            <div class="welcome-content">
                <h2 class="display-4">Welcome {{ auth()->user()->firstName }}!</h2>
                <br><br>
                <p>We’re thrilled to see you at Fariz's Bistro. Browse our menu and let us serve you a great meal today!</p>
            </div>
        </div>
        <br>
        <!-- Menu Section Livewire -->
        @livewire('menu-filter')
    </div>
@endsection

@section('scripts')
<script>
    // JavaScript for image slider
    $('#imageSlider').carousel({ interval: 2000 });

    $(document).ready(function() {
        $('.btn-primary').on('click', function(e) {
            e.preventDefault(); 
            let button = $(this);
            let cartIcon = $('#cart-icon');
            let imgToDrag = button.closest('.card').find("img").eq(0);

            if (imgToDrag.length) {
                let imgClone = imgToDrag.clone()
                    .offset({ top: imgToDrag.offset().top, left: imgToDrag.offset().left })
                    .css({ 'opacity': '0.8', 'position': 'absolute', 'height': '150px', 'width': '150px', 'z-index': '100' })
                    .appendTo($('body'))
                    .animate({
                        'top': cartIcon.offset().top + 10,
                        'left': cartIcon.offset().left + 10,
                        'width': 50,
                        'height': 50
                    }, 1000, function() {
                        imgClone.remove();
                        button.closest('form').submit(); 
                    });
                imgClone.animate({ 'width': 0, 'height': 0 });
            }
        });
    });
</script>
@endsection
