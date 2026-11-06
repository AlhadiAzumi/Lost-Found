@extends('layouts.app')

@section('title', 'Lost & Found | Barang hilang dan temuan')

@section('content')
    <section class="home-hero">
        <div class="home-hero__glow home-hero__glow--one"></div>
        <div class="home-hero__glow home-hero__glow--two"></div>
        <div class="home-shell home-hero__layout">
            <div class="home-hero__copy">
                <x-badge icon="heart-handshake" color="#bef264">Layanan barang hilang dan temuan kampus</x-badge>
                <h1>Cari barang hilang<br><span>atau laporkan temuan.</span></h1>
                <p class="home-hero__lead">
                    Cari barang di katalog atau laporkan barang yang hilang dan ditemukan di area kampus.
                </p>
                <div class="home-hero__actions">
                    <a href="{{ route('catalog.index') }}" class="btn btn--primary">
                        Jelajahi Katalog <x-icon name="search" :size="18" />
                    </a>
                    <a href="{{ route('lost.create') }}" class="btn btn--outline">
                        Laporkan Barang
                    </a>
                </div>
                <div class="home-hero__trust">
                    <x-icon name="shield-check" :size="17" />
                    <span>Serah terima terverifikasi di Pos Satpam Kampus</span>
                </div>
            </div>

            <div class="home-showcase" aria-label="Pratinjau katalog barang terbaru">
                <div class="home-showcase__orbit home-showcase__orbit--one"></div>
                <div class="home-showcase__orbit home-showcase__orbit--two"></div>
                <div class="home-showcase__card">
                    <div class="home-showcase__head">
                        <div>
                            <h2>Baru dilaporkan</h2>
                        </div>
                        <span class="home-showcase__mark"><x-icon name="scan-search" :size="22" /></span>
                    </div>
                    <div class="home-showcase__items">
                        @forelse ($recentItems as $item)
                            <a class="home-showcase__item" href="{{ route('items.show', ['item' => $item]) }}">
                                <span class="home-showcase__item-icon {{ in_array($item->type, ['found', 'ditemukan'], true) ? 'is-found' : '' }}">
                                    <x-icon :name="$item->category->icon ?: 'tag'" :size="19" />
                                </span>
                                <span class="home-showcase__item-copy">
                                    <strong>{{ $item->title }}</strong>
                                    <small><x-icon name="map-pin" :size="13" /> {{ $item->location }}</small>
                                </span>
                                <span class="home-showcase__type">
                                    {{ in_array($item->type, ['found', 'ditemukan'], true) ? 'Temuan' : 'Hilang' }}
                                </span>
                            </a>
                        @empty
                            <div class="home-showcase__empty">
                                Belum ada laporan aktif. Barang yang dilaporkan akan tampil di sini setelah disetujui petugas.
                            </div>
                        @endforelse
                    </div>
                    <a href="{{ route('catalog.index') }}" class="home-showcase__link">
                        Lihat semua barang <span aria-hidden="true">→</span>
                    </a>
                </div>
                <div class="home-float home-float--safe">
                    <x-icon name="shield-check" :size="18" />
                    <span><strong>Aman</strong><small>Dicek petugas</small></span>
                </div>
                <div class="home-float home-float--match">
                    <span class="home-float__check"><x-icon name="circle-check" :size="17" /></span>
                    <span><strong>Barang kembali</strong><small>Satu langkah lebih dekat</small></span>
                </div>
                <span class="home-spark home-spark--one" aria-hidden="true">✳</span>
                <span class="home-spark home-spark--two" aria-hidden="true">✦</span>
            </div>
        </div>
    </section>

    <section class="home-proof" aria-label="Ringkasan laporan">
        <div class="home-shell home-proof__inner">
            <p>Ringkasan laporan barang</p>
            <div class="home-proof__stats">
                <div><strong>{{ number_format($totalItems) }}</strong><span>barang aktif di katalog</span></div>
                <i></i>
                <div><strong>{{ number_format($returnedItems) }}</strong><span>barang telah dikembalikan</span></div>
                <i></i>
                <div class="home-proof__secure"><x-icon name="key-round" :size="19" /><span>Klaim diverifikasi petugas</span></div>
            </div>
        </div>
    </section>

    <section class="home-section home-section--catalog" id="barang-terbaru">
        <div class="home-shell">
            <div class="home-section__heading">
                <div>
                    <h2>Laporan barang terbaru</h2>
                </div>
                <a href="{{ route('catalog.index') }}" class="home-text-link">Buka katalog lengkap <span aria-hidden="true">→</span></a>
            </div>

            <div class="home-category-row" aria-label="Kategori barang">
                @foreach ($categories as $category)
                    <a href="{{ route('catalog.index', ['category' => $category->id]) }}" class="home-category">
                        <x-icon :name="$category->icon ?: 'tag'" :size="17" />
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>

            @if ($recentItems->isNotEmpty())
                <div class="home-item-grid">
                    @foreach ($recentItems as $item)
                        <x-item-card :item="$item" />
                    @endforeach
                </div>
            @else
                <div class="home-empty">
                    <span class="home-empty__icon"><x-icon name="search" :size="25" /></span>
                    <div>
                        <strong>Katalog sedang menunggu laporan pertama</strong>
                        <p>Jika menemukan barang, laporkan agar pemiliknya bisa mulai mencari.</p>
                    </div>
                    <a href="{{ route('found.create') }}" class="btn btn--primary">Lapor Barang Temuan</a>
                </div>
            @endif
        </div>
    </section>

    <section class="home-process" id="cara-kerja">
        <div class="home-shell">
            <div class="home-process__intro">
                <h2>Alur pelaporan dan pengambilan</h2>
                <p>Laporan diperiksa petugas. Barang yang diklaim diserahkan kembali setelah kepemilikan diverifikasi.</p>
            </div>
            <div class="home-steps">
                <article class="home-step home-step--mint">
                    <span class="home-step__number">01</span>
                    <span class="home-step__icon"><x-icon name="scan-search" :size="25" /></span>
                    <h3>Cari atau laporkan</h3>
                    <p>Telusuri katalog. Belum ketemu? Buat laporan barang hilang atau temuan dengan beberapa detail.</p>
                    <a href="{{ route('catalog.index') }}">Mulai mencari <span aria-hidden="true">→</span></a>
                </article>
                <article class="home-step home-step--lime">
                    <span class="home-step__number">02</span>
                    <span class="home-step__icon"><x-icon name="shield-check" :size="25" /></span>
                    <h3>Petugas memverifikasi</h3>
                    <p>Laporan ditinjau sebelum muncul di katalog. Klaim diperiksa dengan pertanyaan keamanan dan bukti.</p>
                    <span class="home-step__note">Privasi dan keamanan diutamakan</span>
                </article>
                <article class="home-step home-step--blue">
                    <span class="home-step__number">03</span>
                    <span class="home-step__icon"><x-icon name="heart-handshake" :size="25" /></span>
                    <h3>Serah terima di kampus</h3>
                    <p>Setelah klaim disetujui, tunjukkan kode unik dan identitasmu kepada petugas Pos Satpam.</p>
                    <a href="{{ route('catalog.index', ['type' => 'ditemukan']) }}">Lihat barang temuan <span aria-hidden="true">→</span></a>
                </article>
            </div>
        </div>
    </section>

    <section class="home-safety">
        <div class="home-shell home-safety__layout">
            <div class="home-safety__visual" aria-hidden="true">
                <div class="home-safety__ring home-safety__ring--outer"></div>
                <div class="home-safety__ring home-safety__ring--inner"></div>
                <div class="home-safety__shield"><x-icon name="shield-check" :size="56" /></div>
                <div class="home-safety__tag home-safety__tag--top">Pertanyaan rahasia</div>
                <div class="home-safety__tag home-safety__tag--bottom">Kode sekali pakai</div>
                <span class="home-safety__dot home-safety__dot--one"></span>
                <span class="home-safety__dot home-safety__dot--two"></span>
            </div>
            <div class="home-safety__copy">
                <h2>Pengambilan barang<br><span>melalui petugas kampus.</span></h2>
                <p>
                    Detail penting barang temuan tidak diumbar di katalog. Pemilik membuktikan kepemilikan, petugas memeriksa klaim, lalu serah terima dilakukan langsung di pos satpam.
                </p>
                <ul>
                    <li><x-icon name="circle-check" :size="18" /> Jawaban keamanan tersimpan dengan aman</li>
                    <li><x-icon name="circle-check" :size="18" /> Kode serah terima unik dan sekali pakai</li>
                    <li><x-icon name="circle-check" :size="18" /> Setiap penyerahan tercatat</li>
                </ul>
                <a href="{{ route('catalog.index', ['type' => 'ditemukan']) }}" class="btn btn--primary">Cari barang temuan</a>
            </div>
        </div>
    </section>

    <section class="home-cta">
        <div class="home-cta__decoration home-cta__decoration--one" aria-hidden="true">✳</div>
        <div class="home-cta__decoration home-cta__decoration--two" aria-hidden="true">✦</div>
        <div class="home-shell home-cta__inner">
            <h2>Cari atau laporkan barang</h2>
            <p>Buka katalog untuk mencari barang, atau buat laporan jika kamu kehilangan atau menemukan sesuatu.</p>
            <div class="home-hero__actions">
                <a href="{{ route('catalog.index') }}" class="btn btn--primary">Jelajahi Katalog</a>
                <a href="{{ route('found.create') }}" class="btn btn--outline">Lapor Barang Temuan</a>
            </div>
        </div>
    </section>
@endsection
