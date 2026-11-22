<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — Lost & Found Kampus</title>
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
        .hero-badges { display: flex; gap: 0.75rem; align-items: center; }
        .badge-pill {
            padding: 0.45rem 1.15rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 700;
        }
        .badge-lost { background: #ffffff; color: #111827; }
        .badge-found { background: #10b981; color: #ffffff; }

        .auth-right {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .glass-card {
            width: 100%;
            max-width: 470px;
            background: rgba(244, 247, 243, 0.84);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.75);
            border-radius: 28px;
            padding: 2rem 2.25rem 2.25rem;
            box-shadow: 0 24px 48px -12px rgba(22, 42, 30, 0.14);
        }
        .tab-switcher {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.65rem;
            margin-bottom: 1.5rem;
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
        .tab-btn.active { background: #111111; color: #ffffff; }
        .tab-btn.inactive { background: #ffffff; color: #374151; }
        .tab-btn.inactive:hover { background: #f3f4f6; }

        .card-title {
            font-size: 1.55rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #111111;
            margin-bottom: 0.3rem;
        }
        .card-subtitle {
            font-size: 0.85rem;
            color: #4b5563;
            margin-bottom: 1.25rem;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.85rem;
        }
        .form-group { margin-bottom: 0.95rem; }
        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.35rem;
        }
        .form-input {
            width: 100%;
            padding: 0.78rem 1.1rem;
            border-radius: 999px;
            border: 1.5px solid transparent;
            background: #ffffff;
            font-family: inherit;
            font-size: 0.88rem;
            color: #111827;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-input:focus {
            border-color: #111111;
            box-shadow: 0 0 0 3px rgba(17, 17, 17, 0.08);
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
            margin-top: 0.5rem;
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
            font-size: 0.82rem;
            margin-bottom: 1rem;
        }
        @media (max-width: 900px) {
            body { grid-template-columns: 1fr; }
            .auth-left { display: none; }
            .form-grid { grid-template-columns: 1fr; gap: 0; }
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
            <p>Buat akun civitas kampus untuk melaporkan temuan, mengajukan klaim, dan memantau status barang.</p>
        </div>

        <div class="hero-badges">
            <span class="badge-pill badge-lost">Hilang</span>
            <span class="badge-pill badge-found">Ditemukan</span>
        </div>
    </section>

    <section class="auth-right">
        <div class="glass-card">
            <div class="tab-switcher">
                <a href="{{ route('login', ['campus' => $campusSlug]) }}" class="tab-btn inactive">Masuk</a>
                <a href="{{ route('register', ['campus' => $campusSlug]) }}" class="tab-btn active">Daftar</a>
            </div>

            <h2 class="card-title">Buat akun baru</h2>
            <p class="card-subtitle">Gunakan email resmi kampus yang terdaftar.</p>

            @if ($errors->any())
                <div class="alert-error">
                    <ul style="padding-left: 1.1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register', ['campus' => $campusSlug]) }}">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Nama lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Nama Anda" class="form-input" required autofocus>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="nim_nip" class="form-label">NIM / NIP</label>
                        <input id="nim_nip" type="text" name="nim_nip" value="{{ old('nim_nip') }}" placeholder="2025123456" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">No. WhatsApp</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="0812xxxxxxxx" class="form-input" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Kampus</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@kampus.ac.id" class="form-input" required>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="password" class="form-label">Kata sandi</label>
                        <input id="password" type="password" name="password" placeholder="Min. 8 karakter" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Konfirmasi sandi</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Ulangi sandi" class="form-input" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Daftar Sekarang</button>
            </form>
        </div>
    </section>
</body>
</html>