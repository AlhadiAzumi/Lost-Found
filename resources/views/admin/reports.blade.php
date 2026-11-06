@extends('layouts.app')

@section('title', 'Moderasi Laporan | Lost & Found')

@section('content')
<div class="admin-page">
    <header class="admin-page__header">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="admin-page__back">&larr; Dashboard</a>
            <h1 class="admin-page__title">Moderasi Laporan</h1>
            <p class="admin-page__description">Tinjau dan proses laporan barang yang masuk.</p>
        </div>
    </header>

    @include('admin.navigation')

    @if (session('success'))
        <div class="card panel panel--mint admin-page__notice">
            <strong>✓ Berhasil!</strong> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="card panel admin-page__notice admin-page__notice--error">
            <strong>✕ Perhatian:</strong> {{ session('error') }}
        </div>
    @endif

    <div class="admin-surface admin-reports">
        <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr style="border-bottom: 2px solid #111;">
                    <th style="padding: 12px 8px;">Barang</th>
                    <th style="padding: 12px 8px;">Tipe</th>
                    <th style="padding: 12px 8px;">Pelapor</th>
                    <th style="padding: 12px 8px;">Lokasi / Tanggal</th>
                    <th style="padding: 12px 8px;">Status</th>
                    <th style="padding: 12px 8px; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 14px 8px;">
                            <strong>{{ $item->title }}</strong>
                            <div style="font-size: 12px; color: #6b7280;">{{ $item->category->name ?? '-' }}</div>
                        </td>
                        <td style="padding: 14px 8px;">
                            <span style="font-weight: 700; color: {{ in_array($item->type, ['found', 'ditemukan']) ? '#16a34a' : '#dc2626' }}">
                                {{ ucfirst($item->type) }}
                            </span>
                        </td>
                        <td style="padding: 14px 8px;">{{ $item->user->name ?? 'Anonim' }}</td>
                        <td style="padding: 14px 8px;">
                            <div>{{ $item->location }}</div>
                            <div style="font-size: 12px; color: #6b7280;">{{ $item->date_event ? $item->date_event->format('d/m/Y') : '-' }}</div>
                        </td>
                        <td style="padding: 14px 8px;">
                            <x-badge icon="circle-check" color="#d1fae8">{{ ucfirst($item->status) }}</x-badge>
                        </td>
                        <td style="padding: 14px 8px; text-align: right;">
                            @if ($item->status === 'pending')
                                <form method="POST" action="{{ route('admin.reports.status', $item) }}" class="admin-report-actions">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <input type="text" name="rejection_reason" placeholder="Alasan penolakan" required class="input admin-report-actions__reason">
                                    <button type="submit" class="btn btn--ghost admin-report-actions__reject">Tolak</button>
                                </form>
                                <form method="POST" action="{{ route('admin.reports.status', $item) }}" class="admin-report-actions">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn--primary admin-report-actions__approve">Setujui</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 24px; text-align: center; color: #6b7280;">Tidak ada laporan ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <div class="admin-page__pagination">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
