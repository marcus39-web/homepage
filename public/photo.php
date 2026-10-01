<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

$category = isset($_GET['category']) && is_string($_GET['category']) ? $_GET['category'] : '';
$relativePath = isset($_GET['file']) && is_string($_GET['file']) ? $_GET['file'] : '';
$filePath = resolve_photo_library_file($category, $relativePath);

if ($filePath === null) {
	http_response_code(404);
	exit;
}

$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
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
header('Cache-Control: public, max-age=3600');
header('Content-Length: ' . (string) filesize($filePath));
readfile($filePath);