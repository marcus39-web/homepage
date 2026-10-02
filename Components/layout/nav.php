<?php

declare(strict_types=1);

// currentPage steuert, welcher Navigationslink auf der aktuellen Seite ausgeblendet wird.
$currentPage = isset($currentPage) && is_string($currentPage) ? $currentPage : '';
// navContext unterscheidet Hero-Navigation (Startseite) und Subpages.
$navContext = isset($navContext) && is_string($navContext) ? $navContext : 'subpage';
$isHomeHero = $navContext === 'hero';
?>
<nav class="site-nav wrap" aria-label="Hauptnavigation">
  <?php if (!$isHomeHero): ?>
    <div class="brand-block">
      <a class="brand" href="/">Marcus Reiser</a>
    </div>
  <?php endif; ?>
  <div class="site-nav-links">
    <?php if (!$isHomeHero): ?>
      <a href="/">Start</a>
    <?php endif; ?>

    <?php if ($currentPage !== 'galerie'): ?>
      <a href="/galerie">Fotografie</a>
    <?php endif; ?>

    <a href="/#tassen">Tassen</a>

    <?php if ($currentPage !== 'kalender'): ?>
      <a href="/kalender">Kalender</a>
    <?php endif; ?>

      <?php if (is_stats_authenticated()): ?>
      <a href="/statistik">Statistik</a>
      <a href="/statistik-logout">Logout</a>
    <?php endif; ?>
    <a href="/contact">Kontakt</a>
  </div>
</nav>
