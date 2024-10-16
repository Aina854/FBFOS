@extends('layouts.staff')

@section('content')
    <!-- Container for Feedback without Responses -->
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <h2 class="mb-4">Feedback Management</h2>

        <!-- Button for viewing past feedback -->
        <div class="container mt-4 mb-4">
            <a href="{{ route('feedback.past') }}">View Past Feedback</a>
        </div>

        @if($feedbacks->isEmpty())
            <div class="alert alert-info" role="alert">
                No feedback available to respond.
            </div>
        @else
            @foreach($feedbacks as $feedback)
                <!-- Only show feedbacks without staff response -->
                @if(!$feedback->staffResponse)
                    <div class="card feedback-entry mb-4" id="feedback-{{ $feedback->id }}">
                        <!-- Check if feedback is anonymous -->
                        <h4>
                            @if($feedback->anonymous === 'yes')
                                Anonymous
                            @else
                                {{ $feedback->user->firstName }} {{ $feedback->user->lastName }}
                            @endif
                        </h4>

                        <!-- Display Rating as Stars -->
                        <p>Rating: 
                            @for($i = 1; $i <= $feedback->rating; $i++)
                                <i class="fas fa-star" style="color:gold;"></i>
                            @endfor
                            @for($i = $feedback->rating + 1; $i <= 5; $i++)
                                <i class="far fa-star" style="color:gold;"></i>
                            @endfor
                        </p>

                        <!-- Feedback Comments -->
                        <div class="feedback-comments">
                            <strong>Feedback:</strong>
                            <div class="feedback-comment-box">
                                {{ $feedback->comments }}
                            </div>
                        </div>

                        <!-- Feedback Creation Time -->
                        <div>
                            <p style="font-size: 12px;">
                                {{ $feedback->created_at->format('d-m-Y H:i') }}
                            </p>
                        </div>

                        <!-- Order Item Details (if any) -->
                        <div class="feedback-comment-box">
                            <img src="{{ asset('storage/' . $feedback->orderItem->menu->menuImage) }}" style="height: 60px; width: 60px;"> 
                            {{ $feedback->orderItem->menu->menuName }} x{{ $feedback->orderItem->quantity }}
                        </div>

                        <!-- Response Form -->
                        <form class="response-form mt-3" action="{{ route('feedback.submit', $feedback->orderItem->orderItemId) }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="responseInput">Your Response:</label>
                                <textarea required class="form-control" name="staffResponse" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Response</button>
                        </form>
                    </div>
                @endif
            @endforeach
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.response-form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!confirm('Please check before you respond to the feedback. Once submitted, you cannot delete your response. Are you sure you want to proceed?')) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
@endpush
