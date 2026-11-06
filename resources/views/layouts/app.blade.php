<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Campus Lost & Found')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lostfound.css') }}">
</head>
<body>
    <header class="nav">
        <a href="{{ route('catalog.index') }}" class="brand" aria-label="Campus Lost & Found">
            <span class="brand__mark"><x-icon name="scan-search" :size="25" /></span>
            <span class="brand__name"><strong>Campus</strong><span>Lost &amp; Found</span></span>
        </a>

        <nav class="nav__menu" aria-label="Menu utama">
            <a href="{{ route('catalog.index') }}" class="{{ request()->routeIs('catalog.*') ? 'is-active' : '' }}">Katalog</a>
            
            @auth
                <a href="{{ route('lost.create') }}" class="{{ request()->routeIs('lost.create') ? 'is-active' : '' }}">+ Lapor Hilang</a>
                <a href="{{ route('found.create') }}" class="{{ request()->routeIs('found.create') ? 'is-active' : '' }}">+ Lapor Temuan</a>
                <a href="{{ route('my.history') }}" class="{{ request()->routeIs('my.history') ? 'is-active' : '' }}">Riwayat Saya</a>
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'is-active' : '' }}" style="color: #166534; font-weight: 800;">Panel Admin</a>
                @endif
            @endauth
        </nav>

        <div class="nav__account" style="display: flex; align-items: center; gap: 8px;">
            @guest
                <a href="{{ route('login') }}" class="btn btn--ghost">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn--primary">Daftar</a>
            @endguest
            @auth
                <span style="font-size: 13px; font-weight: 700; color: #111;">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn--ghost" style="padding: 6px 12px; font-size: 12px;">Keluar</button>
                </form>
            @endauth
        </div>
    </header>

    <main>@yield('content')</main>

    <footer class="footer">
        <p style="margin:0">&copy; {{ date('Y') }} Campus Lost &amp; Found</p>
        <p style="margin:0">Saling bantu, barang kembali. &nbsp;·&nbsp; Pos Satpam Kampus &nbsp;·&nbsp; <a href="{{ route('catalog.index') }}">Katalog</a></p>
    </footer>
</body>
</html>
