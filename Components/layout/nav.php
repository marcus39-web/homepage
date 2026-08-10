<?php

declare(strict_types=1);

// currentPage steuert, welcher Navigationslink auf der aktuellen Seite ausgeblendet wird.
$currentPage = isset($currentPage) && is_string($currentPage) ? $currentPage : '';
// navContext unterscheidet Hero-Navigation (Startseite) und Subpages.
$navContext = isset($navContext) && is_string($navContext) ? $navContext : 'subpage';
$isHomeHero = $navContext === 'hero';
?>
<nav class="site-nav wrap" aria-label="Hauptnavigation">
  <div class="brand-block">
    <!-- Im Hero springt die Marke zum Seitenanfang, sonst zur Startseite. -->
    <a class="brand" href="<?= $isHomeHero ? '#top' : '/' ?>">Marcus Reiser</a>
    <?php if ($isHomeHero): ?>
      <!-- Profilbild wird nur im Hero-Kontext angezeigt. -->
      <img class="brand-avatar" src="/public/assets/images/profilbild_neu_freigestellt.png" alt="Freigestelltes Profilbild von Marcus Reiser">
    <?php endif; ?>
  </div>
  <div class="site-nav-links">
    <?php if (!$isHomeHero): ?>
      <a href="/">Start</a>
    <?php endif; ?>

    <?php if ($currentPage !== 'galerie'): ?>
      <a href="/galerie">Fotografie</a>
    <?php endif; ?>

    <?php if ($currentPage !== 'kalender'): ?>
      <a href="/kalender">Kalender 2026</a>
    <?php endif; ?>

    <?php if ($currentPage !== 'it-projekte'): ?>
      <a href="/it-projekte">IT-Projekte</a>
    <?php endif; ?>

    <a href="/contact">Kontakt</a>
  </div>
</nav>
