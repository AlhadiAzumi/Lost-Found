@extends('layouts.app')

@section('title', 'Moderasi Laporan | Lost & Found')

@section('content')
<div class="container" style="max-width: 1040px; margin: 36px auto; padding: 0 16px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <a href="{{ route('admin.dashboard') }}" style="font-size: 13px; font-weight: 700; color: #111;">&larr; Kembali ke Dashboard</a>
            <h1 style="font-size: 24px; font-weight: 800; margin: 4px 0 0; color: #111;">Moderasi Laporan Masuk</h1>
        </div>
    </div>

    @include('admin.navigation')

    @if (session('success'))
        <div class="card panel panel--mint" style="margin-bottom: 24px; padding: 16px;">
            <strong>✓ Berhasil!</strong> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="card panel" style="margin-bottom: 24px; padding: 16px; background: #fee2e2;">
            <strong>✕ Perhatian:</strong> {{ session('error') }}
        </div>
    @endif

    <div class="card" style="padding: 24px; border: 2px solid #111; border-radius: 18px; box-shadow: 4px 4px 0 #111; background: #fff;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
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
                                <form method="POST" action="{{ route('admin.reports.status', $item) }}" style="display: inline-flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end;">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <input type="text" name="rejection_reason" placeholder="Alasan penolakan" required class="input" style="max-width: 180px; padding: 6px 8px; font-size: 12px;">
                                    <button type="submit" class="btn btn--ghost" style="padding: 6px 12px; font-size: 12px; color: #dc2626;">Tolak</button>
                                </form>
                                <form method="POST" action="{{ route('admin.reports.status', $item) }}" style="display: inline-flex;">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn--primary" style="padding: 6px 12px; font-size: 12px;">Setujui</button>
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

        <div style="margin-top: 20px;">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
