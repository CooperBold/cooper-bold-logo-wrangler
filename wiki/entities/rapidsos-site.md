---
title: RapidSOS site (first consumer)
type: entity
summary: The RapidSOS Bricks site that the plugin was built for, which version it runs, and its site-side tooling.
status: active
created: 2026-10-19
updated: 2026-11-08
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

## Upgrading the site from 1.2.x to 1.3.x

1.3.0 changed the folder, main file, text domain, REST namespace, and meta keys ([[0003-plugin-name-cooper-bold-logo-wrangler]]). Through 1.3.x the plugin kept working for old content: legacy shortcodes (`[logo_soup]`), legacy blocks, the CPT, and legacy meta all still worked ([[logo-collections]]). **Since 1.4.0 the legacy shortcodes and blocks are gone** ([[0008-remove-old-product-names]]), so content using them must move to `[logo_wrangler]` first. Four things do not carry over:

1. **No loader exists for `cooper-bold-logo-soup.php`.** `balanced-logos.php` only covered the short-lived Balanced Logos path, and that loader was deleted in 1.4.0. Uploading the 1.3.x ZIP creates a second plugin folder, `cooper-bold-logo-wrangler/`. Deactivate the old plugin and activate the new one by hand.
2. **REST route moved** from `cb-logo-soup/v1/collections` to `cb-lw/v1/collections` (1.3.0), then `coopbolo/v1/collections` (1.3.5). RapidSOS's `populate-collection.py` uses the old route.
3. **Meta keys:** the RapidSOS populate snippets write `_cb_logo_soup_logos`. After a collection is saved in 1.3.x, that key is deleted and ignored. 1.3.5 also renames the CPT to `coopbolo_collection` and moves `_cb_lw_*` meta on first load; outside snippets that query `cb_logo_collection` find nothing after that.
4. Purge Cloudways, Breeze, and Bricks caches after the switch ([[rapidsos-deploy]]).

Test on a local clone first (`wp-fleet local` / `wp-site`). Never upgrade the live site without Nicholas's go-ahead.

## Related

- [[rapidsos-deploy]] · [[carousel-and-splide]] · [[overview]]
