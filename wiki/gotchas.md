---
title: Gotchas
type: overview
summary: Known traps. Check here after a repeated failure.
status: active
created: 2026-10-19
updated: 2026-11-10
last_verified: 2026-11-10
sources: [AGENTS.md, AI_SESSION_MEMORY.md, AI_RUNBOOK.md, readme.txt, .wp-env.json, .distignore, .github/workflows/deploy.yml, includes/class-coopbolo-collections.php, includes/class-coopbolo-assets.php, src/view.js, src/shared/mount-soup-dom.js, "commits a5bc12b, 096d507, 1f7112d, 403645d"]
tags: [gotchas]
---

# Gotchas

One line each: the trap, then the fix or the page with details.

## Names and history

- **Old docs show today's name for past events.** Every rename was a repo-wide find/replace, including changelog and session memory. Use `git show <commit>:<file>` for history. See [[0003-plugin-name-cooper-bold-logo-wrangler]].
- **The GitHub repo is `cooper-bold-logo-wrangler` since 2026-10-30** (it was `cooper-bold-logo-soup`; GitHub redirects the old URL, and the WP.org review thread still links it). The secrets path and support-forum URL in `docs/WORDPRESS-ORG-SUBMISSION.md` use wrong slugs. See [[wordpress-org-submission]].
- **Four paths, one checkout.** `~/Repos/logo-soup-wp-plugin`, `cooper-bold-logo-wrangler-wp-plugin`, and `logo-soup-wp-plugin-smoke` are symlinks to `~/Repos/Logo Soup WP Plugin`. An edit in one is an edit in all. See [[overview]].

## Code rules (from AGENTS.md, with the why)

- **Old product names are gone as of 1.4.0** (commit `a5bc12b`, merged to `main`). No `[logo_soup]`/`[balanced_logos]` shortcodes, no legacy blocks, no `balanced-logos.php`, no `active_plugins` writes. Guard tests fail if they return. Older wiki pages that describe them are history. See [[0008-remove-old-product-names]].
- **The plugin never touches `active_plugins`.** Since 1.4.0 there is no read and no write of that option at all — not on load, not on activate, not on update. A guard test fails if one returns.
- **Never migrate `active_plugins` on `plugins_loaded`.** WP.org flagged writing that option on every load. This is why the old pattern was activate/update-only, and it is now moot. See [[0002-migrate-legacy-plugin-path-activate-only]] (superseded).
- **No `viewScript` or `style` in `block.json`.** It double-enqueues with the shortcode path. See [[0006-php-owned-asset-enqueue]].
- **Don't re-add the legacy shortcodes, blocks, or meta keys.** 1.4.0 deleted them on purpose: "Logo Soup" was the first WP.org naming complaint, and the `active_plugins` rewrite was an Aug 6 flag. RapidSOS (still on 1.2.11) was confirmed as not depending on them. See [[0008-remove-old-product-names]].
- **Don't change the strip placeholder DOM (`div > span > img`).** Bricks designer CSS targets `.cb-lw-inner > div > span` and must match before and after hydration. See [[rendering-pipeline]].
- **Never import React into the frontend bundle.** Since 1.5.0 `src/view.js` and everything it imports must stay React-free — no `@wordpress/element`, no JSX, and not `to-lw-props.js` or `logo-soup-lifecycle.js`. A React handle in `build/view.asset.php` makes WordPress load ~53 KB gzipped of React on every page with a strip. A Jest import check and a PHPUnit guard fail if it comes back. The editor and the collection preview keep React. See [[0009-react-free-frontend]].
- **Don't reimplement the logo-soup measuring algorithm.** Use the library's engine (`createLogoSoup` from `@sanity-labs/logo-soup`); the React-free frontend is a DOM writer, not a port of the algorithm. See [[0009-react-free-frontend]], [[0005-bundle-upstream-react-component]].
- **A logo's link is matched by `src`, not by render position.** Since 1.5.0 a logo that fails to load no longer shifts every later logo's link. The React build matched by position. See [[0009-react-free-frontend]].
- **Not a marquee.** Don't add animation unless asked (`AGENTS.md`). The carousel scroll belongs to Splide or the site snippet.
- **New build entry → update `readme.txt` `== Source code ==` too**, or WP.org's source check fails again. See [[0004-ship-source-in-release-zip]].

## Runtime

