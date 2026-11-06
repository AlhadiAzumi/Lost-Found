@extends('layouts.app')

@section('title', 'Serah Terima Barang | Lost & Found')

@section('content')
<div class="admin-page admin-page--narrow">
    <header class="admin-page__header">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="admin-page__back">&larr; Dashboard</a>
            <h1 class="admin-page__title">Serah Terima Barang</h1>
            <p class="admin-page__description">Masukkan kode dari pemilik, lalu cocokkan identitas sebelum menyerahkan barang.</p>
        </div>
    </header>

    @include('admin.navigation')

    @if (session('success'))
        <div class="card panel panel--mint admin-page__notice">
            <strong>✓ Berhasil!</strong> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="card panel admin-page__notice admin-page__notice--error">
            <strong>✕ Peringatan:</strong> {{ session('error') }}
        </div>
    @endif

    {{-- Form Pencarian Kode --}}
    <section class="admin-surface admin-handover-search">
        <form method="GET" action="{{ route('admin.handovers') }}" class="admin-handover-search__form">
            <input type="text" name="code" value="{{ $searchCode }}" placeholder="Contoh: LFP-ABCD-1234" required
                   class="input admin-handover-search__input">
            <button type="submit" class="btn btn--primary admin-handover-search__button">Periksa Kode</button>
        </form>
    </section>

    {{-- Hasil Validasi Kode --}}
    @if ($searchCode)
        @if ($claimToHandover)
            <section class="admin-surface admin-handover-result">
                <div class="admin-handover-result__header">
                    <div style="padding: 8px; background: #a3e636; border: 1.5px solid #111; border-radius: 10px;">
                        <x-icon name="check-check" :size="24" />
                    </div>
                    <div>
                        <h2 style="font-size: 20px; font-weight: 800; margin: 0; color: #111;">KODE VALID & TERVERIFIKASI</h2>
                        <span style="font-size: 13px; color: #166534;">Kode ini berhak mengambil barang berikut:</span>
                    </div>
                </div>

                <div class="admin-handover-details">
                    <div class="admin-handover-details__item">
                        <span>Nama barang</span>
                        <strong>{{ $claimToHandover->item->title ?? '-' }}</strong>
                    </div>
                    <div class="admin-handover-details__item">
                        <span>Pemilik (pengklaim)</span>
                        <strong>{{ $claimToHandover->user->name ?? '-' }}</strong>
                        <small>NIM/NIP: {{ $claimToHandover->user->nim_nip ?? 'Belum diisi' }}</small>
                    </div>
                </div>

                @if ($claimToHandover->status === 'approved' && !$claimToHandover->handover)
                    <form method="POST" action="{{ route('admin.handovers.process', $claimToHandover) }}">
                        @csrf
                        <input type="hidden" name="code" value="{{ $claimToHandover->handover_code }}">
                        <div class="admin-handover-notes">
                            <label for="notes">Catatan penyerahan (opsional)</label>
                            <input type="text" id="notes" name="notes" placeholder="Contoh: KTM fisik sudah dicocokkan." class="input">
                        </div>

                        <button type="submit" class="btn btn--primary admin-handover-confirm">
                            Konfirmasi & Serahkan Barang
                        </button>
                    </form>
                @else
                    <div class="admin-handover-expired">
                        Barang ini sudah pernah diserahkan sebelumnya dan kodenya telah hangus!
                    </div>
                @endif
            </section>
        @else
            <div class="admin-handover-not-found">
                <strong>Kode tidak ditemukan</strong>
                <p>Pastikan kode serah terima yang diketik sudah benar.</p>
            </div>
        @endif
    @endif

    {{-- Riwayat Serah Terima --}}
    <section class="admin-surface admin-handover-history">
        <h2>Riwayat serah terima</h2>
        <div class="admin-table-wrap">
        <table class="admin-table admin-table--handovers">
            <thead>
                <tr style="border-bottom: 2px solid #111;">
                    <th style="padding: 10px 6px;">Barang</th>
                    <th style="padding: 10px 6px;">Penerima</th>
                    <th style="padding: 10px 6px;">Petugas</th>
                    <th style="padding: 10px 6px;">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentHandovers as $handover)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 12px 6px; font-weight: 700;">{{ $handover->item->title ?? '-' }}</td>
                        <td style="padding: 12px 6px;">{{ $handover->recipient->name ?? '-' }}</td>
                        <td style="padding: 12px 6px;">{{ $handover->officer->name ?? '-' }}</td>
                        <td style="padding: 12px 6px; color: #6b7280;">{{ $handover->handover_date ? $handover->handover_date->translatedFormat('d M Y, H:i') : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 20px; text-align: center; color: #6b7280;">Belum ada riwayat penyerahan barang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </section>
</div>
@endsection
