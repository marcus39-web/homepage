<?php

declare(strict_types=1);

$pageTitle = 'Marcus Reiser - Fotografie und IT';
$pageDescription = 'Marcus Reiser aus Weimar/Legefeld - Fotografie, Kalender 2026 und IT-Projekte auf einer modernen Startseite.';
$currentPage = 'home';

$visitStats = get_visit_stats();
$visitsTotal = (int) ($visitStats['total'] ?? 0);

require BASE_PATH . '/Components/layout/header.php';
?>
<header class="hero" id="top">
  <?php
  $navContext = 'hero';
  require BASE_PATH . '/Components/layout/nav.php';
  ?>

  <div class="hero-visit-stats wrap" aria-label="Besucherzahlen">
    <div class="hero-visit-box">
      <p>Besucher Gesamt: <strong><?= e((string) $visitsTotal) ?></strong></p>
    </div>
  </div>

  <div class="hero-media" role="img" aria-label="Blumen als Hintergrundbild">
    <img src="/public/assets/images/Blumen.jpg" alt="Blumenbild als Hintergrund">
  </div>

  <div class="hero-content wrap">
    <p class="hero-kicker">Thüringen in Bildern · Projekte · Kalender</p>
    <h1>Marcus Reiser - Fotografie und IT</h1>
    <p class="hero-subline">Klar. Modern. Persönlich. Von der Kamera bis zur technischen Umsetzung.</p>
    <div class="hero-actions">
      <a class="btn btn-primary" href="/galerie">Fotografie ansehen</a>
      <a class="btn btn-secondary" href="/kalender">Kalender 2026 bestellen</a>
      <a class="btn btn-ghost" href="/it-projekte">IT-Projekte</a>
    </div>
  </div>
</header>

<main>
  <section class="intro wrap" aria-labelledby="intro-title">
    <h2 id="intro-title">Willkommen auf meiner Homepage.</h2>
    <p>
      Ich bin Marcus Reiser aus Weimar/Legefeld - Fotograf aus Leidenschaft und IT-Spezialist
      mit langjähriger Erfahrung in Erwachsenenbildung, Technik und Projekten.
      Auf dieser Seite findest du meine besten Fotos, meinen jährlichen Fotokalender sowie ausgewählte IT-Projekte.
    </p>
  </section>

  <section class="gallery-preview wrap" id="fotografie" aria-labelledby="galerie-title">
    <div class="section-head">
      <h2 id="galerie-title">Fotografie-Vorschau</h2>
      <p>Vier Themen, ein Stil: präzise Bildkompositionen aus Thüringen und darüber hinaus.</p>
    </div>

    <div class="gallery-grid">
      <article class="gallery-card">
        <img src="/public/assets/images/preview-natur.svg" alt="Natur und Landschaft">
        <h3>Natur und Landschaft</h3>
      </article>
      <article class="gallery-card">
        <img src="/public/assets/images/preview-architektur.svg" alt="Architektur">
        <h3>Architektur</h3>
      </article>
      <article class="gallery-card">
        <img src="/public/assets/images/preview-tiere.svg" alt="Tiere">
        <h3>Tiere</h3>
      </article>
      <article class="gallery-card">
        <img src="/public/assets/images/preview-portraits.svg" alt="Portraits">
        <h3>Portraits</h3>
      </article>
    </div>

    <a class="btn btn-primary" href="/galerie">Zur Galerie</a>
  </section>

  <section class="calendar-teaser wrap" id="kalender" aria-labelledby="kalender-title">
    <div class="calendar-text">
      <h2 id="kalender-title">Fotokalender 2026 - Thüringen in Bildern</h2>
      <p>
        12 ausgewählte Motive aus deiner Canon R10.
        Gedruckt auf hochwertigem Papier, direkt bestellbar.
      </p>
      <a class="btn btn-secondary" href="/kalender">Kalender ansehen</a>
    </div>
    <div class="calendar-image">
      <img src="/public/assets/images/calendar-2026.svg" alt="Teaserbild Fotokalender 2026">
    </div>
  </section>

  <section class="it-projects wrap" id="it-projekte" aria-labelledby="it-title">
    <div class="section-head">
      <h2 id="it-title">IT-Projekte</h2>
      <p>Praxisnahe Lösungen zwischen Infrastruktur, Workflows und Automationen.</p>
    </div>

    <ul class="project-list">
      <li>Cloud und Hosting (resier.de / marcusreiser.de)</li>
      <li>Windows und iPadOS Workflows</li>
      <li>Fotografie-Automationen</li>
      <li>Schulungsunterlagen und technische Dokumentation</li>
    </ul>

    <a class="btn btn-ghost" href="/it-projekte">IT-Projekte ansehen</a>
  </section>
</main>

<?php
$footerId = 'kontakt';
require BASE_PATH . '/Components/layout/footer.php';
