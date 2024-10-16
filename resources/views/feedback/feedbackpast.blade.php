@extends('layouts.staff')

@section('content')
    <!-- Container for Viewing Past Feedback -->
    <div class="container">
        <h2 class="mb-4">Past Feedback</h2>

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

        @if($feedbacks->isEmpty())
            <div class="alert alert-info" role="alert">
                No past feedback available.
            </div>
        @else
            @foreach($feedbacks as $feedback)
                <div class="card feedback-entry" id="feedback-{{ $feedback->id }}">
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

                    <div>
                        <p style="font-size: 12px;">
                            {{ $feedback->created_at->format('d-m-Y H:i') }}
                        </p>
                    </div>

                    <!-- Display associated menu image and name from orderItem -->
                    @if ($feedback->orderItem && $feedback->orderItem->menu)
                        <div class="feedback-comment-box">
                            <img src="{{ asset('storage/' . $feedback->orderItem->menu->menuImage) }}" style="height: 60px; width: 60px;"> 
                            {{ $feedback->orderItem->menu->menuName }} x{{ $feedback->orderItem->quantity }}
                        </div>
                    @endif

                    <!-- Staff Response -->
                    @if (!empty($feedback->staffResponse))
                        <div class="staff-response-box" style="background-color: #d4edda; padding: 10px; border-radius: 5px;">
                            <strong>Staff Response:</strong>
                            <p>{{ $feedback->staffResponse }}</p>
                        </div>
                        <p class="feedback-timestamp" style="font-size: 12px;">
                            Responded at: {{ $feedback->responseTimestamp }}
                        </p>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
@endsection
