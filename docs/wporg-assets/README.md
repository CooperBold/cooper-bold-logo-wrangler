# WordPress.org directory assets

Files in `assets/` (repo root) deploy to the **SVN `assets/` directory** (sibling to `trunk/`), not inside the plugin ZIP. The [10up deploy action](https://github.com/10up/action-wordpress-plugin-deploy) uploads them automatically when present.

## Required images

| File | Size | Purpose |
| --- | --- | --- |
| `banner-772x250.png` | 772×250 px | Plugin page header |
| `icon-256x256.png` | 256×256 px | Plugin directory icon |
| `screenshot-1.png` | 1200×900 px (4:3) | Block editor with logos selected |
| `screenshot-2.png` | 1200×900 px (4:3) | Frontend normalized logo strip |

WordPress.org accepts PNG or JPG. Filenames must match the patterns above.

## Where things come from

`banner-placeholder.svg` and `icon-placeholder.svg` (in this folder) are design references. `assets/` holds only what WordPress.org shows, because the deploy copies the whole folder: the banner and icon come from `scripts/generate-wporg-assets.py`, and the screenshots are real captures (`scripts/wporg-screenshots/`).
