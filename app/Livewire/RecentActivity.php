<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Log;

class RecentActivity extends Component
{
    public $recentActivities;

    public function mount()
    {
        $this->recentActivities = Log::orderBy('created_at', 'desc')->take(7)->get();
    }

    public function render()
    {
        $this->recentActivities = Log::orderBy('created_at', 'desc')->take(7)->get(); // Fetch latest activities
        return view('livewire.recent-activity');
    }

}
