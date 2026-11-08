---
title: Plugin name — Cooper Bold Logo Wrangler
type: decision
summary: Why the plugin went Logo Soup → Balanced Logos → Cooper Bold Logo Wrangler, and which old identifiers were kept on purpose (most removed in 1.4.0).
status: active
created: 2026-10-19
updated: 2026-11-03
last_verified: 2026-11-03
sources: ["commits 0f37da5, 980383c, 70052c0, 60aab65, aed0e5b, 3b97d49, a5bc12b", "WP.org review email thread 2026-06-26 → 2026-08-03", "https://app.notion.com/p/37afb6901a688069a9ebef05fe326252", readme.txt]
tags: [naming, wordpress-org, legacy]
---

# Plugin name: Cooper Bold Logo Wrangler

**Status:** active. The display name is **Cooper Bold Logo Wrangler** and the requested WP.org slug is `cooper-bold-logo-wrangler` (1.3.0, commit `aed0e5b`, 2026-08-17). The GitHub repo slug was renamed to `cooper-bold-logo-wrangler` on 2026-10-30 (commit `3b97d49`, old URL redirects).

## Timeline (from the `Plugin Name` header at each commit)

| Version | Date | Display name | Main file / text domain | Why |
|---|---|---|---|---|
| 1.0.0 | 2026-06-12 | Cooper Bold Logo Soup | `cooper-bold-logo-soup.php` | first build (`0f37da5`) |
| 1.1.3 | 2026-06-16 | Logo Soup | same | display-name-only change (`980383c`) |
| 1.2.12 | 2026-06-25 | Logo Soup by Cooper Bold | same | first WP.org submission (`70052c0`) |
| 1.2.15 | 2026-07-08 | Balanced Logos | `balanced-logos.php` / `balanced-logos` | WP.org name feedback, round 1 (`60aab65`) |
| 1.3.0 | 2026-08-17 | Cooper Bold Logo Wrangler | `cooper-bold-logo-wrangler.php` / `cooper-bold-logo-wrangler` | WP.org name feedback, round 2 (`aed0e5b`) |

## Why

- **"Logo Soup" was rejected** (review email, 2026-06-26): it is the name of Sanity Labs' library, so the plugin looked like an official Sanity plugin, and adding "by Cooper Bold" at the end did not fix that. The reviewer's AI suggested "Cooper Bold Logo Strip Normalizer". A ZIP with that name exists in `dist/`, but it was never submitted. See [[wporg-review-thread]].
- **"Balanced Logos" was rejected** (2026-07-16): too generic, and it could be confused with other logo plugins. The reviewer asked for a distinctive name with the owner's own term **at the start**.
- On Notion, the team first avoided featuring Cooper Bold too much ("Balanced Logos?"), then chose "Logo Wrangler" for some whimsy. Nicholas asked WP.org to reserve `cooper-bold-logo-wrangler` on 2026-08-03. Source: [[notion-logo-soup-plugin-task]].

## Alternatives considered

Balanced/Partner/Harmonized Logo Strip by Cooper Bold (Notion, 2026-07-01); Balanced Logos (rejected); Cooper Bold Logo Strip Normalizer (AI suggestion, not used).

## Consequences: what was kept on purpose

- Shortcodes `[balanced_logos]`, `[logo_soup]`, `[cooper-bold-logo-soup]`; blocks `cooper-bold/balanced-logos` and `cooper-bold/logo-soup` (hidden from the inserter since 1.3.3); CPT `cb_logo_collection`; legacy meta keys; legacy CSS classes and data attributes, all so existing content keeps rendering. Source: `readme.txt` 1.3.0 changelog. **Most of this list was removed in 1.4.0** (commit `a5bc12b`): the legacy shortcodes/blocks, `balanced-logos.php` loader, all `active_plugins` writes, and the duplicate `cb-balanced-logos-*` CSS/data classes are gone — see [[0008-remove-old-product-names]].
- `balanced-logos.php`, a loader with no header, for sites that activated 1.2.15. There is **no** loader for `cooper-bold-logo-soup.php`; see [[rapidsos-site]]. The loader itself was **deleted in 1.4.0** — see [[0008-remove-old-product-names]].
- Each rename was a repo-wide find/replace, which also rewrote history. For example, the `readme.txt` 1.1.3 changelog now says the name became "Cooper Bold Logo Wrangler", but it was "Logo Soup". `docs/WORDPRESS-ORG-SUBMISSION.md` says the name was "chosen 2026-07-06", but that was the Balanced Logos date. Check old names with `git show`.

## Related

- [[0002-migrate-legacy-plugin-path-activate-only]] · [[0008-remove-old-product-names]] · [[wordpress-plugin]] · [[wordpress-org-submission]]
