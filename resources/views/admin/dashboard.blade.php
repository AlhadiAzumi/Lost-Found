@extends('layouts.app')

@section('title', 'Dashboard Petugas | Lost & Found')

@section('content')
<div class="admin-dashboard">
    <header class="admin-dashboard__header">
        <div class="admin-dashboard__intro">
            <div class="admin-dashboard__badge">
                <x-badge icon="shield-check" color="#a3e636">Panel Satpam / Admin</x-badge>
            </div>
            <h1>Dashboard Pengawasan</h1>
            <p>Pantau laporan, klaim, dan barang yang telah dikembalikan.</p>
        </div>
    </header>

    @include('admin.navigation')

    @if (session('success'))
        <div class="card panel panel--mint admin-dashboard__notice">
            <strong>✓ Berhasil!</strong> {{ session('success') }}
        </div>
    @endif

    <section class="admin-dashboard__stats" aria-label="Ringkasan laporan">
        <article class="admin-stat">
            <span class="admin-stat__label">Total laporan</span>
            <strong class="admin-stat__value">{{ $stats['total_items'] }}</strong>
        </article>
        <article class="admin-stat admin-stat--lost">
            <span class="admin-stat__label">Barang hilang</span>
            <strong class="admin-stat__value">{{ $stats['lost_items'] }}</strong>
        </article>
        <article class="admin-stat admin-stat--found">
            <span class="admin-stat__label">Barang ditemukan</span>
            <strong class="admin-stat__value">{{ $stats['found_items'] }}</strong>
        </article>
        <article class="admin-stat admin-stat--pending">
            <span class="admin-stat__label">Klaim tertunda</span>
            <strong class="admin-stat__value">{{ $stats['pending_claims'] }}</strong>
        </article>
        <article class="admin-stat admin-stat--returned">
            <span class="admin-stat__label">Dikembalikan</span>
            <strong class="admin-stat__value">{{ $stats['returned_items'] }}</strong>
        </article>
    </section>

    <div class="admin-dashboard__panels">
        <section class="admin-dashboard__panel">
            <div class="admin-dashboard__panel-header">
                <h2>Klaim menunggu</h2>
                <a href="{{ route('admin.claims') }}">Lihat semua <span aria-hidden="true">→</span></a>
            </div>

            @if ($recentClaims->isEmpty())
                <p class="admin-dashboard__empty">Tidak ada klaim yang perlu ditinjau saat ini.</p>
            @else
                <div class="admin-dashboard__list">
                    @foreach ($recentClaims as $claim)
                        <article class="admin-dashboard__list-item">
                            <div class="admin-dashboard__item-copy">
                                <strong>{{ $claim->item->title ?? 'Barang' }}</strong>
                                <span>Pengklaim: {{ $claim->user->name ?? 'Pengguna' }}</span>
                            </div>
                            <a href="{{ route('admin.claims') }}" class="admin-dashboard__item-action">Periksa</a>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="admin-dashboard__panel">
            <div class="admin-dashboard__panel-header">
                <h2>Laporan terbaru</h2>
                <a href="{{ route('admin.reports') }}">Lihat semua <span aria-hidden="true">→</span></a>
            </div>

            @if ($recentReports->isEmpty())
                <p class="admin-dashboard__empty">Belum ada laporan masuk.</p>
            @else
                <div class="admin-dashboard__list">
                    @foreach ($recentReports as $item)
                        <article class="admin-dashboard__list-item">
                            <div class="admin-dashboard__item-copy">
                                <strong>{{ $item->title }}</strong>
                                <span>{{ $item->location }}</span>
                            </div>
                            <x-badge icon="circle-check" color="#d1fae8">{{ ucfirst($item->status) }}</x-badge>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
