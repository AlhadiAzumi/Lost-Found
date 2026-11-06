@extends('layouts.app')

@section('title', 'Keamanan | Lost & Found')

@section('content')
<div class="info-page">
    <div class="info-page__shell">
        @include('pages.partials.header', [
            'title' => 'Keamanan saat melapor dan mengambil barang',
            'description' => 'Lindungi informasi pribadi dan serahkan atau ambil barang melalui petugas kampus.',
        ])

        <div class="info-page__grid">
            <section class="info-card info-card--mint">
                <span class="info-card__icon"><x-icon name="map-pin" :size="22" /></span>
                <h2>Serahkan barang ke petugas</h2>
                <p>Barang temuan diserahkan ke pos satpam kampus. Jangan memajang barang berharga atau mengatur pertemuan pribadi dengan orang yang mengaku sebagai pemilik.</p>
            </section>
            <section class="info-card info-card--blue">
                <span class="info-card__icon"><x-icon name="key-round" :size="22" /></span>
                <h2>Lindungi kode serah-terima</h2>
                <p>Kode hanya diberikan setelah klaim diproses. Tunjukkan kepada petugas saat pengambilan; jangan mengirimkannya melalui komentar, grup, atau unggahan publik.</p>
            </section>
            <section class="info-card info-card--lime">
                <span class="info-card__icon"><x-icon name="lock-keyhole" :size="22" /></span>
                <h2>Jaga ciri rahasia barang</h2>
                <p>Deskripsikan barang secukupnya. Simpan ciri khusus untuk pemeriksaan klaim dan jangan mencantumkan kata sandi, nomor kartu, isi dompet, atau nomor seri secara lengkap.</p>
            </section>
            <section class="info-card info-card--plain">
                <span class="info-card__icon"><x-icon name="shield-check" :size="22" /></span>
                <h2>Waspadai permintaan mencurigakan</h2>
                <p>Petugas tidak memerlukan kata sandi akun atau kode OTP. Jangan membayar biaya kepada pihak yang menghubungi di luar kanal layanan yang tercantum di halaman bantuan.</p>
            </section>
        </div>

        <aside class="info-notice info-notice--warning">
            <strong>Catatan penting tentang bukti klaim</strong>
            <p>Jangan unggah foto KTM, KTP, kartu pembayaran, atau dokumen yang memuat nomor identitas. Saat ini lampiran bukti klaim disimpan pada storage publik aplikasi; penyimpanan privat harus diterapkan sebelum layanan digunakan untuk data nyata.</p>
        </aside>

        <div class="info-page__actions">
            <a class="btn btn--outline" href="{{ route('pages.privacy') }}">Baca informasi privasi</a>
            <a class="btn btn--primary" href="{{ route('pages.contact') }}">Hubungi pengelola</a>
        </div>
    </div>
</div>
@endsection
