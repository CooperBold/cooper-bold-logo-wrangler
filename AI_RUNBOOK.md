# Cooper Bold Logo Wrangler WP Plugin — AI Runbook

> Read this first before changing plugin code.

---

## What this project is

**Cooper Bold Logo Wrangler** is a WordPress plugin that displays client and partner logos in a balanced strip using [**@sanity-labs/logo-soup**](https://www.npmjs.com/package/@sanity-labs/logo-soup) for normalization and layout.

Editors can use:

- **Gutenberg block** `cooper-bold/logo-wrangler` (`src/block/`)
- **Shortcode** `[logo_wrangler]` with matching attributes (the old `[logo_soup]` / `[balanced_logos]` aliases were removed in 1.4.0)

---

## Tech stack

| Layer | Technology |
| --- | --- |
| WordPress | 6.4+ |
| PHP | 7.4+, `declare(strict_types=1)` |
| JS | Block editor (`src/block/`, React), front-end (`src/view.js` + `src/shared/mount-soup-dom.js`, no React since 1.5.0) |
| npm package | `@sanity-labs/logo-soup` 1.3.0 (pinned) |
| Build | `@wordpress/scripts` → `build/index.js`, `build/view.js` |
| AI tooling | SimpleMem, wiki, Context7, Claude Code rules in `.claude/rules/` |

---

## Project structure

```
cooper-bold-logo-wrangler.php     # Plugin header + bootstrap
includes/
  class-coopbolo-plugin.php         # Singleton coordinator (block + shortcodes)
  class-coopbolo-assets.php         # Script/style enqueue
  class-coopbolo-renderer.php       # HTML + data for logo soup
  class-coopbolo-collections.php    # CPT + admin UI
  class-coopbolo-admin-branding.php # Admin branding CSS
src/
  block/                           # block.json, edit.js, editor styles
  view.js                          # Front-end init (no React; mounts via shared/mount-soup-dom.js)
  shared/to-lw-props.js            # Shared editor/frontend prop sanitizer
build/                             # Compiled assets (npm run build)
wiki/                              # LLM wiki (Obsidian)
docs/simplemem/                    # SimpleMem store (committed)
```

---

## Key workflows

### Change block editor UI

1. Edit `src/block/edit.js` (and SCSS if needed).
2. `npm run start` for watch, or `npm run build` before commit.
3. Test block in wp-admin; verify front-end via renderer.

### Change front-end behavior

1. Edit `src/view.js` and/or `includes/class-coopbolo-renderer.php`.
2. Rebuild JS; clear caches on test site.

### Add shortcode attribute

1. Update shortcode handler in `class-coopbolo-plugin.php`.
2. Mirror attribute in `block.json` and editor if exposed in UI.
3. Pass through renderer consistently.

### Local testing with wp-env

Requires Docker (Colima on macOS). Repo path has spaces — `.wp-env.json` mounts a **no-spaces symlink**:

```bash
# One-time (adjust source path if clone differs):
ln -sfn "/Users/thedao/Repos/Logo Soup WP Plugin" /Users/thedao/Repos/cooper-bold-logo-wrangler-wp-plugin
```

Shallow `git clone` into `~/.wp-env` can fail `index-pack` on this host — use a local core checkout under `$HOME`:

```bash
export WP_ENV_CORE=/Users/thedao/wp-env-wordpress-core
npm ci && npm run build && npm run wp-env:start
```

- Site: `http://localhost:8888` (admin / password)
- WP-CLI plugin slug: `cooper-bold-logo-wrangler-wp-plugin` (directory name from mount)
- Stop: `npm run wp-env:stop`

**Smoke URLs (local):**

| Test | URL |
| --- | --- |
| Strip (`demo-50-logos`) | `http://localhost:8888/?p=4` |
| Carousel (`layout="carousel" wrapper="full"`) | `http://localhost:8888/?page_id=62` |
| Collection slug | `demo-50-logos` |

**CORS / logo URLs:** Cooper Bold Logo Wrangler hydrates logos via canvas. In local dev, logo `src` must be **same-origin** (WordPress Media Library uploads). External URLs (e.g. `picsum.photos`) fail hydration with CORS errors — seed collections with uploaded PNGs, not hotlinked images.

### Plugin Check (release-shaped)

Install [Plugin Check](https://wordpress.org/plugins/plugin-check/) in wp-env, then run against the mounted plugin with release-like excludes:

```bash
export WP_ENV_CORE=/Users/thedao/wp-env-wordpress-core
npx wp-env run cli -- wp plugin check cooper-bold-logo-wrangler-wp-plugin \
  --slug=cooper-bold-logo-wrangler \
  --exclude-directories=dist,docs,.cursor,.github,tests,node_modules,vendor,uncommitted,scripts \
  --exclude-files=composer-setup.php,composer.phar,.DS_Store,.env,.env.example,.wp-env.json,.phpunit.result.cache,.gitignore,.distignore,phpunit.xml.dist,AI_RUNBOOK.md,AI_SESSION_MEMORY.md,AGENTS.md \
  --ignore-warnings
```

Pass = no errors (warnings may remain). Splide must load from `lib/splide/` only — no jsDelivr on frontend.

**Vendored Splide (current):** `@splidejs/splide@4.1.4` + `@splidejs/splide-extension-auto-scroll@0.5.3` under `lib/splide/` — both are latest on npm. WP.org may still flag auto-scroll as outdated (compares to an unreleased upstream branch); do not bump until npm publishes a newer version. Refresh steps: `lib/splide/README.md`.

### Release checklist

1. `npm run build` and `npm run lint:js`
2. Bump `COOPBOLO_VERSION` and plugin header version
3. `./scripts/build-release-zip.sh` → `dist/cooper-bold-logo-wrangler-*.zip`
4. Plugin Check (above) on wp-env or staging
5. Tag `v*` and push — see **WordPress.org submission** below

**WordPress.org submission:** Full checklist in `docs/WORDPRESS-ORG-SUBMISSION.md` (plugin version **1.5.0** on `main`; WP.org last received 1.3.7). SVN secrets, directory assets. Wiki mirror: `wiki/guides/wordpress-org-submission.md`.

---

## Conventions

- Class prefix: `COOPBOLO_*` (functions `coopbolo_*`)
- Hooks: minimal — `plugins_loaded`, `init` for block
- SimpleMem namespace: `cooper-bold-logo-wrangler-wp-plugin`
- GitHub remote: `https://github.com/CooperBold/cooper-bold-logo-wrangler` (user-facing plugin name: **Cooper Bold Logo Wrangler**)

---

## Commands

```bash
npm install
npm run build
npm run start
npm run lint:js

python3 simplemem_cli.py import-ai-session --path AI_SESSION_MEMORY.md

# Plan multi-step work with the global loops (plan-and-implement; say "use the loops")
```

---

## AI bootstrap (2026-06-12)

SimpleMem, wiki, Context7, Cursor planning, AGENTS.md added. No legacy `docs/*.md` to ingest. Obsidian already on macOS.

---

## Where to look

| Need | Location |
| --- | --- |
| Agent rules | AGENTS.md |
| Block schema | `src/block/block.json` |
| Render pipeline | `class-coopbolo-renderer.php` |
| Context7 IDs | `.cursor/context7-libraries.md` |
| Session log | AI_SESSION_MEMORY.md |
