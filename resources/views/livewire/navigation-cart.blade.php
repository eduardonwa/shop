<x-nav-link href="{{ route('cart') }}" :active="request()->routeIs('cart')">
    {{ __('Tu carrito') }} ({{ $this->count }})
</x-nav-link>