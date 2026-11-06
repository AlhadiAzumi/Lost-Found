@extends('layouts.app')

@section('title', 'Privasi | Lost & Found')

@section('content')
<div class="info-page">
    <div class="info-page__shell">
        @include('pages.partials.header', [
            'title' => 'Privasi dan data pengguna',
            'description' => 'Informasi berikut menjelaskan data yang diminta oleh aplikasi saat ini. Masukkan hanya informasi yang diperlukan untuk mengelola laporan dan klaim.',
        ])

        <aside class="info-notice info-notice--warning">
            <strong>Kebijakan retensi belum ditetapkan kampus</strong>
            <p>Masa penyimpanan, prosedur penghapusan, dan unit pengelola data perlu disahkan sebelum penggunaan produksi. Aplikasi juga masih menyimpan lampiran bukti klaim pada storage publik; jangan unggah dokumen identitas atau data pribadi yang sensitif.</p>
        </aside>

        <section class="info-card info-card--plain">
            <h2>Data yang dimasukkan ke aplikasi</h2>
            <ul class="info-list">
                <li><strong>Akun:</strong> nama, email, serta NIM/NIP dan nomor telepon bila diisi.</li>
                <li><strong>Laporan:</strong> jenis laporan, nama dan kategori barang, deskripsi, lokasi, tanggal, serta foto bila ditambahkan.</li>
                <li><strong>Verifikasi:</strong> pertanyaan dan jawaban untuk barang temuan, jawaban klaim, serta lampiran bukti bila diberikan.</li>
                <li><strong>Aktivitas layanan:</strong> keputusan moderasi, status klaim, dan catatan serah-terima yang dicatat petugas.</li>
            </ul>
        </section>

        <section class="info-card info-card--plain">
            <h2>Informasi yang terlihat di katalog publik</h2>
            <p>Laporan yang disetujui dapat menampilkan jenis dan nama barang, kategori, deskripsi, lokasi, tanggal, foto laporan, pertanyaan verifikasi barang temuan, serta nama pelapor pada halaman detail. Pertimbangkan hal ini sebelum mengirim laporan.</p>
            <p>Jawaban keamanan barang temuan tidak ditampilkan sebagai teks biasa. Gunakan ciri rahasia untuk membantu petugas memeriksa klaim dan jangan memasukkannya ke deskripsi publik.</p>
        </section>

        <section class="info-card info-card--plain">
            <h2>Gunakan data dengan bijak</h2>
            <ul class="info-list">
                <li>Unggah foto barang saja; hindari wajah orang lain dan dokumen identitas.</li>
                <li>Jangan menuliskan kata sandi, OTP, nomor rekening, NIK, atau data sensitif lain.</li>
                <li>Gunakan foto bukti klaim yang relevan dan tidak memuat informasi sensitif.</li>
                <li>Untuk pertanyaan mengenai data atau koreksi informasi, hubungi pengelola melalui halaman <a class="info-link" href="{{ route('pages.contact') }}">Bantuan &amp; Kontak</a>.</li>
            </ul>
        </section>

        <p class="info-page__updated">Informasi ini menjelaskan implementasi aplikasi saat ini, bukan pengganti kebijakan privasi yang disahkan pengelola kampus.</p>
    </div>
</div>
@endsection
