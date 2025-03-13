@props(['title' => false])
<div {{ $attributes->class(['bg-white rounded-lg shadow p-4 space-y-2']) }}>
    @if($title)
        <h2 class="font-medium text-lg">{{ $title }}</h2>
    @endif
    {{ $slot }}
</div>