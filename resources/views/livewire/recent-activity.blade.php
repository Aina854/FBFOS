<div wire:poll.3s>
    <h4 class="text-center" style="font-size: 1.8em; font-weight: bold; color: #333; margin-bottom: 30px;">Recent Activity <i class="fas fa-clock"></i></h4>
    <div class="card shadow-sm rounded-lg">
        <div class="card-body" style="background-color:#f4f4f4;">
            <ul style="list-style: none; padding: 0;">
                @foreach($recentActivities as $activity)
                    <li style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f0f0f0; padding: 10px 0;">
                        <div style="display: flex; align-items: center;">
                            <!-- Icon based on the activity -->
                            @php
                                $icon = '';
                                switch ($activity->action) {
                                    case 'User Registered':
                                        $icon = 'fa-user-plus';
                                        break;
                                    case 'Profile updated':
                                        $icon = 'fa-user-edit';
                                        break;
                                    case 'Add Staff':
                                        $icon = 'fa-user-plus';
                                        break;
                                    case 'Delete Staff':
                                        $icon = 'fa-user-times';
                                        break;
                                    case 'Edit Staff':
                                        $icon = 'fa-user-edit';
                                        break;
                                    case 'Delete Customer':
                                        $icon = 'fa-user-times';
                                        break;
                                    case 'Create Menu Item':
                                        $icon = 'fa-utensils';
                                        break;
                                    case 'Edit Menu Item':
                                        $icon = 'fa-edit';
                                        break;
                                    case 'Delete Menu Item':
                                        $icon = 'fa-trash';
                                        break;
                                    case 'Create Order':
                                        $icon = 'fa-cart-plus';
                                        break;
                                    case 'Create Payment':
                                        $icon = 'fa-credit-card';
                                        break;
                                    default:
                                        $icon = 'fa-question'; // Default icon
                                }
                            @endphp
                            <i class="fas {{ $icon }}" style="margin-right: 10px;"></i>
                            <span>{{ $activity->details }}</span>
                        </div>
                        <div>
                            <span style="font-size: 0.8em; color: #888; opacity: 0.8;">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
