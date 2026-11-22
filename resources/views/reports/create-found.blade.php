@extends('layouts.app')

@section('title', 'Laporkan Barang Ditemukan — Lost & Found')

@push('styles')
<style>
    .found-grid {
        display: grid;
        grid-template-columns: 1.15fr 1fr;
        gap: 1.5rem;
        align-items: start;
    }
    .field-group { margin-bottom: 1.15rem; }
    .two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.9rem;
    }

    /* Dropzone Foto */
    .upload-box {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 155px;
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
        max-height: 210px;
        width: 100%;
        object-fit: cover;
        border-radius: 14px;
    }

    /* Kartu Kanan: Privasi & Verifikasi */
    .section-heading {
        font-size: 1.05rem;
        font-weight: 800;
        color: #111111;
        margin-bottom: 1.1rem;
    }
    .toggle-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.15rem;
    }
    .toggle-title {
        font-size: 0.88rem;
        font-weight: 700;
        color: #111827;
    }
    .toggle-desc {
        font-size: 0.76rem;
        color: #4b5563;
        margin-top: 0.15rem;
    }

    /* Switch Pill */
    .switch {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 26px;
        flex-shrink: 0;
    }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background-color: #cbd5e1;
        transition: .25s;
        border-radius: 999px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .25s;
        border-radius: 50%;
    }
    input:checked + .slider { background-color: #10b981; }
    input:checked + .slider:before { transform: translateX(20px); }

    .info-callout {
        background: rgba(255, 255, 255, 0.7);
        border-radius: 16px;
        padding: 0.85rem 1rem;
        font-size: 0.78rem;
        color: #374151;
        line-height: 1.5;
        margin-bottom: 1.25rem;
    }

    @media (max-width: 900px) {
        .found-grid, .two-col { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
    @php
        $campusParam = request()->route('campus');
        $campusSlug = is_object($campusParam)
            ? $campusParam->slug
            : ($campusParam ?: ($currentCampus->slug ?? request()->segment(2)));

        $storeUrl = Route::has('reports.store')
            ? route('reports.store', ['campus' => $campusSlug])
            : url('/kampus/' . $campusSlug . '/laporan');
    @endphp

    <div class="page-header">
        <h1 class="page-title">Laporkan Barang Ditemukan</h1>
    </div>

    <form action="{{ $storeUrl }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="type" value="found">

        <div class="found-grid">
            {{-- Kolom Kiri: Detail Barang & Upload Foto --}}
            <div class="glass-panel">
                <div class="field-group">
                    <label for="title" class="field-label">Nama barang</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="Contoh: Kunci motor Honda / Dompet hitam"
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
                        <label for="date_event" class="field-label">Tanggal ditemukan</label>
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
                    <label for="location" class="field-label">Lokasi ditemukan</label>
                    <input
                        type="text"
                        id="location"
                        name="location"
                        value="{{ old('location') }}"
                        placeholder="Contoh: Gedung TI Lantai 2, Kantin Utama, Parkiran"
                        class="pill-input"
                        required
                    >
                </div>

                <div class="field-group">
                    <label for="description" class="field-label">Deskripsi umum</label>
                    <textarea
                        id="description"
                        name="description"
                        placeholder="Deskripsi yang aman dipublikasikan (warna luar, merek umum, perkiraan jam ditemukan)"
                        class="area-input"
                        required
                    >{{ old('description') }}</textarea>
                </div>

                <div class="field-group" style="margin-bottom:0;">
                    <label class="field-label">Foto barang</label>
                    <label for="image" class="upload-box" id="dropzone">
                        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/jpg,image/webp" hidden>
                        <div id="uploadPrompt">
                            <div class="upload-placeholder">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                <span>Unggah foto</span>
                            </div>
                            <p class="upload-hint">Klik atau seret foto ke sini (JPG, PNG, WEBP maks. 2MB)</p>
                        </div>
                        <img id="photoPreview" class="upload-preview" alt="Preview foto barang">
                    </label>
                </div>
            </div>

            {{-- Kolom Kanan: Privasi & Pertanyaan Verifikasi --}}
            <div class="glass-panel">
                <h2 class="section-heading">Privasi &amp; Verifikasi</h2>

                <div class="toggle-row">
                    <div>
                        <div class="toggle-title">Sembunyikan detail rahasia</div>
                        <div class="toggle-desc">Jangan tulis nomor seri, isi dompet, atau ciri rahasia di deskripsi publik</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="togglePrivacyTip" checked>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="info-callout" id="privacyTipBox">
                    <strong>Tips Keamanan:</strong> Simpan ciri khusus barang (misalnya nomor seri, isi dalam tas, atau warna gantungan) hanya sebagai pertanyaan verifikasi di bawah ini.
                </div>

                <div class="field-group">
                    <label for="security_question" class="field-label">Pertanyaan keamanan</label>
                    <input
                        type="text"
                        id="security_question"
                        name="security_question"
                        value="{{ old('security_question') }}"
                        placeholder="Contoh: Apa isi kantong depan tas / warna gantungan kunci?"
                        class="pill-input"
                        required
                    >
                </div>

                <div class="field-group">
                    <label for="security_answer" class="field-label">Jawaban benar (otomatis terenkripsi)</label>
                    <input
                        type="text"
                        id="security_answer"
                        name="security_answer"
                        value="{{ old('security_answer') }}"
                        placeholder="Jawaban valid untuk verifikasi pemilik asli"
                        class="pill-input"
                        required
                    >
                </div>

                <button type="submit" class="btn-black" style="width:100%; margin-top:0.5rem;">
                    Publikasikan Temuan
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
    const toggleTip = document.getElementById('togglePrivacyTip');
    const tipBox = document.getElementById('privacyTipBox');

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

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('dragover');
    });
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            imageInput.files = e.dataTransfer.files;
            showPreview(e.dataTransfer.files[0]);
        }
    });

    toggleTip.addEventListener('change', () => {
        tipBox.style.display = toggleTip.checked ? 'block' : 'none';
    });
</script>
@endpush