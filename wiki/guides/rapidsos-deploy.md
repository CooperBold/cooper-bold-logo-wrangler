---
title: RapidSOS deploy (Cooper Bold Logo Wrangler)
type: guide
summary: Build the ZIP, install on RapidSOS Cloudways staging, wire collections into Bricks (strip or carousel), purge caches, roll back.
created: 2026-06-17
updated: 2026-11-08
last_verified: 2026-11-08
tags: [wordpress, deploy, cloudways, bricks, shortcode]
aliases: [rapidsos-deploy]
sources: [docs/RAPIDSOS-DEPLOY.md, includes/class-coopbolo-renderer.php, "commit a5bc12b"]
status: active
---

# RapidSOS deploy (Cooper Bold Logo Wrangler)

Operational runbook for deploying **Cooper Bold Logo Wrangler** to the RapidSOS Cloudways staging site (`https://wordpress-1533060-6135168.cloudwaysapps.com/`) which uses the **Bricks** builder rather than Gutenberg. Source: `docs/RAPIDSOS-DEPLOY.md`.

## Build release ZIP

```bash
npm ci && npm run build && ./scripts/build-release-zip.sh
```

Output: `dist/cooper-bold-logo-wrangler-<version>.zip`. The version follows `cooper-bold-logo-wrangler.php`.

## Install on Cloudways

Pick one:

1. **WP Admin** — Plugins → Add New → Upload Plugin → choose the ZIP → Replace/update if prompted.
2. **SFTP** — Upload ZIP to server, unzip into `wp-content/plugins/cooper-bold-logo-wrangler/` (overwrite existing).
3. **SSH** — `wp plugin install /path/to/cooper-bold-logo-wrangler-<version>.zip --activate` (if WP-CLI available).

Activate **Cooper Bold Logo Wrangler** if not already active.

> **Coming from 1.2.x (`cooper-bold-logo-soup` folder)?** This is not an in-place update. The ZIP installs a second folder. Deactivate the old plugin first, and expect the REST route and meta-key changes listed in [[rapidsos-site]]. Try it on a local clone (`wp-fleet local`) before staging, and never touch live without Nicholas's go-ahead.

## Logo Collections (primary workflow on Bricks)

RapidSOS staging uses **Bricks**, not Gutenberg. Manage logos in wp-admin instead of hand-built shortcode URLs:

1. **Logo Wrangler → Add New** — name the collection (e.g. "Law Enforcement Partners").
2. Add logos via **Add / edit logos** (Media Library), set alt text and optional links, drag to reorder.
3. Tune **Collection Settings** — set **Layout** to **Carousel** for Splide sliders, or **Strip** for a single normalized row.
4. **Publish** and copy the shortcode from the sidebar or **Logo Wrangler → All Collections** list.
5. In Bricks, add a **Shortcode** element.

Typical page: `/commercial-building-safety/`. **Do not** remove or conflate the existing **Splide marquee** on that page; Logo Wrangler is a separate logo strip.

## Bricks carousel (law-enforcement and similar pages)

RapidSOS logo bands use **Bricks Nested Slider** (Splide) with class `logo-slider-slide` (Auto Scroll integration).

**Problem:** a default shortcode renders **one** wrapper with all logos inside → Bricks treats the whole strip as **one** carousel slide.

**Solution — carousel layout + slides wrapper:**

```text
[logo_wrangler collection="law-enforcement-partners" layout="carousel" wrapper="slides"]
```

Markup output: hidden reference strip (cross-logo normalization) + one `<li class="splide__slide logo-slider-slide cb-lw-slide">` per logo. Add the Shortcode element as a **direct child of the slider** (or use a **Code** element at the `.splide__list` level if Bricks wraps it in an extra div).

For a standalone carousel (full Splide markup `.cb-lw-carousel.splide`, initialized by `view.js` when the theme loads Splide):

```text
[logo_wrangler collection="your-slug" layout="carousel"]
```

Verify: multiple `splide__slide` elements with `data-cb-lw-slide`, hidden `data-cb-lw-ref` present, one logo image per slide after hydration, and the `rapidsos-logo-slider-fix` footer snippet active.

