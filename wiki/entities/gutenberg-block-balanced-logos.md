---
title: Logo Wrangler Gutenberg Block
type: entity
summary: The cooper-bold/logo-wrangler dynamic block (the only registered block since 1.4.0) and its frontend hydration hooks.
created: 2026-06-12
updated: 2026-11-03
last_verified: 2026-11-03
tags: [gutenberg, block-editor, logo-wrangler]
aliases: [cooper-bold/balanced-logos, cooper-bold/logo-wrangler, logo-wrangler-block]
status: active
sources: [src/block/block.json, src/block/index.js, src/view.js, includes/class-coopbolo-plugin.php, "commit 385fbad", "commit a5bc12b"]
---

# Logo Wrangler Gutenberg Block

Dynamic block registered from `src/block/block.json`.

## What it is

- **Name:** `cooper-bold/logo-wrangler` (the only registered block since 1.4.0; commit `a5bc12b`)
- **Legacy names:** `cooper-bold/balanced-logos` and `cooper-bold/logo-soup` were hidden from the inserter since v1.3.3 (commit `385fbad`) and **unregistered in 1.4.0** — existing posts containing them no longer resolve in the block library. See [[0008-remove-old-product-names]]
- **API version:** 3
- **Editor script:** `build/index.js`
- **Registration:** `COOPBOLO_Plugin::register_block()` on `init`

## How it's used in this project

Editor UI in `src/block/edit.js`. Server render via `render_callback` in `includes/class-coopbolo-plugin.php` delegating to `COOPBOLO_Renderer`. Shared prop sanitizer: `src/shared/to-lw-props.js` (`toLwProps`).

Frontend hydration (`src/view.js`) reads the `data-cb-lw` attribute (the legacy `data-cb-balanced-logos` fallback was removed in [[0008-remove-old-product-names]]) and fires a `coopbolo-hydrated` bubbling CustomEvent after a carousel hydrates; JS event names and the debug localStorage key (`coopbolo-debug`) carry the `coopbolo-` prefix since `fix/js-hook-prefix` (commit `9501c8d`), which dropped the duplicate `cb-balanced-logos-hydrated`. Debug opt-in: `window.COOPBOLO_DEBUG`, `localStorage['coopbolo-debug']`, or `?coopbolo_debug=1`.

## Related

- [[rendering-pipeline]] · [[logo-collections]]
- [[wordpress-plugin]]
- [[sanity-logo-soup]]
- [[0002-migrate-legacy-plugin-path-activate-only]]
