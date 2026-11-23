---
title: RapidSOS site (first consumer)
type: entity
summary: The RapidSOS Bricks site that the plugin was built for, which version it runs, and its site-side tooling.
status: active
created: 2026-10-19
updated: 2026-11-23
last_verified: 2026-11-08
sources: [docs/RAPIDSOS-DEPLOY.md, "https://app.notion.com/p/37afb6901a688069a9ebef05fe326252", "RapidSOS repo wiki/sources/site-code-audit-2026-08-13.md", "RapidSOS repo deploy/wordpress/logo-soup/README.md", "commit a5bc12b"]
tags: [rapidsos, bricks, cloudways, client-site]
---

# RapidSOS site

RapidSOS is a Cooper Bold client WordPress site built with **Bricks** on Cloudways. The plugin was first built for its "software integrations" page. RapidSOS uses it through collections and the shortcode, not the Gutenberg block. Sources: Notion task; `docs/RAPIDSOS-DEPLOY.md`.

Before any Bricks or WPCode work on this site, read `~/.claude/rules/cooper-bold-wordpress.md`. Melanie owns layout. Use Bricks Style UI, not `_cssCustom` or pasted CSS.

## Facts

| Item | Value | Source |
|---|---|---|
| Staging | Cloudways app `wordpress-1533060-6135168.cloudwaysapps.com` | `docs/RAPIDSOS-DEPLOY.md` |
| Live | Gets staging changes through **Cloudways Push**, which copies the database, including active WPCode snippets | RapidSOS audit 2026-08-13 |
| Plugin version on staging | **1.2.11**, old slug `cooper-bold-logo-soup` (Splide from jsDelivr; ships `composer-setup.php`) | RapidSOS audit 2026-08-13 |
| Pages | `/public-safety/software-integrations/` (origin request), `/public-safety/law-enforcement/`, `/commercial-building-safety/` | Notion task; `docs/RAPIDSOS-DEPLOY.md` |
| Main collection | "trusted public safety software partners", 70 logos (filled by a WPCode snippet) | RapidSOS `deploy/wordpress/logo-soup/README.md` |
| Slider integration | Bricks Nested Slider + footer snippet `rapidsos-logo-slider-fix` + slide class `logo-slider-slide` | [[carousel-and-splide]] |

Whether staging or live has moved past 1.2.11 since 2026-08-13 is *(unverified)*: nobody checked the live sites for this wiki.

## Upgrading the site from 1.2.x

Tested 2026-11-23 on a local copy built from the Nov 3 staging data (1.2.11 → 1.5.2). A straight plugin swap breaks both logo sections, for three reasons:

1. **Shortcode.** Staging content uses `[logo_soup …]` (page 275212 and the "Logo Reel" Bricks component in `bricks_components`). 1.4.0+ registers only `[logo_wrangler]`, so the old tag shows as plain text ([[0008-remove-old-product-names]]).
2. **Logo lists.** 1.4.0–1.5.1 did not read the 1.2.x meta (`_cb_logo_soup_logos` / `_settings`), so collections rendered empty. **Fixed in 1.5.2:** those keys are copied on migration and read as a fallback.
3. **CSS class names.** Bricks custom CSS targets `.cb-logo-soup-inner` (the six-column grid on 275212) and `.cb-logo-soup-carousel.splide::before/::after` (the Logo Reel edge fade); the active WPCode snippet "Logo slider smooth scroll" checks `data-cb-logo-soup-splide`. The plugin now outputs `cb-lw-*`.

`docs/rapidsos-upgrade-from-1.2.sh` does all of it with WP-CLI (plugin swap, `[logo_soup ` → `[logo_wrangler ` and `cb-logo-soup` → `cb-lw` in posts/postmeta/options, `wp bricks regenerate_assets`, cache flush; `--dry-run` first: 15 + 10 replacements on the staging data). Purge Breeze/Varnish afterwards.

After the script, all 70 grid logos measured within 1 px of 1.2.11 at 1280 px, the 21 carousel logos match, Auto Scroll speed is the same (0.9 px/frame), the edge fade works, and mobile (375 px) shows two columns without overflow. Plugin Check: no errors.

Known, left alone: the site's WPCode "Header Tags" snippet loads Auto Scroll from jsDelivr on every page as a raw `<script>`, which the plugin cannot detect, so carousel pages load it twice (2.5 KB gzipped). That is site cleanup, not a plugin bug.

The REST route moved to `coopbolo/v1/collections`, so RapidSOS's `populate-collection.py` (old `cb-logo-soup/v1` route and `_cb_logo_soup_logos` key) is retired. Never upgrade staging or live without Nicholas's go-ahead.

## Related

- [[rapidsos-deploy]] · [[carousel-and-splide]] · [[overview]]
