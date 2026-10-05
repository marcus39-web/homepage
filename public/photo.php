<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

$category = $_GET['category'] ?? '';
$relativePath = $_GET['file'] ?? '';
$variant = $_GET['variant'] ?? '';

$filePath = resolve_photo_library_file($category, $relativePath);

if ($filePath === null) {
    http_response_code(404);
    exit;
}

/**
 * Speicherorte für optimierte Varianten
 */
$cacheRoot = DATA_PATH . '/photo-cache';

$previewRoot = $cacheRoot . '/preview/' . $category;
$galleryRoot = $cacheRoot . '/gallery/' . $category;
$thumbRoot   = $cacheRoot . '/thumb/' . $category;

$previewFile = $previewRoot . '/' . $relativePath . '.webp';
$galleryFile = $galleryRoot . '/' . $relativePath . '.webp';
$thumbFile   = $thumbRoot   . '/' . $relativePath . '.webp';

/**
 * Hilfsfunktion: Ordner anlegen
 */
function ensure_dir(string $path): void {
    if (!is_dir($path)) {
        mkdir($path, 0775, true);
    }
}

function photo_watermark_generation_available(): bool {
    foreach ([
        'imagecreatefromstring',
        'imagecreatetruecolor',
        'imagecopyresampled',
        'imagewebp',
        'imagecreatefromwebp',
        'imagecreatefrompng',
    ] as $function) {
        if (!function_exists($function)) {
            return false;
        }
    }

    return true;
}

function photo_variant_metadata_path(string $variantPath): string {
    return $variantPath . '.wmmeta';
}

function photo_variant_signature(string $sourcePath): ?string {
    $watermarkPath = __DIR__ . '/assets/watermark/watermark.png';
    if (!is_file($sourcePath) || !is_file($watermarkPath)) {
        return null;
    }

    clearstatcache(true, $sourcePath);
    clearstatcache(true, $watermarkPath);
    $sourceMtime = filemtime($sourcePath);
    $sourceSize = filesize($sourcePath);
    $watermarkMtime = filemtime($watermarkPath);
    $watermarkSize = filesize($watermarkPath);
    if ($sourceMtime === false || $sourceSize === false || $watermarkMtime === false || $watermarkSize === false) {
        return null;
    }

    return hash('sha256', implode('|', ['wm-v1', $sourceMtime, $sourceSize, $watermarkMtime, $watermarkSize]));
}

function photo_variant_write_metadata(string $sourcePath, string $variantPath): bool {
    $signature = photo_variant_signature($sourcePath);
    if ($signature === null) {
        return false;
    }

    return @file_put_contents(photo_variant_metadata_path($variantPath), $signature, LOCK_EX) !== false;
}

function photo_variant_is_stale(string $sourcePath, string $variantPath): bool {
    if (!is_file($variantPath)) {
        return true;
    }

    $signature = photo_variant_signature($sourcePath);
    $metadataPath = photo_variant_metadata_path($variantPath);
    clearstatcache(true, $metadataPath);
    if (is_file($metadataPath)) {
        if ($signature === null) {
            return true;
        }

        $storedSignature = @file_get_contents($metadataPath);
        return !is_string($storedSignature) || !hash_equals($signature, trim($storedSignature));
    }

    if (photo_watermark_generation_available()) {
        return true;
    }

    $watermarkPath = __DIR__ . '/assets/watermark/watermark.png';
    clearstatcache(true, $sourcePath);
    clearstatcache(true, $variantPath);
    clearstatcache(true, $watermarkPath);
    $sourceMtime = filemtime($sourcePath);
    $variantMtime = filemtime($variantPath);
    $watermarkMtime = is_file($watermarkPath) ? filemtime($watermarkPath) : false;

    return ($sourceMtime !== false && ($variantMtime === false || $sourceMtime > $variantMtime))
        || ($watermarkMtime !== false && ($variantMtime === false || $watermarkMtime >= $variantMtime));
}
/**
 * Wasserzeichen auf ein Bild legen (unten rechts)
 */
function apply_watermark(string $targetImagePath, string $watermarkPath): bool {
    if (!is_file($watermarkPath)
        || !function_exists('imagecreatefromwebp')
        || !function_exists('imagecreatefrompng')
        || !function_exists('imagecopyresampled')
        || !function_exists('imagewebp')) {
        return false;
    }

    $image = imagecreatefromwebp($targetImagePath);
    $watermark = imagecreatefrompng($watermarkPath);

    if (!$image || !$watermark) {
        return false;
    }

    $imgWidth = imagesx($image);
    $imgHeight = imagesy($image);

    $wmWidth = imagesx($watermark);
    $wmHeight = imagesy($watermark);

    $renderWidth = min($wmWidth, max(1, (int) round($imgWidth * 0.24)));
    $scale = $renderWidth / $wmWidth;
    $renderHeight = max(1, (int) round($wmHeight * $scale));
    $margin = max(8, (int) round(min($imgWidth, $imgHeight) * 0.02));
    $dstX = max(0, $imgWidth - $renderWidth - $margin);
    $dstY = max(0, $imgHeight - $renderHeight - $margin);

    imagealphablending($image, true);
    imagecopyresampled($image, $watermark, $dstX, $dstY, 0, 0, $renderWidth, $renderHeight, $wmWidth, $wmHeight);

    $saved = imagewebp($image, $targetImagePath, 80);

    imagedestroy($image);
    imagedestroy($watermark);

    return $saved;
}

