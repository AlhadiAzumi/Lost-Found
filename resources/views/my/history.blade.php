@extends('layouts.app')

@section('title', 'Riwayat Aktivitas | Lost & Found')

@section('content')
<div class="page-wrap">
    <div class="page-header">
        <div>
            <h1 class="page-header__title">Riwayat Aktivitas</h1>
            <p class="page-header__sub">Pantau status laporan dan klaim barangmu di sini.</p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('lost.create') }}" class="btn btn--outline">+ Lapor Hilang</a>
            <a href="{{ route('found.create') }}" class="btn btn--primary">+ Lapor Temuan</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert--success">
            <x-icon name="circle-check" :size="18" />
            <span><strong>Berhasil!</strong> {{ session('success') }}</span>
        </div>
    @endif

    {{-- Tabs --}}
    <nav class="tabs" style="margin-bottom:24px;" aria-label="Tab riwayat">
        <a href="{{ route('my.history', ['tab' => 'reports']) }}"
           class="tab {{ $tab === 'reports' ? 'is-active' : '' }}">
            Laporan Saya ({{ $reports->count() }})
        </a>
        <a href="{{ route('my.history', ['tab' => 'claims']) }}"
           class="tab {{ $tab === 'claims' ? 'is-active' : '' }}">
            Klaim Saya ({{ $claims->count() }})
        </a>
    </nav>

    {{-- TAB: Laporan Saya --}}
    @if ($tab === 'reports')
        @if ($reports->isEmpty())
            <div class="empty-state">
                <x-icon name="clipboard-list" :size="44" />
                <h3>Belum Ada Laporan</h3>
                <p>Kamu belum pernah membuat laporan barang hilang maupun temuan.</p>
                <a href="{{ route('found.create') }}" class="btn btn--primary">Buat Laporan Baru</a>
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:14px;">
                @foreach ($reports as $item)
                    @php $isFound = in_array($item->type, ['found', 'ditemukan']); @endphp
                    <div class="card card--hover history-card">
                        <div class="history-thumb">
                            <img src="{{ $item->photo_url }}" alt="{{ $item->title }}">
                        </div>

                        <div class="history-info">
                            <div style="display:flex; gap:8px; margin-bottom:6px; align-items:center; flex-wrap:wrap;">
                                <x-badge :icon="$isFound ? 'search-check' : 'search'" :color="$isFound ? '#a3e636' : '#fee2e2'">
                                    {{ $isFound ? 'Temuan' : 'Hilang' }}
                                </x-badge>
                            </div>
                            <h3>
                                <a href="{{ route('items.show', ['item' => $item]) }}">{{ $item->title }}</a>
                            </h3>
                            <div class="history-info__meta">
                                <x-icon name="map-pin" :size="13" />
                                {{ $item->location }}
                                <span style="color:var(--muted-light);">&middot;</span>
                                <x-icon name="calendar" :size="13" />
                                {{ $item->date_event ? $item->date_event->translatedFormat('d M Y') : '-' }}
                            </div>
                        </div>

                        <div>
                            @if(in_array($item->status, ['approved','disetujui']))
                                <x-badge icon="circle-check" color="#d1fae8">Aktif di Katalog</x-badge>
                            @elseif(in_array($item->status, ['returned','selesai']))
                                <x-badge icon="check-check" color="#a3e636">Sudah Dikembalikan</x-badge>
                            @else
                                <x-badge icon="clock" color="#fef3c7">{{ ucfirst($item->status) }}</x-badge>
                            @endif
                            @if ($item->status === 'rejected' && $item->rejection_reason)
                                <p style="margin:8px 0 0; color:#991b1b; font-size:13px;">
                                    <strong>Alasan penolakan:</strong> {{ $item->rejection_reason }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    {{-- TAB: Klaim Saya --}}
    @if ($tab === 'claims')
        @if ($claims->isEmpty())
            <div class="empty-state">
                <x-icon name="handshake" :size="44" />
                <h3>Belum Ada Pengajuan Klaim</h3>
                <p>Jika barangmu ditemukan oleh seseorang di kampus, kamu bisa mengajukan klaim di halaman detail barang.</p>
                <a href="{{ route('catalog.index') }}" class="btn btn--primary">Cari Barang di Katalog</a>
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:16px;">
                @foreach ($claims as $claim)
                    <div class="card" style="padding:24px;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin-bottom:16px; flex-wrap:wrap;">
                            <div>
                                <h3 style="font-size:17px; font-weight:800; margin-bottom:4px;">
                                    <a href="{{ route('items.show', ['item' => $claim->item]) }}" style="color:var(--ink);">
                                        {{ $claim->item->title ?? 'Barang' }}
                                    </a>
                                </h3>
                                <span style="font-size:12.5px; color:var(--muted);">
                                    Diajukan: {{ $claim->created_at->translatedFormat('d M Y, H:i') }}
                                </span>
                            </div>
                            <div>
                                @if ($claim->status === 'approved')
                                    <x-badge icon="circle-check" color="#a3e636">Klaim Disetujui!</x-badge>
                                @elseif ($claim->status === 'completed')
                                    <x-badge icon="check-check" color="#a3e636">Barang Sudah Diambil</x-badge>
                                @elseif ($claim->status === 'rejected')
                                    <x-badge icon="circle-x" color="#fee2e2">Klaim Ditolak</x-badge>
                                @else
                                    <x-badge icon="clock" color="#fef3c7">Menunggu Verifikasi</x-badge>
                                @endif
                            </div>
                        </div>

                        @if ($claim->status === 'approved')
                            <div class="handover-code-box">
                                <span class="handover-code-box__label">Kode Serah Terima Anda</span>
                                <div class="handover-code">{{ $claim->handover_code }}</div>
                                <p style="font-size:13px; color:#3f6212; line-height:1.5;">
                                    Tunjukkan kode ini beserta <strong>KTM</strong> ke petugas Pos Satpam untuk mengambil fisik barang.
                                </p>
                            </div>
                        @elseif ($claim->status === 'rejected')
                            <div class="alert alert--error" style="margin-bottom:0;">
                                <x-icon name="circle-x" :size="18" />
                                <span><strong>Alasan Penolakan:</strong> {{ $claim->admin_note ?? 'Bukti atau jawaban pertanyaan keamanan tidak sesuai.' }}</span>
                            </div>
                        @else
                            <div style="font-size:13px; color:#4b5563; background:#f9fafb; padding:12px 14px; border-radius:var(--radius-md); border:1px solid #e5e7eb;">
                                <strong>Status:</strong> Klaim telah diserahkan kepada petugas untuk diverifikasi.
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
@endsection
