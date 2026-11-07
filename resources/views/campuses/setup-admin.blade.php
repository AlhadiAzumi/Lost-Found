@extends('layouts.app')

@section('title', 'Buat Akun Admin Kampus | Lost & Found')

@section('content')
<div class="auth-wrap">
    <div class="auth-card" style="max-width: 500px;">
        <div style="text-align:center; margin-bottom:24px;">
            <div class="auth-card__icon" style="background: var(--mint);">
                <x-icon name="map-pin" :size="26" />
            </div>
            <h1 class="auth-card__title">Aktifkan portal {{ $application->name }}</h1>
            <p class="auth-card__sub">Buat akun admin kampus menggunakan email {{ $application->email }}.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert--error">
                <x-icon name="circle-x" :size="18" />
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('campus-applications.setup.complete', ['application' => $application->id, 'token' => $token, 'campus' => null]) }}" class="auth-card__form">
            @csrf
            <div class="field">
                <label class="label label--required">Nama lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus class="input" autocomplete="name">
            </div>
            <div class="field">
                <label class="label label--required">Kata sandi</label>
                <input type="password" name="password" required class="input" autocomplete="new-password">
            </div>
            <div class="field">
                <label class="label label--required">Ulangi kata sandi</label>
                <input type="password" name="password_confirmation" required class="input" autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn--primary btn--block">Buat Akun Admin</button>
        </form>
    </div>
</div>
@endsection
