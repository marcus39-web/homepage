<?php

declare(strict_types=1);

function photo_library_root(): ?string
{
	$configuredPath = app_env(
		'PHOTO_LIBRARY_PATH',
		'D:/10_Fotoarchiv/Canon_R10_Bilder/01_Bibiothek_JPG'
	);
	$root = realpath($configuredPath);

	return $root !== false && is_dir($root) ? $root : null;
}

function photo_library_is_private(string $name): bool
{
	return str_contains(strtolower($name), 'privat');
}

function photo_library_is_excluded_category(string $name): bool
{
	return photo_library_is_private($name) || preg_match('/^\d+[._-]*web$/i', $name) === 1;
}

function photo_library_is_supported_file(string $path): bool
{
	return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true);
}

function photo_library_image_url(string $category, string $relativePath): string
{
	return '/public/photo.php?' . http_build_query([
		'category' => $category,
		'file' => $relativePath,
	]);
}

/**
 * @return array<int, array{name: string, label: string, photos: array<int, array{url: string, alt: string}>}>
 */
function get_photo_categories(): array
{
	$root = photo_library_root();
	if ($root === null) {
		return [];
	}

	$categories = [];
	$categoryDirectories = glob($root . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [];
	natsort($categoryDirectories);

	foreach ($categoryDirectories as $categoryDirectory) {
		$categoryName = basename($categoryDirectory);
		if (photo_library_is_excluded_category($categoryName)) {
			continue;
		}

		$categoryRoot = realpath($categoryDirectory);
		if ($categoryRoot === false) {
			continue;
		}

		$directory = new RecursiveDirectoryIterator($categoryRoot, FilesystemIterator::SKIP_DOTS);
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
			if ($realFilePath === false || !str_starts_with($realFilePath, $categoryRoot . DIRECTORY_SEPARATOR)) {
				continue;
			}

			$relativePath = str_replace(DIRECTORY_SEPARATOR, '/', substr($realFilePath, strlen($categoryRoot) + 1));
			if (count(array_filter(explode('/', $relativePath), 'photo_library_is_private')) > 0) {
				continue;
			}

			$imageName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
			$photos[] = [
				'url' => photo_library_image_url($categoryName, $relativePath),
				'alt' => trim(str_replace(['_', '-'], ' ', $imageName)),
			];
		}

		usort($photos, static fn (array $left, array $right): int => strnatcasecmp($left['alt'], $right['alt']));
		if ($photos === []) {
			continue;
		}

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
	if ($root === null || $category === '' || photo_library_is_excluded_category($category)) {
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
	foreach ($segments as $segment) {
		if (photo_library_is_private($segment)) {
			return null;
		}
	}

	$filePath = realpath($categoryPath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $segments));
	if ($filePath === false || !is_file($filePath) || !str_starts_with($filePath, $categoryPath . DIRECTORY_SEPARATOR)) {
		return null;
	}

	return photo_library_is_supported_file($filePath) ? $filePath : null;
}