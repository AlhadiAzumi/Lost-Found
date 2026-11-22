@extends('layouts.app')

@section('title', 'Serah Terima Barang — Lost & Found')

@push('styles')
<style>
    .handover-grid {
        display: grid;
        grid-template-columns: 1fr 1.15fr;
        gap: 1.5rem;
        align-items: start;
    }
    .code-search-form {
        display: flex;
        gap: 0.65rem;
        margin-top: 0.75rem;
    }
    .code-input {
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 800;
    }
    .claim-ticket {
        margin-top: 1.25rem;
        background: #ffffff;
        border-radius: 20px;
        padding: 1.25rem;
        border: 1.5px solid rgba(17, 17, 17, 0.1);
    }
    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px dashed rgba(17, 24, 39, 0.15);
        margin-bottom: 0.95rem;
    }
    .ticket-code {
        font-size: 1.05rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        color: #111111;
    }
    .ticket-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        margin-bottom: 0.55rem;
        gap: 1rem;
    }
    .ticket-row span:first-child { color: #6b7280; }
    .ticket-row span:last-child { font-weight: 700; color: #111111; text-align: right; }

    .history-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0.75rem;
    }
    .history-table th {
        text-align: left;
        font-size: 0.75rem;
        color: #6b7280;
        padding: 0.65rem 0.5rem;
        border-bottom: 1px solid rgba(17, 24, 39, 0.1);
    }
    .history-table td {
        padding: 0.85rem 0.5rem;
        font-size: 0.84rem;
        border-bottom: 1px solid rgba(17, 24, 39, 0.06);
        vertical-align: middle;
    }
    .status-done {
        display: inline-block;
        padding: 0.22rem 0.7rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        background: #d1fae5;
        color: #065f46;
    }

    @media (max-width: 960px) {
        .handover-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
    @php
        $campusParam = request()->route('campus');
        $campusSlug = is_object($campusParam)
            ? $campusParam->slug
            : ($campusParam ?: ($currentCampus->slug ?? request()->segment(2)));
    @endphp

    <div class="page-header">
        <h1 class="page-title">Serah Terima Pos Satpam</h1>
    </div>

    <div class="handover-grid">
        {{-- Kolom Kiri: Cari & Proses Kode Serah Terima --}}
        <div class="glass-panel">
            <h2 style="font-size:1.05rem; font-weight:800; color:#111111;">Verifikasi Kode Pengambilan</h2>
            <p style="font-size:0.82rem; color:#4b5563; margin-top:0.25rem;">
                Masukkan kode serah terima (contoh: <strong>LFP-XXXX-XXXX</strong>) yang ditunjukkan pemilik barang.
            </p>

            <form method="GET" action="{{ route('admin.handovers', ['campus' => $campusSlug]) }}" class="code-search-form">
                <input
                    type="text"
                    name="code"
                    value="{{ $searchCode ?? '' }}"
                    placeholder="LFP-XXXX-XXXX"
                    class="pill-input code-input"
                    required
                >
                <button type="submit" class="btn-black">Cek Kode</button>
            </form>

            @if (!empty($searchCode) && !$claimToHandover)
                <div style="margin-top:1.1rem; padding:0.95rem 1.1rem; border-radius:16px; background:#fee2e2; color:#991b1b; font-size:0.84rem; font-weight:600;">
                    Kode <strong>{{ strtoupper($searchCode) }}</strong> tidak ditemukan. Periksa kembali penulisan kode.
                </div>
            @elseif ($claimToHandover)
                <div class="claim-ticket">
                    <div class="ticket-header">
                        <span class="ticket-code">{{ $claimToHandover->handover_code }}</span>
                        <span style="padding:0.24rem 0.72rem; border-radius:999px; font-size:0.74rem; font-weight:700; {{ $claimToHandover->status === 'approved' ? 'background:#dbeafe;color:#1e40af;' : 'background:#d1fae5;color:#065f46;' }}">
                            {{ $claimToHandover->status === 'approved' ? 'Siap Diserahkan' : ucfirst($claimToHandover->status) }}
                        </span>
                    </div>

                    <div class="ticket-row">
                        <span>Nama Barang</span>
                        <span>{{ $claimToHandover->item->title ?? '-' }}</span>
                    </div>
                    <div class="ticket-row">
                        <span>Penerima / Pemilik</span>
                        <span>{{ $claimToHandover->user->name ?? '-' }}</span>
                    </div>
                    <div class="ticket-row">
                        <span>NIM / NIP</span>
                        <span>{{ $claimToHandover->user->nim_nip ?? '-' }}</span>
                    </div>
                    <div class="ticket-row" style="margin-bottom:0;">
                        <span>Lokasi Temuan</span>
                        <span>{{ $claimToHandover->item->location ?? '-' }}</span>
                    </div>

                    @if ($claimToHandover->status === 'approved')
                        <form action="{{ route('admin.handovers.process', ['campus' => $campusSlug, 'claim' => $claimToHandover]) }}"
                              method="POST"
                              style="margin-top:1.2rem; padding-top:1rem; border-top:1px solid rgba(17,24,39,0.08);">
                            @csrf
                            <input type="hidden" name="code" value="{{ $claimToHandover->handover_code }}">

                            <div style="margin-bottom:0.9rem;">
                                <label for="notes" class="field-label">Catatan Serah Terima (opsional)</label>
                                <textarea
                                    id="notes"
                                    name="notes"
                                    placeholder="Contoh: KTM penerima sudah dicek sesuai nama akun"
                                    class="area-input"
                                    style="min-height:74px; background:#f8fafc;"
                                ></textarea>
                            </div>

                            <button type="submit" class="btn-black" style="width:100%;">
                                Konfirmasi Serah Terima
                            </button>
                        </form>
                    @else
                        <div style="margin-top:1rem; padding:0.75rem 1rem; border-radius:14px; background:#f3f4f6; font-size:0.82rem; color:#374151; text-align:center;">
                            Klaim dengan kode ini sudah selesai diserahkan.
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Kolom Kanan: Riwayat Serah Terima Terbaru --}}
        <div class="glass-panel">
            <h2 style="font-size:1.05rem; font-weight:800; color:#111111;">Riwayat Serah Terima</h2>

            @if (($recentHandovers ?? collect())->isEmpty())
                <p style="font-size:0.85rem; color:#4b5563; margin-top:0.85rem;">
                    Belum ada barang yang tercatat diserahkan.
                </p>
            @else
                <div style="overflow-x:auto;">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Penerima</th>
                                <th>Petugas</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentHandovers as $handover)
                                <tr>
                                    <td style="font-weight:700; color:#111111;">
                                        {{ $handover->item->title ?? '-' }}
                                        <div><span class="status-done">Dikembalikan</span></div>
                                    </td>
                                    <td>{{ $handover->recipient->name ?? '-' }}</td>
                                    <td style="color:#4b5563;">{{ $handover->officer->name ?? '-' }}</td>
                                    <td style="color:#6b7280; white-space:nowrap;">
                                        {{ \Carbon\Carbon::parse($handover->handover_date)->translatedFormat('d M Y, H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($recentHandovers->hasPages())
                    <div style="margin-top:1rem;">
                        {{ $recentHandovers->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection