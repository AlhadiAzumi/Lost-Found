@extends('layouts.app')

@section('title', 'Lapor Barang Temuan — Campus Lost & Found')

@section('content')
<div class="container" style="max-width: 640px; margin: 36px auto; padding: 0 16px;">
    <div class="card" style="padding: 32px; border: 2px solid #111; border-radius: 20px; box-shadow: 6px 6px 0 #111; background: #fff;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
            <div style="padding: 10px; background: #bef264; border: 1.5px solid #111; border-radius: 12px;">
                <x-icon name="search-check" :size="24" />
            </div>
            <div>
                <h1 style="font-size: 22px; font-weight: 800; margin: 0; color: #111;">Lapor Barang Temuan (Found)</h1>
                <p style="margin: 0; color: #6b7280; font-size: 13px;">Terima kasih atas niat baikmu! Amankan barang agar tidak diklaim oleh orang yang salah.</p>
            </div>
        </div>

        @if ($errors->any())
            <div style="margin-bottom: 20px; padding: 12px; background: #fee2e2; border: 1.5px solid #dc2626; border-radius: 10px; font-size: 14px; color: #991b1b;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 18px;">
            @csrf
            <input type="hidden" name="type" value="found">

            <div>
                <label for="title" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Nama Barang yang Ditemukan *</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       class="input" style="width: 100%; box-sizing: border-box;" placeholder="Mis. Flashdisk Sandisk Hitam Merah">
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
                    <label for="date_event" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Tanggal Ditemukan *</label>
                    <input type="date" id="date_event" name="date_event" value="{{ old('date_event', date('Y-m-d')) }}" required
                           class="input" style="width: 100%; box-sizing: border-box;">
                </div>
            </div>

            <div>
                <label for="location" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Lokasi Ditemukan & Dititipkan *</label>
                <input type="text" id="location" name="location" value="{{ old('location') }}" required
                       class="input" style="width: 100%; box-sizing: border-box;" placeholder="Mis. Ditemukan di Lab Jaringan, sudah dititipkan di Pos Satpam Utama">
            </div>

            <div>
                <label for="description" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Deskripsi Umum *</label>
                <textarea id="description" name="description" rows="3" required class="input" style="width: 100%; box-sizing: border-box;"
                          placeholder="Jelaskan kondisi barang tanpa membocorkan ciri rahasia...">{{ old('description') }}</textarea>
            </div>

            {{-- Security Question Box --}}
            <div style="padding: 18px; background: #f0fdf4; border: 1.5px solid #16a34a; border-radius: 14px;">
                <strong style="display: block; color: #166534; font-size: 15px; margin-bottom: 4px;">
                    🛡️ Pertanyaan Keamanan (Security Question) *
                </strong>
                <p style="margin: 0 0 12px; font-size: 13px; color: #15803d; line-height: 1.4;">
                    Buat pertanyaan verifikasi yang hanya bisa dijawab oleh pemilik asli barang ini (misalnya: "Berapa kapasitas flashdisk ini?" atau "Ada stiker gambar apa di casing HP-nya?").
                </p>
                <input type="text" id="security_question" name="security_question" value="{{ old('security_question') }}" required
                       class="input" style="width: 100%; box-sizing: border-box; background: #fff;" placeholder="Mis. Ada gantungan kunci bentuk apa yang terpasang?">
            </div>

            <div>
                <label for="image" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Foto Barang (Sangat Dianjurkan)</label>
                <input type="file" id="image" name="image" accept="image/*" class="input" style="width: 100%; box-sizing: border-box;">
                <small style="color: #6b7280; font-size: 12px; display: block; margin-top: 4px;">Format: JPG, PNG, WebP (Maks. 2 MB)</small>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 10px;">
                <a href="{{ route('catalog.index') }}" class="btn btn--ghost" style="flex: 1; text-align: center;">Batal</a>
                <button type="submit" class="btn btn--primary" style="flex: 2; padding: 12px;">Kirim Laporan Temuan</button>
            </div>
        </form>
    </div>
</div>
@endsection

