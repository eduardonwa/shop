@props([
  'size' => 24,
  'label' => null,
  'color' => '#344D55',
  'decorative' => false,
  'href' => null,
])

@php $sizeValue = is_numeric($size) ? $size.'px' : $size; @endphp

@if($href)
  <a href="{{ $href }}" class="ui-icon-btn" aria-label="{{ $label }}" target="_blank">
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
  </a>
@else
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
@endif