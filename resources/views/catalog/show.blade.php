@extends('layouts.app')

@section('title', $item->title . ' — Detail Barang')

@section('content')
<div class="container" style="max-width: 900px; margin: 32px auto; padding: 0 16px;">
    <a href="{{ route('catalog.index') }}" class="btn btn--ghost" style="margin-bottom: 24px; display: inline-flex; align-items: center; gap: 8px;">
        <x-icon name="arrow-left" :size="20" /> Kembali ke Katalog
    </a>

    @if (session('success'))
        <div class="card panel panel--mint" style="margin-bottom: 24px; padding: 16px;">
            <strong>✓ Berhasil!</strong> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="card panel" style="margin-bottom: 24px; padding: 16px; background-color: #fee2e2; border-color: #dc2626;">
            <strong>✕ Peringatan:</strong> {{ session('error') }}
        </div>
    @endif

    @php
        $isFound = in_array($item->type, ['found', 'ditemukan']);
        $canClaim = $isFound && in_array($item->status, ['approved', 'disetujui']);
    @endphp

    <div class="card" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 32px; padding: 32px; border: 2px solid #111; border-radius: 20px; box-shadow: 6px 6px 0 #111; background: #fff;">
        {{-- Foto Barang --}}
        <div>
            <div style="position: relative; border-radius: 14px; overflow: hidden; border: 2px solid #111; aspect-ratio: 4/3; background: #f3f4f6;">
                <img src="{{ $item->photo_url }}" alt="{{ $item->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                <div style="position: absolute; top: 12px; left: 12px;">
                    <x-badge :icon="$isFound ? 'search-check' : 'search'" :color="$isFound ? '#a3e636' : '#fee2e2'">
                        {{ $isFound ? 'Barang Ditemukan' : 'Barang Hilang' }}
                    </x-badge>
                </div>
            </div>

            @if ($isFound && !empty($item->security_question))
                <div style="margin-top: 20px; padding: 16px; background: #f0fdf4; border: 1.5px solid #16a34a; border-radius: 12px;">
                    <strong style="display: block; color: #166534; font-size: 14px; margin-bottom: 4px;">
                        <x-icon name="shield-check" :size="16" /> Dilindungi Pertanyaan Keamanan
                    </strong>
                    <p style="margin: 0; font-size: 13px; color: #14532d;">
                        Calon pemilik wajib menjawab pertanyaan verifikasi dari penemu sebelum dapat mengambil barang.
                    </p>
                </div>
            @endif
        </div>

        {{-- Detail Informasi --}}
        <div style="display: flex; flex-direction: column;">
            <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 12px; flex-wrap: wrap;">
                <x-badge :icon="$item->category->icon ?? 'tag'" :color="$item->category->color ?? '#dbeafe'">
                    {{ $item->category->name ?? 'Lainnya' }}
                </x-badge>

                @if(in_array($item->status, ['approved', 'disetujui']))
                    <x-badge icon="circle-check" color="#d1fae8">Aktif & Disetujui</x-badge>
                @elseif(in_array($item->status, ['claim_in_progress', 'proses_klaim']))
                    <x-badge icon="clock" color="#dbeafe">Dalam Proses Klaim</x-badge>
                @elseif(in_array($item->status, ['returned', 'selesai']))
                    <x-badge icon="check-check" color="#a3e636">Sudah Dikembalikan</x-badge>
                @else
                    <x-badge icon="clock" color="#fef3c7">{{ ucfirst($item->status) }}</x-badge>
                @endif
            </div>

            <h1 style="font-size: 26px; font-weight: 800; margin: 0 0 16px; color: #111;">{{ $item->title }}</h1>

            <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; color: #4b5563; font-size: 14px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <x-icon name="map-pin" :size="18" />
                    <span><strong>Lokasi:</strong> {{ $item->location }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <x-icon name="calendar-days" :size="18" />
                    <span><strong>Tanggal Kejadian:</strong> {{ $item->date_event ? $item->date_event->translatedFormat('d F Y') : '-' }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <x-icon name="user" :size="18" />
                    <span><strong>Pelapor:</strong> {{ $item->user->name ?? 'Anonim Kampus' }}</span>
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 8px; color: #111;">Deskripsi:</h3>
                <p style="margin: 0; line-height: 1.6; color: #374151; white-space: pre-line; background: #fafafa; padding: 14px; border-radius: 10px; border: 1px solid #e5e7eb;">
                    {{ $item->description ?: 'Tidak ada deskripsi rinci.' }}
                </p>
            </div>

            {{-- Tombol Tindakan / Ajukan Klaim --}}
            <div style="margin-top: auto; padding-top: 16px; border-top: 1px dashed #e5e7eb;">
                @if ($canClaim)
                    @auth
                        @if (auth()->id() === $item->user_id)
                            <div style="padding: 12px; background: #f3f4f6; border-radius: 8px; font-size: 14px; color: #4b5563;">
                                Ini adalah barang temuan yang kamu laporkan sendiri.
                            </div>
                        @else
                            <a href="{{ route('claims.create', $item) }}" class="btn btn--primary" style="display: flex; justify-content: center; align-items: center; gap: 8px; font-size: 16px; padding: 14px; text-decoration: none;">
                                <x-icon name="handshake" :size="20" /> Ajukan Klaim Kepemilikan
                            </a>
                        @endif
                    @else
                        <div class="card panel panel--mint" style="padding: 16px; text-align: center;">
                            <p style="margin: 0 0 12px; font-size: 14px; font-weight: 600;">
                                Merasa ini barang milikmu? Masuk ke akunmu untuk mengajukan verifikasi klaim.
                            </p>
                            <a href="{{ route('login') }}" class="btn btn--primary">Masuk & Ajukan Klaim</a>
                        </div>
                    @endauth
                @elseif($isFound)
                    <div style="padding: 14px; background: #f3f4f6; border-radius: 10px; text-align: center; color: #6b7280; font-size: 14px; font-weight: 600;">
                        Barang ini sedang dalam proses klaim atau sudah dikembalikan ke pemiliknya.
                    </div>
                @else
                    <div style="padding: 14px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; color: #991b1b; font-size: 14px;">
                        💡 <strong>Catatan:</strong> Jika kamu menemukan barang ini di kampus, silakan serahkan ke pos satpam atau buat laporan penemuan barang.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

