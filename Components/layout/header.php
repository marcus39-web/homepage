<?php

declare(strict_types=1);

// Fallback-Metadaten greifen, wenn die Seite keine eigenen Werte setzt.
$pageTitle = isset($pageTitle) && is_string($pageTitle) && $pageTitle !== ''
    ? $pageTitle
    : 'Marcus Reiser - Fotografie und IT';

$pageDescription = isset($pageDescription) && is_string($pageDescription) && $pageDescription !== ''
    ? $pageDescription
    : 'Persönliche Website von Marcus Reiser über Fotografie, Kalender und IT-Projekte.';

// Optionales Body-Attribut für seitenbezogene CSS-Varianten.
$bodyClass = isset($bodyClass) && is_string($bodyClass) ? trim($bodyClass) : '';
$bodyClassAttribute = $bodyClass !== ''
    ? ' class="' . htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8') . '"'
    : '';
?>
<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Titel und Beschreibung werden immer escaped ausgegeben. -->
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/public/css/style.css">
</head>
<body<?= $bodyClassAttribute ?>>
