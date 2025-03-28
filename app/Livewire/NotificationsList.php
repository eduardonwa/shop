<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationsList extends Component
{
    public $notifications;
    public $totalCount;
    public $perPage = 20;
    public $loadedAll = false;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Auth::user()
            ->notifications()
            ->latest()
            ->take($this->perPage)
            ->get();

        $this->totalCount = Auth::user()->notifications()->count();
        $this->loadedAll = $this->notifications->count() >= $this->totalCount;
    }

    public function loadMore()
    {
        $this->perPage += 20;
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notifications-list');
    }
}
