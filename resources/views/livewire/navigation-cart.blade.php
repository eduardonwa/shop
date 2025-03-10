<x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
    {{ __('Carrito') }} ({{ $this->count }})
</x-nav-link>