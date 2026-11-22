@extends('layouts.app')

@section('title', 'Feed Publik — Lost & Found')

@push('styles')
<style>
    .catalog-layout {
        display: grid;
        grid-template-columns: 270px 1fr;
        gap: 1.5rem;
        align-items: start;
    }
    .filter-group { margin-bottom: 1rem; }
    .type-tabs {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.4rem;
        background: #ffffff;
        padding: 0.3rem;
        border-radius: 999px;
        margin-bottom: 1rem;
    }
    .type-tab {
        text-align: center;
        padding: 0.48rem 0.4rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        color: #4b5563;
        text-decoration: none;
        transition: all 0.18s;
    }
    .type-tab.active {
        background: #111111;
        color: #ffffff;
    }

    /* Search Bar Atas */
    .search-bar {
        display: flex;
        gap: 0.65rem;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
    }
    .search-bar .pill-input {
        flex: 1;
        min-width: 220px;
    }

    /* Grid Kartu Barang */
    .items-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(265px, 1fr));
        gap: 1.25rem;
    }
    .item-card {
        background: rgba(244, 247, 243, 0.86);
        backdrop-filter: blur(18px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 24px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 16px 32px -14px rgba(22, 42, 30, 0.12);
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }
    .item-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 22px 40px -14px rgba(22, 42, 30, 0.18);
    }
    .card-thumb {
        width: 100%;
        height: 175px;
        object-fit: cover;
        background: linear-gradient(135deg, #a9cbb2, #8ab496);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1f2937;
        font-size: 0.82rem;
        font-weight: 700;
    }
    .card-body {
        padding: 1.15rem 1.25rem 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
        flex: 1;
    }
    .badge-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.4rem;
    }
    .pill-badge {
        padding: 0.24rem 0.72rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
    }
    .badge-found { background: #d1fae5; color: #065f46; }
    .badge-lost { background: #fee2e2; color: #b91c1c; }
    .badge-progress { background: #fef3c7; color: #92400e; }
    .badge-returned { background: #e5e7eb; color: #374151; }

    .card-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #111111;
        line-height: 1.3;
    }
    .card-meta {
        font-size: 0.78rem;
        color: #4b5563;
        line-height: 1.5;
    }
    .card-desc {
        font-size: 0.82rem;
        color: #374151;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 0.4rem;
    }

    @media (max-width: 920px) {
        .catalog-layout { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
    @php
        $campusParam = request()->route('campus');
        $campusSlug = is_object($campusParam)
            ? $campusParam->slug
            : ($campusParam ?: ($currentCampus->slug ?? request()->segment(2)));

        $activeType = $filters['type'] ?? null;
    @endphp

    <div class="page-header">
        <h1 class="page-title">Feed Publik Barang Kampus</h1>
        <div style="display:flex; gap:0.6rem; flex-wrap:wrap;">
            @auth
                <a href="{{ route('lost.create', ['campus' => $campusSlug]) }}"
                   style="padding:0.68rem 1.2rem; border-radius:999px; background:#ffffff; color:#111111; font-weight:700; font-size:0.84rem; text-decoration:none;">
                    + Lapor Hilang
                </a>
                <a href="{{ route('found.create', ['campus' => $campusSlug]) }}" class="btn-black" style="padding:0.68rem 1.2rem; font-size:0.84rem;">
                    + Lapor Temuan
                </a>
            @endauth
        </div>
    </div>

    <div class="catalog-layout">
        {{-- Sidebar Filter Kiri --}}
        <aside class="glass-panel" style="padding:1.35rem;">
            <h2 style="font-size:1rem; font-weight:800; margin-bottom:0.9rem; color:#111111;">Filter Barang</h2>

            {{-- Tab Tipe Cepat --}}
            <div class="type-tabs">
                <a href="{{ route('catalog.index', array_merge(['campus' => $campusSlug], request()->except('type', 'page'))) }}"
                   class="type-tab {{ !$activeType ? 'active' : '' }}">Semua</a>
                <a href="{{ route('catalog.index', array_merge(['campus' => $campusSlug], request()->except('page'), ['type' => 'found'])) }}"
                   class="type-tab {{ in_array($activeType, ['found', 'ditemukan']) ? 'active' : '' }}">Temuan</a>
                <a href="{{ route('catalog.index', array_merge(['campus' => $campusSlug], request()->except('page'), ['type' => 'lost'])) }}"
                   class="type-tab {{ in_array($activeType, ['lost', 'hilang']) ? 'active' : '' }}">Hilang</a>
            </div>

            <form method="GET" action="{{ route('catalog.index', ['campus' => $campusSlug]) }}">
                @if ($activeType)
                    <input type="hidden" name="type" value="{{ $activeType }}">
                @endif
                @if (!empty($filters['q']))
                    <input type="hidden" name="q" value="{{ $filters['q'] }}">
                @endif

                <div class="filter-group">
                    <label for="category" class="field-label">Kategori</label>
                    <select id="category" name="category" class="pill-select">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories ?? [] as $cat)
                            <option value="{{ $cat->id }}" {{ ($filters['category'] ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="location" class="field-label">Lokasi</label>
                    <select id="location" name="location" class="pill-select">
                        <option value="">Semua Lokasi</option>
                        @foreach ($locations ?? [] as $loc)
                            <option value="{{ $loc }}" {{ ($filters['location'] ?? '') === $loc ? 'selected' : '' }}>
                                {{ $loc }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="from" class="field-label">Dari Tanggal</label>
                    <input type="date" id="from" name="from" value="{{ $filters['from'] ?? '' }}" class="pill-input">
                </div>

                <div class="filter-group">
                    <label for="to" class="field-label">Sampai Tanggal</label>
                    <input type="date" id="to" name="to" value="{{ $filters['to'] ?? '' }}" class="pill-input">
                </div>

                <div class="filter-group">
                    <label for="sort" class="field-label">Urutkan</label>
                    <select id="sort" name="sort" class="pill-select">
                        <option value="terbaru" {{ ($filters['sort'] ?? 'terbaru') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                        <option value="terlama" {{ ($filters['sort'] ?? '') === 'terlama' ? 'selected' : '' }}>Terlama</option>
                    </select>
                </div>

                <button type="submit" class="btn-black" style="width:100%; margin-bottom:0.55rem;">
                    Terapkan Filter
                </button>
                <a href="{{ route('catalog.index', ['campus' => $campusSlug]) }}"
                   style="display:block; text-align:center; padding:0.65rem; border-radius:999px; background:#ffffff; color:#374151; font-size:0.82rem; font-weight:700; text-decoration:none;">
                    Reset Filter
                </a>
            </form>
        </aside>

        {{-- Kolom Kanan: Search & Daftar Kartu Barang --}}
        <div>
            <form method="GET" action="{{ route('catalog.index', ['campus' => $campusSlug]) }}" class="search-bar">
                @if ($activeType)<input type="hidden" name="type" value="{{ $activeType }}">@endif
                @if (!empty($filters['category']))<input type="hidden" name="category" value="{{ $filters['category'] }}">@endif
                @if (!empty($filters['location']))<input type="hidden" name="location" value="{{ $filters['location'] }}">@endif
                @if (!empty($filters['from']))<input type="hidden" name="from" value="{{ $filters['from'] }}">@endif
                @if (!empty($filters['to']))<input type="hidden" name="to" value="{{ $filters['to'] }}">@endif
                @if (!empty($filters['sort']))<input type="hidden" name="sort" value="{{ $filters['sort'] }}">@endif

                <input
                    type="text"
                    name="q"
                    value="{{ $filters['q'] ?? '' }}"
                    placeholder="Cari nama barang, ciri, atau deskripsi..."
                    class="pill-input"
                >
                <button type="submit" class="btn-black">Cari</button>
            </form>

            @if ($items->isEmpty())
                <div class="glass-panel" style="text-align:center; padding:3rem 1.5rem;">
                    <p style="font-size:1.05rem; font-weight:800; color:#111111;">Belum ada barang yang cocok.</p>
                    <p style="font-size:0.85rem; color:#4b5563; margin-top:0.35rem;">
                        Pastikan laporan sudah disetujui admin atau coba reset filter pencarian.
                    </p>
                </div>
            @else
                <div class="items-grid">
                    @foreach ($items as $item)
                        @php
                            $isFound = in_array($item->type, ['found', 'ditemukan']);
                        @endphp
                        <article class="item-card">
                            @if ($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="card-thumb">
                            @else
                                <div class="card-thumb">Tanpa Foto Publik</div>
                            @endif

                            <div class="card-body">
                                <div class="badge-row">
                                    <span class="pill-badge {{ $isFound ? 'badge-found' : 'badge-lost' }}">
                                        {{ $isFound ? 'Ditemukan' : 'Hilang' }}
                                    </span>

                                    @if ($item->status === 'claim_in_progress')
                                        <span class="pill-badge badge-progress">Proses Klaim</span>
                                    @elseif ($item->status === 'returned')
                                        <span class="pill-badge badge-returned">Dikembalikan</span>
                                    @else
                                        <span style="font-size:0.74rem; color:#6b7280; font-weight:600;">
                                            {{ $item->category->name ?? 'Umum' }}
                                        </span>
                                    @endif
                                </div>

                                <h3 class="card-title">{{ $item->title }}</h3>

                                <div class="card-meta">
                                    <div>📍 {{ $item->location }}</div>
                                    <div>📅 {{ \Carbon\Carbon::parse($item->date_event)->translatedFormat('d M Y') }}</div>
                                </div>

                                <p class="card-desc">{{ $item->description }}</p>

                                <a href="{{ route('items.show', ['campus' => $campusSlug, 'item' => $item]) }}"
                                   class="btn-black"
                                   style="width:100%; padding:0.68rem 1rem; font-size:0.84rem; margin-top:auto;">
                                    Lihat Detail
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($items->hasPages())
                    <div style="margin-top:1.5rem;">
                        {{ $items->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection