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

function photo_variant_is_stale(string $sourcePath, string $variantPath): bool {
    if (!is_file($variantPath)) {
        return true;
    }

    clearstatcache(true, $sourcePath);
    clearstatcache(true, $variantPath);
    $sourceMtime = filemtime($sourcePath);
    $variantMtime = filemtime($variantPath);

    return $sourceMtime !== false && ($variantMtime === false || $sourceMtime > $variantMtime);
}

/**
 * Wasserzeichen auf ein Bild legen (unten rechts)
 */
function apply_watermark(string $targetImagePath, string $watermarkPath): bool {
    if (!is_file($watermarkPath)) {
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

    // Position unten rechts mit 20px Abstand
    $dstX = $imgWidth - $wmWidth - 20;
    $dstY = $imgHeight - $wmHeight - 20;

    imagecopy($image, $watermark, $dstX, $dstY, 0, 0, $wmWidth, $wmHeight);

    imagewebp($image, $targetImagePath, 80);

    imagedestroy($image);
    imagedestroy($watermark);

    return true;
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
    imagewebp($resized, $target, 80);

    imagedestroy($image);
    imagedestroy($resized);

    return true;
}

/**
 * Varianten erzeugen (falls nicht vorhanden)
 */
if ($variant === 'preview') {

    if (photo_variant_is_stale($filePath, $previewFile) && !create_webp_variant($filePath, $previewFile, 600)) {
        $servedPath = $filePath;
        $cacheDuration = 3600;
    } else {
        $previewIsCurrent = is_file($previewFile) && !photo_variant_is_stale($filePath, $previewFile);
        $servedPath = $previewIsCurrent ? $previewFile : $filePath;
        $cacheDuration = $previewIsCurrent ? 86400 : 3600;
    }

} elseif ($variant === 'gallery') {

    if (photo_variant_is_stale($filePath, $galleryFile)) {
        if (create_webp_variant($filePath, $galleryFile, 1600)) {
            $watermarkPath = __DIR__ . '/assets/watermark/watermark.png';
            apply_watermark($galleryFile, $watermarkPath);
        }
    }

    $galleryIsCurrent = is_file($galleryFile) && !photo_variant_is_stale($filePath, $galleryFile);
    $servedPath = $galleryIsCurrent ? $galleryFile : $filePath;
    $cacheDuration = $galleryIsCurrent ? 86400 : 3600;

} elseif ($variant === 'thumb') {

    if (photo_variant_is_stale($filePath, $thumbFile) && !create_webp_variant($filePath, $thumbFile, 300)) {
        $servedPath = $filePath;
        $cacheDuration = 3600;
    } else {
        $thumbIsCurrent = is_file($thumbFile) && !photo_variant_is_stale($filePath, $thumbFile);
        $servedPath = $thumbIsCurrent ? $thumbFile : $filePath;
        $cacheDuration = $thumbIsCurrent ? 86400 : 3600;
    }

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
