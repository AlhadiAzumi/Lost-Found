@extends('layouts.app')

@section('title', 'Masuk — Campus Lost & Found')

@section('content')
<div class="container" style="max-width: 440px; margin: 48px auto; padding: 0 16px;">
    <div class="card" style="padding: 32px; border: 2px solid #111; border-radius: 20px; box-shadow: 6px 6px 0 #111; background: #fff;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="display: inline-flex; padding: 12px; background: #bef264; border: 1.5px solid #111; border-radius: 14px; margin-bottom: 12px;">
                <x-icon name="log-in" :size="28" />
            </div>
            <h1 style="font-size: 24px; font-weight: 800; margin: 0 0 6px; color: #111;">Masuk ke Akun</h1>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Gunakan akun kampus untuk lapor atau klaim barang.</p>
        </div>

        @if ($errors->any())
            <div style="margin-bottom: 20px; padding: 12px; background: #fee2e2; border: 1.5px solid #dc2626; border-radius: 10px; font-size: 14px; color: #991b1b;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" style="display: flex; flex-direction: column; gap: 16px;">
            @csrf
            <div>
                <label for="email" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       class="input" style="width: 100%; box-sizing: border-box;" placeholder="nama@campus.ac.id">
            </div>

            <div>
                <label for="password" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Kata Sandi</label>
                <input type="password" id="password" name="password" required
                       class="input" style="width: 100%; box-sizing: border-box;" placeholder="••••••••">
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                    <input type="checkbox" name="remember" style="accent-color: #111;"> Ingat saya
                </label>
            </div>

            <button type="submit" class="btn btn--primary" style="width: 100%; padding: 12px; font-size: 15px; margin-top: 8px;">
                Masuk Sekarang
            </button>
        </form>

        <div style="margin-top: 24px; text-align: center; font-size: 14px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 16px;">
            Belum punya akun? <a href="{{ route('register') }}" style="font-weight: 700; color: #111; text-decoration: underline;">Daftar di sini</a>
        </div>
    </div>
</div>
@endsection

