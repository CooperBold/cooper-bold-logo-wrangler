---
title: Log
type: overview
summary: Append-only history of wiki changes. Older entries are not strictly date-ordered.
status: active
created: 2026-06-12
updated: 2026-11-08
last_verified: 2026-11-08
sources: []
tags: [meta]
---


# Log

> Chronological record of wiki operations. Append-only.
>
> Format: `## [YYYY-MM-DD] verb | Subject`
>
> Verbs: `ingest`, `query`, `lint`, `update`, `create`, `migrate`, `session`

## [2026-10-20] update | Quiet window — no activity since 2026-10-19 wiki upgrade

Only one commit in the 24h window: `c11f51f` (2026-10-20 03:31 PDT, `nightly-simplemem`), which auto-mirrored the [[log]] `2026-10-19 upgrade` entry into `docs/simplemem/memories.json` (+12 lines, only `type: wiki_log` / `source: maintain_all_simplemem` metadata — no new session state). The `2026-10-19` wiki upgrade itself is commit `f5792c5` (touched `AGENTS.md` + all `wiki/` pages) and is already fully recorded in the `2026-10-19 upgrade` entry above, including the SCHEMA migration and every page it created/updated. `AI_SESSION_MEMORY.md` (last close-out 2026-09-02), `AI_RUNBOOK.md` (2026-10-11), and the rest of `docs/` are unchanged in the window; working tree clean. No wiki content edits needed beyond this status confirmation.

## [2026-09-22] update | Quiet window — no activity since 2026-09-21 entry

No commits in the 24h window (`git log --since="24 hours ago"` empty) and no files touched on disk in the window (working tree clean; the only mtime churn is `uncommitted/lancedb_project_knowledge/` — the gitignored LanceDB store, not repo content). `AI_SESSION_MEMORY.md` (last touched 2026-09-02), `AI_RUNBOOK.md` (2026-08-24), and everything under `docs/` are unchanged in the window. The state recorded in [[wordpress-org-submission]] therefore holds unchanged: **v1.3.2 in human review on WP.org**; **v1.3.3** (activate/update-only legacy migration per [[0002-migrate-legacy-plugin-path-activate-only]] + logo-soup 1.3.0 + lifecycle hooks) built and verified (19 Jest incl. 3 new lifecycle tests, build OK) but **not uploaded**, queued until 1.3.2 clears. No wiki content edits needed beyond this status confirmation.

## [2026-09-21] update | Quiet window — no activity since 2026-09-20 entry

No commits in the 24h window (`git log --since="24 hours ago"` empty); latest commit overall is `16450d6` (the 2026-09-20 wiki gardener) and no nightly SimpleMem auto-commit landed in this window. `AI_SESSION_MEMORY.md` (last touched 2026-09-02), `AI_RUNBOOK.md` (2026-08-24), and everything under `docs/` are unchanged in the window; working tree clean. The state recorded in [[wordpress-org-submission]] therefore holds unchanged: **v1.3.2 in human review on WP.org**; **v1.3.3** (activate/update-only legacy migration per [[0002-migrate-legacy-plugin-path-activate-only]] + logo-soup 1.3.0 + lifecycle hooks) built and verified (19 Jest incl. 3 new lifecycle tests, build OK) but **not uploaded**, queued until 1.3.2 clears. No wiki content edits needed beyond this status confirmation.

## [2026-09-20] update | Quiet window — no activity since 2026-09-15 entry

No commits in the 24h window (`git log --since="24 hours ago"` empty) and no files touched on disk in the window (working tree clean). `AI_SESSION_MEMORY.md` (last entry 2026-09-02), `AI_RUNBOOK.md` (2026-08-24), and `docs/` are all unchanged. The state recorded in [[wordpress-org-submission]] therefore holds unchanged: **v1.3.2 in human review on WP.org**; **v1.3.3** (activate/update-only legacy migration per [[0002-migrate-legacy-plugin-path-activate-only]] + logo-soup 1.3.0 + lifecycle hooks) built and verified (19 Jest incl. 3 new lifecycle tests, build OK) but **not uploaded**, queued until 1.3.2 clears. No wiki content edits needed beyond this status confirmation.

## [2026-09-15] update | Quiet window — WP.org state unchanged

