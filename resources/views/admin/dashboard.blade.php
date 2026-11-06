@extends('layouts.app')

@section('title', 'Admin Dashboard — Campus Lost & Found')

@section('content')
<div class="container" style="max-width: 1040px; margin: 36px auto; padding: 0 16px;">
    {{-- Header Admin --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                <x-badge icon="shield-check" color="#a3e636">Panel Satpam / Admin</x-badge>
            </div>
            <h1 style="font-size: 26px; font-weight: 800; margin: 0; color: #111;">Dashboard Pengawasan Kampus</h1>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.reports') }}" class="btn btn--ghost">Moderasi Laporan</a>
            <a href="{{ route('admin.claims') }}" class="btn btn--ghost">Verifikasi Klaim</a>
            <a href="{{ route('admin.handovers') }}" class="btn btn--primary">🔑 Serah Terima</a>
        </div>
    </div>

    @if (session('success'))
        <div class="card panel panel--mint" style="margin-bottom: 24px; padding: 16px;">
            <strong>✓ Berhasil!</strong> {{ session('success') }}
        </div>
    @endif

    {{-- Kartu Statistik --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 32px;">
        <div class="card" style="padding: 20px; border: 2px solid #111; border-radius: 16px; box-shadow: 4px 4px 0 #111; background: #fff;">
            <span style="font-size: 13px; font-weight: 700; color: #6b7280;">TOTAL LAPORAN</span>
            <div style="font-size: 32px; font-weight: 900; margin-top: 6px; color: #111;">{{ $stats['total_items'] }}</div>
        </div>
        <div class="card" style="padding: 20px; border: 2px solid #111; border-radius: 16px; box-shadow: 4px 4px 0 #111; background: #fff;">
            <span style="font-size: 13px; font-weight: 700; color: #dc2626;">BARANG HILANG</span>
            <div style="font-size: 32px; font-weight: 900; margin-top: 6px; color: #dc2626;">{{ $stats['lost_items'] }}</div>
        </div>
        <div class="card" style="padding: 20px; border: 2px solid #111; border-radius: 16px; box-shadow: 4px 4px 0 #111; background: #fff;">
            <span style="font-size: 13px; font-weight: 700; color: #16a34a;">BARANG DITEMUKAN</span>
            <div style="font-size: 32px; font-weight: 900; margin-top: 6px; color: #16a34a;">{{ $stats['found_items'] }}</div>
        </div>
        <div class="card" style="padding: 20px; border: 2px solid #111; border-radius: 16px; box-shadow: 4px 4px 0 #111; background: #fef3c7;">
            <span style="font-size: 13px; font-weight: 700; color: #d97706;">KLAIM TERTUNDA</span>
            <div style="font-size: 32px; font-weight: 900; margin-top: 6px; color: #d97706;">{{ $stats['pending_claims'] }}</div>
        </div>
        <div class="card" style="padding: 20px; border: 2px solid #111; border-radius: 16px; box-shadow: 4px 4px 0 #111; background: #bef264;">
            <span style="font-size: 13px; font-weight: 700; color: #111;">DIKEMBALIKAN</span>
            <div style="font-size: 32px; font-weight: 900; margin-top: 6px; color: #111;">{{ $stats['returned_items'] }}</div>
        </div>
    </div>

    {{-- Dua Panel Berdampingan --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 24px;">
        {{-- Antrean Klaim Baru --}}
        <div class="card" style="padding: 24px; border: 2px solid #111; border-radius: 18px; box-shadow: 4px 4px 0 #111; background: #fff;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="margin: 0; font-size: 18px; font-weight: 800;">Klaim Menunggu Verifikasi</h3>
                <a href="{{ route('admin.claims') }}" style="font-size: 13px; font-weight: 700; color: #111;">Lihat Semua &rarr;</a>
            </div>

            @if ($recentClaims->isEmpty())
                <p style="color: #6b7280; font-size: 14px; margin: 20px 0;">Tidak ada klaim yang sedang menunggu tindakan.</p>
            @else
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach ($recentClaims as $claim)
                        <div style="padding: 12px; border: 1.5px solid #e5e7eb; border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong style="display: block; font-size: 14px;">{{ $claim->item->title ?? 'Barang' }}</strong>
                                <span style="font-size: 12px; color: #6b7280;">Oleh: {{ $claim->user->name ?? 'User' }}</span>
                            </div>
                            <a href="{{ route('admin.claims') }}" class="btn btn--primary" style="font-size: 12px; padding: 6px 12px;">Periksa</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Laporan Terbaru --}}
        <div class="card" style="padding: 24px; border: 2px solid #111; border-radius: 18px; box-shadow: 4px 4px 0 #111; background: #fff;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="margin: 0; font-size: 18px; font-weight: 800;">Laporan Masuk Terbaru</h3>
                <a href="{{ route('admin.reports') }}" style="font-size: 13px; font-weight: 700; color: #111;">Lihat Semua &rarr;</a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach ($recentReports as $item)
                    <div style="padding: 12px; border: 1.5px solid #e5e7eb; border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="display: block; font-size: 14px;">{{ $item->title }}</strong>
                            <span style="font-size: 12px; color: #6b7280;">📍 {{ $item->location }}</span>
                        </div>
                        <x-badge icon="circle-check" color="#d1fae8">{{ ucfirst($item->status) }}</x-badge>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

