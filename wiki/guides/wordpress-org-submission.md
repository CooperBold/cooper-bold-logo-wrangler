---
title: Submit to WordPress.org Plugin Directory
type: guide
summary: Maintainer steps to get the plugin into the WP.org directory: ZIP, review, SVN secrets, tag deploy, directory assets.
created: 2026-06-14
updated: 2026-11-10
last_verified: 2026-11-10
tags: [wordpress, release, deploy, svn, github-actions]
aliases: [wp-org-submit, release-plugin]
sources: [docs/WORDPRESS-ORG-SUBMISSION.md, README.md, .github/workflows/deploy.yml, readme.txt, "commits a5bc12b, 3b97d49, 403645d"]
status: active
---

# Submit to WordPress.org Plugin Directory

Step-by-step procedure for publishing **Cooper Bold Logo Wrangler** on the WordPress plugin directory. Tasks that need the maintainer's account or secrets are marked **(you)**. Source: `docs/WORDPRESS-ORG-SUBMISSION.md`.

**Display name (requested 2026-08-03, shipped in 1.3.0):** **Cooper Bold Logo Wrangler**. The docs' "chosen 2026-07-06" date is a find/replace artifact: on 2026-07-06 the name was Balanced Logos ([[0003-plugin-name-cooper-bold-logo-wrangler]]). — set in the `Plugin Name` header (`cooper-bold-logo-wrangler.php`) and `readme.txt` title. WordPress.org derives the directory slug from that header (expected: `cooper-bold-logo-wrangler`). The install folder slug, text domain, and block name stay `cooper-bold-logo-wrangler` / `cooper-bold/logo-wrangler` (no text-domain change).

## 1. Create a WordPress.org account **(you)**

1. Register at [wordpress.org](https://wordpress.org/support/register.php) if you do not have an account.
2. Choose or confirm the **contributor slug** (used in `readme.txt`; currently `nicholaseymann`, which is the reviewer account, since commit `60aab65`; the docs still say `cooperbold`).
3. Enable two-factor authentication on the account.

## 2. Build a clean plugin ZIP

From the repo root:

```bash
npm ci && npm run build
chmod +x scripts/build-release-zip.sh
./scripts/build-release-zip.sh
```

The ZIP is written to `dist/cooper-bold-logo-wrangler-<version>.zip`. It respects `.distignore` (no `node_modules`, dev docs, etc.). **Since 1.3.1** (WP.org review feedback), the ZIP deliberately **includes** `src/` and `package.json` so reviewers can regenerate the compiled `build/*.js` with `@wordpress/scripts` (`npm install && npm run build`); `readme.txt` maps each `src/**` entry to its `build/**` output in a `== Source code ==` section. See [[adversarial-review]] for verification that `build/block/block.json` and `view.scss.css` end up in the ZIP.

## 3. Submit for review **(you)**

1. Go to [Add your plugin](https://wordpress.org/plugins/developers/add/).
2. Upload the ZIP from step 2.
3. Wait for the review team email (often a few days to two weeks).
4. When approved, you receive **SVN credentials** for `https://plugins.svn.wordpress.org/cooper-bold-logo-wrangler/`.

> **Pre-approval:** the first submission is reviewed manually. Plugin Check (step 7) and escaping/sanitization in this repo are aligned with common review feedback, but approval is not guaranteed until a human reviewer accepts the plugin.
>
> **Status as of 2026-11-10:** **1.3.7** is the last upload (2026-10-29), waiting on the automated recheck; `main` is at **1.5.0**, built and verified but **not uploaded**. 1.5.0 is the React-free frontend ([[0009-react-free-frontend]]), on top of 1.4.0's removal of the old product names, the legacy loader, and every `active_plugins` write ([[0008-remove-old-product-names]]). Nicholas uploads and replies in the existing thread; full timeline in [[wporg-review-thread]].

## 4. Add GitHub secrets for automated deploy **(you)**

After SVN access is granted:

1. Open **GitHub → CooperBold/cooper-bold-logo-wrangler** **→ Settings → Secrets and variables → Actions**.
2. Add repository secrets:
   - `SVN_USERNAME` — your wordpress.org username.
   - `SVN_PASSWORD` — your [application password](https://make.wordpress.org/core/handbook/tutorials/generate-a-password-for-svn/) (not your login password).

Pushes of tags matching `v*` on `main` run `.github/workflows/deploy.yml`, which builds assets and deploys via [10up/action-wordpress-plugin-deploy](https://github.com/10up/action-wordpress-plugin-deploy).

## 5. First release tag **(you)**

When SVN is ready and secrets are set:

```bash
git checkout main
git pull --rebase
npm ci && npm run build
git status   # ensure build/ is committed if changed
git tag -a v1.0.0 -m "Release 1.0.0"
git push origin main
git push origin v1.0.0
```

The `v1.0.0` tag triggers deploy to `tags/1.0.0` and updates `trunk` on WordPress.org SVN.

## 6. Directory assets **(you)**

> **Check before the first tag:** the PNGs live in `assets/` (excluded from the ZIP by `.distignore`). The 10up deploy action reads directory art from `.wordpress-org/` by default (`ASSETS_DIR`), and `deploy.yml` doesn't override it. Either set `ASSETS_DIR: assets` in the workflow env or move the files. `README.md` already describes `.wordpress-org/`.

Before or shortly after launch, add PNGs under `assets/` (see `assets/README.md`):

- `banner-772x250.png`
- `icon-256x256.png`
- `screenshot-1.png`, `screenshot-2.png`

Commit to `main`, then tag again (e.g. `v1.0.1`) or deploy assets via SVN manually.

## 7. Plugin Check **(you)**

On a staging WordPress site (6.4+):

1. Install the [Plugin Check](https://wordpress.org/plugins/plugin-check/) plugin.
2. Upload the release ZIP or clone from SVN trunk.
3. Run **Plugin Check → Check a plugin** and fix any reported issues before promoting to production.

This repo follows WordPress coding practices: `ABSPATH` guards, escaping, sanitization, text domain `cooper-bold-logo-wrangler`. No `eval` or obfuscated JavaScript in source. Splide is bundled under `lib/splide/` (MIT) for standalone carousels.

## 8. After release

- Update `Stable tag` and changelog in `readme.txt` for each release.
- Bump `Version` in `cooper-bold-logo-wrangler.php`, `COOPBOLO_VERSION`, and `src/block/block.json` (`version`); run `npm run build`; then tag `v*`.
- Monitor the plugin support forum (`https://wordpress.org/support/plugin/cooper-bold-logo-wrangler/`, assuming the requested slug is granted) once live. The docs still link the rejected `balanced-logos` slug.
- Before uploading a version that drops an alias, make sure the `readme.txt` **Upgrade Notice** tells users what to change. 1.4.0 does: content using `[logo_soup]` or `[balanced_logos]` must move to `[logo_wrangler]`. 1.5.0 has one too ("Lighter pages: the frontend no longer loads React. Markup and CSS classes are unchanged.").

## What cannot be automated

| Task | Blocker |
| --- | --- |
| Plugin directory submission | WordPress.org account + human review |
| SVN deploy | `SVN_USERNAME` / `SVN_PASSWORD` secrets |
| Plugin Check on live site | Staging WordPress install |
| Marketing screenshots | Design-approved PNG exports |

## Related

- [[wporg-review-thread]] · [[0004-ship-source-in-release-zip]] · [[0003-plugin-name-cooper-bold-logo-wrangler]]
- [[wordpress-plugin]]
- [[adversarial-review]]
- [[sar-review]]
