---
title: wp-env smoke test (Cooper Bold Logo Wrangler)
type: guide
summary: Local Docker (wp-env + Colima) smoke test: build, activate, render a shortcode, check assets and the release ZIP.
created: 2026-06-17
updated: 2026-11-10
last_verified: 2026-11-10
tags: [wordpress, wp-env, docker, colima, smoke-test, ci]
aliases: [smoke-test, wp-env-smoke]
sources: [docs/SMOKE-TEST.md, .wp-env.json, AI_RUNBOOK.md, .distignore, "commits a5bc12b, 096d507, 1f7112d, 403645d"]
status: active
---

# wp-env smoke test (Cooper Bold Logo Wrangler)

End-to-end smoke test for **Cooper Bold Logo Wrangler** in `@wordpress/env` (Docker). Verifies build, plugin activation, shortcode rendering, asset enqueue, release zip, and unit tests. Source: `docs/SMOKE-TEST.md` (recorded 2026-06-15).

## Pass criteria (one-line summary)

`npm ci && npm run build && npm run wp-env:start` → plugin active → shortcode post renders with `data-cb-lw` and `cooper-bold-logo-wrangler-view` script → `wp-env:stop` clean.

## Step-by-step

```bash
cd "/Users/thedao/Repos/Logo Soup WP Plugin"   # real checkout; logo-soup-wp-plugin is a symlink to it
npm ci && npm run build

# If git clone into ~/.wp-env fails index-pack, use a local core checkout under $HOME:
export WP_ENV_CORE=/Users/thedao/wp-env-wordpress-core
npm run wp-env:start

npx wp-env run cli wp plugin list --status=active | grep -i logo
npx wp-env run cli wp post create --post_title='Cooper Bold Logo Wrangler Test' --post_status=publish \
  --post_content='[logo_wrangler logos="https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png|Google"]'

curl -s -o /dev/null -w "%{http_code}" http://localhost:8888/
curl -s "http://localhost:8888/?p=POST_ID" | grep -E 'data-cb-lw|cooper-bold-logo-wrangler-view'

npm run wp-env:stop
```

**Pass:** published page renders `.cb-lw` with `data-cb-lw`, placeholder `<img>` tags, and `cooper-bold-logo-wrangler-view` → `build/view.js` in output.

## Path-with-spaces fix

`@wordpress/env` splits plugin paths on spaces when `"plugins": [ "." ]`, so lifecycle activation fails for `Cooper Bold Logo Wrangler WP Plugin`. Symlink the repo to a no-spaces path and point `.wp-env.json` at it:

```bash
ln -sfn "/Users/thedao/Repos/Logo Soup WP Plugin" "/Users/thedao/Repos/cooper-bold-logo-wrangler-wp-plugin"   # already exists on this Mac
npm run wp-env:start   # uses committed .wp-env.json; no override needed
```

WP-CLI slug is `cooper-bold-logo-wrangler-wp-plugin` (from directory name). Remove any local `.wp-env.override.json` from earlier smoke runs — it overrides `.wp-env.json`.

## Colima notes

- macOS smoke runs use the `colima` docker context. Containers may show `Created` until started; if `localhost:8888` refuses connections after `wp-env start`, run `docker start <project>-wordpress-1` or re-run `wp-env start`.
- `WP_ENV_CORE` must live under `$HOME` (not `~/.wp-env`) because the default shallow clone fails `index-pack` on this host. A local core checkout under `$HOME` mounts correctly in Colima.

## Unit / lint tests (no Docker)

```bash
composer install
vendor/bin/phpunit      # PHPUnit — 63 tests on 1.5.0 (2026-11-10); was 62 on 1.4.0, 49 on 1.3.5, 13 in June
npm test                # Jest — 32 tests in 4 suites (2026-11-10); was 19 in 3 suites on 1.4.0, 11 in June
npm run verify          # .verify.sh gate: Jest + build
npm run lint:js
```

PHPUnit tests: `tests/COOPBOLO_*_Test.php` (renderer, assets, collections, bootstrap, admin branding). Jest: `src/shared/*.test.js`, `src/admin/collection-preview.test.js`. The 1.5.0 frontend work added `src/shared/mount-soup-dom.test.js` and a PHPUnit guard that the built `build/view.asset.php` lists no React handle ([[0009-react-free-frontend]]).

## Release zip verification

```bash
./scripts/build-release-zip.sh
unzip -l dist/cooper-bold-logo-wrangler-<version>.zip
```

**Pass criteria:**

- Contains `build/` (index.js, view.js, block.json, CSS)
- Contains `includes/`, `cooper-bold-logo-wrangler.php`, `readme.txt`, `LICENSE`
- **Contains** `src/`, `package.json`, `package-lock.json`, and `lib/splide/` (since 1.3.1, [[0004-ship-source-in-release-zip]])
- Does **not** contain `tests/`, `composer.json`, `composer.phar`, `vendor/`, `node_modules/`, `phpunit.xml.dist`, `assets/`, `docs/`, `wiki/`

## Recorded result (2026-06-15, historical)

| Step | Result |
|------|--------|
| Docker available (Colima) | Pass |
| `npm ci && npm run build` | Pass |
| `npm run wp-env:start` | Pass |
| Plugin activation (`cooper-bold-logo-wrangler-wp-plugin` v1.0.1) | Pass |
| Published shortcode post (ID 4) | Pass |
| `curl` home HTTP status | Pass (200) |
| Shortcode frontend markup (`data-cb-lw`, `cooper-bold-logo-wrangler-view-js`) | Pass |
| PHPUnit (prior run) | Pass — 13 tests, 24 assertions |
| Jest (prior run) | Pass — 11 tests |
| Release zip (prior run) | Pass — 21 files, no tests/composer/vendor |
| `npm run wp-env:stop` | Pass |

## Related

- [[wordpress-plugin]]
- [[rapidsos-deploy]]
- [[wordpress-org-submission]]
