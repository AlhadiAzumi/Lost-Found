@extends('layouts.app')

@section('title', 'Verifikasi Klaim — Campus Lost & Found')

@section('content')
<div class="container" style="max-width: 1040px; margin: 36px auto; padding: 0 16px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <a href="{{ route('admin.dashboard') }}" style="font-size: 13px; font-weight: 700; color: #111;">&larr; Kembali ke Dashboard</a>
            <h1 style="font-size: 24px; font-weight: 800; margin: 4px 0 0; color: #111;">Verifikasi Klaim Kepemilikan</h1>
        </div>
    </div>

    @if (session('success'))
        <div class="card panel panel--mint" style="margin-bottom: 24px; padding: 16px;">
            <strong>✓ Berhasil!</strong> {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; flex-direction: column; gap: 20px;">
        @forelse ($claims as $claim)
            <div class="card" style="padding: 24px; border: 2px solid #111; border-radius: 18px; box-shadow: 4px 4px 0 #111; background: #fff;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 16px; border-bottom: 1px solid #e5e7eb; padding-bottom: 16px;">
                    <div>
                        <span style="font-size: 12px; font-weight: 700; color: #6b7280;">KLAIM #{{ $claim->id }}</span>
                        <h2 style="font-size: 20px; font-weight: 800; margin: 2px 0; color: #111;">
                            {{ $claim->item->title ?? 'Barang' }}
                        </h2>
                        <span style="font-size: 13px; color: #4b5563;">
                            Diajukan oleh: <strong>{{ $claim->user->name ?? 'User' }}</strong> ({{ $claim->user->email ?? '-' }})
                        </span>
                    </div>

                    <div>
                        @if ($claim->status === 'approved')
                            <x-badge icon="circle-check" color="#a3e636">Disetujui: {{ $claim->handover_code }}</x-badge>
                        @elseif ($claim->status === 'completed')
                            <x-badge icon="check-check" color="#a3e636">Selesai Diserahkan</x-badge>
                        @elseif ($claim->status === 'rejected')
                            <x-badge icon="circle-xmark" color="#fee2e2">Ditolak</x-badge>
                        @else
                            <x-badge icon="clock" color="#fef3c7">Menunggu Verifikasi</x-badge>
                        @endif
                    </div>
                </div>

                {{-- Panel Verifikasi 3 Komponen --}}
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 20px;">
                    <div style="padding: 14px; background: #f9fafb; border-radius: 10px; border: 1px solid #e5e7eb;">
                        <span style="font-size: 12px; font-weight: 700; color: #6b7280; display: block; margin-bottom: 4px;">PERTANYAAN KEAMANAN DARI PENEMU:</span>
                        <p style="margin: 0; font-size: 14px; font-weight: 600;">"{{ $claim->item->security_question ?? '-' }}"</p>
                    </div>

                    <div style="padding: 14px; background: #f0fdf4; border-radius: 10px; border: 1.5px solid #16a34a;">
                        <span style="font-size: 12px; font-weight: 700; color: #166534; display: block; margin-bottom: 4px;">JAWABAN DARI PENGKLAIM:</span>
                        <p style="margin: 0; font-size: 15px; font-weight: 700; color: #111;">"{{ $claim->security_answer }}"</p>
                    </div>

                    <div style="padding: 14px; background: #f9fafb; border-radius: 10px; border: 1px solid #e5e7eb;">
                        <span style="font-size: 12px; font-weight: 700; color: #6b7280; display: block; margin-bottom: 4px;">BUKTI PENDUKUNG:</span>
                        @if ($claim->proof_image)
                            <a href="{{ Storage::url($claim->proof_image) }}" target="_blank" style="font-size: 13px; font-weight: 700; color: #326bec;">
                                Buka Berkas / Foto Bukti
                            </a>
                        @else
                            <span style="font-size: 13px; color: #9ca3af;">Tidak melampirkan berkas bukti</span>
                        @endif
                    </div>
                </div>

                @if ($claim->status === 'pending')
                    <form method="POST" action="{{ route('admin.claims.verify', $claim) }}" style="display: flex; gap: 12px; align-items: center; justify-content: flex-end; border-top: 1px dashed #e5e7eb; padding-top: 16px;">
                        @csrf
                        <input type="text" name="admin_note" placeholder="Catatan admin (wajib bila menolak)..." class="input" style="flex: 1; max-width: 400px; padding: 8px 12px; font-size: 13px;">
                        <button type="submit" name="action" value="reject" class="btn btn--ghost" style="color: #dc2626; border-color: #dc2626;">Tolak Klaim</button>
                        <button type="submit" name="action" value="approve" class="btn btn--primary">✓ Setujui & Terbitkan Kode</button>
                    </form>
                @endif
            </div>
        @empty
            <div class="card" style="padding: 48px; text-align: center; border: 2px dashed #d1d5db; border-radius: 16px; background: #fff;">
                <p style="color: #6b7280; margin: 0;">Belum ada pengajuan klaim.</p>
            </div>
        @endforelse

        <div>
            {{ $claims->links() }}
        </div>
    </div>
</div>
@endsection

