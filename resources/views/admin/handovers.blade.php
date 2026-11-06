@extends('layouts.app')

@section('title', 'Serah Terima Barang — Campus Lost & Found')

@section('content')
<div class="container" style="max-width: 900px; margin: 36px auto; padding: 0 16px;">
    <div style="margin-bottom: 24px;">
        <a href="{{ route('admin.dashboard') }}" style="font-size: 13px; font-weight: 700; color: #111;">&larr; Kembali ke Dashboard</a>
        <h1 style="font-size: 26px; font-weight: 800; margin: 6px 0 0; color: #111;">Validasi & Serah Terima Barang</h1>
        <p style="margin: 0; color: #6b7280; font-size: 14px;">Masukkan kode serah terima yang ditunjukkan oleh pemilik barang di pos satpam.</p>
    </div>

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

    {{-- Form Pencarian Kode --}}
    <div class="card" style="padding: 28px; border: 2px solid #111; border-radius: 18px; box-shadow: 4px 4px 0 #111; background: #fff; margin-bottom: 28px;">
        <form method="GET" action="{{ route('admin.handovers') }}" style="display: flex; gap: 12px; align-items: center;">
            <input type="text" name="code" value="{{ $searchCode }}" placeholder="Contoh: LFP-ABCD-1234" required
                   class="input" style="flex: 1; font-family: monospace; font-size: 18px; text-transform: uppercase; font-weight: 700;">
            <button type="submit" class="btn btn--primary" style="padding: 12px 24px; font-size: 15px;">Periksa Kode</button>
        </form>
    </div>

    {{-- Hasil Validasi Kode --}}
    @if ($searchCode)
        @if ($claimToHandover)
            <div class="card" style="padding: 28px; border: 2px solid #111; border-radius: 18px; box-shadow: 6px 6px 0 #111; background: #fff; margin-bottom: 32px;">
                <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 20px;">
                    <div style="padding: 8px; background: #a3e636; border: 1.5px solid #111; border-radius: 10px;">
                        <x-icon name="check-check" :size="24" />
                    </div>
                    <div>
                        <h2 style="font-size: 20px; font-weight: 800; margin: 0; color: #111;">KODE VALID & TERVERIFIKASI</h2>
                        <span style="font-size: 13px; color: #166534;">Kode ini berhak mengambil barang berikut:</span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; background: #f9fafb; padding: 18px; border-radius: 14px; margin-bottom: 24px; border: 1px solid #e5e7eb;">
                    <div>
                        <span style="font-size: 12px; font-weight: 700; color: #6b7280; display: block;">NAMA BARANG:</span>
                        <strong style="font-size: 16px; color: #111;">{{ $claimToHandover->item->title ?? '-' }}</strong>
                    </div>
                    <div>
                        <span style="font-size: 12px; font-weight: 700; color: #6b7280; display: block;">PEMILIK SAH (PENGKLAIM):</span>
                        <strong style="font-size: 16px; color: #111;">{{ $claimToHandover->user->name ?? '-' }}</strong>
                        <div style="font-size: 12px; color: #4b5563;">NIM/NIP: {{ $claimToHandover->user->nim_nip ?? 'Belum diisi' }}</div>
                    </div>
                </div>

                @if ($claimToHandover->status === 'approved' && !$claimToHandover->handover)
                    <form method="POST" action="{{ route('admin.handovers.process', $claimToHandover) }}">
                        @csrf
                        <div style="margin-bottom: 16px;">
                            <label for="notes" style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 6px;">Catatan Penyerahan (Opsional):</label>
                            <input type="text" id="notes" name="notes" placeholder="Mis. Diserahkan setelah mencocokkan KTM fisik." class="input" style="width: 100%; box-sizing: border-box;">
                        </div>

                        <button type="submit" class="btn btn--primary" style="width: 100%; padding: 14px; font-size: 16px;">
                            Konfirmasi & Serahkan Barang
                        </button>
                    </form>
                @else
                    <div style="padding: 14px; background: #fee2e2; border: 1.5px solid #dc2626; border-radius: 10px; font-size: 14px; color: #991b1b; text-align: center; font-weight: 700;">
                        Barang ini sudah pernah diserahkan sebelumnya dan kodenya telah hangus!
                    </div>
                @endif
            </div>
        @else
            <div class="card" style="padding: 24px; background: #fee2e2; border: 2px solid #dc2626; border-radius: 14px; margin-bottom: 28px; text-align: center;">
                <strong style="color: #991b1b; font-size: 16px; display: block; margin-bottom: 4px;">Kode Tidak Ditemukan!</strong>
                <p style="margin: 0; color: #7f1d1d; font-size: 14px;">Pastikan kode serah terima yang diketikkan sudah benar.</p>
            </div>
        @endif
    @endif

    {{-- Riwayat Serah Terima --}}
    <div class="card" style="padding: 24px; border: 2px solid #111; border-radius: 18px; box-shadow: 4px 4px 0 #111; background: #fff;">
        <h3 style="margin: 0 0 16px; font-size: 18px; font-weight: 800;">Riwayat Serah Terima Terakhir</h3>
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
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
</div>
@endsection

