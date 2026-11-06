@extends('layouts.app')

@section('title', 'Pilih Kampus | Lost & Found')

@section('content')
<main class="campus-directory">
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
                    <span class="campus-directory__icon" aria-hidden="true">↗</span>
                    <span>
                        <strong>{{ $campus->name }}</strong>
                        <small>Portal barang hilang dan temuan</small>
                    </span>
                </a>
            @endforeach
        </div>
    @endif
</main>
@endsection
