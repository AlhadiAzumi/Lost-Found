@props(['campus' => null, 'variant' => 'hero'])

@if ($campus)
    <a {{ $attributes->class(['campus-chip', 'campus-chip--'.$variant]) }}
       href="{{ route('campuses.index') }}"
       title="Ganti portal kampus">
        <x-icon name="map-pin" :size="$variant === 'nav' ? 13 : 14" />
        <span class="campus-chip__name">Portal {{ $campus->name }}</span>
        <span class="campus-chip__action">{{ $variant === 'nav' ? 'Ganti' : 'Ganti kampus' }}</span>
    </a>
@endif
