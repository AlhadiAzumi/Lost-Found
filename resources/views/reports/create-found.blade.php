@extends('layouts.app')

@section('title', 'Lapor Barang Temuan | Lost & Found')

@section('content')
<div class="page-wrap page-wrap--narrow">
    <div class="card" style="padding:32px 28px;">
        {{-- Header --}}
        <div style="display:flex; align-items:center; gap:14px; margin-bottom:28px; padding-bottom:20px; border-bottom:1.5px solid #f1f5f9;">
            <div style="padding:10px; background:var(--lime-light); border:1.5px solid var(--ink); border-radius:12px; flex-shrink:0;">
                <x-icon name="search-check" :size="22" />
            </div>
            <div>
                <h1 style="font-size:20px; font-weight:800; margin:0; color:#111; letter-spacing:-0.3px;">Lapor Barang Temuan</h1>
                <p style="margin:4px 0 0; color:var(--muted); font-size:13px;">Terima kasih atas niat baikmu! Amankan barang agar tidak diklaim oleh orang yang salah.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert--error">
                <x-icon name="circle-x" :size="18" />
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data"
              style="display:flex; flex-direction:column; gap:18px;">
            @csrf
            <input type="hidden" name="type" value="found">

            <div class="field">
                <label class="label label--required">Nama Barang yang Ditemukan</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="input" placeholder="Mis. Flashdisk SanDisk Hitam Merah">
            </div>

            <div class="form-grid-2">
                <div class="field">
                    <label class="label label--required">Kategori</label>
                    <select name="category_id" required class="input">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label class="label label--required">Tanggal Ditemukan</label>
                    <input type="date" name="date_event" value="{{ old('date_event', date('Y-m-d')) }}" required class="input">
                </div>
            </div>

            <div class="field">
                <label class="label label--required">Lokasi Ditemukan &amp; Dititipkan</label>
                <input type="text" name="location" value="{{ old('location') }}" required
                       class="input" placeholder="Mis. Lab Jaringan, sudah dititipkan di Pos Satpam Utama">
            </div>

            <div class="field">
                <label class="label label--required">Deskripsi Umum</label>
                <textarea name="description" rows="3" required class="input"
                          placeholder="Jelaskan kondisi barang tanpa membocorkan ciri rahasia...">{{ old('description') }}</textarea>
            </div>

            {{-- Security Question --}}
            <div class="card panel panel--mint" style="display:flex; flex-direction:column; gap:10px;">
                <div>
                    <strong style="display:block; font-size:14.5px; color:#166534; margin-bottom:4px;">
                        Pertanyaan Keamanan (Security Question) *
                    </strong>
                    <p style="font-size:13px; color:#15803d; line-height:1.5; margin:0 0 12px;">
                        Buat pertanyaan yang hanya bisa dijawab oleh pemilik asli. Misalnya: "Berapa kapasitas flashdisk ini?" atau "Ada stiker gambar apa di casing-nya?"
                    </p>
                    <input type="text" name="security_question" value="{{ old('security_question') }}" required
                           class="input" style="background:var(--white);"
                           placeholder="Mis. Ada gantungan kunci bentuk apa yang terpasang?">
                    <label class="label label--required" for="security_answer">Jawaban Keamanan (tidak ditampilkan ke publik)</label>
                    <input type="text" id="security_answer" name="security_answer" value="{{ old('security_answer') }}" required
                           class="input" style="background:var(--white);"
                           placeholder="Masukkan jawaban yang hanya diketahui pemilik barang">
                </div>
            </div>

            <div class="field">
                <label class="label">Foto Barang <span style="color:var(--muted); font-weight:500;">(Sangat Dianjurkan)</span></label>
                <input type="file" name="image" accept="image/*" class="input">
                <span class="hint">Format JPG, PNG, atau WebP. Maksimal 2 MB.</span>
            </div>

            <div class="form-actions" style="margin-top:4px;">
                <a href="{{ route('catalog.index') }}" class="btn btn--outline" style="flex:1; justify-content:center;">Batal</a>
                <button type="submit" class="btn btn--primary" style="flex:2; padding:13px;">Kirim Laporan Temuan</button>
            </div>
        </form>
    </div>
</div>
@endsection
