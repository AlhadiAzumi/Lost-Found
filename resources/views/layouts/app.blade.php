<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Campus Lost & Found')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lostfound.css') }}">
</head>
<body>
    {{-- Floating Pill Navbar (Modern & Neo-Brutalist) --}}
    <div class="nav-wrapper">
        <header class="nav">
            <a href="{{ route('catalog.index') }}" class="brand" aria-label="Campus Lost & Found">
                <span class="brand__mark"><x-icon name="scan-search" :size="22" /></span>
                <span class="brand__name">
                    <strong>Campus</strong>
                    <span>Lost &amp; Found</span>
                </span>
            </a>

            <nav class="nav__menu" aria-label="Menu navigasi">
                <a href="{{ route('catalog.index') }}" class="nav__link {{ request()->routeIs('catalog.*') ? 'is-active' : '' }}">
                    Katalog
                </a>
                
                @auth
                    <a href="{{ route('lost.create') }}" class="nav__link {{ request()->routeIs('lost.create') ? 'is-active' : '' }}">
                        + Lapor Hilang
                    </a>
                    <a href="{{ route('found.create') }}" class="nav__link {{ request()->routeIs('found.create') ? 'is-active' : '' }}">
                        + Lapor Temuan
                    </a>
                    <a href="{{ route('my.history') }}" class="nav__link {{ request()->routeIs('my.history') ? 'is-active' : '' }}">
                        Riwayat Saya
                    </a>
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="nav__link nav__link--admin {{ request()->routeIs('admin.*') ? 'is-active' : '' }}">
                            🛡️ Panel Admin
                        </a>
                    @endif
                @endauth
            </nav>

            <div class="nav__account">
                @guest
                    <a href="{{ route('login') }}" class="btn btn--ghost" style="padding: 8px 14px;">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn--primary" style="padding: 8px 16px;">Daftar</a>
                @endguest
                @auth
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 13px; font-weight: 800; color: #111;">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0;">
                            @csrf
                            <button type="submit" class="btn btn--ghost" style="padding: 6px 12px; font-size: 12px; color: #ef4444;">Keluar</button>
                        </form>
                    </div>
                @endauth
            </div>
        </header>
    </div>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <p style="margin: 0;">&copy; {{ date('Y') }} <strong>Campus Lost &amp; Found</strong> · Universitas</p>
        <div style="display: flex; gap: 16px; align-items: center;">
            <span>Pos Satpam Kampus Utama</span>
            <span>·</span>
            <a href="{{ route('catalog.index') }}">Katalog Barang</a>
        </div>
    </footer>
</body>
</html>
