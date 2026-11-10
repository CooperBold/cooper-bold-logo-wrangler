---
title: Rendering pipeline (PHP render, JS hydration)
type: concept
summary: How a logo row goes from block/shortcode to a normalized strip, and why assets load only from PHP.
status: active
created: 2026-10-19
updated: 2026-11-10
last_verified: 2026-11-10
sources: [includes/class-coopbolo-renderer.php, includes/class-coopbolo-assets.php, src/view.js, src/shared/mount-soup-dom.js, src/shared/lw-sanitize.js, src/block/block.json, docs/SAR-REVIEW.md, AI_RUNBOOK.md, "commits 1878ed6, a5bc12b, 9501c8d, 403645d"]
tags: [architecture, hydration, enqueue, bricks]
---

# Rendering pipeline

PHP renders placeholder markup; `build/view.js` then mounts a normalized row on each mount node with the logo-soup engine and plain DOM (no React since 1.5.0, [[0009-react-free-frontend]]) and replaces it with the normalized row. The block and every shortcode share one renderer and one view script. This shape won the June 2026 SAR run ([[sar-review]], "Candidate C").

## Steps

1. **Render (PHP).** The block's `render_callback` and the shortcode handler both call `COOPBOLO_Renderer` (`includes/class-coopbolo-plugin.php`). It sanitizes attributes, resolves a collection if `collection`/`id` is set ([[logo-collections]]), and prints `.cb-lw-wrapper > .cb-lw-inner[data-cb-lw="<json>"]` with placeholder `<img>`s. The duplicate legacy `cb-balanced-logos*` classes and the `data-cb-balanced-logos` attribute were removed in 1.4.0; `cb-lw-*` and `data-cb-lw*` are unchanged because themes target them.
2. **Enqueue (PHP).** Rendering calls `COOPBOLO_Assets::enqueue_frontend()`. As an early pass, `maybe_enqueue()` on `wp_enqueue_scripts` checks the singular post with `has_block()` / `has_shortcode()` for `cooper-bold/logo-wrangler` and `logo_wrangler`, **plus the Bricks meta `_bricks_page_content_2`** (added in 1.2.4, `get_post_content_for_detection()`), so builder pages get assets in `<head>`.
3. **Hydrate (JS).** `src/view.js` finds `[data-cb-lw]`, parses the JSON, and mounts with `mountSoupDom()` (`src/shared/mount-soup-dom.js`). A `WeakMap` stops a node from mounting twice. Carousels then fire `coopbolo-hydrated` (bubbling `CustomEvent`; the old `cb-lw-hydrated` and `cb-balanced-logos-hydrated` names were replaced in 1.4.0).
4. **Normalize (library).** `logo-soup` loads each image into a canvas to measure it. That is why logo URLs must be same-origin or CORS-enabled (see [[gotchas]]). Lifecycle hooks from 1.3.0 clear the `cb-lw-soup-loading` class on success or failure ([[sanity-logo-soup]]).

## Why `block.json` has no `viewScript` or `style`

`block.json` declares only `editorScript` and `editorStyle`. Frontend JS/CSS is registered and enqueued by PHP so it loads only when something renders, and is never enqueued twice (once by core for the block, once by the shortcode path). The SAR attack round found the double-enqueue risk; `AGENTS.md` makes it a rule. Adding `viewScript` back breaks that rule. See [[0006-php-owned-asset-enqueue]].

## SSR markup matches the hydrated DOM

The Bricks builder canvas often does not run `view.js`. Since 1.2.1 (commit `1878ed6`), strip placeholders use the same shape LogoSoup produces after hydration: `.cb-lw-inner > div > span > img`. CSS aimed at `.cb-lw-inner > div > span` therefore works in the builder preview and on the live page. Changing the placeholder shape breaks designer CSS on RapidSOS ([[rapidsos-site]]).

## Editor and frontend parity

`src/shared/lw-sanitize.js` holds the shared JS sanitizers: `clamp`, `stripPlainText`, `sanitizeCssColor`, `resolveAlt`, `sanitizeLink`, `sanitizePreviewConfig`, and `toSoupOptions`. Since 1.5.0 the frontend imports `toSoupOptions` from it directly; `src/shared/to-lw-props.js` (`toLwProps`, `sanitizePreviewConfig`) re-exports the same helpers and stays React-only, for the block editor and the collection preview. PHP mirrors the same rules (named-color allowlist, clamps, `densityFactor` 0 when `densityAware` is off). The allowlists in PHP and JS must stay in sync ([[sar-review]] caveats; [[adversarial-review]]).

## Related

- [[carousel-and-splide]] · [[gutenberg-block-balanced-logos]] · [[wordpress-plugin]]
