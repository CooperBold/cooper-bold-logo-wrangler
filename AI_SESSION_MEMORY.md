# AI Session Memory — Cooper Bold Logo Wrangler WP Plugin

Dated log for session context and handoffs.

**Import into SimpleMem:**  
`python3 simplemem_cli.py import-ai-session --path AI_SESSION_MEMORY.md`

---

## [2026-06-12] Bootstrap | Set up project for AI

**Branch:** (initial, no commits yet)

**Completed:**

- Initialized git repository and `.gitignore` (`.env`, Python cache, `uncommitted/`).
- Bootstrapped **SimpleMem** (`simplemem_client.py`, `simplemem_cli.py`, `docs/simplemem/`, namespace `cooper-bold-logo-wrangler-wp-plugin`, local backend).
- Bootstrapped **LLM Wiki** (`wiki/` with SCHEMA, index, log, stub entity `wordpress-plugin`).
- Added **Context7** (`.cursor/context7-libraries.md`, `.cursor/rules/context7.mdc`).
- Wrote **AGENTS.md**, **AI_RUNBOOK.md**, and this file.
- **Legacy docs:** No `docs/` directory — ingest skipped.
- **Obsidian:** Already installed on macOS.

**State:**

- Plugin code **already present**: `cooper-bold-logo-wrangler.php`, `includes/`, `src/block/`, npm + `@sanity-labs/logo-soup`.
- AGENTS.md / runbook / wiki entities updated to match real architecture after bootstrap.

**Decisions:**

- SimpleMem: local-only (`SIMPLEMEM_BACKEND=local`), store at `docs/simplemem`.
- SimpleMem namespace `cooper-bold-logo-wrangler-wp-plugin` (repo slug); user-facing plugin name is **Cooper Bold Logo Wrangler** (vendor: Cooper Bold).

**Next steps:**

1. `npm install && npm run build` on a dev machine; verify block + shortcode on a WP site.
2. First git commit and push to `CooperBold/cooper-bold-logo-wrangler` when ready.
3. `Cursor planning install` + `Cursor planning/.env` for feature planning.
4. Add wiki guides for local WP dev and release zip if needed.

---

## [2026-06-22] WP.org prep v1.2.12 | Carousel + Plugin Check verified

**Branch:** `main` (ahead of `origin/main` by 2 commits)

**Git status (close-out):**

- Uncommitted: `readme.txt` (Tested up to **7.0**), `includes/class-cb-lw-renderer.php` (translators comment from Plugin Check)
- Untracked: `.wp-seed-logos/`, local verification screenshots
- Two WP.org prep commits on `main` not yet pushed

**Completed:**

- WordPress.org submission prep for **v1.2.12**: Splide vendored under `lib/splide/`, directory PNGs in `assets/`, readme polish
- Local **wp-env** Docker verified (Colima + `WP_ENV_CORE` local core checkout)
- Strip smoke test: post **4** with collection `demo-50-logos` (50 logos)
- Carousel smoke test: page **62** (`Cooper Bold Logo Wrangler Carousel Test`) — vendored Splide only, no CDN
- **Plugin Check** pass on release-shaped run (`--slug=cooper-bold-logo-wrangler` with dev-dir excludes)
- Carousel + vendored Splide confirmed via Playwright on `http://localhost:8888/?page_id=62`

**Decisions / lessons:**

- Local logo URLs must be **same-origin** (Media Library). External URLs fail logo-soup hydration due to canvas **CORS** — use uploaded media or same-host URLs in wp-env.

**Next steps:**

