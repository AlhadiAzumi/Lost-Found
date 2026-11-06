@extends('layouts.app')

@section('title', 'Riwayat Saya — Campus Lost & Found')

@section('content')
<div class="container" style="max-width: 960px; margin: 36px auto; padding: 0 16px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 800; margin: 0 0 6px; color: #111;">Riwayat Aktivitas</h1>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Pantau status laporan dan klaim barangmu di sini.</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('lost.create') }}" class="btn btn--ghost" style="font-size: 14px;">+ Lapor Hilang</a>
            <a href="{{ route('found.create') }}" class="btn btn--primary" style="font-size: 14px;">+ Lapor Temuan</a>
        </div>
    </div>

    @if (session('success'))
        <div class="card panel panel--mint" style="margin-bottom: 24px; padding: 16px;">
            <strong>✓ Berhasil!</strong> {{ session('success') }}
        </div>
    @endif

    {{-- Tabs --}}
    <nav class="tabs" style="margin-bottom: 24px;">
        <a href="{{ route('my.history', ['tab' => 'reports']) }}" class="tab {{ $tab === 'reports' ? 'is-active' : '' }}">
            Laporan Saya ({{ $reports->count() }})
        </a>
        <a href="{{ route('my.history', ['tab' => 'claims']) }}" class="tab {{ $tab === 'claims' ? 'is-active' : '' }}">
            Klaim Saya ({{ $claims->count() }})
        </a>
    </nav>

    {{-- TAB 1: Laporan Saya --}}
    @if ($tab === 'reports')
        @if ($reports->isEmpty())
            <div class="card" style="padding: 48px 24px; text-align: center; border: 2px dashed #d1d5db; border-radius: 16px; background: #fff;">
                <x-icon name="clipboard-list" :size="48" />
                <h3 style="font-size: 18px; margin: 12px 0 6px;">Belum Ada Laporan</h3>
                <p style="color: #6b7280; margin: 0 0 16px; font-size: 14px;">Kamu belum pernah membuat laporan barang hilang maupun temuan.</p>
                <a href="{{ route('found.create') }}" class="btn btn--primary">Buat Laporan Baru</a>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 16px;">
                @foreach ($reports as $item)
                    @php $isFound = in_array($item->type, ['found', 'ditemukan']); @endphp
                    <div class="card" style="padding: 20px; border: 2px solid #111; border-radius: 16px; box-shadow: 4px 4px 0 #111; background: #fff; display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
                        <div style="width: 80px; height: 80px; border-radius: 10px; overflow: hidden; border: 1.5px solid #111; flex-shrink: 0;">
                            <img src="{{ $item->photo_url }}" alt="{{ $item->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>

                        <div style="flex: 1; min-width: 240px;">
                            <div style="display: flex; gap: 8px; margin-bottom: 6px; align-items: center;">
                                <x-badge :icon="$isFound ? 'search-check' : 'search'" :color="$isFound ? '#a3e636' : '#fee2e2'">
                                    {{ $isFound ? 'Temuan' : 'Hilang' }}
                                </x-badge>
                                <span style="font-size: 13px; color: #6b7280;">• {{ $item->date_event ? $item->date_event->translatedFormat('d M Y') : '-' }}</span>
                            </div>
                            <h3 style="margin: 0 0 6px; font-size: 17px; font-weight: 800;">
                                <a href="{{ route('items.show', $item) }}" style="color: #111; text-decoration: none;">{{ $item->title }}</a>
                            </h3>
                            <span style="font-size: 13px; color: #4b5563;">📍 {{ $item->location }}</span>
                        </div>

                        <div>
                            @if(in_array($item->status, ['approved', 'disetujui']))
                                <x-badge icon="circle-check" color="#d1fae8">Aktif di Katalog</x-badge>
                            @elseif(in_array($item->status, ['returned', 'selesai']))
                                <x-badge icon="check-check" color="#a3e636">Sudah Dikembalikan</x-badge>
                            @else
                                <x-badge icon="clock" color="#fef3c7">{{ ucfirst($item->status) }}</x-badge>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    {{-- TAB 2: Klaim Saya --}}
    @if ($tab === 'claims')
        @if ($claims->isEmpty())
            <div class="card" style="padding: 48px 24px; text-align: center; border: 2px dashed #d1d5db; border-radius: 16px; background: #fff;">
                <x-icon name="handshake" :size="48" />
                <h3 style="font-size: 18px; margin: 12px 0 6px;">Belum Ada Pengajuan Klaim</h3>
                <p style="color: #6b7280; margin: 0 0 16px; font-size: 14px;">Jika barangmu ditemukan oleh seseorang di kampus, kamu bisa mengajukan klaim di halaman detail barang.</p>
                <a href="{{ route('catalog.index') }}" class="btn btn--primary">Cari Barang di Katalog</a>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 16px;">
                @foreach ($claims as $claim)
                    <div class="card" style="padding: 24px; border: 2px solid #111; border-radius: 16px; box-shadow: 4px 4px 0 #111; background: #fff;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 16px; flex-wrap: wrap;">
                            <div>
                                <h3 style="margin: 0 0 4px; font-size: 18px; font-weight: 800;">
                                    <a href="{{ route('items.show', $claim->item) }}" style="color: #111; text-decoration: none;">
                                        {{ $claim->item->title ?? 'Barang' }}
                                    </a>
                                </h3>
                                <span style="font-size: 13px; color: #6b7280;">Diajukan pada: {{ $claim->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </div>

                            <div>
                                @if ($claim->status === 'approved')
                                    <x-badge icon="circle-check" color="#a3e636">Klaim Disetujui!</x-badge>
                                @elseif ($claim->status === 'completed')
                                    <x-badge icon="check-check" color="#a3e636">Barang Sudah Diambil</x-badge>
                                @elseif ($claim->status === 'rejected')
                                    <x-badge icon="circle-xmark" color="#fee2e2">Klaim Ditolak</x-badge>
                                @else
                                    <x-badge icon="clock" color="#fef3c7">Menunggu Verifikasi</x-badge>
                                @endif
                            </div>
                        </div>

                        {{-- Jika Disetujui: Tampilkan KODE SERAH TERIMA --}}
                        @if ($claim->status === 'approved')
                            <div style="padding: 20px; background: #f7fee7; border: 2px solid #a3e636; border-radius: 14px; margin-top: 12px; text-align: center;">
                                <span style="font-size: 13px; font-weight: 700; color: #365314; display: block; margin-bottom: 6px;">KODE SERAH TERIMA ANDA:</span>
                                <div style="font-size: 28px; font-weight: 900; letter-spacing: 3px; font-family: monospace; color: #111; background: #fff; padding: 10px 20px; border: 2px solid #111; border-radius: 10px; display: inline-block; margin-bottom: 12px;">
                                    {{ $claim->handover_code }}
                                </div>
                                <p style="margin: 0; font-size: 13px; color: #3f6212;">
                                    📌 Tunjukkan kode unik ini beserta <strong>Kartu Tanda Mahasiswa (KTM)</strong> ke petugas Pos Satpam untuk mengambil fisik barang.
                                </p>
                            </div>
                        @elseif ($claim->status === 'rejected')
                            <div style="padding: 14px; background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 10px; font-size: 14px; color: #991b1b;">
                                <strong>Alasan Penolakan:</strong> {{ $claim->admin_note ?? 'Bukti atau jawaban pertanyaan keamanan tidak sesuai.' }}
                            </div>
                        @else
                            <div style="font-size: 13px; color: #4b5563; background: #f9fafb; padding: 12px; border-radius: 8px;">
                                <strong>Jawaban Kamu:</strong> "{{ $claim->security_answer }}"
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
@endsection

