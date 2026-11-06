@extends('layouts.app')

@section('title', 'Bantuan dan kontak | Lost & Found')

@section('content')
<div class="info-page">
    <div class="info-page__shell">
        @include('pages.partials.header', [
            'title' => 'Bantuan dan kontak',
            'description' => 'Hubungi kami jika ada pertanyaan tentang laporan, klaim, atau penggunaan layanan Lost & Found.',
        ])

        <div class="info-page__grid info-page__grid--contact">
            <section class="info-card info-card--plain">
                <span class="info-card__icon"><x-icon name="life-buoy" :size="22" /></span>
                <h2>Email layanan</h2>
                <p>Kirim pertanyaan atau sertakan judul barang dan status laporan agar pengelola dapat membantu menelusurinya.</p>
                <a class="info-contact-link" href="mailto:lostandfoundpnl@gmail.com">lostandfoundpnl@gmail.com</a>
            </section>
            <section class="info-card info-card--plain">
                <span class="info-card__icon"><x-icon name="map-pin" :size="22" /></span>
                <h2>Telepon layanan</h2>
                <p>Untuk bantuan terkait layanan, hubungi nomor berikut pada jam layanan.</p>
                <a class="info-contact-link" href="tel:+6285167811268">0851-6781-1268</a>
                <span class="info-contact-hours">Jam layanan: 08.00–17.00 WIB</span>
            </section>
            <section class="info-card info-card--mint">
                <span class="info-card__icon"><x-icon name="shield-check" :size="22" /></span>
                <h2>Barang temuan &amp; serah-terima</h2>
                <p>Serahkan barang fisik dan lakukan pengambilan melalui petugas di pos satpam kampus. Jangan mengatur serah-terima pribadi berdasarkan pesan dari pihak yang belum diverifikasi.</p>
            </section>
        </div>

        <p class="info-page__updated">Kanal kontak layanan ini disediakan untuk bantuan Lost &amp; Found dan tidak menyatakan bahwa platform merupakan kanal resmi institusi.</p>
    </div>
</div>
@endsection
