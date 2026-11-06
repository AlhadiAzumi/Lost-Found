@props(['item'])

@php $isFound = in_array($item->type, ['found', 'ditemukan'], true); @endphp

<article class="card item">
    <a href="{{ route('items.show', ['item' => $item]) }}" class="item__photo" aria-label="Lihat detail {{ $item->name }}">
        <img src="{{ $item->photo_url }}" alt="{{ $item->name }}" loading="lazy">
        <div class="item__type">
            <x-badge :icon="$isFound ? 'search-check' : 'search'"
                     :color="$isFound ? '#a3e636' : '#fee2e2'">
                {{ $isFound ? 'Ditemukan' : 'Hilang' }}
            </x-badge>
        </div>
    </a>

    <div class="item__body">
        <x-badge :icon="$item->category->icon" :color="$item->category->color">
            {{ $item->category->name }}
        </x-badge>

        <h3 class="item__title">{{ $item->name }}</h3>

        <div class="item__meta"><x-icon name="map-pin" /><span>{{ $item->location }}</span></div>
        <div class="item__meta"><x-icon name="calendar-days" /><span>{{ $item->occurred_on->translatedFormat('j M Y') }}</span></div>

        <x-badge icon="circle-check" color="#d1fae8">Disetujui</x-badge>
    </div>
</article>
