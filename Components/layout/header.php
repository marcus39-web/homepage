<?php

declare(strict_types=1);

// Fallback-Metadaten greifen, wenn die Seite keine eigenen Werte setzt.
$pageTitle = isset($pageTitle) && is_string($pageTitle) && $pageTitle !== ''
    ? $pageTitle
    : 'Marcus Reiser – Fotografie & IT';

$pageDescription = isset($pageDescription) && is_string($pageDescription) && $pageDescription !== ''
    ? $pageDescription
    : 'Persönliche Website von Marcus Reiser über Fotografie, Kalender und IT in Weimar.';

// Optionales Body-Attribut für seitenbezogene CSS-Varianten.
$bodyClass = isset($bodyClass) && is_string($bodyClass) ? trim($bodyClass) : '';
$bodyClassAttribute = $bodyClass !== ''
    ? ' class="' . htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8') . '"'
    : '';

// Aktuelle URL für Canonical / OG
$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
$currentUrl  = 'https://marcusreiser.de' . $currentPath;

?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">

    <!-- Basis-SEO -->
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Canonical -->
    <link rel="canonical" href="<?= htmlspecialchars($currentUrl, ENT_QUOTES, 'UTF-8') ?>">

    <!-- Robots -->
    <meta name="robots" content="index,follow">

    <!-- OpenGraph -->
    <meta property="og:locale" content="de_DE">
    <meta property="og:site_name" content="Marcus Reiser – Fotografie & IT">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($currentUrl, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image" content="https://marcusreiser.de/public/assets/images/galerie/natur/Ilm/Weimarpark_Allee.jpg">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image" content="https://marcusreiser.de/public/assets/images/galerie/natur/Ilm/Weimarpark_Allee.jpg">

    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="/public/assets/css/style.css">
</head>
<body<?= $bodyClassAttribute ?>>
