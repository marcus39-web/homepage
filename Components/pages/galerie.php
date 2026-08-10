<?php

declare(strict_types=1);

$pageTitle = 'Galerie - Marcus Reiser';
$pageDescription = 'Fotogalerie von Marcus Reiser: Natur, Architektur, Tiere und Portraits.';
$bodyClass = 'subpage';
$currentPage = 'galerie';

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
    <p class="eyebrow-lite">Fotografie</p>
    <h1>Galerie</h1>
    <p>Eine Auswahl aus Natur, Architektur, Tierfotografie und Portraits. Die Bilder kannst du später durch deine Originalaufnahmen ersetzen.</p>
    <a class="btn btn-primary" href="/">Zur Startseite</a>
  </section>

  <section class="gallery-grid gallery-full" aria-label="Galerie Kategorien">
    <article class="gallery-card">
      <img src="/public/assets/images/preview-natur.svg" alt="Natur und Landschaft">
      <h3>Natur und Landschaft</h3>
    </article>
    <article class="gallery-card">
      <img src="/public/assets/images/preview-architektur.svg" alt="Architektur">
      <h3>Architektur</h3>
    </article>
    <article class="gallery-card">
      <img src="/public/assets/images/preview-tiere.svg" alt="Tierfotografie">
      <h3>Tiere</h3>
    </article>
    <article class="gallery-card">
      <img src="/public/assets/images/preview-portraits.svg" alt="Portraitfotografie">
      <h3>Portraits</h3>
    </article>
  </section>
</main>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
