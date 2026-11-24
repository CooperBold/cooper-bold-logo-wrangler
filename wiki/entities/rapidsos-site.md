---
title: RapidSOS site (first consumer)
type: entity
summary: The RapidSOS Bricks site that the plugin was built for, which version it runs, and its site-side tooling.
status: active
created: 2026-10-19
updated: 2026-11-24
last_verified: 2026-11-24
sources: [docs/RAPIDSOS-DEPLOY.md, "https://app.notion.com/p/37afb6901a688069a9ebef05fe326252", "RapidSOS repo wiki/sources/site-code-audit-2026-08-13.md", "RapidSOS repo deploy/wordpress/logo-soup/README.md", "commit a5bc12b", docs/rapidsos-upgrade-from-1.2.sh, "branch fix/migrate-1-2-meta", "branch fix/legacy-shortcode-classes", "branch fix/move-1-2-compat-to-site", "RapidSOS repo deploy/wordpress/logo-wrangler/logo-soup-compat-snippet.php"]
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

**Current state (2026-11-24):** RapidSOS staging runs Logo Wrangler with its 1.2.x content unchanged. The old names live on the site, not in the plugin: a WPCode PHP snippet ("Run Everywhere", source in the RapidSOS repo at `deploy/wordpress/logo-wrangler/logo-soup-compat-snippet.php`) registers `[logo_soup]` as `[logo_wrangler]` and gives every `cb-lw*` class a `cb-logo-soup*` twin plus `data-cb-logo-soup-splide`. Bricks CSS targets `.cb-logo-soup-inner` (six-column grid on 275212) and `.cb-logo-soup-carousel.splide::before/::after` (Logo Reel edge fade).

History: 1.5.3 shipped that compatibility inside the plugin (only for sites whose collections came from 1.2.x) and staging was swapped to it on 2026-11-24. 1.5.4 removed it again so the public plugin carries no old product names ([[0008-remove-old-product-names]]); the snippet took over first, then staging went to 1.5.4.

Still in the plugin: the 1.2.x meta migration and read fallback (1.5.2). Collections move to `coopbolo_collection`, so going back to 1.2.11 needs the post type moved back; saving a collection in wp-admin drops its 1.2.x meta.

Other 1.2.x sites: install the plugin, then either add the same snippet or rename the content with `docs/rapidsos-upgrade-from-1.2.sh`.

Known, left alone: the site's WPCode "Header Tags" snippet loads Auto Scroll from jsDelivr on every page as a raw `<script>`, which the plugin cannot detect, so carousel pages load it twice (2.5 KB gzipped). That is site cleanup, not a plugin bug.

The REST route moved to `coopbolo/v1/collections`, so RapidSOS's `populate-collection.py` (old `cb-logo-soup/v1` route and `_cb_logo_soup_logos` key) is retired. Never upgrade staging or live without Nicholas's go-ahead.

## Related

- [[rapidsos-deploy]] · [[carousel-and-splide]] · [[overview]]
