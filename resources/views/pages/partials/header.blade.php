<header class="info-page__header">
    <x-campus-chip :campus="$currentCampus ?? null" variant="page" />
    <h1 class="info-page__title">{{ $title }}</h1>
    <p class="info-page__intro">{{ $description }}</p>
</header>
