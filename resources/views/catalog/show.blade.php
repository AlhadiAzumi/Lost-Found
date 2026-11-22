@extends('layouts.app')

@section('title', $item->title . ' — Detail Barang')

@push('styles')
<style>
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1.1fr;
        gap: 1.5rem;
        align-items: start;
    }
    .detail-photo {
        width: 100%;
        max-height: 380px;
        object-fit: cover;
        border-radius: 22px;
        background: linear-gradient(135deg, #a9cbb2, #8ab496);
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 260px;
        font-weight: 700;
        color: #1f2937;
    }
    .meta-table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.1rem 0;
    }
    .meta-table td {
        padding: 0.65rem 0;
        border-bottom: 1px solid rgba(17, 24, 39, 0.08);
        font-size: 0.88rem;
    }
    .meta-table td:first-child {
        color: #6b7280;
        font-weight: 600;
        width: 150px;
    }
    .meta-table td:last-child {
        color: #111111;
        font-weight: 700;
    }
    .desc-box {
        background: #ffffff;
        border-radius: 18px;
        padding: 1rem 1.2rem;
        font-size: 0.9rem;
        line-height: 1.65;
        color: #374151;
        margin-bottom: 1.35rem;
    }
    @media (max-width: 900px) {
        .detail-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
    @php
        $campusParam = request()->route('campus');
        $campusSlug = is_object($campusParam)
            ? $campusParam->slug
            : ($campusParam ?: ($currentCampus->slug ?? request()->segment(2)));

        $isFound = in_array($item->type, ['found', 'ditemukan']);
        $canClaim = auth()->check()
            && $isFound
            && in_array($item->status, ['approved', 'disetujui'])
            && auth()->id() !== $item->user_id;
    @endphp

    <div class="page-header">
        <h1 class="page-title">Detail Laporan Barang</h1>
        <a href="{{ route('catalog.index', ['campus' => $campusSlug]) }}"
           style="padding:0.65rem 1.25rem; border-radius:999px; background:#ffffff; color:#111111; font-weight:700; font-size:0.84rem; text-decoration:none;">
            ← Kembali ke Feed
        </a>
    </div>

    <div class="detail-grid">
        <div class="glass-panel">
            @if ($item->image)
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="detail-photo">
            @else
                <div class="detail-photo">Tidak ada foto yang diunggah</div>
            @endif
        </div>

        <div class="glass-panel">
            <div style="display:flex; gap:0.5rem; flex-wrap:wrap; margin-bottom:0.75rem;">
                <span style="padding:0.28rem 0.85rem; border-radius:999px; font-size:0.76rem; font-weight:700; {{ $isFound ? 'background:#d1fae5;color:#065f46;' : 'background:#fee2e2;color:#b91c1c;' }}">
                    {{ $isFound ? 'Barang Ditemukan' : 'Barang Hilang' }}
                </span>
                <span style="padding:0.28rem 0.85rem; border-radius:999px; font-size:0.76rem; font-weight:700; background:#ffffff; color:#111827;">
                    Status: {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                </span>
            </div>

            <h2 style="font-size:1.55rem; font-weight:800; color:#111111; letter-spacing:-0.02em;">
                {{ $item->title }}
            </h2>

            <table class="meta-table">
                <tr>
                    <td>Kategori</td>
                    <td>{{ $item->category->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Lokasi</td>
                    <td>{{ $item->location }}</td>
                </tr>
                <tr>
                    <td>Tanggal Kejadian</td>
                    <td>{{ \Carbon\Carbon::parse($item->date_event)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td>Dilaporkan oleh</td>
                    <td>{{ $item->user->name ?? 'Warga Kampus' }}</td>
                </tr>
            </table>

            <div class="field-label">Deskripsi Barang</div>
            <div class="desc-box">{{ $item->description }}</div>

            @if ($canClaim)
                <a href="{{ route('claims.create', ['campus' => $campusSlug, 'item' => $item]) }}"
                   class="btn-black"
                   style="width:100%;">
                    Ajukan Klaim Kepemilikan
                </a>
            @elseif (auth()->check() && auth()->id() === $item->user_id)
                <div style="background:#ffffff; padding:0.85rem 1.1rem; border-radius:16px; font-size:0.84rem; color:#4b5563; text-align:center;">
                    Ini adalah laporan yang kamu buat sendiri.
                </div>
            @elseif (!auth()->check() && $isFound && $item->status === 'approved')
                <a href="{{ route('login', ['campus' => $campusSlug]) }}" class="btn-black" style="width:100%;">
                    Masuk Akun untuk Mengajukan Klaim
                </a>
            @endif
        </div>
    </div>
@endsection