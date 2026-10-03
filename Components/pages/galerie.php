<?php

declare(strict_types=1);

$pageTitle = 'Galerie - Marcus Reiser';
$pageDescription = 'Fotogalerie von Marcus Reiser: Natur, Architektur, Tiere und Portraits.';
$bodyClass = 'subpage';
$currentPage = 'galerie';
$requestedFolder = isset($_GET['ordner']) && is_string($_GET['ordner']) ? $_GET['ordner'] : '';
$requestedSubfolder = isset($_GET['unterordner']) && is_string($_GET['unterordner']) ? trim($_GET['unterordner'], '/') : '';
$includeWebCategory = $requestedFolder === '20.02_Web' && $requestedSubfolder === '13_Zwiebelmarkt';
$photoCategories = get_photo_categories($includeWebCategory);
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
    $segments = explode('/', $requestedSubfolder);
    $validPath = !in_array('', $segments, true)
      && !in_array('.', $segments, true)
      && !in_array('..', $segments, true)
      && count(array_filter($segments, 'photo_library_is_private')) === 0;

    if ($validPath) {
      $libraryRoot = photo_library_root();
      $categoryRoot = $libraryRoot !== null
        ? realpath($libraryRoot . DIRECTORY_SEPARATOR . $selectedCategory['name'])
        : false;
      $subfolderPath = $categoryRoot !== false
        ? realpath($categoryRoot . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $segments))
        : false;

      if ($categoryRoot !== false
        && $subfolderPath !== false
        && is_dir($subfolderPath)
        && str_starts_with($subfolderPath, $categoryRoot . DIRECTORY_SEPARATOR)) {
        $selectedSubfolder = $requestedSubfolder;
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
  <?php require BASE_PATH . '/Components/layout/nav.php'; ?>
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
            <button class="photo-open" type="button" data-full-image="<?= e(photo_library_image_variant_url($photo['url'], 'gallery')) ?>" data-image-alt="<?= e($photo['alt']) ?>" aria-label="Bild vergrößern: <?= e($photo['alt']) ?>">
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
    <section class="folder-grid" aria-label="Fotoordner">
      <?php foreach ($photoCategories as $category): ?>
        <a class="folder-card" href="/galerie?<?= e(http_build_query(['ordner' => $category['name']])) ?>">
          <img class="folder-card-image" src="<?= e(photo_library_image_variant_url($category['photos'][0]['url'], 'preview')) ?>" alt="" loading="lazy">
          <h3><?= e($category['label']) ?></h3>
          <p class="folder-count"><?= count($category['photos']) ?> Bilder</p>
        </a>
      <?php endforeach; ?>
    </section>
  <?php endif; ?>
</main>

<dialog class="photo-lightbox" aria-label="Bildansicht">
  <button class="photo-lightbox-close" type="button" aria-label="Bildansicht schließen">&times;</button>
  <img class="photo-lightbox-image" alt="">
  <div id="exif-datetime" style="margin-top:10px; font-weight:bold;"></div>
  <dl class="photo-exif" id="exif-camera-settings" hidden>
    <div data-exif-key="camera" hidden><dt>Kamera</dt><dd></dd></div>
    <div data-exif-key="lens" hidden><dt>Objektiv</dt><dd></dd></div>
    <div data-exif-key="focal" hidden><dt>Brennweite</dt><dd></dd></div>
    <div data-exif-key="aperture" hidden><dt>Blende</dt><dd></dd></div>
    <div data-exif-key="exposure" hidden><dt>Belichtungszeit</dt><dd></dd></div>
    <div data-exif-key="iso" hidden><dt>ISO</dt><dd></dd></div>
  </dl>
  <div id="exif-location" style="margin-top:10px; font-weight:bold;"></div>
  <div id="weather" style="margin-top:15px; font-size:14px; display:none;">
    <div><strong>Wetter um:</strong> <span id="weather-time"></span></div>
    <div><strong>Temperatur:</strong> <span id="weather-temp"></span></div>
    <div><strong>Bewölkung:</strong> <span id="weather-clouds"></span></div>
    <div><strong>Wind:</strong> <span id="weather-wind"></span></div>
    <div><strong>Wetter:</strong> <span id="weather-desc"></span></div>
  </div>
  <div id="map" style="width:100%; height:300px; display:none; margin-top:20px;"></div>
</dialog>
<script src="/public/assets/js/gallery-lightbox.js" defer></script>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>