# WordPress.org submission guide

Step-by-step checklist for publishing **Cooper Bold Logo Wrangler** on the WordPress plugin directory. Steps that need your account or secrets are marked **(you)**.

**Display name (chosen 2026-07-06):** **Cooper Bold Logo Wrangler** — set in the `Plugin Name` header (`cooper-bold-logo-wrangler.php`) and `readme.txt` title. WordPress.org derives the directory slug from that header (expected: `cooper-bold-logo-wrangler`). The install folder slug, text domain, and block name stay `cooper-bold-logo-wrangler` / `cooper-bold/logo-wrangler` (no text-domain change).

**Current prep state (2026-06-21):** version **1.2.12**, Splide vendored under `lib/splide/`, directory PNGs in `assets/`, release zip via `scripts/build-release-zip.sh`.

## Checklist

| Step | Status | Notes |
| --- | --- | --- |
| Version aligned (`cooper-bold-logo-wrangler.php`, `package.json`, `readme.txt`, `block.json`) | ✅ Done | **1.2.12** |
| `readme.txt` — Stable tag, Tested up to, GPL header | ✅ Done | Tested up to **6.9**; changelog de-cliented |
| Directory PNGs (`assets/banner-772x250.png`, icon, screenshots) | ✅ Done | Branded placeholders; replace with design exports before launch if desired |
| Splide bundled locally (no jsDelivr on frontend) | ✅ Done | `lib/splide/` ships in release ZIP |
| Release ZIP builds cleanly (`.distignore`) | ✅ Done | `dist/balanced-logos-1.2.12.zip` (41 files; excludes `composer-setup.php`, dev docs) |
| PHPUnit / Jest | ✅ Done | 38 PHPUnit, 16 Jest (2026-06-21) |
| Plugin Check on staging WP | ⬜ **(you)** | Install [Plugin Check](https://wordpress.org/plugins/plugin-check/) on WP 6.4+ |
| WordPress.org account + 2FA | ⬜ **(you)** | Contributor slug: `cooperbold` |
| Submit ZIP for review | ⬜ **(you)** | [Add your plugin](https://wordpress.org/plugins/developers/add/) |
| SVN credentials + GitHub secrets | ⬜ **(you)** | `SVN_USERNAME`, `SVN_PASSWORD` after approval |
| Tag + deploy (`v1.2.12`) | ⬜ **(you)** | After SVN access; triggers `.github/workflows/deploy.yml` |

## 1. Create a WordPress.org account **(you)**

1. Register at [wordpress.org](https://wordpress.org/support/register.php) if you do not have an account.
2. Choose or confirm your **contributor slug** (used in `readme.txt`; currently `cooperbold`).
3. Enable two-factor authentication on the account.

## 2. Build a clean plugin ZIP

From the repo root:

```bash
npm ci && npm run build
chmod +x scripts/build-release-zip.sh
./scripts/build-release-zip.sh
```

The ZIP is written to `dist/balanced-logos-1.2.12.zip`. It respects `.distignore` (no `node_modules`, `src/`, dev docs, etc.) and **includes** `lib/splide/` (bundled carousel assets).

Regenerate directory PNG placeholders (optional):

```bash
NODE_PATH=node_modules node scripts/wporg-assets/compose.js
```

## 3. Submit for review **(you)**

1. Go to [Add your plugin](https://wordpress.org/plugins/developers/add/).
2. Upload the ZIP from step 2.
3. Wait for the review team email (often a few days to two weeks).
4. When approved, you receive **SVN credentials** for `https://plugins.svn.wordpress.org/balanced-logos/`.

> **Pre-approval:** The first submission is reviewed manually. Plugin Check (below) and escaping/sanitization in this repo are aligned with common review feedback, but approval is not guaranteed until a human reviewer accepts the plugin.

## 4. Add GitHub secrets for automated deploy **(you)**

After SVN access is granted:

1. Open **GitHub → CooperBold/balanced-logos → Settings → Secrets and variables → Actions**.
2. Add repository secrets:
   - `SVN_USERNAME` — your wordpress.org username
   - `SVN_PASSWORD` — your [application password](https://make.wordpress.org/core/handbook/tutorials/generate-a-password-for-svn/) (not your login password)

Pushes of tags matching `v*` on `main` run `.github/workflows/deploy.yml`, which builds assets and deploys via [10up/action-wordpress-plugin-deploy](https://github.com/10up/action-wordpress-plugin-deploy).

## 5. First release tag **(you)**

When SVN is ready and secrets are set:

```bash
git checkout main
git pull --rebase
npm ci && npm run build
git status   # ensure build/ is committed if changed
git tag -a v1.2.12 -m "Release 1.2.12"
git push origin main
git push origin v1.2.12
```

The tag triggers deploy to `tags/1.2.12` and updates `trunk` on WordPress.org SVN.

## 6. Directory assets

PNG files live in `assets/` (see `docs/wporg-assets/README.md`); `deploy.yml` sets `ASSETS_DIR: assets`. They deploy to the SVN **`assets/`** directory (sibling to `trunk/`), not inside the plugin ZIP. The deploy action uploads them when present.

Replace placeholders with design-approved screenshots before promoting the plugin page, if desired.

## 7. Plugin Check **(you)**

On a staging WordPress site (6.4+):

1. Install the [Plugin Check](https://wordpress.org/plugins/plugin-check/) plugin.
2. Upload the release ZIP or clone from SVN trunk.
3. Run **Plugin Check → Check a plugin** and fix any reported issues before promoting to production.

This repo follows WordPress coding practices (ABSPATH guards, escaping, sanitization, text domain `cooper-bold-logo-wrangler`). No `eval` or obfuscated JavaScript in source. Splide is bundled under `lib/splide/` (MIT) for standalone carousels.

## 8. After release

- Update `Stable tag` and changelog in `readme.txt` for each release.
- Bump `Version` in `cooper-bold-logo-wrangler.php`, `COOPBOLO_VERSION`, `src/block/block.json` (`version`), run `npm run build`, then tag `v*`.
- Monitor the [plugin support forum](https://wordpress.org/support/plugin/balanced-logos/) once live.

## Cannot automate without you

| Task | Blocker |
| --- | --- |
| Plugin directory submission | WordPress.org account + review |
| SVN deploy | `SVN_USERNAME` / `SVN_PASSWORD` secrets |
| Plugin Check on live site | Staging WordPress install |
| Marketing screenshots | Design-approved PNG exports (optional upgrade from placeholders) |
