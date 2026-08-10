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

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = is_string($requestPath) ? rtrim($requestPath, '/') : '/';
if ($path === '') {
    $path = '/';
}

if ($path === '/contact' && $method === 'POST') {
    handle_contact_form_submission();
}

$routes = [
    '/' => 'home.php',
    '/galerie' => 'galerie.php',
    '/kalender' => 'kalender.php',
    '/it-projekte' => 'it-projekte.php',
    '/impressum' => 'impressum.php',
    '/datenschutz' => 'datenschutz.php',
    '/contact' => 'contact.php',
];

if (!isset($routes[$path])) {
    http_response_code(404);
    echo '<!doctype html><html lang="de"><head><meta charset="utf-8"><title>404</title></head><body><h1>Seite nicht gefunden</h1></body></html>';
    exit;
}

require __DIR__ . '/Components/pages/' . $routes[$path];
