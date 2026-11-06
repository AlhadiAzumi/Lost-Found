@extends('layouts.app')

@section('title', 'Daftar Akun — Campus Lost & Found')

@section('content')
<div class="container" style="max-width: 480px; margin: 40px auto; padding: 0 16px;">
    <div class="card" style="padding: 32px; border: 2px solid #111; border-radius: 20px; box-shadow: 6px 6px 0 #111; background: #fff;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="display: inline-flex; padding: 12px; background: #afead4; border: 1.5px solid #111; border-radius: 14px; margin-bottom: 12px;">
                <x-icon name="user-plus" :size="28" />
            </div>
            <h1 style="font-size: 24px; font-weight: 800; margin: 0 0 6px; color: #111;">Registrasi Akun</h1>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Daftarkan dirimu untuk berpartisipasi menjaga barang di kampus.</p>
        </div>

        @if ($errors->any())
            <div style="margin-bottom: 20px; padding: 12px; background: #fee2e2; border: 1.5px solid #dc2626; border-radius: 10px; font-size: 14px; color: #991b1b;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" style="display: flex; flex-direction: column; gap: 16px;">
            @csrf
            <div>
                <label for="name" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Nama Lengkap *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                       class="input" style="width: 100%; box-sizing: border-box;" placeholder="Mis. Ahmad Farhan">
            </div>

            <div>
                <label for="email" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Alamat Email Kampus *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="input" style="width: 100%; box-sizing: border-box;" placeholder="nama@campus.ac.id">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label for="nim_nip" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">NIM / NIP</label>
                    <input type="text" id="nim_nip" name="nim_nip" value="{{ old('nim_nip') }}"
                           class="input" style="width: 100%; box-sizing: border-box;" placeholder="210100...">
                </div>
                <div>
                    <label for="phone_number" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">No. WhatsApp</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                           class="input" style="width: 100%; box-sizing: border-box;" placeholder="0812...">
                </div>
            </div>

            <div>
                <label for="password" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Kata Sandi *</label>
                <input type="password" id="password" name="password" required
                       class="input" style="width: 100%; box-sizing: border-box;" placeholder="Minimal 6 karakter">
            </div>

            <div>
                <label for="password_confirmation" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Ulangi Kata Sandi *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="input" style="width: 100%; box-sizing: border-box;" placeholder="Ulangi kata sandi">
            </div>

            <button type="submit" class="btn btn--primary" style="width: 100%; padding: 12px; font-size: 15px; margin-top: 8px;">
                Daftar Sekarang
            </button>
        </form>

        <div style="margin-top: 24px; text-align: center; font-size: 14px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 16px;">
            Sudah punya akun? <a href="{{ route('login') }}" style="font-weight: 700; color: #111; text-decoration: underline;">Masuk di sini</a>
        </div>
    </div>
</div>
@endsection

