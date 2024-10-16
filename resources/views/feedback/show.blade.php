{{-- resources/views/feedback/show.blade.php --}}
@extends('layouts.staff')

@section('content')
<div class="container">
    <h1>Feedback Details</h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Feedback ID: {{ $feedback->feedbackId }}</h5>
            <p><strong>Order Item ID:</strong> {{ $feedback->orderItemId }}</p>
            <p><strong>Rating:</strong> {{ $feedback->rating }}</p>
            <p><strong>Comments:</strong> {{ $feedback->comments }}</p>
            <p><strong>User ID:</strong> {{ $feedback->id }}</p>
            <p><strong>Comments Time:</strong> {{ $feedback->commentsTime }}</p>
            <p><strong>Staff Response:</strong> {{ $feedback->staffResponse }}</p>
            <p><strong>Response Timestamp:</strong> {{ $feedback->responseTimestamp }}</p>
            <p><strong>Anonymous:</strong> {{ $feedback->anonymous }}</p>

            <a href="{{ route('feedback.edit', $feedback->feedbackId) }}" class="btn btn-warning">Edit Feedback</a>
            <a href="{{ route('feedback.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection
