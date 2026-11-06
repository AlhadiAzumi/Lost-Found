@extends('layouts.app')

@section('title', 'Tentang layanan | Lost & Found')

@section('content')
<div class="info-page">
    <div class="info-page__shell">
        @include('pages.partials.header', [
            'title' => 'Tentang layanan Lost & Found',
            'description' => 'Layanan untuk mencatat barang hilang dan temuan di area kampus.',
        ])

        <div class="info-page__grid">
            <section class="info-card info-card--mint">
                <span class="info-card__icon"><x-icon name="heart-handshake" :size="22" /></span>
                <h2>Tujuan layanan</h2>
                <p>Mengumpulkan informasi barang hilang dan temuan dalam satu katalog yang mudah dicari, sekaligus mengarahkan proses klaim dan serah-terima agar lebih tertib.</p>
            </section>
            <section class="info-card info-card--lime">
                <span class="info-card__icon"><x-icon name="shield-check" :size="22" /></span>
                <h2>Peran petugas</h2>
                <p>Petugas yang ditunjuk meninjau laporan, memeriksa klaim, dan membantu mencatat serah-terima barang. Katalog membantu pencarian, tetapi bukan pengganti pemeriksaan langsung.</p>
            </section>
        </div>

        <section class="info-card info-card--plain">
            <h2>Untuk warga kampus</h2>
            <p>Gunakan layanan ini untuk barang yang hilang atau ditemukan di area kampus. Untuk barang temuan, serahkan barang fisik kepada petugas di pos satpam kampus; jangan menyimpan atau menyerahkannya langsung kepada orang yang belum diverifikasi.</p>
            <p>Layanan ini membantu warga kampus mencari dan melaporkan barang. Pengoperasian serta penanggung jawab layanan mengikuti persetujuan pengelola kampus.</p>
            <div class="info-page__actions">
                <a class="btn btn--primary" href="{{ route('catalog.index') }}">Jelajahi katalog</a>
                <a class="btn btn--outline" href="{{ route('pages.guide') }}">Lihat panduan</a>
            </div>
        </section>
    </div>
</div>
@endsection
