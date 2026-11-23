#!/usr/bin/env python3
"""Draw eight fictional brand logos (mixed shapes, padding, weight) for directory screenshots.

Writes PNGs to uncommitted/demo-logos/. The brands are made up; never use real logos.
"""

from pathlib import Path

from PIL import Image, ImageDraw, ImageFont

ROOT = Path(__file__).resolve().parents[2]
OUT = ROOT / "uncommitted" / "demo-logos"
FONTS = "/System/Library/Fonts/Supplemental/"


def font(name, size):
    for candidate in (name, "Arial Bold.ttf", "Arial.ttf"):
        path = Path(FONTS + candidate)
        if path.exists():
            return ImageFont.truetype(str(path), size)
    return ImageFont.load_default()


def canvas(w, h):
    return Image.new("RGBA", (w, h), (0, 0, 0, 0))


def save(img, name):
    img.save(OUT / f"{name}.png", optimize=True)


def main():
    OUT.mkdir(parents=True, exist_ok=True)

    im = canvas(900, 300)  # wide thin wordmark, lots of padding
    ImageDraw.Draw(im).text((450, 150), "quillmark", font=font("Georgia.ttf", 90), fill=(30, 41, 59), anchor="mm")
    save(im, "quillmark")

    im = canvas(400, 400)  # heavy square monogram
    d = ImageDraw.Draw(im)
    d.rounded_rectangle((20, 20, 380, 380), 60, fill=(220, 38, 38))
    d.text((200, 205), "V", font=font("Arial Black.ttf", 260), fill="white", anchor="mm")
    save(im, "voltan")

    im = canvas(1000, 260)  # icon + wordmark
    d = ImageDraw.Draw(im)
    d.ellipse((30, 40, 210, 220), fill=(5, 150, 105))
    d.ellipse((90, 100, 150, 160), fill="white")
    d.text((250, 130), "Orbitry", font=font("Arial Bold.ttf", 120), fill=(17, 24, 39), anchor="lm")
    save(im, "orbitry")

    im = canvas(360, 520)  # tall stacked
    d = ImageDraw.Draw(im)
    d.polygon([(180, 30), (320, 260), (40, 260)], fill=(37, 99, 235))
    d.text((180, 330), "SUMMIT", font=font("Arial Bold.ttf", 60), fill=(30, 58, 138), anchor="mm")
    d.text((180, 400), "TRAIL", font=font("Arial.ttf", 46), fill=(30, 58, 138), anchor="mm")
    save(im, "summit-trail")

    im = canvas(1200, 800)  # small mark in a huge canvas
    ImageDraw.Draw(im).text((600, 400), "pebblewise", font=font("Arial.ttf", 70), fill=(124, 58, 237), anchor="mm")
    save(im, "pebblewise")

    im = canvas(820, 280)  # light outline logo
    d = ImageDraw.Draw(im)
    d.rounded_rectangle((20, 40, 800, 240), 100, outline=(14, 116, 144), width=6)
    d.text((410, 140), "FERNHOLLOW", font=font("Arial.ttf", 70), fill=(14, 116, 144), anchor="mm")
    save(im, "fernhollow")

    im = canvas(760, 220)  # dense heavy wordmark
    ImageDraw.Draw(im).text((380, 110), "ZENTRO", font=font("Arial Black.ttf", 150), fill=(15, 23, 42), anchor="mm")
    save(im, "zentro")

    im = canvas(980, 300)  # two-tone serif
    d = ImageDraw.Draw(im)
    d.text((40, 150), "Brindle", font=font("Georgia Bold.ttf", 110), fill=(146, 64, 14), anchor="lm")
    d.text((520, 150), "& Oak", font=font("Georgia.ttf", 110), fill=(120, 113, 108), anchor="lm")
    save(im, "brindle-oak")

    print("Wrote demo logos to", OUT)


if __name__ == "__main__":
    main()
