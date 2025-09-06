<div
    x-data="{ open:false }"
    x-cloak
    class="relative ml-4"
>
    <button
        @click="open = !open"
        class="ui-icon-btn"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="ui-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        @if ($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-block w-4 h-4 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full text-xs text-white flex items-center justify-center">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <div
        x-show="open"
        @click.away="open = false"
        class="origin-top-right absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
    >
        <div class="py-1">
            <div class="px-4 py-2 text-sm font-medium text-gray-700 border-b">
                Notificaciones
            </div>

            @forelse ($notifications as $notification)
                <a
                    wire:click.prevent="markAsRead('{{ $notification->id }}')" 
                    href="{{ $notification->data['url'] }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 border-b transition-colors"
                >
                    <p>Nueva orden #{{ $notification->data['order_id'] }}</p>
                    <p class="text-sm text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                </a>
            @empty
                <div class="px-4 py-2 text-sm text-gray-500">
                    No hay notificaciones nuevas
                </div>
            @endforelse

            @if($unreadCount > 0)
                <div class="px-4 py-2 text-center border-t">
                    <button
                        wire:click="markAllAsRead"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                    >
                        Marcar todas como leídas
                    </button>
                </div>
            @endif
            <a
                href="{{ route('notifications') }}" 
                class="text-center block px-4 py-2 text-sm text-blue-600 underline"
            >
                Ver historial completo
            </a>
        </div>
    </div>
</div>
