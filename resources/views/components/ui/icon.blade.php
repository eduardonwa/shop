@props([
  'size' => 24,
  'label' => null,
  'color' => '#344D55',
  'decorative' => false,
])

@php $sizeValue = is_numeric($size) ? $size.'px' : $size; @endphp
<button class="ui-icon-btn" aria-label="Buscar">
  <svg
    {{ $attributes
        ->merge(['class' => 'ui-icon'])
        ->merge($decorative ? ['aria-hidden' => 'true', 'role' => 'img'] : ['role' => 'img', 'aria-label' => $label])
    }}
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    style="--icon-size: {{ $sizeValue }};"
    class="ui-icon"
  >
    {{ $slot }}
  </svg>
</button>