<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Lost & Found Kampus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: linear-gradient(135deg, #c7d9c6 0%, #dce6d5 50%, #b5cbb4 100%);
            color: #111827;
        }
        /* Panel Kiri */
        .auth-left {
            background: #23593a;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3.5rem 4rem;
            color: #ffffff;
        }
        .auth-left::before {
            content: '';
            position: absolute;
            top: -15%;
            right: -10%;
            width: 65%;
            height: 130%;
            background: #2d6a47;
            border-radius: 50% 0 0 50%;
            z-index: 0;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            bottom: -10%;
            right: 5%;
            width: 240px;
            height: 240px;
            background: rgba(16, 48, 30, 0.35);
            clip-path: polygon(20% 0%, 40% 0%, 25% 65%, 85% 40%, 95% 58%, 12% 95%);
            z-index: 0;
        }
        .brand, .hero-copy, .hero-badges { position: relative; z-index: 1; }
        .brand {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            text-decoration: none;
            color: #fff;
        }
        .hero-copy h1 {
            font-size: clamp(2.1rem, 3.2vw, 3rem);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.03em;
            margin-bottom: 1rem;
        }
        .hero-copy p {
            font-size: 1.05rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.6;
            max-width: 420px;
        }
        .hero-badges {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }
        .badge-pill {
            padding: 0.45rem 1.15rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 700;
        }
        .badge-lost { background: #ffffff; color: #111827; }
        .badge-found { background: #10b981; color: #ffffff; }

        /* Panel Kanan */
        .auth-right {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }
        .glass-card {
            width: 100%;
            max-width: 440px;
            background: rgba(244, 247, 243, 0.82);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.75);
            border-radius: 28px;
            padding: 2.25rem 2.25rem 2.5rem;
            box-shadow: 0 24px 48px -12px rgba(22, 42, 30, 0.14);
        }
        .tab-switcher {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.65rem;
            margin-bottom: 1.75rem;
        }
        .tab-btn {
            text-align: center;
            padding: 0.7rem 1rem;
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .tab-btn.active {
            background: #111111;
            color: #ffffff;
        }
        .tab-btn.inactive {
            background: #ffffff;
            color: #374151;
        }
        .tab-btn.inactive:hover {
            background: #f3f4f6;
        }
        .card-title {
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #111111;
            margin-bottom: 0.35rem;
        }
        .card-subtitle {
            font-size: 0.875rem;
            color: #4b5563;
            margin-bottom: 1.5rem;
        }
        .form-group { margin-bottom: 1.15rem; }
        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.45rem;
        }
        .form-input {
            width: 100%;
            padding: 0.85rem 1.2rem;
            border-radius: 999px;
            border: 1.5px solid transparent;
            background: #ffffff;
            font-family: inherit;
            font-size: 0.92rem;
            color: #111827;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-input:focus {
            border-color: #111111;
            box-shadow: 0 0 0 3px rgba(17, 17, 17, 0.08);
        }
        .form-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.82rem;
            margin: 0.5rem 0 1.4rem;
            color: #4b5563;
        }
        .form-meta a {
            color: #111111;
            font-weight: 600;
            text-decoration: none;
        }
        .btn-submit {
            width: 100%;
            padding: 0.9rem 1.5rem;
            border-radius: 999px;
            border: none;
            background: #111111;
            color: #ffffff;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.15s ease, background 0.2s ease;
        }
        .btn-submit:hover {
            background: #262626;
            transform: translateY(-1px);
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.75rem 1rem;
            border-radius: 14px;
            font-size: 0.83rem;
            margin-bottom: 1.2rem;
            font-weight: 500;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            padding: 0.75rem 1rem;
            border-radius: 14px;
            font-size: 0.83rem;
            margin-bottom: 1.2rem;
            font-weight: 500;
        }
        @media (max-width: 900px) {
            body { grid-template-columns: 1fr; }
            .auth-left { display: none; }
        }
    </style>
</head>
<body>
    @php
        $campusParam = request()->route('campus');
        $campusSlug = is_object($campusParam)
            ? $campusParam->slug
            : ($campusParam ?: ($currentCampus->slug ?? request()->segment(2)));
    @endphp

    <section class="auth-left">
        <a href="{{ Route::has('home') ? route('home', ['campus' => $campusSlug]) : url('/kampus/' . $campusSlug) }}" class="brand">
            Lost &amp; Found
        </a>

        <div class="hero-copy">
            <h1>Barang hilang, kembali ke pemiliknya.</h1>
            <p>Lapor, cari, dan verifikasi kepemilikan barang di lingkungan kampus dengan aman.</p>
        </div>

        <div class="hero-badges">
            <span class="badge-pill badge-lost">Hilang</span>
            <span class="badge-pill badge-found">Ditemukan</span>
        </div>
    </section>

    <section class="auth-right">
        <div class="glass-card">
            <div class="tab-switcher">
                <a href="{{ route('login', ['campus' => $campusSlug]) }}" class="tab-btn active">Masuk</a>
                <a href="{{ route('register', ['campus' => $campusSlug]) }}" class="tab-btn inactive">Daftar</a>
            </div>

            <h2 class="card-title">Selamat datang kembali</h2>
            <p class="card-subtitle">Masuk menggunakan akun civitas kampus Anda.</p>

            @if (session('status'))
                <div class="alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login', ['campus' => $campusSlug]) }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email Kampus</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="nama@kampus.ac.id"
                        class="form-input"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Kata sandi</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="••••••••••"
                        class="form-input"
                        required
                    >
                </div>

                <div class="form-meta">
                    <label style="display:flex; align-items:center; gap:0.4rem; cursor:pointer;">
                        <input type="checkbox" name="remember" style="accent-color:#111111;"> Ingat saya
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request', ['campus' => $campusSlug]) }}">Lupa sandi?</a>
                    @endif
                </div>

                <button type="submit" class="btn-submit">Masuk</button>
            </form>
        </div>
    </section>
</body>
</html>