No commits in the 24h window (`git log --since="24 hours ago"` empty); latest commit overall is `b2dcb7e` (2026-09-06 wiki gardener), and no nightly SimpleMem auto-commit landed in this window. `AI_SESSION_MEMORY.md` (last touched 2026-09-02), `AI_RUNBOOK.md` (2026-08-24), and everything under `docs/` are unchanged in the window; working tree clean. The state recorded in [[wordpress-org-submission]] therefore holds unchanged: **v1.3.2 in human review on WP.org**; **v1.3.3** (activate/update-only legacy migration per [[0002-migrate-legacy-plugin-path-activate-only]] + logo-soup 1.3.0 + lifecycle hooks) built and verified (19 Jest incl. 3 new lifecycle tests, build OK) but **not uploaded**, queued until 1.3.2 clears. No wiki content edits needed beyond this status confirmation.

## [2026-09-06] update | Review-pending confirmed; v1.3.3 still queued locally

No new commits in the 24h window except `9d398fa` (2026-09-03 03:30 PDT, `nightly-simplemem`), which auto-mirrored the existing 2026-09-02 `AI_SESSION_MEMORY.md` close-out entry into `docs/simplemem/memories.json` (+12 lines, `source: ai_session_memory` metadata only — no new session state). That close-out (logo-soup 1.3.0 bump + lifecycle hooks) is already fully recorded in the wiki's 2026-09-02 `session` entry above. `AI_SESSION_MEMORY.md` and `AI_RUNBOOK.md` otherwise unchanged in the window; working tree clean; no `docs/` changes. The state recorded in [[wordpress-org-submission]] therefore holds unchanged: **v1.3.2 in human review on WP.org**; **v1.3.3** (activate/update-only legacy migration per [[0002-migrate-legacy-plugin-path-activate-only]] + logo-soup 1.3.0 + lifecycle hooks) built and verified (19 Jest incl. 3 new lifecycle tests, build OK) but **not uploaded**, queued until 1.3.2 clears. No wiki content edits needed beyond this status confirmation.

## [2026-09-02] session | logo-soup 1.3.0 bump + lifecycle hooks

Bumped `@sanity-labs/logo-soup` to **1.3.0** (pinned). New `src/shared/logo-soup-lifecycle.js` wires `onError` (failures logging + clear loading class) and `onNormalized` (clear `cb-lw-soup-loading`) for view hydration, block editor, and collection preview. `to-lw-props.js` adds `crossOrigin="anonymous"` on custom `renderImage` imgs. `view.scss` styles loading state via `data-logo-soup-loading`. Updated [[sanity-logo-soup]]. Verify PASS (19 Jest, build OK). WP.org status unchanged: v1.3.2 in review; v1.3.3 queued locally with this dep bump folded in.

## [2026-09-01] update | Review-pending confirmed; v1.3.3 still queued locally

