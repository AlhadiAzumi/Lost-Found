@extends('layouts.app')

@section('title', 'Panduan | Lost & Found')

@section('content')
<div class="info-page">
    <div class="info-page__shell">
        @include('pages.partials.header', [
            'title' => 'Panduan penggunaan',
            'description' => 'Berikut langkah melaporkan barang, mencari di katalog, mengajukan klaim, dan mengambil barang.',
        ])

        <ol class="info-steps">
            <li class="info-card">
                <span class="info-steps__number">01</span>
                <h2>Buat laporan atau cari barang</h2>
                <p>Masuk untuk membuat laporan kehilangan atau penemuan. Isi nama barang, kategori, lokasi, tanggal, dan deskripsi secukupnya. Foto bersifat opsional.</p>
                <a class="info-link" href="{{ route('catalog.index') }}">Cari di katalog <span aria-hidden="true">→</span></a>
            </li>
            <li class="info-card">
                <span class="info-steps__number">02</span>
                <h2>Tunggu pemeriksaan laporan</h2>
                <p>Laporan baru ditinjau petugas sebelum tampil di katalog. Barang temuan tetap diserahkan ke pos satpam; laporan online tidak menggantikan penitipan fisik.</p>
            </li>
            <li class="info-card">
                <span class="info-steps__number">03</span>
                <h2>Ajukan klaim dengan bukti</h2>
                <p>Jika menemukan barang milikmu, ajukan klaim dari halaman detail. Jawab pertanyaan verifikasi dan berikan bukti yang relevan tanpa mengunggah dokumen sensitif.</p>
            </li>
            <li class="info-card">
                <span class="info-steps__number">04</span>
                <h2>Ambil barang melalui petugas</h2>
                <p>Tunggu keputusan klaim. Setelah disetujui, ikuti instruksi petugas dan lakukan serah-terima di pos satpam. Tunjukkan kode serah-terima langsung kepada petugas; jangan bagikan di katalog atau media sosial.</p>
            </li>
        </ol>

        <section class="info-card info-card--plain info-faq">
            <h2>Pertanyaan yang sering diajukan</h2>
            <details>
                <summary>Apakah saya perlu akun untuk melihat katalog?</summary>
                <p>Tidak. Katalog publik dapat dilihat tanpa masuk. Akun diperlukan untuk membuat laporan, mengajukan klaim, dan melihat riwayat.</p>
            </details>
            <details>
                <summary>Apakah foto laporan harus menampilkan semua ciri barang?</summary>
                <p>Tidak. Hindari menampilkan nomor seri, isi dompet, identitas, atau ciri rahasia yang dapat digunakan orang lain untuk mengaku sebagai pemilik.</p>
            </details>
            <details>
                <summary>Apa yang harus dilakukan jika menemukan barang?</summary>
                <p>Jaga barang tetap aman, serahkan kepada petugas di pos satpam kampus, lalu buat laporan agar pemilik dapat menemukannya.</p>
            </details>
            <details>
                <summary>Bagaimana jika laporan saya belum muncul?</summary>
                <p>Laporan baru menunggu pemeriksaan petugas. Jika memerlukan bantuan, hubungi pengelola melalui halaman <a class="info-link" href="{{ route('pages.contact') }}">Bantuan &amp; Kontak</a>.</p>
            </details>
        </section>
    </div>
</div>
@endsection
