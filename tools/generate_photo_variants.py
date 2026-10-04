from __future__ import annotations

import argparse
import os
import re
import sys
from pathlib import Path

from PIL import Image, ImageOps


PROJECT_ROOT = Path(__file__).resolve().parents[1]
DEFAULT_SOURCE = Path(r"D:\10_Fotoarchiv\Canon_R10_Bilder\01_Bibiothek_JPG")
WATERMARK_PATH = PROJECT_ROOT / "public" / "assets" / "watermark" / "watermark.png"
SUPPORTED_EXTENSIONS = {".jpg", ".jpeg", ".png", ".webp"}
VARIANTS = {
    "preview": (800, 78),
    "gallery": (1800, 82),
}


def is_private(name: str) -> bool:
    return "privat" in name.casefold()


def is_excluded_category(name: str) -> bool:
    return is_private(name) or re.fullmatch(r"\d+(?:[._-]\d+)*[._-]*web", name, re.IGNORECASE) is not None


def iter_photo_files(source_root: Path):
    for category in sorted(source_root.iterdir()):
        if not category.is_dir() or is_excluded_category(category.name):
            continue

        for current_root, directory_names, file_names in os.walk(category):
            directory_names[:] = [name for name in directory_names if not is_private(name)]
            current_path = Path(current_root)
            for file_name in file_names:
                path = current_path / file_name
                relative_path = path.relative_to(source_root)
                if path.suffix.casefold() in SUPPORTED_EXTENSIONS and not any(is_private(part) for part in relative_path.parts):
                    yield path, relative_path


def save_variant(source: Path, relative_path: Path, output_root: Path, variant: str, max_dimension: int, quality: int, force: bool) -> bool:
    # Mirror the archive path so photo.php can find the variant without changing the original.
    target = output_root / variant / relative_path.parent / f"{relative_path.name}.webp"
    required_mtime = source.stat().st_mtime_ns
    if WATERMARK_PATH.is_file():
        required_mtime = max(required_mtime, WATERMARK_PATH.stat().st_mtime_ns)
    if not force and target.is_file() and target.stat().st_mtime_ns > required_mtime:
        return False

    target.parent.mkdir(parents=True, exist_ok=True)
    temporary_target = target.with_name(target.name + ".tmp")

    try:
        with Image.open(source) as original:
            icc_profile = original.info.get("icc_profile")
            image = ImageOps.exif_transpose(original)
            image.thumbnail((max_dimension, max_dimension), Image.Resampling.LANCZOS)

            has_alpha = "A" in image.getbands() or "transparency" in original.info
            if image.mode not in ("RGB", "RGBA"):
                image = image.convert("RGBA" if has_alpha else "RGB")

            if WATERMARK_PATH.is_file():
                with Image.open(WATERMARK_PATH) as watermark_source:
                    watermark = watermark_source.convert("RGBA")
                watermark_width = min(watermark.width, max(1, int(image.width * 0.24)))
                watermark_height = max(1, int(watermark.height * watermark_width / watermark.width))
                watermark = watermark.resize((watermark_width, watermark_height), Image.Resampling.LANCZOS)
                image = image.convert("RGBA")
                margin = max(8, int(min(image.size) * 0.02))
                position = (max(0, image.width - watermark.width - margin), max(0, image.height - watermark.height - margin))
                image.alpha_composite(watermark, position)
                if not has_alpha:
                    image = image.convert("RGB")

            image.save(
                temporary_target,
                format="WEBP",
                quality=quality,
                method=6,
                icc_profile=icc_profile,
            )

        # Let the endpoint fall back to the source if WebP would use more bytes.
        if temporary_target.stat().st_size >= source.stat().st_size:
            temporary_target.unlink()
            return False

        temporary_target.replace(target)
        return True
    except Exception as error:
        if temporary_target.exists():
            temporary_target.unlink()
        print(f"Skipped {source}: {error}", file=sys.stderr)
        return False


def main() -> int:
    parser = argparse.ArgumentParser(description="Create cached WebP variants without changing photo originals.")
    parser.add_argument(
        "--source",
        type=Path,
        default=Path(os.environ.get("PHOTO_LIBRARY_PATH", str(DEFAULT_SOURCE))),
        help="Photo archive root (defaults to PHOTO_LIBRARY_PATH or the local archive path).",
    )
    parser.add_argument(
        "--output",
        type=Path,
        default=PROJECT_ROOT / "data" / "photo-cache",
        help="Output directory mirrored by the PHP photo endpoint.",
    )
    parser.add_argument("--match", help="Only process source paths containing this text.")
    parser.add_argument("--force", action="store_true", help="Recreate variants even when the source has not changed.")
    args = parser.parse_args()

    source_root = args.source.resolve()
    if not source_root.is_dir():
        parser.error(f"Photo source directory does not exist: {source_root}")

    files = list(iter_photo_files(source_root))
    if args.match:
        match = args.match.casefold()
        files = [(path, relative) for path, relative in files if match in relative.as_posix().casefold()]
    if not files:
        print("No eligible photos found.", file=sys.stderr)
        return 1

    generated = 0
    for source, relative_path in files:
        for variant, (max_dimension, quality) in VARIANTS.items():
            if save_variant(source, relative_path, args.output, variant, max_dimension, quality, args.force):
                generated += 1

    print(f"Processed {len(files)} source photo(s); created {generated} WebP variant(s) in {args.output.resolve()}.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())