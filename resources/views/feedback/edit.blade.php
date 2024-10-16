{{-- resources/views/feedback/edit.blade.php --}}
@extends('layouts.customer')

@section('content')
<div class="container">
    <h1>Edit Feedback</h1>

    <form action="{{ route('feedback.update', $feedback->feedbackId) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="orderItemId">Order Item ID</label>
            <input type="number" name="orderItemId" class="form-control" id="orderItemId" value="{{ $feedback->orderItemId }}" required>
        </div>
        
        <div class="form-group">
            <label for="rating">Rating (1-5)</label>
            <input type="number" name="rating" class="form-control" id="rating" min="1" max="5" value="{{ $feedback->rating }}" required>
        </div>

        <div class="form-group">
            <label for="comments">Comments</label>
            <textarea name="comments" class="form-control" id="comments" rows="4" required>{{ $feedback->comments }}</textarea>
        </div>

        <div class="form-group">
            <label for="staffResponse">Staff Response</label>
            <textarea name="staffResponse" class="form-control" id="staffResponse" rows="4">{{ $feedback->staffResponse }}</textarea>
        </div>

        <div class="form-group">
            <label for="anonymous">Anonymous</label>
            <select name="anonymous" class="form-control" id="anonymous" required>
                <option value="yes" {{ $feedback->anonymous === 'yes' ? 'selected' : '' }}>Yes</option>
                <option value="no" {{ $feedback->anonymous === 'no' ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <button type="submit" class="btn btn-warning">Update Feedback</button>
        <a href="{{ route('feedback.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