## Strip layout (single normalized row)

```text
[logo_wrangler collection="rapidsos-partners" base_size="40" gap="32" class="rapidsos-partner-logos"]
```

### Builder preview (strip)

The Bricks builder iframe often does **not** run `view.js`, so the strip shows **server-rendered** placeholder markup. As of **1.2.1**, the SSR output mirrors the hydrated DOM: `.cb-lw-inner` → inner `div` (LogoSoup row) → `span` per logo → `img` (or `a` > `img` when linked). Target **`.cb-lw-inner > div > span`** in Bricks custom CSS for grid layout — works in builder preview and on the live site, before and after hydration.

## Legacy inline logos (shortcode one-offs)

Inline `logos="url|alt,..."` shortcodes still work for one-offs, but collections are preferred. The legacy aliases `[balanced_logos]`, `[logo_soup]`, and `[cooper-bold-logo-soup]` **were removed in 1.4.0** — only `[logo_wrangler]` registers now, so any content still using an old tag renders nothing. See [[0008-remove-old-product-names]].

## Verify after deploy

1. Load the target page (hard refresh or incognito).
2. View page source and confirm:
   - `data-cb-lw` on the mount (`.cb-lw-inner`)
   - Script handle `cooper-bold-logo-wrangler-view` → `build/view.js`
3. Logos render as a normalized strip (not a marquee).
4. After changing a collection, purge cache and hard-refresh — blocks/shortcodes load collection data at render time.

## Purge cache

After any plugin or shortcode change, purge **all** layers:

- Cloudways (Varnish / application cache)
- **Breeze** (if enabled)
- **Bricks** → Settings → Performance → regenerate CSS/cache

Re-check page source if the strip is missing or stale.

## Rollback checklist

**From 1.3.5 back to 1.3.4.** 1.3.5 renamed the collections post type, so this takes more than swapping the folder. Code and database must match. On Cloudways, push files and database together, and never push a 1.3.5 database onto 1.3.4 code: every logo strip would render empty. Run these in order on the site (tested in wp-env 2026-10-22, [[0007-coopbolo-prefix]]):

```bash
# 1. Post type back to the 1.3.4 name.
wp db query "UPDATE $(wp db prefix)posts SET post_type='cb_logo_collection' WHERE post_type='coopbolo_collection'"
# 2. Copy current logos/settings back to the 1.3.4 keys (covers collections edited under 1.3.5).
wp eval 'foreach ( get_posts( array( "post_type" => "cb_logo_collection", "post_status" => array_keys( get_post_stati() ), "posts_per_page" => -1, "fields" => "ids" ) ) as $id ) { foreach ( array( "logos", "settings" ) as $k ) { $v = get_post_meta( $id, "_coopbolo_$k", true ); if ( is_array( $v ) ) { update_post_meta( $id, "_cb_lw_$k", wp_slash( $v ) ); } } }'
# 3. Put 1.3.4 back.
wp plugin install /path/to/cooper-bold-logo-wrangler-1.3.4.zip --force
# 4. Only now (1.3.4 is running): clear the flag so a later 1.3.5 upgrade migrates again.
wp option delete coopbolo_schema_version
wp cache flush
```

Edits made under 1.3.4 after a rollback are kept on the next upgrade only if the collection has no `_coopbolo_*` meta yet; otherwise 1.3.5 keeps its own copy. Re-save those collections after upgrading again.

- [ ] Deactivate plugin or restore previous plugin folder from backup/ZIP
- [ ] Purge Cloudways, Breeze, and Bricks cache again
- [ ] Confirm page source no longer shows `data-cb-lw` (or shows expected prior version)
- [ ] Confirm Splide marquee on `commercial-building-safety` still works unchanged

## Related

- [[rapidsos-site]] · [[carousel-and-splide]]
- Bricks rules for this site: `~/.claude/rules/cooper-bold-wordpress.md`
- [[wordpress-plugin]]
- [[wp-env-smoke-test]]
- [[wordpress-org-submission]]
