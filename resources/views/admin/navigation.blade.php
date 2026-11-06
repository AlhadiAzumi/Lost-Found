<nav class="admin-nav" aria-label="Navigasi panel petugas">
    <a href="{{ route('admin.dashboard') }}" class="admin-nav__link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">Dashboard</a>
    <a href="{{ route('admin.reports') }}" class="admin-nav__link {{ request()->routeIs('admin.reports*') ? 'is-active' : '' }}">Moderasi Laporan</a>
    <a href="{{ route('admin.claims') }}" class="admin-nav__link {{ request()->routeIs('admin.claims*') ? 'is-active' : '' }}">Verifikasi Klaim</a>
    <a href="{{ route('admin.handovers') }}" class="admin-nav__link {{ request()->routeIs('admin.handovers*') ? 'is-active' : '' }}">Serah Terima</a>
</nav>
