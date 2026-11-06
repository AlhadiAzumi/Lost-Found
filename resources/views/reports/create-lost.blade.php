@extends('layouts.app')

@section('title', 'Lapor Barang Hilang — Campus Lost & Found')

@section('content')
<div class="container" style="max-width: 640px; margin: 36px auto; padding: 0 16px;">
    <div class="card" style="padding: 32px; border: 2px solid #111; border-radius: 20px; box-shadow: 6px 6px 0 #111; background: #fff;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
            <div style="padding: 10px; background: #fee2e2; border: 1.5px solid #dc2626; border-radius: 12px;">
                <x-icon name="search" :size="24" />
            </div>
            <div>
                <h1 style="font-size: 22px; font-weight: 800; margin: 0; color: #111;">Lapor Barang Hilang (Lost)</h1>
                <p style="margin: 0; color: #6b7280; font-size: 13px;">Isi data barang yang hilang agar teman kampus atau petugas dapat mengetahuinya.</p>
            </div>
        </div>

        @if ($errors->any())
            <div style="margin-bottom: 20px; padding: 12px; background: #fee2e2; border: 1.5px solid #dc2626; border-radius: 10px; font-size: 14px; color: #991b1b;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 18px;">
            @csrf
            <input type="hidden" name="type" value="lost">

            <div>
                <label for="title" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Nama Barang *</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       class="input" style="width: 100%; box-sizing: border-box;" placeholder="Mis. Dompet Kulit Cokelat Braun Buffel">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label for="category_id" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Kategori *</label>
                    <select id="category_id" name="category_id" required class="input" style="width: 100%; box-sizing: border-box;">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date_event" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Tanggal Hilang *</label>
                    <input type="date" id="date_event" name="date_event" value="{{ old('date_event', date('Y-m-d')) }}" required
                           class="input" style="width: 100%; box-sizing: border-box;">
                </div>
            </div>

            <div>
                <label for="location" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Perkiraan Lokasi Terakhir *</label>
                <input type="text" id="location" name="location" value="{{ old('location') }}" required
                       class="input" style="width: 100%; box-sizing: border-box;" placeholder="Mis. Gedung F - Lantai 2 / Kantin Teknik">
            </div>

            <div>
                <label for="description" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Deskripsi / Ciri-ciri Khusus *</label>
                <textarea id="description" name="description" rows="3" required class="input" style="width: 100%; box-sizing: border-box;"
                          placeholder="Jelaskan warna, gantungan, merk, atau isi barang secara umum...">{{ old('description') }}</textarea>
            </div>

            <div>
                <label for="image" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Foto Barang (Opsional)</label>
                <input type="file" id="image" name="image" accept="image/*" class="input" style="width: 100%; box-sizing: border-box;">
                <small style="color: #6b7280; font-size: 12px; display: block; margin-top: 4px;">Format: JPG, PNG, WebP (Maks. 2 MB)</small>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 10px;">
                <a href="{{ route('catalog.index') }}" class="btn btn--ghost" style="flex: 1; text-align: center;">Batal</a>
                <button type="submit" class="btn btn--primary" style="flex: 2; padding: 12px;">Kirim Laporan Kehilangan</button>
            </div>
        </form>
    </div>
</div>
@endsection

