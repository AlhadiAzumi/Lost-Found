<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Lost & Found')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"></noscript>
    <link rel="stylesheet" href="{{ asset('css/lostfound.css') }}?v={{ filemtime(public_path('css/lostfound.css')) }}">
</head>
<body class="{{ request()->routeIs('home') ? 'home-page' : (request()->routeIs('catalog.*') ? 'catalog-page' : '') }}">

    {{-- ===== NAVBAR ===== --}}
    <div class="nav-wrapper">
        <header class="nav">
            {{-- Brand --}}
            <a href="{{ route('home') }}" class="brand" aria-label="Lost & Found">
                <img src="{{ asset('images/logo-mark.jpg') }}" alt="" class="brand__logo-img">
            </a>

            {{-- Desktop Menu --}}
            <nav class="nav__menu" aria-label="Menu utama">
                <a href="{{ route('home') }}" class="nav__link {{ request()->routeIs('home') ? 'is-active' : '' }}">
                    Beranda
                </a>
                @if (isset($currentCampus))
                    <a href="{{ route('campuses.index') }}" class="nav__link nav__link--campus" title="Ganti kampus">
                        {{ $currentCampus->name }}
                    </a>
                @endif
                <a href="{{ route('catalog.index') }}" class="nav__link {{ request()->routeIs('catalog.*') ? 'is-active' : '' }}">
                    Katalog
                </a>
                <a href="{{ route('pages.about') }}" class="nav__link {{ request()->routeIs('pages.about') ? 'is-active' : '' }}">
                    Tentang
                </a>
                <a href="{{ route('pages.safety') }}" class="nav__link {{ request()->routeIs('pages.safety') ? 'is-active' : '' }}">
                    Keamanan
                </a>
                <a href="{{ route('pages.contact') }}" class="nav__link {{ request()->routeIs('pages.contact') ? 'is-active' : '' }}">
                    Bantuan
                </a>
                <details class="nav__dropdown {{ request()->routeIs('pages.guide', 'pages.privacy', 'pages.terms') ? 'is-active' : '' }}">
                    <summary class="nav__link {{ request()->routeIs('pages.guide', 'pages.privacy', 'pages.terms') ? 'is-active' : '' }}">
                        Lainnya
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </summary>
                    <div class="nav__dropdown-menu">
                        <a href="{{ route('pages.guide') }}" class="nav__dropdown-link {{ request()->routeIs('pages.guide') ? 'is-active' : '' }}">Panduan Penggunaan</a>
                        <a href="{{ route('pages.privacy') }}" class="nav__dropdown-link {{ request()->routeIs('pages.privacy') ? 'is-active' : '' }}">Privasi Data</a>
                        <a href="{{ route('pages.terms') }}" class="nav__dropdown-link {{ request()->routeIs('pages.terms') ? 'is-active' : '' }}">Ketentuan Penggunaan</a>
                    </div>
                </details>
                @auth
                    <a href="{{ route('lost.create') }}" class="nav__link {{ request()->routeIs('lost.create') ? 'is-active' : '' }}">
                        Lapor Hilang
                    </a>
                    <a href="{{ route('found.create') }}" class="nav__link {{ request()->routeIs('found.create') ? 'is-active' : '' }}">
                        Lapor Temuan
                    </a>
                    <a href="{{ route('my.history') }}" class="nav__link {{ request()->routeIs('my.history') ? 'is-active' : '' }}">
                        Riwayat
                    </a>
                    @if (in_array(auth()->user()->role, ['admin', 'satpam'], true))
                        <a href="{{ route('admin.dashboard') }}" class="nav__link nav__link--admin {{ request()->routeIs('admin.*') ? 'is-active' : '' }}">
                            Panel Admin
                        </a>
                    @endif
                    @if (auth()->user()->role === 'admin' && filled(config('platform.admin_email')) && strcasecmp(auth()->user()->email, config('platform.admin_email')) === 0)
                        <a href="{{ route('platform.campuses.index', ['campus' => null]) }}" class="nav__link nav__link--admin {{ request()->routeIs('platform.campuses.*') ? 'is-active' : '' }}">
                            Kelola Kampus
                        </a>
                    @endif
                @endauth
            </nav>

            {{-- Desktop Account --}}
            <div class="nav__account">
                @guest
                    <a href="{{ route('login') }}" class="btn btn--outline btn--sm">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn--primary btn--sm">Daftar</a>
                @endguest
                @auth
                    <div class="nav__user">
                        <a href="{{ route('profile.edit') }}" class="nav__user-name" title="Profil saya">
                            {{ auth()->user()->name }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" style="margin:0">
                            @csrf
                            <button type="submit" class="btn btn--ghost btn--sm" style="color:#ef4444;">Keluar</button>
                        </form>
                    </div>
                @endauth
            </div>

            {{-- Hamburger (mobile) --}}
            <button class="nav__hamburger" id="navHamburger" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="navDrawer">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
        </header>
    </div>

    {{-- ===== MOBILE DRAWER ===== --}}
    <div class="nav__drawer" id="navDrawer" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Menu navigasi">
        <div class="nav__drawer-overlay" id="navOverlay"></div>
        <div class="nav__drawer-panel">
            <div class="nav__drawer-close">
                <strong>Menu</strong>
                <button id="navDrawerClose" type="button" aria-label="Tutup menu">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <a href="{{ route('home') }}" class="nav__drawer-link {{ request()->routeIs('home') ? 'is-active' : '' }}">
                <x-icon name="heart-handshake" :size="18" />
                Beranda
            </a>
            @if (isset($currentCampus))
                <a href="{{ route('campuses.index') }}" class="nav__drawer-link">
                    <x-icon name="map-pin" :size="18" />
                    Ganti Kampus ({{ $currentCampus->name }})
                </a>
            @endif
            @auth
                @if (auth()->user()->role === 'admin' && filled(config('platform.admin_email')) && strcasecmp(auth()->user()->email, config('platform.admin_email')) === 0)
                    <a href="{{ route('platform.campuses.index', ['campus' => null]) }}" class="nav__drawer-link">
                        Kelola Kampus
                    </a>
                @endif
            @endauth
            <a href="{{ route('catalog.index') }}" class="nav__drawer-link {{ request()->routeIs('catalog.*') ? 'is-active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18M3 15h18M9 3v18M15 3v18" opacity=".3"/><rect width="18" height="18" x="3" y="3" rx="2"/></svg>
                Katalog Barang
            </a>
            <div class="nav__drawer-divider"></div>
            <a href="{{ route('pages.about') }}" class="nav__drawer-link {{ request()->routeIs('pages.about') ? 'is-active' : '' }}">
                <x-icon name="info" :size="18" />
                Tentang Layanan
            </a>
            <a href="{{ route('pages.guide') }}" class="nav__drawer-link {{ request()->routeIs('pages.guide') ? 'is-active' : '' }}">
                <x-icon name="list-checks" :size="18" />
                Panduan Penggunaan
            </a>
            <a href="{{ route('pages.safety') }}" class="nav__drawer-link {{ request()->routeIs('pages.safety') ? 'is-active' : '' }}">
                <x-icon name="shield-check" :size="18" />
                Keamanan
            </a>
            <a href="{{ route('pages.privacy') }}" class="nav__drawer-link {{ request()->routeIs('pages.privacy') ? 'is-active' : '' }}">
                <x-icon name="lock-keyhole" :size="18" />
                Privasi Data
            </a>
            <a href="{{ route('pages.contact') }}" class="nav__drawer-link {{ request()->routeIs('pages.contact') ? 'is-active' : '' }}">
                <x-icon name="life-buoy" :size="18" />
                Bantuan &amp; Kontak
            </a>
            <a href="{{ route('pages.terms') }}" class="nav__drawer-link {{ request()->routeIs('pages.terms') ? 'is-active' : '' }}">
                <x-icon name="file-text" :size="18" />
                Ketentuan Penggunaan
            </a>

            @auth
                <div class="nav__drawer-divider"></div>
                <a href="{{ route('lost.create') }}" class="nav__drawer-link {{ request()->routeIs('lost.create') ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    Lapor Barang Hilang
                </a>
                <a href="{{ route('found.create') }}" class="nav__drawer-link {{ request()->routeIs('found.create') ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/><path d="m8 11 3 3 4-4"/></svg>
                    Lapor Barang Temuan
                </a>
                <a href="{{ route('my.history') }}" class="nav__drawer-link {{ request()->routeIs('my.history') ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>
                    Riwayat Aktivitas
                </a>
                <a href="{{ route('profile.edit') }}" class="nav__drawer-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                    Profil Saya
                </a>
                @if (in_array(auth()->user()->role, ['admin', 'satpam'], true))
                    <div class="nav__drawer-divider"></div>
                    <a href="{{ route('admin.dashboard') }}" class="nav__drawer-link nav__drawer-link--admin {{ request()->routeIs('admin.*') ? 'is-active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/></svg>
                        Panel Admin
                    </a>
                @endif

                <div class="nav__drawer-actions">
                    <div style="font-size:13px; color:var(--muted); font-weight:600; padding: 0 2px;">
                        Masuk sebagai <strong style="color:var(--ink);">{{ auth()->user()->name }}</strong>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0">
                        @csrf
                        <button type="submit" class="btn btn--danger btn--block" style="justify-content: center;">
                            Keluar dari Akun
                        </button>
                    </form>
                </div>

            @else
                <div class="nav__drawer-divider"></div>
                <div class="nav__drawer-actions">
                    <a href="{{ route('login') }}" class="btn btn--outline btn--block" style="justify-content: center;">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn--primary btn--block" style="justify-content: center;">Daftar Sekarang</a>
                </div>
            @endauth
        </div>
    </div>

    {{-- ===== MAIN CONTENT ===== --}}
    <main>
        @yield('content')
    </main>

    {{-- ===== FOOTER ===== --}}
    <footer class="footer footer--site">
        <div class="footer__top">
            <div class="footer__identity">
                <a class="footer__brand" href="{{ route('home') }}">Lost &amp; Found</a>
                <p>Layanan informasi barang hilang dan temuan di area kampus.</p>
            </div>
            <nav class="footer__column" aria-label="Informasi layanan">
                <h2>Informasi</h2>
                <a href="{{ route('pages.about') }}">Tentang layanan</a>
                <a href="{{ route('pages.guide') }}">Panduan penggunaan</a>
                <a href="{{ route('pages.safety') }}">Keamanan</a>
            </nav>
            <nav class="footer__column" aria-label="Bantuan dan kebijakan">
                <h2>Bantuan</h2>
                <a href="{{ route('pages.contact') }}">Kontak &amp; bantuan</a>
                <a href="{{ route('pages.privacy') }}">Privasi data</a>
                <a href="{{ route('pages.terms') }}">Ketentuan penggunaan</a>
                <a href="{{ route('catalog.index') }}">Katalog barang</a>
            </nav>
        </div>
        <div class="footer__bottom">
            <p>&copy; {{ date('Y') }} Lost &amp; Found. Layanan untuk area kampus.</p>
        </div>
    </footer>

    {{-- ===== JS: Hamburger & Filter Toggle ===== --}}
    <script>
    (function() {
        var hamburger = document.getElementById('navHamburger');
        var drawer    = document.getElementById('navDrawer');
        var overlay   = document.getElementById('navOverlay');
        var closeBtn  = document.getElementById('navDrawerClose');

        function openDrawer() {
            drawer.classList.add('is-open');
            drawer.setAttribute('aria-hidden', 'false');
            hamburger.setAttribute('aria-expanded', 'true');
            hamburger.setAttribute('aria-label', 'Tutup menu');
            document.body.style.overflow = 'hidden';
            closeBtn.focus();
        }

        function closeDrawer() {
            if (!drawer.classList.contains('is-open')) return;

            drawer.classList.remove('is-open');
            drawer.setAttribute('aria-hidden', 'true');
            hamburger.setAttribute('aria-expanded', 'false');
            hamburger.setAttribute('aria-label', 'Buka menu');
            document.body.style.overflow = '';
            hamburger.focus();
        }

        if (hamburger && drawer && overlay && closeBtn) {
            hamburger.addEventListener('click', function() {
                if (hamburger.getAttribute('aria-expanded') === 'true') {
                    closeDrawer();
                } else {
                    openDrawer();
                }
            });
        }
        if (overlay)   overlay.addEventListener('click', closeDrawer);
        if (closeBtn)  closeBtn.addEventListener('click', closeDrawer);

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeDrawer();
        });

        // Filter toggle (catalog sidebar on mobile)
        var filterToggle = document.getElementById('filterToggle');
        var filterBody   = document.getElementById('filterBody');
        if (filterToggle && filterBody) {
            filterToggle.addEventListener('click', function() {
                var open = filterBody.classList.toggle('is-open');
                filterToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                filterToggle.querySelector('.filter-toggle__icon').textContent = open ? '−' : '+';
            });
        }
    })();
    </script>

</body>
</html>
