@extends('layouts.app')

@section('title', 'Daftar Akun — Campus Lost & Found')

@section('content')
<div class="auth-wrap">
    <div class="auth-card" style="max-width: 500px;">
        <div style="text-align:center; margin-bottom:24px;">
            <div class="auth-card__icon" style="background: var(--mint);">
                <x-icon name="user-plus" :size="26" />
            </div>
            <h1 class="auth-card__title">Registrasi Akun</h1>
            <p class="auth-card__sub">Daftarkan dirimu untuk berpartisipasi menjaga barang di kampus.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert--error">
                <x-icon name="circle-x" :size="18" />
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="auth-card__form">
            @csrf

            <div class="field">
                <label class="label label--required">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="input" placeholder="Mis. Ahmad Farhan">
            </div>

            <div class="field">
                <label class="label label--required">Alamat Email Kampus</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="input" placeholder="nama@campus.ac.id">
            </div>

            <div class="form-grid-2">
                <div class="field">
                    <label class="label">NIM / NIP</label>
                    <input type="text" name="nim_nip" value="{{ old('nim_nip') }}"
                           class="input" placeholder="210100...">
                </div>
                <div class="field">
                    <label class="label">No. WhatsApp</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                           class="input" placeholder="0812...">
                </div>
            </div>

            <div class="field">
                <label class="label label--required">Kata Sandi</label>
                <input type="password" name="password" required
                       class="input" placeholder="Minimal 8 karakter">
            </div>

            <div class="field">
                <label class="label label--required">Ulangi Kata Sandi</label>
                <input type="password" name="password_confirmation" required
                       class="input" placeholder="Ulangi kata sandi">
            </div>

            <button type="submit" class="btn btn--primary btn--block" style="padding:13px; font-size:15px; margin-top:4px;">
                Daftar Sekarang
            </button>
        </form>

        <div class="auth-card__footer">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
        </div>
    </div>
</div>
@endsection
