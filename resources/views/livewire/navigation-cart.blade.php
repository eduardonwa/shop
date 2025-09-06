<div class="cart-icon">
    <x-nav-link wire:navigate href="{{ route('cart') }}" :active="request()->routeIs('cart')">
        <x-icon :size="24" decorative fill="#344D55">
            <x-ui.icons.cart />
        </x-icon> 
        <span>{{ $this->count }}</span>
    </x-nav-link>
</div>