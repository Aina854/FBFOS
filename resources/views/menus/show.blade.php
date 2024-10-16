@extends('layouts.staff')

@section('content')
    <div class="container">
        <h1>Menu Item Details</h1>
        <div class="card">
            <div class="card-body">
                @if($menu->menuImage)
                    <img src="{{ asset('storage/' . $menu->menuImage) }}" alt="{{ $menu->menuName }}" width="200">
                @else
                    No Image
                @endif
                <h2 class="card-title">{{ $menu->menuName }}</h2>
                <p class="card-text"><strong>Category:</strong> {{ $menu->menuCategory }}</p>
                <p class="card-text"><strong>Price:</strong> ${{ $menu->price }}</p>
                <p class="card-text"><strong>Availability:</strong> {{ $menu->availability }}</p>
                <p class="card-text"><strong>Description:</strong> {{ $menu->description }}</p>
                <a href="{{ route('menus.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
@endsection
