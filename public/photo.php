<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

$category = isset($_GET['category']) && is_string($_GET['category']) ? $_GET['category'] : '';
$relativePath = isset($_GET['file']) && is_string($_GET['file']) ? $_GET['file'] : '';
$variant = isset($_GET['variant']) && is_string($_GET['variant']) ? $_GET['variant'] : '';
$filePath = resolve_photo_library_file($category, $relativePath);

if ($filePath === null) {
	http_response_code(404);
	exit;
}

$servedPath = $filePath;
$cacheDuration = 3600;
if (in_array($variant, ['preview', 'gallery'], true)) {
	// Nur nach erfolgreicher Original-Pfadpruefung nach einer Web-Variante suchen; fehlt sie, gilt das Original als Fallback.
	$cacheRoot = realpath(DATA_PATH . '/photo-cache');
	$segments = preg_split('~[\\\\/]~', $relativePath) ?: [];
	$cachedPath = $cacheRoot !== false
		? realpath($cacheRoot . DIRECTORY_SEPARATOR . $variant . DIRECTORY_SEPARATOR . $category . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $segments) . '.webp')
		: false;
	if ($cacheRoot !== false && $cachedPath !== false && is_file($cachedPath) && str_starts_with($cachedPath, $cacheRoot . DIRECTORY_SEPARATOR)) {
		$servedPath = $cachedPath;
		$cacheDuration = 86400;
	}
}

$extension = strtolower(pathinfo($servedPath, PATHINFO_EXTENSION));
$mimeType = match ($extension) {
	'jpg', 'jpeg' => 'image/jpeg',
	'png' => 'image/png',
	'webp' => 'image/webp',
	default => null,
};
if ($mimeType === null) {
	http_response_code(404);
	exit;
}

header('Content-Type: ' . $mimeType);
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=' . $cacheDuration);
header('Content-Length: ' . (string) filesize($servedPath));
readfile($servedPath);