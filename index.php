<?php

declare(strict_types=1);

// Beim eingebauten PHP-Server statische Dateien direkt ausliefern.
if (PHP_SAPI === 'cli-server') {
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $filePath = is_string($requestPath) ? __DIR__ . $requestPath : '';

    if ($filePath !== '' && is_file($filePath)) {
        return false;
    }
}

require __DIR__ . '/bootstrap.php';

// Request-Metadaten normalisieren, damit Routing eindeutig funktioniert.
$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = is_string($requestPath) ? rtrim($requestPath, '/') : '/';
if ($path === '') {
    $path = '/';
}

// POST auf /contact wird sofort serverseitig verarbeitet und beendet den Request mit Redirect.
if ($path === '/contact' && $method === 'POST') {
    handle_contact_form_submission();
}

if ($path === '/kalender-bestellung' && $method === 'POST') {
    handle_calendar_order_submission();
}

// Login/Logout-Endpoints fuer den internen Statistikbereich.
if ($path === '/statistik-login' && $method === 'POST') {
    handle_statistics_login_submission();
}

if ($path === '/statistik-logout' && $method === 'GET') {
    handle_statistics_logout();
}

// Abbildung von URL-Pfaden auf Seiten-Templates.
$routes = [
    '/' => 'home.php',
    '/galerie' => 'galerie.php',
    '/kalender' => 'kalender.php',
    '/it-projekte' => 'it-projekte.php',
    '/statistik-login' => 'statistik-login.php',
    '/statistik' => 'statistik.php',
    '/impressum' => 'impressum.php',
    '/datenschutz' => 'datenschutz.php',
    '/contact' => 'contact.php',
];

// Unbekannte Pfade liefern eine minimale 404-Seite.
if (!isset($routes[$path])) {
    http_response_code(404);
    echo '<!doctype html><html lang="de"><head><meta charset="utf-8"><title>404</title></head><body><h1>Seite nicht gefunden</h1></body></html>';
    exit;
}

// Interne Statistik nur nach erfolgreichem Login freigeben.
if ($path === '/statistik' && !is_stats_authenticated()) {
    header('Location: /statistik-login', true, 302);
    exit;
}

if ($path === '/statistik-login' && is_stats_authenticated()) {
    header('Location: /statistik', true, 302);
    exit;
}

// Nur erfolgreiche Seitenaufrufe werden als Besuch gezählt.
if ($method === 'GET') {
    track_page_visit($path);
}

// Das gefundene Seiten-Template rendert die komplette Seite inklusive Layout.
require __DIR__ . '/Components/pages/' . $routes[$path];
