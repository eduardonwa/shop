<x-nav-link wire:navigate href="{{ route('cart') }}" :active="request()->routeIs('cart')">
    {{ __('Tu carrito') }} ({{ $this->count }})
</x-nav-link>