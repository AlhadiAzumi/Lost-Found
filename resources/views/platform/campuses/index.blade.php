@extends('layouts.app')

@section('title', 'Kelola Kampus | Lost & Found')

@section('content')
<main class="campus-management">
    <header class="campus-management__header">
        <div>
            <a href="{{ route('campuses.index', ['campus' => null]) }}" class="campus-management__back">&larr; Pilih Kampus</a>
            <h1>Kelola Kampus</h1>
            <p>Buat portal baru dan atur portal yang tersedia untuk pengguna.</p>
        </div>
    </header>

    @if (session('success'))
        <div class="campus-management__notice campus-management__notice--success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="campus-management__notice campus-management__notice--error">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="campus-management__notice campus-management__notice--error">{{ $errors->first() }}</div>
    @endif

    <section class="campus-management__panel">
        <h2>Pengajuan portal <span>{{ $pendingApplications->count() }}</span></h2>
        @if ($pendingApplications->isEmpty())
            <p class="campus-management__help">Belum ada pengajuan yang menunggu persetujuan.</p>
        @else
            <div class="campus-management__list">
                @foreach ($pendingApplications as $application)
                    <article class="campus-management__campus">
                        <div class="campus-management__campus-info">
                            <strong>{{ $application->name }}</strong>
                            <span>{{ $application->email }}</span>
                            <small>/kampus/{{ $application->slug }} · {{ $application->created_at->format('d M Y') }}</small>
                        </div>
                        <div class="campus-management__application-actions">
                            <form method="POST" action="{{ route('platform.campuses.applications.approve', ['application' => $application, 'campus' => null]) }}">
                                @csrf
                                <button type="submit" class="btn btn--primary campus-management__toggle">Setujui</button>
                            </form>
                            <form method="POST" action="{{ route('platform.campuses.applications.reject', ['application' => $application, 'campus' => null]) }}">
                                @csrf
                                <button type="submit" class="btn btn--ghost campus-management__toggle">Tolak</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    @if ($awaitingAdminSetup->isNotEmpty())
        <section class="campus-management__panel">
            <h2>Menunggu pembuatan akun admin <span>{{ $awaitingAdminSetup->count() }}</span></h2>
            <div class="campus-management__list">
                @foreach ($awaitingAdminSetup as $application)
                    <article class="campus-management__campus">
                        <div class="campus-management__campus-info">
                            <strong>{{ $application->name }}</strong>
                            <span>{{ $application->email }}</span>
                            <small>Tautan berlaku hingga {{ $application->invitation_expires_at?->format('d M Y') ?? '—' }}</small>
                        </div>
                        <form method="POST" action="{{ route('platform.campuses.applications.resend', ['application' => $application, 'campus' => null]) }}">
                            @csrf
                            <button type="submit" class="btn btn--ghost campus-management__toggle">Kirim Ulang Tautan</button>
                        </form>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="campus-management__panel">
        <h2>Tambah kampus</h2>
        <p class="campus-management__help">Slug akan menjadi bagian alamat portal. Contoh: <code>/kampus/kampus-utara</code>.</p>

        <form method="POST" action="{{ route('platform.campuses.store', ['campus' => null]) }}" class="campus-management__form">
            @csrf
            <div class="campus-management__field">
                <label for="campus-name">Nama kampus</label>
                <input id="campus-name" name="name" type="text" value="{{ old('name') }}" maxlength="255" required
                       placeholder="Contoh: Kampus Utara" class="input">
                @error('name') <small class="campus-management__error">{{ $message }}</small> @enderror
            </div>
            <div class="campus-management__field">
                <label for="campus-slug">Alamat portal (slug)</label>
                <div class="campus-management__slug">
                    <span>/kampus/</span>
                    <input id="campus-slug" name="slug" type="text" value="{{ old('slug') }}" maxlength="80"
                           pattern="[a-z0-9]+(-[a-z0-9]+)*" required placeholder="kampus-utara" class="input">
                </div>
                @error('slug') <small class="campus-management__error">{{ $message }}</small> @enderror
            </div>
            <button type="submit" class="btn btn--primary">Buat Portal Kampus</button>
        </form>
    </section>

    <section class="campus-management__panel">
        <h2>Portal terdaftar <span>{{ $campuses->count() }}</span></h2>
        <div class="campus-management__list">
            @foreach ($campuses as $campus)
                <article class="campus-management__campus">
                    <div class="campus-management__campus-info">
                        <strong>{{ $campus->name }}</strong>
                        <a href="{{ route('home', ['campus' => $campus->slug]) }}" target="_blank" rel="noopener noreferrer">
                            /kampus/{{ $campus->slug }}
                        </a>
                        <span class="campus-management__status {{ $campus->is_active ? 'is-active' : 'is-inactive' }}">
                            {{ $campus->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    @if ($campus->slug !== config('campus.default_slug'))
                        <form method="POST" action="{{ route('platform.campuses.status', ['managedCampus' => $campus]) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_active" value="{{ $campus->is_active ? 0 : 1 }}">
                            <button type="submit" class="btn btn--ghost campus-management__toggle">
                                {{ $campus->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    @else
                        <span class="campus-management__default">Portal utama</span>
                    @endif
                </article>
            @endforeach
        </div>
    </section>
</main>
@endsection
