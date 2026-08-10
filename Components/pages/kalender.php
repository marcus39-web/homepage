<?php

declare(strict_types=1);

$pageTitle = 'Kalender 2026 - Marcus Reiser';
$pageDescription = 'Fotokalender 2026 von Marcus Reiser mit Motiven aus Thüringen.';
$bodyClass = 'subpage';
$currentPage = 'kalender';

require BASE_PATH . '/Components/layout/header.php';
?>
<header class="subpage-top">
  <?php
  $navContext = 'subpage';
  require BASE_PATH . '/Components/layout/nav.php';
  ?>
</header>

<main class="subpage-main wrap">
  <section class="subpage-head panel">
    <p class="eyebrow-lite">Fotokalender</p>
    <h1>Kalender 2026 - Thüringen in Bildern</h1>
    <p>12 ausgewählte Motive aus der Canon R10. Hochwertiger Druck, ideal als Geschenk oder für Zuhause.</p>
    <div class="hero-actions">
      <a class="btn btn-secondary" href="mailto:info@marcusreiser.de?subject=Bestellung%20Kalender%202026">Kalender bestellen</a>
      <a class="btn btn-primary" href="/">Zur Startseite</a>
    </div>
  </section>

  <section class="calendar-teaser panel calendar-panel" aria-label="Kalender Details">
    <div class="calendar-text">
      <h2>Was dich erwartet</h2>
      <ul class="project-list">
        <li>12 Monatsblätter mit ausgewählten Motiven</li>
        <li>Regionale Schwerpunkte aus Thüringen</li>
        <li>Hochwertiges Papier mit starker Farbwiedergabe</li>
        <li>Direkte Bestellung per Kontaktanfrage</li>
      </ul>
    </div>
    <div class="calendar-image">
      <img src="/public/assets/images/calendar-2026.svg" alt="Kalender 2026 Vorschau">
    </div>
  </section>
</main>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