- **Logos from another origin fail to normalize** (canvas CORS). Use Media Library uploads; wp-env seeds must be local PNGs, not hotlinks (`AI_SESSION_MEMORY.md` 2026-06-22). See [[rendering-pipeline]].
- **Bricks registers an empty `splide` handle.** Enqueuing it loads nothing; the plugin uses its own handles instead. See [[carousel-and-splide]].
- **Carousel looks frozen or jumps on RapidSOS:** check whether the `rapidsos-logo-slider-fix` snippet is active. The plugin defers to it only if both its global and its script tag exist. See [[carousel-and-splide]].
- **Block sidebar lists at most 100 collections** (`coopbolo/v1/collections` uses `posts_per_page` 100). See [[logo-collections]].
- **Saving a collection in 1.3.x deletes the legacy `_cb_lw_*` meta**, so outside scripts writing the old key stop working. The older `_cb_logo_soup_*` keys are no longer read at all since 1.4.0. See [[logo-collections]].

## Releases and environments

- **RapidSOS 1.2.11 → 1.3.x is not a drop-in.** It means a new folder, no loader for the old file, and a new REST route. Going to 1.4.x also means the legacy shortcodes and blocks are gone, so old content must be updated to `[logo_wrangler]`. See [[rapidsos-site]].
- **1.3.7 is the version on WP.org** (uploaded 2026-10-29, awaiting the automated recheck). It carries the `coopbolo_` prefix from 1.3.5 plus the PHP 7.4 and Auto Scroll fixes; `main` is at 1.5.0 and is not uploaded. See [[wporg-review-thread]], [[0007-coopbolo-prefix]], [[0008-remove-old-product-names]], [[0009-react-free-frontend]].
- **Check the ZIP's top level before uploading.** `.distignore` must list every new dev folder; `.claude/` and `.wp-env.override.json` leaked into the first 1.3.4 build. As of 1.4.0 expect only `LICENSE admin build cooper-bold-logo-wrangler.php includes lib package-lock.json package.json readme.txt src` (no `balanced-logos.php` — it was deleted).
- **No PHP 8-only string or array functions in plugin code.** The plugin supports PHP 7.4 (`Requires PHP: 7.4`), and WordPress does not polyfill `str_contains()`. The Auto Scroll duplicate check used it until 1.3.6, which switched to `strpos()`; a guard test fails if one comes back. See [[carousel-and-splide]].
- **A theme that already loads Splide Auto Scroll must not get a second copy.** The plugin finds another handle by `src` and defers to it; `reconcile_autoscroll()` settles it late on `wp_print_footer_scripts`, because block themes render content before `wp_enqueue_scripts`. Since 1.3.7. See [[carousel-and-splide]].
- **New global names need the `coopbolo_` / `COOPBOLO_` prefix** (functions, classes, defines, options, meta, nonces, CPT, REST namespace). WP.org reads a prefix up to its first underscore, so `cb_lw_` counted as "cb". `COOPBOLO_Bootstrap_Test` fails on an unprefixed function, class, or define. The `cb-lw` CSS classes / `data-cb-lw` keep their names on purpose (theme CSS); the legacy shortcodes did not survive 1.4.0.
- **Rolling back from 1.3.5 to 1.3.4 needs the post type changed back**, or every collection disappears. 1.3.4 only knows `cb_logo_collection`. Push files and database together on Cloudways; never a 1.3.5 database onto 1.3.4 code. Exact WP-CLI steps: [[rapidsos-deploy]] → Rollback checklist.
- **wp-env serves the old plugin code for about 2 seconds after `wp plugin install --force`** (opcache `revalidate_freq=2`). Any WP-CLI command also boots WordPress and fires `init`, so it runs pending migrations. Wait or use WP-CLI before reading results (2026-10-22).
- **Directory art may not deploy.** The PNGs live in `assets/`, but the 10up deploy action reads `.wordpress-org/` by default, and `deploy.yml` sets no `ASSETS_DIR`. Set it or move the files before the first tag (default per the [10up action README](https://github.com/10up/action-wordpress-plugin-deploy)). See [[wordpress-org-submission]].
- **README says a GitHub Release also deploys**; `deploy.yml` triggers only on `v*` tag pushes.
- **wp-env breaks on paths with spaces.** `.wp-env.json` mounts the `cooper-bold-logo-wrangler-wp-plugin` symlink, and `WP_ENV_CORE` must be a local core checkout under `$HOME` (shallow clone fails `index-pack`). See [[wp-env-smoke-test]].
- **wp-env fails to build on PHP 7.4** (`apt-get install $PHPIZE_DEPS` exits 100). Put `{"phpVersion":"8.2"}` in the gitignored `.wp-env.override.json` (2026-10-21).
- **`AI_RUNBOOK.md` is partly stale.** It still lists the three legacy shortcodes, says the plugin is version **1.2.15**, and lists logo-soup `^1.2.2` (pinned `1.3.0`). Trust the code and `package.json`.

## Related

- [[overview]] · [[index]]
