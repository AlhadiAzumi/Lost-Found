@extends('layouts.app')

@section('title', 'Katalog Barang — Campus Lost & Found')

@section('content')
    {{-- Hero Section dengan gradien cerah Brainfish + Search Box Neo-brutalist --}}
    <section class="hero">
        <form method="GET" action="{{ route('catalog.index') }}" class="hero__container">
            <div class="hero__top">
                <div class="hero__copy">
                    <x-badge icon="heart-handshake" color="#bef264">
                        Saling Bantu, Barang Kembali
                    </x-badge>
                    <h1 class="hero__title">Temukan barangmu, kembalikan barang orang lain.</h1>
                    <p class="hero__lead">
                        Portal resmi kampus untuk mencari dan mengembalikan barang yang tertinggal dengan sistem verifikasi keamanan terpercaya di pos satpam.
                    </p>
                </div>

                <div class="cta-card">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <span style="font-size: 20px;">🎒</span>
                        <h2>Lapor Cepat</h2>
                    </div>
                    <p>Bantu sesama warga kampus lewat satu laporan singkat.</p>
                    <div style="display: flex; gap: 8px; flex-direction: column;">
                        <a href="{{ route('lost.create') }}" class="btn btn--primary" style="text-align: center; text-decoration: none;">
                            <x-icon name="search" :size="16" /> Lapor Barang Hilang
                        </a>
                        <a href="{{ route('found.create') }}" class="btn btn--sky" style="text-align: center; text-decoration: none;">
                            <x-icon name="search-check" :size="16" /> Lapor Barang Temuan
                        </a>
                    </div>
                </div>
            </div>

            <div class="search-box">
                <x-icon name="search" :size="22" />
                <input type="search" name="q" value="{{ $filters['q'] }}"
                       placeholder="Cari nama barang, warna, lokasi, atau deskripsi..." aria-label="Cari barang">
                <button type="submit" class="btn btn--primary" style="padding: 10px 24px;">Cari</button>
            </div>

            {{-- Simpan state query filter --}}
            @foreach (['type', 'category', 'location', 'from', 'to', 'sort'] as $key)
                @if ($filters[$key]) 
                    <input type="hidden" name="{{ $key }}" value="{{ $filters[$key] }}"> 
                @endif
            @endforeach
        </form>
    </section>

    {{-- Main Catalog Area --}}
    <div class="catalog">
        {{-- Sidebar Filter --}}
        <aside class="sidebar">
            <form method="GET" action="{{ route('catalog.index') }}" class="card panel">
                <h2>Filter Barang</h2>

                @foreach (['q', 'type', 'sort'] as $key)
                    @if ($filters[$key]) 
                        <input type="hidden" name="{{ $key }}" value="{{ $filters[$key] }}"> 
                    @endif
                @endforeach

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
            </form>

            <div class="card panel panel--mint">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <x-icon name="shield-check" :size="24" />
                    <strong>Aman & Terverifikasi</strong>
                </div>
                <small style="line-height: 1.5; color: #065f46;">
                    Semua penyerahan barang temuan divalidasi langsung oleh pos satpam kampus menggunakan <strong>Kode Serah Terima Unik</strong>.
                </small>
            </div>
        </aside>

        {{-- Results Area --}}
        <section class="results" aria-label="Daftar barang">
            {{-- Tab Filter Type --}}
            <nav class="tabs">
                @foreach ([['value' => null, 'label' => 'Semua Barang'], ['value' => 'hilang', 'label' => '🔍 Barang Hilang'], ['value' => 'ditemukan', 'label' => '✨ Barang Temuan']] as $tab)
                    <a href="{{ route('catalog.index', array_filter([...request()->except('type', 'page'), 'type' => $tab['value']])) }}"
                       class="tab {{ $filters['type'] === $tab['value'] ? 'is-active' : '' }}">
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </nav>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                <span style="font-weight: 700; font-size: 14px; color: #475569;">
                    Menampilkan {{ $items->total() }} barang
                </span>
                
                {{-- Urutkan --}}
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 13px; font-weight: 600; color: #64748b;">Urutan:</span>
                    <a href="{{ route('catalog.index', array_filter([...request()->except('sort', 'page'), 'sort' => 'terbaru'])) }}"
                       style="font-size: 13px; font-weight: {{ $filters['sort'] === 'terbaru' ? '800; text-decoration: underline;' : '500;' }}">Terbaru</a>
                    <span>·</span>
                    <a href="{{ route('catalog.index', array_filter([...request()->except('sort', 'page'), 'sort' => 'terlama'])) }}"
                       style="font-size: 13px; font-weight: {{ $filters['sort'] === 'terlama' ? '800; text-decoration: underline;' : '500;' }}">Terlama</a>
                </div>
            </div>

            @if ($items->isEmpty())
                <div class="card empty" style="border: 2px dashed #cbd5e1; border-radius: 20px; background: #fff; padding: 48px 24px;">
                    <span style="font-size: 40px; display: block; margin-bottom: 12px;">🔍</span>
                    <h3>Belum Ada Barang yang Cocok</h3>
                    <p style="color: var(--muted); margin: 0 0 20px;">
                        Coba gunakan kata kunci lain atau ubah pengaturan filter di sebelah kiri.
                    </p>
                    <a href="{{ route('catalog.index') }}" class="btn btn--primary">Reset Pencarian</a>
                </div>
            @else
                <div class="grid">
                    @foreach ($items as $item)
                        <x-item-card :item="$item" />
                    @endforeach
                </div>

                <div style="margin-top: 24px;">
                    {{ $items->links('vendor.pagination.lostfound') }}
                </div>
            @endif
        </section>
    </div>
@endsection
