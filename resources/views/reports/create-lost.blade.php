@extends('layouts.app')

@section('title', 'Laporkan Barang Hilang — Lost & Found')

@push('styles')
<style>
    .lost-grid {
        display: grid;
        grid-template-columns: 1.2fr 0.95fr;
        gap: 1.5rem;
        align-items: start;
    }
    .field-group { margin-bottom: 1.15rem; }
    .two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.9rem;
    }
    .upload-box {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 175px;
        border-radius: 20px;
        border: 1.5px dashed rgba(35, 89, 58, 0.45);
        background: linear-gradient(135deg, rgba(169, 203, 178, 0.55) 0%, rgba(138, 180, 150, 0.48) 100%);
        cursor: pointer;
        overflow: hidden;
        transition: border-color 0.2s, transform 0.15s;
        text-align: center;
        padding: 1rem;
    }
    .upload-box:hover, .upload-box.dragover {
        border-color: #111111;
        transform: translateY(-1px);
    }
    .upload-placeholder {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        font-size: 0.92rem;
        font-weight: 700;
        color: #1f2937;
    }
    .upload-hint {
        font-size: 0.76rem;
        color: #374151;
        margin-top: 0.35rem;
    }
    .upload-preview {
        display: none;
        max-height: 220px;
        width: 100%;
        object-fit: cover;
        border-radius: 14px;
    }
    .tips-box {
        background: rgba(255, 255, 255, 0.72);
        border-radius: 18px;
        padding: 1rem 1.15rem;
        font-size: 0.8rem;
        color: #374151;
        line-height: 1.6;
        margin-top: 1.15rem;
        margin-bottom: 1.25rem;
    }
    @media (max-width: 900px) {
        .lost-grid, .two-col { grid-template-columns: 1fr; }
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
        <h1 class="page-title">Laporkan Barang Hilang</h1>
    </div>

    <form action="{{ route('reports.store', ['campus' => $campusSlug]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="type" value="lost">

        <div class="lost-grid">
            {{-- Kolom Kiri: Informasi Barang Hilang --}}
            <div class="glass-panel">
                <div class="field-group">
                    <label for="title" class="field-label">Nama barang yang hilang</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="Contoh: Tumbler Corkcicle hitam / KTM atas nama..."
                        class="pill-input"
                        required
                    >
                </div>

                <div class="two-col field-group">
                    <div>
                        <label for="category_id" class="field-label">Kategori</label>
                        <select id="category_id" name="category_id" class="pill-select" required>
                            <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Pilih kategori</option>
                            @foreach ($categories ?? [] as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="date_event" class="field-label">Tanggal hilang</label>
                        <input
                            type="date"
                            id="date_event"
                            name="date_event"
                            value="{{ old('date_event', date('Y-m-d')) }}"
                            max="{{ date('Y-m-d') }}"
                            class="pill-input"
                            required
                        >
                    </div>
                </div>

                <div class="field-group">
                    <label for="location" class="field-label">Perkiraan lokasi terakhir terlihat</label>
                    <input
                        type="text"
                        id="location"
                        name="location"
                        value="{{ old('location') }}"
                        placeholder="Contoh: Ruang Lab Komputer 3, Masjid Kampus, Parkiran Motor"
                        class="pill-input"
                        required
                    >
                </div>

                <div class="field-group" style="margin-bottom:0;">
                    <label for="description" class="field-label">Ciri-ciri &amp; kronologi singkat</label>
                    <textarea
                        id="description"
                        name="description"
                        placeholder="Jelaskan warna, merek, stiker khusus, atau kronologi terakhir kali barang dibawa..."
                        class="area-input"
                        required
                    >{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Kolom Kanan: Foto Referensi & Kirim --}}
            <div class="glass-panel">
                <h2 style="font-size:1.05rem; font-weight:800; color:#111111; margin-bottom:1rem;">
                    Foto Referensi (Opsional)
                </h2>

                <label for="image" class="upload-box" id="dropzone">
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/jpg,image/webp" hidden>
                    <div id="uploadPrompt">
                        <div class="upload-placeholder">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                            <span>Unggah foto barang</span>
                        </div>
                        <p class="upload-hint">Jika punya foto lama barang tersebut agar mudah dikenali (maks. 2MB)</p>
                    </div>
                    <img id="photoPreview" class="upload-preview" alt="Preview foto barang">
                </label>

                <div class="tips-box">
                    <strong>Info Publikasi:</strong> Laporan kehilangan akan ditinjau petugas terlebih dahulu sebelum tampil di Feed Publik agar terhindar dari spam.
                </div>

                <button type="submit" class="btn-black" style="width:100%;">
                    Kirim Laporan Kehilangan
                </button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    const imageInput = document.getElementById('image');
    const dropzone = document.getElementById('dropzone');
    const uploadPrompt = document.getElementById('uploadPrompt');
    const photoPreview = document.getElementById('photoPreview');

    function showPreview(file) {
        if (!file || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.src = e.target.result;
            photoPreview.style.display = 'block';
            uploadPrompt.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    imageInput.addEventListener('change', (e) => showPreview(e.target.files[0]));
    dropzone.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.classList.add('dragover'); });
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            imageInput.files = e.dataTransfer.files;
            showPreview(e.dataTransfer.files[0]);
        }
    });
</script>
@endpush