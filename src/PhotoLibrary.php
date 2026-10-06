<?php

declare(strict_types=1);

function photo_library_root(): ?string
{
    $paths = [
        app_env('PHOTO_LIBRARY_PATH'),
        DATA_PATH . '/photos',
        'D:/10_Fotoarchiv/Canon_R10_Bilder/01_Bibiothek_JPG',
    ];

    foreach (array_unique($paths) as $path) {
        if ($path === '') {
            continue;
        }

        $root = realpath($path);
        if ($root !== false && is_dir($root)) {
            return $root;
        }
    }

    return null;
}

function photo_library_is_private(string $name): bool
{
    return str_contains(strtolower($name), 'privat');
}

function photo_library_is_excluded_category(string $name): bool
{
    return photo_library_is_private($name)
    || str_contains(strtolower($name), 'passbild')
        || preg_match('/^\d+(?:[._-]\d+)*[._-]*web$/i', $name) === 1;
}

function photo_library_is_supported_file(string $path): bool
{
    return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true);
}

function photo_library_image_url(string $category, string $relativePath): string
{
    $parameters = [
        'category' => $category,
        'file' => $relativePath,
    ];
    $sourcePath = resolve_photo_library_file($category, $relativePath);
    if ($sourcePath !== null) {
        clearstatcache(true, $sourcePath);
        $modifiedAt = filemtime($sourcePath);
        $fileSize = filesize($sourcePath);
        if ($modifiedAt !== false && $fileSize !== false) {
            $parameters['v'] = $modifiedAt . '-' . $fileSize;
        }
    }

    return '/public/photo.php?' . http_build_query($parameters);
}

function photo_library_image_variant_url(string $url, string $variant): string
{
    if (!in_array($variant, ['preview', 'gallery', 'thumb'], true)) {
        return $url;
    }

    return $url . '&variant=' . rawurlencode($variant);
}

function photo_library_get_exif_datetime(string $filePath): ?int
{
    if (!function_exists('exif_read_data')) {
        return filemtime($filePath) ?: null;
    }

    $exif = @exif_read_data($filePath, 'EXIF', true);

    if (isset($exif['EXIF']['DateTimeOriginal'])) {
        $ts = strtotime($exif['EXIF']['DateTimeOriginal']);
        if ($ts !== false) {
            return $ts;
        }
    }

    if (isset($exif['IFD0']['DateTime'])) {
        $ts = strtotime($exif['IFD0']['DateTime']);
        if ($ts !== false) {
            return $ts;
        }
    }

    return filemtime($filePath) ?: null;
}

/**
 * @return array<int, array{name: string, label: string, photos: array<int, array{url: string, alt: string, path: string, datetime: int|null}>}>
 */
function get_photo_categories(bool $includeWebCategory = false): array
{
    if (!$includeWebCategory) {
        return [];
    }

    $root = photo_library_root();
    if ($root === null) {
        return [];
    }

    $webRoot = realpath($root . DIRECTORY_SEPARATOR . '20.02_Web');
    if ($webRoot === false || dirname($webRoot) !== $root) {
        return [];
    }

    $directory = new RecursiveDirectoryIterator($webRoot, FilesystemIterator::SKIP_DOTS);
    $filtered = new RecursiveCallbackFilterIterator(
        $directory,
        static fn (SplFileInfo $item): bool => !$item->isDir() || !photo_library_is_private($item->getFilename())
    );
    $iterator = new RecursiveIteratorIterator($filtered);
    $photos = [];

    foreach ($iterator as $file) {
        if (!$file->isFile() || !photo_library_is_supported_file($file->getFilename())) {
            continue;
        }

        $realFilePath = $file->getRealPath();
        if ($realFilePath === false || !str_starts_with($realFilePath, $webRoot . DIRECTORY_SEPARATOR)) {
            continue;
        }

        $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', substr($realFilePath, strlen($webRoot) + 1));
        $segments = explode('/', $relativePath);
        if (count($segments) < 2
            || count(array_filter($segments, 'photo_library_is_private')) > 0
            || str_contains(strtolower($relativePath), 'passbild')
            || photo_library_is_excluded_category($segments[0])) {
            continue;
        }

        $imageName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        $photos[] = [
            'url' => photo_library_image_url('20.02_Web', $relativePath),
            'alt' => trim(str_replace(['_', '-'], ' ', $imageName)),
            'path' => $relativePath,
            'datetime' => photo_library_get_exif_datetime($realFilePath),
        ];
    }

    usort($photos, static fn ($a, $b) => ($b['datetime'] ?? 0) <=> ($a['datetime'] ?? 0));

    return [[
        'name' => '20.02_Web',
        'label' => 'Web',
        'photos' => $photos,
    ]];
}

/**
 * @return array<int, array{name: string, label: string, photos: array<int, array{url: string, alt: string, path: string, datetime: int|null}>}>
 */
function get_photo_web_export_categories(): array
{
    $webCategory = get_photo_categories(true)[0] ?? null;
    if ($webCategory === null) {
        return [];
    }

    $root = photo_library_root();
    $webRoot = $root !== null ? realpath($root . DIRECTORY_SEPARATOR . '20.02_Web') : false;
    if ($webRoot === false || $root === null || dirname($webRoot) !== $root) {
        return [];
    }

    $directories = glob($webRoot . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [];
    natsort($directories);
    $categories = [];

    foreach ($directories as $directory) {
        $categoryName = basename($directory);
        $categoryRoot = realpath($directory);
        if (photo_library_is_excluded_category($categoryName)
            || $categoryRoot === false
            || dirname($categoryRoot) !== $webRoot) {
            continue;
        }

        $prefix = $categoryName . '/';
        $photos = [];
        foreach ($webCategory['photos'] as $photo) {
            $relativePath = (string) ($photo['path'] ?? '');
            if (!str_starts_with($relativePath, $prefix)) {
                continue;
            }

            $photo['path'] = substr($relativePath, strlen($prefix));
            $photos[] = $photo;
        }

        usort($photos, static fn ($a, $b) => ($b['datetime'] ?? 0) <=> ($a['datetime'] ?? 0));
        $label = preg_replace('/^\d+[._-]*/u', '', $categoryName) ?? $categoryName;
        $categories[] = [
            'name' => $categoryName,
            'label' => trim(str_replace(['_', '-'], ' ', $label)),
            'photos' => $photos,
        ];
    }

    return $categories;
}

function resolve_photo_library_file(string $category, string $relativePath): ?string
{
    $root = photo_library_root();
    if ($root === null || $category !== '20.02_Web') {
        return null;
    }

    $categoryPath = realpath($root . DIRECTORY_SEPARATOR . $category);
    if ($categoryPath === false || dirname($categoryPath) !== $root || !is_dir($categoryPath)) {
        return null;
    }

    $segments = preg_split('~[\\\\/]~', $relativePath) ?: [];
    if ($segments === [] || in_array('', $segments, true) || in_array('.', $segments, true) || in_array('..', $segments, true)) {
        return null;
    }
    if (count($segments) < 2) {
        return null;
    }

    foreach ($segments as $index => $segment) {
        if (photo_library_is_private($segment) || str_contains(strtolower($segment), 'passbild')) {
            return null;
        }
        if ($index === 0 && photo_library_is_excluded_category($segment)) {
            return null;
        }
    }

    $filePath = realpath($categoryPath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $segments));
    if ($filePath === false || !is_file($filePath) || !str_starts_with($filePath, $categoryPath . DIRECTORY_SEPARATOR)) {
        return null;
    }

    return photo_library_is_supported_file($filePath) ? $filePath : null;
}
