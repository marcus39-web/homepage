<?php

declare(strict_types=1);

$pageTitle = 'Galerie - Marcus Reiser';
$pageDescription = 'Fotogalerie von Marcus Reiser: Natur, Architektur, Tiere und Portraits.';
$bodyClass = 'subpage';
$currentPage = 'galerie';
$photoCategories = get_photo_categories();
$requestedFolder = isset($_GET['ordner']) && is_string($_GET['ordner']) ? $_GET['ordner'] : '';
$selectedCategory = null;

foreach ($photoCategories as $category) {
  if ($category['name'] === $requestedFolder) {
    $selectedCategory = $category;
    break;
  }
}

if ($selectedCategory !== null) {
  $photoCategories = [$selectedCategory];
  $pageTitle = 'Fotografien: ' . $selectedCategory['label'] . ' - Marcus Reiser';
}

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
    <h1><?= $selectedCategory !== null ? e($selectedCategory['label']) : 'Galerie' ?></h1>
    <p><?= $selectedCategory !== null ? 'Fotografien aus diesem Ordner.' : 'Aufnahmen aus meinen Bilderordnern, nach Themen sortiert.' ?></p>
    <a class="btn btn-primary" href="/#fotografie">Alle Fotoordner</a>
  </section>

  <?php if ($photoCategories === []): ?>
    <p class="panel">Im Fotoarchiv sind noch keine öffentlichen Bilder verfügbar.</p>
  <?php else: ?>
    <?php foreach ($photoCategories as $category): ?>
      <section class="gallery-category" id="ordner-<?= e($category['name']) ?>" aria-labelledby="category-<?= e($category['name']) ?>">
        <div class="section-head">
          <h2 id="category-<?= e($category['name']) ?>"><?= e($category['label']) ?></h2>
          <p><?= count($category['photos']) ?> Bilder</p>
        </div>
        <div class="photo-grid">
          <?php foreach ($category['photos'] as $photo): ?>
            <figure class="photo-item">
              <img src="<?= e($photo['url']) ?>" alt="<?= e($photo['alt']) ?>" loading="lazy">
              <figcaption><?= e($photo['alt']) ?></figcaption>
            </figure>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
  <?php endif; ?>
</main>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
