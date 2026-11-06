@extends('layouts.app')

@section('title', 'Profil Pengguna — Campus Lost & Found')

@section('content')
<div class="container" style="max-width: 800px; margin: 36px auto; padding: 0 16px;">
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 26px; font-weight: 800; margin: 0 0 6px; color: #111;">Pengaturan Akun & Profil</h1>
        <p style="margin: 0; color: #6b7280; font-size: 14px;">Kelola informasi pribadi kampus dan kata sandi akunmu.</p>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="card panel panel--mint" style="margin-bottom: 24px; padding: 16px;">
            <strong>Berhasil!</strong> Informasi profil berhasil diperbarui.
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="card panel panel--mint" style="margin-bottom: 24px; padding: 16px;">
            <strong>Berhasil!</strong> Kata sandi akun berhasil diubah.
        </div>
    @endif

    <div style="display: flex; flex-direction: column; gap: 24px;">
        {{-- Card 1: Informasi Profil --}}
        <div class="card" style="padding: 28px; border: 2px solid #111; border-radius: 18px; box-shadow: 4px 4px 0 #111; background: #fff;">
            <h2 style="font-size: 18px; font-weight: 800; margin: 0 0 4px; color: #111;">Informasi Pribadi</h2>
            <p style="margin: 0 0 20px; font-size: 13px; color: #6b7280;">Pastikan data kontak seperti nomor WhatsApp dan NIM/NIP selalu akurat untuk keperluan verifikasi barang.</p>

            @if ($errors->any())
                <div style="margin-bottom: 20px; padding: 12px; background: #fee2e2; border: 1.5px solid #dc2626; border-radius: 10px; font-size: 13px; color: #991b1b;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                @method('patch')

                <div>
                    <label for="name" style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Nama Lengkap *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                           class="input" style="width: 100%; box-sizing: border-box;">
                </div>

                <div>
                    <label for="email" style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Alamat Email Kampus *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="input" style="width: 100%; box-sizing: border-box;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div>
                        <label for="nim_nip" style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">NIM / NIP</label>
                        <input type="text" id="nim_nip" name="nim_nip" value="{{ old('nim_nip', $user->nim_nip) }}"
                               class="input" style="width: 100%; box-sizing: border-box;">
                    </div>
                    <div>
                        <label for="phone_number" style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Nomor WhatsApp</label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                               class="input" style="width: 100%; box-sizing: border-box;">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 8px;">
                    <button type="submit" class="btn btn--primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>

        {{-- Card 2: Ubah Sandi --}}
        <div class="card" style="padding: 28px; border: 2px solid #111; border-radius: 18px; box-shadow: 4px 4px 0 #111; background: #fff;">
            <h2 style="font-size: 18px; font-weight: 800; margin: 0 0 4px; color: #111;">Perbarui Kata Sandi</h2>
            <p style="margin: 0 0 20px; font-size: 13px; color: #6b7280;">Gunakan kata sandi yang aman dan tidak mudah ditebak.</p>

            <form method="POST" action="{{ route('password.update') }}" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                @method('put')

                <div>
                    <label for="current_password" style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Kata Sandi Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" class="input" style="width: 100%; box-sizing: border-box;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div>
                        <label for="password" style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Kata Sandi Baru</label>
                        <input type="password" id="password" name="password" class="input" style="width: 100%; box-sizing: border-box;">
                    </div>
                    <div>
                        <label for="password_confirmation" style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="input" style="width: 100%; box-sizing: border-box;">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 8px;">
                    <button type="submit" class="btn btn--primary">Perbarui Kata Sandi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
