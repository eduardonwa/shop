@if ($record = $getRecord())
    @if ($product = $record->product)
        <img
            src="{{ $product->getFirstMediaUrl('featured', 'md_thumb') }}"
            alt="{{ $product->name }}"
            class="rounded-md mx-auto"
        />
    @else
        <p>No product found.</p>
    @endif
@else
    <p>No record found.</p>
@endif