1. `git push origin main` (2 unpushed commits)
2. Build release ZIP (`./scripts/build-release-zip.sh`) and upload to [Add your plugin](https://wordpress.org/plugins/developers/add/)
3. After WP.org approval: add `SVN_USERNAME` / `SVN_PASSWORD` GitHub secrets; tag and push `v1.2.12`
4. Optional: commit Plugin Check micro-fixes (`readme.txt` Tested up to 7.0, renderer translators comment)

---

## [2026-07-10] Push v1.2.15 rename | Splide upgrade check

**Branch:** `main` @ `60aab65` (synced with `origin/main`)

**Completed:**

- Confirmed rename commit: **v1.2.15** — plugin renamed **Logo Soup by Cooper Bold → Cooper Bold Logo Wrangler** (text domain, shortcode, block, CSS classes, contributor `nicholaseymann`).
- Pushed `main` to `origin` (`3afa998..60aab65`) — includes rename + wiki auto-commit. No PR (direct to main).
- Confirmed Splide vendors are already latest on npm: `@splidejs/splide@4.1.4`, `@splidejs/splide-extension-auto-scroll@0.5.3`. No upgrade needed.

**State:**

- Working tree clean; `main` matches `origin/main`.
- GitHub remote: `CooperBold/cooper-bold-logo-wrangler` (renamed from cooper-bold-logo-soup on 2026-10-30; the old URL redirects).

**Decisions:**

- Do **not** bump Splide for WP.org “out of date libraries” on auto-scroll — false positive vs unreleased git branch; bump only when npm publishes a newer version (`lib/splide/README.md`).

**Next steps:**

1. Build release ZIP for **v1.2.15** and continue WP.org submission / SVN if not already live.
2. Update `docs/WORDPRESS-ORG-SUBMISSION.md` version refs from 1.2.12 → 1.2.15 if still stale.
3. Optional: rename GitHub repo slug to match Cooper Bold Logo Wrangler when convenient.

## 2026-08-24 — close-out

- **Where:** Cooper Bold Logo Wrangler WP plugin · branch `feat/wporg-review-1.3.1` · PR none
- **Done:** WordPress.org review loop for slug `cooper-bold-logo-wrangler`; uploaded v1.3.2 (Tested up to 7.1, automated scan passed); replied on review email thread. v1.3.1 prep: `src/` in ZIP, readme source docs, removed `plugins_loaded` `active_plugins` rewrite for WP.org compliance. Bugbot follow-up v1.3.3: migrate legacy `balanced-logos.php` on activate/update only; deactivation cleans both paths; legacy blocks hidden from inserter (`supports.inserter: false`). Verify PASS (43 PHPUnit, 16 Jest). Harvest draft provided (not posted).
- **State:** v1.3.2 in user Downloads; v1.3.3 **not** uploaded to WP.org — waiting on human reviewer for 1.3.2.
- **Decisions:** Legacy plugin path migration only on activate/update hooks (not `plugins_loaded`) → WP.org compliance. Hide legacy blocks from inserter rather than remove block registration.
- **Risks:** Reviewer may request further changes before 1.3.2 approval; 1.3.3 queued locally until 1.3.2 clears.
- **Next:** After 1.3.2 approval, upload v1.3.3 to WP.org; post Harvest time if Nicholas approves draft.

## 2026-09-02 — close-out

- **Where:** Cooper Bold Logo Wrangler WP plugin · branch `main` · PR none
- **Done:** Bumped `@sanity-labs/logo-soup` to **1.3.0** (crossOrigin fix, failures API, stale-results during load). Added `src/shared/logo-soup-lifecycle.js` wiring `onError` / `onNormalized` across view, block editor, and collection preview; `cb-lw-soup-loading` on strip/carousel-ref mounts; CSS for `data-logo-soup-loading`. Bugbot fix: clear loading class on total failure via `onError`. Verify PASS (19 Jest incl. 3 new lifecycle tests, build OK).
- **State:** v1.3.2 still in WP.org human review; v1.3.3 (now includes logo-soup 1.3.0) ready locally, not uploaded.
- **Decisions:** Pin logo-soup at 1.3.0 exact; always `console.warn` on failures (not debug-gated) so editors spot broken URLs.
- **Risks:** WP.org review still pending; measurement values may shift slightly vs 1.2.2 (upstream redmean change).
- **Next:** Upload v1.3.3 after 1.3.2 clears; rebuild release ZIP before upload.

## 2026-11-23 — close-out

- **Where:** Cooper Bold Logo Wrangler WP plugin · branch `main` · PR none (direct commits; repo renamed to `CooperBold/cooper-bold-logo-wrangler`, old URL redirects)
- **Done:** WP.org review fixes and cleanup, 1.3.5 → 1.5.0, all on `main`:
  - 1.3.5: `coopbolo_` / `COOPBOLO_` prefix everywhere, CPT `coopbolo_collection` + one-time migration (hardened after an adversarial gate).
  - 1.3.6/1.3.7: `str_contains()` → `strpos()` (PHP 7.4 fatal flagged by WP.org); Auto Scroll no longer loads twice when a theme has it.
  - CI fixed: Composer platform pinned to PHP 7.4 (lock had a PHP 8.4-only package).
  - 1.4.0: removed old product names (`[logo_soup]`, `[balanced_logos]`, legacy blocks, `balanced-logos.php`, all `active_plugins` writes); JS events/localStorage prefixed `coopbolo-`.
  - 1.5.0: frontend mounts without React (logo-soup engine + plain DOM); public pages drop ~53 KB gzipped of React. DOM and layout identical to the React build in Chromium, WebKit (Safari 26.4 engine) and Firefox 150.
- **State:** 1.3.7 uploaded to WP.org 2026-10-29 with Nicholas's reply; waiting on the automated recheck. 1.4.0 and 1.5.0 are on `main`, not uploaded.
- **Decisions:** Keep `cb-lw-*` CSS classes and `data-cb-lw` (theme/Bricks CSS targets them; the prefix rule covers PHP). Don't upload 1.5.0 mid-review: wait for the 1.3.7 recheck. RapidSOS (1.2.11) will not be upgraded.
- **Risks:** Collections saved only in the Logo Soup era (`_cb_logo_soup_*`) render empty on 1.4.0+ until re-saved. A 3-or-fewer-logo carousel with one broken logo never starts Splide (pre-existing). `save_post` double-unslash drops backslashes in alt text (pre-existing). Not verified on a Bricks site or in the Safari app itself.
- **Next:** When WP.org replies on 1.3.7, upload 1.5.0 (bump to 1.5.1 if anything changes) and answer any new notes.

## 2026-11-25 — close-out

- **Where:** Logo Wrangler plugin repo · branch `main` · PR none
- **Done:** Finished a stalled nightly rebase and pushed `44e5873`. Wiki pages that still said 1.5.2 was unreleased now say 1.5.4 is live (WP.org API checked). Added a gotcha for the expected red listing run on release merges.
- **State:** shipped. 1.5.4 live on WP.org; CI green.
- **Decisions:** none new.
- **Risks:** RapidSOS live is still on 1.2.11 (humans-only upgrade, snippet 289223 on staging).
- **Next:** Add Melanie to readme Contributors once she shares her WP.org username.
