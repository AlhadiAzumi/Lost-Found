@extends('layouts.app')

@section('title', 'Lapor Barang Hilang | Lost & Found')

@section('content')
<div class="page-wrap page-wrap--narrow">
    <div class="card" style="padding:32px 28px;">
        {{-- Header --}}
        <div style="display:flex; align-items:center; gap:14px; margin-bottom:28px; padding-bottom:20px; border-bottom:1.5px solid #f1f5f9;">
            <div style="padding:10px; background:#fee2e2; border:1.5px solid #dc2626; border-radius:12px; flex-shrink:0;">
                <x-icon name="search" :size="22" />
            </div>
            <div>
                <h1 style="font-size:20px; font-weight:800; margin:0; color:#111; letter-spacing:-0.3px;">Lapor Barang Hilang</h1>
                <p style="margin:4px 0 0; color:var(--muted); font-size:13px;">Isi data barang agar teman kampus atau petugas dapat mengetahuinya.</p>
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
            <input type="hidden" name="type" value="lost">

            <div class="field">
                <label class="label label--required">Nama Barang</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="input" placeholder="Mis. Dompet Kulit Cokelat Braun Buffel">
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
                    <label class="label label--required">Tanggal Hilang</label>
                    <input type="date" name="date_event" value="{{ old('date_event', date('Y-m-d')) }}" required class="input">
                </div>
            </div>

            <div class="field">
                <label class="label label--required">Perkiraan Lokasi Terakhir</label>
                <input type="text" name="location" value="{{ old('location') }}" required
                       class="input" placeholder="Mis. Gedung F - Lantai 2 / Kantin Teknik">
            </div>

            <div class="field">
                <label class="label label--required">Deskripsi / Ciri-ciri Khusus</label>
                <textarea name="description" rows="4" required class="input"
                          placeholder="Jelaskan warna, gantungan, merk, atau isi barang secara umum...">{{ old('description') }}</textarea>
            </div>

            <div class="field">
                <label class="label">Foto Barang <span style="color:var(--muted); font-weight:500;">(Opsional)</span></label>
                <input type="file" name="image" accept="image/*" class="input">
                <span class="hint">Format JPG, PNG, atau WebP. Maksimal 2 MB.</span>
            </div>

            <div class="form-actions" style="margin-top:4px;">
                <a href="{{ route('catalog.index') }}" class="btn btn--outline" style="flex:1; justify-content:center;">Batal</a>
                <button type="submit" class="btn btn--primary" style="flex:2; padding:13px;">Kirim Laporan Kehilangan</button>
            </div>
        </form>
    </div>
</div>
@endsection
