@extends('layouts.app')

@section('title', 'Ketentuan penggunaan | Lost & Found')

@section('content')
<div class="info-page">
    <div class="info-page__shell">
        @include('pages.partials.header', [
            'title' => 'Ketentuan penggunaan layanan',
            'description' => 'Hal yang perlu diperhatikan saat memakai katalog, mengirim laporan, mengajukan klaim, dan mengambil barang.',
        ])

        <section class="info-card info-card--plain">
            <h2>1. Penggunaan layanan</h2>
            <p>Gunakan layanan untuk melaporkan barang hilang atau temuan di area kampus. Berikan informasi yang benar, relevan, dan tidak melanggar hak orang lain.</p>
        </section>
        <section class="info-card info-card--plain">
            <h2>2. Laporan dan klaim</h2>
            <p>Laporan dapat ditinjau petugas sebelum dipublikasikan. Pengajuan klaim bukan bukti kepemilikan final; petugas dapat meminta pemeriksaan tambahan sebelum menyetujui serah-terima.</p>
        </section>
        <section class="info-card info-card--plain">
            <h2>3. Informasi dan konten</h2>
            <p>Jangan mengunggah kata sandi, kode OTP, data pembayaran, atau dokumen identitas. Pastikan foto dan deskripsi tidak mengungkap ciri rahasia barang atau informasi pribadi orang lain.</p>
        </section>
        <section class="info-card info-card--plain">
            <h2>4. Serah-terima</h2>
            <p>Serah-terima barang dilakukan melalui petugas di pos satpam kampus. Jangan menyerahkan barang atau kode serah-terima kepada orang yang belum diverifikasi melalui proses layanan.</p>
        </section>
        <aside class="info-notice info-notice--warning">
            <strong>Status layanan</strong>
            <p>Layanan ini ditujukan untuk warga kampus. Ketentuan operasional akhir, penanggung jawab, dan kebijakan data perlu mendapat persetujuan pengelola kampus.</p>
        </aside>
        <p class="info-page__updated">Jika menemukan penyalahgunaan atau informasi yang perlu dikoreksi, hubungi pengelola melalui halaman <a class="info-link" href="{{ route('pages.contact') }}">Bantuan &amp; Kontak</a>.</p>
    </div>
</div>
@endsection
