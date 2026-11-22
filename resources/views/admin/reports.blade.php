@extends('layouts.app')

@section('title', 'Daftar Moderasi Laporan — Lost & Found')

@section('content')
    @php
        $campusParam = request()->route('campus');
        $campusSlug = is_object($campusParam)
            ? $campusParam->slug
            : ($campusParam ?: ($currentCampus->slug ?? request()->segment(2)));
    @endphp

    <div class="page-header">
        <h1 class="page-title">Moderasi Semua Laporan</h1>
        <a href="{{ route('admin.dashboard', ['campus' => $campusSlug]) }}"
           style="padding:0.65rem 1.25rem; border-radius:999px; background:#ffffff; color:#111111; font-weight:700; font-size:0.84rem; text-decoration:none;">
            ← Kembali ke Metrik
        </a>
    </div>

    <div class="glass-panel">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left; font-size:0.78rem; color:#4b5563; border-bottom:1px solid rgba(17,24,39,0.1);">
                        <th style="padding:0.75rem 0.5rem;">Barang</th>
                        <th style="padding:0.75rem 0.5rem;">Tipe</th>
                        <th style="padding:0.75rem 0.5rem;">Kategori</th>
                        <th style="padding:0.75rem 0.5rem;">Pelapor</th>
                        <th style="padding:0.75rem 0.5rem;">Status</th>
                        <th style="padding:0.75rem 0.5rem; text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr style="border-bottom:1px solid rgba(17,24,39,0.07); font-size:0.86rem;">
                            <td style="padding:0.9rem 0.5rem; font-weight:700;">
                                <a href="{{ route('items.show', ['campus' => $campusSlug, 'item' => $item]) }}" style="color:#111111; text-decoration:none;">
                                    {{ $item->title }}
                                </a>
                            </td>
                            <td style="padding:0.9rem 0.5rem;">
                                <span style="padding:0.22rem 0.7rem; border-radius:999px; font-size:0.74rem; font-weight:700; {{ in_array($item->type, ['lost','hilang']) ? 'background:#fee2e2;color:#b91c1c;' : 'background:#d1fae5;color:#065f46;' }}">
                                    {{ in_array($item->type, ['lost','hilang']) ? 'Hilang' : 'Ditemukan' }}
                                </span>
                            </td>
                            <td style="padding:0.9rem 0.5rem; color:#4b5563;">{{ $item->category->name ?? '-' }}</td>
                            <td style="padding:0.9rem 0.5rem; color:#374151;">{{ $item->user->name ?? '-' }}</td>
                            <td style="padding:0.9rem 0.5rem; font-weight:700;">{{ ucfirst($item->status) }}</td>
                            <td style="padding:0.9rem 0.5rem; text-align:right; white-space:nowrap;">
                                @if ($item->status === 'pending')
                                    <form action="{{ route('admin.reports.status', ['campus' => $campusSlug, 'item' => $item]) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn-black" style="padding:0.42rem 0.95rem; font-size:0.76rem;">Setujui</button>
                                    </form>
                                @else
                                    <span style="color:#6b7280; font-size:0.78rem;">Sudah diproses</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:1.25rem;">
            {{ $items->links() }}
        </div>
    </div>
@endsection