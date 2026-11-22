<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Lost & Found Kampus')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #c6d8c5 0%, #dbe5d4 48%, #afc7ae 100%);
            color: #111827;
            position: relative;
            overflow-x: hidden;
        }
        /* Dekorasi daun blur di latar belakang sesuai mockup */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(45px);
            z-index: 0;
            pointer-events: none;
        }
        body::before {
            width: 380px;
            height: 380px;
            background: rgba(52, 105, 72, 0.22);
            bottom: -100px;
            right: -60px;
        }
        body::after {
            width: 300px;
            height: 300px;
            background: rgba(143, 188, 152, 0.35);
            top: -80px;
            left: 220px;
        }

        .app-shell {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
            padding: 1.25rem;
            gap: 1.75rem;
        }

        /* Floating Glass Sidebar */
        .sidebar {
            background: rgba(242, 246, 241, 0.78);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.75);
            border-radius: 28px;
            padding: 1.75rem 1.15rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 20px 40px -15px rgba(20, 45, 30, 0.1);
            height: calc(100vh - 2.5rem);
            position: sticky;
            top: 1.25rem;
        }
        .sidebar-brand {
            padding: 0 0.75rem 1.5rem;
        }
        .sidebar-brand a {
            font-size: 1.35rem;
            font-weight: 800;
            color: #111111;
            text-decoration: none;
            letter-spacing: -0.03em;
            display: block;
        }
        .campus-tag {
            display: inline-block;
            margin-top: 0.35rem;
            font-size: 0.72rem;
            font-weight: 700;
            color: #23593a;
            background: rgba(35, 89, 58, 0.12);
            padding: 0.2rem 0.65rem;
            border-radius: 999px;
        }

        .nav-list {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.78rem 1rem;
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #1f2937;
            text-decoration: none;
            transition: all 0.18s ease;
        }
        .nav-item:hover {
            background: rgba(255, 255, 255, 0.7);
            color: #111111;
        }
        .nav-item.active {
            background: #111111;
            color: #ffffff;
            box-shadow: 0 8px 18px -6px rgba(17, 17, 17, 0.35);
        }
        .nav-item svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .sidebar-footer {
            border-top: 1px solid rgba(17, 24, 39, 0.08);
            padding-top: 1rem;
        }
        .btn-logout {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.72rem 1rem;
            border-radius: 999px;
            border: none;
            background: transparent;
            color: #991b1b;
            font-family: inherit;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.18s;
        }
        .btn-logout:hover {
            background: rgba(254, 226, 226, 0.75);
        }

        /* Main Content */
        .main-content {
            padding: 0.5rem 0.75rem 2rem 0;
            max-width: 1220px;
            width: 100%;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .page-title {
            font-size: 1.85rem;
            font-weight: 800;
            color: #111111;
            letter-spacing: -0.03em;
        }

        /* Glass Card & Form Controls Global */
        .glass-panel {
            background: rgba(244, 247, 243, 0.8);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.78);
            border-radius: 26px;
            padding: 1.75rem;
            box-shadow: 0 20px 42px -15px rgba(22, 42, 30, 0.1);
        }
        .field-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.42rem;
        }
        .pill-input, .pill-select {
            width: 100%;
            padding: 0.82rem 1.15rem;
            border-radius: 999px;
            border: 1.5px solid transparent;
            background: #ffffff;
            font-family: inherit;
            font-size: 0.9rem;
            color: #111827;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .area-input {
            width: 100%;
            padding: 0.95rem 1.15rem;
            border-radius: 20px;
            border: 1.5px solid transparent;
            background: #ffffff;
            font-family: inherit;
            font-size: 0.9rem;
            color: #111827;
            outline: none;
            resize: vertical;
            min-height: 96px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .pill-input:focus, .pill-select:focus, .area-input:focus {
            border-color: #111111;
            box-shadow: 0 0 0 3px rgba(17, 17, 17, 0.08);
        }
        .btn-black {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.88rem 1.6rem;
            border-radius: 999px;
            border: none;
            background: #111111;
            color: #ffffff;
            font-family: inherit;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.15s ease, background 0.2s ease;
        }
        .btn-black:hover {
            background: #262626;
            transform: translateY(-1px);
        }

        .flash-banner {
            padding: 0.85rem 1.2rem;
            border-radius: 16px;
            font-size: 0.88rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }
        .flash-success { background: #d1fae5; color: #065f46; }
        .flash-error { background: #fee2e2; color: #991b1b; }

        @media (max-width: 960px) {
            .app-shell { grid-template-columns: 1fr; }
            .sidebar { height: auto; position: static; }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php
        $campusParam = request()->route('campus');
        $campusSlug = is_object($campusParam)
            ? $campusParam->slug
            : ($campusParam ?: ($currentCampus->slug ?? request()->segment(2)));
        $campusName = is_object($campusParam) ? $campusParam->name : ($currentCampus->name ?? strtoupper(str_replace('-', ' ', $campusSlug)));
        $user = auth()->user();
        $isAdmin = $user && in_array($user->role ?? '', ['admin', 'satpam', 'super_admin']);
    @endphp

    <div class="app-shell">
        <aside class="sidebar">
            <div>
                <div class="sidebar-brand">
                    <a href="{{ Route::has('home') ? route('home', ['campus' => $campusSlug]) : url('/kampus/' . $campusSlug) }}">
                        Lost &amp; Found
                    </a>
                    <span class="campus-tag">{{ $campusName }}</span>
                </div>

                <nav class="nav-list">
                    @auth
                        <a href="{{ route('profile.edit', ['campus' => $campusSlug]) }}"
                           class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M5 20a7 7 0 0 1 14 0"/></svg>
                            Profil
                        </a>

                        <a href="{{ route('lost.create', ['campus' => $campusSlug]) }}"
                           class="nav-item {{ request()->routeIs('lost.create') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                            Lapor Hilang
                        </a>

                        <a href="{{ route('found.create', ['campus' => $campusSlug]) }}"
                           class="nav-item {{ request()->routeIs('found.create') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                            Lapor Temuan
                        </a>
                    @endauth

                    <a href="{{ route('catalog.index', ['campus' => $campusSlug]) }}"
                       class="nav-item {{ request()->routeIs('catalog.*') || request()->routeIs('items.show') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                        Feed Publik
                    </a>

                    @auth
                        <a href="{{ $isAdmin ? route('admin.claims', ['campus' => $campusSlug]) : route('my.history', ['campus' => $campusSlug]) }}"
                           class="nav-item {{ request()->routeIs('admin.claims*') || request()->routeIs('my.history') || request()->routeIs('claims.*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/></svg>
                            Klaim
                        </a>

                        @if($isAdmin)
                            <a href="{{ route('admin.handovers', ['campus' => $campusSlug]) }}"
                               class="nav-item {{ request()->routeIs('admin.handovers*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h3v3h-3zM18 18h3v3h-3z"/></svg>
                                Serah Terima
                            </a>

                            <a href="{{ route('admin.dashboard', ['campus' => $campusSlug]) }}"
                               class="nav-item {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.reports*') ? 'active' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20V10M10 20V4M16 20v-7M22 20v-4"/></svg>
                                Moderasi Admin
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>

            <div class="sidebar-footer">
                @auth
                    <form method="POST" action="{{ Route::has('logout') ? route('logout', ['campus' => $campusSlug]) : url('/kampus/'.$campusSlug.'/logout') }}">
                        @csrf
                        <button type="submit" class="btn-logout">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login', ['campus' => $campusSlug]) }}" class="btn-black" style="width:100%;">Masuk Akun</a>
                @endauth
            </div>
        </aside>

        <main class="main-content">
            @if (session('success'))
                <div class="flash-banner flash-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="flash-banner flash-error">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="flash-banner flash-error">
                    <ul style="padding-left:1.2rem;">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>