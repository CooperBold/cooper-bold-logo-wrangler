---
title: Migrate legacy plugin path on activate/update only
type: decision
summary: Rewrite the legacy balanced-logos.php active_plugins entry only on activate/update (never on plugins_loaded); deactivate removes both paths.
created: 2026-08-30
updated: 2026-11-03
last_verified: 2026-11-03
tags: [wordpress-org, bootstrap, migration, compliance]
aliases: [activate-only-migration, legacy-path-migration]
sources: [cooper-bold-logo-wrangler.php, balanced-logos.php, tests/COOPBOLO_Bootstrap_Test.php, "commit 385fbad", "commit a5bc12b", AI_SESSION_MEMORY.md]
status: superseded
---

# Migrate the legacy `balanced-logos.php` entry on activate/update only

**Superseded by [[0008-remove-old-product-names]] (1.4.0, commit `a5bc12b`).** As of 1.4.0 the legacy loader `balanced-logos.php` and every `active_plugins` read, migrate helper, upgrader hook, and deactivate cleanup listed below are gone — the plugin no longer touches the `active_plugins` option at all. This page is kept as history of why the activate/update-only pattern existed (WP.org compliance) before the whole legacy path was deleted.

## Context

Since the rename to **Cooper Bold Logo Wrangler**, sites that had activated the plugin at the old path (`balanced-logos.php`) keep that entry in the `active_plugins` option; WordPress continues to include that headerless loader file, which in turn requires the canonical bootstrap `cooper-bold-logo-wrangler.php`. The canonical bootstrap therefore rewrites `active_plugins` to replace the legacy basename with the canonical one.

Originally that rewrite ran on **`plugins_loaded`** (priority 0) — i.e. it mutated a global site option on **every load** of the plugin on every site. WordPress.org's manual review flagged this pattern as non-compliant (plugins should not be rewriting the `active_plugins` option on load). This surfaced in the review loop for slug `cooper-bold-logo-wrangler` (v1.3.1 prep → v1.3.2 upload → v1.3.3 bugbot follow-up, session 2026-08-24).

## Decision

Migrate the legacy `active_plugins` entry **only on user-initiated plugin lifecycle events**:

1. **Activation** — `cb_lw_activate()` calls `cb_lw_migrate_legacy_bootstrap_entry()` after CPT creation logic.
2. **Plugin update** — `cb_lw_maybe_migrate_after_upgrade()` on `upgrader_process_complete` (args 10, 2) migrates when `$options['action'] === 'update'`, `$options['type'] === 'plugin'`, and the updated plugin is the canonical or legacy basename.
3. **Deactivation** — new `cb_lw_deactivate()` removes **both** the canonical and legacy basenames from `active_plugins` so the plugin fully stops.

The migration itself dedupes: it skips entries already migrated (and drops the legacy entry even when the canonical one already exists), so repeated runs are idempotent. The legacy path is computed by the new `cb_lw_legacy_bootstrap_basename()` helper (`plugin_basename(CB_LW_PATH . 'balanced-logos.php')`) instead of reading a `CB_LW_BOOTSTRAP_FILE` constant. The `plugins_loaded` hook is never used for this — enforced by string-level assertions in `tests/CB_LW_Bootstrap_Test.php`.

## Consequences

- The plugin no longer writes `active_plugins` on ordinary site loads; the rewrite only ever happens on the two rare, user-caused moments a site admin acts on the plugin.
- Sites that are never reactivated or updated through wp-admin keep their legacy `balanced-logos.php` entry working (the headerless loader still includes the canonical bootstrap), so nothing breaks — the entry just stays legacy until the next activation/update cycle.
- Companion decision in the same v1.3.3 change: legacy block aliases (`cooper-bold/balanced-logos`, `cooper-bold/logo-soup`) are **hidden from the block inserter** (`supports.inserter: false`) rather than unregistered — existing posts keep rendering/editing, new inserts go to the canonical block only. See [[gutenberg-block-balanced-logos]].
- Guard tests codify the WP.org constraint so a future refactor cannot silently reintroduce the `plugins_loaded` migration.
- This only covers the 1.2.15 path `balanced-logos.php`. Sites still on the original `cooper-bold-logo-soup.php` (RapidSOS) have no loader; see [[rapidsos-site]].

## Related

- [[0003-plugin-name-cooper-bold-logo-wrangler]] · [[wordpress-plugin]] · [[wporg-review-thread]]
