@extends('layouts.app')

@section('title', 'Pilih Kampus | Lost & Found')

@section('content')
<main class="campus-directory">
    @if (session('application_submitted'))
        <div class="campus-application__notice">{{ session('application_submitted') }}</div>
    @endif
    @if (session('error'))
        <div class="campus-application__notice campus-application__notice--error">{{ session('error') }}</div>
    @endif

    <header class="campus-directory__header">
        <span class="campus-directory__eyebrow">Lost &amp; Found</span>
        <h1>Pilih portal kampusmu</h1>
        <p>Setiap kampus memiliki laporan, pengguna, dan petugasnya sendiri.</p>
    </header>

    @if ($campuses->isEmpty())
        <div class="campus-directory__empty">
            Belum ada portal kampus yang aktif. Hubungi pengelola untuk menyiapkannya.
        </div>
    @else
        <div class="campus-directory__list">
            @foreach ($campuses as $campus)
                <a class="campus-directory__card" href="{{ route('home', ['campus' => $campus->slug]) }}">
                    <span class="campus-directory__mark" aria-hidden="true">
                        <x-icon name="map-pin" :size="19" />
                    </span>
                    <span class="campus-directory__copy">
                        <strong>{{ $campus->name }}</strong>
                        <small>Portal barang hilang dan temuan</small>
                    </span>
                    <span class="campus-directory__arrow" aria-hidden="true">↗</span>
                </a>
            @endforeach
        </div>
    @endif

    <section class="campus-application" aria-labelledby="campus-application-title">
        <div class="campus-application__intro">
            <span class="campus-application__eyebrow">Belum menemukan kampusmu?</span>
            <h2 id="campus-application-title">Ajukan portal kampus baru</h2>
            <p>Isi nama kampus dan email yang belum terdaftar. Admin platform akan meninjau pengajuan; jika disetujui, tautan untuk membuat akun admin dikirim ke email tersebut.</p>
        </div>

        @if ($errors->any())
            <div class="campus-application__notice campus-application__notice--error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('campus-applications.store', ['campus' => null]) }}" class="campus-application__form">
            @csrf
            <div class="field">
                <label class="label label--required" for="application-campus-name">Nama kampus</label>
                <input id="application-campus-name" name="name" type="text" value="{{ old('name') }}" maxlength="255" required class="input" placeholder="Contoh: Universitas Nusantara">
            </div>
            <div class="field">
                <label class="label label--required" for="application-email">Email pemohon</label>
                <input id="application-email" name="email" type="email" value="{{ old('email') }}" maxlength="255" required class="input" placeholder="email@kampus.ac.id">
            </div>
            <button type="submit" class="btn btn--primary">Kirim Pengajuan</button>
        </form>
    </section>
</main>
@endsection
