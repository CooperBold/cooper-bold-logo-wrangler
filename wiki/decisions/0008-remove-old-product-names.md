---
title: Remove old product names and the legacy loader (1.4.0)
type: decision
summary: 1.4.0 (commit a5bc12b, 2026-10-30) deletes the last "Logo Soup"/"Balanced Logos" remnants — legacy shortcodes, legacy blocks, balanced-logos.php, all active_plugins writes — and the oldest collection meta fallbacks.
status: active
created: 2026-11-03
updated: 2026-11-03
last_verified: 2026-11-03
sources: ["commit a5bc12b", readme.txt 1.4.0, "commit 9501c8d", tests/COOPBOLO_Bootstrap_Test.php, wiki/log.md 2026-10-29/2026-10-30, AI_SESSION_MEMORY.md]
tags: [wordpress-org, legacy, breaking, bootstrap]
---

# Remove old product names and the legacy loader (1.4.0)

**Status:** active. Merged to `main` as **1.4.0** on 2026-10-30 (commit `a5bc12b`, branch `fix/remove-legacy-aliases` on top of `fix/js-hook-prefix` `9501c8d`). 1.4.0 was the `readme.txt` Stable tag on `main` until 1.5.0 replaced it (commit `403645d`); neither has been uploaded to WP.org yet (1.3.7 was the last upload, 2026-10-29 — see [[wporg-review-thread]]).

## Why

"Logo Soup" was the first WP.org naming complaint (2026-06-26, [[wporg-review-thread]]), and the `active_plugins` rewrite was an Aug 6 flag. Both had been patched around (hidden blocks, activate/update-only migration, [[0002-migrate-legacy-plugin-path-activate-only]]) but never removed. Nicholas confirmed RapidSOS (still on 1.2.11) will not be upgraded, so nothing live depends on the aliases — the "keep legacy working" constraint no longer had a live consumer ([[0003-plugin-name-cooper-bold-logo-wrangler]]).

## What was removed

- Shortcodes `[logo_soup]`, `[balanced_logos]`, `[cooper-bold-logo-soup]`; only `[logo_wrangler]` registers now.
- Legacy blocks `cooper-bold/balanced-logos` and `cooper-bold/logo-soup` (previously hidden from the inserter; now gone).
- `balanced-logos.php` and every `active_plugins` read or write: no activate/migrate helper, no `upgrader_process_complete` hook, no legacy-basename helper. The plugin **never touches `active_plugins`** anymore.
- Duplicate `cb-balanced-logos-*` CSS classes and `data-cb-balanced-logos-*` attributes (`.cb-lw-*` / `data-cb-lw*` unchanged — themes target them).
- Read-fallbacks for the oldest collection meta (`_cb_balanced_logos_*`, `_cb_logo_soup_*`); the `_cb_lw_*` fallback from the 1.3.x era stays.
- JS: `fix/js-hook-prefix` renamed event names and the localStorage key to `coopbolo-*` (`coopbolo-hydrated`, `coopbolo-debug`, …) and dropped the duplicate `cb-balanced-logos-hydrated` event; `src/view.js` reads only `data-cb-lw` now.

## What was kept

- The **1.3.x collection migration**: `cb_logo_collection` → `coopbolo_collection` with `_cb_lw_*` meta copy ([[0007-coopbolo-prefix]]). RapidSOS-era content on 1.3.x sites still upgrades cleanly.
- `cb-lw-*` CSS classes and `data-cb-lw` (CSS is outside the WP.org prefix rule, and themes target the classes).

## Verification (from commit `a5bc12b`)

PHPUnit 62, Jest 19, build; wp-env PHP 7.4 in-place upgrade from 1.3.7 with identical strips/carousel and empty `debug.log`; a site with the old loader path still active in `active_plugins` loads normally (the missing file is just not included); Plugin Check: no errors. Two guard tests in `tests/COOPBOLO_Bootstrap_Test.php` fail if the old names or an `active_plugins` write return: `test_old_product_names_are_not_registered_or_emitted` and `test_plugin_never_rewrites_active_plugins`.

## Consequences

- **Breaking for old content.** The `readme.txt` 1.4.0 upgrade notice says: if content still uses `[logo_soup]` or `[balanced_logos]`, change it to `[logo_wrangler]` before updating. Existing posts containing the two legacy blocks no longer resolve in the block library.
- Replaces the keep-legacy posture of [[0002-migrate-legacy-plugin-path-activate-only]] (now superseded) and the "kept on purpose" list in [[0003-plugin-name-cooper-bold-logo-wrangler]].
- The 1.3.4 → 1.3.5 rollback checklist in [[rapidsos-deploy]] is unaffected (collection migration still there).

## Related

- [[0002-migrate-legacy-plugin-path-activate-only]] (superseded) · [[0003-plugin-name-cooper-bold-logo-wrangler]] · [[0007-coopbolo-prefix]] · [[wporg-review-thread]] · [[wordpress-plugin]]
