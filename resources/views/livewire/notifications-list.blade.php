<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Header -->
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800">Historial de Notificaciones</h2>
            <p class="text-sm text-gray-600 mt-1">
                {{ $totalCount }} notificaciones en total
            </p>
        </div>

        <!-- Lista -->
        <div class="divide-y divide-gray-200">
            @forelse($notifications as $notification)
                <div class="px-6 py-4 hover:bg-gray-50 transition-colors
                           {{ $notification->unread() ? 'bg-blue-50' : '' }}">
                    <a href="{{ $notification->data['url'] ?? '#' }}" class="block">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ $notification->data['message'] ?? 'Notificación' }}
                                </p>
                                @isset($notification->data['order_id'])
                                    <p class="text-sm text-gray-600 mt-1">
                                        Referencia: #{{ $notification->data['order_id'] }}
                                    </p>
                                @endisset
                            </div>
                            <span class="text-xs text-gray-500">
                                {{ $notification->created_at->format('d M Y, H:i') }}
                            </span>
                        </div>
                    </a>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    No hay notificaciones registradas
                </div>
            @endforelse
        </div>

        <!-- Cargar más -->
        @if(!$loadedAll)
            <div class="px-6 py-4 border-t text-center bg-gray-50">
                <button wire:click="loadMore" 
                        wire:loading.attr="disabled"
                        class="inline-flex mx-auto items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                    <span wire:loading.remove>Cargar más notificaciones</span>
                    <span wire:loading class="mx-auto" class="">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </div>
        @endif
    </div>
</div>