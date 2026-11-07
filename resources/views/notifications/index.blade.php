@extends('layouts.app')

@section('title', 'Notifikasi | Lost & Found')

@section('content')
<div class="page-wrap page-wrap--medium">
    <div class="page-header">
        <div>
            <h1 class="page-header__title">Notifikasi</h1>
            <p class="page-header__sub">Informasi terbaru tentang laporan dan klaimmu.</p>
        </div>
        @if ($notifications->contains(fn ($notification) => $notification->read_at === null))
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="btn btn--outline btn--sm">Tandai Semua Dibaca</button>
            </form>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert--success">
            <x-icon name="circle-check" :size="18" />
            <span><strong>Berhasil!</strong> {{ session('success') }}</span>
        </div>
    @endif

    @if ($notifications->isEmpty())
        <div class="empty-state">
            <x-icon name="bell" :size="44" />
            <h3>Belum Ada Notifikasi</h3>
            <p>Notifikasi akan muncul di sini saat laporan atau klaimmu diproses oleh petugas.</p>
        </div>
    @else
        <div class="notification-list">
            @foreach ($notifications as $notification)
                @php
                    $isUnread = $notification->read_at === null;
                    $icon = match ($notification->type) {
                        'report_approved', 'claim_approved', 'handover_completed' => 'circle-check',
                        'report_rejected', 'claim_rejected' => 'circle-x',
                        default => 'bell',
                    };
                    $tone = match ($notification->type) {
                        'report_approved', 'claim_approved', 'handover_completed' => 'success',
                        'report_rejected', 'claim_rejected' => 'danger',
                        default => 'info',
                    };
                @endphp

                <article class="notification-item {{ $isUnread ? 'is-unread' : '' }}">
                    <div class="notification-item__icon notification-item__icon--{{ $tone }}">
                        <x-icon :name="$icon" :size="20" />
                    </div>

                    <div class="notification-item__body">
                        <div class="notification-item__head">
                            <h3>{{ $notification->title }}</h3>
                            @if ($isUnread)
                                <span class="notification-item__dot" aria-label="Belum dibaca"></span>
                            @endif
                        </div>
                        <p>{{ $notification->message }}</p>
                        <span class="notification-item__time">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>

                    <form method="POST" action="{{ route('notifications.read', ['notification' => $notification]) }}" class="notification-item__action">
                        @csrf
                        <button type="submit" class="btn btn--ghost btn--sm">
                            {{ filled($notification->url) ? 'Lihat' : 'Tandai Dibaca' }}
                        </button>
                    </form>
                </article>
            @endforeach
        </div>

        <div class="admin-page__pagination">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
