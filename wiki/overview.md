---
title: Overview
type: overview
summary: What Cooper Bold Logo Wrangler is, who it's for, where it runs, and its release state.
status: active
created: 2026-10-19
updated: 2026-11-08
last_verified: 2026-11-08
sources: [README.md, readme.txt, AGENTS.md, AI_SESSION_MEMORY.md, "https://app.notion.com/p/37afb6901a688069a9ebef05fe326252", "WP.org review email thread (Cooper Bold inbox, 2026-06-26 → 2026-10-29)", "commits a5bc12b, 3b97d49"]
tags: [overview]
---

# Cooper Bold Logo Wrangler

A free WordPress plugin that makes a row of mixed partner logos look balanced. It wraps the MIT library `@sanity-labs/logo-soup`, which measures each image and scales it by visual weight. It is not a marquee. Sources: `README.md`, `readme.txt`.

## What and why

- **Origin:** a Cooper Bold Notion task, "Logo Soup plugin" (project RapidSOS), asked for the React package to be turned into a WordPress plugin for the RapidSOS "software integrations" page, and to be released as a free plugin under the Cooper Bold name. A later comment says it is Cooper Bold's own product, with Nicholas credited as creator, not RapidSOS client work. Source: [Notion task](https://app.notion.com/p/37afb6901a688069a9ebef05fe326252), digested in [[notion-logo-soup-plugin-task]].
- **Surfaces:** Gutenberg block `cooper-bold/logo-wrangler` (the only registered block since 1.4.0), shortcode `[logo_wrangler]` (the only one since 1.4.0), and **Logo Collections** (CPT `coopbolo_collection`, `cb_logo_collection` through 1.3.4; admin menu **Logo Wrangler**), which page builders like Bricks use through the shortcode. Sources: `AGENTS.md`, `includes/class-coopbolo-plugin.php`, commit `a5bc12b`.
- **Layouts:** `strip` (one normalized row) and `carousel` (one Splide slide per logo, for Bricks Nested Sliders). See [[carousel-and-splide]].
- **No logo cap** since 1.1.5 (commit `7a1a5fa`): admins own the performance tradeoff.

## People and roles

| Role | Who | Source |
|---|---|---|
| Developer, WP.org submitter (contributor `nicholaseymann`) | Nicholas | `readme.txt` Contributors; commit `60aab65` |
| Requester / product owner, Bricks designer on RapidSOS | Cooper Bold (Melanie for RapidSOS layout) | Notion task; `docs/RAPIDSOS-DEPLOY.md` |
| Directory reviewer | WordPress.org Plugins Team | [[wporg-review-thread]] |

## Where it runs

| Environment | URL / host | Deploys from |
|---|---|---|
| Local dev | wp-env at `http://localhost:8888` (Docker via Colima) | the working tree; see [[wp-env-smoke-test]] |
| RapidSOS staging | Cloudways app `wordpress-1533060-6135168.cloudwaysapps.com` (Bricks) | release ZIP uploaded by hand; see [[rapidsos-deploy]] |
| RapidSOS production | rapidsos.com | Cloudways DB push from staging *(per the RapidSOS repo audit; not checked here)* |
| WordPress.org directory | slug `cooper-bold-logo-wrangler` (requested, not yet approved) | `.github/workflows/deploy.yml` on `v*` tags, once SVN access exists |

Release state (2026-11-08): `main` is at **1.4.0** (commit `a5bc12b`, 2026-10-30), which removes the old product names and the legacy loader ([[0008-remove-old-product-names]]). **1.3.7** is the last WP.org upload (2026-10-29) and is waiting on the automated recheck; 1.4.0 is built but not uploaded. Sources: `readme.txt` Stable tag, `AI_SESSION_MEMORY.md` 2026-10-29 entry, [[wporg-review-thread]].

RapidSOS staging ran **1.2.11** under the old `cooper-bold-logo-soup` slug as of 2026-08-13 (RapidSOS repo `wiki/sources/site-code-audit-2026-08-13.md`). Moving it to 1.3.x is a slug change, not a drop-in; see [[gotchas]].

## Local checkout

There is one checkout: `~/Repos/Logo Soup WP Plugin` (on `main`, tracking `origin/main`). `~/Repos/logo-soup-wp-plugin`, `~/Repos/cooper-bold-logo-wrangler-wp-plugin`, and `~/Repos/logo-soup-wp-plugin-smoke` are **symlinks to it**, not separate clones (checked with `ls -l` on 2026-10-19). `.wp-env.json` mounts the plugin through the `cooper-bold-logo-wrangler-wp-plugin` symlink because wp-env breaks on paths with spaces.

Work lands directly on `main`; there are no pull requests (`gh pr list --state all` is empty). Feature branches are pushed alongside `main` and merged by fast-forward or direct push, so a branch being "merged" usually shows up as a commit on `main` rather than a merge commit — check with `git branch --contains <sha>`.

## Key links

- GitHub: <https://github.com/CooperBold/cooper-bold-logo-wrangler> (public; renamed from `cooper-bold-logo-soup` on 2026-10-30)
- Upstream library: <https://github.com/sanity-labs/logo-soup> ([[sanity-logo-soup]])
- Runbooks in `docs/`: `RAPIDSOS-DEPLOY.md`, `SMOKE-TEST.md`, `WORDPRESS-ORG-SUBMISSION.md`
- Agent rules: `AGENTS.md`; procedures: `AI_RUNBOOK.md`

## Related

- [[wordpress-plugin]] · [[rendering-pipeline]] · [[logo-collections]] · [[rapidsos-site]]
- [[0003-plugin-name-cooper-bold-logo-wrangler]] · [[gotchas]]
