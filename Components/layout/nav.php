<?php declare(strict_types=1);

$currentPage = isset($currentPage) ? $currentPage : '';
$navContext  = isset($navContext) ? $navContext : 'subpage';
$isHomeHero  = $navContext === 'hero';
?>

<nav class="site-nav wrap" aria-label="Hauptnavigation">

    <?php if (!$isHomeHero): ?>
        <div class="brand-block">
            <a class="brand" href="/">Marcus Reiser</a>
        </div>
    <?php endif; ?>

    <!-- Mobile Toggle -->
    <button class="nav-toggle" aria-expanded="false" aria-label="Menü öffnen">
        <span class="nav-toggle-icon"></span>
    </button>

    <div class="site-nav-links">
        <?php if (!$isHomeHero): ?>
            <a href="/">Start</a>
        <?php endif; ?>

        <a href="/galerie">Fotografie</a>
        <a href="/kalender">Kalender</a>

        <?php if (is_stats_authenticated()): ?>
            <a href="/statistik">Statistik</a>
            <a href="/statistik-logout">Logout</a>
        <?php endif; ?>

        <a href="/contact">Kontakt</a>
        <a href="/impressum">Impressum</a>
        <a href="/datenschutz">Datenschutz</a>
    </div>
</nav>
