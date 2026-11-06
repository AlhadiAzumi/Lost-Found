@extends('layouts.app')

@section('title', 'Katalog Barang — Campus Lost & Found')

@section('content')
    {{-- ===== HERO ===== --}}
    <section class="hero">
        <form method="GET" action="{{ route('catalog.index') }}" class="hero__container">
            <div class="hero__top">
                <div class="hero__copy">
                    <x-badge icon="heart-handshake" color="#bef264">
                        Saling Bantu, Barang Kembali
                    </x-badge>
                    <h1 class="hero__title">Temukan barangmu,<br>kembalikan milik orang lain.</h1>
                    <p class="hero__lead">
                        Portal resmi kampus untuk mencari dan mengembalikan barang yang tertinggal, dengan sistem verifikasi keamanan terpercaya di pos satpam.
                    </p>
                    <div class="hero__stats">
                        <div class="hero__stat">
                            <span class="hero__stat-num">{{ $items->total() }}</span>
                            <span class="hero__stat-label">Barang Terdata</span>
                        </div>
                        <div class="hero__stat">
                            <span class="hero__stat-num" style="color:#0284c7;">Pos Satpam</span>
                            <span class="hero__stat-label">Pusat Titik Temu</span>
                        </div>
                    </div>
                </div>

                <div class="cta-card">
                    <div class="cta-card__header">
                        <x-icon name="plus-circle" :size="20" />
                        <h2>Lapor Cepat</h2>
                    </div>
                    <p>Bantu sesama warga kampus lewat satu laporan singkat.</p>
                    <div class="cta-card__btns">
                        <a href="{{ route('lost.create') }}" class="btn btn--primary" style="text-align:center;">
                            <x-icon name="search" :size="16" /> Lapor Barang Hilang
                        </a>
                        <a href="{{ route('found.create') }}" class="btn btn--sky" style="text-align:center;">
                            <x-icon name="search-check" :size="16" /> Lapor Barang Temuan
                        </a>
                    </div>
                </div>
            </div>

            {{-- Search --}}
            <div class="search-box">
                <x-icon name="search" :size="22" />
                <input type="search" name="q" value="{{ $filters['q'] }}"
                       placeholder="Cari nama barang, warna, lokasi, atau deskripsi..." aria-label="Cari barang">
                <button type="submit" class="btn btn--primary" style="padding: 10px 24px; flex-shrink:0;">Cari</button>
            </div>

            {{-- Simpan state --}}
            @foreach (['type','category','location','from','to','sort'] as $key)
                @if ($filters[$key])
                    <input type="hidden" name="{{ $key }}" value="{{ $filters[$key] }}">
                @endif
            @endforeach
        </form>
    </section>

    {{-- ===== CATALOG BODY ===== --}}
    <div class="catalog">
        {{-- Sidebar Filter --}}
        <aside class="sidebar">
            {{-- Mobile filter toggle --}}
            <button class="filter-toggle" id="filterToggle" aria-expanded="false">
                <span style="display:flex;align-items:center;gap:8px;">
                    <x-icon name="sliders-horizontal" :size="16" /> Filter Barang
                </span>
                <span class="filter-toggle__icon">+</span>
            </button>

            <form method="GET" action="{{ route('catalog.index') }}" class="card" style="padding:20px;">
                @foreach (['q','type','sort'] as $key)
                    @if ($filters[$key])
                        <input type="hidden" name="{{ $key }}" value="{{ $filters[$key] }}">
                    @endif
                @endforeach

                <h2>Filter Barang</h2>

                <div class="filter-body" id="filterBody">
                    <div class="field">
                        <label for="category">Kategori</label>
                        <select id="category" name="category" class="input">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected($filters['category'] == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="location">Lokasi</label>
                        <select id="location" name="location" class="input">
                            <option value="">Semua Lokasi</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location }}" @selected($filters['location'] === $location)>
                                    {{ $location }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="from">Dari Tanggal</label>
                        <input id="from" type="date" name="from" value="{{ $filters['from'] }}" class="input">
                    </div>

                    <div class="field">
                        <label for="to">Sampai Tanggal</label>
                        <input id="to" type="date" name="to" value="{{ $filters['to'] }}" class="input">
                    </div>

                    <button type="submit" class="btn btn--primary btn--block">Terapkan Filter</button>
                    <a href="{{ route('catalog.index') }}" class="link-reset">Reset Filter</a>
                </div>
            </form>

            <div class="card panel panel--mint" style="display:flex;flex-direction:column;gap:8px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <x-icon name="shield-check" :size="20" />
                    <strong style="font-size:14px;">Aman &amp; Terverifikasi</strong>
                </div>
                <small style="line-height:1.6;color:#065f46;font-size:12.5px;">
                    Semua serah terima barang divalidasi oleh pos satpam kampus dengan <strong>Kode Serah Terima Unik</strong>.
                </small>
            </div>
        </aside>

        {{-- Results --}}
        <section class="results" aria-label="Daftar barang">
            {{-- Tabs --}}
            <nav class="tabs" aria-label="Filter tipe barang">
                @foreach ([
                    ['value' => null,       'label' => 'Semua Barang'],
                    ['value' => 'hilang',   'label' => 'Barang Hilang'],
                    ['value' => 'ditemukan','label' => 'Barang Temuan'],
                ] as $tab)
                    <a href="{{ route('catalog.index', array_filter([...request()->except('type','page'), 'type' => $tab['value']])) }}"
                       class="tab {{ $filters['type'] === $tab['value'] ? 'is-active' : '' }}">
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Sort bar --}}
            <div class="sort-bar">
                <span>Menampilkan <strong>{{ $items->total() }}</strong> barang</span>
                <div class="sort-bar__links">
                    <span style="font-size:13px;color:var(--muted);">Urutan:</span>
                    <a href="{{ route('catalog.index', array_filter([...request()->except('sort','page'), 'sort' => 'terbaru'])) }}"
                       class="sort-link {{ $filters['sort'] === 'terbaru' ? 'is-active' : '' }}">Terbaru</a>
                    <span style="color:var(--muted-light);">&middot;</span>
                    <a href="{{ route('catalog.index', array_filter([...request()->except('sort','page'), 'sort' => 'terlama'])) }}"
                       class="sort-link {{ $filters['sort'] === 'terlama' ? 'is-active' : '' }}">Terlama</a>
                </div>
            </div>

            @if ($items->isEmpty())
                <div class="empty-state">
                    <x-icon name="search" :size="40" />
                    <h3>Belum Ada Barang yang Cocok</h3>
                    <p>Coba gunakan kata kunci lain atau ubah pengaturan filter di sebelah kiri.</p>
                    <a href="{{ route('catalog.index') }}" class="btn btn--primary">Reset Pencarian</a>
                </div>
            @else
                <div class="grid">
                    @foreach ($items as $item)
                        <x-item-card :item="$item" />
                    @endforeach
                </div>

                <div style="margin-top:8px;">
                    {{ $items->links('vendor.pagination.lostfound') }}
                </div>
            @endif
        </section>
    </div>
@endsection
