@extends('layouts.app')

@section('title', 'Moderasi & Metrik Admin — Lost & Found')

@push('styles')
<style>
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .metric-card {
        background: rgba(244, 247, 243, 0.85);
        backdrop-filter: blur(18px);
        border: 1px solid rgba(255, 255, 255, 0.78);
        border-radius: 24px;
        padding: 1.4rem 1.65rem;
        box-shadow: 0 18px 36px -15px rgba(22, 42, 30, 0.1);
    }
    .metric-label {
        font-size: 0.84rem;
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 0.45rem;
    }
    .metric-value {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: #111111;
    }
    .metric-value.green { color: #059669; }

    /* Bar Chart Minimalis sesuai mockup */
    .chart-box {
        height: 150px;
        display: flex;
        align-items: flex-end;
        justify-content: space-around;
        padding: 1.25rem 2rem 0.5rem;
        gap: 1.25rem;
    }
    .bar-col {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        flex: 1;
        max-width: 56px;
    }
    .bar-fill {
        width: 100%;
        background: #111111;
        border-radius: 10px 10px 4px 4px;
        transition: height 0.3s ease;
    }
    .bar-caption {
        font-size: 0.74rem;
        font-weight: 700;
        color: #4b5563;
    }

    /* Tabel Antrean Moderasi */
    .mod-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0.75rem;
    }
    .mod-table tr {
        border-bottom: 1px solid rgba(17, 24, 39, 0.08);
    }
    .mod-table tr:last-child {
        border-bottom: none;
    }
    .mod-table td {
        padding: 0.95rem 0.65rem;
        font-size: 0.88rem;
        vertical-align: middle;
    }
    .type-pill {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 700;
    }
    .pill-lost { background: #fee2e2; color: #b91c1c; }
    .pill-found { background: #d1fae5; color: #065f46; }

    .btn-mini-black {
        padding: 0.45rem 1rem;
        border-radius: 999px;
        border: none;
        background: #111111;
        color: #ffffff;
        font-family: inherit;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
    }
    .btn-mini-reject {
        padding: 0.45rem 1rem;
        border-radius: 999px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #b91c1c;
        font-family: inherit;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
    }

    /* Modal Tolak */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.45);
        backdrop-filter: blur(4px);
        z-index: 50;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .modal-backdrop.open { display: flex; }
    .modal-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 1.75rem;
        max-width: 420px;
        width: 100%;
        box-shadow: 0 24px 48px rgba(0,0,0,0.2);
    }

    @media (max-width: 820px) {
        .metrics-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
    @php
        $campusParam = request()->route('campus');
        $campusSlug = is_object($campusParam)
            ? $campusParam->slug
            : ($campusParam ?: ($currentCampus->slug ?? request()->segment(2)));

        $totalItems = $stats['total_items'] ?? 0;
        $returnedItems = $stats['returned_items'] ?? 0;
        $pendingClaims = $stats['pending_claims'] ?? 0;
        $lostItems = $stats['lost_items'] ?? 0;
        $foundItems = $stats['found_items'] ?? 0;
        $maxStat = max($totalItems, $lostItems, $foundItems, $returnedItems, $pendingClaims, 1);
    @endphp

    <div class="page-header">
        <h1 class="page-title">Moderasi &amp; Metrik</h1>
        <a href="{{ route('admin.reports', ['campus' => $campusSlug]) }}" class="btn-black" style="padding:0.65rem 1.25rem; font-size:0.84rem;">
            Semua Laporan →
        </a>
    </div>

    {{-- 3 Kartu Metrik --}}
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-label">Total Laporan</div>
            <div class="metric-value">{{ str_pad((string) $totalItems, 4, '0', STR_PAD_LEFT) }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Laporan Selesai</div>
            <div class="metric-value green">{{ str_pad((string) $returnedItems, 4, '0', STR_PAD_LEFT) }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Klaim Aktif</div>
            <div class="metric-value">{{ str_pad((string) $pendingClaims, 3, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    {{-- Ringkasan Grafik --}}
    <div class="glass-panel" style="margin-bottom:1.5rem;">
        <h2 style="font-size:1rem; font-weight:800; color:#111111;">Ringkasan Status Laporan</h2>
        <div class="chart-box">
            @foreach ([
                ['label' => 'Hilang', 'val' => $lostItems],
                ['label' => 'Temuan', 'val' => $foundItems],
                ['label' => 'Klaim', 'val' => $pendingClaims],
                ['label' => 'Selesai', 'val' => $returnedItems],
                ['label' => 'Total', 'val' => $totalItems],
            ] as $bar)
                @php
                    $heightPx = max(18, (int) round(($bar['val'] / $maxStat) * 105));
                @endphp
                <div class="bar-col">
                    <span class="bar-caption">{{ $bar['val'] }}</span>
                    <div class="bar-fill" style="height: {{ $heightPx }}px;"></div>
                    <span class="bar-caption">{{ $bar['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Antrean Moderasi --}}
    <div class="glass-panel">
        <h2 style="font-size:1rem; font-weight:800; color:#111111;">Antrean Moderasi</h2>

        @if (($recentReports ?? collect())->isEmpty())
            <p style="font-size:0.86rem; color:#4b5563; margin-top:0.75rem;">Belum ada laporan terbaru.</p>
        @else
            <div style="overflow-x:auto;">
                <table class="mod-table">
                    <tbody>
                        @foreach ($recentReports as $item)
                            <tr>
                                <td style="font-weight:700; color:#111111;">
                                    <a href="{{ route('items.show', ['campus' => $campusSlug, 'item' => $item]) }}" style="color:inherit; text-decoration:none;">
                                        {{ $item->title }}
                                    </a>
                                </td>
                                <td>
                                    @if (in_array($item->type, ['lost', 'hilang']))
                                        <span class="type-pill pill-lost">Hilang</span>
                                    @else
                                        <span class="type-pill pill-found">Ditemukan</span>
                                    @endif
                                </td>
                                <td style="color:#374151;">{{ $item->user->name ?? '-' }}</td>
                                <td style="color:#6b7280;">{{ \Carbon\Carbon::parse($item->date_event)->translatedFormat('d M Y') }}</td>
                                <td style="text-align:right; white-space:nowrap;">
                                    @if ($item->status === 'pending')
                                        <form action="{{ route('admin.reports.status', ['campus' => $campusSlug, 'item' => $item]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn-mini-black">Setujui</button>
                                        </form>
                                        <button type="button"
                                                class="btn-mini-reject"
                                                onclick="openRejectModal('{{ route('admin.reports.status', ['campus' => $campusSlug, 'item' => $item]) }}', '{{ addslashes($item->title) }}')">
                                            Tolak
                                        </button>
                                    @else
                                        <span style="font-size:0.78rem; font-weight:700; color:#4b5563;">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Modal Alasan Penolakan --}}
    <div class="modal-backdrop" id="rejectModal">
        <div class="modal-card">
            <h3 style="font-size:1.1rem; font-weight:800; margin-bottom:0.35rem;">Tolak Laporan</h3>
            <p id="rejectItemTitle" style="font-size:0.84rem; color:#4b5563; margin-bottom:1rem;"></p>

            <form id="rejectForm" method="POST">
                @csrf
                <input type="hidden" name="status" value="rejected">
                <div style="margin-bottom:1rem;">
                    <label for="rejection_reason" class="field-label">Alasan penolakan</label>
                    <textarea id="rejection_reason" name="rejection_reason" class="area-input" placeholder="Contoh: Foto buram atau informasi tidak lengkap..." required></textarea>
                </div>
                <div style="display:flex; gap:0.65rem; justify-content:flex-end;">
                    <button type="button" onclick="closeRejectModal()" class="btn-mini-reject" style="color:#374151; padding:0.65rem 1.2rem;">Batal</button>
                    <button type="submit" class="btn-mini-black" style="padding:0.65rem 1.2rem;">Kirim Penolakan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openRejectModal(actionUrl, title) {
        document.getElementById('rejectForm').action = actionUrl;
        document.getElementById('rejectItemTitle').textContent = 'Barang: ' + title;
        document.getElementById('rejectModal').classList.add('open');
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.remove('open');
    }
</script>
@endpush