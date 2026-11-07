@php
    $pages = [
        403 => [
            'eyebrow' => 'Akses terbatas',
            'title' => 'Halaman ini khusus petugas kampus',
            'message' => 'Akunmu belum memiliki izin untuk membuka halaman ini. Pastikan kamu masuk dengan akun admin atau petugas dari portal kampus yang sesuai.',
            'illustration' => 'shield',
        ],
        404 => [
            'eyebrow' => 'Halaman tidak ditemukan',
            'title' => 'Sepertinya barang ini tersesat',
            'message' => 'Halaman yang kamu cari mungkin sudah dipindahkan, alamatnya berubah, atau tidak tersedia di portal ini.',
            'illustration' => 'search',
        ],
        500 => [
            'eyebrow' => 'Gangguan pada sistem',
            'title' => 'Kami sedang membereskan sesuatu',
            'message' => 'Terjadi kendala saat memuat halaman. Coba beberapa saat lagi; laporan dan data kampusmu tetap aman.',
            'illustration' => 'tools',
        ],
    ];

    $page = $pages[$code] ?? $pages[500];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $code }} — {{ $page['eyebrow'] }} | Lost &amp; Found</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo-mark.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"></noscript>
    <link rel="stylesheet" href="{{ asset('css/lostfound.css') }}?v={{ filemtime(public_path('css/lostfound.css')) }}">
</head>
<body class="error-page error-page--{{ $code }}">
    <header class="error-page__header">
        <a href="{{ route('campuses.index') }}" class="error-page__brand" aria-label="Lost &amp; Found — pilih kampus">
            <img src="{{ asset('images/logo-mark.jpg') }}" alt="">
            <span><strong>Campus Lost &amp; Found</strong><small>Portal barang hilang dan temuan</small></span>
        </a>
        <span class="error-page__campus"><span></span> Portal kampus</span>
    </header>

    <main class="error-page__main">
        <section class="error-page__content" aria-labelledby="error-title">
            <span class="error-page__eyebrow"><span></span>{{ $page['eyebrow'] }}</span>
            <p class="error-page__code">{{ $code }}</p>
            <h1 id="error-title">{{ $page['title'] }}</h1>
            <p class="error-page__message">{{ $page['message'] }}</p>
            <div class="error-page__actions">
                <a class="btn btn--primary" href="{{ route('campuses.index') }}">Pilih portal kampus</a>
                <button class="btn btn--outline" type="button" onclick="if (history.length > 1) { history.back(); } else { location.assign('/'); }">Kembali</button>
            </div>
            <p class="error-page__help">Butuh bantuan? Pilih portal kampus yang benar, lalu masuk dengan akunmu.</p>
        </section>

        <div class="error-page__art" aria-hidden="true">
            <div class="error-page__art-glow"></div>
            <div class="error-page__art-card">
                <div class="error-page__art-top"><span></span><span></span><span></span></div>
                <div class="error-page__art-scene">
                    <div class="error-page__art-sun"></div>
                    <div class="error-page__art-campus">
                        <svg viewBox="0 0 180 150" fill="none">
                            <path d="M22 63 90 25l68 38v8H22v-8Z" fill="#A3E636" stroke="#111827" stroke-width="4" stroke-linejoin="round"/>
                            <path d="M33 72h114v58H33z" fill="#fff" stroke="#111827" stroke-width="4"/>
                            <path d="M23 130h134v12H23z" fill="#D9F99D" stroke="#111827" stroke-width="4" stroke-linejoin="round"/>
                            <path d="M47 85h17v18H47zM82 85h17v18H82zM117 85h17v18h-17z" fill="#BAE6FD" stroke="#111827" stroke-width="3"/>
                            <path d="M79 108h22v22H79z" fill="#A3E636" stroke="#111827" stroke-width="3"/>
                            <path d="M90 25v-9M82 16h16M70 53h40" stroke="#111827" stroke-width="4" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="error-page__art-item">
                        @if ($page['illustration'] === 'shield')
                            <svg viewBox="0 0 64 64" fill="none">
                                <path d="M32 7 52 15v15c0 13-8.5 22-20 27C20.5 52 12 43 12 30V15l20-8Z" fill="#D9F99D" stroke="#111827" stroke-width="3.5" stroke-linejoin="round"/>
                                <path d="m23 32 6 6 13-14" stroke="#111827" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @elseif ($page['illustration'] === 'tools')
                            <svg viewBox="0 0 64 64" fill="none">
                                <path d="M37 12a17 17 0 0 0 19 21L34 55a8 8 0 0 1-11-11l22-22a17 17 0 0 0-8-10Z" fill="#BAE6FD" stroke="#111827" stroke-width="3.5" stroke-linejoin="round"/>
                                <circle cx="27" cy="48" r="3" fill="#111827"/>
                            </svg>
                        @else
                            <svg viewBox="0 0 64 64" fill="none">
                                <circle cx="28" cy="28" r="17" fill="#BAE6FD" stroke="#111827" stroke-width="3.5"/>
                                <path d="m41 41 13 13M21 28h14" stroke="#111827" stroke-width="4" stroke-linecap="round"/>
                            </svg>
                        @endif
                    </div>
                    <span class="error-page__art-spark error-page__art-spark--one">✦</span>
                    <span class="error-page__art-spark error-page__art-spark--two">✳</span>
                </div>
                <div class="error-page__art-caption">
                    <span><i></i><i></i><i></i></span>
                    <strong>Lost &amp; Found Campus</strong>
                    <small>Temukan portal yang tepat</small>
                </div>
            </div>
            <span class="error-page__art-note">Setiap barang punya jalan pulang.</span>
        </div>
    </main>

    <footer class="error-page__footer">
        <span>&copy; {{ date('Y') }} Campus Lost &amp; Found</span>
        <span>Ruang aman untuk barang kembali</span>
    </footer>
</body>
</html>
