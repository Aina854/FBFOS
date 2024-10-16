<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Menu;
use App\Models\Feedback;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

class MenuFilter extends Component
{
    public $search = '';
    public $menuCategory = 'all';
    public $feedback = [];

    public function render()
    {
        // Log the current values of search and menuCategory
        Log::info('MenuFilter render method called', [
            'search' => $this->search,
            'menuCategory' => $this->menuCategory,
        ]);
    
        // Query based on menuCategory and search term, eager load feedback
        $query = Menu::with(['orderItems.order', 'orderItems.feedback']);
    
        if ($this->menuCategory !== 'all') {
            Log::info('Filtering by menuCategory', ['menuCategory' => $this->menuCategory]);
            $query->where('menuCategory', $this->menuCategory);
        }
    
        if (!empty($this->search)) {
            Log::info('Searching by menu name', ['search' => $this->search]);
            $query->where('menuName', 'like', '%' . $this->search . '%');
        }
    
        $menus = $query->get();
    
        // Loop through the menus and calculate average rating and review count
        foreach ($menus as $menu) {
            $feedbacks = $menu->orderItems->flatMap(function ($orderItem) {
                return $orderItem->order ? $orderItem->feedback : collect();
            });
    
            $averageRating = $feedbacks->avg('rating');
            $reviewsCount = $feedbacks->count();
    
            // Assign the calculated values to the menu
            $menu->averageRating = $averageRating ? round($averageRating, 1) : 'No rating';
            $menu->reviewsCount = $reviewsCount;
            $menu->feedbacks = $feedbacks; // Store feedbacks directly in the menu object
        }
    
        return view('livewire.menu-filter', compact('menus'));
    }
    


    public function setCategory($category)
    {
        $this->menuCategory = $category;  // Update the menu category
        Log::info('Category updated', ['menuCategory' => $this->menuCategory]);
        $this->render();  // Re-render the component
    }

    public function setSearch($search)
    {
        $this->search = $search;  // Update the search value
        Log::info('Search updated', ['search' => $this->search]);
        $this->render();  // Re-render the component
    }

    public function loadFeedback($menuId)
{
    // Fetch the order item IDs related to the specified menu ID
    $orderItemIds = OrderItem::where('menuId', $menuId)->pluck('orderItemId');

    // Now fetch the feedbacks using the order item IDs
    $this->feedback = Feedback::whereIn('orderitemId', $orderItemIds)
        ->with('user') // Ensure the relationship is defined
        ->get();

    // Emit event to update the modal
    $this->dispatch('feedbackLoaded', $this->feedback->toArray());


    // Log the feedback data to check in your log file
    Log::info('Feedback loaded:', $this->feedback->toArray()); // Use toArray for a more readable log
}


    
}
