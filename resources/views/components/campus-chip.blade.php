@props(['campus' => null, 'variant' => 'hero'])

@if ($campus)
    <a {{ $attributes->class(['campus-chip', 'campus-chip--'.$variant]) }}
       href="{{ route('campuses.index') }}"
       title="Ganti portal kampus"
       aria-label="Ganti portal kampus. Portal saat ini: {{ $campus->name }}">
        <x-icon name="map-pin" :size="$variant === 'nav' ? 13 : 14" />
        <span class="campus-chip__name">{{ $variant === 'nav' ? $campus->name : 'Portal '.$campus->name }}</span>
        @if ($variant !== 'nav')
            <span class="campus-chip__action">Ganti kampus</span>
        @endif
    </a>
@endif
