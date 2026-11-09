---
title: Index
type: overview
summary: Start here. Must-know facts, then one line per page.
status: active
created: 2026-06-12
updated: 2026-11-08
last_verified: 2026-11-08
sources: []
tags: [meta]
---

# Cooper Bold Logo Wrangler wiki

## Must know

- WP.org: **1.3.7 is the last upload** (2026-10-29) and is waiting on the automated recheck. `main` is at **1.4.0**, which removes the old product names; it is not uploaded yet. Nicholas uploads and replies. [[wporg-review-thread]] [[0008-remove-old-product-names]]
- The plugin **never touches `active_plugins`** as of 1.4.0, and never add `viewScript`/`style` to `block.json`; both are WP.org/architecture rules with guard tests. [[gotchas]]
- Four renames (Logo Soup → Balanced Logos → Logo Wrangler) were repo-wide find/replaces, so old docs misdate names. As of 1.4.0 the legacy shortcodes, blocks, and loader are **gone**; only the `cb-lw-*` CSS classes and the `_cb_lw_*` meta fallback survive. [[0003-plugin-name-cooper-bold-logo-wrangler]] [[0008-remove-old-product-names]]
- RapidSOS staging ran 1.2.11 under the old slug; moving it to 1.3.x is **not** a drop-in. [[rapidsos-site]]
- `~/Repos/logo-soup-wp-plugin` and two other paths are symlinks to `~/Repos/Logo Soup WP Plugin`: one checkout. [[overview]]

## Overview

- [[overview]] — what this is, who it's for, where it runs, release state
- [[gotchas]] — known traps; check after a repeated failure

## Entities

- [[wordpress-plugin]] — the plugin: identifiers, version, PHP class per surface, bootstrap
- [[gutenberg-block-balanced-logos]] — block `cooper-bold/logo-wrangler`, the only registered block since 1.4.0, hydration events
- [[logo-collections]] — `coopbolo_collection` CPT (was `cb_logo_collection`; migrated in 1.3.5), meta keys with legacy fallback, REST route
- [[sanity-logo-soup]] — upstream `@sanity-labs/logo-soup` (pinned 1.3.0) and lifecycle hooks
- [[rapidsos-site]] — first consumer (Bricks, Cloudways), its version, and the 1.2→1.3 upgrade traps

## Concepts

- [[rendering-pipeline]] — PHP placeholder render → conditional enqueue → React-free hydration → canvas measuring
- [[carousel-and-splide]] — `layout="carousel"`, `wrapper` modes, the RapidSOS autoscroll snippet, bundled Splide

## Decisions

- [[0001-pin-splide-extension-auto-scroll]] — stay on auto-scroll 0.5.3, the latest npm release
- [[0002-migrate-legacy-plugin-path-activate-only]] — migrate `balanced-logos.php` entry on activate/update only
- [[0003-plugin-name-cooper-bold-logo-wrangler]] — why the name changed four times; what legacy stays
- [[0004-ship-source-in-release-zip]] — ZIP ships `src/` + `package.json`; readme links the public repo
- [[0005-bundle-upstream-react-component]] — superseded for the frontend by 0009; still the rule for the editor
- [[0006-php-owned-asset-enqueue]] — frontend assets enqueue from PHP only, when something renders
- [[0007-coopbolo-prefix]] — 1.3.5 renames every PHP global to `coopbolo_` and migrates collections once
- [[0009-react-free-frontend]] — the frontend mounts with the engine and plain DOM; no React on public pages (1.5.0)
- [[0008-remove-old-product-names]] — 1.4.0 deletes the legacy shortcodes, blocks, loader, and every `active_plugins` write

## Guides

- [[wordpress-org-submission]] — get into the WP.org directory: ZIP, review, SVN secrets, tag deploy
- [[rapidsos-deploy]] — install on RapidSOS staging, wire collections into Bricks, purge, roll back
- [[wp-env-smoke-test]] — local Docker smoke test and release-ZIP checks

## Sources

- [[wporg-review-thread]] — WP.org review emails, 2026-06-26 → 2026-08-24
- [[notion-logo-soup-plugin-task]] — the Notion task that started the plugin, plus the naming discussion
- [[sar-review]] — 2026-06-12 Spec-Attack-Repair run that chose the architecture
- [[adversarial-review]] — 2026-06-13 adversarial review: fixes, false positives, deferred work

## Meta

- [[SCHEMA]] — how to write here · [[log]] — change history
