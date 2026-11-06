@extends('layouts.app')

@section('title', 'Katalog barang — Campus Lost & Found')

@section('content')
    {{-- Hero + pencarian --}}
    <form method="GET" action="{{ route('catalog.index') }}" class="hero">
        <div class="hero__top">
            <div class="hero__copy">
                <x-badge icon="heart-handshake" color="#a3e636">Saling bantu, barang kembali</x-badge>
                <h1 class="hero__title">Temukan barangmu, kembalikan barang orang lain</h1>
                <p class="hero__lead">Tempat bertemunya barang yang hilang dan niat baik di kampus.</p>
            </div>

            <div class="card cta-card">
                <h2>Kehilangan atau menemukan?</h2>
                <p>Bantu pemiliknya lewat satu laporan.</p>
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('lost.create') }}" class="btn btn--primary" style="flex: 1; text-align: center; text-decoration: none; font-size: 13px;">Lapor Hilang</a>
                    <a href="{{ route('found.create') }}" class="btn btn--ghost" style="flex: 1; text-align: center; text-decoration: none; font-size: 13px; background: #fff;">Lapor Temu</a>
                </div>
            </div>
        </div>

        <div class="search" role="search">
            <x-icon name="search" :size="24" />
            <input type="search" name="q" value="{{ $filters['q'] }}"
                   placeholder="Cari nama atau deskripsi barang..." aria-label="Cari barang">
            <button type="submit" class="btn btn--primary">Cari barang</button>
        </div>

        {{-- pertahankan filter lain saat mencari --}}
        @foreach (['type', 'category', 'location', 'from', 'to', 'sort'] as $key)
            @if ($filters[$key]) <input type="hidden" name="{{ $key }}" value="{{ $filters[$key] }}"> @endif
        @endforeach
    </form>

    <div class="catalog">
        {{-- Sidebar filter --}}
        <aside class="sidebar">
            <form method="GET" action="{{ route('catalog.index') }}" class="card panel">
                <h2>Filter barang</h2>

                @foreach (['q', 'type', 'sort'] as $key)
                    @if ($filters[$key]) <input type="hidden" name="{{ $key }}" value="{{ $filters[$key] }}"> @endif
                @endforeach

                <div class="field">
                    <label for="category">Kategori</label>
                    <select id="category" name="category" class="input">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($filters['category'] == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="location">Lokasi</label>
                    <select id="location" name="location" class="input">
                        <option value="">Semua lokasi</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location }}" @selected($filters['location'] === $location)>{{ $location }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="from">Dari tanggal</label>
                    <input id="from" type="date" name="from" value="{{ $filters['from'] }}" class="input">
                </div>

                <div class="field">
                    <label for="to">Sampai tanggal</label>
                    <input id="to" type="date" name="to" value="{{ $filters['to'] }}" class="input">
                </div>

                <button type="submit" class="btn btn--primary btn--block">Terapkan filter</button>
                <a href="{{ route('catalog.index') }}" class="link-reset">Reset filter</a>
            </form>

            <div class="card panel panel--mint">
                <x-icon name="shield-check" :size="28" />
                <strong>Barang aman, klaim terverifikasi.</strong>
                <small>Jangan bagikan ciri rahasia barang di deskripsi publik.</small>
            </div>
        </aside>

        {{-- Hasil --}}
        <section class="results" aria-label="Hasil katalog">
            <nav class="tabs" aria-label="Jenis laporan">
                @foreach ([['value' => null, 'label' => 'Semua'], ['value' => 'hilang', 'label' => 'Hilang'], ['value' => 'ditemukan', 'label' => 'Ditemukan']] as $tab)
                    <a href="{{ route('catalog.index', array_filter([...request()->except('type', 'page'), 'type' => $tab['value']])) }}"
                       class="tab {{ $filters['type'] === $tab['value'] ? 'is-active' : '' }}"
                       @if ($filters['type'] === $tab['value']) aria-current="page" @endif>{{ $tab['label'] }}</a>
                @endforeach
            </nav>

            @if ($filters['from'] || $filters['to'])
                <div class="active-filters">
                    <x-badge icon="calendar-days" color="#dbeafe">
                        {{ $filters['from'] ? \Illuminate\Support\Carbon::parse($filters['from'])->translatedFormat('j M Y') : '…' }}
                        –
                        {{ $filters['to'] ? \Illuminate\Support\Carbon::parse($filters['to'])->translatedFormat('j M Y') : '…' }}
                    </x-badge>
                    <a href="{{ route('catalog.index', request()->except('from', 'to', 'page')) }}">Reset filter</a>
                </div>
            @endif

            <div class="results__meta">
                <p style="margin:0">{{ $items->total() }} barang cocok dengan pencarianmu</p>

                <form method="GET" action="{{ route('catalog.index') }}">
                    @foreach (request()->except('sort', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <select name="sort" class="btn" onchange="this.form.submit()" aria-label="Urutkan">
                        <option value="terbaru" @selected($filters['sort'] === 'terbaru')>Terbaru</option>
                        <option value="terlama" @selected($filters['sort'] === 'terlama')>Terlama</option>
                    </select>
                </form>
            </div>

            @if ($items->isEmpty())
                <div class="card empty">
                    <h3>Belum ada barang yang cocok</h3>
                    <p style="margin:0;color:var(--muted)">Coba ubah kata kunci atau reset filter.</p>
                </div>
            @else
                <div class="grid">
                    @foreach ($items as $item)
                        <x-item-card :item="$item" />
                    @endforeach
                </div>
            @endif

            {{ $items->links('vendor.pagination.lostfound') }}
        </section>
    </div>
@endsection
