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

// Abbildung von URL-Pfaden auf Seiten-Templates.
$routes = [
    '/' => 'home.php',
    '/galerie' => 'galerie.php',
    '/kalender' => 'kalender.php',
    '/it-projekte' => 'it-projekte.php',
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

// Das gefundene Seiten-Template rendert die komplette Seite inklusive Layout.
require __DIR__ . '/Components/pages/' . $routes[$path];
