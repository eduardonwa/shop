<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationsDropdown extends Component
{
    public $listeners = ['notifications-marked-read' => '$refresh'];

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()
            ->notifications
            ->findOrFail($notificationId);

        $notification->markAsRead();
        
        return redirect($notification->data['url']);
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->each->markAsRead();
        $this->dispatch('notifications-marked-read');
    }

    public function render()
    {
        return view('livewire.notifications-dropdown', [
            'unreadCount' => Auth::user()->unreadNotifications()->count(),
            'notifications' => Auth::user()->unreadNotifications()->latest()->take(5)->get()
        ]);
    }
}