/**
 * GPS-Helferfunktionen
 */
function gps_part_to_float($part) {
    $parts = explode('/', $part);
    if (count($parts) === 2) {
        return floatval($parts[0]) / floatval($parts[1]);
    }
    return floatval($part);
}

function gps_to_decimal($coord, $hemisphere) {
    if (!$coord) return null;

    $degrees = gps_part_to_float($coord[0] ?? 0);
    $minutes = gps_part_to_float($coord[1] ?? 0);
    $seconds = gps_part_to_float($coord[2] ?? 0);

    $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

    if ($hemisphere === 'S' || $hemisphere === 'W') {
        $decimal *= -1;
    }

    return $decimal;
}

/**
 * EXIF-Daten auslesen
 */
function read_exif_data(string $filePath): array {
    if (!function_exists('exif_read_data')) {
        return [];
    }

    $exif = @exif_read_data($filePath, 'EXIF', true);

    if (!$exif) {
        return [];
    }

    return [
        'camera'      => $exif['IFD0']['Model'] ?? null,
        'lens'        => $exif['EXIF']['LensModel'] ?? null,
        'focal'       => $exif['EXIF']['FocalLength'] ?? null,
        'aperture'    => $exif['EXIF']['FNumber'] ?? null,
        'exposure'    => $exif['EXIF']['ExposureTime'] ?? null,
        'iso'         => $exif['EXIF']['ISOSpeedRatings'] ?? null,
        'datetime'    => $exif['EXIF']['DateTimeOriginal'] ?? null,

        // GPS in Dezimalform
        'gps_lat'     => gps_to_decimal($exif['GPS']['GPSLatitude'] ?? null, $exif['GPS']['GPSLatitudeRef'] ?? null),
        'gps_lon'     => gps_to_decimal($exif['GPS']['GPSLongitude'] ?? null, $exif['GPS']['GPSLongitudeRef'] ?? null),
    ];
}

/**
 * Hilfsfunktion: WebP erzeugen
 */
function create_webp_variant(string $source, string $target, int $maxWidth): bool {
    if (!function_exists('imagecreatefromstring')
        || !function_exists('imagecreatetruecolor')
        || !function_exists('imagecopyresampled')
        || !function_exists('imagewebp')) {
        return false;
    }

    $info = getimagesize($source);
    if ($info === false) {
        return false;
    }

    [$width, $height] = $info;

    $ratio = $width / $height;
    $newWidth = min($width, $maxWidth);
    $newHeight = (int)($newWidth / $ratio);

    $image = imagecreatefromstring(file_get_contents($source));
    if (!$image) {
        return false;
    }

    $resized = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    ensure_dir(dirname($target));
    $saved = imagewebp($resized, $target, 80);

    imagedestroy($image);
    imagedestroy($resized);

    return $saved;
}

function create_watermarked_webp_variant(string $source, string $target, int $maxWidth): bool {
    $watermarkPath = __DIR__ . '/assets/watermark/watermark.png';
    ensure_dir(dirname($target));
    $temporaryTarget = tempnam(dirname($target), 'photo-');
    if ($temporaryTarget === false) {
        return false;
    }

    if (!create_webp_variant($source, $temporaryTarget, $maxWidth)
        || !apply_watermark($temporaryTarget, $watermarkPath)) {
        @unlink($temporaryTarget);
        return false;
    }

    if (!rename($temporaryTarget, $target)) {
        @unlink($temporaryTarget);
        return false;
    }

    if (!photo_variant_write_metadata($source, $target)) {
        @unlink(photo_variant_metadata_path($target));
        return false;
    }

    return true;
}

/**
 * Varianten erzeugen (falls nicht vorhanden)
 */
if ($variant === 'preview') {

    if (photo_variant_is_stale($filePath, $previewFile)) {
        create_watermarked_webp_variant($filePath, $previewFile, 600);
    }
    $previewIsCurrent = is_file($previewFile) && !photo_variant_is_stale($filePath, $previewFile);
    $servedPath = $previewIsCurrent ? $previewFile : $filePath;
    $cacheDuration = $previewIsCurrent ? 86400 : 3600;

} elseif ($variant === 'gallery') {

    if (photo_variant_is_stale($filePath, $galleryFile)) {
        create_watermarked_webp_variant($filePath, $galleryFile, 1600);
    }

    $galleryIsCurrent = is_file($galleryFile) && !photo_variant_is_stale($filePath, $galleryFile);
    $servedPath = $galleryIsCurrent ? $galleryFile : $filePath;
    $cacheDuration = $galleryIsCurrent ? 86400 : 3600;

} elseif ($variant === 'thumb') {

    if (photo_variant_is_stale($filePath, $thumbFile)) {
        create_watermarked_webp_variant($filePath, $thumbFile, 300);
    }
    $thumbIsCurrent = is_file($thumbFile) && !photo_variant_is_stale($filePath, $thumbFile);
    $servedPath = $thumbIsCurrent ? $thumbFile : $filePath;
    $cacheDuration = $thumbIsCurrent ? 86400 : 3600;

} else {

    // Original ausliefern
    $servedPath = $filePath;
    $cacheDuration = 3600;
}

/**
 * MIME-Type bestimmen
 */
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
if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'HEAD') {
    $exifHeader = json_encode(read_exif_data($filePath), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if (is_string($exifHeader)) {
        header('X-Photo-Exif: ' . $exifHeader);
    }
}
header('Content-Length: ' . filesize($servedPath));

readfile($servedPath);
