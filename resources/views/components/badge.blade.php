@props(['icon' => null, 'color' => '#dbeafe'])

<span {{ $attributes->class('badge') }} style="background: {{ $color }}">
    @if ($icon)<x-icon :name="$icon" :size="13" />@endif
    {{ $slot }}
</span>
