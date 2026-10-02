<?php

declare(strict_types=1);

$pageTitle = 'Marcus Reiser - Fotografie';
$pageDescription = 'Fotografie aus Weimar und Thüringen, Bildgalerie, Fototassen und Fotokalender von Marcus Reiser.';
$currentPage = 'home';
$photoCategories = get_photo_categories();
$featuredPhoto = null;
foreach ($photoCategories as $category) {
  if ($category['name'] !== '07_Blumen') {
    continue;
  }

  foreach ($category['photos'] as $photo) {
    if (strcasecmp($photo['alt'], 'Marcus Sonnenblumen 2') === 0) {
      $featuredPhoto = $photo;
      break 2;
    }
  }
}

// Oeffentliche Anzeige: nur Gesamtbesuche im Hero.
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

  <?php if ($featuredPhoto !== null): ?>
    <div class="hero-media">
      <img src="<?= e($featuredPhoto['url']) ?>" alt="">
      <a class="hero-logo" href="#top" aria-label="Marcus Reiser Fotografie, Seitenanfang">
        <img src="/public/assets/images/marcus-reiser-logo.png" alt="">
      </a>
    </div>
  <?php endif; ?>

  <div class="hero-content wrap">
    <p class="hero-kicker">Fotografie aus Thüringen · Motive für Zuhause</p>
    <p class="hero-subline">Ausgewählte Motive aus Weimar, Thüringen und darüber hinaus.</p>
    <div class="hero-actions">
      <a class="btn btn-primary" href="/galerie">Fotografie ansehen</a>
      <a class="btn btn-secondary" href="#angebote">Angebote entdecken</a>
    </div>
  </div>
</header>

<main>
  <section class="intro wrap" aria-labelledby="intro-title">
    <h1 id="intro-title">Fotografie aus Weimar und Thüringen</h1>
    <p>
      Ich bin Marcus Reiser aus Weimar/Legefeld und Fotograf aus Leidenschaft.
      Hier findest du ausgewählte Aufnahmen und nach und nach auch Produkte,
      auf denen meine Motive weiterleben.
    </p>
  </section>

  <section class="gallery-preview wrap" id="fotografie" aria-labelledby="galerie-title">
    <div class="section-head">
      <h2 id="galerie-title">Ausgewählte Fotografien</h2>
      <p>Wähle einen Fotoordner aus, um alle Fotografien dieses Themas anzusehen.</p>
    </div>

    <div class="gallery-grid">
      <?php foreach ($photoCategories as $category): ?>
        <?php $previewPhoto = $category['photos'][0]; ?>
        <a class="gallery-card" href="/galerie?ordner=<?= rawurlencode($category['name']) ?>">
          <img src="<?= e($previewPhoto['url']) ?>" alt="<?= e($previewPhoto['alt']) ?>" loading="lazy">
          <h3><?= e($category['label']) ?></h3>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="offer-band" id="angebote" aria-labelledby="angebote-title">
    <div class="wrap">
      <p class="offer-kicker">Meine Motive zum Mitnehmen</p>
      <h2 id="angebote-title">Fotografie für deinen Alltag</h2>
      <div class="offer-grid">
        <article class="offer-item" id="tassen">
          <p class="offer-index">01 / Fototassen</p>
          <h3>Tassen mit meinen Motiven</h3>
          <p>Ausgewählte Fotografien als Druckmotiv auf einer Tasse. Die Motiv-Auswahl und Bestellmöglichkeiten ergänze ich Schritt für Schritt.</p>
          <a class="offer-link" href="/contact">Interesse an einer Fototasse?</a>
        </article>
        <article class="offer-item" id="fotokalender">
          <p class="offer-index">02 / Kalender</p>
          <h3>Mein Fotokalender</h3>
          <p>Ein Jahr voller ausgewählter Aufnahmen aus Thüringen, zusammengestellt in meinem eigenen Kalender.</p>
          <a class="offer-link" href="/kalender">Zum Fotokalender</a>
        </article>
      </div>
    </div>
  </section>

</main>

<?php
$footerId = 'kontakt';
require BASE_PATH . '/Components/layout/footer.php';
