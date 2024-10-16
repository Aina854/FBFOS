@extends('layouts.customer')

@section('content')
<div class="container">
    <h2 class="mb-4">Your Feedback</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($feedbacks->isEmpty())
        <p>No feedback available for this order.</p>
    @else
        @foreach ($feedbacks as $feedback)
            <div class="card feedback-entry">
                <h4><strong>{{ $feedback->user->firstName }} {{ $feedback->user->lastName }}</strong></h4>

                <!-- Display Rating as Stars -->
                <p>Rating: 
                    @for ($i = 1; $i <= $feedback->rating; $i++)
                        <i class="fas fa-star" style="color:gold;"></i>
                    @endfor
                    @for ($i = $feedback->rating + 1; $i <= 5; $i++)
                        <i class="far fa-star" style="color:gold;"></i>
                    @endfor
                </p>

                <!-- Feedback Comments Section -->
                <div class="feedback-comments">
                    <strong>Feedback:</strong>
                    <div class="feedback-comment-box">
                        {{ $feedback->comments }}
                    </div>
                </div>
                <div>
                    <p class="feedback-timestamp">
                        {{ $feedback->commentsTime }}
                    </p>
                </div>

                <!-- Staff Response Section -->
                @if (!empty($feedback->staffResponse))
                    <div class="feedback-response">
                        <strong>Staff Response:</strong> {{ $feedback->staffResponse }}
                    </div>
                    <p class="feedback-timestamp"> 
                        {{ $feedback->responseTimestamp }}
                    </p>
                @endif

                <!-- Display associated menu image and name from orderItem -->
                @if ($feedback->orderItem && $feedback->orderItem->menu)
                    <div class="feedback-comment-box">
                        @if ($feedback->orderItem->menu->menuImage)
                            <img src="{{ asset('storage/' . $feedback->orderItem->menu->menuImage) }}" style="height: 60px; width: 60px;"> 
                        @endif
                        {{ $feedback->orderItem->menu->menuName }} x{{ $feedback->orderItem->quantity }}
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>

@endsection
