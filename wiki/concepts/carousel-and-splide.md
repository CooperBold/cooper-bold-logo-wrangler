---
title: Carousel layout and Splide
type: concept
summary: How layout="carousel" works with Bricks Nested Sliders, the RapidSOS autoscroll snippet, and the bundled Splide.
status: active
created: 2026-10-19
updated: 2026-11-10
last_verified: 2026-11-10
sources: [includes/class-coopbolo-renderer.php, includes/class-coopbolo-assets.php, src/view.js, src/shared/mount-soup-dom.js, readme.txt, lib/splide/README.md, docs/RAPIDSOS-DEPLOY.md, "commits b65550b, 2ebd174, 096d507, 1f7112d, 403645d"]
tags: [carousel, splide, bricks, rapidsos]
---

# Carousel layout and Splide

`layout="carousel"` outputs one Splide slide per logo, so a logo band can scroll. It was built in one day (1.2.0 → 1.2.11, 2026-06-18/19) for RapidSOS's Bricks Nested Sliders. Most of the version churn was about who gets to start Splide. Sources: `readme.txt` changelog 1.2.0–1.2.11.

## The two wrapper modes

| `wrapper` | Output | Who mounts Splide |
|---|---|---|
| `slides` | A hidden reference strip plus bare `<li class="splide__slide logo-slider-slide cb-lw-slide">` items, to drop **inside** an existing Bricks Nested Slider | Bricks / the site's autoscroll snippet |
| `full` (default) | A complete `.cb-lw-carousel.splide` | The plugin, using bundled Splide unless the theme has it |

`wrapper` is ignored unless `layout` is `carousel` (`class-coopbolo-renderer.php` line ~125). The **hidden reference strip** (`data-cb-lw-ref`) is normalized once across all logos; `view.js` then distributes each measured logo into its slide. Normalizing slides one by one would lose the cross-logo balance.

Why one slide per logo: a plain shortcode inside a slider gives one wrapper holding every logo, which Bricks treats as **one** slide (`docs/RAPIDSOS-DEPLOY.md`, "Bricks carousel").

## The `logo-slider-slide` class and the RapidSOS snippet

Slides carry `logo-slider-slide` because RapidSOS's footer snippet `rapidsos-logo-slider-fix` (source: RapidSOS repo `deploy/wordpress/logo-slider-autoscroll-snippet.php`) starts Auto Scroll on sliders with that class. `view.js` checks for that snippet (`window.__rapidsosLogoSliderFix` **and** the `#rapidsos-logo-slider-fix-js` script element, `externalAutoscrollOwnsSplide()`). If both are present, the plugin defers to the snippet. If not, it mounts Splide itself, retrying at 0/100/500/1500/3000 ms (`STANDALONE_SPLIDE_RETRY_MS`). History:

- 1.2.6–1.2.7: defer to the snippet; never call `splide.refresh()` while it runs (it caused a mid-scroll jump).
- 1.2.8–1.2.9: the plugin owns init when there is no snippet; requiring the actual script element, not just the global flag, fixed false deferrals.
- 1.2.11: mount Splide right after logos are distributed instead of waiting for every image to decode (multi-second delay on big collections).

## Splide handle resolution

Bricks registers a `splide` script handle **with no src** on every page. Enqueuing that handle loads nothing. `COOPBOLO_Assets::resolve_splide_script_handle()` uses `splide` only if it has a src, and otherwise uses the plugin's own handles, `cooper-bold-logo-wrangler-splide` (+ `-autoscroll`), which point at `lib/splide/` (1.2.4 changelog; `class-coopbolo-assets.php` ~lines 149–210).

Splide is **vendored** in `lib/splide/` (core 4.1.4, auto-scroll 0.5.3). 1.2.11 and older loaded it from jsDelivr. WP.org rules out external CDN scripts, so 1.2.12 vendored it. The auto-scroll pin is explained in [[0001-pin-splide-extension-auto-scroll]].

## Auto Scroll loads only once (1.3.7)

If a theme or another plugin already loads the Auto Scroll extension, the plugin must not ship a second copy. `COOPBOLO_Assets::external_autoscroll_handle()` finds another registered handle **by `src`** (a theme can register the same file under any handle) and explicitly skips the plugin's own handle, whose src contains the same file name. When one is found, the plugin enqueues that handle instead of the bundled one and points `view.js` at it.

Because block themes render content before `wp_enqueue_scripts`, a theme that enqueues Auto Scroll late is not visible when the carousel renders. `reconcile_autoscroll()` settles it on `wp_print_footer_scripts` (priority 1): it dequeues the bundled copy and swaps the view script's dependency to the external handle. Commit `1f7112d`.

## PHP 7.4 compatibility (1.3.6)

The plugin declares `Requires PHP: 7.4`, and WordPress does not polyfill PHP 8 functions. The Auto Scroll duplicate check was `page_already_loads_autoscroll_extension()`, which used `str_contains()`; 1.3.6 changed it to `strpos()`, and 1.3.7 replaced the method with `external_autoscroll_handle()`. A guard test fails if a PHP 8-only string or array function is added to the plugin code. Commit `096d507`.

## Fail-open

If hydration stalls, a timer shows the slides anyway. The changelog says 4 s (1.2.9) then 1.5 s (1.2.11); the code now uses `HYDRATION_FAIL_OPEN_MS = 800` (`src/view.js`). Trust the code.

## Debugging

Turn on logs with `window.COOPBOLO_DEBUG = true`, `localStorage['coopbolo-debug']`, or `?coopbolo_debug=1` (1.2.10).

## Related

- [[rapidsos-site]] · [[rendering-pipeline]] · [[rapidsos-deploy]]
