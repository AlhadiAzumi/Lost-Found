@extends('layouts.app')

@section('title', $item->title . ' | Lost & Found')

@section('content')
<div class="page-wrap page-wrap--medium">
    <a href="{{ route('catalog.index') }}" class="btn btn--ghost" style="margin-bottom:24px; display:inline-flex; align-items:center; gap:8px; padding-left:0;">
        <x-icon name="arrow-left" :size="18" /> Kembali ke Katalog
    </a>

    @if (session('success'))
        <div class="alert alert--success">
            <x-icon name="circle-check" :size="18" />
            <span><strong>Berhasil!</strong> {{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert--error">
            <x-icon name="circle-x" :size="18" />
            <span><strong>Peringatan:</strong> {{ session('error') }}</span>
        </div>
    @endif

    @php
        $isFound  = in_array($item->type, ['found', 'ditemukan']);
        $canClaim = $isFound && in_array($item->status, ['approved', 'disetujui']);
    @endphp

    <div class="card" style="padding:32px;">
        <div class="detail-grid">
            {{-- Foto Barang --}}
            <div>
                <div class="detail-photo">
                    <img src="{{ $item->photo_url }}" alt="{{ $item->title }}">
                    <div class="detail-photo__badge">
                        <x-badge :icon="$isFound ? 'search-check' : 'search'" :color="$isFound ? '#a3e636' : '#fee2e2'">
                            {{ $isFound ? 'Barang Ditemukan' : 'Barang Hilang' }}
                        </x-badge>
                    </div>
                </div>

                @if ($isFound && !empty($item->security_question))
                    <div class="card panel panel--mint" style="margin-top:16px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                            <x-icon name="shield-check" :size="16" />
                            <strong style="font-size:13.5px;color:#166534;">Dilindungi Pertanyaan Keamanan</strong>
                        </div>
                        <p style="margin:0;font-size:13px;color:#14532d;line-height:1.5;">
                            Calon pemilik wajib menjawab pertanyaan verifikasi dari penemu sebelum dapat mengambil barang.
                        </p>
                    </div>
                @endif
            </div>

            {{-- Detail Info --}}
            <div class="detail-info">
                {{-- Badges status --}}
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <x-badge :icon="$item->category->icon ?? 'tag'" :color="$item->category->color ?? '#dbeafe'">
                        {{ $item->category->name ?? 'Lainnya' }}
                    </x-badge>

                    @if(in_array($item->status, ['approved','disetujui']))
                        <x-badge icon="circle-check" color="#d1fae8">Aktif &amp; Disetujui</x-badge>
                    @elseif(in_array($item->status, ['claim_in_progress','proses_klaim']))
                        <x-badge icon="clock" color="#dbeafe">Dalam Proses Klaim</x-badge>
                    @elseif(in_array($item->status, ['returned','selesai']))
                        <x-badge icon="check-check" color="#a3e636">Sudah Dikembalikan</x-badge>
                    @else
                        <x-badge icon="clock" color="#fef3c7">{{ ucfirst($item->status) }}</x-badge>
                    @endif
                </div>

                <h1 class="detail-title">{{ $item->title }}</h1>

                <div class="detail-meta">
                    <div class="detail-meta-row">
                        <x-icon name="map-pin" :size="17" />
                        <span><strong>Lokasi:</strong> {{ $item->location }}</span>
                    </div>
                    <div class="detail-meta-row">
                        <x-icon name="calendar-days" :size="17" />
                        <span><strong>Tanggal:</strong> {{ $item->date_event ? $item->date_event->translatedFormat('d F Y') : '-' }}</span>
                    </div>
                    <div class="detail-meta-row">
                        <x-icon name="user" :size="17" />
                        <span><strong>Pelapor:</strong> {{ $item->user->name ?? 'Anonim Kampus' }}</span>
                    </div>
                </div>

                <div>
                    <h3 style="font-size:14px;font-weight:700;margin-bottom:8px;">Deskripsi Barang</h3>
                    <div class="detail-desc">{{ $item->description ?: 'Tidak ada deskripsi rinci.' }}</div>
                </div>
                @if ($item->status === 'rejected' && $item->rejection_reason)
                    <div class="alert alert--error">
                        <x-icon name="info" :size="18" />
                        <span><strong>Alasan penolakan:</strong> {{ $item->rejection_reason }}</span>
                    </div>
                @endif

                {{-- Tombol Klaim --}}
                <div class="detail-action">
                    @if ($canClaim)
                        @auth
                            @if (auth()->id() === $item->user_id)
                                <div class="card panel panel--amber" style="font-size:14px;">
                                    Ini adalah barang temuan yang kamu laporkan sendiri.
                                </div>
                            @else
                                <a href="{{ route('claims.create', $item) }}" class="btn btn--primary btn--block btn--lg" style="justify-content:center;">
                                    <x-icon name="handshake" :size="20" /> Ajukan Klaim Kepemilikan
                                </a>
                            @endif
                        @else
                            <div class="card panel panel--mint" style="text-align:center;">
                                <p style="margin-bottom:14px;font-size:14px;font-weight:600;">
                                    Merasa ini barang milikmu? Masuk ke akunmu untuk mengajukan verifikasi klaim.
                                </p>
                                <a href="{{ route('login') }}" class="btn btn--primary">Masuk &amp; Ajukan Klaim</a>
                            </div>
                        @endauth
                    @elseif($isFound)
                        <div class="card panel" style="text-align:center;color:var(--muted);font-size:14px;font-weight:600;">
                            Barang ini sedang dalam proses klaim atau sudah dikembalikan ke pemiliknya.
                        </div>
                    @else
                        <div class="alert alert--error" style="margin-bottom:0;">
                            <x-icon name="info" :size="18" />
                            <span><strong>Catatan:</strong> Jika kamu menemukan barang ini di kampus, silakan serahkan ke pos satpam atau buat laporan penemuan barang.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
