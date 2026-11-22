@extends('layouts.app')

@section('title', 'Verifikasi Klaim Masuk — Lost & Found')

@push('styles')
<style>
    .verify-grid {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 1.5rem;
        align-items: start;
    }
    .claim-list {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        margin-top: 1rem;
    }
    .claim-selector {
        width: 100%;
        text-align: left;
        padding: 0.9rem 1.15rem;
        border-radius: 18px;
        border: 1.5px solid transparent;
        background: #ffffff;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.18s ease;
    }
    .claim-selector:hover {
        border-color: rgba(17, 17, 17, 0.25);
    }
    .claim-selector.active {
        background: #111111;
        color: #ffffff;
        box-shadow: 0 10px 22px -8px rgba(17, 17, 17, 0.35);
    }
    .claim-selector .top-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.5rem;
        font-weight: 700;
        font-size: 0.9rem;
    }
    .claim-selector .sub-row {
        font-size: 0.78rem;
        opacity: 0.78;
        margin-top: 0.25rem;
    }

    .status-chip {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
    }
    .chip-pending { background: #dbeafe; color: #1e40af; }
    .chip-approved { background: #d1fae5; color: #065f46; }
    .chip-rejected { background: #fee2e2; color: #991b1b; }
    .chip-completed { background: #e5e7eb; color: #1f2937; }

    /* Panel Detail Perbandingan */
    .detail-pane { display: none; }
    .detail-pane.active { display: block; }

    .compare-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin: 1rem 0;
    }
    .compare-box {
        background: #d1fae5;
        color: #064e3b;
        padding: 1rem 1.15rem;
        border-radius: 18px;
        font-size: 0.88rem;
        line-height: 1.55;
    }
    .compare-box.claimant {
        background: #ffffff;
        color: #111827;
        border: 1px solid rgba(17, 24, 39, 0.08);
    }
    .compare-label {
        font-size: 0.76rem;
        font-weight: 700;
        color: #4b5563;
        margin-bottom: 0.4rem;
    }

    .match-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.9rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        margin-bottom: 1.25rem;
    }
    .match-yes { background: #bbf7d0; color: #14532d; }
    .match-no { background: #fecaca; color: #7f1d1d; }

    .action-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.85rem;
        margin-top: 1.25rem;
    }
    .btn-reject {
        padding: 0.88rem 1.5rem;
        border-radius: 999px;
        border: 1.5px solid #e5e7eb;
        background: #ffffff;
        color: #b91c1c;
        font-family: inherit;
        font-size: 0.92rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.18s;
    }
    .btn-reject:hover {
        background: #fef2f2;
    }
    .code-banner {
        background: #111111;
        color: #ffffff;
        padding: 1rem 1.25rem;
        border-radius: 18px;
        margin-top: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    @media (max-width: 960px) {
        .verify-grid, .compare-grid { grid-template-columns: 1fr; }
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
        <h1 class="page-title">Verifikasi Klaim Masuk</h1>
    </div>

    @if ($claims->isEmpty())
        <div class="glass-panel" style="text-align:center; padding:3rem 1.5rem;">
            <p style="font-weight:700; font-size:1.05rem; color:#111827;">Belum ada pengajuan klaim masuk.</p>
            <p style="font-size:0.86rem; color:#4b5563; margin-top:0.35rem;">Daftar klaim dari mahasiswa akan muncul di halaman ini untuk diverifikasi petugas.</p>
        </div>
    @else
        <div class="verify-grid">
            {{-- Kolom Kiri: Daftar Klaim --}}
            <div class="glass-panel">
                <h2 style="font-size:1.05rem; font-weight:800; color:#111111;">
                    Klaim ({{ $claims->total() }})
                </h2>

                <div class="claim-list">
                    @foreach ($claims as $index => $claim)
                        <button
                            type="button"
                            class="claim-selector {{ $index === 0 ? 'active' : '' }}"
                            data-target="claim-pane-{{ $claim->id }}"
                        >
                            <div class="top-row">
                                <span>{{ $claim->user->name ?? 'Pengklaim' }}</span>
                                @if ($claim->status === 'pending')
                                    <span class="status-chip chip-pending">Baru</span>
                                @elseif ($claim->status === 'approved')
                                    <span class="status-chip chip-approved">Disetujui</span>
                                @elseif ($claim->status === 'completed')
                                    <span class="status-chip chip-completed">Selesai</span>
                                @else
                                    <span class="status-chip chip-rejected">Ditolak</span>
                                @endif
                            </div>
                            <div class="sub-row">
                                Barang: {{ $claim->item->title ?? '-' }}
                            </div>
                        </button>
                    @endforeach
                </div>

                @if ($claims->hasPages())
                    <div style="margin-top:1rem; font-size:0.82rem;">
                        {{ $claims->links() }}
                    </div>
                @endif
            </div>

            {{-- Kolom Kanan: Perbandingan Jawaban & Keputusan Satpam --}}
            <div class="glass-panel">
                @foreach ($claims as $index => $claim)
                    @php
                        $isMatch = $claim->item && $claim->item->matchesSecurityAnswer($claim->security_answer);
                    @endphp

                    <div id="claim-pane-{{ $claim->id }}" class="detail-pane {{ $index === 0 ? 'active' : '' }}">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;">
                            <div>
                                <h2 style="font-size:1.15rem; font-weight:800; color:#111111;">Perbandingan Jawaban</h2>
                                <p style="font-size:0.83rem; color:#4b5563; margin-top:0.2rem;">
                                    Pengklaim: <strong>{{ $claim->user->name ?? '-' }}</strong> ({{ $claim->user->nim_nip ?? '-' }}) • Barang: <strong>{{ $claim->item->title ?? '-' }}</strong>
                                </p>
                            </div>

                            @if ($claim->proof_image)
                                <a href="{{ route('claims.proof', ['campus' => $campusSlug, 'claim' => $claim]) }}"
                                   target="_blank"
                                   style="padding:0.5rem 1rem; border-radius:999px; background:#ffffff; color:#111111; font-size:0.8rem; font-weight:700; text-decoration:none; border:1px solid rgba(17,17,17,0.12);">
                                    Lihat Bukti Lampiran ↗
                                </a>
                            @endif
                        </div>

                        <div class="compare-grid">
                            <div>
                                <div class="compare-label">Pertanyaan Keamanan Barang</div>
                                <div class="compare-box">
                                    {{ $claim->item->security_question ?? '-' }}
                                </div>
                            </div>

                            <div>
                                <div class="compare-label">Jawaban Pengklaim</div>
                                <div class="compare-box claimant">
                                    {{ $claim->security_answer }}
                                </div>
                            </div>
                        </div>

                        @if ($isMatch)
                            <div class="match-pill match-yes">
                                ✓ Kecocokan tinggi (Hash jawaban sesuai)
                            </div>
                        @else
                            <div class="match-pill match-no">
                                ✕ Jawaban belum cocok dengan hash penemu
                            </div>
                        @endif

                        @if ($claim->status === 'pending')
                            <form action="{{ route('admin.claims.verify', ['campus' => $campusSlug, 'claim' => $claim]) }}" method="POST">
                                @csrf

                                <div>
                                    <label for="admin_note_{{ $claim->id }}" class="field-label">
                                        Catatan Petugas (wajib diisi jika menolak klaim)
                                    </label>
                                    <textarea
                                        id="admin_note_{{ $claim->id }}"
                                        name="admin_note"
                                        placeholder="Tulis catatan verifikasi atau alasan penolakan..."
                                        class="area-input"
                                        style="min-height:80px;"
                                    >{{ old('admin_note') }}</textarea>
                                </div>

                                <div class="action-row">
                                    <button type="submit" name="action" value="approve" class="btn-black">
                                        Setujui
                                    </button>
                                    <button type="submit" name="action" value="reject" class="btn-reject">
                                        Tolak
                                    </button>
                                </div>
                            </form>
                        @else
                            <div style="background:#ffffff; padding:1rem 1.2rem; border-radius:18px; font-size:0.86rem;">
                                <strong>Status:</strong> {{ ucfirst($claim->status) }}
                                @if ($claim->admin_note)
                                    <div style="margin-top:0.35rem; color:#4b5563;">Catatan: {{ $claim->admin_note }}</div>
                                @endif
                            </div>

                            @if ($claim->handover_code)
                                <div class="code-banner">
                                    <span style="font-size:0.84rem; opacity:0.85;">Kode Serah Terima:</span>
                                    <strong style="font-size:1.15rem; letter-spacing:0.06em;">{{ $claim->handover_code }}</strong>
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.claim-selector').forEach((btn) => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.claim-selector').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.detail-pane').forEach(p => p.classList.remove('active'));

            btn.classList.add('active');
            const target = document.getElementById(btn.dataset.target);
            if (target) target.classList.add('active');
        });
    });
</script>
@endpush