No new commits since `7176b06` (this wiki's own 2026-08-30 gardener): the only commit in the window is `a24103d` (2026-08-31 03:30 PDT, `nightly-simplemem`), which auto-mirrored the 2026-08-30 wiki log entry into `docs/simplemem/memories.json` (+12 lines, only `type: wiki_log` metadata — no new session state). `AI_SESSION_MEMORY.md` and `AI_RUNBOOK.md` are unchanged in the window; working tree clean; no `docs/` changes. The state recorded in [[wordpress-org-submission]] therefore holds unchanged: **v1.3.2 in human review on WP.org**; **v1.3.3** (activate/update-only legacy migration, deactivation cleanup, hidden legacy blocks — [[0002-migrate-legacy-plugin-path-activate-only]]) built and verified (43 PHPUnit, 16 Jest) but **not uploaded**, queued until 1.3.2 clears. No wiki content edits needed beyond this status confirmation.

## [2026-08-17] update | Closeout commit landed the rename to Cooper Bold Logo Wrangler

Commit `aed0e5b closeout: auto-commit from 2026-08-17` (2026-08-17 12:33 PDT) landed everything that the 2026-08-05 (×2) and 2026-08-07 log entries had tracked as "uncommitted, commit pending". Working tree is now clean (`git status` empty). Stat: 62 files, +1017 / −933. Highlights:

- **Bootstrap renamed for real.** `cooper-bold-logo-wrangler.php` is the only file with a `Plugin Name` header; `balanced-logos.php` is a thin headerless legacy loader (`CB_LW_BOOTSTRAP_FILE` → `require_once` canonical bootstrap) that still gets included when the old path is in `active_plugins`.
- **PHP classes renamed to `CB_LW_*`.** `class-cb-balanced-logos.php` → `class-cb-lw.php`; siblings `assets`, `renderer`, `collections`, `admin-branding` all under `cb-lw-`. Shortcode is now handled in `CB_LW` alongside the block (no separate `CB_LW_Shortcode` class — `AI_RUNBOOK.md` was clarified to match).
- **JS / tests renamed.** `src/shared/to-balanced-logos-props.js` → `to-lw-props.js`; matching test rename; `tests/CB_Balanced_Logos_Assets_Test.php` → `CB_LW_Assets_Test.php` (the file was rewritten, not just renamed).
- **`crewai/` scaffolding deleted.** `crewai/` directory is gone: agents/tasks YAML, `crew.py`, `main.py`, `tools/`, `pyproject.toml`, `crewai/.gitignore`, `crewai/.env.example`, `crewai/README.md`. `AI_RUNBOOK.md` "AI tooling" row drops "CrewAI" and lists SimpleMem + Cursor planning + wiki + Context7 instead.
- **`AGENTS.md` tidied.** Heading is "Cooper Bold Logo Wrangler — Agent Notes" (previously still said "Logo Strip Normalizer" / `cooper_cblsn_*`). Build section clarifies there is **no custom `webpack.config.js`** — entries are `src/block/index.js`, `src/view.js`, `src/view.scss`.
- **`readme.txt`** bumped to **Stable tag: 1.3.0**, leading shortcode is `[logo_wrangler]`, plugins folder is `cooper-bold-logo-wrangler/`, admin menu is "Logo Wrangler".

The earlier wiki entities ([[wordpress-plugin]], [[gutenberg-block-balanced-logos]]) and guides ([[wp-env-smoke-test]], [[rapidsos-deploy]], [[wordpress-org-submission]]) already described the post-rename state, so this commit lines the repo up with the wiki rather than the other way around. Touched [[gutenberg-block-balanced-logos]] to drop the now-stale "(uncommitted rename state)" qualifier; the agent [[wordpress-plugin]] was already accurate (only date stamp needed). [[0001-pin-splide-extension-auto-scroll]] unchanged. No new entities or decisions introduced by this commit.

## [2026-08-09] create | Decision: pin splide-extension-auto-scroll@0.5.3

No new commits since `3e8b804`; the uncommitted rename working tree was fully covered by the 2026-08-05/07 entries. One new detail surfaced in the working-tree `readme.txt` (touched 2026-08-08): the 1.3.0 changelog now documents that `splide-extension-auto-scroll@0.5.3` is the **latest published npm release** (upstream repo's last commit 2022-09-06) and that WP.org's `out of date libraries` check is comparing against a development branch with no new tagged release. Recorded as [[0001-pin-splide-extension-auto-scroll]] — the first entry in `decisions/` — and indexed.

## [2026-08-07] update | Rename tail: package.json, test bootstrap, view.scss (uncommitted)

No new commits since `a50f708`. Remaining uncommitted rename details not previously recorded: `package.json` name is now `cooper-bold-logo-wrangler` (version 1.3.0); `tests/bootstrap.php` stubs renamed to `CB_LW_PATH` / `CB_LW_URL` constants (plugin URL now `.../cooper-bold-logo-wrangler/`) and gained `metadata_exists()` / `delete_post_meta()` stubs backed by `$GLOBALS['cb_test_post_meta']`; `src/view.scss` now dual-covers every legacy selector with `.cb-lw-*` equivalents (`.cb-lw-inner`, `.cb-lw-slide`, `.cb-lw-carousel-ref`, hydrating-state rules for both class families). Already consistent with [[gutenberg-block-balanced-logos]]; commit still pending.

## [2026-08-05] update | Legacy loader + crewai removal (uncommitted)

Same working tree, two details not yet recorded: `balanced-logos.php` is now a **headerless legacy loader** (no `Plugin Name` header — not a second plugin entry) that WordPress includes when the old path remains in `active_plugins`; the canonical `cooper-bold-logo-wrangler.php` bootstrap guards double-init via `CB_LW_FILE` and migrates the entry. Also the entire `crewai/` scaffolding (agents/tasks YAML, crew.py) is deleted in the working tree, and `readme.txt` now leads with `[logo_wrangler]` as the primary shortcode (Stable tag 1.3.0). Noted on [[wordpress-plugin]]. Commit still pending.

## [2026-08-05] update | view.js dual-attr hydration + distignore hygiene (uncommitted)

No new commits since `c2449b9`. Working tree gained two things since the 2026-07-31 rename notes: `src/view.js` now reads config from `data-cb-lw` **or** legacy `data-cb-balanced-logos` attrs via a `getConfigAttr()` helper (strips, carousel refs, slides, splide roots all match both), and on hydration dispatches a `cb-lw-hydrated` CustomEvent alongside the legacy `cb-balanced-logos-hydrated`. `.distignore` now also excludes local tooling: `MEMORY.md`, `.verify.sh`, `requirements-lancedb.txt`/`requirements*.txt`, `/.loops`. Noted on [[gutenberg-block-balanced-logos]]. Commit still pending.

## [2026-08-05] update | Guides caught up to committed rename

Commit `78214bb` (nightly auto-commit) renamed the operational docs — `docs/RAPIDSOS-DEPLOY.md`, `docs/SMOKE-TEST.md`, `docs/WORDPRESS-ORG-SUBMISSION.md` — from Balanced Logos to **Cooper Bold Logo Wrangler** (`[logo_wrangler]` shortcode, `cb-lw` classes/attrs, `cooper-bold-logo-wrangler-view` script handle, `CB_LW_VERSION`, test files `CB_LW_Renderer_Test.php` / `to-lw-props.test.js`). The wiki guides still used the old names, so synced [[rapidsos-deploy]], [[wp-env-smoke-test]], and [[wordpress-org-submission]] to match the docs, including the Bricks carousel section (`layout="carousel" wrapper="slides"`, `.cb-lw-slide`, `data-cb-lw-ref`) that [[rapidsos-deploy]] was missing. Also retitled `wiki/index.md`. Entity pages and SCHEMA were already updated by the same commit.

## [2026-06-12] create | Wiki bootstrapped

Initial wiki structure created. Ready for first ingest.

## [2026-06-12] migrate | No docs/ directory

Legacy docs ingest skipped: no `docs/` folder with markdown files at bootstrap time.

## [2026-06-12] update | Plugin entity pages

Added wiki entities for the existing plugin: [[wordpress-plugin]], [[sanity-logo-soup]], [[gutenberg-block-balanced-logos]].

## [2026-06-14] ingest | SAR + Adversarial review docs

Ingested `docs/SAR-REVIEW.md` → [[sar-review]] and `docs/ADVERSARIAL-REVIEW.md` → [[adversarial-review]]. Both are review/audit artifacts that explain the security and architecture decisions baked into [[wordpress-plugin]].

## [2026-06-14] ingest | WordPress.org submission guide

Ingested `docs/WORDPRESS-ORG-SUBMISSION.md` → [[wordpress-org-submission]]. Promoted to `wiki/guides/` because the maintainer-facing release flow (ZIP build, SVN deploy, Plugin Check) is operational, not source material.

## [2026-06-17] ingest | wp-env smoke test + RapidSOS deploy

Ingested `docs/SMOKE-TEST.md` → [[wp-env-smoke-test]] and `docs/RAPIDSOS-DEPLOY.md` → [[rapidsos-deploy]]. Both are operational runbooks (test scaffolding, Cloudways/Bricks deploy) so they live in `wiki/guides/` alongside [[wordpress-org-submission]]. `docs/WORDPRESS-ORG-SUBMISSION.md` was already mirrored from 2026-06-14 (script flagged it again because it only checks `wiki/sources/`; left as-is).

## [2026-07-31] update | Uncommitted `balanced-logos` → `cb-lw` rename continues

No commits in the last 24h, but the working tree carries a large uncommitted internal rename: `balanced-logos.php` → `cooper-bold-logo-wrangler.php`, `CB_Balanced_Logos_*` classes → `CB_LW_*` under `includes/`, and `src/shared/to-balanced-logos-props.js` → `to-lw-props.js`. The only file touched within the window is `src/view.js`: imports `toLwProps` from `./shared/to-lw-props`, hydrating class now `cb-lw-hydrating`, debug opt-in now `window.CB_LW_DEBUG` / `?cb_lw_debug=1`, and the console log prefix is `[Cooper Bold Logo Wrangler]`. Noted on [[gutenberg-block-balanced-logos]]; commit pending.

## [2026-07-31] migrate | Rename to Cooper Bold Logo Wrangler complete (uncommitted)

Product rename locked: **Cooper Bold Logo Wrangler**. Bootstrap `cooper-bold-logo-wrangler.php`, PHP `CB_LW_*`, block `cooper-bold/logo-wrangler`, shortcode `[logo_wrangler]`, CSS/data `cb-lw` (legacy Balanced Logos attrs/classes still emitted). CPT stays `cb_logo_collection`. Bugbot findings fixed (release ZIP script, legacy block editor assets, dual data attrs). Release ZIP smoke: `dist/cooper-bold-logo-wrangler-1.3.0.zip`. Updated [[wordpress-plugin]], [[gutenberg-block-balanced-logos]], SCHEMA. Commit still pending.

## [2026-06-22] session | WP.org prep v1.2.12 close-out

WP.org submission prep complete for **1.2.12** (Splide vendored, PNG assets, readme). Local wp-env verified: strip post 4 (`demo-50-logos`), carousel page 62, Plugin Check pass on release-shaped run. Lesson filed: same-origin Media Library logos required (external URLs CORS-fail hydration). Next: push `main`, upload ZIP, SVN secrets after approval, tag `v1.2.12`. See `AI_SESSION_MEMORY.md` and `docs/WORDPRESS-ORG-SUBMISSION.md`.

## [2026-08-05] lint | Dead link fix + ingest re-flag note

Fixed dead wikilink `[[gutenberg-block-logo-soup]]` → `[[gutenberg-block-balanced-logos]]` in [[sanity-logo-soup]] (block entity page was renamed earlier without updating this backlink). Lint now clean. Ingest script re-flags `docs/RAPIDSOS-DEPLOY.md`, `docs/SMOKE-TEST.md`, `docs/WORDPRESS-ORG-SUBMISSION.md` because it only checks `wiki/sources/` — all three are already mirrored as guides ([[rapidsos-deploy]], [[wp-env-smoke-test]], [[wordpress-org-submission]]); no re-ingest needed.
## [2026-08-24] session | WP.org review 1.3.2 uploaded; 1.3.3 compliance fixes ready

WP.org review loop for slug `cooper-bold-logo-wrangler` (branch `feat/wporg-review-1.3.1`): **v1.3.2 uploaded** (Tested up to 7.1, automated scan passed) with a reply on the review email thread; v1.3.1 prep shipped `src/` + `package.json` in the ZIP with source docs in readme and dropped the `plugins_loaded` `active_plugins` rewrite. Bugbot follow-up produced **v1.3.3** (this run's commit): activate/update-only migration, deactivation cleanup, hidden legacy blocks. v1.3.3 **not** uploaded — queued until the human reviewer clears 1.3.2. Verify PASS (43 PHPUnit, 16 Jest); Harvest time drafted, not posted.

## [2026-08-30] update | v1.3.3 lands: activate-only migration + hidden legacy blocks (commit `385fbad`)

Commit `385fbad fix: WP.org compliance v1.3.3` (2026-08-24 15:16 PDT) implemented the bugbot follow-ups from the WP.org review thread. Working tree clean; `git log --since="24 hours ago"` shows only `cd1d786` (nightly SimpleMem auto-commit of `docs/simplemem/memories.json`, mirroring the session memory below — no wiki meaning). `AI_SESSION_MEMORY.md` gained a 2026-08-24 close-out entry. Substantive changes:

- **Legacy migration now activate/update-only.** The `plugins_loaded` → `cb_lw_migrate_legacy_bootstrap_entry` hook (priority 0) is removed. Migration runs from `cb_lw_activate()` (user-initiated activation) and from `cb_lw_maybe_migrate_after_upgrade()` on `upgrader_process_complete` when `$options['action']==='update'` and the plugin is `cooper-bold-logo-wrangler` (canonical or legacy basename). The migration itself also dedupes: it skips already-migrated entries instead of appending a duplicate canonical basename. `CB_LW_BOOTSTRAP_FILE` guard dropped — the legacy path is now computed by the new `cb_lw_legacy_bootstrap_basename()` helper. New `cb_lw_deactivate()` on the deactivation hook removes **both** canonical and legacy basenames from `active_plugins`. WP.org compliance rationale: no more mutating the `active_plugins` option on every site load.
- **Legacy blocks hidden from the inserter.** `src/block/index.js` registers `cooper-bold/balanced-logos` and `cooper-bold/logo-soup` with `supports: { ...blockSettings.supports, inserter: false }` — existing posts keep working; new inserts are impossible via the block picker. Registration itself is unchanged (decision in [[0002-migrate-legacy-plugin-path-activate-only]]).
- **Version 1.3.0 → 1.3.3** in `cooper-bold-logo-wrangler.php` (`Version` header + `CB_LW_VERSION`), `package.json` (1.3.0 → 1.3.3), and `build/block/block.json` + `build/index.js`/`build/index.asset.php` (rebuild). `readme.txt`: Stable tag 1.3.3, **Tested up to 7.1**, new `== Source code ==` section (public GitHub repo + `src/**` → `build/**` mapping + `npm install && npm run build` rebuild), new `== Third-party libraries ==` section (logo-soup MIT, Splide 4.1.4 MIT, auto-scroll 0.5.3 MIT — see [[0001-pin-splide-extension-auto-scroll]]), and changelog/upgrade-notice entries for **1.3.1**, **1.3.2**, **1.3.3**.
- **Release ZIP now ships `src/` and `package.json`.** `.distignore` drops `/src`, `/package.json`, `/package-lock.json` from excludes — WP.org wants compiled `build/*.js` to be regenerable from shipped source (1.3.1 review feedback).
- **New guard tests:** `tests/CB_LW_Bootstrap_Test.php` — asserts the bootstrap does **not** contain `add_action('plugins_loaded', 'cb_lw_migrate_legacy_bootstrap_entry'...)`, **does** wire the activation/deactivation/upgrader hooks (string-level source assertions), and that `readme.txt` points at the public repo and the `src/view.js` / `src/admin/collection-preview.js` entries.
- **Docs:** `AGENTS.md` stack section corrected — legacy loader "requires the canonical file when the old path is still listed in `active_plugins`; migrate that entry only on user-initiated **activate** or **plugin update** hooks — never on `plugins_loaded`". 1.3.2 was readme-only (Tested up to 7.1).

Updated [[wordpress-plugin]] (version, bootstrap/migration behavior), [[gutenberg-block-balanced-logos]] (legacy blocks hidden from inserter), [[wordpress-org-submission]] (review-loop status as of the 2026-08-24 session), and [[0001-pin-splide-extension-auto-scroll]] (readme now has a dedicated Third-party libraries section). Created [[0002-migrate-legacy-plugin-path-activate-only]] and indexed in [[index]]. `cd1d786` (2026-08-25) touched only `docs/simplemem/memories.json` (auto-import of the close-out memory above).

## [2026-10-19] upgrade | Wiki moved to the standard template

Replaced SCHEMA/index with the standard template (project notes kept). Added [[overview]], [[gotchas]], [[rendering-pipeline]], [[carousel-and-splide]], [[logo-collections]], [[rapidsos-site]], decisions [[0003-plugin-name-cooper-bold-logo-wrangler]] to [[0006-php-owned-asset-enqueue]], and sources [[wporg-review-thread]] and [[notion-logo-soup-plugin-task]], mined from git history, the WP.org email thread, the Notion task, and the RapidSOS repo. Spot-checked every existing page against code. Fixed stale claims: wrong checkout path and test counts in [[wp-env-smoke-test]], the ZIP now ships `src/`, a find/replace "chosen 2026-07-06" name date, wrong repo slug in the secrets path, the support-forum URL and contributor slug in [[wordpress-org-submission]], and the 1.2.x→1.3.x upgrade warning in [[rapidsos-deploy]]. Added standard frontmatter everywhere. Removed a stray duplicate `2026-08-05 lint` heading at the end of this log.

## [2026-10-21] session | 1.3.4 prefix fixes for WP.org review

Renamed the last unprefixed names (`cb_is_logo_wrangler_admin_screen()` → `cb_lw_is_admin_screen()`, nonce field `cb_lw_collection_nonce`, list columns `cb_lw_logo_count` / `cb_lw_shortcode`). Legacy shortcodes now register only when the tag is free. Added a prefix guard test. `.distignore` now excludes `.claude/`, `CLAUDE.md`, `.wp-env.override.json`. Verified: Jest + build, PHPUnit 44 tests, wp-env (PHP 8.2) smoke, Plugin Check on the 1.3.4 ZIP with no errors. Updated [[gotchas]] and [[wporg-review-thread]]. Not uploaded; Nicholas uploads and replies.

## [2026-10-22] session | 1.3.5 coopbolo_ prefix for WP.org review

Nicholas pasted the 2026-08-26 manual review, which the 2026-10-19 inbox search had missed: prefix "cb" too short, plus the auto-scroll "out of date" flag. 1.3.4 still used `cb_lw_`, so built 1.3.5 on branch `fix/coopbolo-prefix`. It renames every PHP global, the CPT (`coopbolo_collection`), meta, nonce, fields, and REST route, with a one-time migration. Verified with PHPUnit 49, Jest 19, a wp-env 1.3.4 → 1.3.5 in-place upgrade, a fresh install with WP_DEBUG, and Plugin Check (no issues). Both Splide files are byte-identical to the latest npm releases. New [[0007-coopbolo-prefix]]; updated [[index]], [[gotchas]], [[logo-collections]], [[wordpress-plugin]], [[rapidsos-site]], [[wporg-review-thread]]. Committed on `fix/coopbolo-prefix`; not pushed or uploaded.

## [2026-10-22] gate | 1.3.5 migration hardened before merge

Adversarial gate (security, regression, edge-case personas): FIX REQUIRED → fixed → APPROVE. The migration now:
- checks `get_post_type()` before moving, because a theme's `pre_get_posts` can hijack the lookup
- copies `_cb_lw_*` meta instead of deleting it, so a rollback works
- skips non-array meta
- sets the option only when every move succeeded
- runs on `init` priority 20
- clears the option on deactivate

Rollback WP-CLI steps are in [[rapidsos-deploy]]. Verified in wp-env (rollback, then a re-upgrade with a hostile filter), PHPUnit 53, Plugin Check clean. Merged to `main`.

## [2026-10-29] session | 1.3.7 uploaded; JS hook names prefixed on a branch

Nicholas uploaded 1.3.7 and replied in the WP.org thread (4:28–4:29 PM); now waiting on the automated recheck. Pre-empting new reviewer notes, branch `fix/js-hook-prefix` (not merged) renames the JS event names and the localStorage key to `coopbolo-*` (`coopbolo-hydrated`, `coopbolo-preview-update`, `coopbolo-debug`) and drops the `cb-balanced-logos-hydrated` duplicate. RapidSOS's snippet already listens for `cb-logo-soup-hydrated`, which the plugin stopped firing earlier, so nothing live depends on these names. Left alone on purpose: `cb-lw-*` CSS classes and `data-cb-lw` (CSS is outside the prefix rule), and the legacy aliases below, pending Nicholas's call.

## [2026-10-30] session | 1.4.0: old product names removed (branch, not merged)

Branch `fix/remove-legacy-aliases` (on top of `fix/js-hook-prefix`) removes the `[logo_soup]`, `[balanced_logos]` and `[cooper-bold-logo-soup]` shortcodes, the two legacy blocks, `balanced-logos.php` and its `active_plugins` migration, the `cb-balanced-logos-*` classes and `data-cb-balanced-logos-*` attributes, and the `_cb_balanced_logos_*` / `_cb_logo_soup_*` meta fallbacks. The 1.3.x collection migration stays. Reason: "Logo Soup" was the first WP.org naming complaint, and the `active_plugins` rewrite was an Aug 6 flag. Nicholas confirmed RapidSOS (1.2.11) will not be upgraded, so nothing live loses content. Guard tests: `COOPBOLO_Bootstrap_Test::test_old_product_names_are_not_registered_or_emitted` and `test_plugin_never_rewrites_active_plugins`.

## [2026-10-30] ops | GitHub repo renamed

`CooperBold/cooper-bold-logo-soup` → `CooperBold/cooper-bold-logo-wrangler` (Nicholas asked; done with `gh repo rename`). GitHub redirects the old URL, local `origin` remotes were repointed, and the repo URL in the plugin header, readme and docs updates on branch `chore/repo-rename`. WP.org's reviewers were given the old URL; it still redirects. The new URL reaches WP.org only with the next uploaded version.

## [2026-11-08] update | 1.4.0 and 1.3.6/1.3.7 reconciled across the wiki

The 24-hour window was empty (newest commit `a26e3ab`, 2026-11-04), but the wiki had drifted from `main` since the 2026-10-20 gardener run. This entry also corrects the two 2026-10-29/30 entries above: **`fix/js-hook-prefix` (`9501c8d`) and `fix/remove-legacy-aliases` (`a5bc12b`) are both merged into `main`** — they were written as branches and later landed by fast-forward, so "branch, not merged" is now history. `main` is at **1.4.0**.

Everything below was verified against the code on `main`, not inferred:

- **1.4.0** (`a5bc12b`): only `[logo_wrangler]` and only `cooper-bold/logo-wrangler` remain; `balanced-logos.php` and every `active_plugins` read/write are gone; the duplicate `cb-balanced-logos-*` classes and `data-cb-balanced-logos-*` attributes are gone; the `_cb_balanced_logos_*` / `_cb_logo_soup_*` meta fallbacks are gone (only `_cb_lw_*` remains). The `readme.txt` 1.4.0 upgrade notice tells users to move content to `[logo_wrangler]`. Guard tests: `test_old_product_names_are_not_registered_or_emitted` and `test_plugin_never_rewrites_active_plugins`.
- **1.3.6** (`096d507`): `str_contains()` → `strpos()` in the Auto Scroll duplicate check, because the plugin declares `Requires PHP: 7.4` and WordPress does not polyfill PHP 8 functions. A guard test fails if a PHP 8-only function returns.
- **1.3.7** (`1f7112d`): Auto Scroll loads once. `external_autoscroll_handle()` finds another handle by `src`; `reconcile_autoscroll()` settles it on `wp_print_footer_scripts`, because block themes render content before `wp_enqueue_scripts`.
- **WP.org:** 1.3.7 was uploaded 2026-10-29 and is waiting on the automated recheck. 1.4.0 is built and verified but **not uploaded**. 1.3.7 carries the `coopbolo_` prefix, so it answers the Aug 26 prefix finding.
- **Counts:** PHPUnit is 62 tests (175 assertions), Jest 19 tests in 3 suites — both re-run on 2026-11-08. The 1.4.0 ZIP top level is `LICENSE admin build cooper-bold-logo-wrangler.php includes lib package-lock.json package.json readme.txt src`; no `balanced-logos.php`.

**Pages updated:** [[index]] (Must-know now says 1.3.7 is the last upload and 1.4.0 is on `main`; added the missing [[0008-remove-old-product-names]] entry), [[gotchas]] (removed the now-contradictory "don't remove legacy shortcodes" rule, added the never-touch-`active_plugins` rule, the PHP 7.4 and Auto Scroll traps, and corrected the 1.3.4/1.3.5 status and the ZIP top-level list), [[overview]] (release state 1.3.2/1.3.3 → 1.4.0/1.3.7; surfaces now list only the one block and one shortcode), [[logo-collections]] (legacy meta fallbacks are `_cb_lw_*` only), [[rapidsos-site]] and [[rapidsos-deploy]] (legacy shortcodes removed in 1.4.0), [[0007-coopbolo-prefix]] (status: merged and shipped in 1.3.7, no longer "not uploaded"), [[0002-migrate-legacy-plugin-path-activate-only]] (test filename corrected), [[wporg-review-thread]] (timeline through 2026-10-30), [[rendering-pipeline]] and [[carousel-and-splide]] (legacy classes/events removed; new Auto Scroll and PHP 7.4 sections), [[wordpress-org-submission]] (status block), [[wp-env-smoke-test]] (test counts), [[SCHEMA]] (CPT is `coopbolo_collection`).

Also finished the three items the 2026-11-03 gardener run left open (they were never committed): the `_cb_balanced_logos_*` / `_cb_logo_soup_*` fallback lines in [[logo-collections]], the "not uploaded yet" status on [[0007-coopbolo-prefix]], and the missing [[0008-remove-old-product-names]] line in [[index]].

Note for the next run: two repo files are still stale and were left alone (this job only touches `wiki/`). `AI_RUNBOOK.md` lists the three legacy shortcodes, says the plugin is version 1.2.15, and pins logo-soup `^1.2.2` instead of `1.3.0`. `src/block/index.js` still has a header comment saying it registers "plus legacy block names still present in existing content", but it now registers only `cooper-bold/logo-wrangler`.

## [2026-11-09] session | 1.5.0: React-free frontend (branch, not merged)

Branch `feat/react-free-frontend`: `build/view.js` now mounts with the logo-soup engine and plain DOM, so public pages stop loading WordPress's React (53 KB gzipped). DOM compared against the 1.4.0 React build in wp-env: identical in seven cases. New [[0009-react-free-frontend]]; [[0005-bundle-upstream-react-component]] superseded for the frontend. Also found while testing: on 1.4.0 the old demo collection (id 60, only `_cb_logo_soup_*` meta) renders empty, as expected after 1.4.0 dropped that fallback.
