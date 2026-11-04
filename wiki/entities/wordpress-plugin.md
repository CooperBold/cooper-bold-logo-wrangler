---
title: Cooper Bold Logo Wrangler Plugin
type: entity
summary: The plugin itself: identifiers, version, PHP classes per surface, and bootstrap behavior.
created: 2026-06-12
updated: 2026-11-03
last_verified: 2026-11-03
tags: [wordpress, plugin, logo-soup, cooper-bold, logo-wrangler]
aliases: [logo-soup-plugin, balanced-logos, logo-wrangler, cooper-bold-logo-wrangler]
status: active
sources: [cooper-bold-logo-wrangler.php, includes/, package.json, readme.txt, "commit a5bc12b"]
---

# Cooper Bold Logo Wrangler Plugin

WordPress plugin in this repository for displaying normalized logo strips on the front end. Earlier names, oldest first: Cooper Bold Logo Soup, Logo Soup, Logo Soup by Cooper Bold, Balanced Logos ([[0003-plugin-name-cooper-bold-logo-wrangler]]).

## What it is

- **Main file:** `cooper-bold-logo-wrangler.php`
- **Package:** `CooperBoldLogoWrangler` / prefix `COOPBOLO_*` and `coopbolo_*` since 1.3.5 (`CB_LW_*` / `cb_lw_*` before; see [[0007-coopbolo-prefix]])
- **Version:** **1.4.0** on `main` (`COOPBOLO_VERSION`, `Version` header, `package.json`, `block.json`, `readme.txt` Stable tag; `feat!: remove old product names and the legacy loader (1.4.0)`, commit `a5bc12b`, 2026-10-30). WP.org has **1.3.7** (uploaded 2026-10-29); 1.4.0 is built but not uploaded ([[wporg-review-thread]], [[0007-coopbolo-prefix]], [[0008-remove-old-product-names]])
- **Text domain:** `cooper-bold-logo-wrangler`
- **Upstream:** Wraps `@sanity-labs/logo-soup` npm package
- **Release ZIP:** now ships `src/` + `package.json` alongside compiled `build/` (since 1.3.1) so WP.org reviewers can regenerate assets via `@wordpress/scripts` ([[0004-ship-source-in-release-zip]])

## How it's used in this project

| Surface | Identifier | PHP class |
| --- | --- | --- |
| Block | `cooper-bold/logo-wrangler` | `COOPBOLO_Plugin` (register + render) |
| Shortcode | `logo_wrangler` (the only one since 1.4.0) | `COOPBOLO_Plugin` |
| Render | shared | `COOPBOLO_Renderer` |
| Assets | `build/view.js` | `COOPBOLO_Assets` |
| Collections CPT | `coopbolo_collection` (was `cb_logo_collection` through 1.3.4) | `COOPBOLO_Collections` |

Bootstrap: `plugins_loaded` → `COOPBOLO_Plugin::instance()`. The legacy `balanced-logos.php` loader and the `active_plugins` activate/update migration are **gone since 1.4.0** (commit `a5bc12b`) — the plugin no longer reads or writes `active_plugins` at all; a site with the old loader path still listed there simply has its missing file skipped, and the deactivate hook now only clears the collections `coopbolo_schema_version` option. History and rationale: [[0002-migrate-legacy-plugin-path-activate-only]] (superseded) and [[0008-remove-old-product-names]].

## Key details

- Block metadata: `src/block/block.json`
- Build output: `build/` via `npm run build`
- Repo remote: `github.com/CooperBold/cooper-bold-logo-wrangler` (renamed from `cooper-bold-logo-soup` on 2026-10-30, commit `3b97d49`; the old URL redirects)
- Frontend CSS: `.cb-lw-wrapper` / `.cb-lw-inner` (the duplicated legacy `.cb-balanced-logos-*` classes were removed in 1.4.0; `cb-lw-*` is deliberate, themes target it)

## Related

- [[overview]] · [[rendering-pipeline]] · [[logo-collections]] · [[carousel-and-splide]]
- [[sanity-logo-soup]]
- [[gutenberg-block-balanced-logos]]
