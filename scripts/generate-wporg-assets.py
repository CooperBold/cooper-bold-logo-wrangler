#!/usr/bin/env python3
"""Generate WordPress.org directory art: banner and icon.

Screenshots are real captures of the plugin (see wiki/guides/wordpress-org-submission.md),
so this script only draws them with --placeholder-screenshots.
"""

from __future__ import annotations

import sys
from pathlib import Path

from PIL import Image, ImageDraw, ImageFont

ROOT = Path(__file__).resolve().parents[1]
ASSETS = ROOT / "assets"

BG_TOP = (26, 26, 46)
BG_BOTTOM = (22, 33, 62)
ACCENT = [(233, 69, 96), (245, 166, 35), (78, 205, 196)]
WHITE = (255, 255, 255)
MUTED = (200, 214, 229)


def gradient(size: tuple[int, int]) -> Image.Image:
    img = Image.new("RGB", size, BG_TOP)
    draw = ImageDraw.Draw(img)
    w, h = size
    for y in range(h):
        ratio = y / max(h - 1, 1)
        color = tuple(
            int(BG_TOP[i] + (BG_BOTTOM[i] - BG_TOP[i]) * ratio) for i in range(3)
        )
        draw.line([(0, y), (w, y)], fill=color)
    return img


def load_font(size: int, bold: bool = False) -> ImageFont.FreeTypeFont | ImageFont.ImageFont:
    candidates = [
        "/System/Library/Fonts/Supplemental/Arial Bold.ttf" if bold else "/System/Library/Fonts/Supplemental/Arial.ttf",
        "/Library/Fonts/Arial Bold.ttf" if bold else "/Library/Fonts/Arial.ttf",
        "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf" if bold else "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf",
    ]
    for path in candidates:
        if Path(path).exists():
            return ImageFont.truetype(path, size)
    return ImageFont.load_default()


def draw_logo_mark(draw: ImageDraw.ImageDraw, cx: int, cy: int) -> None:
    radii = [48, 36, 28]
    offsets = [-50, 0, 40]
    for radius, offset, color in zip(radii, offsets, ACCENT):
        draw.ellipse(
            (cx + offset - radius, cy - radius, cx + offset + radius, cy + radius),
            fill=color + (230,),
        )


def banner() -> None:
    img = gradient((772, 250))
    draw = ImageDraw.Draw(img)
    draw_logo_mark(draw, 120, 125)
    title_font = load_font(36, bold=True)
    sub_font = load_font(18)
    draw.text((270, 82), "Logo Wrangler", fill=WHITE, font=title_font)
    draw.text((272, 130), "by Cooper Bold", fill=MUTED, font=sub_font)
    draw.text(
        (272, 160),
        "Balanced partner logo strips for WordPress",
        fill=MUTED,
        font=sub_font,
    )
    img.save(ASSETS / "banner-772x250.png", optimize=True)


def icon() -> None:
    img = Image.new("RGB", (256, 256), BG_TOP)
    draw = ImageDraw.Draw(img)
    draw.rounded_rectangle((0, 0, 255, 255), radius=48, fill=BG_TOP)
    for radius, offset, color in zip([40, 30, 22], [-40, 0, 32], ACCENT):
        draw.ellipse(
            (128 + offset - radius, 128 - radius, 128 + offset + radius, 128 + radius),
            fill=color,
        )
    font = load_font(22, bold=True)
    draw.text((128, 198), "LW", fill=WHITE, font=font, anchor="mm")
    img.save(ASSETS / "icon-256x256.png", optimize=True)


def screenshot(number: int, title: str, subtitle: str) -> None:
    img = gradient((1200, 900))
    draw = ImageDraw.Draw(img)
    draw.rounded_rectangle((60, 60, 1140, 840), radius=24, fill=(15, 20, 35))
    draw.rounded_rectangle((90, 110, 1110, 760), radius=16, fill=(28, 36, 58))
    title_font = load_font(42, bold=True)
    sub_font = load_font(24)
    draw.text((600, 380), title, fill=WHITE, font=title_font, anchor="mm")
    draw.text((600, 440), subtitle, fill=MUTED, font=sub_font, anchor="mm")
    draw_logo_mark(draw, 600, 560)
    badge_font = load_font(18, bold=True)
    draw.rounded_rectangle((1020, 780, 1110, 820), radius=8, fill=ACCENT[0])
    draw.text((1065, 800), f"#{number}", fill=WHITE, font=badge_font, anchor="mm")
    img.save(ASSETS / f"screenshot-{number}.png", optimize=True)


def main() -> None:
    ASSETS.mkdir(parents=True, exist_ok=True)
    banner()
    icon()
    if "--placeholder-screenshots" in sys.argv:
        screenshot(1, "Block editor", "Select logos and tune normalization")
        screenshot(2, "Frontend strip", "Normalized logo row on the page")
    print("Wrote PNG assets to", ASSETS)


if __name__ == "__main__":
    main()
