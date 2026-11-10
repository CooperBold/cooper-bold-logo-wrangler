---
title: WP.org plugin review thread (2026-06 → 2026-10)
type: source
summary: Digest of the WordPress.org Plugin Directory review emails — what reviewers asked for, what we changed, and where it stands.
status: active
created: 2026-10-19
updated: 2026-11-10
last_verified: 2026-11-10
sources: ["Cooper Bold inbox thread 'Review in Progress: Logo Soup by Cooper Bold' (read with cooper-mail, 2026-10-19)", "Aug 26 review email pasted by Nicholas 2026-10-22", "commits 096d507, 1f7112d, 0144b59, 9501c8d, a5bc12b, 403645d", AI_SESSION_MEMORY.md]
tags: [wordpress-org, review, source]
---

# WP.org plugin review thread

One email thread from the WordPress.org Plugins Team covers the whole submission. It is still titled "Review in Progress: Logo Soup by Cooper Bold". It sits in the Cooper Bold inbox. Read it with `~/.claude/bin/cooper-mail search 'from:plugins@wordpress.org'` (read-only; never reply for Nicholas).

## Timeline

| Date | Who | What |
|---|---|---|
| 2026-06-26 | Reviewers (manual + AI) | First review of "Logo Soup by Cooper Bold" (slug `cooper-bold-logo-soup`). Main issue: the name uses the third-party "Logo Soup" project name, so the plugin looks official. Asked for a new name and slug in the readme, headers, and text domain. |
| 2026-07-06 | Nicholas | Uploaded **1.2.14** as "Balanced Logos" and asked to reserve `balanced-logos`. Stated no affiliation with Sanity Labs and that auto-scroll 0.5.3 is the latest npm release. |
| 2026-07-16 | Reviewers | "Balanced Logos" is too generic; asked for a distinctive name with the owner's term first. |
| 2026-08-03 | Nicholas | Uploaded **1.3.0** as "Cooper Bold Logo Wrangler" and asked to reserve `cooper-bold-logo-wrangler`. |
| 2026-08-06 | Automated scan | Failed: minified `build/*.js` with no matching source, and the readme did not point to this plugin's source (Guidelines 1 and 4). |
| 2026-08-24 | Nicholas | Uploaded **1.3.2**: source in the ZIP plus the public repo link, auto-scroll kept at 0.5.3, the `active_plugins` rewrite on load removed, Tested up to 7.1. |
| 2026-08-26 | Reviewers (manual) | Review of 1.3.2 (ID `R cooper-bold-logo-wrangler/nicholaseymann/6Aug26/T2 26Aug26/4.2`). Two issues: (1) "Out of date libraries" on `lib/splide/js/splide-extension-auto-scroll.min.js` 0.5.3, with the core Splide repo as the suggested URL; (2) prefix "cb" too short, with 11 hits: the `CB_LW*` classes and defines and the `cb_logo_collection` CPT. They want prefixes of 5+ characters and suggested `coopbolo`. The 2026-10-19 inbox search missed this email; Nicholas pasted it on 2026-10-22. |
| 2026-10-21 | Agent | Built **1.3.4** (not uploaded): prefix fixes for the Aug 6 "check everything" note. Plugin Check on the ZIP: no errors. Text domain was already `cooper-bold-logo-wrangler` in 1.3.2. |
| 2026-10-21 | Nicholas | Uploaded **1.3.4** with an upload comment on the prefix fixes. WP.org confirmed receipt. The status changed to "waiting on action from our plugins team" (577 in queue). 1.3.4 still used `CB_LW_` / `cb_lw_`, the prefix the Aug 26 review rejected. |
| 2026-10-22 | Agent | Built **1.3.5** on branch `fix/coopbolo-prefix` (not uploaded): everything renamed to `coopbolo_` / `COOPBOLO_`, with a one-time CPT and meta migration ([[0007-coopbolo-prefix]]). Confirmed both bundled Splide files are byte-identical to npm `@splidejs/splide@4.1.4` and `@splidejs/splide-extension-auto-scroll@0.5.3` (the latest releases), so issue (1) is a false positive to explain in the reply. |
| 2026-10-25 | Automated review | Flagged `page_already_loads_autoscroll_extension()` for calling PHP 8's `str_contains()` while the plugin declares `Requires PHP: 7.4`. |
| 2026-10-26 | Agent | Built **1.3.6** (`strpos()` instead of `str_contains()`, plus a guard test against PHP 8-only string/array functions) and **1.3.7** (Auto Scroll loads once when a theme already provides it). Merged to `main`. |
| 2026-10-29 | Nicholas | Uploaded **1.3.7** with an upload comment and replied in the thread. 1.3.7 carries the `coopbolo_` prefix, so it answers the Aug 26 prefix issue. Status: waiting on the automated recheck. |
| 2026-10-29 | Agent | Branch `fix/js-hook-prefix` renamed the JS event names and the debug localStorage key to `coopbolo-` and dropped the duplicate `cb-balanced-logos-hydrated` (commit `9501c8d`). |
| 2026-10-30 | Agent | **1.4.0** merged to `main` (commit `a5bc12b`): the old product names, the legacy loader, and every `active_plugins` write are removed ([[0008-remove-old-product-names]]). Not uploaded yet — it answers the original "Logo Soup" name complaint and the Aug 6 flag outright. |
| 2026-11-09 | Agent | **1.5.0** merged to `main` (commit `403645d`): the frontend mounts with the logo-soup engine and plain DOM, so public pages with a logo strip stop loading WordPress's React (~53 KB gzipped) ([[0009-react-free-frontend]]). DOM compared against the 1.4.0 React build in wp-env: identical in seven cases. Plugin Check on the 1.5.0 ZIP: no errors. Not uploaded yet. |

## What it changed in the repo

- Name and slug: [[0003-plugin-name-cooper-bold-logo-wrangler]]
- Source in the ZIP: [[0004-ship-source-in-release-zip]]
- No `active_plugins` writes on load: [[0002-migrate-legacy-plugin-path-activate-only]] (superseded by [[0008-remove-old-product-names]])
- Old product names deleted outright: [[0008-remove-old-product-names]]
- Library "out of date" flag answered, not "fixed": [[0001-pin-splide-extension-auto-scroll]]
- Before submitting (1.2.12–1.2.13, from Plugin Check runs): Splide vendored (no CDN), sanitized collection POST arrays, translators comments.

## Notes for the next upload

- Reply **in the same thread** and upload through the "Add your plugin" page. Reviewers expect every listed issue fixed and similar cases searched for.
- The reviewer account is the WP.org user `nicholaseymann`, which matches `readme.txt` Contributors. The docs saying contributor `cooperbold` are stale.
- The next upload should be **1.5.0**, which carries 1.4.0's removal of the "Logo Soup"/"Balanced Logos" names and the `active_plugins` handling ([[0008-remove-old-product-names]]) plus the React-free frontend ([[0009-react-free-frontend]]). The 1.4.0 upgrade notice tells users to move content off `[logo_soup]` and `[balanced_logos]` to `[logo_wrangler]`; 1.5.0's notice says the markup and CSS classes are unchanged.
- The auto-scroll "out of date" finding is still a false positive (0.5.3 is the latest npm release); explain it in the reply rather than bumping. See [[0001-pin-splide-extension-auto-scroll]].

## Related

- [[wordpress-org-submission]] · [[notion-logo-soup-plugin-task]] · [[overview]]
