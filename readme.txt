=== Cooper Bold Logo Wrangler ===
Contributors: nicholaseymann
Tags: logo, logos, partners, brands, block
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.5.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display partner and client logos in a balanced, harmonious strip.

== Description ==

Cooper Bold Logo Wrangler is a WordPress plugin that wraps the open-source [logo-soup](https://github.com/sanity-labs/logo-soup) library from Sanity Labs. It analyzes each logo image and normalizes visual weight, density, and alignment so mixed brand assets look intentional together — not a scrolling marquee.

**Features**

* Gutenberg block with live editor preview
* **Logo Collections** admin UI — build reusable logo sets with Media Library picker
* `[logo_wrangler]` shortcode for classic content areas
* Reference collections by slug (`collection="homepage-partners"`) or ID (`id="123"`)
* Media library integration for logo selection
* Tunable normalization controls (size, density, contrast, alignment)
* Frontend assets load only when the block or shortcode is present on the page

**Credits**

Logo normalization powered by [@sanity-labs/logo-soup](https://www.npmjs.com/package/@sanity-labs/logo-soup) (MIT). Built by [Cooper Bold](https://cooperbold.com). The WordPress plugin is GPL-2.0-or-later.

== Source code ==

Plugin source (including the unminified JavaScript that compiles to `build/`) is public:

https://github.com/CooperBold/cooper-bold-logo-wrangler

This ZIP also includes that source:

* `src/block/index.js` and `src/block/edit.js` → `build/index.js`
* `src/view.js` and `src/shared/mount-soup-dom.js` → `build/view.js`
* `src/admin/collection-preview.js` → `build/collection-preview.js`

Rebuild compiled assets with `@wordpress/scripts`:

`npm install && npm run build`

== Third-party libraries ==

* [@sanity-labs/logo-soup](https://github.com/sanity-labs/logo-soup) (MIT) — bundled into `build/` via the compile above
* [Splide](https://github.com/Splidejs/splide) 4.1.4 (MIT) — `lib/splide/`
* [@splidejs/splide-extension-auto-scroll](https://www.npmjs.com/package/@splidejs/splide-extension-auto-scroll) 0.5.3 (MIT) — latest tagged npm release (`lib/splide/js/splide-extension-auto-scroll.min.js`). Upstream GitHub has no newer tag; WP.org's library scanner compares against an unreleased branch.

== Installation ==

1. Upload the `cooper-bold-logo-wrangler` folder to `/wp-content/plugins/`, or install the ZIP from the WordPress plugin directory.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Add the **Cooper Bold Logo Wrangler** block in the block editor, or use the `[logo_wrangler]` shortcode.

== Frequently Asked Questions ==

= Does this animate logos in a marquee? =

No. Cooper Bold Logo Wrangler normalizes logo sizing and alignment in a static strip.

= Which shortcode should I use? =

Use the `[logo_wrangler]` shortcode. Pipe-delimited example:

`[logo_wrangler logos="/wp-content/uploads/acme.svg|Acme Corp" gap="28" base_size="48"]`

For many logos, use comma-separated chunks: `url|alt|link,url2|alt2`. Raw JSON cannot be used in shortcode attributes (WordPress treats `]` as the end of the tag); use the `base64:` prefix instead: `logos="base64:W3sidXJsIjoiLi4uIn1d"`.

= Does this work with page builders? =

Yes. Use the block in the block editor, or paste the shortcode into any shortcode-capable area (classic editor, widgets, builder HTML modules). For Bricks and similar builders, create a collection under **Logo Wrangler** in wp-admin, then paste the shortcode snippet from the collection list.

= How do Logo Collections work? =

Go to **Logo Wrangler → Add New** in wp-admin. Name the collection, add logos from the Media Library, set normalization options, and publish. Use `[logo_wrangler collection="your-slug"]` or pick the collection in the block sidebar. Inline `logos` shortcodes still work for one-off strips.

= How many logos can I add? =

There is no built-in limit. Add as many logos as your collection or block needs; very large sets may affect page load time.

== Screenshots ==

1. Block editor — select logos from the media library and tune normalization in the sidebar.
2. Frontend — normalized logo strip after Cooper Bold Logo Wrangler scales and aligns each brand mark.

== Changelog ==

= 1.5.0 =
* The frontend script no longer uses React. Pages that show a logo strip or carousel stop loading WordPress's React scripts (about 53 KB gzipped)
* The rendered markup is unchanged (`.cb-lw-inner > div > span > img`), so existing CSS keeps working
* Fix: a logo's link stays on the right logo when an earlier logo fails to load
* The block editor and the collection preview still use React, which WordPress already loads there

= 1.4.0 =
* Remove the old product names: the `[balanced_logos]`, `[logo_soup]` and `[cooper-bold-logo-soup]` shortcodes, the `cooper-bold/balanced-logos` and `cooper-bold/logo-soup` blocks, and the `balanced-logos.php` loader. Use `[logo_wrangler]` and the Logo Wrangler block
* The plugin no longer touches the `active_plugins` option
* Remove the duplicate `cb-balanced-logos-*` CSS classes and `data-cb-balanced-logos-*` attributes; `cb-lw-*` and `data-cb-lw*` are unchanged
* Stop reading the oldest collection meta keys (`_cb_balanced_logos_*`, `_cb_logo_soup_*`). Collections saved by 1.3.x still migrate automatically
* Prefix the JavaScript event names and the debug localStorage key with `coopbolo-`

= 1.3.7 =
* Load the Splide Auto Scroll extension only once: when a theme or another plugin already loads it, the bundled copy is skipped

= 1.3.6 =
* Replace `str_contains()` with `strpos()` in the Splide Auto Scroll duplicate check, so standalone carousels render on PHP 7.4 without relying on WordPress's PHP 8 polyfill
* Add a test that fails if PHP 8-only string or array functions are added to the plugin code

= 1.3.5 =
* Use the `coopbolo_` / `COOPBOLO_` prefix for every PHP function, class, constant, the Logo Collections post type (`coopbolo_collection`), meta keys, the schema option, the collection form nonce and fields, list columns, and the REST route (`coopbolo/v1`)
* Existing collections move from `cb_logo_collection` to `coopbolo_collection` once, on the first page load after updating. Their `_cb_lw_*` meta is copied to the new keys and kept until the collection is next saved. Older meta keys are still read as a fallback
* Admin script and style handles now use `cooper-bold-logo-wrangler-*`
* Frontend debug logging is now `window.COOPBOLO_DEBUG` or `?coopbolo_debug=1`
* Shortcodes, blocks, and frontend CSS classes (`.cb-lw-wrapper`, `.cb-lw-inner`) are unchanged

= 1.3.4 =
* Use the plugin's `cb_lw_` prefix for the admin-screen helper, the collection form nonce, and the collections list columns
* Register the legacy `[balanced_logos]`, `[logo_soup]` and `[cooper-bold-logo-soup]` shortcodes only when no other plugin already uses those names

= 1.3.3 =
* Migrate legacy `balanced-logos.php` active_plugins entries on **activate** and **plugin update** only (not on `plugins_loaded`)
* On deactivate, remove both canonical and legacy bootstrap paths so the plugin fully stops
* Hide legacy block aliases from the block inserter while keeping editor support for existing content

= 1.3.2 =
* Bump **Tested up to** to 7.1 for WordPress.org automated checks

= 1.3.1 =
* Ship `src/` and `package.json` in the plugin ZIP and document the public GitHub repo so compiled `build/*.js` files can be regenerated
* Stop rewriting the `active_plugins` option on load. Legacy `balanced-logos.php` still loads the plugin if that path remains active; it does not change activation state
* Clarify that Splide Auto Scroll 0.5.3 is the current npm stable (see Third-party libraries)

= 1.3.0 =
* Rename plugin to **Cooper Bold Logo Wrangler** — display name, folder slug (`cooper-bold-logo-wrangler`), text domain, block (`cooper-bold/logo-wrangler`), shortcode (`[logo_wrangler]`), CSS/data attributes, and PHP identifiers
* Legacy support: `[balanced_logos]`, `[logo_soup]`, `[cooper-bold-logo-soup]`; blocks `cooper-bold/balanced-logos` and `cooper-bold/logo-soup`; CPT `cb_logo_collection`; meta keys `_cb_balanced_logos_*` and `_cb_logo_soup_*`
* Document that `splide-extension-auto-scroll@0.5.3` is the latest published npm release (the upstream repo's last commit was 2022-09-06); WP.org's `out of date libraries` check is comparing against a development branch with no new tagged release

= 1.2.13 =
* Plugin Check: sanitize collection editor POST arrays; prefix admin screen helper for PHPCS

= 1.2.12 =
* Vendor Splide core and Auto Scroll extension locally for standalone carousels (no external CDN scripts)
* WordPress.org directory assets and readme polish for plugin review

= 1.2.11 =
* Mount carousel Splide immediately after logo nodes are distributed — no longer wait for every slide image decode (fixes multi-second scroll delay on large collections)
* Reduce hydration fail-open timeout from 4s to 1.5s (safety net when LogoSoup render is slow)

= 1.2.10 =
* Opt-in frontend debug logging for carousel hydration (`CB_LW_DEBUG`, `?cb_lw_debug=1`, or localStorage)

= 1.2.9 =
* Standalone Splide fallback when a theme autoscroll snippet is absent or fails to mount after hydration
* Snippet deferral now requires the live footer autoscroll script, not only a global flag
* Carousel shows hydrated slide images while Splide initializes (`cb-lw-hydrating` visibility override)
* Accept LogoSoup blob: images in slide load checks; 4s fail-open completes hydration when image probes stall
* Fix logo row lookup during carousel distribute (use LogoSoup root child, not `[class*="logo-soup"]` descendant search)

= 1.2.8 =
* Standalone carousel: plugin owns Splide + Auto Scroll init after logo images load (no custom snippet required)
* Wait for slide images before first Splide mount — prevents empty mount and scroll jerk
* Enqueue Splide Auto Scroll extension for wrapper=full carousels
* Viewport-based autoscroll speed; defer init when a theme autoscroll snippet is active on legacy pages

= 1.2.7 =
* Standalone carousel hydration no longer calls `splide.refresh()` when a theme autoscroll snippet is present — avoids mid-scroll reposition jerk

= 1.2.6 =
* Standalone carousel hydration defers Splide mount when a theme autoscroll snippet is present; snippet owns init to avoid destroy/remount visual jump

= 1.2.5 =
* Fix carousel hydration and Splide clone handling on the frontend


= 1.2.4 =
* Fix standalone carousel Splide enqueue when Bricks registers an empty `splide` script handle (load bundled Splide 4.1.4 instead)
* Detect `[logo_soup]` shortcodes in Bricks `_bricks_page_content_2` meta for early asset enqueue

= 1.2.3 =
* Standalone carousel layout (`wrapper="full"`) enqueues Splide 4.1.4 core CSS/JS before view.js when Bricks/theme Splide is absent
* Conditional detection for block, shortcode, and collection-backed carousel markup

= 1.2.2 =
* Docs-only: professionalize plugin code comments (no behavior changes)

= 1.2.1 =
* Strip layout SSR placeholders mirror LogoSoup hydrated DOM (`div` > `span` > `img`) so Bricks builder preview matches frontend grid CSS without running view.js
* Bricks CSS hint: target `.cb-lw-inner > div > span` for strip layout

= 1.2.0 =
* Carousel layout mode — one normalized logo per Splide slide for nested sliders
* Collection setting and block/shortcode `layout` attribute (`strip` default, `carousel` for sliders)
* Shortcode `wrapper="slides"` outputs slide fragments for nesting inside existing Splide lists; `wrapper="full"` outputs a standalone carousel
* `logo-slider-slide` class on carousel slides for Splide Auto Scroll compatibility

= 1.1.12 =
* One-line help text under each collection and block setting explaining what it does
* Stable BEM wrapper classes for frontend CSS: `.cb-lw-wrapper` (outer) and `.cb-lw-inner` (logo row / hydration mount)

= 1.1.11 =
* Fix collection admin Live Preview — read density-aware and crop settings from the advanced settings table so preview matches frontend output
* Match preview strip layout to frontend (full-width scroll, shared CSS variables for size/gap/background)

= 1.1.10 =
* Replace wp-admin footer wordmark with plain CooperBold text link on Logo Collections screens
* Match block editor sidebar credit to plain CooperBold text
* Clear wp-admin right footer (theme/plugin update nags) on Logo Collections screens

= 1.1.9 =
* Release tag for Cooper Bold wordmark in wp-admin left footer on Logo Collections screens via `admin_footer_text` (removes redundant meta box text credit from 1.1.8)

= 1.1.8 =
* Replace wp-admin left footer text with Cooper Bold wordmark on Logo Collections screens via `admin_footer_text` (no overlap with version nags)

= 1.1.7 =
* Fix Cooper Bold admin footer credit — small wordmark bottom-right on Logo Collections screens only, no overlap with core wp-admin footer

= 1.1.6 =
* Add Cooper Bold wordmark footer on Logo Collections admin list and edit screens
* Subtle cooperbold.com credit in the block editor sidebar

= 1.1.5 =
* Remove the 50-logo cap from collections, block editor, and frontend rendering

= 1.1.4 =
* Fix duplicate **All Collections** entry in the wp-admin Logo Soup menu

= 1.1.3 =
* User-facing plugin name is now **Cooper Bold Logo Wrangler** (folder slug, text domain, and block name unchanged)

= 1.1.2 =
* Simplified Logo Collections admin UI — sparse settings, single primary shortcode with discreet ID alternative, compact list-table copy

= 1.1.1 =
* Live Cooper Bold Logo Wrangler preview in the collection admin editor (updates as logos and settings change)
* One-click **Copy** buttons for shortcodes on the collection edit screen and All Collections list

= 1.1.0 =
* Logo Collections admin UI (custom post type) with Media Library picker, reorder, alt text, and link URLs
* Top-level **Cooper Bold Logo Wrangler** admin menu with collection list and copy-paste shortcode snippets
* Block sidebar collection dropdown; shortcode `collection` and `id` attributes
* PHPUnit coverage for collection resolution

= 1.0.1 =
* PHPUnit tests for renderer sanitization (logos, colors, densityFactor parity)
* Jest tests for editor preview config (`sanitizePreviewConfig` / `toLwProps`)

= 1.0.0 =
* Initial release with Gutenberg block and shortcodes
* Conditional frontend asset loading
* Sanitized shortcode and block attributes

== Upgrade Notice ==

= 1.5.0 =
Lighter pages: the frontend no longer loads React. Markup and CSS classes are unchanged.

= 1.4.0 =
Removes old shortcode, block and CSS-class aliases. If your content still uses [logo_soup] or [balanced_logos], change it to [logo_wrangler] before updating.

= 1.3.7 =
Avoids loading the carousel Auto Scroll script twice on themes that already include it.

= 1.3.6 =
PHP 7.4 compatibility fix for standalone carousels.

= 1.3.5 =
Renames internal names to the `coopbolo_` prefix. Existing logo collections move over automatically; shortcodes and CSS classes are unchanged.

= 1.3.4 =
Consistent `cb_lw_` naming for WordPress.org review. No changes to content or settings.

= 1.3.3 =
Fixes legacy bootstrap activation/deactivation for upgraded installs.

= 1.3.2 =
Readme only — **Tested up to** 7.1 for directory submission.

= 1.3.1 =
WordPress.org review: includes JavaScript source, no longer rewrites active plugins, documents Splide Auto Scroll 0.5.3.

= 1.2.14 =
Display name only — no migration required.

= 1.2.13 =
Plugin Check compliance — no behavior change for collections or frontend output.

= 1.2.12 =
Vendors Splide locally for carousel mode — recommended before WordPress.org directory release.

= 1.1.12 =
Adds setting descriptions and stable wrapper classes for theme CSS — no migration required.

= 1.1.11 =
Fixes collection Live Preview not matching the frontend strip — no migration required.

= 1.1.10 =
Plain CooperBold footer text on Logo Collections screens and in the block sidebar — no migration required.

= 1.1.9 =
Cooper Bold wordmark replaces the default wp-admin footer text on Logo Collections screens — no migration required.

= 1.1.8 =
Cooper Bold wordmark replaces the default wp-admin footer text on Logo Collections screens — no migration required.

= 1.1.7 =
Fixes oversized admin footer branding on Logo Collections screens — no migration required.

= 1.1.6 =
Adds a Cooper Bold wordmark in Logo Collections wp-admin and a credit link in the block sidebar — no migration required.

= 1.1.5 =
Removes the previous 50-logo limit — large collections now render in full.

= 1.1.4 =
Fixes a duplicate menu item under **Cooper Bold Logo Wrangler** in wp-admin.

= 1.1.3 =
Display name only — no migration required.

= 1.1.2 =
Cleaner Logo Collections admin — fewer visible options by default, streamlined shortcode copy.

= 1.1.1 =
Adds a live preview and one-click shortcode copy buttons when editing Logo Collections in wp-admin.

= 1.1.0 =
Adds Logo Collections — manage logos in wp-admin instead of hand-built shortcode URLs.

= 1.0.1 =
Adds automated test coverage; no user-facing behavior changes.

= 1.0.0 =
Initial public release.
