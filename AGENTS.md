# Cooper Bold Logo Wrangler — Agent Notes

Claude Code loads this file through `.claude/CLAUDE.md`. Always-on rules are in `.claude/rules/`; before non-trivial work follow `.claude/rules/pre-task-retrieval.md`.

**Project wiki:** read `wiki/index.md` before non-trivial work; open pages it points to only when relevant.
After a repeated failure, check `wiki/gotchas.md`. Writing to the wiki: follow `wiki/SCHEMA.md`.

## What this plugin does

Wraps `@sanity-labs/logo-soup` for WordPress. **Cooper Bold Logo Wrangler** normalizes mixed logo images (size, density, visual center) into a balanced strip. It is not a marquee.

## Stack

- PHP 7.4+ plugin bootstrap in `cooper-bold-logo-wrangler.php` + `includes/` (only that file has the Plugin Name header). The plugin never writes the `active_plugins` option.
- Gutenberg block: `src/block/` → `build/index.js`
- Frontend hydration: `src/view.js` → `build/view.js`
- Build: `npm run build` (`@wordpress/scripts`; entry points are listed in the `build` script in `package.json`)

## Key conventions

- Block name: `cooper-bold/logo-wrangler`
- Shortcode: `[logo_wrangler]` — prefer `collection="slug"` or `id="123"` from Logo Collections CPT.
- Collections CPT: `coopbolo_collection` — admin menu **Logo Wrangler**. Through 1.3.4 it was `cb_logo_collection`; `COOPBOLO_Collections::maybe_migrate_legacy_data()` moves old posts and `_cb_lw_*` meta once (option `coopbolo_schema_version`)
- PHP prefix: `coopbolo_` / `COOPBOLO_` for functions, classes, constants, options, meta keys, nonces, the CPT, and the REST route (`coopbolo/v1`). WP.org reads a prefix up to its first underscore, so `cb_lw_` was rejected as "cb". Frontend CSS classes and `data-cb-lw` keep `cb-lw` (themes target them)
- Frontend mount selector: `[data-cb-lw]` on `.cb-lw-inner`
- Frontend CSS targeting: `.cb-lw-wrapper` for outer spacing/margins; `.cb-lw-inner` for the logo row
- View script handle: `cooper-bold-logo-wrangler-view`
- Assets enqueue only when block `render_callback` or shortcode runs

## Asset enqueue

- `block.json` declares **editor** assets only (`editorScript`, `editorStyle`). Do **not** add `viewScript` or `style` — frontend JS/CSS is registered and enqueued from `COOPBOLO_Assets::maybe_enqueue()` when `has_block()` or `[logo_wrangler]` (or legacy shortcodes) is present.
- Build entries: `npm run build` compiles `src/block/index.js`, `src/view.js`, and `src/view.scss` (no custom `webpack.config.js`).

## Logo count

No built-in cap on logos per collection, block, or shortcode. Admin users manage performance tradeoffs themselves.

## Layout modes

- `layout="strip"` (default) — one normalized row (`.cb-lw-wrapper` > `.cb-lw-inner`)
- `layout="carousel"` — one Splide slide per logo; use `wrapper="slides"` to nest inside Bricks nested sliders, or `wrapper="full"` for standalone Splide markup
- Carousel slides use class `logo-slider-slide` for RapidSOS Splide Auto Scroll compatibility

## Do not

- Commit `node_modules/` or secrets
- Reimplement the logo-soup measuring algorithm. Use the library's engine (`createLogoSoup` from `@sanity-labs/logo-soup`)
- Import React, `@wordpress/element`, JSX, `to-lw-props.js` or `logo-soup-lifecycle.js` from `src/view.js` or anything it imports. That makes WordPress load React on every page with a strip. The frontend mounts with `src/shared/mount-soup-dom.js`; Jest and PHPUnit guards fail if React comes back. The editor and collection preview keep React
- Add marquee/animation unless explicitly requested — out of scope for logo-soup

## Verify after changes

```bash
npm run verify      # .verify.sh: Jest unit tests (npm run test) + npm run build
npm run test:php    # PHPUnit with WordPress stubs (needs composer install)
npm run lint:js
```

Test in WP: insert block, add logos, confirm frontend strip renders; test shortcode on a classic page.
