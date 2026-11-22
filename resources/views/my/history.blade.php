@extends('layouts.app')

@section('title', 'Riwayat Laporan & Klaim — Lost & Found')

@push('styles')
<style>
    .tab-bar {
        display: inline-flex;
        gap: 0.45rem;
        background: rgba(244, 247, 243, 0.85);
        backdrop-filter: blur(18px);
        padding: 0.38rem;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.8);
        margin-bottom: 1.5rem;
    }
    .tab-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1.3rem;
        border-radius: 999px;
        font-size: 0.86rem;
        font-weight: 700;
        color: #374151;
        text-decoration: none;
        transition: all 0.18s ease;
    }
    .tab-pill:hover {
        background: rgba(255, 255, 255, 0.75);
        color: #111111;
    }
    .tab-pill.active {
        background: #111111;
        color: #ffffff;
        box-shadow: 0 8px 18px -6px rgba(17, 17, 17, 0.35);
    }
    .tab-count {
        font-size: 0.74rem;
        padding: 0.14rem 0.55rem;
        border-radius: 999px;
        background: rgba(17, 24, 39, 0.1);
    }
    .tab-pill.active .tab-count {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }

    /* Daftar Kartu Riwayat */
    .history-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .history-card {
        background: rgba(244, 247, 243, 0.86);
        backdrop-filter: blur(18px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 24px;
        padding: 1.25rem 1.45rem;
        display: grid;
        grid-template-columns: 96px 1fr auto;
        gap: 1.25rem;
        align-items: center;
        box-shadow: 0 14px 30px -15px rgba(22, 42, 30, 0.1);
    }
    .history-thumb {
        width: 96px;
        height: 96px;
        border-radius: 18px;
        object-fit: cover;
        background: linear-gradient(135deg, #a9cbb2, #8ab496);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        font-weight: 700;
        color: #1f2937;
        text-align: center;
        padding: 0.4rem;
    }

    .badge-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        margin-bottom: 0.45rem;
    }
    .status-pill {
        padding: 0.22rem 0.75rem;
        border-radius: 999px;
        font-size: 0.73rem;
        font-weight: 700;
    }
    .st-pending { background: #fef3c7; color: #92400e; }
    .st-approved { background: #d1fae5; color: #065f46; }
    .st-progress { background: #dbeafe; color: #1e40af; }
    .st-returned { background: #e5e7eb; color: #1f2937; }
    .st-rejected { background: #fee2e2; color: #991b1b; }

    .note-callout {
        margin-top: 0.6rem;
        padding: 0.65rem 0.9rem;
        border-radius: 14px;
        background: #ffffff;
        font-size: 0.8rem;
        color: #374151;
    }

    /* Tiket Kode Serah Terima */
    .handover-ticket {
        margin-top: 0.75rem;
        background: #111111;
        color: #ffffff;
        border-radius: 18px;
        padding: 0.9rem 1.15rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .code-value {
        font-size: 1.1rem;
        font-weight: 800;
        letter-spacing: 0.08em;
    }
    .btn-copy {
        padding: 0.45rem 0.95rem;
        border-radius: 999px;
        border: none;
        background: #ffffff;
        color: #111111;
        font-family: inherit;
        font-size: 0.76rem;
        font-weight: 700;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .history-card {
            grid-template-columns: 1fr;
        }
        .history-thumb {
            width: 100%;
            height: 150px;
        }
    }
</style>
@endpush

@section('content')
    @php
        $campusParam = request()->route('campus');
        $campusSlug = is_object($campusParam)
            ? $campusParam->slug
            : ($campusParam ?: ($currentCampus->slug ?? request()->segment(2)));

        $activeTab = ($tab ?? 'reports') === 'claims' ? 'claims' : 'reports';
    @endphp

    <div class="page-header">
        <h1 class="page-title">Riwayat Aktivitas Saya</h1>
    </div>

    {{-- Navigasi Tab --}}
    <div class="tab-bar">
        <a href="{{ route('my.history', ['campus' => $campusSlug, 'tab' => 'reports']) }}"
           class="tab-pill {{ $activeTab === 'reports' ? 'active' : '' }}">
            <span>Laporan Saya</span>
            <span class="tab-count">{{ $reports->count() }}</span>
        </a>

        <a href="{{ route('my.history', ['campus' => $campusSlug, 'tab' => 'claims']) }}"
           class="tab-pill {{ $activeTab === 'claims' ? 'active' : '' }}">
            <span>Klaim Saya</span>
            <span class="tab-count">{{ $claims->count() }}</span>
        </a>
    </div>

    {{-- TAB 1: LAPORAN SAYA --}}
    @if ($activeTab === 'reports')
        @if ($reports->isEmpty())
            <div class="glass-panel" style="text-align:center; padding:3rem 1.5rem;">
                <p style="font-size:1.05rem; font-weight:800; color:#111111;">Kamu belum pernah membuat laporan.</p>
                <p style="font-size:0.85rem; color:#4b5563; margin:0.35rem 0 1.25rem;">
                    Laporkan barang yang hilang atau barang yang kamu temukan di area kampus.
                </p>
                <div style="display:flex; gap:0.75rem; justify-content:center; flex-wrap:wrap;">
                    <a href="{{ route('lost.create', ['campus' => $campusSlug]) }}"
                       style="padding:0.75rem 1.35rem; border-radius:999px; background:#ffffff; color:#111111; font-weight:700; font-size:0.86rem; text-decoration:none;">
                        + Lapor Hilang
                    </a>
                    <a href="{{ route('found.create', ['campus' => $campusSlug]) }}" class="btn-black" style="padding:0.75rem 1.35rem; font-size:0.86rem;">
                        + Lapor Temuan
                    </a>
                </div>
            </div>
        @else
            <div class="history-list">
                @foreach ($reports as $item)
                    @php
                        $isFound = in_array($item->type, ['found', 'ditemukan']);
                    @endphp
                    <div class="history-card">
                        @if ($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="history-thumb">
                        @else
                            <div class="history-thumb">Tanpa Foto</div>
                        @endif

                        <div>
                            <div class="badge-group">
                                <span class="status-pill {{ $isFound ? 'st-approved' : 'st-rejected' }}">
                                    {{ $isFound ? 'Temuan' : 'Hilang' }}
                                </span>

                                @if ($item->status === 'pending')
                                    <span class="status-pill st-pending">Menunggu Moderasi</span>
                                @elseif ($item->status === 'approved')
                                    <span class="status-pill st-approved">Tayang di Katalog</span>
                                @elseif ($item->status === 'claim_in_progress')
                                    <span class="status-pill st-progress">Dalam Proses Klaim</span>
                                @elseif ($item->status === 'returned')
                                    <span class="status-pill st-returned">Sudah Dikembalikan</span>
                                @else
                                    <span class="status-pill st-rejected">Ditolak</span>
                                @endif
                            </div>

                            <h3 style="font-size:1.05rem; font-weight:800; color:#111111; margin-bottom:0.25rem;">
                                {{ $item->title }}
                            </h3>
                            <p style="font-size:0.82rem; color:#4b5563;">
                                {{ $item->category->name ?? 'Umum' }} • 📍 {{ $item->location }} • 📅 {{ \Carbon\Carbon::parse($item->date_event)->translatedFormat('d M Y') }}
                            </p>

                            @if ($item->status === 'rejected' && $item->rejection_reason)
                                <div class="note-callout" style="border-left:3px solid #dc2626; color:#991b1b;">
                                    <strong>Alasan Penolakan:</strong> {{ $item->rejection_reason }}
                                </div>
                            @endif
                        </div>

                        <div>
                            <a href="{{ route('items.show', ['campus' => $campusSlug, 'item' => $item]) }}"
                               class="btn-black"
                               style="padding:0.65rem 1.2rem; font-size:0.82rem;">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    {{-- TAB 2: KLAIM SAYA --}}
    @else
        @if ($claims->isEmpty())
            <div class="glass-panel" style="text-align:center; padding:3rem 1.5rem;">
                <p style="font-size:1.05rem; font-weight:800; color:#111111;">Belum ada pengajuan klaim.</p>
                <p style="font-size:0.85rem; color:#4b5563; margin:0.35rem 0 1.25rem;">
                    Cari barangmu di Feed Publik dan ajukan klaim kepemilikan.
                </p>
                <a href="{{ route('catalog.index', ['campus' => $campusSlug]) }}" class="btn-black" style="padding:0.75rem 1.4rem; font-size:0.86rem;">
                    Buka Feed Publik
                </a>
            </div>
        @else
            <div class="history-list">
                @foreach ($claims as $claim)
                    <div class="history-card">
                        @if ($claim->item && $claim->item->image)
                            <img src="{{ asset('storage/' . $claim->item->image) }}" alt="{{ $claim->item->title }}" class="history-thumb">
                        @else
                            <div class="history-thumb">Barang Klaim</div>
                        @endif

                        <div>
                            <div class="badge-group">
                                @if ($claim->status === 'pending')
                                    <span class="status-pill st-pending">Menunggu Verifikasi Petugas</span>
                                @elseif ($claim->status === 'approved')
                                    <span class="status-pill st-approved">Klaim Disetujui</span>
                                @elseif ($claim->status === 'completed')
                                    <span class="status-pill st-returned">Barang Sudah Diterima</span>
                                @else
                                    <span class="status-pill st-rejected">Klaim Ditolak</span>
                                @endif
                            </div>

                            <h3 style="font-size:1.05rem; font-weight:800; color:#111111; margin-bottom:0.25rem;">
                                {{ $claim->item->title ?? 'Barang tidak tersedia' }}
                            </h3>
                            <p style="font-size:0.82rem; color:#4b5563;">
                                Jawaban verifikasi: <strong>"{{ $claim->security_answer }}"</strong>
                            </p>

                            @if ($claim->admin_note)
                                <div class="note-callout">
                                    <strong>Catatan Petugas:</strong> {{ $claim->admin_note }}
                                </div>
                            @endif

                            @if ($claim->status === 'approved' && $claim->handover_code)
                                <div class="handover-ticket">
                                    <div>
                                        <div style="font-size:0.72rem; opacity:0.78;">Tunjukkan kode ini di Pos Satpam:</div>
                                        <div class="code-value" id="code-{{ $claim->id }}">{{ $claim->handover_code }}</div>
                                    </div>
                                    <button type="button" class="btn-copy" onclick="copyHandoverCode('{{ $claim->handover_code }}', this)">
                                        Salin Kode
                                    </button>
                                </div>
                            @endif
                        </div>

                        <div style="display:flex; flex-direction:column; gap:0.5rem;">
                            @if ($claim->item)
                                <a href="{{ route('items.show', ['campus' => $campusSlug, 'item' => $claim->item]) }}"
                                   class="btn-black"
                                   style="padding:0.62rem 1.15rem; font-size:0.8rem;">
                                    Detail Barang
                                </a>
                            @endif
                            @if ($claim->proof_image)
                                <a href="{{ route('claims.proof', ['campus' => $campusSlug, 'claim' => $claim]) }}"
                                   target="_blank"
                                   style="padding:0.58rem 1.1rem; border-radius:999px; background:#ffffff; color:#111111; font-size:0.78rem; font-weight:700; text-decoration:none; text-align:center;">
                                    Lihat Bukti
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
@endsection

@push('scripts')
<script>
    function copyHandoverCode(code, btn) {
        navigator.clipboard.writeText(code).then(() => {
            const original = btn.textContent;
            btn.textContent = 'Tersalin!';
            setTimeout(() => btn.textContent = original, 1800);
        });
    }
</script>
@endpush