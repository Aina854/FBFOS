{{-- resources/views/feedback/create.blade.php --}}
@extends('layouts.customer')

@section('content')
<div class="container">
    <h1 class="mb-4">Add Feedback</h1>

    <div class="feedback-section">
        {{-- Assuming you have a collection of order items --}}
        @foreach ($orderItems as $item)
        <div class="feedback-form border rounded p-4 shadow-sm bg-white mb-4">
            <h2 class="mb-4">Add Feedback for {{ $item->menu->menuName }}</h2>
            <form action="{{ route('feedback.store') }}" method="POST" class="feedback-form">
                @csrf
                <input type="hidden" name="orderId" value="{{ $order->orderId }}">
                <input type="hidden" name="orderItemId" value="{{ $item->orderItemId }}">
                <!-- Hidden input for authenticated user ID -->
                <input type="hidden" name="id" value="{{ auth()->id() }}">


                <div class="form-group">
                    <label for="comments" class="feedback-label">Your Feedback</label>
                    <textarea id="comments" name="comments" class="form-control feedback-input" rows="5" required></textarea>
                </div>

                <div class="form-group">
                    <label for="rating">Rating:</label>
                    <div class="rating" id="starRating_{{ $item->orderItemId }}">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="star" data-rating="{{ $i }}">&#9733;</span>
                        @endfor
                    </div>
                    <input type="hidden" id="rating" name="rating" required>
                </div>

                <div class="form-group anonymous-checkbox">
                    <input type="checkbox" id="anonymous_{{ $item->orderItemId }}" name="anonymous" value="yes">
                    <label for="anonymous_{{ $item->orderItemId }}">Submit as Anonymous</label>
                </div>

                <button type="submit" class="btn submit-btn btn btn-primary">Submit Feedback</button>
            </form>
        </div>
        @endforeach
    </div>
</div>



<script>
    // Star rating logic
    $(document).ready(function () {
    $('.star').on('click', function () {
        var rating = $(this).data('rating');
        var starRating = $(this).closest('.rating'); // Get closest parent .rating div
        var stars = starRating.children('.star');

        // Remove the selected class from all stars
        stars.removeClass('selected');

        // Add the selected class to the clicked star and all preceding stars
        stars.each(function (index) {
            if (index < rating) {
                $(this).addClass('selected');
            }
        });

        // Find the correct hidden input field within the form and set the value
        $(this).closest('form').find('input[name="rating"]').val(rating);
    });
});

    // Optional: Confirm submission
    $('form').on('submit', function() {
        return confirm('Please ensure that you have reviewed your feedback carefully before submitting. Once submitted, you will not be able to edit your feedback.');
    });
</script>
@endsection
