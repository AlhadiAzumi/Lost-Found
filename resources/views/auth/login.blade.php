@extends('layouts.app')

@section('title', 'Masuk — Campus Lost & Found')

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <div style="text-align:center; margin-bottom:24px;">
            <div class="auth-card__icon">
                <x-icon name="log-in" :size="26" />
            </div>
            <h1 class="auth-card__title">Masuk ke Akun</h1>
            <p class="auth-card__sub">Gunakan akun kampus untuk lapor atau klaim barang.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert--error">
                <x-icon name="circle-x" :size="18" />
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="auth-card__form">
            @csrf

            <div class="field">
                <label for="email" class="label label--required">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       class="input" placeholder="nama@campus.ac.id">
            </div>

            <div class="field">
                <label for="password" class="label label--required">Kata Sandi</label>
                <input type="password" id="password" name="password" required
                       class="input" placeholder="Minimal 8 karakter">
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; font-size:13px;">
                <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-weight:600;">
                    <input type="checkbox" name="remember" style="accent-color:#111;"> Ingat saya
                </label>
            </div>

            <button type="submit" class="btn btn--primary btn--block" style="padding:13px; font-size:15px; margin-top:4px;">
                Masuk Sekarang
            </button>
        </form>

        <div class="auth-card__footer">
            Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
        </div>
    </div>
</div>
@endsection
