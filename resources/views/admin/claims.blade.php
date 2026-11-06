@extends('layouts.app')

@section('title', 'Verifikasi Klaim | Lost & Found')

@section('content')
<div class="admin-page">
    <header class="admin-page__header">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="admin-page__back">&larr; Dashboard</a>
            <h1 class="admin-page__title">Verifikasi Klaim</h1>
            <p class="admin-page__description">Periksa jawaban keamanan dan bukti kepemilikan.</p>
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
            <strong>✕ Perhatian:</strong> {{ session('error') }}
        </div>
    @endif

    <div class="admin-claim-list">
        @forelse ($claims as $claim)
            <article class="admin-surface admin-claim-card">
                <header class="admin-claim-card__header">
                    <div>
                        <span class="admin-claim-card__number">Klaim #{{ $claim->id }}</span>
                        <h2 class="admin-claim-card__title">
                            {{ $claim->item->title ?? 'Barang' }}
                        </h2>
                        <span class="admin-claim-card__claimant">
                            Diajukan oleh: <strong>{{ $claim->user->name ?? 'User' }}</strong> ({{ $claim->user->email ?? '-' }})
                        </span>
                    </div>

                    <div class="admin-claim-card__status">
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
                </header>

                {{-- Panel Verifikasi 3 Komponen --}}
                <div class="admin-claim-details">
                    <div class="admin-claim-detail">
                        <span class="admin-claim-detail__label">Pertanyaan keamanan</span>
                        <p>"{{ $claim->item->security_question ?? '-' }}"</p>
                    </div>

                    <div class="admin-claim-detail admin-claim-detail--answer">
                        <span class="admin-claim-detail__label">Jawaban pengklaim</span>
                        <p>"{{ $claim->security_answer }}"</p>
                        @if ($claim->item->matchesSecurityAnswer($claim->security_answer) === true)
                            <strong style="color: #166534;">Jawaban cocok</strong>
                        @elseif ($claim->item->matchesSecurityAnswer($claim->security_answer) === false)
                            <strong style="color: #b91c1c;">Jawaban tidak cocok</strong>
                        @else
                            <strong style="color: #92400e;">Jawaban belum tersimpan untuk laporan ini; tinjau bukti secara manual.</strong>
                        @endif
                    </div>

                    <div class="admin-claim-detail">
                        <span class="admin-claim-detail__label">Bukti pendukung</span>
                        @if ($claim->proof_image)
                            <a href="{{ Storage::url($claim->proof_image) }}" target="_blank" rel="noopener noreferrer" class="admin-claim-proof">
                                Buka Berkas / Foto Bukti
                            </a>
                        @else
                            <span class="admin-claim-proof admin-claim-proof--empty">Tidak melampirkan berkas bukti</span>
                        @endif
                    </div>
                </div>

                @if ($claim->status === 'pending')
                    <form method="POST" action="{{ route('admin.claims.verify', $claim) }}" class="admin-claim-actions">
                        @csrf
                        <input type="text" name="admin_note" placeholder="Catatan admin (wajib bila menolak)..." class="input admin-claim-actions__note">
                        <button type="submit" name="action" value="reject" class="btn btn--ghost" style="color: #dc2626; border-color: #dc2626;">Tolak Klaim</button>
                        <button type="submit" name="action" value="approve" class="btn btn--primary">✓ Setujui & Terbitkan Kode</button>
                    </form>
                @endif
            </article>
        @empty
            <div class="admin-surface admin-page__empty">
                <p>Belum ada pengajuan klaim.</p>
            </div>
        @endforelse

        <div>
            {{ $claims->links() }}
        </div>
    </div>
</div>
@endsection
