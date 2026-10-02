<?php

declare(strict_types=1);

$pageTitle = 'Galerie - Marcus Reiser';
$pageDescription = 'Fotogalerie von Marcus Reiser: Natur, Architektur, Tiere und Portraits.';
$bodyClass = 'subpage';
$currentPage = 'galerie';
$photoCategories = get_photo_categories();
$requestedFolder = isset($_GET['ordner']) && is_string($_GET['ordner']) ? $_GET['ordner'] : '';
$requestedSubfolder = isset($_GET['unterordner']) && is_string($_GET['unterordner']) ? trim($_GET['unterordner'], '/') : '';
$selectedCategory = null;
$selectedSubfolder = '';
$subfolders = [];
$visiblePhotos = [];

foreach ($photoCategories as $category) {
  if ($category['name'] === $requestedFolder) {
    $selectedCategory = $category;
    break;
  }
}

if ($selectedCategory !== null) {
	// Aeltere Scanner-Versionen liefern path nicht mit; die Foto-URL enthaelt ihn ebenfalls.
  foreach ($selectedCategory['photos'] as &$photo) {
    if (isset($photo['path']) && is_string($photo['path']) && $photo['path'] !== '') {
      continue;
    }

    $query = parse_url((string) ($photo['url'] ?? ''), PHP_URL_QUERY);
    parse_str(is_string($query) ? $query : '', $photoQuery);
    $photo['path'] = isset($photoQuery['file']) && is_string($photoQuery['file'])
      ? str_replace('\\', '/', $photoQuery['file'])
      : '';
  }
  unset($photo);

  $photoCategories = [$selectedCategory];
  $pageTitle = 'Fotografien: ' . $selectedCategory['label'] . ' - Marcus Reiser';

  if ($requestedSubfolder !== '') {
	// Unterordner nur akzeptieren, wenn der gesuchte Zweig zu dieser Kategorie gehoert.
    $segments = explode('/', $requestedSubfolder);
    $validPath = !in_array('', $segments, true)
      && !in_array('.', $segments, true)
      && !in_array('..', $segments, true)
      && count(array_filter($segments, 'photo_library_is_private')) === 0;

    if ($validPath) {
      foreach ($selectedCategory['photos'] as $photo) {
        if (str_starts_with($photo['path'], $requestedSubfolder . '/')) {
          $selectedSubfolder = $requestedSubfolder;
          break;
        }
      }
    }
  }

  foreach ($selectedCategory['photos'] as $photo) {
    $relativePath = $photo['path'];
    if ($selectedSubfolder !== '') {
      $prefix = $selectedSubfolder . '/';
      if (!str_starts_with($relativePath, $prefix)) {
        continue;
      }
      $relativePath = substr($relativePath, strlen($prefix));
    }

    // Die naechste Pfadebene wird als Ordnerkarte gezeigt; Dateien auf dieser Ebene bleiben sichtbar.
    $pathSegments = explode('/', $relativePath, 2);
    if (count($pathSegments) === 1) {
      $visiblePhotos[] = $photo;
      continue;
    }

    $folderName = $pathSegments[0];
    $folderPath = $selectedSubfolder !== '' ? $selectedSubfolder . '/' . $folderName : $folderName;
    if (!isset($subfolders[$folderPath])) {
      $subfolders[$folderPath] = [
        'name' => $folderName,
        'path' => $folderPath,
        'count' => 0,
        'preview' => $photo,
      ];
    }
    $subfolders[$folderPath]['count']++;
  }

  uasort($subfolders, static fn (array $left, array $right): int => strnatcasecmp($left['name'], $right['name']));
  $pageTitle = $selectedSubfolder !== ''
    ? 'Fotografien: ' . basename($selectedSubfolder) . ' - ' . $selectedCategory['label']
    : $pageTitle;
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
    <h1><?= $selectedSubfolder !== '' ? e(basename($selectedSubfolder)) : ($selectedCategory !== null ? e($selectedCategory['label']) : 'Galerie') ?></h1>
    <p><?= $selectedCategory !== null ? 'Fotografien aus ' . e($selectedSubfolder !== '' ? basename($selectedSubfolder) : $selectedCategory['label']) . '.' : 'Aufnahmen aus meinen Bilderordnern, nach Themen sortiert.' ?></p>
    <a class="btn btn-primary" href="/#fotografie">Alle Fotoordner</a>
  </section>

  <?php if ($photoCategories === []): ?>
    <p class="panel">Im Fotoarchiv sind noch keine öffentlichen Bilder verfügbar.</p>
  <?php elseif ($selectedCategory !== null): ?>
    <?php
    $backSubfolder = $selectedSubfolder !== '' && str_contains($selectedSubfolder, '/')
      ? substr($selectedSubfolder, 0, strrpos($selectedSubfolder, '/'))
      : '';
    $backUrl = $backSubfolder !== ''
      ? '/galerie?' . http_build_query(['ordner' => $selectedCategory['name'], 'unterordner' => $backSubfolder])
      : '/galerie?' . http_build_query(['ordner' => $selectedCategory['name']]);
    ?>
    <?php if ($selectedSubfolder !== ''): ?>
      <p><a class="folder-back-link" href="<?= e($backUrl) ?>">Zurück zu <?= e($backSubfolder !== '' ? basename($backSubfolder) : $selectedCategory['label']) ?></a></p>
    <?php endif; ?>

    <?php if ($subfolders !== []): ?>
      <section class="folder-grid" aria-label="Unterordner in <?= e($selectedCategory['label']) ?>">
        <?php foreach ($subfolders as $subfolder): ?>
          <a class="folder-card" href="/galerie?<?= e(http_build_query(['ordner' => $selectedCategory['name'], 'unterordner' => $subfolder['path']])) ?>">
            <img class="folder-card-image" src="<?= e(photo_library_image_variant_url($subfolder['preview']['url'], 'preview')) ?>" alt="" loading="lazy">
            <h3><?= e($subfolder['name']) ?></h3>
            <p class="folder-count"><?= (int) $subfolder['count'] ?> <?= $subfolder['count'] === 1 ? 'Bild' : 'Bilder' ?></p>
          </a>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>

    <?php if ($visiblePhotos !== []): ?>
      <section class="photo-grid" aria-label="Fotografien aus <?= e($selectedSubfolder !== '' ? basename($selectedSubfolder) : $selectedCategory['label']) ?>">
        <?php foreach ($visiblePhotos as $photo): ?>
          <figure class="photo-item">
            <button class="photo-open" type="button" data-full-image="<?= e($photo['url']) ?>" data-image-alt="<?= e($photo['alt']) ?>" aria-label="Bild vergrößern: <?= e($photo['alt']) ?>">
              <img src="<?= e(photo_library_image_variant_url($photo['url'], 'gallery')) ?>" alt="" loading="lazy">
            </button>
            <figcaption><?= e($photo['alt']) ?></figcaption>
          </figure>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>

    <?php if ($subfolders === [] && $visiblePhotos === []): ?>
      <p class="panel">Dieser Fotoordner enthält keine anzeigbaren Bilder.</p>
    <?php endif; ?>
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
              <button class="photo-open" type="button" data-full-image="<?= e($photo['url']) ?>" data-image-alt="<?= e($photo['alt']) ?>" aria-label="Bild vergrößern: <?= e($photo['alt']) ?>">
                <img src="<?= e(photo_library_image_variant_url($photo['url'], 'gallery')) ?>" alt="" loading="lazy">
              </button>
              <figcaption><?= e($photo['alt']) ?></figcaption>
            </figure>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
  <?php endif; ?>
</main>

<dialog class="photo-lightbox" aria-label="Bildansicht">
  <button class="photo-lightbox-close" type="button" aria-label="Bildansicht schließen">&times;</button>
  <img class="photo-lightbox-image" alt="">
</dialog>
<script src="/public/js/gallery-lightbox.js" defer></script>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
