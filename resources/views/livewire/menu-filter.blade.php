<div>
    <!-- Search and Category Filter -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="input-group">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="categoryFilter">Category</label>
                </div>
                <select class="custom-select" id="categoryFilter" wire:change="setCategory($event.target.value)">
                    <option value="all">All Categories</option>
                    <option value="Main Course">Main Course</option>
                    <option value="Western">Western</option>
                    <option value="Drinks">Drinks</option>
                    <option value="Side Order">Side Order</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="input-group mb-3">
                <input type="text" id="search" class="form-control" placeholder="Search by Menu Name" wire:input="setSearch($event.target.value)">
            </div>
        </div>
    </div>

    <!-- Menu Items -->
    <div class="row">
        @if($menus->isEmpty())
            <div class="col-12">
                <p>No menu items found.</p>
            </div>
        @else
            @foreach($menus as $menu)
                <div class="col-md-4">
                    <div class="card" data-category="{{ strtolower($menu->menuCategory) }}">
                        <img src="{{ asset('storage/' . $menu->menuImage) }}" class="card-img-top" alt="{{ $menu->menuName }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $menu->menuName }}</h5>
                            <p class="card-quantity-stock" 
                                style="color: 
                                    <?php 
                                        if($menu->quantityStock == 0) {
                                            echo 'red'; 
                                        } elseif($menu->quantityStock <= 10) {
                                            echo 'orange'; 
                                        } else {
                                            echo 'green'; 
                                        }
                                    ?>
                                ">
                                <strong>Available Quantity: {{ $menu->quantityStock }}</strong>
                            </p>


                            <p class="card-price">RM{{ number_format($menu->price, 2) }}</p>
                            
                            <div class="rating-container" 
                                 data-toggle="modal" 
                                 data-target="#feedbackModal" 
                                 data-menu-id="{{ $menu->menuId }}" 
                                 data-menu-name="{{ $menu->menuName }}">
                                 
                                <div class="stars1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="star1 {{ (is_numeric($menu->averageRating) && $menu->averageRating > 0 && $i <= $menu->averageRating) ? 'filled' : '' }}">&starf;</span>
                                    @endfor
                                </div>
                                <span class="reviews-count">
                                    @if ($menu->averageRating === null || $menu->averageRating == 0)
                                        (No ratings yet)
                                    @else
                                        ({{ $menu->reviewsCount }} review{{ $menu->reviewsCount !== 1 ? 's' : '' }})
                                    @endif
                                </span>
                            </div>

                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="cartId" value="{{ session('cartId') }}">
                                <input type="hidden" name="menuId" value="{{ $menu->menuId }}">
                                
                                <label for="quantity">Quantity:</label>
                                <input 
                                    type="number" 
                                    name="quantity" 
                                    min="1" 
                                    max="{{ $menu->quantityStock }}" 
                                    value="1" 
                                    required
                                    @if($menu->quantityStock == 0) disabled @endif
                                >
                                
                                <button 
                                    type="submit" 
                                    class="btn btn-primary" 
                                    @if($menu->quantityStock == 0) disabled @endif
                                >
                                    Add to Cart
                                </button>
                                
                                @if($menu->quantityStock == 0)
                                    <div class="text-danger">This item is currently out of stock.</div>
                                @endif
                            </form>


                            <!-- Centered Button with arrow icon -->
                            <button class="btn btn-link center-icon-button" data-toggle="collapse" data-target="#description-{{ $menu->menuId }}" aria-expanded="false" aria-controls="description-{{ $menu->menuId }}">
                                <i class="fas fa-chevron-down"></i> <!-- Arrow icon -->
                            </button>

                            <!-- Collapsible Description Section -->
                            <div class="collapse" id="description-{{ $menu->menuId }}">
                                <div class="card1 card-body">
                                    {{ $menu->description }}
                                    
                                    <!-- Feedback Display Section -->
                                    <div id="feedback-{{ $menu->menuId }}" class="feedback-section mt-2">
                                        @if($menu->feedbacks->isEmpty())
                                            <p>No feedback available for this menu item.</p>
                                        @else
                                            <div id="feedbackCarousel{{ $menu->menuId }}" class="carousel slide" data-ride="carousel">
                                                <div class="carousel-inner">
                                                    @foreach($menu->feedbacks as $key => $feedback)
                                                        <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                                                            <div class="card1 feedback-item mb-3">
                                                                <div class="card-body" style="background-color: #fff; padding: 15px; border-left: 4px solid #ffc107; border-radius: 5px; margin-bottom: 15px; box-shadow: none;">
                                                                    <h5 class="card-subtitle mb-2 text-muted" style="font-size: 0.9em;">
                                                                        <strong>
                                                                        @if($feedback->anonymous === 'yes')
                                                                            Anonymous
                                                                        @else
                                                                            {{ $feedback->user->firstName }} {{ $feedback->user->lastName }} <br>
                                                                        @endif

                                                                        <div class="rating-stars" style="font-size: 0.6em; color: gold;">
                                                                            @for ($i = 1; $i <= 5; $i++)
                                                                                <i class="fa{{ $feedback->rating >= $i ? 's' : 'r' }} fa-star"></i>
                                                                            @endfor
                                                                        </div>
                                                                        </strong>
                                                                    </h5>
                                                                    <p class="card-text" style="font-size: 0.9em;">"{{ $feedback->comments ?? 'No comments available.' }}"</p>
                                                                    <small class="text-muted" style="font-size: 0.8em;">{{ \Carbon\Carbon::parse($feedback->commentsTime)->format('n/j/Y, g:i:s A') }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <!-- Controls -->
                                                <a class="carousel-control-prev" href="#feedbackCarousel{{ $menu->menuId }}" role="button" data-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Previous</span>
                                                </a>
                                                <a class="carousel-control-next" href="#feedbackCarousel{{ $menu->menuId }}" role="button" data-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
