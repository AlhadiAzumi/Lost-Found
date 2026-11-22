@extends('layouts.app')

@section('title', 'Ajukan Klaim Barang — Lost & Found')

@push('styles')
<style>
    .claim-grid {
        display: grid;
        grid-template-columns: 0.95fr 1.15fr;
        gap: 1.5rem;
        align-items: start;
    }
    .item-preview-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-radius: 18px;
        background: linear-gradient(135deg, #a9cbb2, #8ab496);
        margin-bottom: 1.1rem;
    }
    .item-badge {
        display: inline-block;
        padding: 0.32rem 0.85rem;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 700;
        background: #d1fae5;
        color: #065f46;
        margin-bottom: 0.65rem;
    }
    .item-title {
        font-size: 1.35rem;
        font-weight: 800;
        color: #111111;
        margin-bottom: 0.45rem;
    }
    .item-meta {
        font-size: 0.85rem;
        color: #4b5563;
        line-height: 1.6;
        margin-bottom: 0.9rem;
    }
    .question-box {
        background: #ffffff;
        border-radius: 18px;
        padding: 1rem 1.2rem;
        margin-bottom: 1.2rem;
        border-left: 4px solid #111111;
    }
    .question-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }
    .question-text {
        font-size: 1rem;
        font-weight: 700;
        color: #111111;
    }
    .proof-dropzone {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 130px;
        border-radius: 20px;
        border: 1.5px dashed rgba(35, 89, 58, 0.45);
        background: linear-gradient(135deg, rgba(169, 203, 178, 0.5) 0%, rgba(138, 180, 150, 0.42) 100%);
        cursor: pointer;
        padding: 1rem;
        text-align: center;
        transition: border-color 0.2s, transform 0.15s;
    }
    .proof-dropzone:hover {
        border-color: #111111;
        transform: translateY(-1px);
    }
    .proof-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        font-weight: 700;
        color: #1f2937;
    }
    .proof-sub {
        font-size: 0.76rem;
        color: #374151;
        margin-top: 0.3rem;
    }
    .proof-filename {
        margin-top: 0.6rem;
        font-size: 0.82rem;
        font-weight: 700;
        color: #065f46;
        background: #ffffff;
        padding: 0.3rem 0.85rem;
        border-radius: 999px;
        display: none;
    }
    @media (max-width: 900px) {
        .claim-grid { grid-template-columns: 1fr; }
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
        <h1 class="page-title">Formulir Klaim Kepemilikan</h1>
    </div>

    <div class="claim-grid">
        {{-- Kolom Kiri: Ringkasan Barang Temuan --}}
        <div class="glass-panel">
            @if ($item->image)
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="item-preview-img">
            @else
                <div class="item-preview-img" style="display:flex; align-items:center; justify-content:center; color:#1f2937; font-weight:700;">
                    Tidak ada foto publik
                </div>
            @endif

            <span class="item-badge">Barang Ditemukan</span>
            <h2 class="item-title">{{ $item->title }}</h2>

            <div class="item-meta">
                <div><strong>Kategori:</strong> {{ $item->category->name ?? '-' }}</div>
                <div><strong>Lokasi ditemukan:</strong> {{ $item->location }}</div>
                <div><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($item->date_event)->translatedFormat('d M Y') }}</div>
            </div>

            <p style="font-size:0.88rem; color:#374151; line-height:1.6;">
                {{ $item->description }}
            </p>
        </div>

        {{-- Kolom Kanan: Form Verifikasi Klaim --}}
        <div class="glass-panel">
            <h2 style="font-size:1.1rem; font-weight:800; margin-bottom:1rem; color:#111111;">
                Verifikasi Kepemilikan
            </h2>

            <div class="question-box">
                <div class="question-label">Pertanyaan Keamanan dari Penemu</div>
                <div class="question-text">{{ $item->security_question }}</div>
            </div>

            <form action="{{ route('claims.store', ['campus' => $campusSlug, 'item' => $item]) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div style="margin-bottom:1.15rem;">
                    <label for="security_answer" class="field-label">Jawaban Anda</label>
                    <textarea
                        id="security_answer"
                        name="security_answer"
                        placeholder="Tulis jawaban spesifik sesuai ciri barang milik Anda..."
                        class="area-input"
                        required
                    >{{ old('security_answer') }}</textarea>
                </div>

                <div style="margin-bottom:1.4rem;">
                    <label class="field-label">Bukti kepemilikan (opsional namun disarankan)</label>
                    <label for="proof_image" class="proof-dropzone">
                        <input type="file" id="proof_image" name="proof_image" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" hidden>
                        <div class="proof-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                            <span>Unggah Bukti Kepemilikan</span>
                        </div>
                        <div class="proof-sub">Foto barang saat masih dimiliki, nota pembelian, atau PDF (maks. 2MB)</div>
                        <span id="proofFileName" class="proof-filename"></span>
                    </label>
                </div>

                <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                    <button type="submit" class="btn-black" style="flex:1;">
                        Kirim Klaim
                    </button>
                    <a href="{{ route('items.show', ['campus' => $campusSlug, 'item' => $item]) }}"
                       style="padding:0.88rem 1.4rem; border-radius:999px; background:#ffffff; color:#111827; font-weight:700; font-size:0.9rem; text-decoration:none; text-align:center;">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const proofInput = document.getElementById('proof_image');
    const proofName = document.getElementById('proofFileName');

    proofInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            proofName.textContent = 'File dipilih: ' + file.name;
            proofName.style.display = 'inline-block';
        }
    });
</script>
@endpush